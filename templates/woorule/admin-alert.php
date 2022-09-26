<?php
/**
 * Admin settings.
 *
 * @package Woorule
 * @codeCoverageIgnore
 */

defined( 'ABSPATH' ) || exit;

/* @var array $args Template arguments. */
?>
<script>
	jQuery( document ).ready( function ( $ ) {
		var elm = $( '#woorule_alert_product_show' );
		if ( elm.is( ':checked' ) ) {
			$( '.alert-tr' ).removeClass( 'hidden' );
		}

		$( elm ).on( 'click', function() {
			if ( elm.is( ':checked' ) ) {
				$( '.alert-tr' ).removeClass( 'hidden' );
			} else {
				$( '.alert-tr' ).addClass( 'hidden' );
			}
		} );
	} );
</script>
<tr>
	<th>
		<h2><?php esc_html_e( 'Product Alert', 'woorule' ); ?></h2>
	</th>
</tr>
<tr>
	<th>
		<label for="woorule_alert_product_show">
			<?php esc_html_e( 'Show signup form on the product page', 'woorule' ); ?>
		</label>
	</th>
	<td>
		<input type="checkbox" name="woorule_alert_product_show" id="woorule_alert_product_show" <?php checked( $args['show'], 'on' ); ?> />
		<span class="description">
			<?php esc_html_e( 'Display a signup form on the product page', 'woorule' ); ?>
		</span>
	</td>
</tr>
<tr class="alert-tr hidden">
	<th>
		<label for="woorule_alert_label">
			<?php esc_html_e( 'Signup form label', 'woorule' ); ?>
		</label>
	</th>
	<td>
		<input name="woorule_alert_label" id="woorule_alert_label" type="text" value="<?php echo esc_attr( $args['label'] ); ?>" class="regular-text code"/>
		<span class="description">
					<?php esc_html_e( 'Text to display next to the signup form', 'woorule' ); ?>
				</span>
	</td>
</tr>
<tr class="alert-tr hidden">
	<th>
		<label for="woorule_alert_placeholder">
			<?php esc_html_e( 'Placeholder', 'woorule' ); ?>
		</label>
	</th>
	<td>
		<input name="woorule_alert_placeholder" id="woorule_alert_placeholder" type="text" value="<?php echo esc_attr( $args['placeholder'] ); ?>" class="regular-text code"/>
		<span class="description"> <?php esc_html_e( 'Placeholder', 'woorule' ); ?> </span>
	</td>
</tr>
<tr class="alert-tr hidden">
	<th>
		<label for="woorule_alert_success">
			<?php esc_html_e( 'Message for successful submission', 'woorule' ); ?>
		</label>
	</th>
	<td>
		<input name="woorule_alert_success" id="woorule_alert_success" type="text" value="<?php echo esc_attr( $args['success'] ); ?>" class="regular-text code"/>
		<span class="description"> <?php esc_html_e( 'Message for successful submission', 'woorule' ); ?> </span>
	</td>
</tr>
<tr class="alert-tr hidden">
	<th>
		<label for="woorule_alert_error">
			<?php esc_html_e( 'Message for failed submission', 'woorule' ); ?>
		</label>
	</th>
	<td>
		<input name="woorule_alert_error" id="woorule_alert_error" type="text" value="<?php echo esc_attr( $args['error'] ); ?>" class="regular-text code"/>
		<span class="description"> <?php esc_html_e( 'Message for failed submission', 'woorule' ); ?> </span>
	</td>
</tr>
<tr class="alert-tr hidden">
	<th>
		<label for="woorule_alert_button">
			<?php esc_html_e( 'Button text', 'woorule' ); ?>
		</label>
	</th>
	<td>
		<input name="woorule_alert_button" id="woorule_alert_button" type="text" value="<?php echo esc_attr( $args['button'] ); ?>" class="regular-text code"/>
		<span class="description"> <?php esc_html_e( 'Button text', 'woorule' ); ?> </span>
	</td>
</tr>
<tr class="alert-tr hidden">
	<th>
		<label for="woorule_alert_tags">
			<?php esc_html_e( 'Subscriber Tags', 'woorule' ); ?>
		</label>
	</th>
	<td>
		<input name="woorule_alert_tags" id="woorule_alert_tags" type="text" value="<?php echo esc_attr( $args['tags'] ); ?>" class="regular-text code"/>
		<span class="description">
			<?php esc_html_e( 'Signup form tags (Comma separated)', 'woorule' ); ?>
			<?php esc_html_e( 'Tags that will be added to a subscriber on the Rule platform as soon as a new alert is created', 'woorule' ); ?>
		</span>
	</td>
</tr>
<tr class="alert-tr hidden">
	<th>
		<label for="woorule_alert_product_tags">
			<?php esc_html_e( 'Subscriber Tags', 'woorule' ); ?>
		</label>
	</th>
	<td>
		<input name="woorule_alert_product_tags" id="woorule_alert_product_tags" type="text" value="<?php echo esc_attr( $args['product_tags'] ); ?>" class="regular-text code"/>
		<span class="description">
			<?php esc_html_e( 'Signup form tags (Comma separated)', 'woorule' ); ?>
			<?php esc_html_e( 'Tags that are applied to a subscriber when an alert is triggered. Note: If this option is provided it will overwrite any existing alert tags.', 'woorule' ); ?>
		</span>
	</td>
</tr>
<tr class="alert-tr hidden">
	<th>
		<label for="woorule_alert_min_stock">
			<?php esc_html_e( 'Minimum stock', 'woorule' ); ?>
		</label>
	</th>
	<td>
		<input name="woorule_alert_min_stock" id="woorule_alert_min_stock" type="number" value="<?php echo esc_attr( $args['min_stock'] ); ?>" class="regular-text code"/>
		<span class="description"> <?php esc_html_e( 'The minimum stock units needed to start triggering alerts. The default is 10.', 'woorule' ); ?> </span>
	</td>
</tr>
<tr class="alert-tr hidden">
	<th>
		<label for="woorule_alerts_per_stock">
			<?php esc_html_e( 'Number of alerts', 'woorule' ); ?>
		</label>
	</th>
	<td>
		<input name="woorule_alerts_per_stock" id="woorule_alerts_per_stock" type="number" value="<?php echo esc_attr( $args['per_stock'] ); ?>" class="regular-text code"/>
		<span class="description"> <?php esc_html_e( 'The number of alerts that are triggered for every inventory unit in stock. The default is 20.', 'woorule' ); ?> </span>
	</td>
</tr>

