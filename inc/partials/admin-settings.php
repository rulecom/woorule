<?php
/**
 * Admin settings page.
 *
 * @package Woorule
 */

// phpcs:disable Generic.WhiteSpace.DisallowSpaceIndent.SpacesUsed
// phpcs:disable Universal.WhiteSpace.PrecisionAlignment.Found

defined( 'ABSPATH' ) || exit;

/* @var array $args Template arguments. */
?>

<form class="woorule" method="post">
	<a href="https://app.rule.io" target="_blank">
		<img width="128" src="<?php echo esc_url( $args['logo_url'] ); ?>" alt="" class="lazyloaded"
			data-ll-status="loaded"/>
	</a>
	<input type="hidden" name="page" value="woorule-settings"/>
	<input type="hidden" name="save" value="woorule"/>

	<table class="form-table" role="presentation">
		<tbody>
		<tr>
			<th>
				<h2><?php esc_html_e( 'Checkout form', 'woorule' ); ?></h2>
			</th>
		</tr>
		<tr>
			<th>
				<label for="woorule_checkout_show">
					<?php esc_html_e( 'Show signup form on checkout', 'woorule' ); ?>
				</label>
			</th>
			<td>
				<input type="checkbox" name="woorule_checkout_show"
					id="woorule_checkout_show" <?php checked( $args['show'], 'on' ); ?> />
				<span class="description">
					<?php esc_html_e( 'Display a signup form on the checkout page', 'woorule' ); ?>
				</span>
			</td>
		</tr>
		<tr>
			<th>
				<label for="woorule_checkout_label">
					<?php esc_html_e( 'Signup form label', 'woorule' ); ?>
				</label>
			</th>
			<td>
				<input name="woorule_checkout_label" id="woorule_checkout_label" type="text"
					value="<?php echo esc_attr( $args['label'] ); ?>" class="regular-text code"/>
				<span class="description">
					<?php esc_html_e( 'Text to display next to the signup form', 'woorule' ); ?>
				</span>
			</td>
		</tr>
		<tr>
			<th>
				<label for="woorule_checkout_tags">
					<?php esc_html_e( 'Tags', 'woorule' ); ?>
				</label>
			</th>
			<td>
				<input name="woorule_checkout_tags" id="woorule_checkout_tags" type="text"
					value="<?php echo esc_attr( $args['tags'] ); ?>" class="regular-text code"/>
				<span class="description">
					<?php esc_html_e( 'Signup form tags (Comma separated)', 'woorule' ); ?>
				</span>
			</td>
		</tr>
		<?php do_action( 'woorule_admin_settings_after_checkout' ); ?>
		<tr class="line">
			<th></th>
			<td></td>
		</tr>
		<tr>
			<th>
				<h2><?php esc_html_e( 'Configuration', 'woorule' ); ?></h2>
			</th>
		</tr>
		<tr>
			<th>
				<label for="woorule_api">
					<?php esc_html_e( 'Rule API Key', 'woorule' ); ?>
				</label>
			</th>
			<td>
				<input name="woorule_api" id="woorule_api" type="text" class="regular-text code" value="<?php echo esc_attr( $args['api_key'] ); ?>"/>
				<span class="description">
					<?php
                    echo wp_kses_post(
						__(
							'You can find your Rule API key in the <a href="https://app.rule.io/#/settings/developer">developer tab in your Rule account</a>.',
							'woorule'
						)
					);
					?>
				</span>
			</td>
		</tr>
		</tbody>
	</table>

	<?php wp_nonce_field( 'woorule-settings' ); ?>

	<?php submit_button(); ?>
</form>
