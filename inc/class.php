<?php 
namespace manhattan_shipping;
use WP_Query;


/*
* manhattan_shipping Class 
*/

if (!class_exists('manhattan_shippingClass')) {
    class manhattan_shippingClass{
        public $plugin_url;
        public $plugin_dir;
        public $wpdb;
        public $easy_shipping; 
        public $easy_ziptable; 
        public $postmeta; 
        public $posts;
        
    
        /**Plugin init action**/ 
        public function __construct() {
            global $wpdb;
            $this->plugin_url 				= SHIPPINGURL;
            $this->plugin_dir 				= SHIPPINGDIR;
            $this->wpdb 					= $wpdb;
            $this->easy_ziptable            = $this->wpdb->prefix . 'easy_ziptable';	
            $this->easy_shipping            = $this->wpdb->prefix . 'easy_shipping';
            $this->postmeta                 = $this->wpdb->prefix . 'postmeta';
            $this->posts                    = $this->wpdb->prefix . 'posts';
         
            $this->init();
            $this->db();
        }
    
        public function db(){
            
            // $this->wpdb->query('DROP TABLE ' . $this->easy_shipping );
            if($this->wpdb->get_var("SHOW TABLES LIKE '$this->easy_shipping'") != $this->easy_shipping) {
                echo 'inside db if <pre>';
                print_r($this->wpdb);
                echo '</pre>';
                //table not in database. Create new table
                $charset_collate = $this->wpdb->get_charset_collate();
                $sqlo = "CREATE TABLE $this->easy_shipping (
                     id int(20) NOT NULL AUTO_INCREMENT,
                     country_name varchar(300) NOT NULL,
                     state varchar(300) NOT NULL, 
                     city text NOT NULL, 
                     delivery_area text NOT NULL, 
                     min_amount varchar(100) NOT NULL, 
                     max_amount varchar(100) NOT NULL, 
                     charge varchar(100) NOT NULL, 
                     active_express int(20) NOT NULL,
                     express_delivery varchar(100) NOT NULL,
                     isexpress varchar(150) NOT NULL,
                     express_delivery int(20) NOT NULL,
                     created_dt timestamp NOT NULL,
                     UNIQUE KEY id (id)
                ) $charset_collate;";
                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
                dbDelta( $sqlo );
            }

            if($this->wpdb->get_var("SHOW TABLES LIKE '$this->easy_ziptable'") != $this->easy_ziptable) {
                //table not in database. Create new table
                $charset_collate = $this->wpdb->get_charset_collate();
                $sql = "CREATE TABLE $this->easy_ziptable (
                     id int(20) NOT NULL AUTO_INCREMENT,
                     s_id int(20) NOT NULL,
                     zipcode varchar(500) NOT NULL,
                     created_dt timestamp NOT NULL,
                     UNIQUE KEY id (id)
                ) $charset_collate;";
                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
                dbDelta( $sql );
            }
        } // End db();




        private function init(){
            

            add_action( 'admin_enqueue_scripts', array($this, 'manhattan_shipping_backend_script') );

            add_action('wp_enqueue_scripts',array($this, 'manhattan_shipping_script') );
            /*Admin enque script*/

            add_action('wp_head', array($this, 'frontendmanhattan_shipping') );


            add_filter( 'woocommerce_settings_tabs_array', array($this, 'add_settings_tab'), 50 );

            add_action( 'woocommerce_settings_tabs_easy_shipping', array($this,'settings_tab') );

            // Admin Head
            add_action( 'admin_head', array($this, 'easyAdminheadHook') );

            if(isset($_COOKIE['easy_country'])){
            /* Add Extra Field to shipping */
                add_filter('woocommerce_shipping_fields', array($this, 'easy_woocommerce_shipping_fields'));
                
                add_action('woocommerce_add_to_cart' , array($this, 'set_country_befor_cart_page'));     
            }
            

            add_filter( 'woocommerce_shipping_package_name', array($this, 'easy_shipping_package_name') );
            add_filter( 'woocommerce_order_button_html', array($this, 'replace_order_button_html'), 10, 2 );
            /*
            * Ajax Functions List Backend 
            */
            // Change Shipping Cost Label
            add_filter( 'woocommerce_cart_shipping_method_full_label', array($this, 'changeShippingLabe'), 10, 2 );

            /*Get selected contry State*/
            add_action('wp_ajax_nopriv_getContryState', array($this, 'getContryState'));
            add_action( 'wp_ajax_getContryState', array($this, 'getContryState') );

            //=======================  S A V E & U P D A T E  D A T A ====================//
            add_action('wp_ajax_nopriv_saveEasyData', array($this, 'saveEasyData'));
            add_action( 'wp_ajax_saveEasyData', array($this, 'saveEasyData') );

            //=================== D E L E T E  D A T A ================== //
            add_action('wp_ajax_nopriv_deleteEasyData', array($this, 'deleteEasyData'));
            add_action( 'wp_ajax_deleteEasyData', array($this, 'deleteEasyData') );

            //=================== A C T I V E  E A S Y  S H I P P I N G ================== //
            add_action('wp_ajax_nopriv_activeShippingMethod', array($this, 'activeShippingMethod'));
            add_action( 'wp_ajax_activeShippingMethod', array($this, 'activeShippingMethod') );

            //=================== A C T I V E  E A S Y  S H I P P I N G ================== //
            add_action('wp_ajax_nopriv_deleteLocaionPrice', array($this, 'deleteLocaionPrice'));
            add_action( 'wp_ajax_deleteLocaionPrice', array($this, 'deleteLocaionPrice') );

            //=================== S A V E  E A S Y  S E T T I N G S ================== //
            add_action('wp_ajax_nopriv_updateEasySettings', array($this, 'updateEasySettings'));
            add_action( 'wp_ajax_updateEasySettings', array($this, 'updateEasySettings') );

            //=================== C H A N G E  M U L T I P L E  A R E A   N A M E ================== //
            add_action('wp_ajax_nopriv_changeneighborAreaMultiple', array($this, 'changeneighborAreaMultiple'));
            add_action( 'wp_ajax_changeneighborAreaMultiple', array($this, 'changeneighborAreaMultiple') );
            

            //=================== S E A R C H  A D M I N  L I S T  D A T A ================== //
            add_action('wp_ajax_nopriv_adminSearchDB', array($this, 'adminSearchDB'));
            add_action( 'wp_ajax_adminSearchDB', array($this, 'adminSearchDB') );
            

            /*
            * Ajax Functions List Fronend 
            */
            //================= G E T  C I T Y  N A M E ===================//
            add_action('wp_ajax_nopriv_getAllCityviaState', array($this, 'getAllCityviaState'));
            add_action( 'wp_ajax_getAllCityviaState', array($this, 'getAllCityviaState') );

            //================= G E T  A L L  A R E A  N A M E  U S I N G  C I T Y  ===================//
            add_action('wp_ajax_nopriv_getAllArea', array($this, 'getAllArea'));
            add_action( 'wp_ajax_getAllArea', array($this, 'getAllArea') );

            add_filter( 'woocommerce_package_rates', array($this, 'custom_shipping_costs'), 20, 2 );


            if(get_option( 'easy_shipping', 0 ) == 1):
            /********** Change Product price by Shipping Location ***********/
            add_filter('woocommerce_product_get_regular_price', array( $this, 'easy_shipping_regular_dynamic_price'), 99);
            add_filter('woocommerce_product_get_price', array( $this, 'easy_shipping_dynamic_price'), 99);

            /*=========== S E T  S H I P I N G   A R E A   A S  P R O D U C T  T A X A N O M Y ===========*/
            add_action( 'admin_init', array($this, 'insertAreatoAsCityTax') );

            /*=================== Set Location Product ======================*/
            add_action( 'woocommerce_product_query', array($this, 'customizeWoocommerceProductQury') );

            /* ========= T A X O N O M Y ========== */
            add_action( 'init', array($this, 'easy_create_my_taxonomy') );

            /*============= A D D  M E T A  B O X  F O R  L O C A T I O N  B A S E D  P R I C E  ===========*/
            add_action( 'woocommerce_product_options_general_product_data', array($this, 'locationbasedProductPrice') );

            /*=========== S A V E  L O C A T I O N  B A S E D  P R I C E =============**/
            add_action( 'woocommerce_process_product_meta', array($this, 'savelocationbasedprice') );

            /**** ===========  Change product price in cart ============ ****/
            add_action( 'woocommerce_before_calculate_totals', array($this, 'sv_change_product_price_cart'), 10, 1 );

            /****======================= Get Popup data using country select  ======================**/
            add_action('wp_ajax_nopriv_getDatausingCountry', array($this, 'getDatausingCountry'));
            add_action( 'wp_ajax_getDatausingCountry', array($this, 'getDatausingCountry') );

            /*==================== Ajax auto fill delivery area for suggession ========================*/
            add_action('wp_ajax_nopriv_typeDeliveryAutoFill', array($this, 'typeDeliveryAutoFill'));
            add_action( 'wp_ajax_typeDeliveryAutoFill', array($this, 'typeDeliveryAutoFill') );

            /**** ========================== Popup 2 action ======================= **/
            add_action('wp_ajax_nopriv_popuptAction', array($this, 'popuptAction'));
            add_action( 'wp_ajax_popuptAction', array($this, 'popuptAction') );

            /**** ========================== Popup 1 action ======================= **/
            add_action('wp_ajax_nopriv_popupaction2', array($this, 'popupaction2'));
            add_action( 'wp_ajax_popupaction2', array($this, 'popupaction2') );

            // Update List Data 
            add_action('wp_ajax_nopriv_updateEasyShippingListData', array($this, 'updateEasyShippingListData'));
            add_action( 'wp_ajax_updateEasyShippingListData', array($this, 'updateEasyShippingListData') );


            /***====================== New Admin filder to woocommerce product ========================**/
            add_action( 'restrict_manage_posts', array($this, 'admin_posts_filter_restrict_manage_posts_by_taxonomy') );

            /* New Column in order list */
            add_filter( 'manage_edit-shop_order_columns', array($this, 'set_custom_edit_shop_order_columns') );
            add_action( 'manage_shop_order_posts_custom_column' , array($this, 'custom_shop_order_column'), 10, 2 );

            /*=== After Complete Order ===*/
            add_filter( 'parse_query', array($this, 'wporder_posts_filter') );

            /* ============= Different shipping open by default ================*/ 
            add_filter( 'woocommerce_ship_to_different_address_checked', '__return_true' );

            // Change Woocommerce Template path to from this plugin
            add_filter( 'woocommerce_locate_template', array($this, 'woo_easy_plugin_template'), 1, 3 );
            
            endif;


        } // End init()

        function custom_override_checkout_fields( $fields ) {

            $fields['shipping']['custom_field'] = array(
                'label' => 'Custom field',
                'required' => 1,
                'class' => array ('address-field', 'update_totals_on_change' )
            );
        
            return $fields;
        }

        function easy_shipping_package_name( $name ) {
            return __('Delivery', 'easy');
        }
        function wporder_posts_filter($query){
            global $pagenow;
            $type = 'shop_order';
            if (isset($_GET['post_type'])) {
                $type = $_GET['post_type'];
            }

            if ( 'shop_order' == $type && is_admin() && $pagenow=='edit.php' && isset($_GET['meta-city']) && $_GET['meta-city'] != '') {
                echo 'inside class function ooooo <br/>';
                $query->query_vars['meta_key'] = '_shipping_city';
                $query->query_vars['meta_value'] = $_GET['meta-city'];
            }
        }

        function admin_posts_filter_restrict_manage_posts_by_taxonomy(){
            if (isset($_GET['post_type']) && 'product' == $_GET['post_type']){
               $terms = get_terms( 'available-city', array(
                    'hide_empty' => true,
                ) );
               echo "<select name='available-city' id='available-city' class='postform'>";
               echo '<option value="">' . sprintf( esc_html__( 'Show All %s', 'easy' ), 'Available City' ) . '</option>';
               foreach($terms as $sterms){
                    $selected = (isset($_REQUEST['available-city']) && $_REQUEST['available-city'] == $sterms->slug)?'selected':'';
                    echo '<option '.$selected.' value="'.$sterms->slug.'">'.$sterms->name.'</option>';
               }
               echo '</select>';
            }elseif(isset($_GET['post_type']) && 'shop_order' == $_GET['post_type']){
                $allCitys = $this->wpdb->get_results('SELECT `meta_value` FROM '.$this->postmeta.' pm LEFT JOIN '.$this->posts.' p ON pm.`post_id`=p.`ID` WHERE p.`post_type`="shop_order" AND pm.`meta_key`="_billing_city"', OBJECT);

                echo '<select name="meta-city" id="meta-city" class="post-meta">';
                    echo '<option value="">'. sprintf(esc_html__('Show All %s', 'easy'), 'City'  ) .'</option>';
                    foreach($allCitys as $sc){
                        $sltd = (isset($_REQUEST['meta-city']) && $_REQUEST['meta-city'] == $sc->meta_value)?'selected':'';
                        echo '<option '.$sltd.' value="'.$sc->meta_value.'">'.$sc->meta_value.'</option>';
                    }
                echo '</select>';

                
            }
        }

    function custom_shipping_costs( $rates, $package ) {
    // New shipping cost (can be calculated)
    if(isset($_COOKIE['easy_area'])):
        $country    = $_COOKIE['easy_country'];
        $state      = $_COOKIE['easy_state'];
        $city       = $_COOKIE['easy_city'];
        $area       = $_COOKIE['easy_area'];

        $cartSubTotal = WC()->cart->subtotal;

        $quryRate = $this->wpdb->get_row('SELECT `charge` FROM '.$this->easy_shipping.' WHERE country_name="'.$country.'" AND state like "%'.$state.'%" AND city="'.$city.'" AND delivery_area="'.$area.'" AND min_amount <= '.$cartSubTotal.' AND max_amount >= '.$cartSubTotal.'', OBJECT);


    $new_cost = ($quryRate)?$quryRate->charge:00;
    $tax_rate = 00;

    foreach( $rates as $rate_key => $rate ){
        // Excluding free shipping methods
        if( $rate->method_id == 'easy_shipping'){

            // Set rate cost
            $rates[$rate_key]->cost = $new_cost;

            // Set taxes rate cost (if enabled)
            $taxes = array();
            foreach ($rates[$rate_key]->taxes as $key => $tax){
                if( $rates[$rate_key]->taxes[$key] > 0 )
                    $taxes[$key] = $new_cost * $tax_rate;
            }
            $rates[$rate_key]->taxes = $taxes;

        }
    }
    endif;
    
    return $rates;
    
}
      

    /*
    * Show free delivery text if shipping value 0
    */
    function changeShippingLabe($label, $method){
         if(isset($_COOKIE['easy_area']) && $method->get_method_id() == 'easy_shipping'):
            $country    = $_COOKIE['easy_country'];
            $state      = $_COOKIE['easy_state'];
            $city       = $_COOKIE['easy_city'];
            $area       = $_COOKIE['easy_area'];
    
            $cartSubTotal = WC()->cart->subtotal;

            $quryRate = $this->wpdb->get_row('SELECT `min_amount`, `max_amount` FROM '.$this->easy_shipping.' WHERE country_name="'.$country.'" AND state like "%'.$state.'%" AND city="'.$city.'" AND delivery_area="'.$area.'"', OBJECT);
            
            if($cartSubTotal < $quryRate->min_amount):
                    $more = $quryRate->min_amount - $cartSubTotal;
                    $label = __('Our minimum order amount is '.wc_price($quryRate->min_amount).' please add '.wc_price($more).' more to accept order!', 'easy');
                    remove_action( 'woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20);
            elseif($cartSubTotal > $quryRate->min_amount && $cartSubTotal < $quryRate->max_amount ):
                    $more = $quryRate->max_amount - $cartSubTotal;
                    $label = $method->get_label() . ' : ' .  wc_price($method->cost) . '<span class="easysippingNote">'. __('We offer Free Normal Delivery on '.wc_price($quryRate->max_amount).' or more. Please add '.wc_price($more).' to get free Normal Delivery.', 'easy') . '</span>';
            else:
                $label = $method->get_label() . ' : ' . __('Free delivery', 'easy');
            endif;
            return $label;
            else:
                return $label;
            endif;
    }

        /*
        * Easy Shippinng tab content
        */
        public static function settings_tab() {
            //woocommerce_admin_fields( $this->get_settings() );
            if(isset($_REQUEST['zone_id'])){
                $this->newZoneInsert();
            }
            elseif(isset($_REQUEST['easy-settings'])){
                $this->eassyShippingSettings();
            }
            else{
                $this->shippingList();
            }
        } //End function settings_tab()


        protected function eassyShippingSettings(){
            global $hide_save_button;
            $hide_save_button = true;
            require_once($this->plugin_dir . '/view/html-admin-page-easy-shipping-settings.php');
        }

        protected function shippingList(){
            global $hide_save_button;
            $hide_save_button = true;
            $status = get_option( 'easy_shipping', 0 );
            $allships = $this->wpdb->get_results('SELECT * FROM '.$this->easy_shipping.' ORDER BY `delivery_area` ASC', OBJECT);
            require_once($this->plugin_dir . '/view/html-admin-page-shipping-zones.php');    
        }   

        protected function getareaZipCodes($s_id){
            $arrays = array();
            $ary = $this->wpdb->get_results('SELECT `zipcode` FROM '.$this->easy_ziptable.' WHERE s_id='.$s_id.'', OBJECT);
            if(count($ary) > 0){
                foreach($ary as $sz) array_push($arrays, $sz->zipcode);
            }
            return $arrays;
        }
        /*
        * New Zone function
        */
        protected function newZoneInsert(){
            global $current_section, $hide_save_button;
            $hide_save_button = true;
            $allowed_countries = WC()->countries->get_allowed_countries();
            $defautCnt = WC()->countries->get_base_country();
            $states = WC()->countries->get_states( $defautCnt );
            $continents        = WC()->countries->get_continents();

            if(isset($_REQUEST['zone_id']) && $_REQUEST['zone_id'] != 'new'){
                $zipcodes = array();
                $zipQry = $this->wpdb->get_results('SELECT `zipcode` FROM '.$this->easy_ziptable.' WHERE s_id='.$_REQUEST['zone_id'].'', OBJECT);
                foreach($zipQry as $sz) array_push($zipcodes, $sz->zipcode);
                $exstZone = $this->wpdb->get_row('SELECT * FROM '.$this->easy_shipping.' WHERE id='.$_REQUEST['zone_id'].'', OBJECT);
            }

            require_once($this->plugin_dir . '/view/html-admin-page-shipping-zone-methods.php');
        }


        /*
        * Easy ShippingContent
        */
        public static function get_settings() {
        }
        /*
        * Add New tab to Woocommerce settings tab
        */
        function add_settings_tab($settings_tabs){
            $settings_tabs['easy_shipping'] = __( 'Easy Shipping', 'easy' );
            return $settings_tabs;
            woocommerce_admin_fields( self::get_settings() );
        }

        /*
        * Appointment Back office Script
        */
        function manhattan_shipping_backend_script(){
            wp_enqueue_style( 'jqueryDataTable', $this->plugin_url . 'asset/css/admin/jquery.dataTables.min.css', array(), true, 'all' );
            wp_enqueue_style( 'easycss', $this->plugin_url . 'asset/css/admin/easycss.css', array(), true, 'all' );
            
            wp_enqueue_script( 'jQueryDataTableJS', $this->plugin_url . 'asset/js/admin/jquery.dataTables.min.js', array('jquery'), '9.0.1', true );
            wp_enqueue_script( 'manhattan_shippingjs', $this->plugin_url . 'asset/js/admin/easy.js', array('jquery'), '9.0.2', true );
            wp_localize_script( 'manhattan_shippingjs', 'easyAjax', admin_url( 'admin-ajax.php' ));
        }
        /*
        * Voteing font Script
        */ 
        function manhattan_shipping_script(){
            wp_enqueue_style( 'jquerycss', $this->plugin_url . 'asset/jqueryui/jquery-ui.min.css', array(), true, 'all' );
            wp_enqueue_style( 'select2css', $this->plugin_url . 'asset/select2/css/select2.min.css', array(), true, 'all' );
            wp_enqueue_style( 'manhattan_shipping-bacCSS', $this->plugin_url . 'asset/css/easy-shipping.css', array(), true, 'all' );

            wp_enqueue_script( 'jqueryui', $this->plugin_url . 'asset/jqueryui/jquery-ui.min.js', array(), '9.0.0', true );
            wp_enqueue_script( 'select2', $this->plugin_url . 'asset/select2/js/select2.min.js', array(), '9.0.1', true );
            wp_enqueue_script( 'easyshippingjs', $this->plugin_url . 'asset/js/easyshippingjs.min.js', array('jquery', 'select2'), '9.0.2', true );
            wp_localize_script( 'easyshippingjs', 'easyAjax', admin_url( 'admin-ajax.php' ));


        }
     

        /*
        * All Delevery States
        */
        protected function deliveryStates($country = ''){
            
            $qry = 'SELECT `state`, `country_name` FROM '.$this->easy_shipping.' WHERE state!=""';
            if($country != '') $qry .= ' AND country_name="'.$country.'"';
            $qry .=' ORDER BY `delivery_area` ASC';
            $allData = $this->wpdb->get_results($qry, OBJECT);

            $dStates = array();
            foreach($allData as $sSt){
                //$sDSate = json_decode($sSt->state);                
                if (!array_key_exists($sSt->country_name, $dStates)) $dStates[$sSt->country_name] = array();
                //foreach($sDSate as $sSate){
                if(!in_array($sSt->state, $dStates[$sSt->country_name])) $dStates[$sSt->country_name][] =  $sSt->state;
               // }
            }
            return $dStates;
        }



        /*
        * Shortcode Front Use
        * this function return voter candidate using crosel
        */
        function frontendmanhattan_shipping(){
            if(isset($_REQUEST['cid'])){
             $extdata = $this->wpdb->get_row('SELECT * FROM '.$this->easy_shipping.' WHERE id='.$_REQUEST['cid'].'', OBJECT);
            }
            $defaultSates = WC()->countries->get_states( 'US' );
            $home = (is_front_page() || is_home())?true:false;
            if(get_option( 'easy_shipping', 0 ) == 1){
                $states = $this->deliveryStates();
                $allcities = $this->getAllCitywithStateNCountry();
                //$countries = WC()->get_countries();
                require_once($this->plugin_dir . '/view/front-popup.php');
            }
        } // End frontend manhattan shipping

        function getContryState(){
            $cnt = $_REQUEST['cnt'];
            $states = WC()->countries->get_states( $cnt );

            echo json_encode(
                array(
                    'message' => 'success',
                    'state' => $states
                )
            );
            die();
        } // getContryState()


        function saveEasyData(){
            unset($_REQUEST['action']);
            $data = $_REQUEST;
            $ziparray  = array();
            //$deliverAreas = ($_REQUEST['delivery_area'] != '')?explode('|', $_REQUEST['delivery_area']):array();
            //$data['state'] = json_encode($data['state']);
            $zipesyid = array();

            if(!isset($data['id'])){
                $data['delivery_area'] = json_decode(stripslashes($data['delivery_area']));
                foreach($data['delivery_area'] as $sD):
                    $sD->charge = ($sD->charge != '')?$sD->charge:0;
                    if($sD->delivery_area != ''){
                        $insert = $this->wpdb->insert(
                            $this->easy_shipping,
                            array(
                                 'country_name' => $data['country_name'],
                                 'state' => $data['state'], 
                                 'city' => $data['city'], 
                                 'delivery_area' => $sD->delivery_area, 
                                 'min_amount' => $sD->min_amount, 
                                 'max_amount' => $sD->max_amount, 
                                 'charge' => $sD->charge, 
                            ),
                            array('%s', '%s', '%s', '%s', '%s', '%s', '%s')                
                        );

                        echo 'insert: ' . $insert . '<br/>';
                    }
                if($insert){
                    if($sD->zipcode != ''){
                        array_push($zipesyid,  $this->wpdb->insert_id);
                        array_push($ziparray, $sD->zipcode);
                    }
                }
            endforeach;
            }else{
                $update = $this->wpdb->update(
                    $this->easy_shipping,
                    array(
                         'country_name' => $data['country_name'],
                         'state' => $data['state'], 
                         'city' => $data['city'], 
                         'delivery_area' => $data['delivery_area'], 
                         'min_amount' => $data['min_amount'], 
                         'max_amount' => $data['max_amount'], 
                         'charge' => $data['charge'], 
                    ),
                    array('id' => $data['id']),
                    array('%s', '%s', '%s', '%s', '%s', '%s', '%s'),
                    array('%d')
                );
                $deleteOldzip = $this->wpdb->delete($this->easy_ziptable, array('s_id' => $data['id']), array('%d'));
                array_push($zipesyid,  $data['id']);
                array_push($ziparray, $data['zipcode']);
            }
            
            if(count($ziparray) > 0):
                // First Delete existing
                $delete = $this->wpdb->delete(
                    $this->easy_ziptable,
                    array('s_id' => $data['id']),
                    array('%d')
                );
                for($j=0; count($zipesyid) > $j; $j++ ):
                    $szipArray = explode(',', $ziparray[$j]);
                        foreach($szipArray as $sZ):
                            $inserzip = $this->wpdb->insert(
                                $this->easy_ziptable,
                                array(
                                    's_id' => $zipesyid[$j],
                                    'zipcode' => $sZ
                                ),
                                array(
                                    '%d',
                                    '%s'
                                )
                            );
                        endforeach;
                endfor;
            endif;

            $msg = (isset($insert) || isset($update))?'success':'fail';            

            echo json_encode(
                array(
                    'message' => $msg,
                    'data' => $data
                )
            );
            die();
        } // End Save



        /*
        * D E L E T E  D A T A
        */
        function deleteEasyData(){
            $id = $_REQUEST['id'];
            $delete = $this->wpdb->delete(
                $this->easy_shipping,
                array('id'=>$id),
                array('%d')
            );
            $msg = ($delete)?'success':'fail';
            echo json_encode(
                array(
                    'message' => $msg
                )
            );
            die();
        } // end deleteEasyDate()



        function getAllCitywithStateNCountry(){
             $query = 'SELECT * FROM '.$this->easy_shipping.' WHERE city!="" GROUP BY city ORDER BY `city` ASC';
             $qrCitys = $this->wpdb->get_results($query, OBJECT);
             return $qrCitys;
        }


        function getAllCity(
            $val = array(
                'using' => 'country',
                'val' => 'US'
            )
        ){
            $cityArray = array();
            $query = 'SELECT `city` FROM '.$this->easy_shipping.' WHERE city!=""';
            switch($val['using']):
                case 'country':
                    $query .= ' AND country_name="'.$val['val'].'"';        
                break;
            endswitch;
            $query .= ' ORDER BY `city` ASC';

            $qrCitys = $this->wpdb->get_results($query, OBJECT);

            foreach($qrCitys as $sCity){
                if(!in_array($sCity->city, $cityArray)) array_push($cityArray, $sCity->city);
            } 

            return $cityArray;
        }

        function getAllNaigerbor(
            $val = array(
                'using' => 'country',
                'value' => 'US',
                'cnt' => ''
            )
        ){
            $naiborArray  = array();
            $query = 'SELECT `delivery_area` FROM '.$this->easy_shipping.' WHERE delivery_area!=""';
            switch($val['using']):
                case 'country':
                    $query .= ' AND country_name="'.$val['value'].'"';        
                break;
                default:
                    $query .= ' AND country_name="'.$val['cnt'].'" AND state LIKE "%'.$val['value'].'%"';        
            endswitch;
            $query .= ' ORDER BY `delivery_area` ASC';
            $qrAreas = $this->wpdb->get_results($query, OBJECT);

            foreach($qrAreas as $sarea){
                if(!in_array($sarea->delivery_area, $naiborArray)) array_push($naiborArray, $sarea->delivery_area);
            } 

            return $naiborArray;
        }

        function getAllCityviaState(){
            $stateID = $_REQUEST['sID'];
            $cnt = $_REQUEST['cnt'];

            if(get_option( 'v_city', 'yes' ) == 'yes'){
                $output = array();
                $qry = 'SELECT `city` FROM '.$this->easy_shipping.' WHERE country_name="'.$cnt.'"';
                $qry .= ($stateID != 'undefined')?' AND state like "%'.$stateID.'%"':'';
                $qry .= ' ORDER BY `city` ASC';
                $qrCitys = $this->wpdb->get_results($qry, OBJECT);
                foreach($qrCitys as $sCity){
                    if(!in_array($sCity->city, $output)) array_push($output, $sCity->city);
                } 
            }else{
                $output = $this->getAllNaigerbor(array('value' => $stateID, 'cnt' => $cnt, 'using' => 'state'));
            }       

            echo json_encode( 
                array(
                    'message' => 'success',
                    'outputs' => $output,
                    'tt' => $tt
                )
            );
            die();
        } // getAllCityViaState()


        function getAllArea(){
            $cnt = $_REQUEST['cnt'];
            $city_name = $_REQUEST['city_name'];

            $deliverArray = array();
            $qrCitys = $this->wpdb->get_results('SELECT `delivery_area` FROM '.$this->easy_shipping.' WHERE country_name="'.$cnt.'" AND city="'.$city_name.'" ORDER BY `delivery_area` ASC', OBJECT);

            foreach($qrCitys as $sCity){
                if(!in_array($sCity->delivery_area, $deliverArray)) array_push($deliverArray, $sCity->delivery_area);
            } 

            echo json_encode( 
                array(
                    'message' => 'success',
                    'area' => $deliverArray
                )
            );
            die();
        }


        /*
        * Active Normal Delivery and Deactive other shipping method
        */
        function activeShippingMethod(){
            $active = $_REQUEST['active'];
            if(get_option( 'easy_shipping') !== false){
                update_option( 'easy_shipping', $active, 'yes' );
            }else{
                add_option( 'easy_shipping', $active, '', 'yes');
            }
            echo json_encode( 
                array(
                    'message' => 'success'                    
                )
            );

            die();
        }


        /*
        * make taxonomy for woocommerce produt
        * This taxonomy item / tax will set automatically
        */
        function easy_create_my_taxonomy(){
             register_taxonomy(
                'available-city',
                array('product', 'shop_order'),
                array(
                    'label' => __( 'Available City' ),
                    'rewrite' => array( 'slug' => 'available-city' ),
                    'hierarchical' => true,
                    'show_admin_column' => true,
                    'show_in_nav_menus' => false
                )
            );
        } // End Taxonomy

        function easy_shipping_regular_dynamic_price($original_price){
            global $post, $woocommerce;
          if(isset($_COOKIE['easy_city'])){
              $cityname = $_COOKIE['easy_city'];
              $terms = get_term_by('name', $cityname, 'available-city');
              $term_id = $terms->term_id;

              $lPrice = get_post_meta( $post->ID, '_elp_price_'.$term_id, true );
          }

              //Logic for calculating the new price here
          $new_price = (isset($_COOKIE['easy_city']) && $lPrice)?$lPrice:$original_price;//$original_price * 2;

          //Return the new price (this is the price that will be used everywhere in the store)
          return $new_price;
        }

        /*
        * Change Product Price
        */
        function easy_shipping_dynamic_price( $original_price ) {
          global $post, $woocommerce;
          if(isset($_COOKIE['easy_city'])){
              $cityname = $_COOKIE['easy_city'];
              $terms = get_term_by('name', $cityname, 'available-city');
              $term_id = $terms->term_id;

              $lPrice = @get_post_meta( $post->ID, '_elp_sales_price_'.$term_id, true );
          }

          //Logic for calculating the new price here
          $new_price = (isset($_COOKIE['easy_city']) && $lPrice)?$lPrice:$original_price;//$original_price * 2;

          //Return the new price (this is the price that will be used everywhere in the store)
          return $new_price;
         } // easy_shipping_dynamic_price


         /*
         * New Price in Cart
         */
         function sv_change_product_price_cart($cart){
             foreach ( $cart->get_cart() as $cart_item ) {
                $p_id = $cart_item['product_id'];
                // Set your price

                $cityname = $_COOKIE['easy_city'];
                $terms = get_term_by('name', $cityname, 'available-city');
                $term_id = $terms->term_id;

                $lPriceSales = get_post_meta( $p_id, '_elp_sales_price_'.$term_id, true );
                $lPrice = get_post_meta( $p_id, '_elp_price_'.$term_id, true );

                //Logic for calculating the new price here
                if($lPriceSales){
                    $cart_item['data']->set_price( $lPriceSales ); // WC 3.0+    
                }elseif($lPriceSales == '' && $lPrice != ''){
                    $cart_item['data']->set_price( $lPrice ); // WC 3.0+    
                }    
                
                    
                
            }
         }

         /*
         * Set Normal Delivery Area as Location Taxonomy for woocommerce product
         */
         function insertAreatoAsCityTax(){
            $allCitys = $this->wpdb->get_results('SELECT `country_name`, `city` FROM '.$this->easy_shipping.' WHERE city!="" GROUP BY `city` ORDER BY `city` ASC', OBJECT);

            foreach($allCitys as $sCity){
                wp_insert_term(
                  $sCity->city, // the term 
                  'available-city', // the taxonomy
                  array(
                    'description'=> 'A City of ' . WC()->countries->countries[ $sCity->country_name ],
                    'slug' => str_replace(' ', '-', $sCity->city)
                  )
                );
            }
         } // End insertAreatoAscityTax()

    function customizeWoocommerceProductQury( $q ){
        if(isset($_COOKIE['easy_city'])):
        $easy_city = $_COOKIE['easy_city'];
        $easy_city = str_replace(' ', '-', strtolower($easy_city));
        $taxQuery = array(
            'relation' => 'AND', 
            array(
                'taxonomy'         => 'available-city',
                'field'            => 'slug',
                'terms'            => array( $easy_city ),
                'include_children' => true,
                'operator'         => 'IN',
            )
        );

        $q->set( 'tax_query', (array) $taxQuery );
        endif;

    } // End customizeWoocommerceProductQury

    function locationbasedProductPrice() {
    global $post;      
    $postAllLPrices = $this->wpdb->get_results('SELECT * FROM '.$this->postmeta.' WHERE meta_key LIKE "_elp_price_%" AND post_id='.$post->ID.'', OBJECT);

    $postAllSalesPrices = $this->wpdb->get_results('SELECT * FROM '.$this->postmeta.' WHERE meta_key LIKE "_elp_sales_price_%" AND post_id='.$post->ID.'', OBJECT);

    $existTerms = array();
    foreach($postAllLPrices as $sTerm){
        $sKey = str_replace('_elp_price_', '', $sTerm->meta_key);
        array_push($existTerms, (int)$sKey);
    }

    $terms = get_terms([
        'taxonomy' => 'available-city',
        'hide_empty' => false,
    ]);

    $cityOption = array(''=>'Select City Name..');
    foreach($terms as $sty){
      if(!in_array($sty->term_id, $existTerms)) $cityOption[$sty->term_id] = $sty->name;  
    } 
      $select_field = array(
        'id' => 'cityprice',
        'label' => __( 'Location Based price ('.get_woocommerce_currency_symbol().')' , 'easy' ),
        'options' => $cityOption,
        'desc_tip' => 'true',
        'description' => __('Product Price for Different Location.', 'easy')
      );

      woocommerce_wp_select( $select_field );

      foreach($postAllLPrices as $k => $sk){
        $locationKey = str_replace('_elp_price_', '', $sk->meta_key);
        $getTerm = get_term_by('id', (int)$locationKey, 'available-city');

        echo '<div class="locationpricesinglewrap"><p class="form-field singleLocationPrice"><label for="elp_price"><strong>'.$getTerm->name.'</strong> ('.__('Regular price', 'easy').')</label><input type="number" step="0.01" min="0" class="short" style="" name="_elp_price['.$locationKey.']" id="_elp_price_'.$locationKey.'" value="'.$sk->meta_value.'" placeholder=""><span class="deletelocationPrice" data-name="'.$getTerm->name.'" data-tax_id="'.$locationKey.'"><span class="dashicons dashicons-no-alt"></span></span></p>

            <p class="form-field singleRegularLocationPrice"><label for="elp_sales_price"><strong>'.$getTerm->name.'</strong> ('.__('Sales price', 'easy').')</label><input type="number" class="short" style="" step="0.01" min="0" name="_elp_sales_price['.$locationKey.']" id="_elp_sales_price_'.$locationKey.'" value="'.$postAllSalesPrices[$k]->meta_value.'" placeholder=""></p>
        </div>';
      }

    } // end locationbasedproductprice

    function savelocationbasedprice($post_id){
         $locationPrices = isset( $_POST['_elp_price'] ) ? $_POST['_elp_price'] : array();
         $locationSalesPrices = isset( $_POST['_elp_sales_price'] ) ? $_POST['_elp_sales_price'] : array();

         $product = wc_get_product( $post_id );
         foreach($locationPrices as $k => $slP):
            $product->update_meta_data( '_elp_price_' . $k, $slP );
            $product->update_meta_data( '_elp_sales_price_' . $k, $locationSalesPrices[$k] );
         endforeach;
         $product->save();
    } // End Class

    function easyAdminheadHook(){
        $newentry = (isset($_REQUEST['zone_id']) && $_REQUEST['zone_id'] == 'new')?'new':'edit';
        echo '<script>
            var easy = {
                easy_page:"'.admin_url( 'admin.php?page=wc-settings&tab=easy_shipping', 'easy' ).'",
                wc_currency_samble:"'.get_woocommerce_currency_symbol().'",
                entry_type:"'.$newentry.'"
                } 
        </script>';
    }

    /*
    * Delete Location based price meta
    */
    function deleteLocaionPrice(){
        $tax_id     = $_REQUEST['tax_id'];
        $post_id    = $_REQUEST['post_id'];

        $delete = $this->wpdb->delete(
            $this->postmeta,
            array('post_id' => $post_id, 'meta_key' => '_elp_price_'.$tax_id),
            array('%s', '%s')
        );
        $delete1 = $this->wpdb->delete(
            $this->postmeta,
            array('post_id' => $post_id, 'meta_key' => '_elp_sales_price_'.$tax_id),
            array('%s', '%s')
        );

        
        $msg = ($delete)?'success':'fail';
        echo json_encode( array(
            'message' => $msg, 
            'tax_id' => $tax_id, 
            'post_id' => $post_id
        ) );

        die();
    } // End deleteLocaionPrice();

    /*
    * Add Extra Shipping Field 
    */
    function easy_woocommerce_shipping_fields($fields){
         $fields['shipping_area'] = array(
            'label' => __('Neighbourhood Area', 'easy'), // Add custom field label
            'required' => true, // if field is required or not
            'clear' => false, // add clear or not
            'type' => 'text', // add field type
            'class' => array('easyautoarea'),    // add class name
            'default' => $_COOKIE['easy_area'],
            'custom_attributes' => array('readonly' => true)
        );
        
        $fields['shipping_state']['custom_attributes'] = array('readonly' => true, 'disabled' => true);
        $fields['shipping_city']['custom_attributes'] = array('readonly' => true);
        
        $fields['shipping_country']['custom_attributes'] = array('readonly' => true, 'disabled' => true);
    return $fields;
    } // End custom_woocommerce_shipping_fields();


    /*
    * Set Shipping Value using previous stored cookie
    */
    function set_country_befor_cart_page(){
        
        WC()->customer->set_country($_COOKIE['easy_country']); //reset default country
        WC()->customer->set_shipping_country($_COOKIE['easy_country']);

        //$stateme = WC()->countries->get_states( $_COOKIE['easy_country'] )[$_COOKIE['easy_state']]; 
        WC()->customer->set_state($_COOKIE['easy_state']); //reset default country
        WC()->customer->set_shipping_state($_COOKIE['easy_state']);

        WC()->customer->set_city($_COOKIE['easy_city']); //reset default country
        WC()->customer->set_shipping_city($_COOKIE['easy_city']);
    } // End set_country_before_cart_page()


    /*
    * Update / Save Settings 
    */
    function updateEasySettings(){
        unset($_POST['action']);
        $post = $_POST;
        foreach($post as $k => $sp){
            $action = (get_option($k))?update_option( $k, $sp, 'yes' ):add_option( $k, $sp, '', 'yes' );
        }
        echo json_encode( array(
            'message' => 'success'
        ) );
        die();
    }

    function getDatausingCountry(){
        $countryName = $_POST['country'];
        if(get_option( 'v_state', 'yes' ) == 'yes'){
            $output = array();
            $dstates = $this->deliveryStates($countryName);
            foreach($dstates[$countryName] as $ss){
              $output[$ss] = WC()->countries->get_states( $countryName )[$ss];  
            } 
        }elseif(get_option( 'v_city', 'yes' ) == 'yes'){
            $output = $this->getAllCity(array('val' =>$countryName, 'using' => 'country'));
        }else{
            $output = $this->getAllNaigerbor(array('value' =>$countryName, 'using' => 'country'));
        }
        $msg = (isset($output))?'success':'fail';
        echo json_encode( array(
                'message' => $msg, 
                'output' => $output
        ) );
        
        die();
    } // End getDatausingCountry()


    function typeDeliveryAutoFill(){
        $req = $_REQUEST['term'];
        $qerDeliver = 'SELECT * FROM '.$this->easy_shipping.' es LEFT JOIN '.$this->easy_ziptable.' ez ON ez.`s_id`=es.`id` WHERE es.`delivery_area` LIKE "%'.$_REQUEST['term'].'%" OR ez.`zipcode` LIKE "%'.$_REQUEST['term'].'%" OR es.`city` LIKE "%'.$_REQUEST['term'].'%" ORDER BY es.`delivery_area` ASC';
        $eQuery = $this->wpdb->get_results($qerDeliver, OBJECT);

        $output = array();

        foreach($eQuery as $sd){
                $otp = WC()->countries->countries[ $sd->country_name ] . ' > ' . WC()->countries->get_states($sd->country_name )[$sd->state] . ' > ' . $sd->city . ' > ' . $sd->delivery_area;
                if($sd->zipcode != '') $otp .= ' > ' . $sd->zipcode;    
                $output[] = $otp;
        }
        echo json_encode( $output );
        die();
    } //end typeDeliveryAutoFill();


    /*
    * popupaction2
    */
    function popupaction2(){
        $post = $_POST;

        $qry = 'SELECT `country_name`, `state`, `city`, `delivery_area` FROM '.$this->easy_shipping.' WHERE delivery_area !=""';
        if($post['country'] != '') $qry.=' AND country_name="'.$post['country'].'"';
        if($post['state'] != '') $qry.=' AND state="'.$post['state'].'"';
        if($post['city'] != '') $qry.=' AND city="'.$post['city'].'"';
        if($post['area'] != '') $qry.=' AND delivery_area="'.$post['area'].'"';
        $query = $this->wpdb->get_row($qry, OBJECT);
        $msg = ($query)?'success':'fail';
   
        echo json_encode(
            array(
                'message' => $msg,
                'qry' => $query
            )
        );
        die();
    }

    /*
    * Popup 2 Action
    */
    function popuptAction(){
        $vals = explode(' > ', $_REQUEST['val']);
        $endvalue = end($vals);

        $qry = 'SELECT `country_name`, `state`, `city`, `delivery_area` FROM '.$this->easy_shipping.' es';
        if(count($vals) > 4):
            $cityname = $vals[count($vals) - 3];
            $qry .= ' LEFT JOIN '.$this->easy_ziptable.' ez ON es.`id`=ez.`s_id` WHERE ez.`zipcode`="'.$endvalue.'"';
        else:
            $cityname = $vals[count($vals) - 2];
            $qry .= ' WHERE es.`delivery_area`="'.$endvalue.'"';
        endif;
        $qry .= ' AND es.`city`="'.$cityname.'" ORDER BY es.`delivery_area` ASC';

        $rowQry = $this->wpdb->get_row($qry, OBJECT);
        
        echo json_encode( 
            array(
                'message' => 'success',
                'qry' => $rowQry
            )
        );

        die();
    }

    function set_custom_edit_shop_order_columns($columns){
        $columns['city'] = 'City';
        return $columns;
    }

    function custom_shop_order_column( $column, $post_id ) {
        switch ( $column ) {

            case 'city' :
                $city = get_post_meta( $post_id, '_shipping_city', true );
                if ( $city  )
                    echo $city;
                else
                    _e( 'Unable to get City(s)', 'easy' );
            break;

        }
    } // End custom_shop_order_column()


    /*
    * Change multiple area in single action
    */
    function changeneighborAreaMultiple(){
        $post = $_REQUEST;
        foreach($post['dbids'] as $sid):
        $update = $this->wpdb->update(
            $this->easy_shipping, 
            array('delivery_area' => $post['areaname']),
            array('id' => $sid),
            array('%s'),
            array('%d')
        );
        endforeach;
        echo json_encode(
            array(
                'message' => 'success'
            )
        );
        die();
    } //End

    /*
    * Search Admin list
    */
    function adminSearchDB(){

        $qerDeliver = 'SELECT * FROM '.$this->easy_shipping.' es LEFT JOIN '.$this->easy_ziptable.' ez ON es.`id`=ez.`s_id` WHERE es.`delivery_area` LIKE "%'.$_REQUEST['search'].'%" OR ez.`zipcode` LIKE "%'.$_REQUEST['search'].'%" OR es.`city` LIKE "%'.$_REQUEST['search'].'%" GROUP BY es.`id` ORDER BY es.`delivery_area` ASC';
        $eQuery = $this->wpdb->get_results($qerDeliver, OBJECT);
        foreach($eQuery as $k => $sQ){
            $eQuery[$k]->state          =  WC()->countries->get_states( $sQ->country_name )[$sQ->state];
            $eQuery[$k]->country_name   =  WC()->countries->countries[$sQ->country_name];
        }
        echo json_encode(
            array(
                'message' => 'success',
                'results' => $eQuery
            )
        );
        die();
    }

    function get_total_volume(){
        $total_volume = 0;
    
         // Loop through cart items and calculate total volume
        foreach( WC()->cart->get_cart() as $cart_item ){
            $product_volume = (float) get_post_meta( $cart_item['product_id'], '_item_volume', true );
            $total_volume  += $product_volume * $cart_item['quantity'];
        }
        return $total_volume;
    }

    function replace_order_button_html( $order_button ) {
        $country    = $_COOKIE['easy_country'];
        $state      = $_COOKIE['easy_state'];
        $city       = $_COOKIE['easy_city'];
        $area       = $_COOKIE['easy_area'];

        $cartSubTotal = WC()->cart->subtotal;
        $quryRate = $this->wpdb->get_row('SELECT `min_amount`, `max_amount` FROM '.$this->easy_shipping.' WHERE country_name="'.$country.'" AND state like "%'.$state.'%" AND city="'.$city.'" AND delivery_area="'.$area.'"', OBJECT);
        if( $cartSubTotal > $quryRate->min_amount ){
            return $order_button;
        }else{
            $more = $quryRate->min_amount - $cartSubTotal;
            $order_button_text = __( "Add more ".wc_price($more)." to accept order", "easy" );    
            $style = ' style="color:#fff;cursor:not-allowed;background-color:#999;"';
            return '<a class="button alt"'.$style.' name="woocommerce_checkout_place_order" id="place_order" >' .  $order_button_text  . '</a>';
        }

    }

    // Update shipping data 
    function updateEasyShippingListData(){
        switch($_POST['name']){
            case 'zipcode':
                $zipArray = explode(',',$_POST['value']);
                $this->wpdb->delete(
                    $this->easy_ziptable,
                    array('s_id' => $_POST['id']),
                    array('%d')
                );
                foreach($zipArray as $szip):
                    $this->wpdb->insert(
                        $this->easy_ziptable,
                        array(
                            'zipcode' => $szip,
                            's_id' => $_POST['id']
                        ),
                        array('%s', '%d')
                    );
                endforeach;
            break;
            default:
            $update = $this->wpdb->update(
                $this->easy_shipping,
                array(
                    $_POST['name'] => $_POST['value']
                ),
                array('id' => $_POST['id']),
                array('%s'),
                array('%d')
            );
        }

        echo json_encode(
            array(
                'msg' => 'success',
                'post' => $_POST
            )
        );
        die();
    }


    /*
    * Woocommerce Template path
    */
    function woo_easy_plugin_template( $template, $template_name, $template_path ) {
        global $woocommerce;
        $_template = $template;
        if ( ! $template_path ) 
           $template_path = $woocommerce->template_url;
    
        $plugin_path  = untrailingslashit( $this->plugin_dir )  . '/woocommerce/templates/';
    
       // Look within passed path within the theme - this is priority
       $template = locate_template(
       array(
         $template_path . $template_name,
         $template_name
       )
      );
    
      if( ! $template && file_exists( $plugin_path . $template_name ) ){
       $template = $plugin_path . $template_name;
      }
    
      if ( ! $template )
       $template = $_template;
   
      return $template;
   }

    } // End Class
} // End Class check if exist / not 