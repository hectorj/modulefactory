<?php

/**
 * Description of Sql
 *
 * @author Hectorj
 */
class Hj_Modulefactory_Test_Model_Sql extends EcomDev_PHPUnit_Test_Case {
	public function setUp(){
		parent::setUp();
	}
	/**
	 * Test the generation of the xml config
	 *
	 * @test
	 * @dataProvider provider
	 */
	public function testConfig($codePool, $packageName, $moduleName, $type, $version, $version_from, $config_filename_expec, $expected_file_path)
	{
		$module=new Hj_Modulefactory_Model_Module($packageName, $moduleName, '1.0.0', $codePool);
		
		$script_class='Hj_Modulefactory_Model_File_Sql_'.$type;
		if($version_from){
			$script=new $script_class($module, $version, $version_from);
		} else {
			$script=new $script_class($module, $version);
		}
		/* @var $script Hj_Module_Factory_Model_File_Sql */
		
		$config=new Varien_Simplexml_Element('<config></config>');
		$script->generate($config);
		$expected=str_replace("\r", '', file_get_contents('app'.DS.'code'.DS.'local'.DS.'Hj'.DS.'Modulefactory'.DS.'Test'.DS.'Model'.DS.'Sql'.DS.$config_filename_expec));
		$this->assertEquals($expected, $config->asNiceXml(), 'Bad xml config generated.');
		$this->assertTrue(file_exists($expected_file_path), 'SQL '.$type.' file doesn\'t exists.');
	}
	
	public function provider(){
		$base_path='app'.DS.'code'.DS.'local'.DS.'Unittestpackage'.DS.'Testmodule'.DS.'sql'.DS.'testmodule_setup'.DS;
		return array(
			array('local', 'Unittestpackage', 'Testmodule', 'Install', '0.0.1', null, 'expected_config.xml', $base_path.'mysql4-install-0.0.1.php'),
			array('local', 'Unittestpackage', 'Testmodule', 'Upgrade', '0.0.2', '0.0.1', 'expected_config.xml', $base_path.'mysql4-upgrade-0.0.1-0.0.2.php')
		);
	}
	
	public function tearDown(){
		parent::tearDown();
	}
}

?>
