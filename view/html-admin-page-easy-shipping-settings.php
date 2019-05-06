<?php
/**
 * Shipping zone admin
 *
 * @package WooCommerce/Admin/Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$cutofftime = get_option('cut_off_time');

?>
<h2>
	<a href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings&tab=easy_shipping' ) ); ?>"><?php esc_html_e( 'Shipping zones', 'easy' ); ?></a> &gt;
	<span class="wc-shipping-zone-name"><?php _e('Settings', 'easy'); ?></span>
</h2>
<?php do_action( 'woocommerce_shipping_zone_before_methods_table' ); ?>

<table class="form-table wc-shipping-zone-settings">
	<tbody>
			<tr valign="top" class="">
				<th scope="row" class="titledesc">
					<label for="zone_name">
						<?php esc_html_e( 'Visiable POPUP Style 2 ?', 'easy' ); ?>
						<?php echo wc_help_tip( __( 'By default popup style 1 is activated.', 'easy' ) ); // @codingStandardsIgnoreLine ?>
					</label>
				</th>
				<td class="forminp easyShipping">
					<label class="switch">
					  <input type="checkbox" name="v_popup2" <?php echo (get_option( 'v_popup2', 'no' ) == 'yes')?'checked="checked"':'';?> >
					  <span class="slider round"></span>
					</label>
				</td>
			</tr>
			<tr valign="top" class="forstyle1">
				<th scope="row" class="titledesc">
					<label for="zone_name">
						<?php esc_html_e( 'Visiable Country ?', 'easy' ); ?>
						<?php echo wc_help_tip( __( 'This the option for visiable country for popup element.', 'easy' ) ); // @codingStandardsIgnoreLine ?>
					</label>
				</th>
				<td class="forminp easyShipping">
					<label class="switch">
					  <input type="checkbox" name="v_country" <?php echo (get_option( 'v_country', 'yes' ) == 'yes')?'checked="checked"':'';?>>
					  <span class="slider round"></span>
					</label>
				</td>
			</tr>
			<tr valign="top" class="forstyle1">
				<th scope="row" class="titledesc">
					<label for="zone_locations">
						<?php esc_html_e( 'Visiable State ?', 'woocommerce' ); ?>
						<?php echo wc_help_tip( __( 'These are regions inside this zone. Customers will be matched against these regions.', 'woocommerce' ) ); // @codingStandardsIgnoreLine ?>
					</label>
				</th>
				<td class="forminp">
					<label class="switch">
					  <input type="checkbox" name="v_state" <?php echo (get_option( 'v_state', 'yes' ) == 'yes')?'checked="checked"':'';?>>
					  <span class="slider round"></span>
					</label>
				</td>
		</tr>
		<tr valign="top" class="">
				<th scope="row" class="titledesc">
					<label for="zone_city">
						<?php esc_html_e( 'City', 'easy' ); ?>
						<?php echo wc_help_tip( __( 'These are regions inside this zone. Customers will be matched against these regions.', 'easy' ) ); // @codingStandardsIgnoreLine ?>
					</label>
				</th>
				<td class="forminp">
					<label class="switch">
					  <input type="checkbox" name="v_city" <?php echo (get_option( 'v_city', 'yes' ) == 'yes')?'checked="checked"':'';?>>
					  <span class="slider round"></span>
					</label>
				</td>			
		</tr>
		<tr valign="top" class="">
				<th scope="row" class="titledesc">
					<label for="express_note">
						<?php esc_html_e( 'Express Delivery Note', 'easy' ); ?>
						<?php echo wc_help_tip( __( 'Express Delivery or Same day deliery note on cart page.', 'easy' ) ); // @codingStandardsIgnoreLine ?>
					</label>
				</th>
				<td class="forminp">
					<input type="text" style="width:100%;" name="express_note" id="express_note" value="<?php echo get_option( 'express_note', '' ); ?>" class="form-control">
				</td>			
		</tr>

		<tr valign="top" class="">
				<th scope="row" class="titledesc">
					<label for="express_note">
						<?php esc_html_e( 'Cut off time', 'easy' ); ?>
						<?php echo wc_help_tip( __( 'Express Delivery cut off time.', 'easy' ) ); // @codingStandardsIgnoreLine ?>
					</label>
				</th>
				<td class="forminp">
					<input type="time" name="cut_off_time" id="cut_off_time" value="<?php echo get_option( 'cut_off_time', '' ); ?>" class="form-control">
				</td>			
		</tr>


	</tbody>
</table>
<br>
<p class="submit">
	<button name="active_save" id="easy_settings_save" class="button-primary woocommerce-save-button" type="submit" value="Save changes"><?php _e('Save settings', 'easy'); ?></button>
</p>

<?php do_action( 'woocommerce_shipping_zone_after_methods_table' ); ?>