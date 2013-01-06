<?php 
class Hj_Modulefactory_Block_Tabs_Element_Template extends Varien_Data_Form_Element_Abstract {
	public function getElementHtml(){
		return $this->getBlock()->toHtml();
	}
}
?>