jQuery(document).ready(function ($) {
    $('#category-select').on('change', function () {
        const category = $(this).val();
        const page = 1;

        console.log('Sending AJAX request...'); // دیباگ

        $.ajax({
            url: courses_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'filter_courses',
                category: category,
                page: page,
                nonce: courses_ajax.nonce,
            },
            success: function (response) {
                console.log('AJAX response:', response); // دیباگ
                $('#courses-container').html(response);
            },
            error: function (error) {
                console.error('AJAX error:', error);
            },
        });
    });

    $(document).on('click', '.courses-pagination a', function (e) {
        e.preventDefault();
        const page = $(this).attr('href').match(/paged=(\d+)/)[1];

        $.ajax({
            url: courses_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'filter_courses',
                category: $('#category-select').val(),
                page: page,
                nonce: courses_ajax.nonce,
            },
            success: function (response) {
                $('#courses-container').html(response);
            },
            error: function (error) {
                console.error('AJAX error:', error);
            },
        });
    });
});