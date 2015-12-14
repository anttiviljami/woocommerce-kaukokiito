<?php
/**
 * Plugin name: WooCommerce Kaukokiito Shipping Method
 * Plugin URI: https://github.com/anttiviljami/woocommerce-kaukokiito
 * Description: Fixed price shipping method for Kaukokiito terminals
 * Version: 0.1
 * Author: Seravo Oy
 * Author: http://seravo.fi
 * License: GPLv3
 * Text Domain: woocommerce-kaukokiito
 */

/** Copyright 2015 Seravo Oy
  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 3, as
  published by the Free Software Foundation.
  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU General Public License for more details.
  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! class_exists ( 'WC_Kaukokiito' ) ) :

/**
 * Our main plugin class
 */
class WC_Kaukokiito {
 public static $instance;

  public static function init() {
    if ( is_null( self::$instance ) ) {
      self::$instance = new WC_Kaukokiito();
    }
    return self::$instance;
  }
  private function __construct() {
    add_action( 'plugins_loaded', array( $this, 'load_our_textdomain' ) );
    add_action( 'woocommerce_shipping_init', array( $this, 'shipping_method_init' ) );
  }

  /**
   * Load our plugin textdomain
   */
  public static function load_our_textdomain() {
    load_plugin_textdomain( 'woocommerce-kaukokiito', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );
  }

  /**
   * Init our shipping method
   */
  public static function shipping_method_init() {
    require_once 'inc/class-wc-kaukokiito-shipping-method.php'; // WC_Kaukokiito_Shipping_Method
    add_filter( 'woocommerce_shipping_methods', __CLASS__ . '::add_shipping_method' );
  }

  /**
   * Add shipping methods to WooCommerce
   */
  public static function add_shipping_method( $methods ) {
    $methods[] = 'WC_Kaukokiito_Shipping_Method';
    return $methods;
  }
}

endif;

// Init our plugin
$woocommerce_kaukokiito = WC_Kaukokiito::init();
