<?php 
class Hj_Modulefactory_Test_Model_Controller extends EcomDev_PHPUnit_Test_Case{

	public function setUp(){
		parent::setUp();
	}
	/**
	 * Test the generation of the xml config
	 *
	 * @test
	 * @dataProvider provider
	 */
	public function testConfig($codePool, $packageName, $moduleName, $routerPath, $routerType, $url, $extends, $overrideUrl, $config_filename_expec, $expected_file_path)
	{
		$module=new Hj_Modulefactory_Model_Module($packageName, $moduleName, '1.0.0', $codePool);
		
		$router=new Hj_Modulefactory_Model_Router($module, $routerPath, $routerType);
		
		$controller=new Hj_Modulefactory_Model_file_Class_Controller($router, $url, $extends, $overrideUrl);
		
		$config=new Varien_Simplexml_Element('<config></config>');
		$controller->generate($config);
		$expected=str_replace("\r", '', file_get_contents('app'.DS.'code'.DS.'local'.DS.'Hj'.DS.'Modulefactory'.DS.'Test'.DS.'Model'.DS.'Controller'.DS.$config_filename_expec));
		$this->assertEquals($expected, $config->asNiceXml(), 'Bad xml config generated.');
		$this->assertTrue(file_exists($expected_file_path), 'Controller file doesn\'t exists.');
	}
	
	public function provider(){
		$base_module_path='app'.DS.'code'.DS.'local'.DS.'Unittestpackage'.DS.'Testmodule'.DS;
		return array(
			array('local', 'Unittestpackage', 'Testmodule', 'testfrontend', 'frontend', 'index', 'Mage_Catalog_Product_CompareController', 'catalog/product_compare', 'expected_config_frontend_override.xml', $base_module_path.'controllers'.DS.'IndexController.php'),
			array('local', 'Unittestpackage', 'Testmodule', 'testadmin', 'admin', 'adminhtml_index', 'Mage_Adminhtml_Catalog_CategoryController', 'admin/catalog_category', 'expected_config_admin_override.xml', $base_module_path.'controllers'.DS.'Adminhtml'.DS.'IndexController.php')
		);
	}
	
	public function tearDown(){
		Mage::helper('modulefactory/file')->rrmdir('app'.DS.'code'.DS.'local'.DS.'Unittestpackage');
		parent::tearDown();
	}
}
?>