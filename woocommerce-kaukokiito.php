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

    add_action( 'woocommerce_before_order_notes', array( $this, 'dropdown' ), 10, 1 );
    add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'update_order_meta' ) );
    add_action( 'woocommerce_admin_order_data_after_billing_address', array( $this, 'display_admin_order_meta' ), 10, 1 );
    add_action( 'woocommerce_checkout_process', array( $this, 'checkout_field_process' ) ); 
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

  /**
   * Render the terminal select dropdown
   */
  public static function dropdown( $checkout ) {
?>
<p class="form-row form-row-wide validate-required woocommerce-validated" id="kaukokiito_field" style="display:none">
  <label for="kaukokiito_terminal" class=""><?php _e( 'Choose your Kaukokiito Terminal', 'woocommerce-kaukokiito' ); ?> <abbr class="required" title="<?php _e( 'required', 'woocommerce' ); ?>">*</abbr></label>
  <select name="kaukokiito_terminal" id="kaukokiito_terminal">
    <option value=""><?php _e( 'Choose a Terminal', 'woocommerce-kaukokiito'); ?></option>
    <option value="Eura">Eura</option>
    <option value="Forssa">Forssa</option>
    <option value="Helsinki">Helsinki</option>
    <option value="H%C3%A4meenlinna">Hämeenlinna</option>
    <option value="Joensuu">Joensuu</option>
    <option value="Jyv%C3%A4skyl%C3%A4">Jyväskylä</option>
    <option value="Kajaani">Kajaani</option>
    <option value="Kemi">Kemi</option>
    <option value="Kokkola">Kokkola</option>
    <option value="Kouvola">Kouvola</option>
    <option value="Kuopio">Kuopio</option>
    <option value="Kuusamo">Kuusamo</option>
    <option value="Lahti">Lahti</option>
    <option value="Lappeenranta">Lappeenranta</option>
    <option value="Maarianhamina">Maarianhamina</option>
    <option value="Mikkeli">Mikkeli</option>
    <option value="Oulu">Oulu</option>
    <option value="Pori">Pori</option>
    <option value="Rauma">Rauma</option>
    <option value="Rovaniemi">Rovaniemi</option>
    <option value="Salo">Salo</option>
    <option value="Sastamala">Sastamala</option>
    <option value="Savonlinna">Savonlinna</option>
    <option value="Sein%C3%A4joki">Seinäjoki</option>
    <option value="Tampere">Tampere</option>
    <option value="Turku">Turku</option>
    <option value="Vaasa">Vaasa</option>
    <option value="Varkaus">Varkaus</option>
    <option value="Ylivieska">Ylivieska</option>
  </select>
</p>
<script>
(function($){
$(document).ready(function() {
  // only show if shipping method is kaukokiito
  function toggleTerminalSelect() {
    console.log('debug');
    if( $('input.shipping_method:checked').val() === 'kaukokiito' ) {
      $('#kaukokiito_field').show();
    }
    else {
      $('#kaukokiito_field').hide();
    }
  }
  $(document).on('change', '#shipping_method input:radio', toggleTerminalSelect);
  toggleTerminalSelect();
  // use select2 if available
  if( $().select2 ) {
    $('select[name="kaukokiito_terminal"]').select2({ 
      placeholder: "<?php _e( 'Choose a Terminal', 'woocommerce-kaukokiito'); ?>",
      placeholderOption: 'first'
    });
  }
});
})(jQuery);
</script>
<?php
  }

  /**
   * Update the Order Meta With Field Value
   *
   * @return void
   */  
  function update_order_meta( $order_id ) {
    if ( ! empty( $_POST['kaukokiito_terminal'] ) ) {
      update_post_meta( $order_id, 'kaukokiito_terminal', sanitize_text_field( $_POST['kaukokiito_terminal'] ) );
    }
  }

  /**
   * Process the checkout
   *
   * @return void
   */  
  function checkout_field_process() {
    // Check if set, if its not set add an error.
    if ( ! $_POST['kaukokiito_terminal'] ) {
      wc_add_notice( __( '<strong>Kaukokiito Terminal</strong> is a required field.', 'woocommerce-kaukokiito' ), 'error' );
    }
  } 

  /**
   * Display field value on the order edit page
   *
   * @return void
   */  
  function display_admin_order_meta( $order ){
?>
<p><strong><?php _e( 'Terminal:', 'woocommerce-kaukokiito' ); ?></strong> <?php echo get_post_meta( $order->id, 'kaukokiito_terminal', true ); ?></p>
<?php
  } 

}

endif;

// Init our plugin
$woocommerce_kaukokiito = WC_Kaukokiito::init();
