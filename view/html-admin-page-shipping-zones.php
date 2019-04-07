<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$continents = WC()->countries->get_continents(); 

$countrys = array(); 
foreach($continents as $sk){
	foreach($sk['countries'] as $sc) array_push($countrys, $sc);
} 

?>
<?php 
	echo '<pre>';
	print_r($allships);
	echo '</pre>';
?>

<h2 class="wc-shipping-zones-heading">
	<?php _e( 'Shipping zones', 'easy' ); ?>
	<a href="<?php echo admin_url( 'admin.php?page=wc-settings&tab=easy_shipping&zone_id=new' ); ?>" class="page-title-action"><?php esc_html_e( 'Add shipping zone', 'woocommerce' ); ?></a>

	<a href="<?php echo admin_url( 'admin.php?page=wc-settings&tab=easy_shipping&easy-settings=1' ); ?>" class="page-title-action"><?php esc_html_e( 'Settings', 'woocommerce' ); ?></a>
</h2>
<p><?php echo __( 'A shipping zone is a geographic region where a certain set of shipping methods are offered.', 'woocommerce' ) . ' ' . __( 'WooCommerce will match a customer to a single zone using their shipping address and present the shipping methods within that zone to them.', 'woocommerce' ); ?></p>

<div class="easy left">
	<p><fieldset><label><input type="checkbox" <?php echo ($status == 1)?'checked':''; ?> value="1" name="active_easy" id="active_easy"/>&nbsp;<?php _e('Active Normal Delivery', 'woocommerce'); ?></label></fieldset></p>
</div>
<div class="easy right">
	<!--<div class="form-group">
		<input type="text" name="search_easy" id="search_easy">
		<button class="button button-primary" id="easy_earch_button" type="submit"><?php _e('Search', 'easy'); ?></button>
	</div>-->
</div>

