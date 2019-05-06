<?php
/**
 * Shipping zone admin
 *
 * @package WooCommerce/Admin/Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$expreses = $this->wpdb->get_results('SELECT * FROM '.$this->table_slot.'', OBJECT);
// echo '<pre>';
// print_r($expreses);
// echo '</pre>';

?>
<h2>
	<a href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings&tab=easy_shipping' ) ); ?>"><?php esc_html_e( 'Shipping zones', 'easy' ); ?></a> &gt;
	<span class="wc-shipping-zone-name"><?php _e('Normal Delivery Settings', 'easy'); ?></span>
</h2>
<?php do_action( 'woocommerce_shipping_zone_before_methods_table' ); ?>

<table id="expressTable" class="form-table wc-express-zone-settings">
	<thead>
            <tr>
            <td colspan="7">
                <?php _e('Add time slot for Normal Delivery with specific date. Each time slot are applicable to next two weeks.', 'easy'); ?>
            </td>
            </tr>
            <tr>
                <th><?php _e('City', 'easy'); ?></th>
                <th><?php _e('Date', 'easy'); ?></th>
                <th><?php _e('Start Time', 'easy'); ?></th>
                <th><?php _e('End time', 'easy'); ?></th>
                <th><?php _e('Cut of time', 'easy'); ?></th>
                <th><?php _e('Order Limit', 'easy'); ?></th>
                <th><?php _e('Action', 'easy'); ?></th>
            </tr>
	</thead>
    <tbody>
    <?php foreach($expreses as $slot): ?>
    <tr>
        <td>
            <?php echo $this->citynamebyid($slot->city); ?>
        </td>
        <td>
            <?php echo date('d M, Y', strtotime($slot->slot_date)); ?>
        </td>
        <td>
            <?php echo date('h:i a', strtotime($slot->s_time)); ?>
        </td>
        <td>
            <?php echo date('h:i a', strtotime($slot->e_time)); ?>
        </td>
        <td>
            <?php echo date('d M, Y - h:i a', strtotime($slot->cut_off)); ?>
        </td>
        <td>
            <?php echo $slot->order_limit; ?>
        </td>
        <td data-id="<?php echo $slot->id; ?>" class="delete"><span class="dashicons dashicons-dismiss"></span></td>
    </tr>
    <?php endforeach; ?>


    <tr valign="top" class="">
				<td colspan="7">
                    <p class="addTimeSlot">
                        <span class="dashicons dashicons-plus-alt"></span>
                    </p>
                </td>
			</tr>
    </tbody>
</table>
<br>
<p class="submit">
	<button name="express_save" id="express_save" class="button-primary woocommerce-save-button" type="submit" value="Save changes"><?php _e('Save', 'easy'); ?></button>
</p>

<?php do_action( 'woocommerce_shipping_zone_after_methods_table' ); ?>