<?php 
class Hj_Modulefactory_Model_File_Class_Component_Block extends Hj_Modulefactory_Model_File_Class_Component{
	public function __construct(Hj_Modulefactory_Model_Module $module, $componentName, $extends=null, $override=false){
		$this->setComponentType('Block');
		parent::__construct($module, $componentName, $extends, $override);
	}
}
?>