<?php
if (! defined('ABSPATH')) {
    exit;
}
?>

    <img src="<?php echo plugin_dir_url(__DIR__); ?>../../assets/woorule.svg" />
	<h2><?php _e('WooRule mailing rules', 'woorule'); ?></h2>
	<?php if (!get_option('woocommerce_rulemailer_settings')['woorule_api_key']) { ?>
  	  <div style="margin-bottom:10px; display: inline-block;width: 100%;">
		You need to set up an API key on <a href="<?php echo admin_url('admin.php?page=wc-settings&tab=integration'); ?>"> API settings page</a> before you can use this plugin.
  	  </div>
	<?php } ?>

<table class="wp-list widefat fixed striped">
	<thead>
		<tr>
			<th><?php _e('Name', 'woorule'); ?></th>
			<th colspan="2"><?php _e('Enabled', 'woorule'); ?></th>
		</tr>
	</thead>
	<tbody>

		<?php
        foreach ($rules as $id => $value) {
            echo '<tr>';
            echo '<td>' . WC_Admin_Settings::get_option($value['name']['id'], 'Unnamed') . '</td>';
            echo '<td>' . WC_Admin_Settings::get_option($value['enabled']['id'], 'no') .'</td>';
            echo '<td><a href="' . $edit_url . $id . '">' . __('Edit', 'woorule') . '</a></td>';
            echo '</tr>';
        }

        ?>
	</tbody>
</table>


	<a href="<?php echo $create_url; ?>" class="add-new-h2" style="margin:10px 0; display: inline-block;">
		<?php _e('Add new', 'woorule'); ?>
	</a>
