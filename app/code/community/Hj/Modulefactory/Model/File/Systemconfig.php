<?php

/**
 * Systemconfig
 *
 * @author Hectorj
 */
class Hj_Modulefactory_Model_File_Systemconfig extends Mage_Core_Model_Abstract implements Hj_Modulefactory_Model_Generable {
	public function __construct(Hj_Modulefactory_Model_Module $module, $xmlData) {
		parent::__construct();
		$this->setModule($module);
		$module->registerItem('systemconfig', 0, $this);
		$this->setXmlData(new Varien_Simplexml_Element($xmlData));
	}
	public function generate(Varien_Simplexml_Element $config) {
		$config_helper=Mage::helper('modulefactory/config');
		/* @var $config_helper Hj_Modulefactory_Helper_Config */
		
		$system_config=new Varien_Simplexml_Element('<config></config>');
		$data=$this->getXmlData();
		$hasUserCreatedElements=false;
		foreach ($data->item as $tab) {
			$tab_id=$tab->getAttribute('id');
			if($tab->getAttribute('user_created')==1){
				$config_helper->addChildIfNotExist($system_config, 'tabs');
				$system_config->tabs->addChild($tab_id);
				$system_config->tabs->$tab_id->addChild('label', $tab->content->name);
				$hasUserCreatedElements=true;
			}
			
			foreach ($tab->item as $section) {
				$section_id=$section->getAttribute('id');
				if($section->getAttribute('user_created')==1){
					$config_helper->addChildIfNotExist($system_config, 'sections');
					$system_config->sections->addChild($section_id);
					$system_config->sections->$section_id->addChild('label', $section->content->name);
					$system_config->sections->$section_id->addChild('tab', $tab_id);
					$hasUserCreatedElements=true;
				}
				
				foreach ($section->item as $group) {
					$group_id=$group->getAttribute('id');
					if($group->getAttribute('user_created')==1){
						$config_helper->addChildIfNotExist($system_config, 'sections');
						$config_helper->addChildIfNotExist($system_config->sections, $section_id);
						$config_helper->addChildIfNotExist($system_config->sections->$section_id, 'groups');
						if($system_config->sections->$section_id->groups==NULL){
							Zend_Debug::dump($section_id, 'Section id : ');
							Zend_Debug::dump($section->getAttribute('user_created'), 'Section was user created : ');
						}
						$system_config->sections->$section_id->groups->addChild($group_id);
						$system_config->sections->$section_id->groups->$group_id->addChild('label', $group->content->name);
						$hasUserCreatedElements=true;
					}
					
					foreach ($group->item as $field) {
						$field_id=$field->getAttribute('id');
						if($field->getAttribute('user_created')==1){
							$config_helper->addChildIfNotExist($system_config, 'sections');
							$config_helper->addChildIfNotExist($system_config->sections, $section_id);
							$config_helper->addChildIfNotExist($system_config->sections->$section_id, 'groups');
							$config_helper->addChildIfNotExist($system_config->sections->$section_id->groups, $group_id);
							$_fields=$config_helper->addChildIfNotExist($system_config->sections->$section_id->groups->$group_id, 'fields');
							$_fields->addChild($field_id);
							$_fields->$field_id->addChild('label', $field->content->name);
							$_fields->$field_id->addChild('type', $field->getAttribute('fieldType'));
							$hasUserCreatedElements=true;
						}
					}
				}
			}
		}
		if($hasUserCreatedElements){
			$etc_path=$this->getModule()->getEtcPath();
			Mage::helper('modulefactory/file')->createWDir($etc_path);
			$sys_config_file=Mage::helper('modulefactory/file')->createFile($etc_path.'system.xml');
			fwrite($sys_config_file, $system_config->asNiceXml());
			fclose($sys_config_file);

			$config_helper->addChildIfNotExist($config, 'adminhtml');
			$config_helper->addChildIfNotExist($config->adminhtml, 'resources');
			$config_helper->addChildIfNotExist($config->adminhtml->resources, 'all');
			$config_helper->addChildIfNotExist($config->adminhtml->resources->all, 'title', 'Allow Everything');
			$config_helper->addChildIfNotExist($config->adminhtml->resources, 'admin');
			$config_helper->addChildIfNotExist($config->adminhtml->resources->admin, 'children');
			$config_helper->addChildIfNotExist($config->adminhtml->resources->admin->children, 'system');
			$config_helper->addChildIfNotExist($config->adminhtml->resources->admin->children->system, 'children');
			$config_helper->addChildIfNotExist($config->adminhtml->resources->admin->children->system->children, 'config');
			$module_name=lcfirst($this->getModule()->getModuleName());
			$config_helper->addChildIfNotExist($config->adminhtml->resources->admin->children->system->children->config, $module_name, $module_name.' - All');
		}
	}
}
?>