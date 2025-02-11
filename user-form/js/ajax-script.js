jQuery(document).ready(function ($) {
    $('#my-contact-form').on('submit', function (e) {
        e.preventDefault();

        // گرفتن داده‌های فرم
        const formData = $(this).serialize();

        console.log(formData); // بررسی کنید آیا ایمیل در داده‌های ارسال شده موجود است


        // ارسال AJAX
        $.ajax({
            url: ajax_object.ajax_url,
            type: 'POST',
            data: formData,
            success: function (response) {
                if (response.success) {
                    $('#response-message').html('<p style="color:green;">' + response.data.message + '</p>');
                    $('#my-contact-form')[0].reset();
                } else {
                    $('#response-message').html('<p style="color:red;">' + response.data.message + '</p>');
                }
            },
            error: function () {
                $('#response-message').html('<p style="color:red;">مشکلی در ارسال فرم پیش آمد.</p>');
            }
        });
    });
});
