<?php
global $wpdb;
$table_name = eshop_config('social_media_table_name');
if ($action == 'edit') {
    if (!isset($_GET['id'])) return wp_redirect(admin_url('admin.php?page=social-media'));
    $id = $_GET['id'];
    $data = $wpdb->get_row("SELECT * FROM {$table_name} WHERE social_media_id = {$id}");
    if (!$data) return wp_redirect(admin_url('admin.php?page=social-media'));
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && $_POST['action'] === 'Create') {
        eshop_enhancement_form_validate(
            // wp admin url
            admin_url('admin.php?page=social-media&action=create'),
            $_POST,
            [
                'social_media_name' => ['required'],
                'social_media_link' => ['required'],
                'social_media_logo' => ['required'],
            ]
        );

        # data 
        $data = eshop_enhancement_form_get_data_only($_POST, ['social_media_name', 'social_media_link', 'social_media_logo']);

        # save to table wordpress
        $wpdb->insert(
            $table_name,
            $data,
            ['%s', '%s', '%s']
        );
        
        // redirect to list
        wp_redirect(admin_url('admin.php?page=social-media'));
    }
    if (isset($_POST['action']) && $_POST['action'] === 'Edit') {
        eshop_enhancement_form_validate(
            // wp admin url
            admin_url('admin.php?page=social-media&action=create'),
            $_POST,
            [
                'social_media_name' => ['required'],
                'social_media_link' => ['required'],
                'social_media_logo' => ['required'],
            ]
        );

        # data 
        $data = eshop_enhancement_form_get_data_only($_POST, ['social_media_name', 'social_media_link', 'social_media_logo']);

        //  update to database
        $wpdb->update(
            $table_name,
            $data,
            ['social_media_id' => $id],
            ['%s', '%s', '%s'],
            ['%d']
        );
        
        // redirect to list
        wp_redirect(admin_url('admin.php?page=social-media'));
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
		Social Media > <?= ucfirst($action) ?>
	</h1>
	<hr class="wp-header-end">
    <form method="POST" class="form-wrap validate">
        <div class="form-field form-required term-name-wrap">
            <label for="social_media_name">Social Media Name</label>
            <input name="social_media_name" id="social_media_name" type="text" value="<?= @$data->social_media_name ?>" size="40" aria-required="true" require>
        </div>
        <div class="form-field form-required term-name-wrap">
            <label for="social_media_link">Social Media Link</label>
            <input name="social_media_link" id="social_media_link" type="text" value="<?= @$data->social_media_link ?>" size="40" aria-required="true" require>
        </div>
        <div class="form-field form-required term-name-wrap">
            <label for="social_media_name">Social Media Logo</label>
            <div class="row">
                <div class="mr-2">
                    <img id="social-media-preview-image" src="<?= @$data->social_media_logo ?>" />
                </div>
                <div>
                    <input type="button" class="button" value="Select image" id="eshop_enhancement_social_media_media_manager"/>
                </div>
            </div>
            <input type="hidden" name="social_media_logo" id="social_media_image" class="regular-text" value="<?= @$data->social_media_logo ?>" />
        </div>
        <?php if ($action == 'create') { ?>
            <input type="submit" name="action" id="save" class="button button-primary button-large" value="Create">
        <?php } else { ?>
            <input type="submit" name="action" id="save" class="button button-primary button-large" value="Edit">
        <?php } ?>
    </form>
</div>