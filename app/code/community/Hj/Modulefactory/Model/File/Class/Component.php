<?php 
abstract class Hj_Modulefactory_Model_File_Class_Component extends Hj_Modulefactory_Model_File_Class{
	public function __construct(Hj_Modulefactory_Model_Module $module, $componentName, $extends=null, $override=false){
		parent::__construct();
		$this->setModule($module);
		$componentName=uc_words($componentName);
		$module->registerItem($this->getComponentType(), $componentName, $this);
		$this->setComponentName($componentName);
		$this->setParent($extends, $override);
		$result=Mage::helper('modulefactory/file')->extractCompleteNameFromCompletePath('app'.DS.'code'.DS.$this->getModule()->getChannel().DS.$this->getModule()->getPackage().DS.$this->getModule()->getModuleName().DS.$this->getComponentType().DS.str_replace('_', DS, $componentName).'.php');
		$this->setPath($result['path']);
		$this->setFileName($result['complete_name']);
	}
	
	public function setComponentName($string){
		parent::setComponentName(uc_words($string));
	}
	
        /**
         * Generates xml config, creates component file and fill it with a skeleton code
         * @param Varien_Simplexml_Element $config
         */
	public function generate(Varien_Simplexml_Element $config){
		if(!$config->global){
			$config->addChild('global');
		}
		$type=strtolower($this->getComponentType()).'s';
		if(!$config->global->$type){
			$config->global->addChild($type);
		}
		$module=strtolower($this->getModule()->getModuleName());
		if(!$config->global->$type->$module){
			$config->global->$type->addChild($module);
		}
		$baseClassPath=$this->getModule()->getPackage().'_'.$this->getModule()->getModuleName().'_'.$this->getComponentType();
		if(!$config->global->$type->$module->class){
			$config->global->$type->$module->addChild('class', $baseClassPath);
		}
		if($this->getOverride() && $this->getExtends()){
			$override=$this->getExtends();
			$override=explode('_', strtolower($override));
			$override_mod=$override[1];
			unset($override[0]);
			unset($override[1]);
			unset($override[2]);
			$override=implode('_', $override);
			
			if(!$config->global->$type->$override_mod){
				$config->global->$type->addChild($override_mod);
			}
			if(!$config->global->$type->$override_mod->rewrite){
				$config->global->$type->$override_mod->addChild('rewrite');
			}
			if(!$config->global->$type->$override_mod->rewrite->$override){
				$config->global->$type->$override_mod->rewrite->addChild($override, $baseClassPath.'_'.$this->getComponentName());
			}
		}
		
		Mage::helper('modulefactory/file')->createWDir($this->getPath());
		$file=Mage::helper('modulefactory/file')->createFile($this->getPath().DS.$this->getFileName());
		
		$block=$this->getBlock($baseClassPath.'_'.$this->getComponentName(), $this->getExtends());
		
		fwrite($file, $block->renderView());
		fclose($file);
	}
}
?>