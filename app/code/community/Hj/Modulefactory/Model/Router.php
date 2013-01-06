<?php 
class Hj_Modulefactory_Model_Router extends Mage_Core_Model_Abstract implements Hj_Modulefactory_Model_Generable {
	public function __construct(Hj_Modulefactory_Model_Module $module, $path, $type){
		$this->setModule($module);
		$this->setType($type);
		$this->setPath($path);
		
		$module->registerItem('router', $type, $this);
	}
	
	public function setType($type){
		if($type!='admin' && $type!='frontend'){
			Mage::throwException('Unkwon router type : "'.$type.'"');
		}
		parent::setType($type);
	}

	public function generate(\Varien_Simplexml_Element $config) {
		$type=$this->getType();
		if(!$config->$type){
			$config->addChild($type);
		}
		$config->$type->addChild('routers');
		
		$module_short_ref=strtolower($this->getModule()->getModuleName());
		$module_ref=$this->getModule()->getPackage().'_'.$this->getModule()->getModuleName();
		
		$config->$type->routers->addChild($module_short_ref);
		
		$config->$type->routers->$module_short_ref->addChild('use', $type=='admin' ? 'admin' : 'standard');
		$config->$type->routers->$module_short_ref->addChild('args');
		$config->$type->routers->$module_short_ref->args->addChild('module', $module_ref);
		$config->$type->routers->$module_short_ref->args->addChild('frontName', $this->getPath());
	}
	
}
?>