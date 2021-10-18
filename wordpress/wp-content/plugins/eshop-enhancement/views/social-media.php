<?php
global $wpdb;

# if method delete
if (isset($_POST['action']) && $_POST['action'] == 'Delete' && isset($_POST['id']) && $_POST['id'] != '') {
	$id = $_POST['id'];

	// delete
	$table_name = eshop_config('social_media_table_name');
	$wpdb->delete($table_name, array('social_media_id' => $id));

	// redirect
	$redirect_url = admin_url('admin.php?page=social-media');
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
		Social Media
	</h1>
	<a href="<?= admin_url("admin.php?page=social-media&action=create"); ?>" class="page-title-action">Add New</a>
	<hr class="wp-header-end">
	<table class="wp-list-table widefat">
		<thead>
			<tr>
			<th width="2%">Logo</th>
			<th width="25%">Name</th>
			<th width="25%">Link</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach (eshop_get_social_media() as $print) { ?>
				<tr>
					<td>
						<div>
                            <?php if (strpos($print->social_media_logo, 'fa-') !== false): ?>
                                <i class="<?= $print->social_media_logo ?> fa-3x"></i>
                            <?php else: ?>
                                <img src="<?= $print->social_media_logo ?>" alt="<?= $print->social_media_name ?>" style="max-width: 54px;">
                            <?php endif; ?>
						</div>
					</td>
					<td>
						<strong><?= $print->social_media_name ?></strong>
						<div class="row-actions">
							<span>ID: <?= $print->social_media_id ?> |</span>
							<span>
								<a href="<?= admin_url("admin.php?page=social-media&action=edit&id={$print->social_media_id}"); ?>">Edit</a>
								|
							</span>
							<span>
								<form method="POST" style="display: inline;">
									<input type="hidden" name="id" value="<?= $print->social_media_id ?>">
									<input type="hidden" name="action" value="Delete">
									<a href="javascript:void(0);" class="submitdelete" onclick="this.parentElement.submit()">Trash</a>
								</form>
							</span>
						</div>
					</td>
					<td><?= $print->social_media_link ?></td>
				</tr>
			<?php } ?>
		</tbody>  
	</table>
</div>