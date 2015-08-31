<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>

<h3>
	<?php _e( 'Rules', 'woorule' ); ?>
	<a href="<?php echo $create_url; ?>" class="add-new-h2">
		<?php _e('Add new', 'woorule'); ?>
	</a>
</h3>

<table class="wp-list widefat fixed striped">
	<thead>
		<tr>
			<th><?php _e( 'Name', 'woorule' ); ?></th>
			<th colspan="2"><?php _e( 'Enabled', 'woorule' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($rules as $id => $value) {
			echo '<tr>';
			echo '<td>' . WC_Admin_Settings::get_option( $value['name']['id'], 'Unnamed' ) . '</td>';
			echo '<td>' . WC_Admin_Settings::get_option( $value['enabled']['id'], 'no' ) .'</td>';
			echo '<td><a href="' . $edit_url . $id . '">' . __('Edit', 'woorule') . '</a></td>';
			echo '</tr>';
		}
		?>
	</tbody>
</table>

