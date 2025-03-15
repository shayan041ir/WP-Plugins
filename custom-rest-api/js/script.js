jQuery(document).ready(function($) {
    $('#group-post-link-form').on('submit', function(e) {
        e.preventDefault();
        var formData = {
            action: 'process_group_post_link',
            count: $('#count').val(),
            group_name: $('#group_name').val(),
            post_link: $('#post_link').val()
        };
        $.post(ajaxurl, formData, function(response) {
            if (response.success) {
                alert(response.data);
            } else {
                alert(response.data);
            }
        });
    });
});