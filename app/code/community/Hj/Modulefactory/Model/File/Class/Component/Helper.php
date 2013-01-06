<?php 
class Hj_Modulefactory_Model_File_Class_Component_Helper extends Hj_Modulefactory_Model_File_Class_Component{
	public function __construct(Hj_Modulefactory_Model_Module $module, $componentName, $extends=null, $override=false){
		$this->setComponentType('Helper');
		parent::__construct($module, $componentName, $extends, $override);
	}
}
?>