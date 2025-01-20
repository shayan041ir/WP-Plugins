jQuery(document).ready(function ($) {
    $('#submit-rating').on('click', function (e) {
        e.preventDefault();

        var rating = $('#rating').val(); // دریافت مقدار امتیاز
        var postID = ratingAjax.post_id; // آیدی نوشته

        $.ajax({
            url: ratingAjax.ajax_url,
            type: 'POST',
            data: {
                action: 'save_rating', // نام اکشن
                rating: rating,
                post_id: postID,
                nonce: ratingAjax.nonce
            },
            success: function (response) {
                $('#rating-message').html(response.data.message);
            },
            error: function () {
                $('#rating-message').html('خطایی رخ داده است.');
            }
        });
    });
});
