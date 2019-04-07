<?php 
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
  function express_shipping_method_init() {
    if ( ! class_exists( 'Express_Shipping_Method' ) ) {
      class Express_Shipping_Method extends WC_Shipping_Method {
        /**
         * Constructor for your shipping class
         *
         * @access public
         * @return void
         */
        public function __construct() {
          global $hide_save_button;
         
          if(isset($_REQUEST['section']) && $_REQUEST['section'] == 'easy_shipping') $hide_save_button = true;

          $status = (get_option( 'easy_shipping', 0 )==1)?'yes':'no';
          // Express Delivery 
          $this->id                 = 'express_delivery'; // Id for your shipping method. Should be uunique.
          $this->method_title       = __( 'Express Delivery' );  // Title shown in admin
          $this->method_description = wp_sprintf('Click %s Express Delivery</a> Tab from top Tab list for setting Normal Delivery.', '<a href="'.admin_url( '/admin.php?page=wc-settings&tab=easy_shipping', 'admin' ).'">'); // Description shown in admin
          $this->enabled            = $status; // This can be added as an setting but for this example its forced enabled
          $this->title              = "Express Delivery"; // This can be added as an setting but for this example its forced.
          
          $this->init();
        }
        /**
         * Init your settings
         *
         * @access public
         * @return void
         */
        function init() {
          // Load the settings API
          $this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings
          $this->init_settings(); // This is part of the settings API. Loads settings you previously init.
          // Save settings in admin if you have any defined
          add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );

        }

        /**
         * calculate_shipping function.
         *
         * @access public
         * @param mixed $package
         * @return void
         */
        public function calculate_shipping( $package ) {
          $rate = array(
            'id' => $this->id,
            'label' => $this->title
          );
          // Register the rate
          $this->add_rate( $rate );
        }

        public function is_available( $package ){
         
          return true;
        }
      }
    }
  }
  add_action( 'woocommerce_shipping_init', 'express_shipping_method_init' );
  function add_express_shipping_method( $methods ) {
    $methods['express_delivery'] = 'Express_Shipping_Method';
    return $methods;
  }
  add_filter( 'woocommerce_shipping_methods', 'add_express_shipping_method' );
}