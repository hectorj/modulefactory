<?php

class Hj_Modulefactory_Block_Tabs_Components extends Hj_Modulefactory_Block_Template
{
	public function getExtendsDefault($type){
		if($type=='controller'){
			return 'Mage_Core_Controller_Front_Action';
		}
		return 'Mage_Core_'.ucfirst($type).'_Abstract';
	}
	public function getComponentsOptions($type){
		$map=unserialize(@file_get_contents('var/moduleMap'));
		if(!$map){
			Mage::throwException('Cannot read moduleMap file.');
		}
		
		$moduleOptions='<option value="">None</option>';
		if($type=='controller'){
			$moduleOptions.='<option controllerType="frontend" selected="selected"  value="Mage_Core_Controller_Front_Action">Core/Controller/Front/Action.php</option>';
			$moduleOptions.='<option controllerType="admin" value="Mage_Adminhtml_Controller_Action" disabled="disabled" >Adminhtml/Controller/Action.php</option>';
			$i=1;
			foreach ($map as $codePoolName=>$codePool){
				foreach ($codePool as $moduleName=>$module){
					if(isset($module['controller']) && is_array($module['controller']) && count($module['controller'])){
						$moduleOptions.='<optgroup label="'.$moduleName.'">';
						foreach ($module['controller'] as $fileName){
							$fileName=str_replace('app/code/'.$codePoolName.'/', '', $fileName);
							$componentName=str_replace('/', '_', $fileName);
							$componentName=str_replace('.php', '', $componentName);
							$fileName=explode('/', $fileName);
							unset($fileName[0]);
							unset($fileName[1]);
							unset($fileName[2]);
							$fileName=implode('/', $fileName);
							$moduleOptions.='<option value="'.$componentName.'" '.($moduleName=='Mage_Adminhtml' ? 'controllerType="admin" disabled="disabled"':'controllerType="frontend"').' >'.$fileName.'</option>';
						}
						$moduleOptions.='</optgroup>';
					}
				}
				$i++;
			}
		} else {
			$i=1;
			foreach ($map as $codePoolName=>$codePool){
				foreach ($codePool as $moduleName=>$module){
					if(isset($module[$type]) && is_array($module[$type]) && count($module[$type])){
						$moduleOptions.='<optgroup label="'.$moduleName.'">';
						foreach ($module[$type] as $fileName){
							$fileName=str_replace('app/code/'.$codePoolName.'/', '', $fileName);
							$componentName=str_replace('/', '_', $fileName);
							$componentName=str_replace('.php', '', $componentName);
							$fileName=explode('/', $fileName);
							unset($fileName[0]);
							unset($fileName[1]);
							unset($fileName[2]);
							$fileName=implode('/', $fileName);
							$moduleOptions.='<option value="'.$componentName.'" '.($componentName == 'Mage_Core_'.ucfirst($type).'_Abstract' ? 'selected="selected"' : '').' >'.$fileName.'</option>';
						}
						$moduleOptions.='</optgroup>';
					}
				}
				$i++;
			}
		}
		return $moduleOptions;
	}
	public function loadCurrentConfig(){
		$html='<ul><li rel="component_root" componentType="block"><a href="#">Block</a></li>
			<li rel="component_root" componentType="helper"><a href="#">Helper</a></li>
			<li rel="component_root" componentType="model"><a href="#">Model</a></li>
			<li rel="controller_root" componentType="controller"><a href="#">controllers</a></li>
			</ul>';
		return $html;
	}
}