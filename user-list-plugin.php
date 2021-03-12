<?php
/*
 * Plugin Name: User List Plugin
 * Plugin URI: https://demowordpress/plugins/user-list-plugin/user-list-plugin.php
 * Description:All about users
 * Version: 1.0.0
 * Author: Deependra
 * Author URI: https://deepench.com
 * License:GPLv2 or later
 * Text Domain:user-list-plugin
 * Domain Path: /languages
*/
if (!defined('ABSPATH')) {

    exit; // Exit if accessed directly.
}


if (!class_exists('UserList_Plugin')) :

    class UserList_Plugin
    {

        public function __construct()
        {
            $this->init();
        }
        /**
         * Hooks into actions and filters
         */
        public function init()
        {
            add_action('wp_enqueue_scripts', array($this, 'ulp_script_enqueuer'));
            add_shortcode('ulp_user_detail', array($this, 'ulp_user_list_detail'));
            // add_action('wp_ajax_nopriv_filter', array($this, 'filter_ajax'));
            add_action('wp_ajax_load_table', array($this, 'ulp_load_table'));
            add_action('wp_ajax_filter', array($this, 'filter_ajax'));
        }


        /**
         * Including a shorcode List
         */
        public function ulp_user_list_detail()
        {;
            ob_start();
            include('ulp_table_info.php');
            return ob_get_clean();
        }

        public function ulp_script_enqueuer()
        {
            wp_register_script('ulp_plugin_script', plugin_dir_url(__FILE__) . "assets/js/main.js", array('jquery'), '1.0.0', true);
            wp_enqueue_style('ulp_plugin_css', plugin_dir_url(__FILE__) .
                "assets/css/style.css");
            wp_localize_script('ulp_plugin_script', 'myAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
            wp_enqueue_script('jquery');
            wp_enqueue_script('ulp_plugin_script');
        }
        public function ulp_load_table()
        {
            global $wpdb;
            $rows = $wpdb->get_results("SELECT  wp_users.ID, wp_users.user_nicename, wp_users.display_name, wp_usermeta.meta_value 
            FROM wp_users 
            JOIN wp_usermeta ON wp_users.ID = wp_usermeta.user_id 
            WHERE wp_usermeta.meta_key = 'wp_capabilities' LIMIT {$offset},{$limit_per_page}
            ");
            $output = "";
            $output =  '<table>';
            $output .=   '<thead>
                    <tr>
                        <th> ' . esc_html__("S.N.", "user-list-plugin") . '</th>
                        <th> ' . esc_html__("User Name", "user-list-plugin") . '  </th>
                        <th> ' . esc_html__("Display Name", "user-list-plugin") . '  </th>
                        <th> ' . esc_html__("Role", "user-list-plugin") . '  </th>
                    </tr>
                </thead>';
            $output .= '<tbody>';
            foreach ($rows as $row) {
                $user_info = get_userdata($row->ID);
                $user_roles = implode(', ', $user_info->roles);
                $output  .= '<tr>
                    <td> ' . $row->ID . '</td>
                    <td> ' . $row->user_nicename . '</td>
                    <td>' . $row->display_name . '</td>
                   <td> ' . $user_roles . '</td>
                </tr>';
            }
            $output .= '</tbody>';
            $output .= '</table>';

            $sql_total = $wpdb->get_results("SELECT wp_users.ID, wp_users.user_nicename, wp_users.display_name, wp_usermeta.meta_value 
            FROM wp_users 
            JOIN wp_usermeta ON wp_users.ID = wp_usermeta.user_id 
            WHERE wp_usermeta.meta_key = 'wp_capabilities'
            ");
            $total_records = $wpdb->num_rows;
            $total_pages = ceil($total_records / $limit_per_page);

            for ($i = 1; $i <= $total_pages; $i++) {
                $output .= '<div id="pagination">';
                $output .= '<a  class="active" href="" id=' . $i . '>' . $i . '</a>';
                $output .= '</div>';
            }
            wp_send_json($output);
        }

        public function filter_ajax()
        {

            // if (!check_ajax_referer('ulp_script_form_nonce', 'security', false)) {
            //     wp_send_json_error(
            //         array(
            //             'message' => __('Nonce error, please reload.', 'user-list-plugin'),
            //         )
            //     );
            // }
            global $wpdb;
            $role = sanitize_text_field(isset($_POST['ulp_role']) ? $_POST['ulp_role'] : '');
            $order = sanitize_text_field(isset($_POST['ulp_role']) ? $_POST['ulp_role'] : '');

            // if ($order == 'asc') {
            //     $order = 'asc';
            // } else {
            //     $order = 'desc';
            // }

            $rows = $wpdb->get_results("SELECT wp_users.ID, wp_users.user_nicename, wp_users.display_name, wp_usermeta.meta_value 
                FROM wp_users 
                JOIN wp_usermeta ON wp_users.ID = wp_usermeta.user_id 
                WHERE wp_usermeta.meta_key = 'wp_capabilities' AND display_name='$role' ORDER BY display_name '$order'");
            $output = "";
            $output =  '<table>';
            $output .=   '<thead>
                        <tr>
                            <th> ' . esc_html__("S.N.", "user-list-plugin") . '</th>
                            <th> ' . esc_html__("User Name", "user-list-plugin") . '  </th>
                            <th> ' . esc_html__("Display Name", "user-list-plugin") . '  </th>
                            <th> ' . esc_html__("Role", "user-list-plugin") . '  </th>
                        </tr>
                    </thead>';
            $output .= '<tbody>';
            foreach ($rows as $row) {
                $user_info = get_userdata($row->ID);
                $user_roles = implode(', ', $user_info->roles);
                $output  .= '<tr>
                        <td> ' . $row->ID . '</td>
                        <td> ' . $row->user_nicename . '</td>
                        <td>' . $row->display_name . '</td>
                       <td> ' . $user_roles . '</td>
                    </tr>';
            }
            $output .= '</tbody>';
            $output .= '</table>';
            wp_send_json($output);
        }
    }

endif;

$ulpPlugin = new UserList_Plugin();


SELECT wp_users.user_login, wp_users.display_name,  "'.$args['role'].'" as meta_value 
		FROM wp_users INNER JOIN wp_usermeta 
		ON wp_users.ID = wp_usermeta.user_id 
		WHERE wp_usermeta.meta_key = "wp_capabilities" 
		AND wp_usermeta.meta_value LIKE "%'.$args['role'].'%"