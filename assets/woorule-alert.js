jQuery( document ).ready(
    function ($) {

        function woorule_error() {
            $( '.woorule-alert .error' ).show();
        }

        $( '.woorule-alert__checkbox', '.woorule-alert' ).on(
            'change',
            function ( target ) {
                $( 'input[type="submit"]', '.woorule-alert form' ).prop( 'disabled', ! target.checked )
            }
        )

        $( document ).on( 'click', '.woorule-alert [type="submit"]', function( e ) {
            e.preventDefault();
            $( '.woorule-alert .error' ).hide();

            let form       = $( this ).closest('.woorule-alert');
            let product_id = $( '[name="product_id"]', form ).val();
            let email      = $( '[name="email"]', form ).val();
            let tags       = $( '[name="tags"]', form ).val();

            //const requireOptIn = $( '[name="require-opt-in"]', form ).val();

            if (( ! email) || (email.length < 4)) {
                woorule_error();
                return;
            }

            if ( ! tags) {
                tags = 'Alert';
            }

            console.log( 'Shortcode form input: ' + email );

            $.ajax(
                {
                    url: ajax_var.url,
                    type: 'post',
                    data: {
                        action: 'woorule_subscribe_alert',
                        nonce: ajax_var.nonce,
                        product_id,
                        email,
                        tags,
                        //requireOptIn
                    },
                    success( data ) {
                        if (data.success) {
                            $( '.woorule-alert .form_elem' ).hide();
                            $( '.woorule-alert .success' ).show();
                            console.log( data );

                        } else {
                            woorule_error();
                        }
                    },
                }
            );

            return false;
        } );

    }
);
