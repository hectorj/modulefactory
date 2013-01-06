<?php
/**
 * 
 * @author HectorJ
 *
 */
class Hj_Modulefactory_Adminhtml_ModulefactoryController extends Mage_Adminhtml_Controller_Action
{
	const SELF_PATH='app/code/local/Hj/Modulefactory/';
	const TEMPLATE_PATH='app/code/local/Hj/Modulefactory/etc/template/';
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('modulefactory');
		$this->getLayout()->getBlock('head')->setTitle('Module Factory for Magento CE');
		return $this;
	}
 
	protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('modulefactory');
    }
 
	public function indexAction() {
		$map=unserialize(@file_get_contents('var/moduleMap'));
		if(!$map){
			$this->_redirect('*/*/map');
		} else {
			$this->_initAction();
			/**
			 * 
			 * @var Mage_Adminhtml_Block_Widget_Form
			 */
			$formBlock=$this->getLayout()->createBlock('adminhtml/widget_form');
			$form = new Varien_Data_Form();
			$form->setAction($this->getUrl('*/*/create'));
			$form->setMethod('post');
			$form->setUseContainer(true);
			$form->setId('module_create_form');
			$formBlock->setForm($form);

			$formBlock->setTemplate('modulefactory/modulefactory.phtml');

			$formBlock->setChild('create_button',
					$this->getLayout()->createBlock('adminhtml/widget_button')
						->setData(array(
							'label'     => Mage::helper('catalog')->__('Create'),
							'onclick'   => 'moduleFactory.submit();',
							'class' => 'save'
						))
				);
			
			$head=$this->getLayout()->getBlock('head');
			/** @var $head Mage_Adminhtml_Block_Page_Head */
			$head->addJs('jquery-1.7.2.min.js');
			$head->addJs('jquery-ui-1.8.23.custom.min.js');
			$head->addJs('jstree/jquery.jstree.js');
			$head->addJs('moduleFactory.js');
			$head->addCss('modulefactory.css');
			$head->addCss('jquery-ui-1.8.23.custom.css');
			
			$this->_addContent($formBlock);
			
			$tabsBlock=$this->getLayout()->createBlock('modulefactory/tabs');
			
			$this->_addLeft($tabsBlock);
			$this->renderLayout();
		}
	}
	
	public function saveAction(){
		$data = $this->getRequest()->getPost();
		Mage::getSingleton('adminhtml/session')->setFormData($data);
		$this->getResponse()->setHeader('Content-Type', 'application/octet-stream', true);
		$this->getResponse()->setHeader('Content-Disposition', 'attachment ;filename="'.$data['name'].'-'.$data['version'].'.mfproject"', true);
		$this->getResponse()->sendHeaders();
		echo serialize($data);
		$this->getResponse()->sendResponse();
	}
	
	public function loadAction(){
		try{
			if(!isset($_FILES['parameters'])){
				Mage::throwException('No file sent.');
			}
			$data = unserialize(file_get_contents($_FILES['parameters']['tmp_name']));
			Mage::getSingleton('adminhtml/session')->setFormData($data);
			Mage::getSingleton('adminhtml/session')->addSuccess('Params loaded.');
		} catch(Exception $e){
			Mage::getSingleton('adminhtml/session')->addError('Can not load params : '.$e->getMessage());
		}
	}
	protected $_postData;
	public function getzipAction(){
		$data=$this->_postData;
		
		$this->getResponse()->setRedirect(null, 200);
		$this->getResponse()->setHeader('Content-Type', 'application/zip', true);
		$this->getResponse()->setHeader('Content-Disposition', 'attachment ;filename="'.$data['package'].'_'.$data['name'].'-'.$data['version'].'.zip"', true);
		$this->getResponse()->sendHeaders();
		$zip = new ZipArchive;
		$zip_name=Mage::helper('modulefactory/file')->getTmpPath().$data['package'].$data['name'].'-'.$data['version'].'.zip';
		$zip->open($zip_name, ZipArchive::CREATE);
		foreach(Hj_Modulefactory_Helper_File::$createdFiles as $path){
			$zip->addFile(Mage::helper('modulefactory/file')->getTmpPath().$path, $path);
		}
		$zip->close();
		echo file_get_contents($zip_name);
		Mage::helper('modulefactory/file')->rrmdir(Mage::helper('modulefactory/file')->getTmpPath());
		$this->getResponse()->sendResponse();
	}
	
	public function createAction(){
		$this->_initAction();
		try{
			$required_params=array('package'=>1, 'name'=>1, 'version0'=>1, 'version1'=>1, 'version2'=>1);
			$data = $this->getRequest()->getPost();
			
			Mage::getSingleton('adminhtml/session')->setFormData($data);
			$this->_redirect('*/*/index');
			
			if(is_array($data) && count($data)){//check for data existing
				
				$missing_data=array_diff_key($required_params, $data);// check for missing fields
				if(count($missing_data)){
					Mage::throwException('Missing field(s) : '.implode(', ', $missing_data));
				} else {
					function recursive_trim($data){
						if(is_array($data)){
							return array_map('recursive_trim', $data);
						} else {
							return trim($data);
						}
					}
					if(empty($data['name']) || empty($data['package'])){
						Mage::throwException('Module and/or package name is empty.');
					}
					$data=array_map('recursive_trim', $data);//remove useless whitespace
					Mage::getSingleton('adminhtml/session')->setFormData($data);
					$data['version']=  intval($data['version0']).'.'.intval($data['version1']).'.'.intval($data['version2']);
					$this->_postData=$data;
					/* 			New version code		 */
					//Zend_Debug::dump($data);exit();
					$module=new Hj_Modulefactory_Model_Module($data['package'], $data['name'], $data['version']);//Mage::getModel('modulefactory/module', array());
					foreach(array('block', 'helper', 'model') as $type){
						if(isset($data[$type]) && count($data[$type])){
							//creating dummy $type(s)
							$_class='Hj_Modulefactory_Model_File_Class_Component_'.ucfirst($type);
							foreach ($data[$type] as $key=>$componentName) {
								$extends=NULL;
								$override=false;
								if(isset($data[$type.'Extends'][$key]) && !empty($data[$type.'Extends'][$key])){
									$extends=$data[$type.'Extends'][$key];
								}
								if(isset($data[$type.'Override'][$key]) && $data[$type.'Override'][$key]){
									$override=true;
								}
								new $_class($module, $componentName, $extends, $override);
							}
						}
					}
					$routers=array();
					foreach(array('frontend', 'admin') as $type){
						if(isset($data[$type.'Router']) && !empty($data[$type.'Router'])){
							$routers[$type]=new Hj_Modulefactory_Model_Router($module, $data[$type.'Router'], $type);
							if(isset($data[$type.'Controller']) && count($data[$type.'Controller'])){
								//creating controller(s)
								foreach ($data[$type.'Controller'] as $key=>$value) {
									new Hj_Modulefactory_Model_File_Class_Controller($routers[$type], $value, $data[$type.'ControllerExtends'][$key], $data[$type.'ControllerOverride'][$key]);;
								}
							}
						}
					}
					if(isset($data['addSqlInstallVersion0']) && isset($data['addSqlInstallVersion1']) && isset($data['addSqlInstallVersion2'])){
						new Hj_Modulefactory_Model_File_Sql_Install($module, intval($data['addSqlInstallVersion0']).'.'.intval($data['addSqlInstallVersion1']).'.'.intval($data['addSqlInstallVersion2']));
					}
					if(isset($data['SqlUpgradeFrom0']) && isset($data['SqlUpgradeTo0']) && count($data['SqlUpgradeFrom0'])){
						foreach($data['SqlUpgradeFrom0'] as $key=>$dummy){
							new Hj_Modulefactory_Model_File_Sql_Upgrade($module, intval($data['SqlUpgradeTo0'][$key]).'.'.intval($data['SqlUpgradeTo1'][$key]).'.'.intval($data['SqlUpgradeTo2'][$key]), intval($data['SqlUpgradeFrom0'][$key]).'.'.intval($data['SqlUpgradeFrom1'][$key]).'.'.intval($data['SqlUpgradeFrom2'][$key]));
						}
					}
					if(isset($data['system_config_xml_tree']) && !empty($data['system_config_xml_tree'])){
						new Hj_Modulefactory_Model_File_Systemconfig($module, $data['system_config_xml_tree']);
					}
					$module->generate();
					if(Mage::helper('modulefactory/file')->getMakeArchive()){
						$this->getzipAction();
					}
				}
			} else {
				Mage::throwException('No data found');
			}
		} catch(Exception $e){
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			return;
		}
		if(!Mage::helper('modulefactory/file')->getMakeArchive()){
			Mage::getSingleton('adminhtml/session')->addSuccess('<p>Module created without exception.</p><p style="text-decoration:underline;">Created directories :</p><ul><li>'.implode('</li><li>', array_unique(Hj_Modulefactory_Helper_File::$createdDirs)).'</li></ul><p style="text-decoration:underline;">Created files :</p><ul><li>'.implode('</li><li>', Hj_Modulefactory_Helper_File::$createdFiles).'</li></ul>');
		}
	}
	
	public function mapAction(){
		$data=array();

		$modules = (array)Mage::getConfig()->getNode('modules')->children();
		foreach($modules as $moduleName=>$moduleElement){
			$moduleInfos=(array)$moduleElement->children();
			if(isset($moduleInfos['codePool'])){
				$codePool=$moduleInfos['codePool'];
				if($codePool=='local'){
					continue;
				}
				if(!empty($codePool)){
					if(!isset($data[$codePool])){
						$data[$codePool]=array();
					}
					$data[$codePool][$moduleName]=array();
					$pathSegment=explode('_', $moduleName);
				
					$base_path='app/code/'.$codePool.'/';
					$path=$base_path.str_replace('_', '/', $moduleName);
					foreach(array('helper', 'block', 'model') as $type){
						$data[$codePool][$moduleName][$type]=null;
						if(is_dir($path)){
							$group=$type.'s';
							$groupConfig=Mage::getConfig()->getNode('global/'.$group.'/'.strtolower($pathSegment[1]));
							if(!$groupConfig || ($className=$groupConfig->getClassName())==''){
								$className=$moduleName.'_'.ucfirst($type);
							}
							$groupPath=$base_path.str_replace('_', '/', $className).'/';
							if(is_dir($groupPath)){
								$data[$codePool][$moduleName][$type]=$this->readdir_recur($groupPath);
								sort($data[$codePool][$moduleName][$type]);
							}
						}
					}
					$data[$codePool][$moduleName]['controller']=null;
					if(is_dir($path) && is_dir($path.'/controllers')){
						$data[$codePool][$moduleName]['controller']=$this->readdir_recur($path.'/controllers/');
						sort($data[$codePool][$moduleName]['controller']);
					}
					ksort($data[$codePool][$moduleName]);
					ksort($data[$codePool]);
				}
			}
		}
		ksort($data);
		$file=fopen('var/moduleMap', 'w');
		fwrite($file, serialize($data));
		fclose($file);
		Mage::getSingleton('adminhtml/session')->addSuccess('Modules map generated.');
		$this->_redirect('*/*/index');
	}
	
	protected function readdir_recur($path){
		$dir=opendir($path);
		$result=array();
		while(false !== ($entry = readdir($dir))){
			if(!empty($entry) && $entry!='.' && $entry!='..'){
				if(is_dir($path.$entry)){
					$result=array_merge($result, $this->readdir_recur($path.$entry.'/'));
				} else {
					if(substr($entry, -4)=='.php'){
						$result[]=$path.$entry;
					}
				}
			}
		}
		return $result;
	}
}
?>