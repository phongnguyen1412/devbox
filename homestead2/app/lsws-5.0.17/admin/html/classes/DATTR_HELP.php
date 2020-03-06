<?php

class DATTR_HELP
{
	private $db;
    private $edb;

	private function __construct()
	{
		$this->init();
	}

	public static function GetInstance()
	{
        if ( !isset($GLOBALS['_DATTR_HELP_']) )
			$GLOBALS['_DATTR_HELP_'] = new DATTR_HELP();
		return $GLOBALS['_DATTR_HELP_'];
	}

	public function GetItem($label)
	{
		if (isset($this->db[$label]))
			return $this->db[$label];
		else
			return NULL;
	}

    public function GetEditTip($labels)
    {
        $tips = array();
        foreach ($labels as $label) {
            if (isset($this->edb[$label]))
                $tips = array_merge($tips, $this->edb[$label]);
        }
		return $tips;
    }

	private function init() {
		// add other customized items here

		include 'DATTR_HELP_inc.php';
	}

}
