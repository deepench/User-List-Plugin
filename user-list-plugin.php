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
if ( !defined( 'ABSPATH' ) ) {

    exit; // Exit if accessed directly.
}



    class UserList_Plugin
    {

        public function __construct() {
            $this->init();
        }
        /**
         * Hooks into actions and filters
         */
        public function init() {
            add_action( 'wp_enqueue_scripts', array( $this, 'ulp_script_enqueuer' ) );
            add_shortcode( 'ulp_user_detail', array( $this, 'ulp_user_list_detail' ) );
            add_action('wp_ajax_nopriv_load_table', array( $this, 'ulp_load_table_login' ) );
            add_action( 'wp_ajax_load_table', array( $this, 'ulp_load_table' ) );
        }
        /**
         * Including a shorcode List
         * and checking the user is admin 
         */
        public function ulp_user_list_detail() {
            ob_start();
            $user = wp_get_current_user();
            $allowed_roles = array( 'administrator' );

            if( array_intersect( $allowed_roles, $user->roles ) ) { 
                include  plugin_dir_path(__FILE__)."inc/ulp_table_info.php";
        } else {
                echo __('<p class="unauthorized-msg">You are not authorized to acess this page<p>','user-list-plugin');
        }
            return ob_get_clean();
        }
        /**
         * including the css nad jaavscript file for intialization
         */

        public function ulp_script_enqueuer()  {
            wp_register_script( 'ulp_plugin_script', plugin_dir_url(__FILE__) . "assets/js/main.js", array('jquery'), '1.0.0', true );
            wp_enqueue_style( 'ulp_plugin_css', plugin_dir_url(__FILE__) .
                "assets/css/style.css" );
            wp_localize_script( 'ulp_plugin_script', 'myAjax', array(
                'ajaxurl' => admin_url( 'admin-ajax.php' ),
                'ulp_script_nonce' => wp_create_nonce( 'ulp-nonce' ),
            ));
            wp_enqueue_script( 'ulp_plugin_script' );
        }

        /**
         * Processing the userlist data
         */
        public function ulp_load_table() {
            global $wpdb;

            // Checking the nonce for the security purpose
            if ( !check_ajax_referer( 'ulp-nonce', 'security', false ) ) {
                wp_send_json_error(
                    array(
                        'message' => __('Nonce error, please reload.', 'user-list-plugin'),
                    )
                );
               
            }
            
            // Sanitizing the fileds to avoid SQL injections
            $role = sanitize_text_field( isset( $_POST['ulp_role'] ) ? $_POST['ulp_role'] : '' ); //The value of the role we get.

            $order = sanitize_text_field( isset( $_POST['ulp_order'] ) ? $_POST['ulp_order'] : '' ); //the value of the display order.

            $ulp_userorder = sanitize_text_field( isset( $_POST['ulp_userorder'] ) ? $_POST['ulp_userorder'] : '' ); //the value of the user order

            $record_per_page = 10; //Numbr of items to display per page

            $page = '';

            if ( isset( $_POST["page"] ) ) {
                $page = intval($_POST["page"]);
            } else {
                $page = 1;
            }
            
            $start_from = ( $page - 1 ) * $record_per_page;

        //Retrieve all the users
            $rows = $wpdb->get_results($wpdb->prepare(
                    'SELECT wp_users.ID, wp_users.user_nicename, wp_users.display_name,"' . $role . '" as meta_value FROM wp_users INNER JOIN wp_usermeta  ON wp_users.ID = wp_usermeta.user_id 
                    WHERE wp_usermeta.meta_key = "wp_capabilities" AND wp_usermeta.meta_value LIKE "%' . $role . '%" ORDER BY wp_users.display_name ' . $order . ', wp_users . user_nicename ' . $ulp_userorder . '  LIMIT ' . $start_from . ',' . $record_per_page . ' ' ) );
          
                    $output = "";
                    $output =  '<table>';
                    $output .=   '<thead>
                    <tr>
                        <th> ' . esc_html__( "S.N.", "user-list-plugin" ) . '</th>
                        <th> ' . esc_html__( "User Name", "user-list-plugin" ) . '  </th>
                        <th> ' . esc_html__( "Display Name", "user-list-plugin" ) . '  </th>
                        <th> ' . esc_html__( "Role", "user-list-plugin" ) . '  </th>
                    </tr>
                </thead>';
          
                $output .= '<tbody>';
           
                $i=1;
                foreach ( $rows as $key => $row ) {
                        $user_info = get_userdata( $row->ID );
                        $user_roles = implode( ', ', $user_info->roles );
                        $output  .='<tr>
                                        <td> ' . $i++ . '</td>
                                        <td> ' . $row->user_nicename . '</td>
                                        <td>' . $row->display_name . '</td>
                                        <td> ' . $user_roles . '</td>
                                    </tr>';
                    }
                $output .= '</tbody>';
                $output .= '</table>';
            
                $page_query =
                $wpdb->get_results($wpdb->prepare( 'SELECT wp_users.ID, wp_users.user_nicename, wp_users. display_name,"' . $role . '" as meta_value 
                FROM wp_users INNER JOIN wp_usermeta 
                ON wp_users.ID = wp_usermeta.user_id 
                WHERE wp_usermeta.meta_key = "wp_capabilities" 
                AND wp_usermeta.meta_value LIKE "%' . $role . '%" ORDER BY wp_users.display_name ' . $order . ' ' ) );
                    
                if ( count( $page_query ) == 0 ) {
                    echo __('Sorry,No Data Found For This User','user-list-plugin'); //If the query returns nothing we thrown a error message
                    die();
                } else {
                    $rowcount = count( $page_query );
                }

                 $total_pages = ceil( $rowcount / $record_per_page );

                 $output .= '<div id="pagination">';
                 for ( $i = 1; $i <= $total_pages; $i++ ) {
                    $output .= '<a  class="active" href="" id=' . $i . '>' . $i . '</a>';
                    }
                $output .= '</div>';

            wp_send_json( $output );
        }

        public function ulp_load_table_login()  {
        //  Unauthourized Users
            echo __('Hello Please Login To See the Information','user-list-plugin');
            die();
        }
    }


if ( class_exists( 'UserList_Plugin' ) ) {
$ulpPlugin = new UserList_Plugin();
}