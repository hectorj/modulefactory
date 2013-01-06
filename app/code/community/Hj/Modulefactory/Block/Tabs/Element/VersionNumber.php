<?php 
class Hj_Modulefactory_Block_Tabs_Element_VersionNumber extends Varien_Data_Form_Element_Text {
    public function getElementHtml()
    {
		$values=explode('.', $this->getEscapedValue());
		if(count($values)!=3){
			$values=array('','','');
		}
        $html = '<input id="'.$this->getHtmlId().'0" name="'.$this->getName()
             .'0" value="'.$values[0].'" '.$this->serialize($this->getHtmlAttributes()).'/>&nbsp;.&nbsp;';
		$html.= '<input id="'.$this->getHtmlId().'1" name="'.$this->getName()
             .'1" value="'.$values[1].'" '.$this->serialize($this->getHtmlAttributes()).'/>&nbsp;.&nbsp;';
		$html.= '<input id="'.$this->getHtmlId().'2" name="'.$this->getName()
             .'2" value="'.$values[2].'" '.$this->serialize($this->getHtmlAttributes()).'/>'."\n";
        $html.= $this->getAfterElementHtml();
        return $html;
    }
	
	    public function getHtmlAttributes()
    {
        return array('type', 'title', 'class', 'style', 'onclick', 'onchange', 'onkeyup', 'disabled', 'readonly', 'maxlength', 'tabindex', 'size');
    }
}
?>