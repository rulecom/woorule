jQuery(document).ready(function ($) {
    
    if ($('.woorule-custom-fields').length) {
 
        $('body').on('click', '.woorule-custom-fields .field-row .remove', function (e) {
            e.preventDefault();
            $(this).parent().remove();
        });

        $('body').on('click', '.woorule-custom-fields .add-one-more', function (e) {
            e.preventDefault();
            $('.woorule-custom-fields .fields').append($('.woorule-custom-fields .empty').html());
        });

        $('body').on('click', '.woorule-custom-fields .metas li', function (e) {
            e.preventDefault();
            var src = $(this);
            var key = $('.woorule-custom-fields .fields li').length;
            let field = $('.woorule-custom-fields .fields').append('<li class="field-row f'+key+'">'+$('.woorule-custom-fields .empty li').html()+'</li>');
            $(field).find('.f' + key + ' input[name="attribute"]').val($(src).text().replace('Add', ''));
            $(field).find('.f'+key+' select[name="source"]').val($(src).attr('type'));
            
        });

         

        $('body').on('click', '.woorule-custom-fields .show-all-metas', function (e) {
            e.preventDefault();
            $('.woorule-custom-fields .metas').toggle();
        });

        $('body').on('click', '.woocommerce-save-button', function (e) {

            if ($('.woorule-custom-fields').length) {
                const values = []
        
                $(".woorule-custom-fields .fields li").each(function () {
                    values.push({
                        'attribute': $(this).find('input[name="attribute"]').val(),
                        'source': $(this).find('select[name="source"]').val()
                    });
                });
       
                $('input[name ^= "woorule_custom_fields"]').val(JSON.stringify(values));
            }
        });

        // spawn fields

        const input = $('input[name ^= "woorule_custom_fields"]').val();
        if (input.length) {
            var fields = JSON.parse(input);

            Object.keys(fields).forEach(function(key) {
                console.log(fields[key].attribute);
                let field = $('.woorule-custom-fields .fields').append('<li class="field-row f'+key+'">'+$('.woorule-custom-fields .empty li').html()+'</li>');
                $(field).find('.f'+key+' input[name="attribute"]').val(fields[key].attribute);
                $(field).find('.f'+key+' select[name="source"]').val(fields[key].source);
            });
        }

    }
    

});