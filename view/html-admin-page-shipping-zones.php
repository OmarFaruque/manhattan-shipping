<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<h2 class="wc-shipping-zones-heading">
	<?php _e( 'Shipping zones', 'woocommerce' ); ?>
	<a href="<?php echo admin_url( 'admin.php?page=wc-settings&tab=easy_shipping&zone_id=new' ); ?>" class="page-title-action"><?php esc_html_e( 'Add shipping zone', 'woocommerce' ); ?></a>

	<a href="<?php echo admin_url( 'admin.php?page=wc-settings&tab=easy_shipping&easy-settings=1' ); ?>" class="page-title-action"><?php esc_html_e( 'Settings', 'woocommerce' ); ?></a>
</h2>
<p><?php echo __( 'A shipping zone is a geographic region where a certain set of shipping methods are offered.', 'woocommerce' ) . ' ' . __( 'WooCommerce will match a customer to a single zone using their shipping address and present the shipping methods within that zone to them.', 'woocommerce' ); ?></p>
<p><fieldset><label><input type="checkbox" <?php echo ($status == 1)?'checked':''; ?> value="1" name="active_easy" id="active_easy"/>&nbsp;<?php _e('Active Easy Shipping', 'woocommerce'); ?></label></fieldset></p>
<table class="wc-shipping-zones widefat omar">
	<thead>
		<tr>
			<th class="wc-shipping-zone-sort"><?php echo wc_help_tip( __( 'This is the Delivery area where a user got there product.', 'woocommerce' ) ); ?></th>
			<th class="wc-shipping-zone-name"><?php esc_html_e( 'Area name', 'woocommerce' ); ?></th>
			<th class="wc-shipping-zone-city"><?php esc_html_e( 'City name', 'woocommerce' ); ?></th>
			<th class="wc-shipping-zone-state"><?php esc_html_e( 'State', 'woocommerce' ); ?></th>
			<th class="wc-shipping-zone-min"><?php esc_html_e( 'Min', 'woocommerce' ); ?></th>
			<th class="wc-shipping-zone-max"><?php esc_html_e( 'Max', 'woocommerce' ); ?></th>
			<th class="wc-shipping-zone-charge"><?php esc_html_e( 'Delivery Charge', 'woocommerce' ); ?></th>
		</tr>
	</thead>
	<tbody class="wc-shipping-zone-rows">
		<?php 
		$output ='';
		foreach($allships as $s_ship){
			$output .= '<tr data-id="'.$s_ship->id.'">';
			$output .= '<td width="1%" class="wc-shipping-zone-sort"></td>';
			$output .= '<td class="wc-shipping-zone-name">
					<a href="admin.php?page=wc-settings&amp;tab=easy_shipping&amp;zone_id='.$s_ship->id.'">'.$s_ship->delivery_area.'</a>
					<div class="row-actions">
						<a href="admin.php?page=wc-settings&amp;tab=easy_shipping&amp;zone_id='.$s_ship->id.'">'. __( 'Edit', 'woocommerce' ).'</a> | <a href="#" class="easy_shipping_d wc-shipping-zone-delete">'. __( 'Delete', 'woocommerce' ).'</a>
					</div>
					</td>';
			$output .= '<td class="wc-shipping-city">'.$s_ship->city.'</td>';
			$output .= '<td class="wc-shipping-state">'.WC()->countries->get_states( $s_ship->country_name )[$s_ship->state].'</td>';
			$output .= '<td width="1%" class="wc-shipping-min">'.get_woocommerce_currency_symbol(). $s_ship->min_amount.'</td>';
			$output .= '<td width="1%" class="wc-shipping-max">'.get_woocommerce_currency_symbol().$s_ship->max_amount.'</td>';
			$output .= '<td class="wc-shipping-charge">'.get_woocommerce_currency_symbol().$s_ship->charge.'</td>';
			$output .= '</tr>';
		}
		echo $output;
		?>
	</tbody>
	<tfoot>
		<tr>
			<th class="wc-shipping-zone-sort"><?php echo wc_help_tip( __( 'This is the Delivery area where a user got there product.', 'woocommerce' ) ); ?></th>
			<th class="wc-shipping-zone-name"><?php esc_html_e( 'Area name', 'woocommerce' ); ?></th>
			<th class="wc-shipping-zone-city"><?php esc_html_e( 'City name', 'woocommerce' ); ?></th>
			<th class="wc-shipping-zone-state"><?php esc_html_e( 'State', 'woocommerce' ); ?></th>
			<th class="wc-shipping-zone-min"><?php esc_html_e( 'Min', 'woocommerce' ); ?></th>
			<th class="wc-shipping-zone-max"><?php esc_html_e( 'Max', 'woocommerce' ); ?></th>
			<th class="wc-shipping-zone-charge"><?php esc_html_e( 'Delivery Charge', 'woocommerce' ); ?></th>
		</tr>
	</tfoot>
</table>

<p class="submit">
	<button name="active_save" id="active_easy_save" class="button-primary woocommerce-save-button" type="submit" value="Save changes">Save changes</button>
</p>



