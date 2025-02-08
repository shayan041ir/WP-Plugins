jQuery(document).ready(function($) {
    $('#keyword-research-form').on('submit', function(e) {
        e.preventDefault();
        var seed_keyword = $('#seed_keyword').val();
        var nonce = $('#my_keyword_plugin_nonce_field').val();

        // نمایش پیام در حال پردازش
        $('#keyword-research-result').html('<p>در حال پردازش...</p>');

        $.ajax({
            type: 'POST',
            url: myKeywordPlugin.ajaxurl, // آدرس admin-ajax.php از طریق wp_localize_script ارسال شده است
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
            },
            error: function(xhr, status, error) {
                $('#keyword-research-result').html('<span class="error">خطایی رخ داده است: ' + error + '</span>');
            }
        });
    });
});
