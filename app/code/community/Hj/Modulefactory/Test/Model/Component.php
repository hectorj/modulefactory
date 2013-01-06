<?php 
class Hj_Modulefactory_Test_Model_Component extends EcomDev_PHPUnit_Test_Case{

	public function setUp(){
		parent::setUp();
	}
	/**
	 * Test the generation of the xml config
	 *
	 * @test
	 * @dataProvider provider
	 */
	public function testConfig($codePool, $packageName, $moduleName, $componentType, $componentName, $extends, $override, $config_filename_expec, $expected_file_path)
	{
		$module=new Hj_Modulefactory_Model_Module($packageName, $moduleName, '1.0.0', $codePool);
		
        $component_class='Hj_Modulefactory_Model_File_Class_Component_'.$componentType;
		$component=new $component_class($module, $componentName, $extends, $override);
                /* @var $component Hj_Modulefactory_Model_File_Class_Component */
		
		$config=new Varien_Simplexml_Element('<config></config>');
		$component->generate($config);
		$expected=str_replace("\r", '', file_get_contents('app'.DS.'code'.DS.'local'.DS.'Hj'.DS.'Modulefactory'.DS.'Test'.DS.'Model'.DS.'Component'.DS.$config_filename_expec));
		$this->assertEquals($expected, $config->asNiceXml(), 'Bad xml config generated.');
		$this->assertTrue(file_exists($expected_file_path), 'Component file doesn\'t exists.');
	}
	
	public function provider(){
		$base_module_path='app'.DS.'code'.DS.'local'.DS.'Unittestpackage'.DS.'Testmodule'.DS;
		return array(
			array('local', 'Unittestpackage', 'Testmodule', 'Helper', 'testhelper', null, null, 'expected_config_simplehelper.xml', $base_module_path.'Helper'.DS.'Testhelper.php'),
			array('local', 'Unittestpackage', 'Testmodule', 'Block', 'testblock', null, null, 'expected_config_simpleblock.xml', $base_module_path.'Block'.DS.'Testblock.php'),
			array('local', 'Unittestpackage', 'Testmodule', 'Model', 'testmodel', null, null, 'expected_config_simplemodel.xml', $base_module_path.'Model'.DS.'Testmodel.php')
		);
	}
	
	public function tearDown(){
		Mage::helper('modulefactory/file')->rrmdir('app'.DS.'code'.DS.'local'.DS.'Unittestpackage');
		parent::tearDown();
	}
}
?>
