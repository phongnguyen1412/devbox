<?php

class DTblDef
{
	private $_tblDef = array();
	private $_options;
	private $_attrs;

	public static function getInstance()
	{
        if ( !isset($GLOBALS['_DTblDef_']) )
			$GLOBALS['_DTblDef_'] = new DTblDef();
		return $GLOBALS['_DTblDef_'];
	}

	private function __construct()
	{
		$this->loadCommonOptions();
		$this->loadCommonAttrs();
	}

	public function GetTblDef($tblId)
	{
		if (!isset( $this->_tblDef[$tblId] ))
		{
			$funcname = 'add_' . $tblId;
			if (!method_exists($this, $funcname))
				die('invalid tid');
			$this->$funcname($tblId);
		}
		return $this->_tblDef[$tblId];
	}

	private function DupTblDef($tblId, $newId, $newTitle=NULL)
	{
		$tbl = $this->GetTblDef($tblId);
		$newtbl = $tbl->dup($newId, $newTitle);
		return $newtbl;
	}


	private function loadCommonOptions()
	{
		$this->_options = array();
		$this->_options['text_size'] = 'size="95"';
		$this->_options['tp_vname'] = array( '/\$VH_NAME/', 'requiring variable $VH_NAME');
		$this->_options['tp_vroot'] = array( '/(\$VH_NAME)|(\$VH_ROOT)/',
											  'requiring variable $VH_NAME or $VH_ROOT');

		$this->_options['symbolLink'] = array( '1'=>'Yes', '2'=>'If Owner Match', '0'=>'No');

		$this->_options['allowOverride'] = array('1'=>'Limit', '2'=>'Auth', '4'=>'FileInfo',
												'8'=>'Indexes', '16'=>'Options', '0'=>'None');
		$this->_options['logHeaders'] = array('1'=>'Referrer', '2'=>'UserAgent', '4'=>'Host', '0'=>'None');

		$this->_options['railsEnv'] = array(
				''=>'',
				'0'=>'Development',
				'1'=>'Production',
				'2'=>'Staging'
				 );

		$this->_options['scriptHandler'] = array(
				'fcgi'=>'Fast CGI', 'servlet'=>'Servlet Engine',
				'lsapi'=>'LiteSpeed SAPI',
				'proxy'=>'Web Server', 'cgi'=>'CGI',
				'loadbalancer'=>'Load Balancer' );

		$this->_options['extType'] = array(
				'fcgi'=>'Fast CGI App', 'fcgiauth'=>'Fast CGI Authorizer',
				'lsapi'=>'LSAPI App',
				'servlet'=>'Servlet Engine', 'proxy'=>'Web Server',
				'logger'=>'Piped Logger',
				'loadbalancer'=>'Load Balancer');

		$this->_options['extAutoStart'] = array(
				'1'=>'Yes', '0'=>'No', '2'=>'Through CGI Daemon', '3'=>'Through CGI Daemon (Async)');

		$this->_options['extTbl'] = array(
				0=>'type', 1=>'A_EXT_FCGI',
			    'fcgi'=>'A_EXT_FCGI', 'fcgiauth'=>'A_EXT_FCGIAUTH',
				'lsapi'=>'A_EXT_LSAPI',
				'servlet'=>'A_EXT_SERVLET', 'proxy'=>'A_EXT_PROXY',
				'logger'=>'A_EXT_LOGGER',
				'loadbalancer'=>'A_EXT_LOADBALANCER');

		$this->_options['tp_extTbl'] = array(
				0=>'type', 1=>'TP_EXT_FCGI',
				'fcgi'=>'TP_EXT_FCGI', 'fcgiauth'=>'TP_EXT_FCGIAUTH',
				'lsapi'=>'TP_EXT_LSAPI',
				'servlet'=>'TP_EXT_SERVLET', 'proxy'=>'TP_EXT_PROXY',
				'logger'=>'TP_EXT_LOGGER',
				'loadbalancer'=>'TP_EXT_LOADBALANCER');

		$this->_options['ext_respBuffer'] = array('0'=>'No', '1'=>'Yes',
												  '2'=>'No for Non-Parsed-Header (NPH)');

		$this->_options['vh_uidmode'] = array( 0=>'Server UID', 1=>'CGI File UID', 2=>'DocRoot UID');
		$this->_options['chrootmode'] = array( 0=>'Same as Server', 1=>'Virtual Host Root', 2=>'Customized Chroot Path');

		$this->_options['ctxType'] = array(
				'NULL'=>'Static', 'webapp'=>'Java Web App',
				'servlet'=>'Servlet', 'fcgi'=>'Fast CGI',
				'lsapi'=>'LiteSpeed SAPI',
				'proxy'=>'Proxy', 'cgi'=>'CGI',
				'loadbalancer'=> 'Load Balancer',
				'redirect'=>'Redirect',
				'rails'=>'Rack/Rails');

		$this->_options['ctxTbl'] = array(
				0=>'type', 1=>'VH_CTXG',
				'NULL'=>'VH_CTXG', 'webapp'=>'VH_CTXJ',
				'servlet'=>'VH_CTXS', 'fcgi'=>'VH_CTXF',
				'lsapi'=>'VH_CTXL',
				'proxy'=>'VH_CTXP', 'cgi'=>'VH_CTXC',
				'loadbalancer'=>'VH_CTXB',
				'redirect'=>'VH_CTXR',
				'rails'=>'VH_CTXRL');

		$this->_options['on_off'] = array('off'=>'Off', 'on'=>'On');

		$this->_options['realmType'] = array('file' => 'Password File', 'LDAP' => 'LDAP' );
		$this->_options['realmTbl'] = array( 0=>'type', 1=>'VH_REALM_LDAP',
										 'file' => 'VH_REALM_FILE', 'LDAP' => 'VH_REALM_LDAP' );
		$this->_options['tp_realmTbl'] = array( 0=>'type', 1=>'VH_REALM_LDAP',
										 'file' => 'TP_REALM_FILE', 'LDAP' => 'VH_REALM_LDAP' );

		$this->_options['logLevel'] = array('ERROR'=>'ERROR', 'WARN'=>'WARNING',
									'NOTICE'=>'NOTICE', 'INFO'=>'INFO', 'DEBUG'=>'DEBUG');
		$this->_options['updateInterval'] = array( 86400=>'Daily', 604800=>'Weekly', 2592000=>'Monthly' );


		$ipv6 = array();
		if ( $_SERVER['LSWS_IPV6_ADDRS'] != '' )
		{
			$ipv6['[ANY]'] = '[ANY] IPv6';
			$ips = explode( ',', $_SERVER['LSWS_IPV6_ADDRS'] );
			foreach( $ips as $ip )
				{
					$pos = strpos( $ip, ':' );
					if ( $pos )
					{
						$aip = substr( $ip, $pos+1 );
						$ipv6[$aip] = $aip;
					}
				}
		}
		$ipo = array();
		$ipo['ANY'] = 'ANY';
		$ips = explode(',', $_SERVER['LSWS_IPV4_ADDRS']);
		foreach( $ips as $ip )
		{
			$pos = strpos($ip, ':');
			if ( $pos )
			{
				$aip = substr($ip, $pos+1);
				$ipo[$aip] = $aip;
				if ( $aip != '127.0.0.1' )
					$ipv6["[::FFFF:$aip]"] = "[::FFFF:$aip]";
			}
		}
		if ( $_SERVER['LSWS_IPV6_ADDRS'] != '' )
			$this->_options['ip'] = $ipo + $ipv6;
		else
			$this->_options['ip'] = $ipo;

	}

	private function loadCommonAttrs()
	{
		$ctxOrder = new DAttr('order', 'cust', 'Order');
		$ctxOrder->_FDE = 'NNN';

		$this->_attrs = array(
			'priority' => new DAttr('priority', 'uint', 'Priority', 'text', true, -20, 20),
			'indexFiles' => new DAttr('indexFiles', 'fname', 'Index Files', 'textarea', true, NULL, NULL, 'rows="2" cols="60"', 1),
			'autoIndex' => new DAttr('autoIndex', 'bool', 'Auto Index', 'radio'),
			'vh_adminEmails' => new DAttr('adminEmails', 'email', 'Administrator Email', 'textarea', true, NULL, NULL, 'rows="3" cols="60"', 1, 'vhadminEmails'),
			'suffix' => new DAttr('suffix', 'parse', 'Suffixes', 'text', false,
								  "/^[A-z0-9_\-]+$/", 'comma-separated list, allowed character[A-z0-9_\-]', $this->_options['text_size'], 1),
			'fileName2' => new DAttr('fileName', 'file0', 'File Name', 'text', false, 2, 'r', $this->_options['text_size']),
			'fileName3' => new DAttr('fileName', 'file0', 'File Name', 'text', true, 3, 'r', $this->_options['text_size']),

			'rollingSize' => new DAttr('rollingSize', 'uint', 'Rolling Size (bytes)', 'text', true, NULL, NULL, NULL, NULL, 'log_rollingSize'),
			'keepDays' => new DAttr('keepDays', 'uint', 'Keep Days', 'text', true, 0, NULL, NULL, 0, 'accessLog_keepDays'),
			'logFormat' => new DAttr('logFormat', 'cust', 'Log Format', 'text', true, NULL, NULL, $this->_options['text_size'], 0, 'accessLog_logFormat'),
			'logHeaders' => new DAttr('logHeaders',	'checkboxOr', 'Log Headers', 'checkboxgroup', true, NULL, $this->_options['logHeaders'], NULL, 0, 'accessLog_logHeader'),
			'compressArchive' => new DAttr('compressArchive', 'bool', 'Compress Archive', 'radio', true, NULL, NULL, NULL, 0, 'accessLog_compressArchive'),
			'allowOverride' => new DAttr('allowOverride', 'checkboxOr', 'Allow Override', 'checkboxgroup', true, NULL, $this->_options['allowOverride']),
            'allowOverrideContext' => new DAttr('allowOverride', 'checkboxOr', 'Allow Override', 'checkboxgroup', true, NULL, $this->_options['allowOverride'], NULL, 0, 'allowOverrideContext'),

			'extraHeaders' => new DAttr(
				'extraHeaders', 'cust', 'Extra Headers', 'textarea1', true,
				NULL, 'Extra headers will be inserted into HTTP response Header, one header per line, put "NONE" to disable headers inherited from parent content.',
			    'rows="2" cols="60" wrap=off'),

			'scriptHandler_type' => new DAttr('type', 'sel', 'Handler Type', 'select', false, 0,
											  $this->_options['scriptHandler'],
											  'onChange="document.confform.a.value=\'c\';document.confform.submit()"', 0, 'shType'),

			'scriptHandler' => new DAttr('handler', 'sel1', 'Handler Name', 'select', false,
										array('ext:$$type!fcgi'), NULL, NULL, 0, 'shHandlerName'),

			'ext_type' => new DAttr('type', 'sel', 'Type', 'select', false, NULL, $this->_options['extType'], NULL, 0, 'extAppType'),
			'name'=> new DAttr('name', 'name', 'Name', 'text', false),
			'ext_address' => new DAttr('address', 'addr', 'Address', 'text', false, NULL, NULL, $this->_options['text_size'], 0, 'extAppAddress'),
			'ext_maxConns' => new DAttr('maxConns', 'uint', 'Max Connections', 'text', false, 1, 2000),
			'pcKeepAliveTimeout' => new DAttr('pcKeepAliveTimeout', 'uint', 'Connection Keepalive Timeout', 'text', true, -1, 10000 ),

			'ext_env' => new DAttr('env', 'parse', 'Environment', 'textarea', true,
								   "/\S+=\S+/", 'PROPERTY=VALUE', 'rows="5" cols="50" wrap=off', 2),
			'ext_initTimeout' => new DAttr('initTimeout', 'uint', 'Initial Request Timeout (secs)', 'text', false, 1),
			'ext_retryTimeout' => new DAttr('retryTimeout', 'uint', 'Retry Timeout (secs)', 'text', false, 0),
			'ext_respBuffer' => new DAttr('respBuffer', 'sel', 'Response Buffering', 'select', false, 0, $this->_options['ext_respBuffer']),
			'ext_persistConn' => new DAttr('persistConn', 'bool', 'Persistent Connection', 'radio', true ),
			'ext_autoStart' => new DAttr('autoStart', 'sel', 'Auto Start', 'select', false, NULL, $this->_options['extAutoStart']),
			'ext_path' => new DAttr('path', 'file1', 'Command', 'text', true, 3, 'x', $this->_options['text_size'], 0, 'extAppPath'),
			'ext_backlog' => new DAttr('backlog', 'uint', 'Back Log', 'text', true, 1, 100),
			'ext_instances' => new DAttr('instances', 'uint', 'Instances', 'text', true, 0, 1000),
			'ext_runOnStartUp' => new DAttr('runOnStartUp', 'sel', 'Run On Start Up', 'select', true, NULL,
							array(
							    ''=>'', '1'=>'Yes', '0'=>'No', '2'=>'suEXEC Daemon' )),
			'ext_user' => new DAttr('extUser', 'cust', "suEXEC User", 'text', true ),
			'ext_group' => new DAttr('extGroup', 'cust', "suEXEC Group", 'text', true ),

			'cgiUmask' => new DAttr('umask', 'parse', 'umask', 'text', true, "/^[0-7][0-7][0-7]$/", '[000-777].'),
			'memSoftLimit' => new DAttr('memSoftLimit', 'uint', 'Memory Soft Limit (bytes)', 'text', true, 0),
			'memHardLimit' => new DAttr('memHardLimit', 'uint', 'Memory Hard Limit (bytes)', 'text', true, 0),
			'procSoftLimit' => new DAttr('procSoftLimit', 'uint', 'Process Soft Limit', 'text', true, 0),
			'procHardLimit' => new DAttr('procHardLimit', 'uint', 'Process Hard Limit','text', true, 0),

		    'ssl_renegProtection' => new DAttr('renegProtection', 'bool', 'SSL Renegotiation Protection', 'radio'),
			'l_vhost' => new DAttr('vhost', 'sel1', 'Virtual Host', 'select', false, array('VHS'), NULL, NULL, 0, 'virtualHostName' ),
			'l_domain' => new DAttr('domain', 'domain', 'Domains', 'text', false, NULL, NULL, $this->_options['text_size'], 1, 'domainName'),
			'tp_templateFile' => new DAttr('templateFile', 'filetp', 'Template File', 'text', false, 2, 'rwc', $this->_options['text_size']),
			'tp_listeners' => new DAttr('listeners', 'sel2', 'Mapped Listeners', 'text', false, array('LNS'), NULL, $this->_options['text_size'], 1, 'mappedListeners'),
			'tp_vhName' => new DAttr('vhName', 'vhname', 'Virtual Host Name', 'text', false, NULL, NULL, $this->_options['text_size'], 0, 'templateVHName'),
			'tp_vhDomain' => new DAttr('vhDomain', 'domain', 'Domain', 'text', true, NULL, NULL, $this->_options['text_size'], 0, 'templateVHDomain'),
			'tp_vhRoot' => new DAttr('vhRoot', 'parse', 'Default Virtual Host Root', 'text', false,
									 $this->_options['tp_vname'][0], $this->_options['tp_vname'][1],
									 $this->_options['text_size'], 0, 'templateVHRoot'),

			'tp_vrFile' => new DAttr('fileName', 'parse', 'File Name', 'text', true,
									   $this->_options['tp_vroot'][0], $this->_options['tp_vroot'][1],
										 $this->_options['text_size'], 0, 'templateFileRef'),

			'tp_name' => new DAttr('name', 'parse', 'Name', 'text', false,
								   $this->_options['tp_vname'][0], $this->_options['tp_vname'][1], NULL, 0, 'tpextAppName'),
			'vh_maxKeepAliveReq' => new DAttr('maxKeepAliveReq', 'uint', 'Max Keep-Alive Requests', 'text', true, 0, 32767, NULL, 0, 'vhMaxKeepAliveReq'),
			'vh_smartKeepAlive' => new DAttr('smartKeepAlive', 'bool', 'Smart Keep-Alive', 'radio', true, NULL, NULL, NULL, 0, 'vhSmartKeepAlive'),
			'vh_enableGzip' => new DAttr('enableGzip', 'bool', 'Enable GZIP Compression', 'radio'),
			'vh_spdyAdHeader' => new DAttr('spdyAdHeader', 'parse', 'SPDY Advertisement', 'text', true, "/^\d+:npn-spdy\/[23]$/", 'required format: ssl_port:npn-spdy/version like 443:npn-spdy/3', $this->_options['text_size']),
			'vh_allowSymbolLink' => new DAttr('allowSymbolLink', 'sel', 'Follow Symbolic Link', 'select', true, 0, $this->_options['symbolLink']),
			'vh_enableScript' => new DAttr('enableScript', 'bool', 'Enable Scripts/ExtApps', 'radio', false),
			'vh_restrained' => new DAttr('restrained', 'bool', 'Restrained', 'radio', false),
			'vh_setUIDMode' => new DAttr('setUIDMode', 'sel', 'ExtApp Set UID Mode', 'select', true, 2, $this->_options['vh_uidmode'], NULL, 0, 'setUidMode'),
			'vh_chrootMode' => new DAttr('chrootMode', 'sel', 'ExtApp Chroot Mode', 'select', true, 0, $this->_options['chrootmode']),
            'vh_bandwidth' => new DAttr('totalBandwidth', 'uint', 'Total Output Bandwidth (KB/sec)', 'text', true, 0),
			'staticReqPerSec' => new DAttr('staticReqPerSec', 'uint', 'Static Requests/second', 'text', true, 0),
			'dynReqPerSec' => new DAttr('dynReqPerSec', 'uint', 'Dynamic Requests/second', 'text', true, 0),
			'outBandwidth' => new DAttr('outBandwidth', 'uint', 'Outbound Bandwidth (bytes/sec)', 'text', true, 0),
			'inBandwidth' => new DAttr('inBandwidth', 'uint', 'Inbound Bandwidth (bytes/sec)', 'text', true, 0),

			'ctx_order' => $ctxOrder,
			'ctx_type' => new DAttr('type', 'sel', 'Type', 'select', false, NULL, $this->_options['ctxType'], NULL, 0, 'ctxType'),
			'ctx_uri' => new DAttr('uri', 'expuri', 'URI', 'text', false, NULL, NULL, $this->_options['text_size'], 0, 'expuri'),
			'ctx_location' => new DAttr('location', 'cust', 'Location', 'text', false, NULL, NULL, $this->_options['text_size']),
			'ctx_shandler' => new DAttr('handler', 'sel1', 'Servlet Engine', 'select', false,
										array('ext:servlet'), NULL, NULL, 0, 'servletEngine'),
			'railsEnv' => new DAttr('railsEnv', 'sel', 'Run-time Mode', 'select', true, 0, $this->_options['railsEnv']),

			'geoipDBFile' => new DAttr( 'geoipDBFile', 'filep', 'DB File Path', 'text', false, 2, 'r', $this->_options['text_size']),

			'geoipDBCache' => new DAttr( 'geoipDBCache', 'sel',
							'DB Cache Type', 'select', true, 0,
							array( ''=>'', 'Standard'=>'Standard',
								   'MemoryCache' => 'MemoryCache',
								   'CheckCache' => 'CheckCache',
								   'IndexCache' => 'IndexCache' ) ),
			'enableIpGeo' => new DAttr( 'enableIpGeo', 'bool', 'Enable IP GeoLocation', 'radio'),
			'apacheconf' => new DAttr('apacheConf', 'cust', 'Apache Style configurations', 'textarea1', true, NULL, NULL, 'rows="6" cols="60" wrap=off'),
			'note' => new DAttr('note', 'cust', 'Notes', 'textarea', true, NULL, NULL, 'rows="4" cols="60" wrap=off'),

		);

		$this->_attrs['vh_chrootMode']->_version = 1;
		$this->_attrs['geoipDBFile']->_version = 1;
		$this->_attrs['geoipDBCache']->_version = 1;
		$this->_attrs['enableIpGeo']->_version = 1;
		$this->_attrs['logFormat']->_version = 1;

	}

