<?php 
abstract class Hj_Modulefactory_Model_File_Class extends Mage_Core_Model_Abstract implements Hj_Modulefactory_Model_Generable {
	protected $_methods=array();
	public function __construct(){
		//parent::__construct();
		//$this->setExt('php');
	}
	public function setParent($parent, $override=false){
		$this->setExtends($parent);
		$this->setOverride($override);
	}
	
	protected function setExtends($extends){
		parent::setExtends($extends);
	}
	
	public function getBlock($classname, $extends=null){
		$block=new Hj_Modulefactory_Block_Template_Class($classname, $extends);
		$block->setTemplate('modulefactory/files/class.phtml');
		return $block;
	}
	
	public function addMethod($name, array $args=array(), $type='public', $abstract=false, $content=null, $comments=null){
		if(!in_array($type, array('public','private','protected'))){
			Mage::throwException('Adding method failed. "'.$type.'" should be "public", "private" or "protected".');
		}
		$this->_methods[]=array('name'=>$name, 'args'=>$args, 'type'=>$type, 'abstract'=>$abstract);
	}
	
	public function getMethods($asArray=false){
		if($asArray){
			return $this->_methods;
		}
		$return=array();
		foreach ($this->_methods as $key=>$method) {
			$return[$key]='';
			if(!empty($method['comments'])){
				$return[$key].='/**'."\n".$method['comments']."\n".'**/';
			}
			if($method['abstract']){
				$return[$key].='abstract ';
			}
			$return[$key].=$method['type'].' function '.$method['name'].'(';
			if(count($method['args'])){
				$return[$key].='$'.implode(', $', $method['args']);
			}
			$return[$key].='){'."\n";
		}
		return $return;
	}
}
?>