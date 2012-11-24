<?php
    if (!function_exists('is_user_logged_in')) {
        header("Location: /");
        exit();
    }
?>
<div class="wrap">
    <div class="RWSDevBlip">
        <?php screen_icon(); ?>
        <h2>Blip Videos Administration - Videos</h2>
        <form method="post" enctype="multipart/form-data" id="RWSDevBlip-upload-form">
            <fieldset class="RWSDevBlip_container">
                <legend>Upload Video to Blip</legend>
                <div class="fl first">
                    <input type="hidden" name="video[id]" id="video_id" value="new" />
                    <label for="video[title]">Title</label>
                    <input type="text" name="video[title]" id="title" value="" style="width: 600px !important; margin-left: 0 !important" />
                </div>
                <div class="fl first">
                    <label for="video[description]">Description</label>
                    <textarea type="text" name="video[description]" id="description" value="" rows="5" cols="70"></textarea>
                </div>
                <div class="fl first">
                    <label for="video">Select Video</label>
                    <input type="file" name="video" />
                </div>
                <input type="hidden" name="action" value="update_video" />
                <?php wp_nonce_field('RWSDevBlip-upload-form'); ?>
                <p class="submit ar">
                    <input type="submit" name="submit" class="button-primary" value="<?php _e('Upload') ?>" onclick="disp_loading();" />
                </p>
                <div style="margin-right: auto; margin-left: auto; width: 130px" id="ajax_loading"><img src="<?php echo $this->pluginurl.'/images/ajax-loader.gif';?>" /></div>
            </fieldset>
        </form>
    </div>
</div>
