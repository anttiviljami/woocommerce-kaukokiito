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

if ( ! class_exists ( 'WC_Kaukokiito_Shipping_Method' ) ) :

/**
 * Our main plugin class
 */
class WC_Kaukokiito_Shipping_Method extends WC_Shipping_Method {
 public static $instance;

  public static function init() {
    if ( is_null( self::$instance ) ) {
      self::$instance = new WC_Kaukokiito_Shipping_Method();
    }
    return self::$instance;
  }
  private function __construct() {
    add_action( 'plugins_loaded', array( $this, 'load_our_textdomain' ) );
  }

  /**
   * Load our plugin textdomain
   */
  public static function load_our_textdomain() {
    load_plugin_textdomain( 'woocommerce-kaukokiito', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );
  }

}

endif;

// Init our plugin
$woocommerce_kaukokiito = WC_Kaukokiito_Shipping_Method::init();
