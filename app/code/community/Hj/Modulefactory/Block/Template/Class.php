<?php
class Hj_Modulefactory_Block_Template_Class extends Hj_Modulefactory_Block_Template {
	public function __construct($className, $extends=null){
		parent::__construct();
		$this->setClassName($className);
		$this->setExtends($extends);
	}
}
?>