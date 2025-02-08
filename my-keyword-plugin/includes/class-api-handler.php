<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class My_Keyword_Plugin_API_Handler {

    private $api_key;
    private $api_url = 'https://api.openai.com/v1/chat/completions';

    public function __construct() {
        $this->api_key = get_option( 'my_keyword_plugin_api_key', '' );
    }

    public function get_keyword_research( $prompt ) {
        if ( empty( $this->api_key ) ) {
            return new WP_Error( 'no_api_key', 'API Key تنظیم نشده است.' );
        }

        $body = array(
            'model'    => 'gpt-3.5-turbo', // یا مدلی که ترجیح می‌دهید
            'messages' => array(
                array( 'role' => 'system', 'content' => 'You are a helpful assistant specialized in SEO keyword research.' ),
                array( 'role' => 'user', 'content' => $prompt )
            ),
            'temperature' => 0.7,
            'max_tokens'  => 150
        );

        $args = array(
            'headers' => array(
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $this->api_key,
            ),
            'body'    => json_encode( $body ),
            'timeout' => 60,
        );

        $response = wp_remote_post( $this->api_url, $args );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $response_code = wp_remote_retrieve_response_code( $response );
        $response_body = wp_remote_retrieve_body( $response );

        if ( $response_code !== 200 ) {
            return new WP_Error( 'api_error', 'خطایی در ارتباط با API رخ داده است. کد خطا: ' . $response_code );
        }

        $data = json_decode( $response_body, true );
        if ( isset( $data['choices'][0]['message']['content'] ) ) {
            return $data['choices'][0]['message']['content'];
        }

        return new WP_Error( 'no_response', 'پاسخی دریافت نشد.' );
    }
}
