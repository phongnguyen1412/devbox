<?php

class DAttr extends DAttrBase
{
	public $_version = 0; // 0: no restriction; 1: LSWS ENTERPRISE; 2: LSWS 2CPU +;
	public $_feature = 0; // feature bit

	public function blockedVersion()
	{
		if ($this->_feature == 0 && $this->_version == 0)
			return FALSE;	// no restriction

		if ($this->_feature != 0) {
			$features = $_SERVER['LS_FEATURES'];
			if ( ($this->_feature & $features) == $this->_feature)
				return FALSE;  // feature enabled
			elseif ($this->_version == 0)
				return TRUE;
		}

		if ($this->_version == 1) {
			// LSWS ENTERPRISE;
			$edition = strtoupper($_SERVER['LSWS_EDITION']);
			return ( strpos($edition, "ENTERPRISE" ) === FALSE );
		}
		elseif ($this->_version == 2) {
			// LSWS 2CPU +
			$processes = $_SERVER['LSWS_CHILDREN'];
			if ( !$processes) {
				$processes = 1;
			}
			return ($processes < 2);
		}
		else
			return TRUE; // not supported
	}

	public function dup($key, $label, $helpkey)
	{
		$d = parent::dup($key, $label, $helpkey);
		$d->_version = $this->_version;
		$d->_feature = $this->_feature;
		return $d;
	}

	public function bypassSavePost()
	{
		return ($this->_FDE[2] == 'N' || $this->blockedVersion());
	}
}
