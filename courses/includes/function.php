<?php
//// filepath: /c:/xampp/htdocs/wordpress/wp-content/plugins/includes/function.php

// اضافه کردن استایل‌ها و فایل‌های جاوااسکریپت به همراه nonce برای استفاده از AJAX
function enqueue_courses_scripts() {
    wp_enqueue_style('courses-style', plugins_url('css/courses-style.css', __FILE__));
    wp_enqueue_script('courses-script', plugins_url('js/courses-script.js', dirname(__FILE__)), array('jquery'), null, true);
    wp_localize_script('courses-script', 'courses_ajax', array(
         'ajax_url' => admin_url('admin-ajax.php'),
         'nonce'    => wp_create_nonce('filter_courses_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_courses_scripts');

// Ajax handler جهت فیلتر کردن دوره‌ها
function filter_courses() {
    if ( ! isset($_POST['nonce']) || ! wp_verify_nonce($_POST['nonce'], 'filter_courses_nonce') ) {
        wp_send_json_error(array('message' => 'درخواست نامعتبر است.'));
    }
    $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '';
    $page     = isset($_POST['page']) ? intval($_POST['page']) : 1;

    $args = array(
        'post_type'      => 'courses',
        'posts_per_page' => 5,
        'paged'          => $page,
        'orderby'        => 'title',
        'order'          => 'ASC'
    );
    if ( ! empty($category) ) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'course_category',
                'field'    => 'slug',
                'terms'    => $category,
            )
        );
    }
    $query = new WP_Query($args);

    ob_start();
    if ( $query->have_posts() ) {
        echo '<div class="courses-list">';
        while ( $query->have_posts() ) {
            $query->the_post();
            echo '<div class="course-item">';
            echo '<h3>' . esc_html(get_the_title()) . '</h3>';
            echo '<a href="' . esc_url(get_permalink()) . '">مشاهده جزئیات</a>';
            echo '</div>';
        }
        echo '</div>';

        echo '<div class="courses-pagination">';
        echo paginate_links(array(
            'total'     => $query->max_num_pages,
            'current'   => $page,
            'prev_text' => 'قبلی',
            'next_text' => 'بعدی'
        ));
        echo '</div>';
    } else {
        echo '<p>دوره‌ای یافت نشد.</p>';
    }
    wp_reset_postdata();
    $html = ob_get_clean();
    wp_send_json_success(array('html' => $html));
}
add_action('wp_ajax_filter_courses', 'filter_courses');
add_action('wp_ajax_nopriv_filter_courses', 'filter_courses');

// Shortcode نمایش فیلتر دسته‌بندی و محفظه نمایش دوره‌ها
add_shortcode('dynamic_course_filter', function () {
    $categories = get_terms(array(
        'taxonomy'   => 'course_category',
        'hide_empty' => true,
    ));

    if ( is_wp_error($categories) || empty($categories) ) {
        return '<p>هیچ دسته‌بندی‌ای یافت نشد.</p>';
    }

    ob_start(); ?>
    <div id="course-filter">
         <select name="category" id="category-select">
            <option value="">همه دسته‌بندی‌ها</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo esc_attr($category->slug); ?>">
                    <?php echo esc_html($category->name); ?>
                </option>
            <?php endforeach; ?>
         </select>
    </div>
    <div id="courses-container">
         <?php
         // بارگذاری اولیه دوره‌ها با استفاده از شورت‌کد موجود
         echo do_shortcode('[courses_list]');
         ?>
    </div>
    <?php
    return ob_get_clean();
});