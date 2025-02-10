jQuery(document).ready(function ($) {
    $('#ajax-form').on('submit', function (e) {
        e.preventDefault(); // جلوگیری از رفرش صفحه

        let name = $('#name').val();

        $.ajax({
            type: 'POST',
            url: ajax_object.ajax_url, // URL ارسال درخواست AJAX
            data: {
                action: 'my_action', // نام اکشن تعریف شده در PHP
                security: ajax_object.security, // Nonce برای امنیت
                name: name, // داده ارسالی
            },
            success: function (response) {
                if (response.success) {
                    $('#ajax-response').text(response.data.message);
                } else {
                    $('#ajax-response').text('خطایی رخ داد.');
                }
            },
            error: function () {
                $('#ajax-response').text('ارتباط با سرور ناموفق بود.');
            },
        });
    });
});
