<?php

/**
 * Plugin Name: Simple MinMax
 * Plugin URI: http://tips.rf.gd/smm.html
 * Description: A plugin that allows the setting of minimum and/or maximum order quantities on a product by product basis and optionally displays the information to the user.
 * Author:  NicNet
 * Author URI: http://tips.rf.gd/smm.html
 * Version: 3.0.1
 * Requires at least: 2.0
 * Tested up to: 6.6.1
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

defined( 'ABSPATH' ) || exit;


if ( ! class_exists( 'WC_simple_minmax_plugin' ) ) :
class WC_simple_minmax_plugin {
  /**
  * Construct the plugin.
  */
  public function __construct() {
    add_action( 'plugins_loaded', array( $this, 'init' ) );
  }
  /**
  * Initialize the plugin.
  */
  public function init() {
    // Checks if WooCommerce is installed.
    if ( class_exists( 'WC_Integration' ) ) {
      // Include our integration class.
      include_once 'class-wc-integration-simpleminmax-integration.php';
      // Register the integration.
      add_filter( 'woocommerce_integrations', array( $this, 'add_integration' ) );
    }



    if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    // Set the plugin slug
    define( 'SIMPLE_MINMAX_PLUGIN_SLUG', 'wc-settings' );
    // Setting action for plugin
    add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'WC_simple_minmax_plugin_action_links' );
    }
  }
  /**
   * Add a new integration to WooCommerce.
   */
  public function add_integration( $integrations ) {
    $integrations[] = 'WC_simple_minmax_plugin_Integration';
    return $integrations;
  }
 /**
  * Cloning is forbidden.
  */
  public function __clone() {
            // Override this PHP function to prevent unwanted copies of your instance.
            //   Implement your own error or use `wc_doing_it_wrong()`
  }


}


$WC_simple_minmax_plugin = new WC_simple_minmax_plugin( __FILE__ );

function WC_simple_minmax_plugin_action_links( $links ) {
    $base = menu_page_url( SIMPLE_MINMAX_PLUGIN_SLUG, false ).'&tab=integration&section=simple-minmax-plugin-integration';
    $links[] = '<a href="'. esc_url($base).'">Settings</a>';
    return $links;
  }
endif;
