<?php
/**
 * Class Woorule_Shortcode
 *
 * @package Woorule
 */

/**
 * Class Woorule_Shortcode
 *
 * @package Woorule
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 * @SuppressWarnings(PHPMD.CamelCaseParameterName)
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 * @SuppressWarnings(PHPMD.CamelCaseVariableName)
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class Woorule_Shortcode {
	/**
	 * Woorule_Shortcode constructor.
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ) );
		add_shortcode( 'woorule', array( $this, 'output' ) );

		add_action(
			'wp_ajax_woorule_subscribe_user',
			array( __CLASS__, 'subscribe_user' )
		);
		add_action(
			'wp_ajax_nopriv_woorule_subscribe_user',
			array( __CLASS__, 'subscribe_user' )
		);
	}

	/**
	 * Plugin Stylesheet.
	 *
	 * @return void
	 */
	public function register_assets() {
		global $post;

		if ( $post && has_shortcode( $post->post_content, 'woorule' ) ) {
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_enqueue_style(
				'woorule',
				WOORULE_URL . 'assets/woorule' . $suffix . '.css',
				array(),
				WOORULE_VERSION
			);

			wp_enqueue_script(
				'woorule',
				WOORULE_URL . 'assets/woorule' . $suffix . '.js',
				array( 'jquery' ),
				WOORULE_VERSION,
				true
			);

			wp_localize_script(
				'woorule',
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
				'title'          => __( 'Newsletter subscription', 'woorule' ),
				'submit'         => __( 'Submit', 'woorule' ),
				'placeholder'    => __( 'Your e-mail', 'woorule' ),
				'success'        => __( 'Thank you!', 'woorule' ),
				'error'          => __( 'Oops, something is wrong..', 'woorule' ),
				'tag'            => '',
				'checkbox'       => '',
				'require_opt_in' => false,
			),
			$atts,
			'woorule'
		);

		ob_start();

		load_template( WOORULE_PATH . 'inc/partials/shortcode-woorule.php', false, $atts );

		return ob_get_clean();
	}

	/**
	 * Subscribe user.
	 *
	 * @return void
	 * @SuppressWarnings(PHPMD.Superglobals)
	 * @SuppressWarnings(PHPMD.ExitExpression)
	 * @codeCoverageIgnore
	 */
	public static function subscribe_user() {
		// Check for nonce security.
		if (
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			! wp_verify_nonce( wc_clean( $_POST['nonce'] ), 'woorule' )
			||
			! isset( $_POST['email'] )
		) {
			die( 'err' );
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
		$email = filter_var( wc_clean( $_POST['email'] ), FILTER_VALIDATE_EMAIL );
		if ( ! $email ) {
			die( 'err' );
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
		$require_opt_in = filter_var( wc_clean( $_POST['requireOptIn'] ), FILTER_VALIDATE_BOOLEAN );

		$subscription = array(
			'apikey'              => Woorule_Options::get_api_key(),
			'update_on_duplicate' => true,
			'auto_create_tags'    => true,
			'auto_create_fields'  => true,
			'async'               => true,
			'tags'                => $tags,
			'subscribers'         => array(
				'email' => $email,
			),
			'require_opt_in'      => $require_opt_in,
		);

		RuleMailer_API::subscribe( $subscription );

		die( 'ok' );
	}
}
