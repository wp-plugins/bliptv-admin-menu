<?php
    if (!function_exists('is_user_logged_in')) {
        header("Location: /");
        exit();
    }
?>
<div class="wrap">
    <div class="RWSDevBlip">
        <?php screen_icon(); ?>
        <h2>Blip Videos Administration - Settings & Options</h2>
        <fieldset class="RWSDevBlip_container">
            <legend>About This Plugin</legend>
            <p>This plugin was written by: Jason Becht, a member of the <a href="http://rwsdev.net/" target="_blank">RWS Development Team</a>.</p>
            <p>The initial blipPHP library was written by Almog Baku and updated by Jason Becht.</p>
        </fieldset>
        <form method="post" action="options.php">
            <fieldset class="RWSDevBlip_container">
                <legend>Blip Account Information</legend>
                <div class="fl first" style="width: 600px;">
                <label for="RWSDevBlip_username" class="ar fl">Blip Username</label>
                <input type="text" name="RWSDevBlip_username" value="<?php echo get_option('RWSDevBlip_username'); ?>" /><br />
                </div>
                <div class="fl first" style="width: 600px;">
                <label for="RWSDevBlip_password" class="ar fl">Blip Password</label>
                <input type="text" name="RWSDevBlip_password" value="<?php echo get_option('RWSDevBlip_password'); ?>" /><br />
                </div>
                <input type="hidden" name="action" value="update" />
                <input type="hidden" name="page_options" value="RWSDevBlip_username,RWSDevBlip_password" />
                <?php wp_nonce_field('update-options'); ?>
                <p class="submit ar">
                    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
                </p>
            </fieldset>
        </form>
        <fieldset class="RWSDevBlip_container">
            <legend>Importing</legend>
            <p class="al">
                After saving your Username and Password above, click the button to import your video data from Blip.tv
            </p>
            <p class="submit ar">
                <input type="submit" class="button-primary" value="<?php _e('Import Video Info From Blip') ?>" onclick="import_videos()" />
            </p>
            <div class="fr" style="padding-right: 30px;" id="ajax_loading"><img src="<?php echo $this->pluginurl.'/images/ajax-loader.gif';?>" /></div>
        </fieldset>
    </div>
</div>
