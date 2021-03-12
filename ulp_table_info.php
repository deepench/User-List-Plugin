<!-- User List Table -->
<?php
global $wpdb;
$rows = $wpdb->get_results("SELECT wp_users.ID, wp_users.user_nicename, wp_users.display_name, wp_usermeta.meta_value 
 FROM wp_users 
 JOIN wp_usermeta ON wp_users.ID = wp_usermeta.user_id 
 WHERE wp_usermeta.meta_key = 'wp_capabilities'
LIMIT 10");

?>
<form action="post" id="filter_table">
    <?php global $wp_roles; ?>
    <label for=""><?php echo __('Filter Role :', 'user-list-plugin') ?></label>
    <select name="ulp_role" id="ulp_role">
        <option value="">Select the Role</option>
        <?php foreach ($wp_roles->roles as $key => $value) : ?>
            <option value="<?php echo $key ?>"><?php echo $value['name']; ?></option>
        <?php endforeach; ?>
    </select>
    <select name="ulp_order" id="ulp_order">
        <option value="">select the Order</option>
        <option value="asc">Ascending</option>
        <option value="desc">Descending</option>
    </select>
    <input type="submit" value="Filter">
</form>
<br>
<br>
<table id="ulp_list_table">
</table>