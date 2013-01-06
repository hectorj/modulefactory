<?php

class Hj_Modulefactory_Block_Tabs_Sql extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$form_data=Mage::getSingleton('adminhtml/session')->getFormData();
		$fieldset = $form->addFieldset('modulefactory_form_sql', array('legend'=>Mage::helper('modulefactory')->__('SQL scripts')));
		
		$fieldset->addType('version', 'Hj_Modulefactory_Block_Tabs_Element_VersionNumber');
		$fieldset->addField('addSqlInstallVersion', 'version', array(
			'name'=>'addSqlInstallVersion',
			'class'=>'validate-digits',
			'label' => 'Version of the sql install script',
			'maxlength'=>3,
			'style'=>'width:20px !important;'
			)
		)->setAfterElementHtml('*leave blank if you don\'t need it.');
		$grid= $this->getLayout()->createBlock('core/template');
		$grid->setTemplate('modulefactory/sql/grid.phtml');
		$values=array();
		if(isset($form_data['SqlUpgradeFrom'])){
			foreach ($form_data['SqlUpgradeFrom'] as $key=>$value) {
				$values[]=array($value, $form_data['SqlUpgradeTo'][$key]);
			}
		}
		$grid->setValues($values);
		
		$fieldset->addType('componentgrid', 'Hj_Modulefactory_Block_Tabs_Element_Template');
		$fieldset->addField('addSqlUpgrade', 'componentgrid', array('label'=>'Add upgrade scripts'))->setBlock($grid);
		
		
		$form->setValues($form_data);
		return parent::_prepareForm();
	}
}
?>
