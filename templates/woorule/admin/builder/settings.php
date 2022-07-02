<?php

// phpcs:disable Generic.WhiteSpace.DisallowSpaceIndent.SpacesUsed
// phpcs:disable WordPress.WhiteSpace.PrecisionAlignment.Found

defined( 'ABSPATH' ) || exit;

/** @var string $section */
/** @var string $title */
/** @var string $option_group */
/** @var array $settings */
/** @var array $wc_fields WC Order fields. */
/** @var array $rule_fields Rule fields with assigns. */
/** @var array $rule_fields_status  */
?>

<h1 class="screen-reader-text"><?php esc_html( $title ); ?></h1>
<h2><?php esc_html( $title ); ?></h2>

<form id="woorule" class="woorule" method="post" action="options.php">
    <?php
    settings_fields( $option_group );
    do_settings_sections( $option_group );
    ?>

    <?php foreach ( $settings as $key => $value ) : ?>
        <input type="hidden" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( json_encode( $value ) ); ?>">
    <?php endforeach; ?>

    <div class="wrap woocommerce">
        <table class="form-table">
            <tr>
                <td>
                    <table class="wc_gateways widefat">
                        <thead>
                        <tr>
                            <th><?php esc_html_e( 'Enable', 'woorule' ); ?></th>
                            <th><?php esc_html_e( 'Rule field', 'woorule' ); ?></th>
                            <th><?php esc_html_e( 'WooCommerce Field', 'woorule' ); ?></th>
                        </tr>
                        </thead>
                        <tbody>
						<?php foreach ( $rule_fields as $rule_field => $wc_field ) : ?>
                            <tr>
                                <td class="required">
                                    <a data-field_attr="required" data-field="<?php echo esc_html( $rule_field ); ?>"
                                       data-section="<?php echo esc_html( $section ); ?>"
                                       class="woorule-field-toggle-attribute" href="#">
                                        <?php if ( $rule_fields_status[ $rule_field ] ) : ?>
                                            <span class="woocommerce-input-toggle woocommerce-input-toggle--enabled">Yes</span>
                                        <?php else : ?>
                                            <span class="woocommerce-input-toggle woocommerce-input-toggle--disabled">No</span>
                                        <?php endif; ?>
                                    </a>
                                    <input class="field_status" name="<?php echo esc_html( $rule_field ); ?>_status"
                                           type="hidden" value="<?php echo $rule_fields_status[ $rule_field ] ? '1' : '0'; ?>">

                                </td>
                                <td><?php echo esc_html( $rule_field ); ?></td>
                                <td>
                                    <select class="field_value" name="<?php echo esc_html( $rule_field ); ?>">
										<?php foreach ( $wc_fields as $field => $label ) : ?>
                                            <option value="<?php echo esc_html( $field ); ?>" <?php echo $wc_field === $field ? ' selected' : ''; ?>>
												<?php echo esc_html( $label ); ?>
                                            </option>
										<?php endforeach; ?>
                                    </select>
                                    <script type="application/javascript">
                                        jQuery(document).on( 'ready', function ( $ ) {
                                            jQuery( '[name="<?php echo esc_html( $rule_field ); ?>"]' ).select2( {
                                                placeholder: 'Select an option'
                                            } );
                                        } );
                                    </script>
                                </td>
                            </tr>
						<?php endforeach; ?>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
    </div>

	<?php submit_button( __( 'Save', 'woorule' ), 'primary', 'btn-submit' ); ?>
</form>
