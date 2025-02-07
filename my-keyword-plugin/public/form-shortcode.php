<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function my_keyword_plugin_form_shortcode() {
    ob_start();
    ?>
    <form id="keyword-research-form" method="post">
        <?php wp_nonce_field( 'my_keyword_plugin_nonce', 'my_keyword_plugin_nonce_field' ); ?>
        <label for="seed_keyword">کلمه کلیدی اولیه:</label>
        <input type="text" name="seed_keyword" id="seed_keyword" required>
        <!-- می‌تونی فیلدهای بیشتری اضافه کنی -->
        <button type="submit">شروع تحقیق</button>
    </form>
    <div id="keyword-research-result"></div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'keyword_research_form', 'my_keyword_plugin_form_shortcode' );
