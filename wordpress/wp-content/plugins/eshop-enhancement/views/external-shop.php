<?php
global $wpdb;

# if method delete
if (isset($_POST['action']) && $_POST['action'] == 'Delete' && isset($_POST['id']) && $_POST['id'] != '') {
	$id = $_POST['id'];

	// delete
	$table_name = eshop_config('external_shop_table_name');
	$wpdb->delete($table_name, array('external_shop_id' => $id));

	// redirect
	$redirect_url = admin_url('admin.php?page=external-shop');
	wp_redirect($redirect_url);
}
?>
<style>
	.submitdelete {
		color: #b32d2e;
	}
</style>
<div class="wrap">
	<h1 class="wp-heading-inline">
		External Shop
	</h1>
	<a href="<?= admin_url("admin.php?page=external-shop&action=create"); ?>" class="page-title-action">Add New</a>
	<hr class="wp-header-end">
	<table class="wp-list-table widefat">
		<thead>
			<tr>
			<th width="2%">Logo</th>
			<th width="25%">Shop Name</th>
			<th width="25%">Shop Link</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$table_name = eshop_config('external_shop_table_name');
			$result = $wpdb->get_results("SELECT * FROM $table_name");
			foreach ($result as $print) { ?>
				<tr>
					<td>
						<div>
							<img src="<?= $print->shop_logo ?>" alt="<?= $print->shop_name ?>" style="max-width: 54px;">
						</div>
					</td>
					<td>
						<strong><?= $print->shop_name ?></strong>
						<div class="row-actions">
							<span>ID: <?= $print->external_shop_id ?> |</span>
							<span>
								<a href="<?= admin_url("admin.php?page=external-shop&action=edit&id={$print->external_shop_id}"); ?>">Edit</a>
								|
							</span>
							<span>
								<form method="POST" style="display: inline;">
									<input type="hidden" name="id" value="<?= $print->external_shop_id ?>">
									<input type="hidden" name="action" value="Delete">
									<a href="javascript:void(0);" class="submitdelete" onclick="this.parentElement.submit()">Trash</a>
								</form>
							</span>
						</div>
					</td>
					<td><?= $print->shop_link ?></td>
				</tr>
			<?php } ?>
		</tbody>  
	</table>
</div>