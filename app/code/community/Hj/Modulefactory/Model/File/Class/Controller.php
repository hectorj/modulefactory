<?php 
class Hj_Modulefactory_Model_File_Class_Controller extends Hj_Modulefactory_Model_File_Class{
	public function __construct(Hj_Modulefactory_Model_Router $router, $url, $extends=null, $overrideUrl=null){
		parent::__construct();
		$this->setRouter($router);
		$this->getModule()->registerItem($router->getType().'Controller', $url, $this);
		$this->setUrl($url);
		$extends=str_replace('_controllers_', '_', $extends);
		$this->setParent($extends, $overrideUrl);
	}
	
	public function getModule(){
		if($this->getRouter()){
			return $this->getRouter()->getModule();
		} else {
			return null;
		}
	}
	
	public function getType(){
		if($this->getRouter()){
			return $this->getRouter()->getType();
		} else {
			return null;
		}
	}
	
	public function setUrl($url){
		$url=trim($url, '/');
		parent::setCompleteUrl('/'.$this->getRouter()->getPath().'/'.$url.'/');
		parent::setUrl($url);
	}
	
	public function setComponentName($string){
		parent::setComponentName(uc_words($string));
	}
	
	public function getPath(){
		$path = $this->getUrl();
		$pos2=strpos($path, '_');
		while($pos2!==false){
			$pos=$pos2;
			$pos2=strpos($path, '_', $pos+1);
		}
		if(isset($pos)){
			$path=substr($path, 0, $pos);
		} else {
			$path='';
		}
		$path=uc_words($path);
		$path=str_replace('_', DS, $path);
		$path='app'.DS.'code'.DS.$this->getModule()->getChannel().DS.$this->getModule()->getPackage().DS.$this->getModule()->getModuleName().DS.'controllers'.DS.$path;
		return $path;
	}
	
	public function getFileName(){
		$filename=$this->getUrl();
		$pos2=strpos($filename, '_');
		while($pos2!==false){
			$pos=$pos2;
			$pos2=strpos($filename, '_', $pos+1);
		}
		if(isset($pos)){
			$filename=substr($filename, $pos+1);
		}
		$filename.='Controller.php';
		return ucfirst($filename);
	}
	
	public function getClassName(){
		return $this->getModule()->getPackage().'_'.$this->getModule()->getModuleName().'_'.uc_words($this->getUrl()).'Controller';
	}
	
	public function generate(Varien_Simplexml_Element $config){
		
		Mage::helper('modulefactory/file')->createWDir($this->getPath());
		$file=Mage::helper('modulefactory/file')->createFile($this->getPath().DS.$this->getFileName());
		
		$extends=$this->getExtends();
		$block=$this->getBlock($this->getClassName(), $extends);
		if($extends && $extends!='Mage_Core_Controller_Front_Action' && $extends!='Mage_Adminhtml_Controller_Action'){
			$extends_part=explode('_', $extends);
			$require_dir=Mage::getModuleDir('controllers', $extends_part[0].'_'.$extends_part[1]);
			$require_dir=substr($require_dir, strpos($require_dir, 'app'));
			$extends_part=array_splice($extends_part, 2);
			$require_path=$require_dir.DS.implode(DS, $extends_part).'.php';
			$block->setRequires(array($require_path));
		}
		fwrite($file, $block->renderView());
		fclose($file);
		
		if($this->getOverride()){
			if(!$config->global){
				$config->addChild('global');
			}
			if(!$config->global->rewrite){
				$config->global->addChild('rewrite');
			}
			$classname=strtolower($this->getClassName());
			$config->global->rewrite->addChild($classname);
			$config->global->rewrite->$classname->addChild('from', '#'.$this->getOverride().'#');
			$config->global->rewrite->$classname->addChild('to', $this->getCompleteUrl());
		}
	}
}
?>