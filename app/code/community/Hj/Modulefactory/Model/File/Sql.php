<?php

/**
 * Description of Sql
 *
 * @author Hectorj
 */
abstract class Hj_Modulefactory_Model_File_Sql extends Mage_Core_Model_Abstract implements Hj_Modulefactory_Model_Generable {
	protected $scriptType;
	
	public function __construct(Hj_Modulefactory_Model_Module $module, $version) {
		parent::__construct();
		$this->setModule($module);
		$this->setVersion($version);
	}
	
	abstract public function registerItem();
	
	public function getPath(){
		return 'app'.DS.'code'.DS.$this->getModule()->getChannel().DS.$this->getModule()->getPackage().DS.$this->getModule()->getModuleName().DS.'sql'.DS.lcfirst($this->getModule()->getModuleName()).'_setup'.DS;
	}
	
	abstract public function getFileName();
	
	public function getBlock(){
		$block = new Hj_Modulefactory_Block_Template_Sql();
		$block->setTemplate('modulefactory/files/sql.phtml');
		return $block;
	}
	
	public function generate(Varien_Simplexml_Element $config) {
		$config_helper=Mage::helper('modulefactory/config');
		/* @var $config_helper Hj_Modulefactory_Helper_Config */
		$config_helper->addChildIfNotExist($config, 'global');
		$resources=$config_helper->addChildIfNotExist($config->global, 'resources');
		
		$setup_name=lcfirst($this->getModule()->getModuleName()).'_setup';
		if(!$resources->$setup_name){
			$setup=$resources->addChild($setup_name);
			$setup->addChild('setup')->addChild('module', $this->getModule()->getPackage().'_'.$this->getModule()->getModuleName());
			$setup->addChild('connection')->addChild('use', 'core_setup');
		}
		
		$write_name=lcfirst($this->getModule()->getModuleName()).'_write';
		if(!$resources->$write_name)
			$resources->addChild($write_name)->addChild('connection')->addChild('use', 'core_write');
		
		$read_name=lcfirst($this->getModule()->getModuleName()).'_read';
		if(!$resources->$read_name)
			$resources->addChild($read_name)->addChild('connection')->addChild('use', 'core_read');
		
		Mage::helper('modulefactory/file')->createWDir($this->getPath());
		$file=Mage::helper('modulefactory/file')->createFile($this->getPath().$this->getFileName());
		
		$block=$this->getBlock();
		
		fwrite($file, $block->renderView());
		fclose($file);
	}
}

?>
