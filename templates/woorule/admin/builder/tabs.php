<?php

// phpcs:disable Generic.WhiteSpace.DisallowSpaceIndent.SpacesUsed
// phpcs:disable WordPress.WhiteSpace.PrecisionAlignment.Found

defined( 'ABSPATH' ) || exit;

/** @var string $current_section */
?>
<ul class="subsubsub">
    <li>
        <a href="<?php echo admin_url( 'admin.php?page=woorule_builder&section=orders' ); ?>" class="<?php echo ( 'orders' === $current_section ? 'current' : '' ); ?>">
			<?php esc_html_e( 'Orders', 'woorule' ); ?>
        </a> |
    </li>
    <li>
        <a href="<?php echo admin_url( 'admin.php?page=woorule_builder&section=cart' ); ?>" class="<?php echo ( 'cart' === $current_section ? 'current' : '' ); ?>">
			<?php esc_html_e( 'Cart', 'woorule' ); ?>
        </a>
    </li>
</ul>
<br class="clear" />
