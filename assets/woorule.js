(function ($) {

    function wooRule_error(obj,error) {
        console.log(error);
        obj.find('.error').text(error);
        obj.find('.error').show().delay(1000).queue(function (next) {
            $(this).hide();
            next();
        });
    }

    $(document).ready(function () {
        $('.woorule .btn').click(function () {
            form = $(this).parent();
            email = form.find('input').val();
            var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);

            if (pattern.test(email)) {
                $.post(
                    WooRule_Ajax.ajaxurl, {
                        // wp ajax action
                        action: 'ajax-wooruleSubmit',
                        // vars
                        email: email,
                        // send the nonce along with the request
                        nextNonce: WooRule_Ajax.nextNonce
                    },
                    function (response) {
                        console.log(response);
                        form.find('input').hide();
                        form.find('.btn').hide();
                        form.find('.success').show();
                    }
                ).fail(function(error) {
                    wooRule_error(form, error.responseText);
                  });
            } else {
                    wooRule_error(form, 'Please enter valid email address!');
                   }
            });

            return false;

    });
})(jQuery);