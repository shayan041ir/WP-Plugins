function my_keyword_plugin_handle_ajax() {
    check_ajax_referer( 'my_keyword_plugin_nonce', 'nonce' );

    $seed_keyword = isset( $_POST['seed_keyword'] ) ? sanitize_text_field( $_POST['seed_keyword'] ) : '';
    if ( empty( $seed_keyword ) ) {
        wp_send_json_error( 'لطفاً کلمه کلیدی را وارد کنید.' );
    }

    // ساخت پرامپ برای ChatGPT بر اساس اطلاعات ورودی
    $prompt = "بررسی و ارائه لیستی از کلمات کلیدی مرتبط برای: " . $seed_keyword;

    // فراخوانی کلاس API Handler برای ارتباط با ChatGPT
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
