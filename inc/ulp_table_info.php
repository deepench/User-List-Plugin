<!-- User List Table -->

<!-- Form for the Filter and Ordering -->
<form action="post" id="filter_table">

    <?php global $wp_roles; ?>
    <label for=""><?php echo __( 'Filter Role :', 'user-list-plugin' ) ?></label>
    <select name="ulp_role" id="ulp_role">
        <option value="">Select the Role</option>
        <?php foreach ( $wp_roles->roles as $key => $value ) : ?>
            <option value="<?php echo $key ?>"><?php echo $value['name']; ?></option>
        <?php endforeach; ?>
    </select>

    <label for=""><?php echo __( 'Display Filter', 'user-list-plugin' ) ?></label>
    <select name="ulp_order" id="ulp_order">
        <option value="">select the Order</option>
        <option value="asc">Ascending</option>
        <option value="desc">Descending</option>
    </select>

    <label for=""><?php echo __( 'Username Filter', 'user-list-plugin' ) ?></label>
    <select name="ulp_userorder" id="ulp_userorder">
        <option value="">select the Order</option>
        <option value="asc">Ascending</option>
        <option value="desc">Descending</option>
    </select>

    <input type="submit" value="<?php echo __('Filter', 'user-list-plugin') ?>" id="submit" class="button">
</form>
<br>
<br>

<table id="ulp_list_table">
</table>