<?php 
class Hj_Modulefactory_Block_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {
	public function __construct()
	{
		parent::__construct();
		$this->setId('module_info_tabs');
		$this->setDestElementId('module_create_form');
		$this->setTitle(Mage::helper('catalog')->__('Module Parameters'));
	}
	
	protected function _prepareLayout()
	{
		$this->addTab('module_general_tab', array(
                    'label'     => Mage::helper('catalog')->__('General'),
                    'content'   => $this->getLayout()->createBlock('modulefactory/tabs_general')->toHtml()
                ));
		
		$this->addTab('module_components_tab', array(
				'label'     => Mage::helper('catalog')->__('Components'),
				'content'   => $this->getLayout()->createBlock('modulefactory/tabs_components')->setTemplate('modulefactory/component/tree.phtml')->toHtml()
		));
//		$types=array('helper', 'block', 'model');
//		$componentBlocks=array();
//		foreach($types as $type){
//			$componentBlocks[$type]=$this->getLayout()->createBlock('modulefactory/tabs_component');
//			$componentBlocks[$type]->setComponentType($type);
//			$this->addTab('module_'.$type.'s_tab', array(
//					'label'     => Mage::helper('catalog')->__(ucfirst($type).'s'),
//					'content'   => $componentBlocks[$type]->toHtml()
//			));
//		}
		
//		$this->addTab('module_controllers_tab', array(
//				'label'     => Mage::helper('catalog')->__('Controllers'),
//				'content'   => $this->getLayout()->createBlock('modulefactory/tabs_controllers')->toHtml()
//		));
		
		$this->addTab('module_systemconfig_tab', array(
				'label'     => Mage::helper('catalog')->__('Config System'),
				'content'   => $this->getLayout()->createBlock('modulefactory/tabs_systemconfig')->setTemplate('modulefactory/systemconfig/tree.phtml')->toHtml()
		));
		
		$this->addTab('module_sql_tab', array(
				'label'     => Mage::helper('catalog')->__('SQL scripts'),
				'content'   => $this->getLayout()->createBlock('modulefactory/tabs_sql')->toHtml()
		));
		
		return parent::_prepareLayout();
	}
}
?>