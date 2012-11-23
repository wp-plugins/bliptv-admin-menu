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
        <form method="post" action="" id="RWSDevBlip-videos-form">
            <fieldset class="RWSDevBlip_container">
                <legend>Blip Video List</legend>
                <div class="fl first">
                    <select name="video[id]" id="video_id" size="5" class="titles" onchange="load_video_info(this[this.selectedIndex].value)">
                        <option selected="selected" value="0" style="display: none;"></option>
                        <?php
                        foreach ($videosArr as $video) {
                            echo '<option value="'.$video->id.'">'.$video->title.'</option>';
                        }
                        ?>
                    </select>
                </div>
            </fieldset>
           <fieldset class="RWSDevBlip_container">
                <legend>Blip Video Information</legend>
                <div class="fl first">
                    <label for="video[title]">Title</label>
                    <input type="text" name="video[title]" id="title" value="" style="width: 600px !important; margin-left: 0 !important" />
                </div>
                <div class="fl first">
                    <label for="description">Description</label>
                    <textarea type="text" name="video[description]" id="description" value="" rows="5" cols="70"></textarea>
                </div>
                <div class="first" style="padding: 15px; height: 20px;">
                    <input type="button" class='button-secondary fl' onclick="delete_video($jq('#video_id :selected').val())" value="Delete Selected Video" />
                    <input type="submit" name="submit" class="button-primary fr" value="<?php _e('Save Changes') ?>" />
                </div>
                <div class="fl" style="padding-left: 23px;" id="ajax_loading"><img src="<?php echo $this->pluginurl.'/images/ajax-loader.gif';?>" /></div><br /><br />

                <div style="margin-right: auto; margin-left: auto; text-align: center;" id="embed_div"></div>
            </fieldset>
                <input type="hidden" name="video[blip_id]" id="blip_id" value="" />
                <input type="hidden" name="action" value="update_video" />
                <?php wp_nonce_field('RWSDevBlip-videos-form'); ?>
            </fieldset>
        </form>
    </div>
</div>
