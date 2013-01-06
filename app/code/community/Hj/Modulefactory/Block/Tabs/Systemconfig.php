<?php

/**
 * Description of Sql
 *
 * @author PC
 */
class Hj_Modulefactory_Block_Tabs_Systemconfig extends Hj_Modulefactory_Block_Template {

	public function loadCurrentConfig() {
		$html = '<ul>';
		$configFields = Mage::getSingleton('adminhtml/config');
		/* @var $configFields Mage_Adminhtml_Model_Config */

		$tabs = (array) $configFields->getTabs()->children();
		usort($tabs, array($this, '_sort'));

		$sections = (array) $configFields->getSections();
		usort($sections, array($this, '_sort'));

		foreach ($tabs as $tab) {
			/* @var $tab Mage_Core_Model_Config_Element */
			$html.='<li rel="tab" id="' . $tab->getName() . '" user_created=0><a href="#">' . $tab->label . '</a><ul>';
			foreach ($sections as $section) {
				if ($section->tab == $tab->getName()) {
					$html.='<li rel="section" id="' . $section->getName() . '" user_created=0><a href="#">' . $section->label . '</a><ul>';
					if ($section->groups) {
						$_groups = (array) $section->groups;
						foreach ($_groups as $_group) {
							$html.='<li rel="group" id="' . $_group->getName() . '" user_created=0><a href="#">' . $_group->label . '</a><ul>';
							if ($_group->fields) {
								$_fields = (array) $_group->fields;
								foreach ($_fields as $_field) {
									$html.='<li rel="field" id="' . $_field->getName() . '" user_created=0><a href="#">' . $_field->label . '</a></li>';
								}
							}
							$html.='</ul></li>';
						}
					}
					$html.='</ul></li>';
				}
			}
			$html.='</ul></li>';
		}
		$html.='</ul>';
		return $html;
	}

	protected function _sort($a, $b) {
		return (int) $a->sort_order < (int) $b->sort_order ? -1 : ((int) $a->sort_order > (int) $b->sort_order ? 1 : 0);
	}

}

?>