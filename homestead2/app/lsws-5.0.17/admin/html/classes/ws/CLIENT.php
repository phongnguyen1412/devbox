<?php

class CLIENT
{

    const UTYPE = 'LSWS' ;

    private $id = "" ;
    private $id_field = "lsws_uid" ;
    private $pass = "" ;
    private $pass_field = "lsws_pass" ;
    private $secret = NULL ;
    private $token = "" ;
    private $timeout = 0 ;
    private $valid = FALSE ;
    private $changed = FALSE ;
    //limit array size per stat..
    private $stat_limit = 60 ;
    private static $_instance = NULL ;

    // prevent an object from being constructed
    private function __construct()
    {

    }

    public static function singleton()
    {

        if ( ! isset(self::$_instance) ) {
            $c = __CLASS__ ;
            self::$_instance = new $c ;
            self::$_instance->init() ;
        }

        return self::$_instance ;
    }

    public function getToken()
    {
        return $this->token ;
    }

    public function hasChanged()
    {
        return $this->changed ;
    }

    public function setChanged($changed=true)
    {
        $this->changed = $changed;
    }

    public function init()
    {

        session_name(self::UTYPE . 'WEBUI') ; // to prevent conflicts with other app sessions
        session_start() ;


        if ( ! array_key_exists('secret', $_SESSION) ) {
            $_SESSION['secret'] = 'litespeedrocks' ;
        }

        if ( ! array_key_exists('changed', $_SESSION) ) {
            $_SESSION['changed'] = FALSE ;
        }


        if ( ! array_key_exists('valid', $_SESSION) ) {
            $_SESSION['valid'] = FALSE ;
        }

        if ( ! array_key_exists('timeout', $_SESSION) ) {
            $_SESSION['timeout'] = 0 ;
        }

        if ( ! array_key_exists('token', $_SESSION) ) {
            $_SESSION['token'] = microtime() ;
        }

        $this->valid = &$_SESSION['valid'] ;
        $this->changed = &$_SESSION['changed'] ;
        $this->secret = &$_SESSION['secret'] ;
        $this->timeout = &$_SESSION['timeout'] ;
        $this->token = $_SESSION['token'] ;

        if ( $this->valid == TRUE ) {

            if ( array_key_exists('lastaccess', $_SESSION) ) {

                if ( $this->timeout > 0 && time() - $_SESSION['lastaccess'] > $this->timeout ) {
                    $this->clear() ;
                    header("location:/login.php?timedout=1") ;
                    die() ;
                }

                $this->id = DUtil::grab_input('cookie', $this->id_field) ;
                $this->pass = DUtil::grab_input('cookie', $this->pass_field) ;
            }
            $this->updateAccessTime() ;
        }
    }

    public function isValid()
    {
        return $this->valid ;
    }

    public function store( $uid, $pass )
    {
		$domain = $_SERVER['HTTP_HOST'];
		if ($pos = strpos($domain, ':')) {
			$domain = substr($domain, 0, $pos);
		}
		$secure = !empty($_SERVER['HTTPS']);
		$httponly = true;
        setcookie($this->id_field, $uid, 0, "/", $domain, $secure, $httponly) ;
        setcookie($this->pass_field, $pass, 0, "/", $domain, $secure, $httponly) ;
        $this->updateAccessTime() ;
        $this->valid = TRUE ;
    }

    public function getIdData()
    {
        return array('id' => $this->id, 'pass' => $this->pass,
            'sec0' => $this->secret[0], 'sec1' => $this->secret[1]);
    }

    public function setSecret( $secret )
    {
        $this->secret = $secret ;
    }

    public function getTimeout()
    {
        return $this->timeout ;
    }

    public function setTimeout( $timeout )
    {
        $this->timeout = (int) $timeout ;
    }

    public function updateAccessTime()
    {
        $_SESSION['lastaccess'] = time() ;
    }

    public function clear()
    {

        $this->valid = FALSE ;
        session_destroy() ;
        session_unset() ;
        $outdated = time() - 3600 * 24 * 30 ;
        setcookie($this->id_field, '', $outdated, "/") ;
        setcookie($this->pass_field, '', $outdated, "/") ;
        setcookie(session_name(), '', $outdated, "/") ;
    }

