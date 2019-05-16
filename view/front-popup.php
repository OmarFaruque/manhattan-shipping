<?php 
/*
* Front Popup
*/

$setArea = (isset($_COOKIE['easy_area']))?'set_true':'set_false';
$homepage = ($home)?'home':'';
$permalinkcity = (isset($extdata))?$extdata->city:''; 
/*
echo 'cookies:<pre>';
print_r($_COOKIE);
echo '</pre>';*/

echo '<script>
  var easyCooki = {
    setarea: "'.$setArea.'",
    home: "'.$homepage.'",
    homeurl: "'.home_url().'",
    v_country:"'.get_option( 'v_country', 'yes' ).'",
    v_state:"'.get_option( 'v_state', 'yes' ).'",
    v_city:"'.get_option( 'v_city', 'yes' ).'",
    v_popup2:"'.get_option( 'v_popup2', 'yes' ).'",
    permalinkcity:"'.$permalinkcity.'"
  };
</script>';



if(isset($_COOKIE['easy_state'])){
  $rates = $this->wpdb->get_row('SELECT `charge`, `active_express`, `express_delivery` FROM '.$this->easy_shipping.' WHERE country_name="'.$_COOKIE['easy_country'].'" AND state like "%'.$_COOKIE['easy_state'].'%" AND city="'.$_COOKIE['easy_city'].'" AND delivery_area="'.$_COOKIE['easy_area'].'"', OBJECT);
  $output = '<div id="deliverAreaReset"><div class="innerD text-right">';
      $output.=($_COOKIE['easy_state'] != 'undefined')?'<span class="state"><b><i>State: '.WC()->countries->get_states( $_COOKIE['easy_country'] )[$_COOKIE['easy_state']].'</i></b></span>':'';
      $output.='<span class="city"><b><i>City: '.$_COOKIE['easy_city'].'</span></i></b>
      <span class="area"><b><i>Delivery Area: '.$_COOKIE['easy_area'].'</span></i></b>';
      if($rates->charge > 0):
      $output.='<span class="deliveryCharge"><b><i>'.__('Normal Delivery', 'easy').': '.wc_price($rates->charge).'</span></i></b>';
      else:
        $output.='<span class="deliveryCharge"><b><i>'.__('Free Normal Delivery', 'easy').'</span></i></b>';
      endif;
      $expressDelivery = ($rates->active_express == 1) ? wc_price($rates->express_delivery) : 'Not Offered';
      if($rates->active_express == 1 && $rates->express_delivery <= 0) $expressDelivery = 'Free Express Delivery';
      $output.='<span class="deliveryCharge"><b><i>'.__('Express Delivery', 'easy').': '.$expressDelivery.'</span></i></b>
      <span id="changeDeliverAreay"><button type="button" class="btn btn-primary">'.__('Change', 'easy').'</button></span>
  </div></div>';  
}else{
  $output ='';
}

$output .= '<div style="display:none;" id="easyshippingWrap"><div class="deliveryInnter"><div class="delivery">
             <form class="deliverAreaForm" name="deliverAreaForm" id="deliverAreaForm">';
             
              require_once(SHIPPINGDIR . 'view/popup-part-'.get_option( 'v_popup2', 'yes' ).'.php');

              $output.='</form>
            </div></div></div>';
           echo $output;


