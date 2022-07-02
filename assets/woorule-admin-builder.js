jQuery( document ).ready(
	function ( $ ) {
		// Toggle on/off.
		$( document ).on(
			'click',
			'.woorule-field-toggle-attribute',
			function() {
				var $link = $( this ),
				$row      = $link.closest( 'tr' ),
				$toggle   = $link.find( '.woocommerce-input-toggle' ),
				$section  = $link.data( 'section' ),
				$field    = $link.data( 'field' ),
				$status   = $( '[name="' + $field + '_status"]' );

				if ( $status.val() === '1' ) {
					$status.val( '0' );
				} else {
					$status.val( '1' );
				}

				var data = {
					action: 'woorule_builder_field_enabled',
					nonce: Woorule_Admin_Builder.nonce,
					section: $section,
					field: $field,
					status: $status.val()
				};

				$toggle.addClass( 'woocommerce-input-toggle--loading' );

				$.ajax(
					{
						url:      Woorule_Admin_Builder.ajax_url,
						data:     data,
						dataType : 'json',
						type     : 'POST',
						success:  function( response ) {
							if ( true === response.data ) {
								$toggle.removeClass( 'woocommerce-input-toggle--enabled, woocommerce-input-toggle--disabled' );
								$toggle.addClass( 'woocommerce-input-toggle--enabled' );
								$toggle.removeClass( 'woocommerce-input-toggle--loading' );
							} else if ( false === response.data ) {
								$toggle.removeClass( 'woocommerce-input-toggle--enabled, woocommerce-input-toggle--disabled' );
								$toggle.addClass( 'woocommerce-input-toggle--disabled' );
								$toggle.removeClass( 'woocommerce-input-toggle--loading' );
							}
						}
					}
				);
			}
		);

		$( document ).on(
			'click',
			'form.woorule #btn-submit',
			function( e) {
				var form = $( this ).closest( 'form' );

				if ( form.hasClass( 'processing' ) ) {
					return true;
				}

				e.preventDefault();

				async.parallel(
					{
						fields: function( callback2 ) {
							var fields = {};
							$( 'select.field_value', form ).each(
								function() {
									fields[ $( this ).prop( 'name' ) ] = $( this ).val();
								}
							).promise().done(
								function() {
									$( '[name="rule_order_fields"]', form ).val( JSON.stringify( fields ) );
									$( '[name="rule_cart_fields"]', form ).val( JSON.stringify( fields ) );

									callback2( null, fields );
								}
							);
						},
						statuses: function( callback2 ) {
							var statuses = {};
							$( 'input.field_status', form ).each(
								function() {
									var name                                  = $( this ).prop( 'name' );
									statuses[ name.replace( '_status', '' ) ] = $( this ).val();
								}
							).promise().done(
								function() {
									$( '[name="rule_order_fields_status"]', form ).val( JSON.stringify( statuses ) );
									$( '[name="rule_cart_fields_status"]', form ).val( JSON.stringify( statuses ) );

									callback2( null, statuses );
								}
							);
						},
					},
					function( err, results ) {
						form.addClass( 'processing' );
						form.submit();
					}
				);

				return false;
			}
		);
	}
);
