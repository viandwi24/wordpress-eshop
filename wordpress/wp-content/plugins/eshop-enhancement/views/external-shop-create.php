<?php
global $wpdb;
$table_name = eshop_config('external_shop_table_name');
if ($action == 'edit') {
    if (!isset($_GET['id'])) return wp_redirect(admin_url('admin.php?page=external-shop'));
    $id = $_GET['id'];
    $data = $wpdb->get_row("SELECT * FROM {$table_name} WHERE external_shop_id = {$id}");
    if (!$data) return wp_redirect(admin_url('admin.php?page=external-shop'));
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && $_POST['action'] === 'Create') {
        eshop_enhancement_form_validate(
            // wp admin url
            admin_url('admin.php?page=external-shop&action=create'),
            $_POST,
            [
                'shop_name' => ['required'],
                'shop_link' => ['required'],
                'shop_logo' => ['required'],
            ]
        );

        # data 
        $data = eshop_enhancement_form_get_data_only($_POST, ['shop_name', 'shop_link', 'shop_logo']);

        # save to table wordpress
        $wpdb->insert(
            $table_name,
            $data,
            ['%s', '%s', '%s']
        );
        
        // redirect to list
        wp_redirect(admin_url('admin.php?page=external-shop'));
    }
    if (isset($_POST['action']) && $_POST['action'] === 'Edit') {
        eshop_enhancement_form_validate(
            // wp admin url
            admin_url('admin.php?page=external-shop&action=create'),
            $_POST,
            [
                'shop_name' => ['required'],
                'shop_link' => ['required'],
                'shop_logo' => ['required'],
            ]
        );

        # data 
        $data = eshop_enhancement_form_get_data_only($_POST, ['shop_name', 'shop_link', 'shop_logo']);

        //  update to database
        $wpdb->update(
            $table_name,
            $data,
            ['external_shop_id' => $id],
            ['%s', '%s', '%s'],
            ['%d']
        );
        
        // redirect to list
        wp_redirect(admin_url('admin.php?page=external-shop'));
    }
}
eshop_enhancement_form_display_errors();
?>
<style>
    .form-wrap .form-field {
        margin: 1em 0;
        padding: 0;
    }
    .form-field input[type=email], .form-field input[type=number], .form-field input[type=password], .form-field input[type=search], .form-field input[type=tel], .form-field input[type=text], .form-field input[type=url], .form-field textarea {
        border-style: solid;
        border-width: 1px;
        width: 95%;
    }
    #shop-preview-image {
        max-width: 100px;
        max-height: 100px;
    }
    .row {
        display: flex;
    }
    .mr-2 {
        margin-right: .5rem;
    }
</style>
<div class="wrap">
	<h1 class="wp-heading-inline">
		External Shop > <?= ucfirst($action) ?>
	</h1>
	<hr class="wp-header-end">
    <form method="POST" class="form-wrap validate">
        <div class="form-field form-required term-name-wrap">
            <label for="shop_name">Shop Name</label>
            <input name="shop_name" id="shop_name" type="text" value="<?= @$data->shop_name ?>" size="40" aria-required="true" require>
        </div>
        <div class="form-field form-required term-name-wrap">
            <label for="shop_link">Shop Link</label>
            <input name="shop_link" id="shop_link" type="text" value="<?= @$data->shop_link ?>" size="40" aria-required="true" require>
        </div>
        <div class="form-field form-required term-name-wrap">
            <label for="shop_name">Shop Logo</label>
            <div class="row">
                <div class="mr-2">
                    <img id="shop-preview-image" src="<?= @$data->shop_logo ?>" />
                </div>
                <div>
                    <input type="button" class="button" value="Select image" id="eshop_enhancement_media_manager"/>
                </div>
            </div>
            <input type="hidden" name="shop_logo" id="shop_image" class="regular-text" value="<?= @$data->shop_logo ?>" />
        </div>
        <?php if ($action == 'create') { ?>
            <input type="submit" name="action" id="save" class="button button-primary button-large" value="Create">
        <?php } else { ?>
            <input type="submit" name="action" id="save" class="button button-primary button-large" value="Edit">
        <?php } ?>
    </form>
</div>