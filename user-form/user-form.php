<?php
/*
Plugin Name: User Form
Description: A form for users to submit their information
Version: 1.0
Author: shayan rezayi
*/

function my_enqueue_scripts()
{
    wp_enqueue_script('ajax-script', plugin_dir_url(__FILE__) . 'assets/js/ajax-script.js', array('jquery'), null, true);

    wp_localize_script('ajax-script', 'ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'security' => wp_create_nonce('my_nonce_action'), // نانس امنیتی
    ));
}
add_action('wp_enqueue_scripts', 'my_enqueue_scripts');



function my_contact_form_shortcode()
{
    ob_start();
?>
    <style>
        #my-contact-form {
            max-width: 400px;
            margin: 0 auto;
        }

        #my-contact-form label {
            display: block;
            margin-top: 10px;
        }

        #my-contact-form input,
        #my-contact-form textarea {
            width: 100%;
            padding: 5px;
            margin-top: 5px;
        }

        #my-contact-form button {
            padding: 5px 10px;
            margin-top: 10px;
            background-color: #0073aa;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        #response-message {
            margin-top: 10px;
            padding: 10px;
            border-radius: 5px;
        }
    </style>
    <form id="my-contact-form" action="<?php echo admin_url('admin-ajax.php'); ?>" method="post">
        <label for="name">نام:</label>
        <input type="text" id="name" name="name" required>

        <label for="email">ایمیل:</label>
        <input type="email" id="email" name="email" required>

        <label for="message">پیام:</label>
        <textarea id="message" name="message" required></textarea>

        <input type="hidden" name="action" value="my_contact_form">
        <input type="hidden" name="security" value="<?php echo wp_create_nonce('my_contact_form_nonce'); ?>">

        <button type="submit">ارسال</button>
        <div id="response-message"></div>
    </form>
    <!-- <script>
        jQuery(document).ready(function($) {
            $('#my-contact-form').submit(function(e) {
                e.preventDefault();

                var formData = $(this).serialize();

                $.ajax({
                    type: 'post',
                    url: ajax_object.ajax_url,
                    data: formData,
                    beforeSend: function() {
                        $('#response-message').html('در حال ارسال...');
                    },
                    success: function(response) {
                        $('#response-message').html(response.data.message);
                    },
                    error: function() {
                        $('#response-message').html('خطا در ارسال فرم.');
                    }
                });
            });
        });
    </script> -->

<?php
    return ob_get_clean();
}
add_shortcode('my_contact_form', 'my_contact_form_shortcode');




// function handle_my_contact_form() {
//     check_ajax_referer('my_nonce_action', 'security'); // بررسی نانس

//     $name = sanitize_text_field($_POST['name']);
//     $email = sanitize_email($_POST['email']);
//     $message = sanitize_textarea_field($_POST['message']);

//     // پاسخ AJAX
//     wp_send_json_success(array(
//         'message' => 'Form submitted successfully!',
//     ));
// }
// add_action('wp_ajax_my_contact_form', 'handle_my_contact_form');
// add_action('wp_ajax_nopriv_my_contact_form', 'handle_my_contact_form'); // برای کاربران غیر لاگین

//for ajax
function my_contact_form_handler()
{
    // بررسی امنیت
    check_ajax_referer('my_contact_form_nonce', 'security');

    // گرفتن داده‌ها
    $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $message = isset($_POST['message']) ? sanitize_textarea_field($_POST['message']) : '';

    // اعتبارسنجی
    if (empty($name) || empty($email) || empty($message)) {
        wp_send_json_error(array('message' => 'لطفاً تمام فیلدها را پر کنید.'));
    }

    // (اختیاری) ارسال ایمیل
    $to = get_option('admin_email');
    $subject = "پیام جدید از $name";
    $body = "نام: $name\nایمیل: $email\nپیام:\n$message";
    $headers = array('Content-Type: text/plain; charset=UTF-8');


    $email = 'test@example.com';
    if (!is_email($email)) {
        wp_send_json_error(array('message' => 'ایمیل نامعتبر است.'));
    } else {
        wp_send_json_success(array('message' => 'ایمیل معتبر است.'));
    }


    // ارسال ایمیل و نمایش پیام موفقیت‌آمیز یا خطا به کاربر   
    // if (wp_mail($to, $subject, $body, $headers)) {
    //     wp_send_json_success(array('message' => 'پیام شما با موفقیت ارسال شد.'));
    // } else {
    //     wp_send_json_error(array('message' => 'خطا در ارسال ایمیل. لطفاً دوباره تلاش کنید.'));
    // }

}
add_action('wp_ajax_my_contact_form', 'my_contact_form_handler');
add_action('wp_ajax_nopriv_my_contact_form', 'my_contact_form_handler');
