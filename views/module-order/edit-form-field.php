<?php
    /**
     * @param int $currentOrder Current order of the module
     */
?>

<tr class="form-field">
    <th scope="row"><label for="module_order"><?php _e('Module Order', 'courses-and-lessons'); ?></label></th>
    <td>
        <input name="module_order" id="module_order" type="number" value="<?php echo $currentOrder ?>">
    </td>
</tr>