    public function authenticate( $authUser, $authPass )
    {
        $auth = FALSE ;

        if ( strlen($authUser) && strlen($authPass) ) {
            $filename = DUtil::grab_input("server", "LS_SERVER_ROOT") . 'admin/conf/htpasswd' ;

            $fd = fopen($filename, 'r') ;
            if ( ! $fd ) {
                return FALSE ;
            }

            $all = trim(fread($fd, filesize($filename))) ;
            fclose($fd) ;

            $lines = explode("\n", $all) ;
            foreach ( $lines as $line ) {
                list($user, $pass) = explode(':', $line) ;
                if ( $user == $authUser ) {
                    if ( $pass[0] != '$' )
                        $salt = substr($pass, 0, 2) ;
                    else
                        $salt = substr($pass, 0, 12) ;
                    $encypt = crypt($authPass, $salt) ;
                    if ( $pass == $encypt ) {
                        $auth = TRUE ;
                        break ;
                    }
                }
            }
        }
        if ( ! $auth ) {
            // log in error log
            $ip = $_SERVER["REMOTE_ADDR"] ;
            $uri = $_SERVER['SCRIPT_URI'] ;
            error_log("[WebAdmin Console] Failed Login Attempt - username: $authUser ip: $ip url: $uri\n") ;

            // email notice
            $confcenter = ConfCenter::singleton() ;
            $emails = $confcenter->GetAdminEmails() ;
            if ( $emails != '' ) {
                $hostname = gethostbyaddr($ip) ;
                $date = date("F j, Y, g:i a") ;
                $subject = 'LiteSpeed Web Admin Console Failed Login Attempt' ;
                $contents = "A recent login attempt to LiteSpeed web admin console failed. Details of the attempt are below.\n
	Date/Time: $date
	Username: $authUser
	IP Address: $ip
	Hostname: $hostname
	URL: $uri

If you do not recognize the IP address, please follow below recommended ways to secure your admin console:

	1. set access allowed list to limit certain IP that can access under WebConsole->Admin->Security tab;
	2. change the listener port from default value 7080;
	3. do not use simple password;
	4. use https for admin console.
	" ;
                $result = mail($emails, $subject, $contents) ;
            }
        }
        return $auth ;
    }

    //persistent stats
    public function &getStat( $key )
    {

        $key = "stat_$key" ;

        if ( isset($_SESSION[$key]) ) {
            return $_SESSION[$key] ;
        }
        else {
            $temp = NULL ;
            return $temp ;
        }
    }

    public function addStat( $key, &$data )
    {

        $result = &$this->getStat($key) ;
        $sess_key = "stat_$key" ;
        $sess_keylock = "{$key}_lock_" ;

        if ( $result != NULL ) {
            $curtime = time() ;
            $time_span = $curtime - $_SESSION[$sess_keylock] ;
            if ( isset($_SESSION[$sess_keylock]) ) {
                if ( $time_span <= 1 ) {
                    //multiple stats windows open...check locks
                    echo("multiple stats windows open\n") ;
                    return FALSE ;
                }
                elseif ( $time_span > 70 ) {
                    //data is stale
                    $_SESSION[$sess_key] = NULL ;
                    echo ("data is stale\n") ;
                    return FALSE ;
                }
            }

            //incoming data's column set does not match that of store data
            if ( count($data) != count($result) ) {
                echo("incoming data's column set does not match that of the stored data.\n") ;
                return FALSE ;
            }

            //max item 30 reached...shorten array by 1 from head
            if ( count($result[0]) >= $this->stat_limit ) {
                foreach ( $result as $index => $set ) {
                    while ( count($result[$index]) >= $this->stat_limit ) {
                        array_shift($result[$index]) ;
                    }
                }
            }

            //add data
            foreach ( $result as $index => $set ) {
                $result[$index][] = $data[$index] ;
            }

            $_SESSION[$sess_keylock] = $curtime ;
            return TRUE ;
        }
        else {
            $result = array() ;

            //init data
            foreach ( $data as $index => $value ) {
                $result[$index] = array() ;
                $result[$index][] = $value ;
            }

            $_SESSION[$sess_key] = &$result ;
            $_SESSION[$sess_keylock] = $curtime ;
            return TRUE ;
        }
    }

}
