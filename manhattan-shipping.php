<?php
/**
 * Plugin Name: Easy Shipping
 * Plugin URI: http://larasoftbd.com/
 * Description: Easy shipping for Woocommerce shipping on your local or any place you want. 
 * Version: 1.0.0
 * Author: larasoft
 * Author URI: https://larasoftbd.com/
 * Text Domain: manhattan-shipping
 * Domain Path: /languages
 * Requires at least: 4.0
 * Tested up to: 4.9
 *
 * @package     manhattan-shipping
 * @category 	Core
 * @author 		LaraSoft
 */

/**
 * Restrict direct access
*/


if ( ! defined( 'ABSPATH' ) ) { exit; }
define('SHIPPINGDIR', plugin_dir_path( __FILE__ ));
define('SHIPPINGURL', plugin_dir_url( __FILE__ ));



function pluginprefix_deactivation(){
        /* if uninstall not called from WordPress exit */
        /*if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
            exit;
        }*/
   global $wpdb;
   $wpdb->query( "DROP TABLE IF EXISTS ".$wpdb->prefix."easy_shipping" );
   $wpdb->query( "DROP TABLE IF EXISTS ".$wpdb->prefix."easy_ziptable" );
}
register_deactivation_hook( __FILE__, 'pluginprefix_deactivation');


require_once(SHIPPINGDIR . 'inc/class.php');

new manhattan_shipping\manhattan_shippingClass;

/* New Shipping */
require_once SHIPPINGDIR.'inc/easy-shipping.php';