<table id="dataTable" class="wc-shipping-zones lists widefat">
	<thead>
		<tr>
			<th class="wc-shipping-zone-select">
				<input style="margin-left:0;" type="checkbox" name="selectalleasy" id="selectalleasy" />
			</th>
			<th class="wc-shipping-zone-name"><?php esc_html_e( 'Area name', 'easy' ); ?></th>
			<th class="wc-shipping-zone-zipcode"><?php esc_html_e( 'Ziocodes', 'easy' ); ?></th>
			<th class="wc-shipping-zone-city"><?php esc_html_e( 'City name', 'easy' ); ?></th>
			<th class="wc-shipping-zone-state"><?php esc_html_e( 'State', 'easy' ); ?></th>
			<th class="wc-shipping-zone-country"><?php esc_html_e( 'Country', 'easy' ); ?></th>
			<th class="wc-shipping-zone-min"><?php esc_html_e( 'Min', 'easy' ); ?></th>
			<th class="wc-shipping-zone-max"><?php esc_html_e( 'Max', 'easy' ); ?></th>
			<th class="wc-shipping-zone-charge"><?php esc_html_e( 'Delivery Charge', 'easy' ); ?></th>
			<th class="wc-shipping-zone-express"><?php esc_html_e( 'Express Delivery', 'easy' ); ?></th>
		</tr>
	</thead>
	<tbody class="wc-shipping-zone-rows">
		<?php 
		$output ='';
		foreach($allships as $s_ship){
			$expressActive = ($s_ship->active_express == 1)?'checked':'';
			$allzip = $this->getareaZipCodes($s_ship->id);
			$zopcodes = implode(', ', $allzip);
			$output .= '<tr data-id="'.$s_ship->id.'">';
			$output .= '<td width="1%" class="wc-shipping-zone-select">
					<input value="'.$s_ship->id.'" type="checkbox" name="selecteasy[]" />
				</td>';
			$output .= '<td class="wc-shipping-zone-name">
					<a href="admin.php?page=wc-settings&amp;tab=easy_shipping&amp;zone_id='.$s_ship->id.'">'.$s_ship->delivery_area.'</a>
					<div class="row-actions">
						<a class="ajax edit" data-name="delivery_area" href="#">'. __( 'Edit', 'woocommerce' ).'</a> 
						| 
						<a href="#" class="easy_shipping_d wc-shipping-zone-delete">'. __( 'Delete', 'woocommerce' ).'</a>
					</div>
					</td>';
			$output .= '<td data-name="zipcode" class="wc-shipping-zipcodes zipcode">
						<span class="data">'.$zopcodes.'</span>
						<div class="row-actions">
							<a class="ajax edit" href="#">'. __( 'Edit', 'woocommerce' ).'</a>
						</div>
						<div class="form-group hidden">
							<input class="form-control" type="text" value="'.$zopcodes.'" name="zipcode" />
							<span class="savebutton"><span class="dashicons dashicons-yes"></span></span>
						</div>
			</td>';
			$output .= '<td class="wc-shipping-city city">
						<span class="data">'.$s_ship->city.'</span>
						<div class="row-actions">
							<a class="ajax edit" href="#">'. __( 'Edit', 'woocommerce' ).'</a>
						</div>
						<div class="form-group hidden">
							<input class="form-control" type="text" value="'.$s_ship->city.'" name="city" />
							<span class="savebutton"><span class="dashicons dashicons-yes"></span></span>
						</div>
			</td>';
			$output .= '<td class="wc-shipping-state">
						<span class="data">'.WC()->countries->get_states( $s_ship->country_name )[$s_ship->state].'</span>
						<div class="row-actions">
							<a class="ajax edit" href="#">'. __( 'Edit', 'woocommerce' ).'</a>
						</div>
						<div class="form-group hidden">
							<select name="state" class="form-control">';
							$states = WC()->countries->get_states( $s_ship->country_name );
							if(is_array($states) && count($states)){
								foreach($states as $k => $sState):
									$output.= '<option value="'.$k.'">'.$sState.'</option>';
								endforeach;
							}
							$output .='</select>
							<span class="savebutton"><span class="dashicons dashicons-yes"></span></span>
						</div>
			</td>';
			$output .= '<td class="wc-shipping-country">
						<span class="data">'.WC()->countries->countries[$s_ship->country_name].'</span>
						<div class="row-actions">
						<a class="ajax edit" href="#">'. __( 'Edit', 'woocommerce' ).'</a>
						</div>
						<div class="form-group hidden">
							<select name="country_name" class="form-control">';
							foreach($countrys as $k => $sCountry):
								$output.= '<option value="'.$sCountry.'">'.WC()->countries->countries[$sCountry].'</option>';
							endforeach;
							$output .='</select>
							<span class="savebutton"><span class="dashicons dashicons-yes"></span></span>
						</div>
						</td>';
			$output .= '<td width="1%" class="wc-shipping-min">
						<span class="data">'.get_woocommerce_currency_symbol(). $s_ship->min_amount.'</span>
						<div class="row-actions">
							<a class="ajax edit" href="#">'. __( 'Edit', 'woocommerce' ).'</a>
						</div>
						<div class="form-group hidden">
							<input class="form-control" type="text" value="'.$s_ship->min_amount.'" name="min_amount" />
							<span class="savebutton"><span class="dashicons dashicons-yes"></span></span>
						</div>
			</td>';
			$output .= '<td width="1%" class="wc-shipping-max">
						<span class="data">'.get_woocommerce_currency_symbol().$s_ship->max_amount.'</span>
						<div class="row-actions">
							<a class="ajax edit" href="#">'. __( 'Edit', 'woocommerce' ).'</a>
						</div>
						<div class="form-group hidden">
							<input class="form-control" type="text" value="'.$s_ship->max_amount.'" name="max_amount" />
							<span class="savebutton"><span class="dashicons dashicons-yes"></span></span>
						</div>
			</td>';
			$output .= '<td class="wc-shipping-charge">
			<span class="data">'.get_woocommerce_currency_symbol().$s_ship->charge.'</span>
						<div class="row-actions">
							<a class="ajax edit" href="#">'. __( 'Edit', 'woocommerce' ).'</a>
						</div>
						<div class="form-group hidden">
							<input class="form-control" type="text" value="'.$s_ship->charge.'" name="charge" />
							<span class="savebutton"><span class="dashicons dashicons-yes"></span></span>
						</div>
			</td>';
			
			//Express Delivery		

			$output .= '<td class="wc-shipping-express">
			<span class="activeExpressDelivery">
					<input name="active_express" '.$expressActive.' type="checkbox" value="1" />
			</span>
			<span class="data">'.get_woocommerce_currency_symbol().$s_ship->express_delivery.'</span>
						<div class="row-actions">
							<a class="ajax edit" href="#">'. __( 'Edit', 'woocommerce' ).'</a>
						</div>
						<div class="form-group hidden">
							<input class="form-control" type="text" value="'.$s_ship->express_delivery.'" name="express_delivery" />
							<span class="savebutton"><span class="dashicons dashicons-yes"></span></span>
						</div>
			</td>';
			$output .= '</tr>';
		}
		echo $output;
		?>
	</tbody>
	<tfoot>
		<tr>
			<th class="wc-shipping-zone-select">
				<input style="margin-left:0;" type="checkbox" name="selectalleasy" id="selectalleasy" />
			</th>
			<th class="wc-shipping-zone-name"><?php esc_html_e( 'Area name', 'easy' ); ?></th>
			<th class="wc-shipping-zone-zipcode"><?php esc_html_e( 'Ziocodes', 'easy' ); ?></th>
			<th class="wc-shipping-zone-city"><?php esc_html_e( 'City name', 'easy' ); ?></th>
			<th class="wc-shipping-zone-state"><?php esc_html_e( 'State', 'easy' ); ?></th>
			<th class="wc-shipping-zone-country"><?php esc_html_e( 'Country', 'easy' ); ?></th>
			<th class="wc-shipping-zone-min"><?php esc_html_e( 'Min', 'easy' ); ?></th>
			<th class="wc-shipping-zone-max"><?php esc_html_e( 'Max', 'easy' ); ?></th>
			<th class="wc-shipping-zone-charge"><?php esc_html_e( 'Delivery Charge', 'easy' ); ?></th>
			<th class="wc-shipping-zone-express"><?php esc_html_e( 'Express Delivery', 'easy' ); ?></th>
		</tr>
	</tfoot>
</table>

<p class="submit">
	<button name="active_save" id="active_easy_save" class="button-primary woocommerce-save-button" type="submit" value="Save changes">Save changes</button>
</p>



