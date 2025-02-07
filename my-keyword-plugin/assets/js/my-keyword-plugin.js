jQuery(document).ready(function($) {
    $('#keyword-research-form').on('submit', function(e) {
        e.preventDefault();

        var seed_keyword = $('#seed_keyword').val();
        var nonce = $('#my_keyword_plugin_nonce_field').val();

        $.ajax({
            type: 'POST',
            url: ajaxurl, // در صورت استفاده از admin-ajax.php
            data: {
                action: 'my_keyword_plugin_process',
                nonce: nonce,
                seed_keyword: seed_keyword
            },
            success: function(response) {
                if(response.success) {
                    $('#keyword-research-result').html('<pre>' + response.data + '</pre>');
                } else {
                    $('#keyword-research-result').html('<span class="error">' + response.data + '</span>');
                }
            }
        });
    });
});
