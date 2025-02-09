<?php
// اضافه کردن استایل‌ها
function enqueue_courses_scripts() {
    wp_enqueue_style('courses-style', plugins_url('css/courses-style.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'enqueue_courses_scripts');

// نمایش فرم فیلتر دسته‌بندی‌ها و لیست دوره‌ها
add_shortcode('dynamic_course_filter', function () {
    $categories = get_terms(array(
        'taxonomy'   => 'course_category',
        'hide_empty' => true,
    ));

    if (is_wp_error($categories) || empty($categories)) {
        return '<p>هیچ دسته‌بندی‌ای یافت نشد.</p>';
    }

    // دریافت دسته‌بندی انتخاب‌شده از پارامتر GET
    $selected_category = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : '';

    ob_start(); ?>
    <div id="course-filter">
        <form id="category-filter-form" method="GET" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
            <select name="category" id="category-select">
                <option value="">همه دسته‌بندی‌ها</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo esc_attr($category->slug); ?>" <?php selected($selected_category, $category->slug); ?>>
                        <?php echo esc_html($category->name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">فیلتر</button>
        </form>

        <div id="courses-container">
            <?php echo do_shortcode('[courses_list category="' . $selected_category . '"]'); ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
});
function filter_courses() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'filter_courses_nonce')) {
        echo '<p>درخواست نامعتبر است.</p>';
        wp_die();
    }

    $category = isset($_POST['category']) ? urldecode(sanitize_text_field($_POST['category'])) : '';
    $page     = isset($_POST['page']) ? intval($_POST['page']) : 1;

    $args = array(
        'post_type'      => 'courses',
        'posts_per_page' => 5,
        'paged'          => $page,
        'orderby'        => 'title',
        'order'          => 'ASC',
    );

    if (!empty($category)) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'course_category',
                'field'    => 'slug',
                'terms'    => $category,
            ),
        );
    }

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            echo '<div class="course-item">';
            echo '<h3>' . esc_html(get_the_title()) . '</h3>';
            echo '<a href="' . esc_url(get_permalink()) . '">مشاهده جزئیات</a>';
            echo '</div>';
        }

        echo '<div class="courses-pagination">';
        echo paginate_links(array(
            'total'     => $query->max_num_pages,
            'current'   => $page,
            'prev_text' => 'قبلی',
            'next_text' => 'بعدی',
        ));
        echo '</div>';
    } else {
        echo '<p>دوره‌ای یافت نشد.</p>';
    }

    wp_reset_postdata();
    wp_die();
}