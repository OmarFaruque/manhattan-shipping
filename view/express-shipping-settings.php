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
	<a href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings&tab=easy_shipping' ) ); ?>"><?php esc_html_e( 'Shipping zones', 'easy' ); ?></a> &gt;
	<span class="wc-shipping-zone-name"><?php _e('Express Settings', 'easy'); ?></span>
</h2>
<?php do_action( 'woocommerce_shipping_zone_before_methods_table' ); ?>

<table id="expressTable" class="form-table wc-shipping-zone-settings">
	<tbody>
            <tr>
            <td colspan="5">
                <?php _e('Add Express time slot with specific date. Each time slot are applicable to next two weeks.', 'easy'); ?>
            </td>
            </tr>
            <tr>
                <td><?php _e('Date', 'easy'); ?></td>
                <td><?php _e('Start Time', 'easy'); ?></td>
                <td><?php _e('End time', 'easy'); ?></td>
                <td><?php _e('Order Limit', 'easy'); ?></td>
            </tr>
			<tr valign="top" class="">
				<td colspan="5">
                    <p class="addTimeSlot">
                        <span class="dashicons dashicons-plus-alt"></span>
                    </p>
                </td>
			</tr>

	</tbody>
</table>
<br>
<p class="submit">
	<button name="active_save" id="easy_settings_save" class="button-primary woocommerce-save-button" type="submit" value="Save changes"><?php _e('Save settings', 'easy'); ?></button>
</p>

<?php do_action( 'woocommerce_shipping_zone_after_methods_table' ); ?>