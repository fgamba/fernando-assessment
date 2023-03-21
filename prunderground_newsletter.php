<?php
/**
* Plugin Name: PR Underground Newsletter
* Plugin URI: https://www.prunderground.com/
* Description: Test plugin.
* Version: 0.1
* Author: Fernando Gamba
* Author URI: https://www.linkedin.com/in/fernando-gamba-566151b/
**/


register_activation_hook( __FILE__, 'pr_install' );

function pr_install() {
	global $wpdb;
	
	$table_name = $wpdb->prefix . 'prunderground_newsletter';
	
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		name tinytext NOT NULL,
		email text NOT NULL,
		ipaddress varchar(20) DEFAULT '' NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );

}


function newsletter_shortcode() {
  global $wpdb;
  $ipaddress = $_SERVER['REMOTE_ADDR'];
  $table_name = $wpdb->prefix . 'prunderground_newsletter';
  
  $sql = "SELECT * FROM $table_name WHERE ipaddress = '".$ipaddress."'";
  $result = $wpdb->query($sql);
  $html = "";
  if((int)$result == 0) {
    $html = '<div class="news-container">';
    $html .= '<div class="news-form-container">';
    $html .= '<h3><span>PR</span>UNDERGROUND NEWSLETTER</h3>';
    $html .= '<form action="" method="post" name="news_form" class="news-form" >';
    $html .= '<input type="text" name="name" id="name" placeholder="Your Name *"/>';
    $html .= '<span class="name error-class"></span>';
    $html .= '<input type="email" name="email" id="email" placeholder="Email Address *"/>';
    $html .= '<span class="email error-class"></span>';
    $html .= '<input type="submit" value="SIGNUP"/>';
    $html .= '<div class="success_msg">You have been successfully subscribed, thank you!</div>';
    $html .= '<div class="error_msg">Something went wrong, please try again</div>';
    $html .= '</form>';
    $html .= '</div>';
    $html .= '</div>';
  }
  return $html;
}
add_shortcode('pr-newsletter', 'newsletter_shortcode');

wp_register_style('pr_stylesheets', plugins_url('pr-newsletter.css',__FILE__ ));
wp_enqueue_style('pr_stylesheets');

function plugin_scripts() {
    
    wp_register_script('jquery', 'https://code.jquery.com/jquery-3.6.4.min.js');
    wp_enqueue_script('jquery');
    wp_register_script('pr_script', plugins_url('pr-newsletter.js',__FILE__ ));
    wp_enqueue_script('pr_script');
    wp_localize_script( 'pr_script', 'ajax_var', array(
        'url'    => admin_url( 'admin-ajax.php' ),
        'nonce'  => wp_create_nonce( 'pr-ajax-nonce' )
    ) );
}

add_action( 'wp_enqueue_scripts', 'plugin_scripts' );
add_action( 'wp_ajax_set_form', 'set_form' );
add_action( 'wp_ajax_nopriv_set_form', 'set_form');

function set_form() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'prunderground_newsletter';
    $ipaddress = $_SERVER['REMOTE_ADDR'];
  
    if(!empty($_POST)) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $wpdb->insert( 
            $table_name, 
            array( 
                'name' => $name, 
                'email' => $email, 
                'ipaddress' => $ipaddress
            )
        );
    }
    exit(0);
}