	//	DAttr($key, $type, $label,  $inputType, $allowNull,$min, $max, $inputAttr, $multiInd)
	private function get_expires_attrs()
	{
		return array(
				new DAttr('enableExpires', 'bool', 'Enable Expires', 'radio'),
				new DAttr('expiresDefault', 'parse', 'Expires Default', 'text', true, "/^[AaMm]\d+$/",
						'[A|M]###, A means client access time, M means file modified time, ### is number of seconds'),
				new DAttr(	'expiresByType', 'parse', 'Expires By Type', 'textarea', true,
						"/^(\*\/\*)|([A-z0-9_\-\.\+]+\/\*)|([A-z0-9_\-\.\+]+\/[A-z0-9_\-\.\+]+)=[AaMm]\d+$/",
						'MIME_type=A|Mseconds, MIME type can be like */*, text/*, text/html',
						'rows="2" cols="50"', 1)
		);

	}

	private function add_SERV_PROCESS($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Server Process');

		$enableChroot = new DAttr('enableChroot', 'bool', 'Enable chroot', 'radio', false);
		$enableChroot->_version = 1;

		$chrootPath = new DAttr('chrootPath', 'cust', 'chroot Path', 'text', true);
		$chrootPath->_FDE = 'YYN';
		$chrootPath->_version = 1;
		$chrootPath->_helpKey = 'servChrootPath';

		$runningAs = new DAttr('runningAs', 'cust', 'Running As', 'text', false, NULL, NULL, $this->_options['text_size'] . ' readonly');
		$runningAs->_FDE = 'NYN';

		$user = new DAttr('user', 'cust', NULL, 'text', false);
		$user->_FDE = 'YNN';
		$group = new DAttr('group', 'cust', NULL, 'text', false);
		$group->_FDE = 'YNN';

		$attrs = array(
			new DAttr('serverName', 'name', 'Server Name', 'text', false),
			$runningAs, $user, $group,
			$this->_attrs['priority']->dup(NULL, NULL, 'serverPriority'),
			new DAttr('cpuAffinity', 'uint', 'CPU Affinity', 'text', true, 0, NULL),
			$chrootPath, $enableChroot,
			new DAttr( 'enableLVE', 'sel', 'Cloud Linux', 'select', true, 1, array( 0=>"Disabled", 1=>"LVE", 2=>"CageFS", 3=>"CageFS without suEXEC" ) ),
			new DAttr('inMemBufSize', 'uint', 'Memory I/O Buffer', 'text', false, 0, NULL),
			new DAttr('swappingDir', 'cust', 'Swapping Directory', 'text', false, 1, 'rw', $this->_options['text_size']),
			new DAttr( 'autoFix503', 'bool', 'Auto Fix 503 Error', 'radio', true ),
            new DAttr( 'enableh2c', 'bool', 'Enable HTTP/2 Over Cleartext TCP', 'radio', true ),
			new DAttr('gracefulRestartTimeout', 'uint', 'Graceful Restart Timeout (secs)', 'text', true, -1, 2592000)

			);
		$this->_tblDef[$id]->setAttr($attrs, 'general');
	}

	private function add_APACHE_CONF_FILE( $id )
	{
		$this->_tblDef[$id] = new DTbl($id, 'Using Apache Configuration File');
		$attrs = array(
			new DAttr( 'loadApacheConf', 'bool', 'Load Apache Configuration', 'radio', true ),
			new DAttr( 'autoReloadApacheConf', 'sel', 'Auto Reload On Changes', 'select', true, 0, array( 0=>'No', 1=>'(deprecated) Yes', 2=>'Use Apache binary wrapper', 3=>'override cPanel restart HTTPD script') ),
			new DAttr( 'apacheBinPath', 'file', 'Apache Binary Path', 'text', true, 2, '', $this->_options['text_size'] ),
			new DAttr( 'apacheConfFile', 'file', 'Apache Configuration File', 'text', true, 2, '', $this->_options['text_size'] ),
			new DAttr( 'apachePortOffset', 'uint', 'Apache Port Offset', 'text', true ),
			new DAttr( 'apacheIpOffset', 'uint', 'Apache IP Offset', 'text', true ),
 			new DAttr( 'apacheHandledContent', 'cust', 'Apache Handled Content', 'text', true, NULL, NULL, $this->_options['text_size']),
 			new DAttr( 'apacheIgnoredModules', 'cust', 'Ignore Apache Modules', 'text', true, NULL, NULL, $this->_options['text_size']),
                        new DAttr( 'jitConfig', 'bool', 'Just In Time Configuration', 'radio', true ),
			new DAttr( 'phpSuExec', 'sel', 'PHP suEXEC', 'select', true, 0, array( 0=>'No', 1=>'Yes', 2=>'User\'s Home Directory Only') ),
			new DAttr( 'phpSuExecMaxConn', 'uint', 'PHP suEXEC Max Conn', 'text', true ),
			new DAttr( 'apacheEnableFP', 'bool', 'Enable FrontPage Extension', 'radio', true ),
			new DAttr( 'apacheEnvVars', 'cust', "Apache Environment", 'textarea1', true, NULL, NULL, 'rows="3" cols="60" wrap=off')

			);
		$this->_tblDef[$id]->setAttr($attrs, 'general');
	}

	private function add_SERV_GENERAL($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'General Settings');

		$attr_mime = new DAttr('mime', 'file', 'MIME Settings', 'text', false, 2, 'rw', $this->_options['text_size']);
		$attr_mime->_href = '&t=SERV_MIME_TOP';

		$abortexptapp_options = array(''=>'', '1'=>'Enable Abort for Requests from External IPs',
										 '2'=>'Enable Abort for All Requests', '0'=>'No Abort');
		$attr_ar = new DAttr('adminRoot', 'cust', NULL, NULL, false);
		$attr_ar->_FDE = 'YNN';

