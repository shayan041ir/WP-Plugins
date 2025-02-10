<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// تعریف Shortcode برای نمایش فرم
function my_keyword_plugin_form_shortcode() {
    ob_start();
    ?>
    <form id="keyword-research-form" method="post">
        <?php wp_nonce_field( 'my_keyword_plugin_nonce', 'my_keyword_plugin_nonce_field' ); ?>
        <label for="seed_keyword">کلمه کلیدی اولیه:</label>
        <input type="text" name="seed_keyword" id="seed_keyword" required>
        <button type="submit">شروع تحقیق</button>
    </form>
    <div id="keyword-research-result"></div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'keyword_research_form', 'my_keyword_plugin_form_shortcode' );

// پردازش درخواست AJAX
function my_keyword_plugin_handle_ajax() {
    check_ajax_referer( 'my_keyword_plugin_nonce', 'nonce' );

    $seed_keyword = isset( $_POST['seed_keyword'] ) ? sanitize_text_field( $_POST['seed_keyword'] ) : '';
    if ( empty( $seed_keyword ) ) {
        wp_send_json_error( 'لطفاً کلمه کلیدی را وارد کنید.' );
    }

    // ساخت پرامپ برای ChatGPT
    $prompt = "بررسی و ارائه لیستی از کلمات کلیدی مرتبط برای: " . $seed_keyword;

    // فراخوانی کلاس API Handler
    $api_handler = new My_Keyword_Plugin_API_Handler();
    $result = $api_handler->get_keyword_research( $prompt );

    if ( is_wp_error( $result ) ) {
        wp_send_json_error( $result->get_error_message() );
    } else {
        wp_send_json_success( $result );
    }
}
add_action( 'wp_ajax_my_keyword_plugin_process', 'my_keyword_plugin_handle_ajax' );
add_action( 'wp_ajax_nopriv_my_keyword_plugin_process', 'my_keyword_plugin_handle_ajax' );
