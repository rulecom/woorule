<?php

trait Woorule_Logging {

	/**
	 * Log.
	 *
	 * @param mixed $msg Message.
	 *
	 * @return void
	 * @SuppressWarnings(PHPMD.MissingImport)
	 * @SuppressWarnings(PHPMD.ElseExpression)
	 */
	private static function log( $msg ) {
		if ( WP_DEBUG === true ) {
			$logger = new WC_Logger();

			if ( is_array( $msg ) || is_object( $msg ) ) {
				$logger->add( 'woorule', wc_print_r( $msg, true ) );
			} else {
				$logger->add( 'woorule', $msg );
			}
		}
	}
}
