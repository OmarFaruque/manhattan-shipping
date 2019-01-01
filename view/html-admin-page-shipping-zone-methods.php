<?php
/**
 * Shipping zone admin
 *
 * @package WooCommerce/Admin/Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<h2>
	<a href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings&tab=easy_shipping' ) ); ?>"><?php esc_html_e( 'Shipping zones', 'woocommerce' ); ?></a> &gt;
	<span class="wc-shipping-zone-name"><?php echo ($_REQUEST['zone_id'] != 'new')?$exstZone->delivery_area:''; ?></span>
</h2>
<?php do_action( 'woocommerce_shipping_zone_before_methods_table' ); ?>

<table class="form-table wc-shipping-zone-settings">
	<tbody>
		<?php //if ( 0 !== $zone->get_id() ) : ?>
			<tr valign="top" class="">
				<th scope="row" class="titledesc">
					<label for="zone_name">
						<?php esc_html_e( 'Country Name', 'woocommerce' ); ?>
						<?php echo wc_help_tip( __( 'This is the name of the country for your reference.', 'woocommerce' ) ); // @codingStandardsIgnoreLine ?>
					</label>
				</th>
				<td class="forminp easyShipping">
					<select name="country_name" data-attribute="zone_name" id="zone_name" data-placeholder="<?php esc_html_e( 'Select country within this zone', 'woocommerce' ); ?>" class="wc-shipping-zone-region-select chosen_select">
						<?php 
							foreach($allowed_countries as $k => $sCntry){
								$selected = ($_REQUEST['zone_id'] != 'new' && $k == $exstZone->country_name || $k == 'US')?'selected':'';
								echo '<option '.$selected.' value="'.$k.'" >'.$sCntry.'</option>';
							}
						?>
					</select>
				</td>
			</tr>
			<tr valign="top" class="">
				<th scope="row" class="titledesc">
					<label for="zone_locations">
						<?php esc_html_e( 'State', 'woocommerce' ); ?>
						<?php echo wc_help_tip( __( 'These are regions inside this zone. Customers will be matched against these regions.', 'woocommerce' ) ); // @codingStandardsIgnoreLine ?>
					</label>
				</th>
				<td class="forminp">
					<select data-attribute="zone_locations" id="zone_locations" name="zone_locations" data-placeholder="<?php esc_html_e( 'Select regions within this zone', 'woocommerce' ); ?>" class="wc-shipping-zone-region-select chosen_select">
						<?php
						foreach($states as $k => $sStt){
							$stctd = ($_REQUEST['zone_id'] != 'new' && $k == $exstZone->state)?'selected':'';
							echo '<option '.$stctd.' value="'.$k.'">'.$sStt.'</option>';
						}
						?>
					</select>
				</td>
		</tr>
		<tr valign="top" class="">
				<th scope="row" class="titledesc">
					<label for="zone_city">
						<?php esc_html_e( 'City', 'woocommerce' ); ?>
						<?php echo wc_help_tip( __( 'These are regions inside this zone. Customers will be matched against these regions.', 'woocommerce' ) ); // @codingStandardsIgnoreLine ?>
					</label>
				</th>
				<td class="forminp">
					<input type="text" data-attribute="zone_city" id="zone_city" name="city" placeholder="City Name" data-placeholder="<?php esc_html_e( 'Select regions within this zone', 'easy' ); ?>" value="<?php echo ($_REQUEST['zone_id'] != 'new')?$exstZone->city:''; ?>" class="wc-shipping-zone-region-select" />
				</td>
			
		</tr>
		<!-- Delivery Area -->
		<tr valign="top" class="">
				<th scope="row" class="titledesc">
					<label for="delivery_area">
						<?php esc_html_e( 'Delivery Area', 'easy' ); ?>
						<?php echo wc_help_tip( __( 'Specific Delivery Area in Selected city. Use | for seperate multiple delivery area', 'easy' ) ); // @codingStandardsIgnoreLine ?>
					</label>
				</th>
				<td class="forminp">
					<input type="text" data-attribute="delivery_area" id="delivery_area" name="delivery_area" placeholder="Area 1 | Area 2 | Area 3"  value="<?php echo ($_REQUEST['zone_id'] != 'new')?$exstZone->delivery_area:''; ?>" class="wc-shipping-zone-region-select" />
				</td>
		</tr>

		<!-- Zipcode -->
		<tr valign="top" class="">
				<th scope="row" class="titledesc">
					<label for="delivery_zipcode">
						<?php esc_html_e( 'Zipcode', 'easy' ); ?>
						<?php echo wc_help_tip( __( 'If have multiple zipcode seperate each zipcode using (,) comma. If you have multiple Neighborhood seperate each zip bundle with (|)', 'easy' ) ); // @codingStandardsIgnoreLine ?>
					</label>
				</th>
				<td class="forminp">
					<input type="text" data-attribute="delivery_zipcode" id="delivery_zipcode" name="zipcode" placeholder="000000, 000000 | 000000, 000000"  value="<?php echo ($_REQUEST['zone_id'] != 'new')?implode(', ', $zipcodes):''; ?>" class="wc-shipping-zone-region-select" />
				</td>
		</tr>
		
		<!-- Min -->
		<tr valign="top" class="">
				<th scope="row" class="titledesc">
					<label for="min_amount">
						<?php esc_html_e( 'Min Amount', 'woocommerce' ); ?>
						<?php echo wc_help_tip( __( 'Minimum ammount of money.', 'woocommerce' ) ); // @codingStandardsIgnoreLine ?>
					</label>
				</th>
				<td class="forminp">
					<input type="number" data-attribute="min_amount" step="0.01" id="min_amount" name="min_amount" placeholder="Min Amount of money"  value="<?php echo ($_REQUEST['zone_id'] != 'new')?$exstZone->min_amount:''; ?>" class="wc-shipping-zone-region-select" />
				</td>
		</tr>

		<!-- Max -->
		<tr valign="top" class="">
				<th scope="row" class="titledesc">
					<label for="max_amount">
						<?php esc_html_e( 'Max Amount', 'woocommerce' ); ?>
						<?php echo wc_help_tip( __( 'Maximum ammount of money.', 'woocommerce' ) ); // @codingStandardsIgnoreLine ?>
					</label>
				</th>
				<td class="forminp">
					<input type="number" data-attribute="max_amount" step="0.01" id="max_amount" name="max_amount" placeholder="Max Amount of money"  value="<?php echo ($_REQUEST['zone_id'] != 'new')?$exstZone->max_amount:''; ?>" class="wc-shipping-zone-region-select" />
				</td>
		</tr>



		<tr valign="top" class="">
				<th scope="row" class="titledesc">
					<label for="delivery_charge">
						<?php esc_html_e( 'Delivery Charge', 'woocommerce' ); ?>
						<?php echo wc_help_tip( __( 'These are regions inside this zone. Customers will be matched against these regions.', 'woocommerce' ) ); // @codingStandardsIgnoreLine ?>
					</label>
				</th>
				<td class="forminp">
					<input type="number" data-attribute="delivery_charge" id="delivery_charge" step="0.01" name="delivery_charge" placeholder="Delivery Charge"  value="<?php echo ($_REQUEST['zone_id'] != 'new')?$exstZone->charge:''; ?>" class="wc-shipping-zone-region-select" />
				</td>
		</tr>
		<?php if(isset($_REQUEST['zone_id']) && $_REQUEST['zone_id'] != 'new'): ?>
		<tr>
			<th>
				<?php echo _e('Permalink', 'easy');  ?>
			</th>
			<td class="forminp">
				<?php echo get_home_url() . '/?cid=' . $exstZone->id; ?>
			</td>
		</tr>
		<?php endif; ?>
	</tbody>
</table>

<?php do_action( 'woocommerce_shipping_zone_after_methods_table' ); ?>

<p class="submit">
	<button type="submit" name="submit" id="<?php echo ($_REQUEST['zone_id'] == 'new')?'easy_submit':'easy_update'; ?>" class="button button-primary button-large wc-shipping-zone-method-save" value="<?php esc_attr_e( 'Save changes', 'woocommerce' ); ?>" <?php echo ($_REQUEST['zone_id'] == 'new')?'disabled':'data-upid="'.$exstZone->id.'"'; ?>><?php ($_REQUEST['zone_id'] == 'new')?esc_html_e( 'Save changes', 'woocommerce' ):esc_html_e( 'Update changes', 'woocommerce' ); ?></button>
</p>





