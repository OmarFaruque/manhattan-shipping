<?php 
                   $output.='<label for="deliveryAreaSelect">Select your Delivery Area</label>';
                    if(get_option( 'v_country', 'yes' ) == 'yes'):
                    $readonly = (count($states) <= 1)?'disabled="disabled"':'';
                    $output .='<select '.$readonly.' name="country" class="chosen_select select2" id="country">';
                      $output .=(count($states) > 1)?'<option value="">'.__('Select Country', 'woocommerce').'...</option>':'';
                        foreach($states as $k => $sGroup){
                            
                            $output .='<option value="'.$k.'">'.WC()->countries->countries[ $k ].'</option>';
                        }
                    $output .='</select>';
                    endif;  // Enc Country

                    if(get_option( 'v_state', 'yes' ) == 'yes'):
                    $stateReadonly = (count($states, COUNT_RECURSIVE) <=2 || isset($extdata))?'disabled="disabled"':'';
                    $output .='<select '.$stateReadonly.' name="state" class="chosen_select select2" id="sate">';
                      $output .=(count($states, COUNT_RECURSIVE) >2)?'<option value="">'.__('Select State', 'woocommerce').'...</option>':'';
                        if(get_option( 'v_country', 'yes' ) != 'yes' || count($states) <= 1):
                        foreach($states as $k => $sGroup){
                            $output .='<optgroup label="'.WC()->countries->countries[ $k ].'">';
                            foreach($sGroup as $sOption){
                                $selected = (isset($extdata) && $sOption == $extdata->state)?'selected':'';
                                $output .='<option '.$selected.' data-cntrycode="'.$k.'" value="'.$sOption.'">'.WC()->countries->get_states( $k )[$sOption].'</option>';
                            }
                            $output .='</optgroup>';
                        }
                        endif;  

                    $output .='</select>';
                    endif;  
                    // End Country

                    if(get_option( 'v_city', 'yes' ) == 'yes'):
                    $dsabcity = (isset($extdata))?'disabled':''; 
                    $output .='<select '.$dsabcity.' id="city" name="city" class="select2" >
                        <option value="">'.__('Select City...', 'woocommerce').'...</option>';

                        if(isset($extdata)){
                            $output.='<option selected value="'.$extdata->city.'">'.$extdata->city.'</option>';
                        }
                        if(get_option( 'v_country', 'yes' ) == 'no' && get_option( 'v_state', 'yes' ) == 'no'){
                            foreach($allcities as $sC) $output.='<option data-cnt="'.$sC->country_name.'" value="'.$sC->city.'">'.$sC->city.'</option>';
                        }
                        
                    $output.='</select>';
                    endif;  
                    // End State selection

                    
                    $output.='<select id="area" name="area" class="select2">
                      <option value="">'.__('Area...', 'woocommerce').'</option>';
                      if(isset($_COOKIE['easy_area'])) $output.='<option selected value="'.$_COOKIE['easy_area'].'">'.$_COOKIE['easy_area'].'</option>';
                      if(isset($extdata)){
                        $deliveyAreas = $this->wpdb->get_results('SELECT `delivery_area` FROM '.$this->easy_shipping.' WHERE city="'.$extdata->city.'"', OBJECT);
                        foreach($deliveyAreas as $sa){
                            $output.='<option value="'.$sa->delivery_area.'">'.$sa->delivery_area.'</option>';
                        }
                      }
                    $output.='</select>';
                    // End Area Selection

                    $output .='<input class="btn dark-green" id="easy_locationSave" type="submit" disabled name="easy_submit" value="OK">';