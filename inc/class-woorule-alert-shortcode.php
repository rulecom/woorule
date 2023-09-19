<?php

/**
 * Class Woorule_Alert_Shortcode
 *
 * @package Woorule
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 * @SuppressWarnings(PHPMD.CamelCaseParameterName)
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 * @SuppressWarnings(PHPMD.CamelCaseVariableName)
 * @SuppressWarnings(PHPMD.MissingImport)
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class Woorule_Alert_Shortcode {
	/**
	 * Woorule_Shortcode constructor.
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ) );
		add_shortcode( 'woorule_alert', array( $this, 'output' ) );

		add_action(
			'wp_ajax_woorule_subscribe_alert',
			array( __CLASS__, 'subscribe_alert' )
		);
		add_action(
			'wp_ajax_nopriv_woorule_subscribe_alert',
			array( __CLASS__, 'subscribe_alert' )
		);
	}

	/**
	 * Plugin Stylesheet.
	 *
	 * @return void
	 */
	public function register_assets() {
		global $post;

		$add_assets = true;

		if ( is_product() && is_object( $post ) ) {
			$product = wc_get_product( $post->ID );

			if ( ! $product->is_in_stock() || $product->get_stock_quantity() <= 0 ) {
				$add_assets = true;
			}
		}

		if ( $add_assets || has_shortcode( $post->post_content, 'woorule_alert' ) ) {
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_enqueue_style(
				'woorule-alert',
				WOORULE_URL . 'assets/woorule-alert' . $suffix . '.css',
				array(),
				WOORULE_VERSION
			);

			wp_enqueue_script(
				'woorule-alert',
				WOORULE_URL . 'assets/woorule-alert' . $suffix . '.js',
				array( 'jquery' ),
				WOORULE_VERSION,
				true
			);

			wp_localize_script(
				'woorule-alert',
				'ajax_var',
				array(
					'url'   => admin_url( 'admin-ajax.php' ),
					'nonce' => wp_create_nonce( 'woorule' ),
				)
			);
		}
	}

	/**
	 * Output shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 *
	 * @return false|string
	 */
	public function output( $atts ) {
		$atts = shortcode_atts(
			array(
				'checkbox'       => '',
				'product_id'     => $atts['product_id'],
				'label'          => Woorule_Options::get_alert_label(),
				'placeholder'    => Woorule_Options::get_alert_placeholder(),
				'success'        => Woorule_Options::get_alert_success(),
				'error'          => Woorule_Options::get_alert_error(),
				'tag'            => Woorule_Options::get_alert_tags(),
				'button'         => Woorule_Options::get_alert_button(),
				'require_opt_in' => false,
			),
			$atts,
			'woorule_alert'
		);

		ob_start();

		wc_get_template(
			'woorule/alert.php',
			array(
				'args' => $atts,
			),
			'',
			__DIR__ . '/../templates/'
		);

		return ob_get_clean();
	}

	/**
	 * Subscribe alert.
	 *
	 * @return void
	 * @SuppressWarnings(PHPMD.Superglobals)
	 * @SuppressWarnings(PHPMD.ExitExpression)
	 * @codeCoverageIgnore
	 */
	public static function subscribe_alert() {
		// Check for nonce security.
		if (
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			! wp_verify_nonce( wc_clean( $_POST['nonce'] ), 'woorule' )
			||
			! isset( $_POST['email'] )
		) {
			wp_send_json_error(
				array(
					'state'   => 'error',
					'message' => __( 'Invalid data.', 'woorule' ),
				)
			);

			return;
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
		$product_id = wc_clean( $_POST['product_id'] );
		if ( ! $product_id ) {
			wp_send_json_error(
				array(
					'state'   => 'error',
					'message' => __( 'Product ID is invalid.', 'woorule' ),
				)
			);

			return;
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
		$email = filter_var( wc_clean( $_POST['email'] ), FILTER_VALIDATE_EMAIL );
		if ( ! $email ) {
			wp_send_json_error(
				array(
					'state'   => 'error',
					'message' => __( 'E-Mail is invalid.', 'woorule' ),
				)
			);

			return;
		}

		// Default tag should exist. Otherwise there will be an error from RULE API.
		$tags = array();
		// Add custom tags if set.
		if ( isset( $_POST['tags'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			foreach ( explode( ',', wc_clean( $_POST['tags'] ) ) as $tag ) {
				$tags[] = sanitize_text_field( $tag );
			}
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
		//$require_opt_in = filter_var( wc_clean( $_POST['requireOptIn'] ), FILTER_VALIDATE_BOOLEAN );

		$product = wc_get_product( $product_id );
		if ( ! $product->get_id() ) {
			wp_send_json_error(
				array(
					'state'   => 'error',
					'message' => __( 'Product is invalid.', 'woorule' ),
				)
			);

			return;
		}

		$result = ProductAlert_API::create_alert(
			array(
				'apikey'     => Woorule_Options::get_api_key(),
				'product_id' => $product->get_id(),
				'email'      => $email,
				'tags'       => $tags,
			)
		);

		if ( is_wp_error( $result ) ) {
			/** @var WP_Error $result */
			wp_send_json_error(
				array(
					'message' => $result->get_error_message(),
				)
			);

			return;
		}

		wp_send_json_success(
			array(
				'message' => $result['message'],
			)
		);
	}
}

new Woorule_Alert_Shortcode();
