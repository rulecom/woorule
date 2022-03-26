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

        $( '.woorule-alert form' ).submit(
            function (e) {
                e.preventDefault();
                $( '.woorule-alert .error' ).hide();

                let product_id = $( '.woorule-alert #product_id' ).val();
                let email      = $( '.woorule-alert #semail' ).val();
                let tags       = $( '.woorule-alert .tag' ).val();
                //const requireOptIn = $( '.woorule-alert [name="require-opt-in"]' ).val();

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
                                return;
                            }

                        },
                    }
                );

            }
        );

    }
);
