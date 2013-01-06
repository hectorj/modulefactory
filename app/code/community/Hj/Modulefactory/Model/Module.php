<?php 
class Hj_Modulefactory_Model_Module extends Mage_Core_Model_Abstract {
	protected $registry;
	
	public function __construct($package, $moduleName, $version='1.0.0', $channel='local'){
		parent::__construct();
		$this->setChannel($channel);
		$this->setVersion($version);
		$this->setModuleName($moduleName);
		$this->setPackage($package);
		Mage::helper('modulefactory/file')->rrmdir(Mage::helper('modulefactory/file')->getTmpPath());
	}
	
	public function getEtcPath(){
		return 'app'.DS.'code'.DS.$this->getChannel().DS.$this->getPackage().DS.$this->getModuleName().DS.'etc'.DS;
	}
	
	public function generate(){
		$config = new Varien_Simplexml_Element('<config></config>');
		$module_ref=uc_words($this->getPackage().'_'.$this->getModuleName());
		$config->addChild('modules')->addChild($module_ref)->addChild('version', ($this->getVersion()));
		
		$declare = new Varien_Simplexml_Element('<config></config>');
		$declare_module=$declare->addChild('modules')->addChild($module_ref);
		$declare_module->addChild('active', 'true');
		$declare_module->addChild('codePool', 'local');
		
		if(is_array($this->registry) && count($this->registry)){
			foreach ($this->registry as $type=>$typeRegistry) {
				foreach ($typeRegistry as $id=>$item) {
					if($item instanceof Hj_Modulefactory_Model_Generable){
						$item->generate($config);
					} else {
						//@TODO : maybe report some error... or log something
					}
				}
			}
		}
		$etc_path=$this->getEtcPath();
		$declare_path='app'.DS.'etc'.DS.'modules'.DS;
		
		Mage::helper('modulefactory/file')->createWDir($etc_path);
		$config_file=Mage::helper('modulefactory/file')->createFile($etc_path.'config.xml');
		fwrite($config_file, $config->asNiceXml());
		fclose($config_file);
		
		Mage::helper('modulefactory/file')->createWDir($declare_path);
		$declare_file=Mage::helper('modulefactory/file')->createFile($declare_path.$this->getPackage().'_'.$this->getModuleName().'.xml');
		fwrite($declare_file, $declare->asNiceXml());
		fclose($declare_file);
	}
	
	public function registerItem($type, $id, $item){
		if(isset($this->registry[$type][$id])){
			Mage::throwException($type.' '.$id.' exists already');
		}
		$this->registry[$type][$id]=$item;
	}
	
}
?>