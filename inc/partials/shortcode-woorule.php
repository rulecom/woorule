<?php
/**
 * 'woorule' shortcode.
 *
 * @package Woorule
 */

defined( 'ABSPATH' ) || exit;

/* @var array $args Template arguments. */
?>

<div class="woorule-subscribe">
	<form>
		<label for="semail" class="form_elem"><?php echo esc_html( $args['title'] ); ?></label>
		<input type="text" id="semail" name="email" class="form_elem" placeholder="<?php echo esc_html( $args['placeholder'] ); ?>"/>
		<input type="submit" value="<?php echo esc_html( $args['submit'] ); ?>" class="form_elem"/>
		<input type="hidden" value="<?php echo esc_html( $args['tag'] ); ?>" name="tags" class="tag"/>
		<p class="hidden success"><?php echo esc_html( $args['success'] ); ?></p>
		<p class="hidden error"><?php echo esc_html( $args['error'] ); ?></p>
	</form>
</div>
