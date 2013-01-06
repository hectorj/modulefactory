<?php

/**
 * Description of Upgrade
 *
 * @author Hectorj
 */
class Hj_Modulefactory_Model_File_Sql_Upgrade extends Hj_Modulefactory_Model_File_Sql {
	public function __construct(Hj_Modulefactory_Model_Module $module, $version, $from_version) {
		parent::__construct($module, $version);
		$this->setFromVersion($from_version);
		$this->registerItem();
	}
	
	public function getFileName() {
		return 'mysql4-upgrade-'.$this->getFromVersion().'-'.$this->getVersion().'.php';
	}
	
	public function registerItem() {
		$this->getModule()->registerItem('sqlUpgrade', $this->getFromVersion().'-'.$this->getVersion(), $this);
	}
}

?>
