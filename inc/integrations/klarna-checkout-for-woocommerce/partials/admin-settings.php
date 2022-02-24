<?php
/**
 * Admin settings.
 *
 * @package Woorule
 */

defined( 'ABSPATH' ) || exit;

/* @var array $args Template arguments. */
?>

<tr>
	<th>
		<label for="woorule_klarna_checkout_show">
			<?php esc_html_e( 'Show signup form on Klarna checkout', 'woorule' ); ?>
		</label>
	</th>
	<td>
		<input type="checkbox" name="woorule_klarna_checkout_show"
		id="woorule_klarna_checkout_show" <?php checked( $args['show'], 'on' ); ?> />
		<span class="description">
			<?php esc_html_e( 'Display a signup form on the Klarna checkout form', 'woorule' ); ?>
		</span>
	</td>
</tr>
