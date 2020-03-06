<?php

class GUI extends GUIBase
{

	public static function top_menu()
	{
		$dropdown = '
  <ul>
    <li><a href="/" class="mainlevel">Home</a></li>
    <li><a href="/service/serviceMgr.php" class="mainlevel">Actions</a>
      <ul>
	 	<li><a href="javascript:go(\'restart\',\'\');">Graceful Restart</a></li>
		<li><a href="javascript:toggle();">Toggle Debug Logging</a></li>
		<li><a href="/service/serviceMgr.php?vl=1">Server Log Viewer</a></li>
		<li><a href="/service/serviceMgr.php?vl=2">Real-Time Stats</a></li>
		<li><a href="/service/serviceMgr.php?vl=3">Version Manager</a></li>
		<li><a href="/utility/build_php/buildPHP.php">Compile PHP</a></li>
      </ul>
    </li>
    <li><a href="/config/confMgr.php?m=serv" class="mainlevel">Configuration</a>
      <ul>
      	<li><a href="/config/confMgr.php?m=serv">Server</a></li>
        <li><a href="/config/confMgr.php?m=sltop">Listeners</a></li>
        <li><a href="/config/confMgr.php?m=vhtop">Virtual Hosts</a></li>
        <li><a href="/config/confMgr.php?m=tptop">Virtual Host Templates</a></li>
      </ul>
    </li>
    <li><a href="/config/confMgr.php?m=admin" class="mainlevel">Web Console</a>
      <ul>
        <li><a href="/config/confMgr.php?m=admin">General</a></li>
        <li><a href="/config/confMgr.php?m=altop">Listeners</a></li>
      </ul>
    </li>
    <li><a href="/docs/" class="mainlevel" target="_blank">Help</a></li>
  </ul>';

		$ver_link = '/service/serviceMgr.php?vl=3';
		$buf = parent::gen_top_menu($dropdown, $ver_link);
		return $buf;
	}

	public static function footer()
	{
		$year = '2002-2015';
		$buf = parent::gen_footer($year, '');
		return $buf;
	}

	public static function left_menu()
	{
		$confCenter = ConfCenter::singleton();

		$conftype = $confCenter->_confType;

		if($conftype == "admin") {

			$listeners = count($confCenter->_serv->_data['listeners']);

			$tabs = array('Admin' => 'admin', "Listeners ($listeners)" => 'altop');
		}
		else {

			$vhosts = count($confCenter->_info['VHS']);
			$listeners = count($confCenter->_info['LNS']);
			$templates = isset($confCenter->_serv->_data['tpTop']) ? count($confCenter->_serv->_data['tpTop']) : 0;

			$tabs = array('Server' => 'serv', "Listeners ($listeners)" => 'sltop',
					"Virtual Hosts ($vhosts)" => 'vhtop', "Virtual Host Templates ($templates)" => 'tptop');
		}

		return parent::gen_left_menu($tabs);
	}


}

