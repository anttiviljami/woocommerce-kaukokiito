<?php
/**
 * Filename: class-wc-kaukokiito-shipping-method.php
 * Description: Our main shipping method class
 */

if ( ! class_exists( 'WC_Kaukokiito_Shipping_Method' ) ) :

class WC_Kaukokiito_Shipping_Method extends WC_Shipping_Method {

  /**
   * Our shipping class constructor
   *
   * @access public
   * @return void
   */
  public function __construct() {
    $this->id = 'kaukokiito';
    $this->method_title = __( 'Kaukokiito', 'woocommerce-kaukokiito' );
    $this->method_description = __( 'Ship to a preset list of Kaukokiito Terminals', 'woocommerce-kaukokiito' ); 
    $this->init();

		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
  }

  /**
   * Init our plugin settings
   *
   * @access public
   * @return void
   */
  public function init() {
    // Load the settings API
    $this->init_form_fields(); 
    $this->init_settings(); 

    // Define settings
		$this->title = $this->get_option( 'title' );
		$this->enabled = $this->get_option( 'enabled' );
		$this->availability = $this->get_option( 'availability' );
		$this->countries = $this->get_option( 'countries' );
		$this->tax_status = $this->get_option( 'tax_status' );
		$this->cost = $this->get_option( 'cost' );
  }

  /**
  * Initialise Gateway Settings Form Fields
  */
  function init_form_fields() {
    $this->form_fields = array(
      'enabled' => array(
        'title' 		=> __( 'Enable/Disable', 'woocommerce' ),
        'type' 			=> 'checkbox',
        'label' 		=> __( 'Enable this shipping method', 'woocommerce' ),
        'default' 		=> 'no',
      ),
      'title' => array(
        'title' 		=> __( 'Method Title', 'woocommerce' ),
        'type' 			=> 'text',
        'description' 	=> __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
        'default'		=> __( 'Ship to Kaukokiito Terminal', 'woocommerce-kaukokiito' ),
        'desc_tip'		=> true
      ),
      'availability' => array(
        'title' 		=> __( 'Availability', 'woocommerce' ),
        'type' 			=> 'select',
        'default' 		=> 'all',
        'class'			=> 'availability wc-enhanced-select',
        'options'		=> array(
          'all' 		=> __( 'All allowed countries', 'woocommerce' ),
          'specific' 	=> __( 'Specific Countries', 'woocommerce' ),
        ),
      ),
      'countries' => array(
        'title' 		=> __( 'Specific Countries', 'woocommerce' ),
        'type' 			=> 'multiselect',
        'class'			=> 'wc-enhanced-select',
        'css'			=> 'width: 450px;',
        'default' 		=> '',
        'options'		=> WC()->countries->get_shipping_countries(),
        'custom_attributes' => array(
          'data-placeholder' => __( 'Select some countries', 'woocommerce' )
        )
      ),
      'tax_status' => array(
        'title' 		=> __( 'Tax Status', 'woocommerce' ),
        'type' 			=> 'select',
        'class'         => 'wc-enhanced-select',
        'default' 		=> 'taxable',
        'options'		=> array(
          'taxable' 	=> __( 'Taxable', 'woocommerce' ),
          'none' 		=> _x( 'None', 'Tax status', 'woocommerce' )
        )
      ),
      'cost' => array(
        'title' 		=> __( 'Cost', 'woocommerce' ),
        'type' 			=> 'text',
        'placeholder'	=> '',
        'description'	=> __( 'Set a fixed cost', 'woocommerce-kaukokiito' ),
        'default'		=> '',
        'desc_tip'		=> true
      )
    );
  }

  /**
   * Return our shipping costs
   *
   * @access public
   * @param mixed $package
   * @return void
   */
  public function calculate_shipping( $package ) {
    $rate = array(
      'id' => $this->id,
      'label' => $this->title,
      'cost' => $this->cost,
    );

    // Register the rate
    $this->add_rate( $rate );
  }

	/**
	 * is_available function.
	 *
	 * @param array $package
	 * @return bool
	 */
	public function is_available( $package ) {
		if ( "no" === $this->enabled ) {
			return false;
		}
		if ( 'including' === $this->availability ) {
			if ( is_array( $this->countries ) && ! in_array( $package['destination']['country'], $this->countries ) ) {
				return false;
			}
		} else {
			if ( is_array( $this->countries ) && ( in_array( $package['destination']['country'], $this->countries ) || ! $package['destination']['country'] ) ) {
				return false;
			}
		}
		return apply_filters( 'woocommerce_shipping_' . $this->id . '_is_available', true, $package );
	}
}

endif;

