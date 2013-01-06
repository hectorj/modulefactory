<?php 
class Hj_Modulefactory_Test_Model_Router extends EcomDev_PHPUnit_Test_Case{

	public function setUp(){
		parent::setUp();
	}
	/**
	 * Test the generation of the xml config
	 *
	 * @test
	 * @dataProvider provider
	 */
	public function testConfig($codePool, $packageName, $moduleName, $routerPath, $routerType, $config_filename_expec)
	{
		$module=new Hj_Modulefactory_Model_Module($packageName, $moduleName, '1.0.0', $codePool);
		
		$router=new Hj_Modulefactory_Model_Router($module, $routerPath, $routerType);
		
		$config=new Varien_Simplexml_Element('<config></config>');
		$router->generate($config);
		$expected=str_replace("\r", '', file_get_contents('app'.DS.'code'.DS.'local'.DS.'Hj'.DS.'Modulefactory'.DS.'Test'.DS.'Model'.DS.'Router'.DS.$config_filename_expec));
		$this->assertEquals($expected, $config->asNiceXml(), 'Bad xml config generated.');
	}
	
	public function provider(){
		return array(
			array('local', 'Unittestpackage', 'Testmodule', 'testfrontend', 'frontend', 'expected_config_frontend.xml'),
			array('local', 'Unittestpackage', 'Testmodule', 'testadmin', 'admin', 'expected_config_admin.xml')
		);
	}
	
	public function tearDown(){
		parent::tearDown();
	}
}
?>