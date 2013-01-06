<?php

/**
 * Description of Install
 *
 * @author Hectorj
 */
class Hj_Modulefactory_Model_File_Sql_Install extends Hj_Modulefactory_Model_File_Sql {
	public function __construct(Hj_Modulefactory_Model_Module $module, $version) {
		parent::__construct($module, $version);
		$this->registerItem();
	}
	public function getFileName() {
		return 'mysql4-install-'.$this->getVersion().'.php';
	}
	public function registerItem() {
		$this->getModule()->registerItem('sqlInstall', $this->getVersion(), $this);
	}
}

?>
