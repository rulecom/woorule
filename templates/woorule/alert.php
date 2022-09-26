<?php
/**
 * 'woorule_alert' shortcode.
 *
 * @package Woorule
 * @codeCoverageIgnore
 */

defined( 'ABSPATH' ) || exit;

/* @var array $args Template arguments. */
?>

<div class="woorule-alert">
	<form>
		<input type="hidden" value="<?php echo esc_html( $args['product_id'] ); ?>" name="product_id" />
		<label for="semail" class="form_elem"><?php echo esc_html( $args['label'] ); ?></label>
		<input type="text" id="semail" name="email" class="form_elem" placeholder="<?php echo esc_html( $args['placeholder'] ); ?>"/>
		<input type="submit" value="<?php echo esc_html( $args['button'] ); ?>" class="form_elem"
			<?php disabled( ! empty( $args['checkbox'] ) ); ?>
		/>
		<input type="hidden" value="<?php echo esc_html( $args['tag'] ); ?>" name="tags" class="tag"/>
		<input type="hidden" value="<?php echo esc_html( $args['require_opt_in'] ); ?>" name="require-opt-in"/>
		<p class="hidden success"><?php echo esc_html( $args['success'] ); ?></p>
		<p class="hidden error"><?php echo esc_html( $args['error'] ); ?></p>

		<?php if ( $args['checkbox'] ) : ?>
			<label>
				<input type="checkbox" class="woorule-subscribe__checkbox">
				<?php echo wp_kses_post( $args['checkbox'] ); ?>
			</label>
		<?php endif; ?>
	</form>
</div>
