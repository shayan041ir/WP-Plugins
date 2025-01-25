<?php
/*
Plugin Name: ّFilter for Courses
Description: مدیریت دوره‌های آموزشی با Custom Post Types.
Version: 1.0
Author: Shayan Rezaei
*/


// نمایش فرم فیلتر دسته‌بندی‌ها و لیست دوره‌ها
add_shortcode('dynamic_course_filter', function () {
    // دریافت دسته‌بندی‌های مربوط به دوره‌ها
    $categories = get_terms(array(
        'taxonomy'   => 'course_category',
        'hide_empty' => true,
    ));

    if (is_wp_error($categories) || empty($categories)) {
        return '<p>هیچ دسته‌بندی‌ای یافت نشد.</p>';
    }

    ob_start(); ?>
    <div id="course-filter">
        <!-- فرم انتخاب دسته‌بندی -->
        <form id="category-filter-form">
            <?php wp_nonce_field('filter_courses_nonce', 'course_nonce'); ?>
            <select name="category" id="category-select">
                <option value="">همه دسته‌بندی‌ها</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo esc_attr($category->slug); ?>">
                        <?php echo esc_html($category->name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <!-- بخش نمایش دوره‌ها -->
        <div id="courses-container">
            <?php echo do_shortcode('[courses_list]'); ?>
        </div>
    </div>

    <!-- اسکریپت AJAX -->
    <script>
        document.getElementById('category-select').addEventListener('change', function() {
        const category = this.value;
        const nonce = document.getElementById('course_nonce').value;
        const data = new FormData();
        data.append('action', 'filter_courses');
        data.append('category', encodeURIComponent(category)); // Encoding دسته‌بندی
        data.append('course_nonce', nonce);

        console.log('Sending AJAX request with category:', category);

        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                body: data,
            })
            .then((response) => response.text())
            .then((data) => {
                console.log('Received response:', data); // دیباگ پاسخ
                document.getElementById('courses-container').innerHTML = data;
            })
            .catch((error) => console.error('AJAX error:', error));
        });
    </script>
<?php
    return ob_get_clean();
});

add_action('wp_ajax_filter_courses', 'filter_courses');
add_action('wp_ajax_nopriv_filter_courses', 'filter_courses');

function filter_courses()
{
    // بررسی nonce برای امنیت
    if (!isset($_POST['course_nonce']) || !wp_verify_nonce($_POST['course_nonce'], 'filter_courses_nonce')) {
        echo '<p>درخواست نامعتبر است.</p>';
        wp_die();
    }

    // دریافت دسته‌بندی و decode آن
    $category = isset($_POST['category']) ? urldecode(sanitize_text_field($_POST['category'])) : '';

    $args = array(
        'post_type'      => 'courses',
        'posts_per_page' => -1,
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
        wp_reset_postdata();
    } else {
        echo '<p>دوره‌ای یافت نشد.</p>';
    }

    wp_die();
}
