(function($) {
    // Save a reference to the original Quick Edit function
    var $wp_inline_edit = inlineEditPost.edit;
    
    // Overwrite the inlineEditPost.edit function with our own
    inlineEditPost.edit = function(post_id) {
        // Call the original edit function first
        $wp_inline_edit.apply(this, arguments);
        
        // Now, add our custom field population
        // Get the post ID being edited
        var id = 0;
        if (typeof post_id === 'object') {
            id = this.getId(post_id);  // if post_id is an event, extract the actual ID
        } else {
            id = post_id;
        }
        
        if (!id) return;  // safety check

        // Define the specific rows
        var $editRow = $('#edit-' + id);    // Quick Edit form row
        var $postRow = $('#post-' + id);    // the post's row in the table
        
        // Get the data from the existing columns
        var lessonOrder = $('.column-lesson_order', $postRow).text().trim();
        
        // Populate the Quick Edit fields with this data
        $(':input[name="lesson_order"]', $editRow).val(lessonOrder);
    };
})(jQuery);