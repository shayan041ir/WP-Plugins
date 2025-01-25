<?php
// نمایش فرم فیلتر دسته‌بندی‌ها و لیست دوره‌ها
add_shortcode('dynamic_course_filter', function () {
    // دریافت دسته‌بندی‌های مربوط به دوره‌ها
    $categories = get_terms(array(
        'taxonomy'   => 'course_category',
        'hide_empty' => true,
    ));

    ob_start(); ?>
    <div id="course-filter">
        <!-- فرم انتخاب دسته‌بندی -->
        <form id="category-filter-form">
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
            console.log('Selected category:', category); // بررسی مقدار
            const data = new FormData();
            data.append('action', 'filter_courses');
            data.append('category', category);

            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                    method: 'POST',
                    body: data
                })
                .then(response => response.text())
                .then(data => {
                    document.getElementById('courses-container').innerHTML = data;
                });
        });
    </script>
<?php
    return ob_get_clean();
});
add_action('wp_ajax_filter_courses', 'filter_courses');
add_action('wp_ajax_nopriv_filter_courses', 'filter_courses');

function filter_courses()
{

    $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '';

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
    if (empty($category)) {
        echo '<p>دسته‌بندی ارسال نشده است.</p>';
        wp_die();
    }

    $query = new WP_Query($args);


    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            echo '<div class="course-item">';
            echo '<h3>' . esc_html(get_the_title()) . '</h3>';
            echo '<a href="' . get_permalink() . '">مشاهده جزئیات</a>';
            echo '</div>';
        }
    } else {
        echo '<p>دوره‌ای یافت نشد.</p>';
    }
    echo 'AJAX در حال اجرا است'; // برای اطمینان از عملکرد

    wp_die(); // پایان پردازش AJAX
}
