<?php
    /**
     * @param array $courses Array of courses
     */
?>

<div class="form-field">

    <label for="mocule_course">
        <?php _e('Course', 'courses-and-lessons'); ?>
    </label>

    <select name="mocule_course" id="mocule_course">
        <option value=""><?php _e('Select a Course', 'courses-and-lessons'); ?></option>
        <?php foreach ($courses as $course): ?>
            <option value="<?php echo esc_attr($course->term_id); ?>">
                <?php echo esc_html($course->name); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <p class="description">
        <?php _e('Select the course this module belongs to.', 'courses-and-lessons'); ?>
    </p>

</div>