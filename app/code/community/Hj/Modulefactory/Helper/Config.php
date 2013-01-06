<?php

/**
 * Description of Config
 *
 * @author Hectorj
 */
class Hj_Modulefactory_Helper_Config extends Mage_Core_Helper_Abstract {
	/**
	 * Add a $child to $element if it doesn't exist already
	 * @param SimpleXMLElement $element
	 * @param string $child
	 * @return SimpleXMLElement
	 */
	public function addChildIfNotExist(SimpleXMLElement $element, $child, $value=null){
		if(!$element->$child || !($element->$child instanceof SimpleXMLElement)){
			return $element->addChild($child, $value);
		}
		return $element->$child;
	}
}

?>