		$attrs = array(
			$attr_mime,
			new DAttr('disableInitLogRotation', 'bool', 'Disable Initial Log Rotation', 'radio', true),
			new DAttr('showVersionNumber', 'sel', 'Server Signature', 'select', false, NULL, array('0'=>'Hide Version', '1'=>'Show Version', '2'=>'Hide Full Header')),
			new DAttr('hideSigInErrorPage', 'bool', 'Hide Error Page Signature', 'radio'),
			$this->_attrs['enableIpGeo'],
			new DAttr('useIpInProxyHeader', 'sel', 'Use Client IP in Header', 'select', true, NULL, array('0'=>'No', '1'=>'Yes', '2'=>'Trusted IP Only') ),
			new DAttr('extAppAbort', 'sel', 'External Application Abort', 'select', true, NULL, $abortexptapp_options ),
			new DAttr('autoUpdateInterval', 'sel', 'Check For Update', 'select', false, NULL, $this->_options['updateInterval'] ),
			new DAttr('autoUpdateDownloadPkg', 'bool', 'Download Updates', 'radio', false),
            new DAttr('adminEmails', 'email', 'Administrator Email', 'textarea', true, NULL, NULL, 'rows="3" cols="60"', 1),
			$attr_ar );
		$this->_tblDef[$id]->setAttr($attrs, 'general');

	}

	private function add_SERV_INDEX($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Index Files');
		$attrs = array(
			$this->_attrs['indexFiles'],
			$this->_attrs['autoIndex'],
			new DAttr('autoIndexURI', 'uri', 'Auto Index URI', 'text', true, NULL, NULL, $this->_options['text_size'])
			);
		$this->_tblDef[$id]->setAttr($attrs, 'general' );
	}

	private function add_SERV_MIME_TOP($id)
	{
		$align = array('left', 'left', 'center');
		$this->_tblDef[$id] = new DTbl($id, 'MIME Type Definition', 1, 'SERV_MIME', $align, "file", FALSE);

		$attrs = array(
			new DAttr('suffix', 'cust', 'Suffix'),
			new DAttr('type', 'cust', 'MIME Type'),
			new DAttr('action', 'action', 'Action', NULL, false, 'SERV_MIME', 'Ed')
			);
		$this->_tblDef[$id]->setAttr($attrs, 'MIME');
		$this->_tblDef[$id]->setRepeated('suffix');
		$this->_tblDef[$id]->_hasB = true;
	}

	private function add_SERV_MIME($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'MIME Type Entry', 2);
		$attrs = array(
			$this->_attrs['suffix']->dup('suffix', 'Suffixes', mimesuffix),
			new DAttr('type', 'parse', 'Mime Type', 'text', false,
					  "/^[A-z0-9_\-\.\+]+\/[A-z0-9_\-\.\+]+(\s*;?.*)$/", 'like text/html', $this->_options['text_size'], 0, 'mimetype')
			);
		$this->_tblDef[$id]->setAttr($attrs, 'MIME');
		$this->_tblDef[$id]->setRepeated('suffix');
		$this->_tblDef[$id]->_hasB = true;
	}

	private function add_SERV_LOG($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Server Log');

		$dbg_options = array('10'=>'HIGH', '5'=>'MEDIUM', '2'=>'LOW', '0'=>'NONE');
		$attrs = array(
			$this->_attrs['fileName2']->dup(NULL, NULL, 'log_fileName'),
			new DAttr('logLevel', 'sel', 'Log Level', 'select', false, 0, $this->_options['logLevel'], NULL, 0, 'log_logLevel'),
			new DAttr('debugLevel', 'sel', 'Debug Level', 'select', false, 0, $dbg_options, NULL, 0, 'log_debugLevel'),
			$this->_attrs['rollingSize'],
			new DAttr('enableStderrLog', 'bool', 'Enable stderr Log', 'radio', true, NULL, NULL, NULL, 0, 'log_enableStderrLog'),
            new DAttr('enableAioLog', 'bool', 'Enable AIO Logging', 'radio', true, NULL, NULL, NULL, 0, 'log_enableAioLog')
			);
		$this->_tblDef[$id]->setAttr($attrs, 'general:log', 'logging:log');
	}

	private function add_SERV_ACLOG($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Access Log');
		$attrs = array(
			$this->_attrs['fileName2']->dup(NULL, NULL, 'accessLog_fileName'),
 			new DAttr('pipedLogger', 'sel1', 'Piped Logger', 'select', true,
										  array('ext:logger'), NULL, NULL, 0, 'accessLog_pipedLogger'),
			$this->_attrs['logFormat'],
			$this->_attrs['logHeaders'],
			$this->_attrs['rollingSize'],
			$this->_attrs['keepDays'],
			$this->_attrs['compressArchive']
			);
		$this->_tblDef[$id]->setAttr($attrs, 'general:aclog', 'logging:accessLog');
	}

	private function add_SERV_EXPIRES($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Expires Settings');

		$attrs = $this->get_expires_attrs();

		$this->_tblDef[$id]->setAttr($attrs, 'general:expires', 'expires');
	}

	private function add_SERV_APACHECONF($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Apache Style Configurations');

		$attrs = array(
			$this->_attrs['apacheconf']
			);

		$this->_tblDef[$id]->setAttr($attrs, 'general' );

	}

	private function add_SERV_TUNING_OS($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'OS Optimization');

		$ssloptions = array( 'null' => '(built-in) OpenSSL internal engine',
				     'auto' => '(auto) Automatically use available devices',
				     'aesni' => '(aesni) Intel AES-NI engine',
				     'dynamic'=>'(dynamic) Dynamic engine loading support',
				     'cswift' => '(cswift) CryptoSwift hardware engine support',
				     'chil' => '(chil) nCipher hardware engine support',
				     'atalla' => '(atalla) Atalla hardware engine support',
				     'nuron' => '(nuron) Nuron hardware engine support',
				     'ubsec' => '(ubsec) UBSEC hardware engine support',
				     'aep' => '(aep) Aep hardware engine support',
				     'sureware' => '(sureware) SureWare hardware engine support',
				     '4758cca' => '(4758cca) IBM 4758 CCA hardware engine support' );

		$edoptions = array( 'best'    => 'best (All platforms)',
				    'poll'    => 'poll (All platforms)',
				    'epoll'   => 'epoll (Linux 2.6 kernel)',
				    'kqueue'  => 'kqueue (FreeBSD/Mac OS X)',
				    'devpoll' => 'devpoll (Solaris)');

		$sslDevice = new DAttr('SSLCryptoDevice', 'sel', 'SSL Hardware Accelerator', 'select', true, 0, $ssloptions);
		$sslDevice->_version = 1;

		$attrs = array(
			new DAttr('eventDispatcher', 'sel', 'I/O Event Dispatcher', 'select', true, 0, $edoptions),
			new DAttr('ramDiskPath', 'path', 'RAM Disk Path', 'text', true, 1, 'rw', $this->_options['text_size']),
			$sslDevice,
            new DAttr('SSLStrongDhKey', 'bool', 'SSL Strong DH Key', 'radio', true)
			);

		$this->_tblDef[$id]->setAttr($attrs, 'tuning', 'tuning');
	}

	private function add_SERV_TUNING_CONN($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Connection');

		$attrs = array(
			new DAttr('maxConnections', 'uint', 'Max Connections', 'text', false, 1),
			new DAttr('maxSSLConnections', 'uint', 'Max SSL Connections', 'text', false, 0),
			new DAttr('connTimeout', 'uint', 'Connection Timeout (secs)', 'text', false, 10, 1000000),
			new DAttr('maxKeepAliveReq', 'uint', 'Max Keep-Alive Requests', 'text', false, 0, 32767),
			new DAttr('smartKeepAlive', 'bool', 'Smart Keep-Alive', 'radio', false),
			new DAttr('keepAliveTimeout', 'uint', 'Keep-Alive Timeout (secs)', 'text', false, 0, 60),
			new DAttr('sndBufSize', 'uint', 'Send Buffer Size (bytes)', 'text', false, 0, 65535),
			new DAttr('rcvBufSize', 'uint', 'Receive Buffer Size (bytes)', 'text', false, 0, 65535),
			);

		$this->_tblDef[$id]->setAttr($attrs, 'tuning', 'tuning', 'connTuning');
	}

	private function add_SERV_TUNING_STATIC($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Static File Delivery Optimization');

		$aioblock_options = array( '1' => '128K', '2' => '256K', '3' => '512K',
					   '4' => '1M', '5' => '2M', '6' => '4M', '7' => '8M');

		$etag_options = array( '4'=>'iNode', '8'=>'Modified Time', '16'=>'Size', '0'=>'None');

		$useAIO_options = array('0'=>'No', '1'=>'Yes', '2'=>'Keep File Mapped (64 bit OS)');

		$attrs = array(
			new DAttr('maxCachedFileSize', 'uint', 'Max Cached Small File Size (bytes)', 'text', false, 0, 1048576),
			new DAttr('totalInMemCacheSize', 'uint', 'Total Small File Cache Size (bytes)', 'text', false, 0),
			new DAttr('maxMMapFileSize', 'uint', 'Max MMAP File Size (bytes)', 'text', false, 0),
			new DAttr('totalMMapCacheSize', 'uint', 'Total MMAP Cache Size (bytes)', 'text', false, 0),
			new DAttr('useSendfile', 'bool', 'Use sendfile()', 'radio'),
			new DAttr('useAIO', 'sel', 'Use Asynchronized disk I/O (AIO)', 'select', true, NULL, $useAIO_options ),
			new DAttr('AIOBlockSize', 'sel', 'AIO Block Size', 'select', true, 0, $aioblock_options),
			new DAttr('fileETag', 'checkboxOr', 'File ETag', 'checkboxgroup', true, 28, $etag_options),
			);

		$this->_tblDef[$id]->setAttr($attrs, 'tuning', 'tuning');
	}

	private function add_SERV_TUNING_REQ($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Request/Response');

		$attrs = array(
			new DAttr('maxReqURLLen', 'uint', 'Max Request URL Length (bytes)', 'text', false, 200, 8192),
			new DAttr('maxReqHeaderSize', 'uint', 'Max Request Header Size (bytes)', 'text', false, 1024, 16380),
			new DAttr('maxReqBodySize', 'uint', 'Max Request Body Size (bytes)', 'text', false, '1M', NULL ),
			new DAttr('maxDynRespHeaderSize', 'uint', 'Max Dynamic Response Header Size (bytes)', 'text', false, 200, 8192),
			new DAttr('maxDynRespSize', 'uint', 'Max Dynamic Response Body Size (bytes)', 'text', false, '1M')
			);
		$this->_tblDef[$id]->setAttr($attrs, 'tuning', 'tuning', 'reqTuning');
	}

	private function add_SERV_TUNING_GZIP($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'GZIP Compression');

		$parseFormat = "/^(\!)?(\*\/\*)|([A-z0-9_\-\.\+]+\/\*)|([A-z0-9_\-\.\+]+\/[A-z0-9_\-\.\+]+)$/";
		$parseHelp = '(!)MIME types, like */*, text/*, text/html, text/*, !text/css';

		$attrs = array(
			new DAttr('enableGzipCompress', 'bool', 'Enable Compression', 'radio', false),
			new DAttr('enableDynGzipCompress', 'bool', 'Enable Dynamic Compression', 'radio', false),
			new DAttr('gzipCompressLevel', 'uint', 'Compression Level (Dynamic Content)', 'text', true, 1, 9),
			new DAttr('compressibleTypes', 'parse', 'Compressible Types', 'textarea', true, $parseFormat, $parseHelp, 'rows="5" cols="50"', 1),
			new DAttr('gzipAutoUpdateStatic', 'bool', 'Auto Update Static File', 'radio'),
			new DAttr('gzipCacheDir', 'cust', 'Static GZIP Cache Directory', 'text', true, 1, 'rw', $this->_options['text_size']),

			new DAttr('gzipStaticCompressLevel', 'uint', 'Compression Level (Static File)', 'text', true, 1, 9),
			new DAttr('gzipMaxFileSize', 'uint', 'Max Static File Size (bytes)', 'text', true, '1K'),
			new DAttr('gzipMinFileSize', 'uint', 'Min Static File Size (bytes)', 'text', true, 200)
			);
		$this->_tblDef[$id]->setAttr($attrs, 'tuning', 'tuning', 'gzipTuning');
	}

	private function add_SERV_SEC_FILE($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'File Access');

		$parseFormat = "/^[0-7]{3,4}/";
		$parseHelp = '3 digits or more octet number. Default is 000';

		$attrs = array(
			new DAttr('followSymbolLink', 'sel', 'Follow Symbolic Link', 'select', false, 0, $this->_options['symbolLink']),
			new DAttr('checkSymbolLink', 'bool', 'Check Symbolic Link', 'radio', false),
			new DAttr('forceStrictOwnership', 'bool', 'Force Strict Ownership', 'radio', false),
			new DAttr('requiredPermissionMask', 'parse', 'Required Permission Mask', 'text', true, $parseFormat, $parseHelp),
			new DAttr('restrictedPermissionMask', 'parse', 'Restricted Permission Mask', 'text', true, $parseFormat, $parseHelp),
			new DAttr('restrictedScriptPermissionMask', 'parse', 'Script Restricted Permission Mask', 'text', true, $parseFormat, $parseHelp),
			new DAttr('restrictedDirPermissionMask', 'parse', 'Script Restricted Directory Permission Mask', 'text', true, $parseFormat, $parseHelp)
			);

		$this->_tblDef[$id]->setAttr($attrs, 'sec:file', 'security:fileAccessControl', 'fileAccessControl');
	}

	private function add_SERV_SEC_CGI($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'CGI Settings');

		$attrs = array(
			new DAttr('cgidSock', 'addr', 'CGI Daemon Socket', 'text', true, NULL, NULL, $this->_options['text_size']),

			new DAttr('maxCGIInstances', 'uint', 'Max CGI Instances', 'text', true, 1, 2000),
			new DAttr('minUID', 'uint', 'Minimum UID', 'text', true, 10),
			new DAttr('minGID', 'uint', 'Minimum GID', 'text', true, 5),
			new DAttr('forceGID', 'uint', 'Force GID', 'text', true, 0),
			$this->_attrs['cgiUmask'],
			$this->_attrs['priority']->dup(NULL, 'CGI Priority', 'CGIPriority'),
			new DAttr('CPUSoftLimit', 'uint', 'CPU Soft Limit (sec)', 'text', true, 0),
			new DAttr('CPUHardLimit', 'uint', 'CPU Hard Limit (sec)', 'text', true, 0),
			$this->_attrs['memSoftLimit'],
			$this->_attrs['memHardLimit'],
			$this->_attrs['procSoftLimit'],
			$this->_attrs['procHardLimit']
			);
		$this->_tblDef[$id]->setAttr($attrs, 'sec:cgi', 'security:CGIRLimit', 'cgiResource');
	}

	private function add_SERV_SEC_CONN($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Per Client Throttling');

		$attrs = array(
			$this->_attrs['staticReqPerSec'],
			$this->_attrs['dynReqPerSec'],
			$this->_attrs['outBandwidth'],
			$this->_attrs['inBandwidth'],
			new DAttr('softLimit', 'uint', 'Connection Soft Limit',  'text', true, 0),
			new DAttr('hardLimit', 'uint', 'Connection Hard Limit', 'text', true, 0),
			new DAttr('blockBadReq', 'bool', 'Block Bad Request', 'radio', true),
			new DAttr('gracePeriod', 'uint', 'Grace Period (sec)', 'text', true, 1, 3600),
			new DAttr('banPeriod', 'uint', 'Banned Period (sec)', 'text', true, 0)

			);
		$this->_tblDef[$id]->setAttr($attrs, 'sec:conn', 'security:perClientConnLimit', 'perClientConnLimit');
	}

	private function add_SERV_SEC_DENY($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Access Denied Directories');

		$attr = new DAttr('dir', 'dir', NULL, 'textarea', true, 2, ',', 'rows="15" cols="78" wrap=off', 2);
		$attrs = array( $attr );
		$this->_tblDef[$id]->setAttr($attrs, 'sec:denyDir', 'security:accessDenyDir', 'accessDenyDir');
		$this->_tblDef[$id]->_cols = 1;
	}

	private function add_S_RAILS($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Rack/Rails Settings');
		$attrs = array(
			new DAttr('rubyBin', 'file', 'Ruby Path', 'text', true, 1, 'x', $this->_options['text_size']),
			$this->_attrs['railsEnv'],
			$this->_attrs['ext_maxConns'],
			$this->_attrs['ext_env'],
			$this->_attrs['ext_initTimeout'],
			$this->_attrs['ext_retryTimeout'],
			$this->_attrs['pcKeepAliveTimeout'],
			$this->_attrs['ext_respBuffer'],
			$this->_attrs['ext_backlog'],
			$this->_attrs['ext_runOnStartUp'],
			new DAttr('extMaxIdleTime', 'uint', 'Max Idle Time', 'text', true, -1 ),
			$this->_attrs['priority']->dup(NULL, NULL, 'extAppPriority'),
			$this->_attrs['memSoftLimit'],
			$this->_attrs['memHardLimit'],
			$this->_attrs['procSoftLimit'],
			$this->_attrs['procHardLimit']);
		$this->_tblDef[$id]->setAttr($attrs, 'railsDefaults', 'railsDefaults', 'railsDefault');
	}

	private function add_S_RAILS_HOSTING($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Rack/Rails Shared Hosting');
		$attrs = array(
			new DAttr('enableRailsHosting', 'bool', 'Enable Shared Hosting', 'radio', true),
			new DAttr('railsAppLimit', 'uint', 'Max Apps per Account', 'text', true, 0, 20),
			new DAttr('rubyProcLimit', 'uint', 'Max Ruby Processes per Account', 'text', true, 0, 100)
		);
		$this->_tblDef[$id]->setAttr($attrs, 'railsDefaults', 'railsDefaults', 'railsHosting');
	}


	private function add_A_HTACCESS($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'HT Access');

		$attrs = array(
			$this->_attrs['allowOverride'],
			new DAttr('accessFileName', 'accf', 'Access File Name', 'text')
			);

		$this->_tblDef[$id]->setAttr($attrs, 'general:htaccess', 'htAccess', 'htaccess');
	}

	private function add_A_SECAC($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Access Control');

		$attrs = array(
			new DAttr('allow', 'subnet', 'Allowed List', 'textarea', true, NULL, NULL, 'rows="5" cols="60"',1, 'accessControl_allow'),
			new DAttr('deny', 'subnet', 'Denied List', 'textarea', true, NULL, NULL, 'rows="5" cols="60"', 1, 'accessControl_deny'));
		$this->_tblDef[$id]->setAttr($attrs, 'sec:ac', 'security:accessControl', 'accessControl' );
		$this->_tblDef[$id]->_cols = 1;
	}

	private function add_SERV_SEC_CC($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Request Filter');

		$attrs = array(
			new DAttr('enableCensorship', 'bool', 'Enable Request Filtering', 'radio'),
			new DAttr('logLevel', 'uint', 'Log Level', 'text', true, 0, 9, NULL, 0, 'censorLogLevel'),
			new DAttr('defaultAction', 'cust', 'Default Action', 'text', true, NULL, NULL, $this->_options['text_size']),
			new DAttr('scanPOST', 'bool', 'Scan Request Body', 'radio'),
			new DAttr('disableSecHtaccess', 'bool', 'Disable .htaccess Override', 'radio'),
			new DAttr('secAuditLogEngine', 'bool', 'Enable Security Audit Log', 'radio'),
			new DAttr('secAuditLog', 'file0', 'Security Audit Log', 'text', false, 2, 'r', $this->_options['text_size']));
		$this->_tblDef[$id]->setAttr($attrs, 'sec:cc', 'security:censorshipControl' );
	}

	private function add_VH_SEC_CC($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Request Filter');

		$attrs = array(
			new DAttr('enableCensorship', 'bool', 'Enable Request Filtering', 'radio'),
			new DAttr('logLevel', 'uint', 'Log Level', 'text', true, 0, 9, NULL, 0, 'censorLogLevel'),
			new DAttr('defaultAction', 'cust', 'Default Action', 'text', true, NULL, NULL, $this->_options['text_size']),
			new DAttr('scanPOST', 'bool', 'Scan Request Body', 'radio'));
		$this->_tblDef[$id]->setAttr($attrs, 'sec:cc', 'security:censorshipControl' );
	}

	private function add_A_SEC_CR_TOP($id)
	{
		$align = array('left', 'left', 'center', 'center');
		$this->_tblDef[$id] = new DTbl($id, 'Request Filtering Rule Set', 1, 'A_SEC_CR', $align, "filter");

		$attrs = array(
			new DAttr('name', 'cust', 'Name', NULL, true, NULL, NULL, NULL, 0, 'censorRuleSetName'),
			new DAttr('ruleSetAction', 'cust', 'Action'),
			new DAttr('enabled', 'bool', 'Enabled', 'radio', false, NULL, NULL, NULL, 0, 'censorRuleSetEnabled'),
			new DAttr('action', 'action', 'Action', NULL, false, 'A_SEC_CR', 'vEd')
			);
		$this->_tblDef[$id]->setAttr($attrs, 'sec:cr', 'security:censorshipRuleSet');
		$this->_tblDef[$id]->setRepeated('name');
	}

	private function add_A_SEC_CR($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Request Filtering Rule Set');

		$attrs = array(
			new DAttr('name', 'cust', 'Name', 'text', false, null, null, $this->_options['text_size'], 0, 'censorRuleSetName'),
			new DAttr('ruleSetAction', 'cust', 'Action', 'text', true, null, null, $this->_options['text_size']),
			new DAttr('enabled', 'bool', 'Enabled', 'radio', false, NULL, NULL, NULL, 0, 'censorRuleSetEnabled'),
			new DAttr('ruleSet', 'cust', 'Rules Definition', 'textarea1', true, NULL, NULL, 'rows="15" cols="60" wrap=off', 0, 'censorRuleSet'),
			$this->_attrs['note'],
			);
		$this->_tblDef[$id]->setAttr($attrs, 'sec:cr', 'security:censorshipRuleSet');
		$this->_tblDef[$id]->setRepeated('name');
	}

	private function add_A_CACHE_STORAGE($id)
	{
	    $this->_tblDef[$id] = new DTbl($id, 'Cache Storage Settings');
	    $cacheStorePath = new DAttr('cacheStorePath', 'cust', 'Storage Path', 'text', false, NULL, NULL, $this->_options['text_size']);
	    $cacheStorePath->_version = 2;
	    $cacheStorePath->_feature = 1;
	    $attrs = array(	$cacheStorePath );

	    $this->_tblDef[$id]->setAttr($attrs, 'cache:storage', 'cache:storage' );
	}

	private function add_A_CACHE_POLICY($id)
	{
	    $this->_tblDef[$id] = new DTbl($id, 'Cache Policy');
	    $attrs = $this->get_ctx_attrs('cache');
	    $this->_tblDef[$id]->setAttr($attrs, 'cache', 'cache' );
	}

	private function add_A_CACHE_NCURL($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Do-Not-Cache URL');
		$ncurl = new DAttr('noCacheUrl', 'cust', NULL, 'textarea1', true, NULL, NULL, 'rows="10" cols="60" wrap=off', 0, 'noCacheUrl');
		$ncurl->_version = 2;
		$ncurl->_feature = 1;
		$attrs = array($ncurl);
		$this->_tblDef[$id]->setAttr($attrs, 'cache', 'cache');
		$this->_tblDef[$id]->_cols = 1;
	}

	private function add_SERV_CACHE_NCDOMAIN($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Do-Not-Cache Domains');
		$ncdomain = new DAttr('noCacheDomain', 'cust', NULL, 'textarea1', true, NULL, NULL, 'rows="10" cols="60" wrap=off', 0, 'noCacheDomain');
		$ncdomain->_version = 2;
		$ncdomain->_feature = 1;
		$attrs = array($ncdomain);
		$this->_tblDef[$id]->setAttr($attrs, 'cache', 'cache');
		$this->_tblDef[$id]->_cols = 1;
	}

	private function add_A_EXT_TOP($id)
	{
		$align = array('left', 'left', 'left', 'center');
		$this->_tblDef[$id] = new DTbl($id, 'External Applications', 1, 'A_EXT_SEL', $align, "application");

		$attrs = array(
			$this->_attrs['ext_type'],
			$this->_attrs['name']->dup(NULL, NULL, 'extAppName'),
			new DAttr('address', 'cust', 'Address'),
			new DAttr('action', 'action', 'Action', NULL, false, $this->_options['extTbl'], 'vEd')
			);
		$this->_tblDef[$id]->setAttr($attrs, 'ext');
		$this->_tblDef[$id]->setRepeated( 'name' );
	}

	private function add_A_EXT_SEL($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'New External App');
		$this->_tblDef[$id]->_subTbls = $this->_options['extTbl'];

		$attrs[] =  $this->_attrs['ext_type'];
		$this->_tblDef[$id]->setAttr($attrs, 'ext', 'extProcessorList:extProcessor');
		$this->_tblDef[$id]->setRepeated( 'name' );
	}

	private function add_A_EXT_FCGI($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'FastCGI App Definition', 2);
		$attrs = array(
			$this->_attrs['name']->dup(NULL, NULL, 'extAppName'),
			$this->_attrs['ext_address'],
			$this->_attrs['note'],
			$this->_attrs['ext_maxConns'],
			$this->_attrs['ext_env'],
			$this->_attrs['ext_initTimeout'],
			$this->_attrs['ext_retryTimeout'],
			$this->_attrs['ext_persistConn'],
			$this->_attrs['pcKeepAliveTimeout'],
			$this->_attrs['ext_respBuffer'],
			$this->_attrs['ext_autoStart'],
			$this->_attrs['ext_path'],
			$this->_attrs['ext_backlog'],
			$this->_attrs['ext_instances'],
			$this->_attrs['ext_user'],
			$this->_attrs['ext_group'],
			$this->_attrs['cgiUmask'],
			$this->_attrs['ext_runOnStartUp'],
			new DAttr('extMaxIdleTime', 'uint', 'Max Idle Time', 'text', true, -1 ),
			$this->_attrs['priority']->dup(NULL, NULL, 'extAppPriority'),
			$this->_attrs['memSoftLimit'],
			$this->_attrs['memHardLimit'],
			$this->_attrs['procSoftLimit'],
			$this->_attrs['procHardLimit']);
		$this->_tblDef[$id]->setAttr($attrs, 'ext', NULL, 'extFCGI');
		$this->_tblDef[$id]->setRepeated( 'name' );
		$this->_tblDef[$id]->_defaultExtract = array('type'=>'fcgi');
	}

	private function add_A_EXT_FCGIAUTH($id)
	{
		$this->_tblDef[$id] = $this->DupTblDef('A_EXT_FCGI', $id, 'FastCGI Authorizer Definition');
        $this->_tblDef[$id]->_helpKey = 'extFCGIAuth';
		$this->_tblDef[$id]->_defaultExtract = array('type'=>'fcgiauth');
	}

	private function add_A_EXT_LSAPI($id)
	{
		$this->_tblDef[$id] = $this->DupTblDef('A_EXT_FCGI', $id, 'LiteSpeed SAPI App Definition');
		$this->_tblDef[$id]->_defaultExtract = array('type'=>'lsapi');
	}

	private function add_A_EXT_LOADBALANCER($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Load Balancer Definition', 2);

		$parseFormat = "/^(fcgi|fcigauth|lsapi|servlet|proxy)::.+$/";
		$parseHelp = 'ExtAppType::ExtAppName, e.g. fcgi::myphp, servlet::tomcat';

		$attrs = array( $this->_attrs['name']->dup(NULL, NULL, 'extAppName'),
			new DAttr('workers', 'parse', 'Workers', 'textarea', true, $parseFormat, $parseHelp, 'rows="3" cols="50"', 1, 'extWorkers'),
			$this->_attrs['note'],
			);
		$this->_tblDef[$id]->setAttr($attrs, 'ext', NULL, 'extLB');
		$this->_tblDef[$id]->setRepeated( 'name' );
		$this->_tblDef[$id]->_defaultExtract = array('type'=>'loadbalancer');
	}

	private function add_A_EXT_LOGGER($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Piped Logger Definition', 2);
		$attrs = array( $this->_attrs['name']->dup(NULL, NULL, 'extAppName'),
				new DAttr('address', 'addr', 'Address for remote logger (Optional)', 'text', true, NULL, NULL, $this->_options['text_size'], 0, 'extAppAddress'),
				$this->_attrs['note'],
				$this->_attrs['ext_maxConns'],
				$this->_attrs['ext_env'],
				$this->_attrs['ext_path'],
				$this->_attrs['ext_instances'],
				$this->_attrs['ext_user'],
				$this->_attrs['ext_group'],
				$this->_attrs['cgiUmask'],
				$this->_attrs['priority']->dup(NULL, NULL, 'extAppPriority'));
		$this->_tblDef[$id]->setAttr($attrs, 'ext');
		$this->_tblDef[$id]->setRepeated( 'name' );
		$this->_tblDef[$id]->_defaultExtract = array('type'=>'logger');
	}

	private function add_A_EXT_SERVLET($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Servlet Engine Definition', 2);
		$attrs = array( $this->_attrs['name']->dup(NULL, NULL, 'extAppName'),
						$this->_attrs['ext_address'],
						$this->_attrs['note'],
						$this->_attrs['ext_maxConns'],
						$this->_attrs['pcKeepAliveTimeout'],
						$this->_attrs['ext_env'],
						$this->_attrs['ext_initTimeout'],
						$this->_attrs['ext_retryTimeout'],
						$this->_attrs['ext_respBuffer']
			);
		$this->_tblDef[$id]->setAttr($attrs, 'ext');
		$this->_tblDef[$id]->setRepeated( 'name' );
		$this->_tblDef[$id]->_defaultExtract = array('type'=>'servlet');
	}

	private function add_A_EXT_PROXY($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Web Server Definition', 2);
		$attrs = array( $this->_attrs['name']->dup(NULL, NULL, 'extAppName'),
                        $this->_attrs['ext_address']->dup(NULL, NULL, 'expWSAddress'),
						$this->_attrs['note'],
						$this->_attrs['ext_maxConns'],
						$this->_attrs['pcKeepAliveTimeout'],
						$this->_attrs['ext_env'],
						$this->_attrs['ext_initTimeout'],
						$this->_attrs['ext_retryTimeout'],
						$this->_attrs['ext_respBuffer']
			);
		$this->_tblDef[$id]->setAttr($attrs, 'ext');
		$this->_tblDef[$id]->setRepeated( 'name' );
		$this->_tblDef[$id]->_defaultExtract = array('type'=>'proxy');
	}

	private function add_A_SCRIPT_TOP($id)
	{
		$align = array('center', 'center', 'center', 'center');
		$this->_tblDef[$id] = new DTbl($id, 'Script Handler Definition', 1, 'A_SCRIPT', $align, "script");

		$attrs = array(
			$this->_attrs['suffix'],
			$this->_attrs['scriptHandler_type'],
			$this->_attrs['scriptHandler'],
			new DAttr('action', 'action', 'Action', NULL, false, 'A_SCRIPT', 'Ed'));
		$this->_tblDef[$id]->setAttr($attrs, 'scriptHandler');
		$this->_tblDef[$id]->setRepeated('suffix');
	}

	private function add_A_SCRIPT($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Script Handler Definition', 2);

		$attrs = array(
			$this->_attrs['suffix'],
			$this->_attrs['scriptHandler_type'],
			$this->_attrs['scriptHandler'],
			$this->_attrs['note'],
			);
		$this->_tblDef[$id]->setAttr($attrs, 'scriptHandler', 'scriptHandlerList:scriptHandler');
		$this->_tblDef[$id]->setRepeated('suffix');
	}

	private function add_A_GEOIP_TOP($id)
	{
		$align = array('center', 'center', 'center');
		$this->_tblDef[$id] = new DTbl($id, 'IP to GeoLocation DB', 1, 'A_GEOIP', $align, "database");

		$attrs = array(
			$this->_attrs['geoipDBFile'],
			$this->_attrs['geoipDBCache'],
			new DAttr('action', 'action', 'Action', NULL, false, 'A_GEOIP', 'Ed'));
		$this->_tblDef[$id]->setAttr($attrs, 'geoipDB', NULL, 'geolocationDB');
		$this->_tblDef[$id]->setRepeated('geoipDBFile');
	}

	private function add_A_GEOIP($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'IP to GeoLocation DB configuration', 2);

		$attrs = array(
			$this->_attrs['geoipDBFile'],
			$this->_attrs['geoipDBCache'],
			$this->_attrs['note'],
			);
		$this->_tblDef[$id]->setAttr($attrs, 'geoipDB', 'ipToGeo:geoipDB', 'geolocationDB');
		$this->_tblDef[$id]->setRepeated('geoipDBFile');
	}

	private function add_VH_TOP($id)
	{
		$align = array('left', 'left', 'center');
		$this->_tblDef[$id] = new DTbl($id, 'Virtual Host List', 1, 'VH_TOP_D', $align, "web");

		$attrs = array(
			new DAttr('name', 'cust', 'Name'),
			new DAttr('vhRoot', 'cust', 'Virtual Host Root'),
			new DAttr('action', 'action', 'Action', NULL, false, 'VH_TOP_D', 'Xd'));
		$this->_tblDef[$id]->setAttr($attrs, 'vhTop');
		$this->_tblDef[$id]->setRepeated('name');
	}

	private function add_VH_TOP_D($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Virtual Hosts', 2);

		$chrootPath = new DAttr('chrootPath', 'path', 'ExtApp Chroot Path', 'text', true, 3, 'rx', $this->_options['text_size']);
		$chrootPath->_version = 1;

		$attrs = array(
			new DAttr('name', 'vhname', 'Virtual Host Name', 'text', false, NULL, NULL, NULL, 0, 'vhName'),
			new DAttr('vhRoot', 'path', 'Virtual Host Root', 'text', false, 2, '', $this->_options['text_size']),//no validation, maybe suexec owner
			new DAttr('configFile', 'file', 'Config File', 'text', false, 3, 'rwc', $this->_options['text_size']),
			$this->_attrs['note'],
			$this->_attrs['vh_allowSymbolLink'],
			$this->_attrs['vh_enableScript'],
			$this->_attrs['vh_restrained'],
			$this->_attrs['vh_maxKeepAliveReq'],
			$this->_attrs['vh_smartKeepAlive'],
			$this->_attrs['vh_setUIDMode'],
			$this->_attrs['vh_chrootMode'],
			$chrootPath,
            $this->_attrs['vh_bandwidth'],
			$this->_attrs['staticReqPerSec'],
			$this->_attrs['dynReqPerSec'],
			$this->_attrs['outBandwidth'],
			$this->_attrs['inBandwidth'],
			);
		$this->_tblDef[$id]->setAttr($attrs, 'vhTop', 'virtualHostList:virtualHost', 'vhTop');
		$this->_tblDef[$id]->setRepeated('name');
	}

	private function add_VH_BASE($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Base');

		$attrs = array(
			new DAttr('name', 'vhname', 'Virtual Host Name', 'text', false, NULL, NULL, NULL, 0, 'vhName'),
			new DAttr('vhRoot', 'path', 'Virtual Host Root', 'text', false, 2, 'rx', $this->_options['text_size']),
			new DAttr('configFile', 'file', 'Config File', 'text', false, 3, 'rwc', $this->_options['text_size']),
			$this->_attrs['note']
			);
		$this->_tblDef[$id]->setAttr($attrs);

	}

	private function add_VH_BASE_SECURITY($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Security');
		$chrootPath = new DAttr('chrootPath', 'path', 'ExtApp Chroot Path', 'text', true, 3, 'rx', $this->_options['text_size']);
		$chrootPath->_version = 1;

		$attrs = array(
			$this->_attrs['vh_allowSymbolLink'],
			$this->_attrs['vh_enableScript'],
			$this->_attrs['vh_restrained'],
			$this->_attrs['vh_setUIDMode'],
			$this->_attrs['vh_chrootMode'],
			$chrootPath,
            $this->_attrs['vh_bandwidth']
			);
		$this->_tblDef[$id]->setAttr($attrs);

	}

	private function add_VH_BASE_CONNECTION($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Connection');

		$attrs = array(

			$this->_attrs['vh_maxKeepAliveReq'],
			$this->_attrs['vh_smartKeepAlive']
			);
		$this->_tblDef[$id]->setAttr($attrs);

	}

	private function add_VH_BASE_THROTTLE($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Per Client Throttling');

		$attrs = array(
			$this->_attrs['staticReqPerSec'],
			$this->_attrs['dynReqPerSec'],
			$this->_attrs['outBandwidth'],
			$this->_attrs['inBandwidth']
			);
		$this->_tblDef[$id]->setAttr($attrs);

	}

	private function add_VH_SEC_EXTAPP($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'External Application');
		$chrootPath = new DAttr('chrootPath', 'path', 'ExtApp Chroot Path', 'text', true, 3, 'rx', $this->_options['text_size']);
		$chrootPath->_version = 1;

		$attrs = array(
			$this->_attrs['vh_setUIDMode'],
			$this->_attrs['vh_chrootMode'],
			$chrootPath
			);
		$this->_tblDef[$id]->setAttr($attrs, 'vhTop', 'security:extapp');

	}

	private function add_VH_GENERAL($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'General');

		$attrs = array(
			new DAttr('docRoot', 'path', 'Document Root', 'text', false, 3, '', $this->_options['text_size']),//no validation, maybe suexec owner
			$this->_attrs['vh_adminEmails'],
			$this->_attrs['vh_enableGzip'],
			$this->_attrs['enableIpGeo'],
			$this->_attrs['vh_spdyAdHeader']);
		$this->_tblDef[$id]->setAttr($attrs, 'general');
	}

	private function add_VH_LOG($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Virtual Host Log');

		$attrs = array(
			new DAttr('useServer', 'bool', 'Use Server\'s Log', 'radio', false, NULL, NULL, NULL, 0, 'logUseServer'),
			$this->_attrs['fileName3']->dup(NULL, NULL, 'vhlog_fileName'),
			new DAttr('logLevel', 'sel', 'Log Level', 'select', true, 0, $this->_options['logLevel'], NULL, 0, 'vhlog_logLevel'),
			$this->_attrs['rollingSize']
			);
		$this->_tblDef[$id]->setAttr($attrs, 'general:log', 'logging:log', 'vhLog');
	}

	private function add_VH_ACLOG($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Access Log');
		$use_options = array(0=>'Own Log File', 1=>'Server\'s Log File', 2=>'Disabled');

		$attrs = array(
			new DAttr('useServer', 'sel', 'Log Control', 'select', false, 0, $use_options, NULL, 0, 'aclogUseServer'),
			$this->_attrs['fileName3']->dup(NULL, NULL, 'vhaccessLog_fileName'),
 			new DAttr('pipedLogger', 'sel1', 'Piped Logger', 'select', true,
										  array( 'ext:logger'), NULL, NULL, 0, 'accessLog_pipedLogger'),
			$this->_attrs['logFormat'],
			$this->_attrs['logHeaders'],
			$this->_attrs['rollingSize'],
			$this->_attrs['keepDays'],
			new DAttr('bytesLog', 'file0', 'Bytes log', 'text', true, 3, 'r', $this->_options['text_size'], 0, 'accessLog_bytesLog'),
			$this->_attrs['compressArchive']
			);
		$this->_tblDef[$id]->setAttr($attrs, 'general:aclog', 'logging:accessLog', 'vhAcLog');
	}

	private function add_VH_INDXF($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Index Files');
		$options = array(0=>'No', 1=>'Yes', 2=>'Addition');

		$attrs = array(
			new DAttr('useServer', 'sel', 'Use Server Index Files', 'select', false, 0, $options, NULL, 0, 'indexUseServer'),
			$this->_attrs['indexFiles'],
			$this->_attrs['autoIndex'],
			new DAttr('autoIndexURI', 'uri', 'Auto Index URI', 'text', true, NULL, NULL, $this->_options['text_size'])
			);
		$this->_tblDef[$id]->setAttr($attrs, 'general:index', 'index');
	}

	private function add_VH_ERRPG_TOP($id)
	{
		$align = array('left', 'left', 'center');
		$this->_tblDef[$id] = new DTbl($id, 'Customized Error Pages', 1, 'VH_ERRPG', $align, "file");
		$errCodeOptions = &CustStatusCode::Get();
		$attrs = array(
			new DAttr('errCode', 'sel', 'Error Code', NULL, false, 0, $errCodeOptions),
			new DAttr('url', 'cust', 'URL'),
			new DAttr('action', 'action', 'Action', NULL, false, 'VH_ERRPG', 'Ed'));
		$this->_tblDef[$id]->setAttr($attrs, 'general:errorPage', NULL, 'errPage');
		$this->_tblDef[$id]->setRepeated('errCode');
	}

	private function add_VH_ERRPG($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Customized Error Pages', 2);
		$errCodeOptions = &CustStatusCode::Get();
		$attrs = array(
			new DAttr('errCode', 'sel', 'Error Code', 'select', false, 0, $errCodeOptions),
			new DAttr('url', 'custom', 'URL', 'text', false, NULL, NULL, $this->_options['text_size'], 0, 'errURL'),
			$this->_attrs['note'],
			);
		$this->_tblDef[$id]->setAttr($attrs, 'general:errorPage', 'customErrorPages:errorPage', 'errPage');
		$this->_tblDef[$id]->setRepeated('errCode');
	}

	private function add_VH_APACHECONF($id)
	{
		$this->add_SERV_APACHECONF( $id );
	}

	private function add_VH_EXPIRES($id)
	{
		$this->_tblDef[$id] = $this->DupTblDef('SERV_EXPIRES', $id);
	}

	private function add_VH_SECHL($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Hotlink Protection');

		$attrs = array(
			new DAttr('enableHotlinkCtrl', 'bool', 'Enable Hotlink Protection', 'radio', false),
			new DAttr('suffixes', 'suffix', 'Suffix', 'text', false, NULL, NULL, $this->_options['text_size'], 1),
			new DAttr('redirectUri', 'url', 'Redirect URL', 'text', true, NULL, NULL, $this->_options['text_size'] ),
			new DAttr('allowDirectAccess', 'bool', 'Allow Direct Access', 'radio', false),
			new DAttr('onlySelf', 'bool', 'Only Self Reference', 'radio', false),
			new DAttr('allowedHosts', 'domain', 'Allowed Domains', 'text', true, NULL, NULL, $this->_options['text_size'], 1),
			new DAttr('matchedHosts', 'cust', 'REGEX Matched Domains', 'text', true, NULL, NULL, $this->_options['text_size'])
			);
		$this->_tblDef[$id]->setAttr($attrs,  'sec:hotlink', 'security:hotlinkCtrl', 'vhHotlink');
	}

	private function get_realm_attrs()
	{
		return array(
				'realm_type' => new DAttr('type', 'sel', 'DB Type', 'select', false, NULL, $this->_options['realmType'], NULL, 0, 'realmType'),
				'realm_name' => new DAttr('name', 'name', 'Realm Name', 'text', false, NULL, NULL, $this->_options['text_size'], 0, 'realmName'),
				'realm_udb_maxCacheSize' => new DAttr('userDB:maxCacheSize', 'uint', 'User DB Max Cache Size',	'text', true, 0, '100K', NULL, 0, 'userDBMaxCacheSize'),
				'realm_udb_cacheTimeout' => new DAttr('userDB:cacheTimeout', 'uint', 'User DB Cache Timeout (secs)', 'text', true, 0, 3600, NULL, 0, 'userDBCacheTimeout'),
				'realm_gdb_maxCacheSize' => new DAttr('groupDB:maxCacheSize', 'uint', 'Group DB Max Cache Size', 'text', true, 0, '100K', NULL, 0, 'groupDBMaxCacheSize'),
				'realm_gdb_cacheTimeout' => new DAttr('groupDB:cacheTimeout', 'uint','Group DB Cache Timeout (secs)', 'text', true, 0, 3600, NULL, 0, 'groupDBCacheTimeout'));
	}

	private function add_VH_REALM_TOP($id)
	{
		$align = array('center', 'center', 'center');
		$this->_tblDef[$id] = new DTbl($id, 'Authorization Realms', 1, 'VH_REALM_SEL', $align, 'shield', FALSE);
		$realm_attr = $this->get_realm_attrs();

		$attrs = array(
				$realm_attr['realm_name'],
				$realm_attr['realm_type'],
				new DAttr('action', 'action', 'Action', NULL, false, $this->_options['realmTbl'], 'vEd')
		);
		$this->_tblDef[$id]->setAttr($attrs, 'sec:realm', NULL, 'realms');
		$this->_tblDef[$id]->setRepeated('name');
	}

	private function add_TP_REALM_TOP($id)
	{
		$align = array('center', 'center', 'center');
		$this->_tblDef[$id] = new DTbl($id, 'Authorization Realms', 1, 'TP_REALM_SEL', $align, 'shield', FALSE);
		$realm_attr = $this->get_realm_attrs();

		$attrs = array(
				$realm_attr['realm_name'],
				$realm_attr['realm_type'],
				new DAttr('action', 'action', 'Action', NULL, false, $this->_options['tp_realmTbl'], 'vEd')
		);
		$this->_tblDef[$id]->setAttr($attrs, 'sec:realm', NULL, 'realms');
		$this->_tblDef[$id]->setRepeated('name');
	}

	private function add_VH_REALM_SEL($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'New Realm');
		$this->_tblDef[$id]->_subTbls = $this->_options['realmTbl'];

		$realm_attr = $this->get_realm_attrs();
		$attrs[] = $realm_attr['realm_type'];
		$this->_tblDef[$id]->setAttr($attrs, 'sec:realm', 'security:realmList:realm', 'realms');
		$this->_tblDef[$id]->setRepeated('name');
	}


	private function add_TP_REALM_SEL($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'New Realm');
		$this->_tblDef[$id]->_subTbls = &$this->_options['tp_realmTbl'];

		$realm_attr = $this->get_realm_attrs();
		$attrs[] = $realm_attr['realm_type'];
		$this->_tblDef[$id]->setAttr($attrs, 'sec:realm', 'security:realmList:realm', 'realms');
		$this->_tblDef[$id]->setRepeated('name');
	}

	private function add_VH_REALM_FILE($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Password File Realm Definition', 2);
		$udbLoc = new DAttr('userDB:location', 'file', 'User DB Location', 'text', false, 3, 'rc', $this->_options['text_size'], 0, 'userDBLocation');
		$udbLoc->_href = '&t1=VH_UDB_TOP&r1=$R';
		$gdbLoc = new DAttr('groupDB:location', 'file', 'Group DB Location', 'text', true, 3, 'rc', $this->_options['text_size'], 0, 'GroupDBLocation');
		$gdbLoc->_href = '&t1=VH_GDB_TOP&r1=$R';

		$realm_attr = $this->get_realm_attrs();
		$attrs = array(
				$realm_attr['realm_name'],
				$udbLoc,
				$this->_attrs['note'],
				$realm_attr['realm_udb_maxCacheSize'],
				$realm_attr['realm_udb_cacheTimeout'],
				$gdbLoc,
				$realm_attr['realm_gdb_maxCacheSize'],
				$realm_attr['realm_gdb_cacheTimeout']
		);
		$this->_tblDef[$id]->setAttr($attrs, 'sec:realm');
		$this->_tblDef[$id]->setRepeated('name');
		$this->_tblDef[$id]->_defaultExtract = array('type'=>'file');
	}

	private function add_TP_REALM_FILE($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Password File Realm Definition', 2);

		$realm_attr = $this->get_realm_attrs();
		$attrs = array(
				$realm_attr['realm_name'],
				new DAttr('userDB:location', 'cust', 'User DB Location', 'text', false, 3, 'rc', $this->_options['text_size'], 0, 'userDBLocation'),
				$this->_attrs['note'],
				$realm_attr['realm_udb_maxCacheSize'],
				$realm_attr['realm_udb_cacheTimeout'],
				new DAttr('groupDB:location', 'cust', 'Group DB Location', 'text', true, 3, 'rc', $this->_options['text_size'], 0, 'GroupDBLocation'),
				$realm_attr['realm_gdb_maxCacheSize'],
				$realm_attr['realm_gdb_cacheTimeout']
		);
		$this->_tblDef[$id]->setAttr($attrs, 'sec:realm');
		$this->_tblDef[$id]->setRepeated('name');
		$this->_tblDef[$id]->_defaultExtract = array('type'=>'file');
	}

	private function add_VH_REALM_LDAP($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'LDAP Realm Definition', 2);

		$parseFormat = "/^(ldap|ldaps):\/\//";
		$parseHelp = 'start with ldap:// or ldaps://';

		$realm_attr = $this->get_realm_attrs();
		$attrs = array(
				$realm_attr['realm_name'],
				new DAttr('userDB:location', 'parse', 'User DB Location', 'text', false, $parseFormat, $parseHelp, $this->_options['text_size'], 0, 'userDBLocation'),
				new DAttr('userDB:attrPasswd', 'name', 'Password Attribute', 'text', true, NULL, NULL, NULL, 0, 'userDB_attrPasswd'),
				new DAttr('userDB:attrMemberOf', 'name', 'Member-of Attribute', 'text', true, NULL, NULL, NULL, 0, 'userDB_attrMemberOf'),
				$realm_attr['realm_udb_maxCacheSize'],
				$realm_attr['realm_udb_cacheTimeout'],
				new DAttr('groupDB:location', 'parse', 'Group DB Location', 'text', true, $parseFormat, $parseHelp, $this->_options['text_size'], 0, 'GroupDBLocation'),
				new DAttr('groupDB:attrGroupMember', 'name', 'Group Member Attribute', 'text', true, NULL, NULL, NULL, 0, 'groupDB_attrGroupMember'),
				$realm_attr['realm_gdb_maxCacheSize'],
				$realm_attr['realm_gdb_cacheTimeout'],
				new DAttr('LDAPBindDN', 'cust', 'LDAP Bind DN', 'text', true, NULL, NULL, $this->_options['text_size']),
				new DAttr('LDAPBindPasswd', 'cust', 'LDAP Bind Password', 'text'),
				$this->_attrs['note'],
		);
		$this->_tblDef[$id]->setAttr($attrs, 'sec:realm');
		$this->_tblDef[$id]->setRepeated('name');
		$this->_tblDef[$id]->_defaultExtract = array('type'=>'LDAP');
	}

	private function add_VH_UDB_TOP($id)
	{
		$align = array('left', 'left', 'center');
		$this->_tblDef[$id] = new DTbl($id, 'User DB Entries', 1, 'VH_UDB', $align, 'administrator', FALSE);

		$attrs = array(
			new DAttr('name', 'cust', 'User Name'),
			new DAttr('group', 'cust', 'Groups'),
			new DAttr('action', 'action', 'Action', NULL, false, 'VH_UDB', 'Ed')
			);
		$this->_tblDef[$id]->setAttr($attrs, 'UDB!$0');
		$this->_tblDef[$id]->setRepeated('name');
		$this->_tblDef[$id]->_hasB = true;
		$this->_tblDef[$id]->_showParentRef = true;
	}

	private function add_VH_UDB($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'User DB Entry', 2);

		$attrs = array(
			new DAttr('name', 'name', 'User Name', 'text', false, NULL, NULL, $this->_options['text_size']),
			new DAttr('group', 'name', 'Groups', 'text', true, NULL, NULL, $this->_options['text_size'], 1, 'UDBgroup'),
			new DAttr('pass', 'cust', 'New Password', 'password', true, NULL, NULL, $this->_options['text_size']),
			new DAttr('pass1', 'cust', 'Retype Password', 'password', true, NULL, NULL, $this->_options['text_size'])
			);
		$this->_tblDef[$id]->setAttr($attrs, 'UDB!$0');
		$this->_tblDef[$id]->setRepeated('name');
		$this->_tblDef[$id]->_defaultExtract = array('passwd'=>'$$');
		$this->_tblDef[$id]->_hasB = true;
		$this->_tblDef[$id]->_showParentRef = true;
	}

	private function add_VH_GDB_TOP($id)
	{
		$align = array('left', 'left', 'center');
		$this->_tblDef[$id] = new DTbl($id, 'Group DB Entries', 1, 'VH_GDB', $align, NULL, FALSE);

		$attrs = array(
			new DAttr('name', 'cust', 'Group Name'),
			new DAttr('users', 'cust', 'Users'),
			new DAttr('action', 'action', 'Action', NULL, false, 'VH_GDB', 'Ed')
			);
		$this->_tblDef[$id]->setAttr($attrs, 'GDB!$0');
		$this->_tblDef[$id]->setRepeated('name');
		$this->_tblDef[$id]->_hasB = true;
		$this->_tblDef[$id]->_showParentRef = true;
	}

	private function add_VH_GDB($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Group DB Entry', 2);
		$users = new DAttr('users', 'name', 'Users', 'textarea', true, NULL, NULL, 'rows="15" cols="50"', 1);
		$users->_glue = ' ';

		$attrs = array(
			new DAttr('name', 'name', 'Group Name', 'text', false, NULL, NULL, $this->_options['text_size']),
			$users,
			);
		$this->_tblDef[$id]->setAttr($attrs, 'GDB!$0');
		$this->_tblDef[$id]->setRepeated('name');
		$this->_tblDef[$id]->_hasB = true;
		$this->_tblDef[$id]->_showParentRef = true;
	}

	private function add_VH_REWRITE_CTRL($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Rewrite Control');

		$attrs = array(
			new DAttr('enable', 'bool', 'Enable Rewrite', 'radio', true, NULL, NULL, NULL, 0, 'enableRewrite'),
			new DAttr('logLevel', 'uint', 'Log Level', 'text', true, 0, 9, NULL, 0, 'rewriteLogLevel'));

		$this->_tblDef[$id]->setAttr($attrs, 'rewrite', 'rewrite');

	}

	private function add_VH_REWRITE_RULE($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Rewrite Rules');

		$attrs = array(
			new DAttr('rules', 'cust', NULL, 'textarea1', true, NULL, NULL, 'rows="15" cols="60" wrap=off'));
		$this->_tblDef[$id]->setAttr($attrs, 'rewrite', 'rewrite', 'rewriteRules');
		$this->_tblDef[$id]->_cols = 1;
	}

	private function add_VH_REWRITE_MAP_TOP($id)
	{
		$align = array('left', 'left', 'center');
		$this->_tblDef[$id] = new DTbl($id, 'Rewrite Map', 1, 'VH_REWRITE_MAP', $align, "redirect");

		$attrs = array(
			new DAttr('name', 'cust', 'Name'),
			new DAttr('location', 'cust', 'Location'),
			new DAttr('action', 'action', 'Action', NULL, false, 'VH_REWRITE_MAP', 'Ed')
			);
		$this->_tblDef[$id]->setAttr($attrs, 'rewrite:map', 'rewrite:map');
		$this->_tblDef[$id]->setRepeated('name');
	}

	private function add_VH_REWRITE_MAP($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Rewrite Map');

		$parseFormat = "/^((txt|rnd):\/*)|(int:(toupper|tolower|escape|unescape))$/";
		$parseHelp = 'txt:/location or rnd:/location or int:(toupper|tolower|escape|unescape)';

		$attrs = array(
			new DAttr('name', 'name',  'Name', 'text', false, NULL, NULL, NULL, 0, 'rewriteMapName'),
			new DAttr('location', 'parse', 'Location', 'text', true, $parseFormat, $parseHelp, $this->_options['text_size'], 0, 'rewriteMapLocation'),
			$this->_attrs['note'],
			);
		$this->_tblDef[$id]->setAttr($attrs, 'rewrite:map', 'rewrite:map');
		$this->_tblDef[$id]->setRepeated('name');
	}

	private function add_VH_CTX_TOP($id)
	{
		$align = array('center', 'left', 'center', 'center');
		$this->_tblDef[$id] = new DTbl($id, 'Context List', 1, 'VH_CTX_SEL', $align, array("NULL" => "file", "proxy" => "network", "redirect" => "redirect"));
		$o = new DAttr('order', 'ctxseq', 'Order');
		$o->_href = '&ctxseq=';
		$o->_FDE = 'NYN';

		$attrs = array(
			$this->_attrs['ctx_type'],
			new DAttr('uri', 'cust', 'URI'),
			$o,
			new DAttr('action', 'action', 'Action', NULL, false, $this->_options['ctxTbl'], 'vEd')
			);
		$this->_tblDef[$id]->setAttr($attrs, 'context');
		$this->_tblDef[$id]->setRepeated('uri');
	}

	private function add_VH_CTX_SEL($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'New Context');
		$this->_tblDef[$id]->_subTbls = $this->_options['ctxTbl'];

		$attrs[] = $this->_attrs['ctx_type'];
		$this->_tblDef[$id]->setAttr($attrs, 'context', 'contextList:context');
		$this->_tblDef[$id]->setRepeated('uri');
	}

	private function get_ctx_attrs($type)
	{
		if ($type == 'auth') {
			return array(
					new DAttr('realm', 'sel1', 'Realm', 'select', true, array('realms')),
					new DAttr('authName', 'name', 'Authentication Name', 'text'),
					new DAttr('required', 'cust', 'Require (Authorized Users/Groups)', 'text'),
					new DAttr('accessControl:allow', 'subnet', 'Access Allowed', 'textarea', true, NULL, NULL, 'rows="3" cols="60"', 1, 'accessAllowed'),
					new DAttr('accessControl:deny', 'subnet', 'Access Denied', 'textarea', true, NULL, NULL, 'rows="3" cols="60"', 1, 'accessDenied'),
					new DAttr('authorizer', 'sel1', 'Authorizer', 'select', true,	array('ext:fcgiauth'), NULL, NULL, 0, 'extAuthorizer')
			);
		}
		if ($type == 'rewrite') {
			return array(
					new DAttr('rewrite:enable', 'bool', 'Enable Rewrite', 'radio', true, NULL, NULL, NULL, 0, 'enableRewrite'),
					new DAttr('rewrite:inherit', 'bool', 'Rewrite Inherit', 'radio', true, NULL, NULL, NULL, 0, 'rewriteInherit'),
					new DAttr('rewrite:base', 'uri', 'Rewrite Base', 'text', true, NULL, NULL, $this->_options['text_size'], 0, 'rewriteBase'),
					new DAttr('rewrite:rules', 'cust', 'Rewrite Rules', 'textarea1', true, NULL, NULL, 'rows="6" cols="60" wrap=off', 0, 'rewriteRules')
			);
		}
		if ($type == 'charset') {
			return array(
					new DAttr('addDefaultCharset', 'sel', 'Add Default Charset', 'select', true, 0, $this->_options['on_off']),
					new DAttr('defaultCharsetCustomized', 'cust', 'Customized Default Charset', 'text'),
					$this->_attrs['enableIpGeo']
			);
		}
		if ($type == 'uri') {
			return array(
					$this->_attrs['ctx_uri'],
					$this->_attrs['ctx_order']);
		}
		if ($type == 'cache') {
			$cache = array(
					new DAttr('cachePolicy:enableCache', 'bool', 'Enable Public Cache', 'radio', true),
					new DAttr('cachePolicy:checkPublicCache', 'bool', 'Check Public Cache', 'radio', true, NULL, NULL, NULL, 0, 'checkPublicCache'),
					new DAttr('cachePolicy:maxCacheObjSize', 'uint', 'Max Object Size', 'text', true, '1K', '10M'),
					new DAttr('cachePolicy:expireInSeconds', 'uint', 'Cache Expire Time (seconds)', 'text', true, 0),
					new DAttr('cachePolicy:maxStaleAge', 'uint', 'Cache Stale Age (seconds)', 'text', true, 0),
					new DAttr('cachePolicy:qsCache', 'bool', 'Cache Request with Query String', 'radio', true),
					new DAttr('cachePolicy:reqCookieCache', 'bool', 'Cache Request with Cookie', 'radio', true),
					new DAttr('cachePolicy:respCookieCache', 'bool', 'Cache Response with Cookie', 'radio', true),
					new DAttr('cachePolicy:ignoreReqCacheCtrl', 'bool', 'Ignore Request Cache-Control', 'radio', true),
					new DAttr('cachePolicy:ignoreRespCacheCtrl', 'bool', 'Ignore Response Cache-Control', 'radio', true),
					new DAttr('cachePolicy:enablePrivateCache', 'bool', 'Enable Private Cache', 'radio', true),
					new DAttr('cachePolicy:checkPrivateCache', 'bool', 'Check Private Cache', 'radio', true, NULL, NULL, NULL, 0, 'checkPrivateCache'),
					new DAttr('cachePolicy:privateExpireInSeconds', 'uint', 'Private Cache Expire Time (seconds)', 'text', true, 0)
				);
			$c = count($cache);
			for ( $i = 0 ; $i < $c ; $i++ ) {
				$cache[$i]->_version = 2;
				$cache[$i]->_feature = 1;
			}

			return $cache;
		}
	}

	private function add_VH_CTXG($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Static Context Definition', 2);
		$attrs = array_merge(
				$this->get_ctx_attrs('uri'),
				array(
						$this->_attrs['ctx_location'],
						new DAttr('allowBrowse', 'bool', 'Accessible', 'radio', false),
						$this->_attrs['note']),
				$this->get_expires_attrs(),
				array(
						$this->_attrs['extraHeaders'],

						new DAttr('addMIMEType', 'parse', 'MIME Type', 'textarea', true,
								"/[A-z0-9_\-\.\+]+\/[A-z0-9_\-\.\+]+(\s+[A-z0-9_\-\+]+)+/",
								'MIME_type suffix suffix, next MIME_type... MIME type is like text/html, followed by one or more suffix types separated by space. Use comma to separate different MIME types.',
								'rows="2" cols="60"', 1),
						new DAttr('forceType', 'parse', 'Force MIME Type', 'text', true,
								"/^([A-z0-9_\-\.\+]+\/[A-z0-9_\-\.\+]+)|(NONE)$/i",
								'MEME_type like text/html, or put "NONE" to disable inherited Force Type.'),
						new DAttr('defaultType', 'parse', 'Default MIME Type', 'text', true,
								"/^[A-z0-9_\-\.\+]+\/[A-z0-9_\-\.\+]+$/", 'MEME_type like text/html'),
						$this->_attrs['indexFiles'],
						$this->_attrs['autoIndex'],
						$this->_attrs['allowOverrideContext']),
				$this->get_ctx_attrs('auth'),
				$this->get_ctx_attrs('rewrite'),
				$this->get_ctx_attrs('charset'),
				$this->get_ctx_attrs('cache'),
				array($this->_attrs['apacheconf'])
		);
		$this->_tblDef[$id]->setAttr($attrs, 'context', NULL, 'generalContext');
		$this->_tblDef[$id]->setRepeated('uri');
		$this->_tblDef[$id]->_defaultExtract = array('type'=>'NULL');
	}

	private function add_VH_CTXJ($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Java WebApp Context Definition', 2);
		$attrs = array_merge(
				array(
						new DAttr('uri', 'uri', 'URI', 'text', false, NULL, NULL, $this->_options['text_size']),
						$this->_attrs['ctx_order'],
						$this->_attrs['ctx_location']->dup(NULL, NULL, 'javaWebApp_location'),
						$this->_attrs['ctx_shandler'],
						$this->_attrs['note']),
				$this->get_expires_attrs(),
				array(
						$this->_attrs['extraHeaders'],
						$this->_attrs['indexFiles'],
						$this->_attrs['autoIndex'],
						$this->_attrs['allowOverrideContext']),
				$this->get_ctx_attrs('auth'),
				$this->get_ctx_attrs('cache'),
				$this->get_ctx_attrs('charset')
		);
		$this->_tblDef[$id]->setAttr($attrs, 'context', NULL, 'javaWebAppContext');
		$this->_tblDef[$id]->setRepeated('uri');
		$this->_tblDef[$id]->_defaultExtract = array('type'=>'webapp');
	}

	private function add_VH_CTXRL($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Rack/Rails Context Definition', 2);
		$attrs = array_merge(
				$this->get_ctx_attrs('uri'),
				array(
                        $this->_attrs['ctx_location']->dup(NULL, NULL, 'rails_location'),
						$this->_attrs['note'],
						$this->_attrs['railsEnv'],
						new DAttr('maxConns', 'uint', 'Max Connections', 'text', true, 1, 2000),
						$this->_attrs['ext_env']),
				$this->get_expires_attrs(),
				array(
						$this->_attrs['extraHeaders'],
						$this->_attrs['indexFiles'],
						$this->_attrs['autoIndex'],
						$this->_attrs['allowOverrideContext']),
				$this->get_ctx_attrs('auth'),
				$this->get_ctx_attrs('rewrite'),
				$this->get_ctx_attrs('cache'),
				$this->get_ctx_attrs('charset')
		);
		$this->_tblDef[$id]->setAttr($attrs, 'context', NULL, 'railsContext');
		$this->_tblDef[$id]->setRepeated('uri');
		$this->_tblDef[$id]->_defaultExtract = array('type'=>'rails');
	}

	private function add_VH_CTXS($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Servlet Context Definition', 2);
		$attrs = array_merge(
				$this->get_ctx_attrs('uri'),
				array(
						$this->_attrs['ctx_shandler'],
						$this->_attrs['note'],
						$this->_attrs['extraHeaders'],
						$this->_attrs['allowOverrideContext']),
				$this->get_ctx_attrs('auth'),
				$this->get_ctx_attrs('cache'),
				$this->get_ctx_attrs('charset')
		);
		$this->_tblDef[$id]->setAttr($attrs, 'context', NULL, 'servletContext');
		$this->_tblDef[$id]->setRepeated('uri');
		$this->_tblDef[$id]->_defaultExtract = array('type'=>'servlet');
	}

	private function add_VH_CTXF($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'FCGI Context Definition', 2);
		$attrs = array_merge(
				$this->get_ctx_attrs('uri'),
				array(
						new DAttr('handler', 'sel1', 'Fast CGI App', 'select', false,
						  array('ext:fcgi'), NULL, NULL, 0, 'fcgiapp'),
						$this->_attrs['note'],
						$this->_attrs['extraHeaders'],
						$this->_attrs['allowOverrideContext']),
				$this->get_ctx_attrs('auth'),
				$this->get_ctx_attrs('cache'),
				$this->get_ctx_attrs('charset')
		);
		$this->_tblDef[$id]->setAttr($attrs, 'context', NULL, 'fcgiContext');
		$this->_tblDef[$id]->setRepeated('uri');
		$this->_tblDef[$id]->_defaultExtract = array('type'=>'fcgi');
	}

	private function add_VH_CTXL($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'LiteSpeed SAPI Context Definition', 2);
		$attrs = array_merge(
				$this->get_ctx_attrs('uri'),
				array(
						new DAttr('handler', 'sel1', 'LSAPI App', 'select', false,
						  array('ext:lsapi'), NULL, NULL, 0, 'lsapiapp'),
						$this->_attrs['note'],
						$this->_attrs['extraHeaders'],
						$this->_attrs['allowOverrideContext']),
				$this->get_ctx_attrs('auth'),
				$this->get_ctx_attrs('cache'),
				$this->get_ctx_attrs('charset')
		);
		$this->_tblDef[$id]->setAttr($attrs, 'context', NULL, 'lsapiContext');
		$this->_tblDef[$id]->setRepeated('uri');
		$this->_tblDef[$id]->_defaultExtract = array('type'=>'lsapi');
	}

	private function add_VH_CTXB($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Load Balancer Context Definition', 2);
		$attrs = array_merge(
				$this->get_ctx_attrs('uri'),
				array(
						new DAttr('handler', 'sel1', 'Load Balancer', 'select', false,
						  array('ext:loadbalancer'), NULL, NULL, 0, 'lbapp'),
						$this->_attrs['note'],
						$this->_attrs['allowOverrideContext']),
				$this->get_ctx_attrs('auth'),
				$this->get_ctx_attrs('cache'),
				$this->get_ctx_attrs('charset')
		);
		$this->_tblDef[$id]->setAttr($attrs, 'context');
		$this->_tblDef[$id]->setRepeated('uri');
		$this->_tblDef[$id]->_defaultExtract = array('type'=>'loadbalancer');
	}

	private function add_VH_CTXP($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Proxy Context Definition', 2);
		$attrs = array_merge(
				$this->get_ctx_attrs('uri'),
				array(
						new DAttr('handler', 'sel1', 'Web Server', 'select', false,
						  array('ext:proxy'), NULL, NULL, 0, 'proxyWebServer'),
						$this->_attrs['note'],
						$this->_attrs['extraHeaders']),
				$this->get_ctx_attrs('auth'),
				$this->get_ctx_attrs('cache'),
				$this->get_ctx_attrs('charset')
		);
		$this->_tblDef[$id]->setAttr($attrs, 'context', NULL, 'proxyContext');
		$this->_tblDef[$id]->setRepeated('uri');
		$this->_tblDef[$id]->_defaultExtract = array('type'=>'proxy');
	}

	private function add_VH_CTXC($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'CGI Context Definition', 2);
		$attrs = array_merge(
				$this->get_ctx_attrs('uri'),
				array(
						$this->_attrs['ctx_location']->dup(NULL, 'Path', 'cgi_path'),
						$this->_attrs['note'],
						$this->_attrs['extraHeaders'],
						new DAttr('allowSetUID', 'bool', 'Allow Set UID', 'radio'),
						$this->_attrs['allowOverrideContext']),
				$this->get_ctx_attrs('auth'),
				$this->get_ctx_attrs('rewrite'),
				$this->get_ctx_attrs('cache'),
				$this->get_ctx_attrs('charset'),
				array($this->_attrs['apacheconf'])
		);
		$this->_tblDef[$id]->setAttr($attrs, 'context', NULL, 'cgiContext');
		$this->_tblDef[$id]->setRepeated('uri');
		$this->_tblDef[$id]->_defaultExtract = array('type'=>'cgi');
	}

	private function add_VH_CTXR($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Redirect Context Definition', 2);

		$options = CustStatusCode::GetRedirect();

		$attrs = array_merge(
				$this->get_ctx_attrs('uri'),
				array(
						new DAttr('externalRedirect', 'bool',  'External Redirect', 'radio', false, NULL, NULL, NULL, 0, 'externalredirect'),
						new DAttr('statusCode', 'sel', 'Status Code', 'select', true, 0, $options, NULL, 0, 'statuscode'),
						new DAttr('location', 'url', 'Destination URI', 'text', true, NULL, NULL, $this->_options['text_size'], 0, 'destinationuri'),
						$this->_attrs['note'],
						$this->_attrs['allowOverrideContext']),
				$this->get_ctx_attrs('auth')
		);
		$this->_tblDef[$id]->setAttr($attrs, 'context', NULL, 'redirectContext');
		$this->_tblDef[$id]->setRepeated('uri');
		$this->_tblDef[$id]->_defaultExtract = array('type'=>'redirect');
	}

	private function add_VH_WEBSOCKET_TOP($id)
	{
		$align = array('left', 'left', 'center');
		$this->_tblDef[$id] = new DTbl($id, 'Web Socket Proxy Setup', 1, 'VH_WEBSOCKET', $align, 'web_link');

		$attrs = array(
				new DAttr('uri', 'cust', 'URI'),
				new DAttr('address', 'cust', 'Address'),
				new DAttr('action', 'action', 'Action', NULL, false, 'VH_WEBSOCKET', 'Ed'));
		$this->_tblDef[$id]->setAttr($attrs, 'websocket');
		$this->_tblDef[$id]->setRepeated('uri');
	}

	private function add_VH_WEBSOCKET($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Web Socket Definition', 2);

		$attrs = array(
				$this->_attrs['ctx_uri']->dup(NULL,NULL,'wsuri'),
				$this->_attrs['ext_address']->dup(NULL, NULL, 'wsaddr'),
				$this->_attrs['note'],
		);
		$this->_tblDef[$id]->setAttr($attrs, 'websocket', 'websocketList:websocket');
		$this->_tblDef[$id]->setRepeated('uri');
	}

	private function add_VH_FRONTPG($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Front Page Server Extension');

		$parseFormat = "/^[ ]*(([A-z0-9_\-]+)\.)*([A-z0-9_\-]+):[0-9]+[ ]*$/";
		$parseHelp = 'domain:port, e.g. www.domain1.com:80, www.domain2.com:443';

		$attrs = array(
			new DAttr('enable', 'bool', 'Enable', 'radio', true, NULL, NULL, NULL, 0, 'enableFrontPage'),
			new DAttr('disableAdmin', 'bool', 'Disable Admin', 'radio'),
			new DAttr('domainNames', 'parse', 'Domain Names', 'textarea', true, $parseFormat, $parseHelp, 'rows="3" cols="50"', 1)
			);

		$this->_tblDef[$id]->setAttr($attrs, 'addon:frontPage', 'frontPage');
	}

	private function add_VH_AWSTATS($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'AWStats Integration');
		$mode_options = array(0=>'Disabled', 1=>'Static', 2=>'Dynamic');
		$interval_options = array(3600=>'Hourly', 86400=>'Daily');

		$attrs = array(
			new DAttr('updateMode', 'sel', 'Update Mode', 'select', false, 0, $mode_options),
			new DAttr('workingDir', 'path', 'Working Directory', 'text', false, 3, 'r', $this->_options['text_size']),
			new DAttr('awstatsURI', 'parse', 'AWStats URI', 'text', false, "/^\/.+\/$/", 'starts with / and ends with /', $this->_options['text_size']),
			new DAttr('siteDomain', 'domain', 'Site Domain', 'text', false, NULL, NULL, $this->_options['text_size']),
			new DAttr('siteAliases', 'cust', 'Site Aliases', 'text', false, NULL, NULL, $this->_options['text_size']),
			new DAttr('updateInterval', 'sel', 'Update Interval', 'select', false, 0, $interval_options),
			new DAttr('updateOffset', 'uint', 'Update Offset (secs)', 'text', true, 0, 86399),
			new DAttr('securedConn', 'bool', 'Secured Connection',  'radio', false),
			'realm'=>new DAttr('realm', 'sel1', 'Authentication Realm', 'select', true, array('realms') )
			);
		$this->_tblDef[$id]->setAttr($attrs, 'addon:awstats', 'awstats', 'awstats');
	}

	private function add_TP_TOP($id)
	{
		$align = array('left', 'left', 'center');
		$this->_tblDef[$id] = new DTbl($id, 'Virtual Host Template List', 1, 'TP1', $align, "form");

		$attrs = array(
			new DAttr('name', 'cust', 'Name'),
			new DAttr('listeners', 'cust', 'Mapped Listeners'),
			new DAttr('action', 'action', 'Action', NULL, false, 'TP1', 'Xd'));
		$this->_tblDef[$id]->setAttr($attrs, 'tpTop');
		$this->_tblDef[$id]->setRepeated('name');
	}

	private function add_TP($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Template Location', 2);

		$attrs = array(
			new DAttr('name', 'vhname', 'Template Name', 'text', false, NULL, NULL, $this->_options['text_size'], 0, 'templateName'),
			$this->_attrs['tp_templateFile'],
			$this->_attrs['tp_listeners'],
			$this->_attrs['note']
			);
		$this->_tblDef[$id]->setAttr($attrs);
	}

	private function add_TP1($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Virtual Host Template', 2);

		$attrs = array(
			new DAttr('name', 'vhname', 'Template Name', 'text', false, NULL, NULL, $this->_options['text_size']),
			$this->_attrs['tp_templateFile'],
			$this->_attrs['tp_listeners'],
			$this->_attrs['note']
			);
		$this->_tblDef[$id]->setAttr($attrs, 'tpTop');
		$this->_tblDef[$id]->setRepeated('name');
	}

	private function add_TP_MEMBER_TOP($id)
	{
		$align = array('left', 'left', 'center');
		$this->_tblDef[$id] = new DTbl($id, 'Member Virtual Hosts', 1, 'TP_MEMBER', $align, "web", FALSE);

		$attrs = array(
			$this->_attrs['tp_vhName'],
			$this->_attrs['tp_vhDomain'],
			new DAttr('action', 'action', 'Action', NULL, false, 'TP_MEMBER', 'vEdi'));

		$this->_tblDef[$id]->setAttr($attrs, 'member', 'member');
		$this->_tblDef[$id]->setRepeated('vhName');
	}

	private function add_TP_MEMBER($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Member Virtual Host', 2);
		$vhroot = new DAttr('vhRoot', 'path', 'Member Virtual Host Root', 'text', true, 2, 'r', $this->_options['text_size'], 0, 'memberVHRoot');
		$vhroot->_note = 'Optional: You can set up a different VH_ROOT other than the default one';
		$attrs = array(
			$this->_attrs['tp_vhName'],
			$this->_attrs['tp_vhDomain'],
			new DAttr('vhAliases', 'domain', 'Aliases', 'text', true, NULL, NULL, $this->_options['text_size'], 1, 'templateVHAliases'),
			$vhroot
			);

		$this->_tblDef[$id]->setAttr($attrs, 'member', 'member');
		$this->_tblDef[$id]->setRepeated('vhName');
	}

	private function add_TP_GENERAL($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Template Settings');
			// for gui use only
		$attrs = array(
				$this->_attrs['tp_vhRoot'],
				$this->_attrs['tp_vrFile']->dup('configFile', 'Config File', NULL),
				$this->_attrs['vh_maxKeepAliveReq'],
				$this->_attrs['vh_smartKeepAlive'],
				$this->_attrs['tp_vrFile']->dup('docRoot', 'Document Root', NULL),
				$this->_attrs['vh_adminEmails'],
				$this->_attrs['vh_enableGzip'],
				$this->_attrs['enableIpGeo'],
				$this->_attrs['vh_spdyAdHeader']
			);
// to do: need check path contain VH variable.
		$this->_tblDef[$id]->setAttr($attrs, 'general');
	}

	private function add_TP_GENERAL1($id)
	{
		// for file use only
		$this->_tblDef[$id] = new DTbl($id);

		$attrs = array(
			$this->_attrs['tp_vhRoot'],
			$this->_attrs['tp_vrFile']->dup('configFile', 'Config File', NULL),
			$this->_attrs['vh_maxKeepAliveReq'],
			$this->_attrs['vh_smartKeepAlive']
			);
// to do: need check path contain VH variable.
		$this->_tblDef[$id]->setAttr($attrs, 'general');
	}

	private function add_TP_GENERAL2($id)
	{
		// for file use only
		$this->_tblDef[$id] = new DTbl($id);
		$attrs = array(
				$this->_attrs['tp_vrFile']->dup('docRoot', 'Document Root', NULL),
				$this->_attrs['vh_adminEmails'],
				$this->_attrs['vh_enableGzip'],
				$this->_attrs['enableIpGeo'],
				$this->_attrs['vh_spdyAdHeader']
			);
		$this->_tblDef[$id]->setAttr($attrs, 'general');
	}

	private function add_TP_LOG($id)
	{
		$this->_tblDef[$id] = $this->DupTblDef('VH_LOG', $id);
		$this->_tblDef[$id]->_dattrs[1] =  $this->_attrs['tp_vrFile'];
	}

	private function add_TP_ACLOG($id)
	{
		$this->_tblDef[$id] = $this->DupTblDef('VH_ACLOG', $id);
		$this->_tblDef[$id]->_dattrs[1] =  $this->_attrs['tp_vrFile'];
	}

	private function add_TP_SEC_FILE($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'File Access Control');

		$attrs = array(
			$this->_attrs['vh_allowSymbolLink'],
			$this->_attrs['vh_enableScript'],
			$this->_attrs['vh_restrained']
			);

		$this->_tblDef[$id]->setAttr($attrs, 'sec:file');
	}

	private function add_TP_SEC_CGI($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'ExtApp Resource Control');
		$chroot_Path = new DAttr('chrootPath', 'path', 'ExtApp Chroot Path', 'text', true, 3, 'rx', $this->_options['text_size']);
		$chroot_Path->_version = 1;

		$attrs = array(
			$this->_attrs['vh_setUIDMode'],
			$this->_attrs['vh_chrootMode'],
			$chroot_Path
			);
		$this->_tblDef[$id]->setAttr($attrs, 'sec:cgi');
	}

	private function add_TP_SEC_CONN($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Per Client Throttling');

		$attrs = array(
			$this->_attrs['staticReqPerSec'],
			$this->_attrs['dynReqPerSec'],
			$this->_attrs['outBandwidth'],
			$this->_attrs['inBandwidth']
			);
		$this->_tblDef[$id]->setAttr($attrs, 'sec:conn');
	}

	private function add_TP_EXT_TOP($id)
	{
		$align = array('center', 'center', 'left', 'center');
		$this->_tblDef[$id] = new DTbl($id, 'External Applications', 1, 'TP_EXT_SEL', $align);

		$attrs = array(
			$this->_attrs['ext_type'],
			new DAttr('name', 'cust', 'Name'),
			new DAttr('address', 'cust', 'Address'),
			new DAttr('action', 'action', 'Action', NULL, false, $this->_options['tp_extTbl'], 'vEd')
			);
		$this->_tblDef[$id]->setAttr($attrs, 'ext');
		$this->_tblDef[$id]->setRepeated( 'name' );
	}

	private function add_TP_EXT_SEL($id)
	{
		$this->_tblDef[$id] = $this->DupTblDef('A_EXT_SEL', $id);
		$this->_tblDef[$id]->_subTbls = &$this->_options['tp_extTbl'];
	}

	private function add_TP_EXT_FCGI($id)
	{
		$this->_tblDef[$id] = $this->DupTblDef('A_EXT_FCGI', $id);
		$this->_tblDef[$id]->_dattrs[0] = $this->_attrs['tp_name'];
	}

	private function add_TP_EXT_FCGIAUTH($id)
	{
		$this->_tblDef[$id] = $this->DupTblDef('A_EXT_FCGIAUTH', $id);
		$this->_tblDef[$id]->_dattrs[0] = $this->_attrs['tp_name'];
	}

	private function add_TP_EXT_LSAPI($id)
	{
		$this->_tblDef[$id] = $this->DupTblDef('A_EXT_LSAPI', $id);
		$this->_tblDef[$id]->_dattrs[0] = $this->_attrs['tp_name'];
	}

	private function add_TP_EXT_LOADBALANCER($id)
	{
		$this->_tblDef[$id] = $this->DupTblDef('A_EXT_LOADBALANCER', $id);
		$this->_tblDef[$id]->_dattrs[0] = $this->_attrs['tp_name'];
	}

	private function add_TP_EXT_LOGGER($id)
	{
		$this->_tblDef[$id] = $this->DupTblDef('A_EXT_LOGGER', $id);
		$this->_tblDef[$id]->_dattrs[0] = $this->_attrs['tp_name'];
	}

	private function add_TP_EXT_SERVLET($id)
	{
		$this->_tblDef[$id] = $this->DupTblDef('A_EXT_SERVLET', $id);
		$this->_tblDef[$id]->_dattrs[0] = $this->_attrs['tp_name'];
	}

	private function add_TP_EXT_PROXY($id)
	{
		$this->_tblDef[$id] = $this->DupTblDef('A_EXT_PROXY', $id);
		$this->_tblDef[$id]->_dattrs[0] = $this->_attrs['tp_name'];
	}

	private function add_TP_AWSTATS($id)
	{
		$this->_tblDef[$id] = $this->DupTblDef('VH_AWSTATS', $id);
		$this->_tblDef[$id]->_dattrs[1] = $this->_attrs['tp_vrFile']->dup('workingDir', 'Working Directory', NULL);
	}

	private function add_L_TOP($id)
	{
		$align = array('center', 'center', 'center', 'center', 'center');
		$this->_tblDef[$id] = new DTbl($id, 'Listener List', 1, 'L_GENERAL1', $align, "link");

		$attrs = array(
			new DAttr('name', 'cust', 'Listener Name'),
			new DAttr('ip', 'cust', 'IP Address'),
			new DAttr('port', 'cust', 'Port'),
			new DAttr('secure', 'bool', 'Secure'),
			new DAttr('action', 'action', 'Action', NULL, false, 'L_GENERAL1', 'Xd'),

			);
		$this->_tblDef[$id]->setAttr($attrs, 'listeners');
		$this->_tblDef[$id]->setRepeated('name');
	}

	private function add_L_GENERAL($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Address Settings');

		$name =	new DAttr('name', 'name', 'Listener Name', 'text', false);
		$name->_helpKey = 'listenerName';
		$addr = new DAttr('address', 'cust', 'Address', NULL, false);
		$addr->_FDE = 'YNN';
		$ip = new DAttr('ip', 'sel', 'IP Address', 'select', false, 0, $this->_options['ip']);
		$ip->_FDE = 'NYY';
		$ip->_helpKey = 'listenerIP';
		$port = new DAttr('port', 'uint', 'Port', 'text', false, 0, 65535);
		$port->_FDE = 'NYY';
		$port->_helpKey = 'listenerPort';

		$processes = $_SERVER['LSWS_CHILDREN'];
		if ( !$processes )
			$processes = 1;
		for( $i = 1; $i <= $processes; ++$i )
			$bindoptions[1<<($i-1)] = "Process $i";

		$binding = new DAttr('binding', 'checkboxOr', 'Binding', 'checkboxgroup', true, NULL, $bindoptions);
		$binding->_version = 2; //2: LSWS 2CPU +
		$binding->_helpKey = 'listenerBinding';

		$attrs = array(
			$name,
			$addr, $ip,	$port, $binding,
			new DAttr('secure', 'bool', 'Secure', 'radio', false, NULL, NULL, NULL, 0, 'listenerSecure'),
			$this->_attrs['note'],
			);
		$this->_tblDef[$id]->setAttr($attrs, NULL, NULL, 'listenerGeneral');
		$this->_tblDef[$id]->_defaultExtract = array('address'=>'$$');
	}

	private function add_L_GENERAL1($id)
	{
		$this->_tblDef[$id] = $this->DupTblDef('L_GENERAL', $id);
		$this->_tblDef[$id]->setDataLoc('listeners');
		$this->_tblDef[$id]->setRepeated('name');
	}

	private function add_L_VHMAP_TOP($id)
	{
		$align = array('left', 'left', 'center');
		$this->_tblDef[$id] = new DTbl($id, 'Virtual Host Mappings', 1, 'L_VHMAP', $align, "web_link", FALSE);

		$attrs = array(
			$this->_attrs['l_vhost'],
			$this->_attrs['l_domain'],
			new DAttr('action', 'action', 'Action', NULL, false, 'L_VHMAP', 'Ed'));

		$this->_tblDef[$id]->setAttr($attrs, 'vhMap', 'vhostMapList:vhostMap', 'virtualHostMapping');
		$this->_tblDef[$id]->setRepeated('vhost');
	}

	private function add_L_VHMAP($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Virtual Host Mappings', 2);

		$attrs = array(
			$this->_attrs['l_vhost'],
			$this->_attrs['l_domain']);
		$this->_tblDef[$id]->setAttr($attrs, 'vhMap', 'vhostMapList:vhostMap', 'virtualHostMapping');
		$this->_tblDef[$id]->setRepeated('vhost');
	}

	private function add_L_SSL_CERT($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'SSL Private Key & Certificate');

		$attrs = array(
			new DAttr('keyFile', 'cust', 'Private Key File', 'text', true, NULL, '', $this->_options['text_size']),
			new DAttr('certFile', 'cust', 'Certificate File', 'text', true, NULL, '', $this->_options['text_size']),
			new DAttr('certChain', 'bool', 'Chained Certificate', 'radio'),
			new DAttr('CACertPath', 'cust', 'CA Certificate Path', 'text', true, NULL, '', $this->_options['text_size']),
			new DAttr('CACertFile', 'cust', 'CA Certificate File', 'text', true, NULL, '', $this->_options['text_size']),
		    );
		$this->_tblDef[$id]->setAttr($attrs, NULL, NULL, 'sslCert');
	}

	private function add_VH_SSL_CERT($id)
	{
		$this->_tblDef[$id] = $this->DupTblDef('L_SSL_CERT', $id,  'SSL Private Key & Certificate for Virtual Host');
		$this->_tblDef[$id]->setDataLoc('vhssl', 'vhssl');
	}

	private function add_L_SSL($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'SSL Protocol');
		$sslversion = array('1'=>'SSL v3.0', '2'=>'TLS v1.0', '4'=>'TLS v1.1', '8'=>'TLS v1.2');

		$attrs = array(
			new DAttr('sslProtocol', 'checkboxOr', 'Protocol Version', 'checkboxgroup', TRUE, NULL, $sslversion),
			new DAttr('ciphers', 'cust', 'Ciphers', 'text', true, NULL, '', $this->_options['text_size']),
			new DAttr('enableECDHE', 'bool', 'Enable ECDH Key Exchange', 'radio', true),
			new DAttr('enableDHE', 'bool', 'Enable DH Key Exchange', 'radio', true),
			new DAttr('DHParam', 'cust', 'DH Parameter', 'text', true, NULL, '', $this->_options['text_size']),
		);

		$this->_tblDef[$id]->setAttr($attrs);
	}

	private function add_VH_SSL_SSL($id)
	{
		$this->_tblDef[$id] = $this->DupTblDef('L_SSL', $id, 'Virtual Host SSL Protocol');
		$this->_tblDef[$id]->setDataLoc('vhssl', 'vhssl');
	}

	private function add_L_SSL_FEATURE($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Security & Features');

		$attrs = array(
			$this->_attrs['ssl_renegProtection'],
            new DAttr('enableSpdy', 'checkboxOr', 'Enable SPDY/HTTP2', 'checkboxgroup', true, NULL, array('1'=>'SPDY/2', '2'=>'SPDY/3', '4'=>'HTTP/2', '0'=>'None'))
		    );
		$this->_tblDef[$id]->setAttr($attrs);
	}

	private function add_VH_SSL_FEATURE($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Security');

		$attrs = array($this->_attrs['ssl_renegProtection']);

		$this->_tblDef[$id]->setAttr($attrs, 'vhssl', 'vhssl');
	}

	private function add_L_SSL_OCSP($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'OCSP Stapling');

		$attrs = array(
		    new DAttr('enableStapling', 'bool', 'Enable OCSP Stapling', 'radio', true),
		    new DAttr('ocspRespMaxAge', 'uint', 'OCSP Response Max Age (secs)', 'text', true, -1, NULL),
		    new DAttr('ocspResponder', 'httpurl', 'OCSP Responder', 'text', true, NULL, NULL, $this->_options['text_size']),
		    new DAttr('ocspCACerts', 'cust', 'OCSP CA Certificates', 'text', true, NULL, '', $this->_options['text_size']),
		    );
		$this->_tblDef[$id]->setAttr($attrs);
	}

	private function add_VH_SSL_OCSP($id)
	{
		$this->_tblDef[$id] = $this->DupTblDef('L_SSL_OCSP', $id);
		$this->_tblDef[$id]->setDataLoc('vhssl', 'vhssl');
	}

	private function add_L_SSL_CLIENT_VERIFY($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Client Verification');
		$clientVerify =	new DAttr('clientVerify', 'sel', 'Client Verification', 'select', true, NULL,
			array('0'=>'none','1'=>'optional','2'=>'require','3'=>'optional_no_ca' ));
		$verifyDepth = new DAttr('verifyDepth', 'uint', 'Verify Depth', 'text', true, 0, 100);
		$crlPath = new DAttr('crlPath', 'cust', 'Client Revocation Path', 'text', true, NULL, '', $this->_options['text_size']);
		$crlFile = new DAttr('crlFile', 'cust', 'Client Revocation File', 'text', true, NULL, '', $this->_options['text_size']);
		$clientVerify->_version = 1;
		$verifyDepth->_version = 1;
		$crlPath->_version = 1;
		$crlFile->_version = 1;

		$attrs = array($clientVerify, $verifyDepth, $crlPath, $crlFile);
		$this->_tblDef[$id]->setAttr($attrs);
	}

	private function add_VH_SSL_CLIENT_VERIFY($id)
	{
		$this->_tblDef[$id] = $this->DupTblDef('L_SSL_CLIENT_VERIFY', $id);
		$this->_tblDef[$id]->setDataLoc('vhssl', 'vhssl');
	}

	private function add_ADMIN_PHP($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'General');

		$attrs = array(
			new DAttr('enableCoreDump', 'bool', 'Enable Core Dump', 'radio', false ),
			new DAttr('sessionTimeout', 'uint', 'Session Timeout (secs)', 'text', true, 60, NULL, NULL, 0, 'consoleSessionTimeout')
			);
		$this->_tblDef[$id]->setAttr($attrs, 'general');
	}

	private function add_ADMIN_USR_TOP($id)
	{
		$align = array('left', 'center');
		$this->_tblDef[$id] = new DTbl($id, 'Web Admin Users', 1, 'ADMIN_USR_NEW', $align, "administrator", FALSE);

		$attrs = array(
			new DAttr('name', 'cust', 'Admin User Name'),
			new DAttr('action', 'action', 'Action', NULL, false, 'ADMIN_USR', 'Ed')
			);
		$this->_tblDef[$id]->setAttr($attrs, 'ADM');
		$this->_tblDef[$id]->setRepeated('name');
	}

	private function add_ADMIN_USR($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Web Admin User', 2);

		$attrs = array(
			new DAttr('name', 'name', 'Web Admin User Name', 'text', false, NULL, NULL, $this->_options['text_size']),
			new DAttr('oldpass', 'cust', 'Old Password', 'password', false, NULL, NULL, $this->_options['text_size'], 0, 'adminOldPass'),
			new DAttr('pass', 'cust', 'New Password', 'password', false, NULL, NULL, $this->_options['text_size']),
			new DAttr('pass1', 'cust', 'Retype Password', 'password', false, NULL, NULL, $this->_options['text_size'])
			);
		$this->_tblDef[$id]->setAttr($attrs, 'ADM', NULL, 'adminUser');
		$this->_tblDef[$id]->setRepeated('name');
		$this->_tblDef[$id]->_defaultExtract = array('passwd'=>'$$');
	}

	private function add_ADMIN_USR_NEW($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'New Web Admin User', 2);

		$attrs = array(
			new DAttr('name', 'name', 'Web Admin User Name', 'text', false, NULL, NULL, $this->_options['text_size']),
			new DAttr('pass', 'cust', 'New Password', 'password', false, NULL, NULL, $this->_options['text_size']),
			new DAttr('pass1', 'cust', 'Retype Password', 'password', false, NULL, NULL, $this->_options['text_size'])
			);
		$this->_tblDef[$id]->setAttr($attrs, 'ADM');
		$this->_tblDef[$id]->setRepeated('name');
		$this->_tblDef[$id]->_defaultExtract = array('passwd'=>'$$');
	}

	private function add_ADMIN_L_TOP($id)
	{
		$align = array('center', 'center', 'center', 'center', 'center');
		$this->_tblDef[$id] = new DTbl($id, 'Listener List', 1, 'ADMIN_L_GENERAL1', $align, "link");

		$attrs = array(
			new DAttr('name', 'cust', 'Listener Name'),
			new DAttr('ip', 'cust', 'IP Address'),
			new DAttr('port', 'cust', 'Port'),
			new DAttr('secure', 'bool', 'Secure'),
			new DAttr('action', 'action', 'Action', NULL, false, 'ADMIN_L_GENERAL1', 'Xd'),

			);
		$this->_tblDef[$id]->setAttr($attrs, 'listeners');
		$this->_tblDef[$id]->setRepeated('name');
	}

	private function add_ADMIN_L_GENERAL($id)
	{
		$this->_tblDef[$id] = $this->DupTblDef('L_GENERAL', $id, 'Admin Listener Address Settings');
		unset($this->_tblDef[$id]->_dattrs[4]); // remove binding
	}

	private function add_ADMIN_L_GENERAL1($id)
	{
		$this->_tblDef[$id] = $this->DupTblDef('ADMIN_L_GENERAL', $id);
		$this->_tblDef[$id]->setDataLoc('listeners');
		$this->_tblDef[$id]->setRepeated('name');
	}

	private function add_SERVICE_SUSPENDVH($id)
	{
		$this->_tblDef[$id] = new DTbl($id, 'Suspend Vhosts');

		$attr0 = new DAttr('suspendedVhosts', 'vhname', NULL);
		$attr0->multiInd = 1;
		$attr0->_FDE = 'YNN';
		$attrs = array( $attr0);
		$this->_tblDef[$id]->setAttr($attrs, 'service');
	}
}
