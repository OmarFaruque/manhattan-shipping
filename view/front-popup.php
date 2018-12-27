<?php 
/*
* Front Popup
*/

$setArea = (isset($_COOKIE['easy_area']))?'set_true':'set_false';
$homepage = ($home)?'home':'';
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
    permalinkcity:"'.$extdata->city.'"
  };
</script>';



if(isset($_COOKIE['easy_state'])){
  $output = '<div id="deliverAreaReset"><div class="innerD">';
      $output.=($_COOKIE['easy_state'] != 'undefined')?'<span class="state"><b>State: </b>'.WC()->countries->get_states( $_COOKIE['easy_country'] )[$_COOKIE['easy_state']].'</span>':'';
      $output.='<span class="city"><b>City: </b>'.$_COOKIE['easy_city'].'</span>
      <span class="area"><b>Deliver Area: </b>'.$_COOKIE['easy_area'].'</span>
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


