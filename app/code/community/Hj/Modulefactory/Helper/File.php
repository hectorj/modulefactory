<?php 
class Hj_Modulefactory_Helper_File extends Mage_Core_Helper_Abstract {
	
	protected $overrideFiles = true;//@TODO : make a configure parameter for this var
	public static $createdDirs=array();
	
	/**
	 * 
	 * @return bool
	 */
	public function getOverridingFiles(){
		return Mage::app()->getRequest()->getParam('override_files', null)!==null;
	}
	
	public function getMakeArchive(){
		return Mage::app()->getRequest()->getParam('archive_module', 0);
	}
	
	public function rrmdir($dir) {
		if(is_dir($dir)){
			foreach (glob($dir . '/*') as $file) {
				if (is_dir($file))
					$this->rrmdir($file);
				else
					unlink($file);
			}
			rmdir($dir);
		}
	}
	public function getTmpPath(){
		return '.'.DS.'tmp'.DS.'Modulefactory'.DS;
	}
	public function createWDir($path){
		if($this->getMakeArchive()){
			$tmp_path=$this->getTmpPath().$path;
			if(!is_dir($tmp_path)) {
				@mkdir($tmp_path, 0777, true) OR Mage::throwException('Unable to create tmp directory ('.$tmp_path.')');
			}
			if(!is_writable($tmp_path)){
				@chmod($tmp_path, 0777) OR Mage::throwException('Tmp directory is not writable. Please use chmod on "'.$tmp_path.'"');
			}
		} else {
			if(!is_dir($path)) {
				@mkdir($path, 0777, true) OR Mage::throwException('Unable to create directory ('.$path.')');
			}
			if(!is_writable($path)){
				@chmod($path, 0777) OR Mage::throwException('Directory is not writable. Please use chmod on "'.$path.'"');
			}
		}
		self::$createdDirs[]=$path;
	}
	
	public static $createdFiles=array();
	public function createFile($completePath){
		if($this->getMakeArchive()){
			$tmp_path=$this->getTmpPath().$completePath;
			$handle=fopen($tmp_path, 'w');
		} else {
			if($this->overrideFiles){
				$handle=fopen($completePath, 'w');
			} else {
				$handle=fopen($completePath, 'x');
			}
		}
		if(!$handle){
			Mage::throwException('File "'.$completePath.'" can not be created or exists already');
		}
		self::$createdFiles[]=$completePath;
		return $handle;
	}
	
	public function extractPathNameExt($_string){
		$result = $this->extractCompleteNameFromCompletePath($_string);
		$result2 = $this->extractExtFromCompleteName($result['complete_name']);
		return array('path'=>$result['path'], 'name'=>$result2['name'], 'ext'=>$result2['ext']);
	}
	
	public function extractCompleteNameFromCompletePath($_path){
		$pos2=strpos($_path, '/');
		while($pos2!==false){
			$pos=$pos2;
			$pos2=strpos($_path, '/', $pos+1);
		}
		if(!isset($pos)){
			return array('complete_name'=>$_path, 'path'=>null);
		} else {
			return array('path'=>substr($_path, 0, $pos), 'complete_name'=>substr($_path, $pos+1));
		}
	
	}
	
	public function extractExtFromCompleteName($_name){
		$pos2=strpos($_name, '.');
		while($pos2!==false){
			$pos=$pos2;
			$pos2=strpos($_name, '.', $pos+1);
		}
		if(!isset($pos)){
			return array('name'=>$_name, 'ext'=>null);
		} else {
			return array('name'=>substr($_name, 0, $pos), 'ext'=>substr($_name, $pos+1));
		}
		
	}
}
?>