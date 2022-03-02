<?php

class WC_Rule_Utils_Test extends WC_Unit_Test_Case {
	public function test_payment_gateway() {
		$object = new Woorule_Utils();
		$this->assertInstanceOf( Woorule_Utils::class, new $object );

		$result = Woorule_Utils::round( 125 );
		$this->assertEquals( '125.00', $result );
		$this->assertIsString( $result );
	}
}
