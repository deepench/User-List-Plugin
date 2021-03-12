<!-- User List Table -->
<?php
global $wpdb;
$rows = $wpdb->get_results("SELECT wp_users.ID, wp_users.user_nicename, wp_users.display_name, wp_usermeta.meta_value 
 FROM wp_users 
 JOIN wp_usermeta ON wp_users.ID = wp_usermeta.user_id 
 WHERE wp_usermeta.meta_key = 'wp_capabilities'
LIMIT 10");
<<<<<<< HEAD
?>

<!-- Form for the Filter and Ordering -->
=======

?>
>>>>>>> b9da7b76938cd905f8c31389e84eaa885ed83a3b
<form action="post" id="filter_table">
    <?php global $wp_roles; ?>
    <label for=""><?php echo __('Filter Role :', 'user-list-plugin') ?></label>
    <select name="ulp_role" id="ulp_role">
        <option value="">Select the Role</option>
<<<<<<< HEAD
        <?php foreach ( $wp_roles->roles as $key => $value ) : ?>
            <option value="<?php echo $key ?>"><?php echo $value['name']; ?></option>
        <?php endforeach; ?>
    </select>
    <label for=""><?php echo __( 'Display Filter', 'user-list-plugin' ) ?></label>
=======
        <?php foreach ($wp_roles->roles as $key => $value) : ?>
            <option value="<?php echo $key ?>"><?php echo $value['name']; ?></option>
        <?php endforeach; ?>
    </select>
>>>>>>> b9da7b76938cd905f8c31389e84eaa885ed83a3b
    <select name="ulp_order" id="ulp_order">
        <option value="">select the Order</option>
        <option value="asc">Ascending</option>
        <option value="desc">Descending</option>
    </select>
<<<<<<< HEAD
    <label for=""><?php echo __( 'Username Filter', 'user-list-plugin' ) ?></label>
    <select name="ulp_userorder" id="ulp_userorder">
        <option value="">select the Order</option>
        <option value="asc">Ascending</option>
        <option value="desc">Descending</option>
    </select>
    <input type="submit" value="<?php echo __('Filter', 'user-list-plugin') ?>" id="submit" class="button">
=======
    <input type="submit" value="Filter">
>>>>>>> b9da7b76938cd905f8c31389e84eaa885ed83a3b
</form>
<br>
<br>
<table id="ulp_list_table">
</table>