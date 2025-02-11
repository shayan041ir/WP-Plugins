//// filepath: /c:/xampp/htdocs/wordpress/wp-content/plugins/js/courses-script.js
jQuery(document).ready(function($){
    // رویداد تغییر در select دسته‌بندی
    $('#category-select').on('change', function(e){
         e.preventDefault();
         var category = $(this).val();
         
         $.ajax({
            url: courses_ajax.ajax_url,
            type: 'POST',
            data: {
                 action: 'filter_courses',
                 nonce: courses_ajax.nonce,
                 category: category,
                 page: 1
            },
            beforeSend: function(){
                 $('#courses-container').html('<p>در حال بارگذاری...</p>');
            },
            success: function(response){
                 if ( response.success ) {
                      $('#courses-container').html(response.data.html);
                 } else {
                      $('#courses-container').html('<p>خطا در دریافت داده‌ها.</p>');
                 }
            },
            error: function(){
                 $('#courses-container').html('<p>خطا در برقراری ارتباط.</p>');
            }
         });
    });
});