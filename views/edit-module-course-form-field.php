<?php
    /**
     * @param array $courses Array of courses
     * @param int $currentCourse Id of the currently selected course
     */
?>

<tr class="form-field">
    <th scope="row"><label for="course"><?php _e('Course', 'courses-and-lessons'); ?></label></th>
    <td>
        <select name="course" id="course">
            <option value=""><?php _e('Select a Course', 'courses-and-lessons'); ?></option>
            <?php foreach ($courses as $course): ?>
                <option value="<?php echo esc_attr($course->term_id); ?>" 
                    <?php selected($currentCourse, $course->term_id); ?>>
                    <?php echo esc_html($course->name); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p class="description"><?php _e('Select the course this module belongs to.', 'courses-and-lessons'); ?></p>
    </td>
</tr>