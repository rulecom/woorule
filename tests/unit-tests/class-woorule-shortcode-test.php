<?php

class Woorule_Shortcode_Test extends WC_Unit_Test_Case {
	public function test_output() {
		$object = new Woorule_Shortcode();

		$result = $object->output( array(
			'title' => 'Newsletter test',
		) );
		$this->assertIsString( $result );
		$this->assertStringContainsString( 'Newsletter test', $result );
	}
}
