<?php

class Hj_Modulefactory_Block_Tabs_General extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('modulefactory_form_general', array('legend'=>Mage::helper('modulefactory')->__('Module General Parameters')));
      $form_data=Mage::getSingleton('adminhtml/session')->getFormData();
      $fieldset->addField('package', 'text', array(
      		'label'     =>	Mage::helper('modulefactory')->__('Package'),
      		'class'     =>	'required-entry  validate-strict ucfirst',
			'name'		=>	'package',
			'required'	=>	'true',
		));
      $fieldset->addField('name', 'text', array(
      		'label'     =>	Mage::helper('modulefactory')->__('Name'),
      		'class'     =>	'required-entry  validate-strict ucfirst',
      		'name'		=>	'name',
      		'required'	=>	'true',
      ));
	  $fieldset->addType('version', 'Hj_Modulefactory_Block_Tabs_Element_VersionNumber');
      $fieldset->addField('version', 'version', array(
      		'label'     =>	Mage::helper('modulefactory')->__('Version'),
      		'class'     =>	'required-entry validate-digits',
      		'name'		=>	'version',
      		'required'	=>	'true',
			'maxlength'	=>	3,
			'value'		=>	'1.0.0',
		    'style'		=>	'width:20px !important;'
      ));
	  $form_data['archive_module']=1;
	  $fieldset->addField('archive_module', 'hidden', array(
      		'label'     =>	'',
      		'name'		=>	'archive_module',
			'value'		=>	'1',
      ));
      $form->setValues($form_data);
      return parent::_prepareForm();
  }
}
?>
