<?php 
class Hj_Modulefactory_Test_Helper_Data extends EcomDev_PHPUnit_Test_Case{
	/**
	 * Send one succes, one failure
	 *
	 * @test
	 */
	public function dummy()
	{
		$this->assertEquals(true,true, 'WTF ?!?!?! True is not true ?!?');
		//$this->assertEquals(true,false, 'True is not false. Thanks Captain Obvious ;) !');
	}
}
?>