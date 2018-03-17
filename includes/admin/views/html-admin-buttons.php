<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>

<table class="form-table">
	<tr valign="top">
		<td colspan="2" scope="row" style="padding-left: 0;">
			<p class="submit">
				<input type="submit" class="button button-primary button-large" value="<?php _e( 'Save Rule', 'woorule' ); ?>">
				<a href="<?php echo $delete_url; ?>" style="margin-left: 10px; color: #a00; text-decoration: none;"><?php _e( 'Delete', 'woorule' ); ?></a>
			</p>
		</td>
	</tr>
</table>

