<?php
/*
Plugin Name: bliptv-admin-menu
Plugin URI: http://CrypTechStudios.com/wp-plugins/bliptv-admin-menu
Description: Wordpress admin area interface for Blip.tv API.  Allow upload, delete and edit of uploaded files on blip.tv
Author: Jason Becht (CrypTech Studios, Inc.)
Version: 2.0  Wed May 27 10:05:00 2015
Author URI: http://CrypTechStudios.com/
*/

if (!class_exists('RWSDevBlip')) {
    class RWSDevBlip {
        const DB_VERSION = 1;

        public function __construct() {
            /* globalize $wpdb */
            global $wpdb;

            /* include for pluggable.php */
            include_once(ABSPATH.'wp-includes/pluggable.php');

            /* blipPHP class */
            include_once('includes/blip-php/blipPHP.php');

            /* set plugin path and url */
            $this->pluginpath = dirname(__FILE__);
            $this->pluginurl = WP_PLUGIN_URL.'/bliptv-admin-menu/';

            /* set table names */

            /* hooks, actions and filters */
            register_activation_hook(__FILE__,array($this,'RWSDevBlip_create_tables'));
            register_activation_hook(__FILE__,array($this,'RWSDevBlip_activate'));
            add_action('admin_menu', array($this,'RWSDevBlip_add_admin_menu'));
            add_action('init', array($this,'RWSDevBlip_load_deps'));
            add_action('wp_ajax_import_videos',array($this,'import_videos'));
            add_action('wp_ajax_load_video_info',array($this,'load_video_info'));
            add_action('wp_ajax_delete_video',array($this,'delete_video'));


            /* localize $_POST and $_FILES data if we have it */
            $this->post_data = (isset($_POST))?$_POST:NULL;
            $this->files_data = (isset($_FILES))?$_FILES:NULL;

        }
        public function RWSDEVBlip_activate() {
            add_option('RWSDevBlip_username');
            add_option('RWSDevBlip_password');
        }
        public function RWSDevBlip_create_tables() {
            /* globalize $wpdb */
            global $wpdb;

            /* set table names */
            $this->videos_table = $wpdb->prefix."RWSDevBlip_videos";

            /* SQL for queries */
            $sql[0] = "CREATE TABLE $this->videos_table(
                id int(9) NOT NULL AUTO_INCREMENT,
                title VARCHAR(255) NOT NULL,
                description LONGTEXT NOT NULL,
                privacy ENUM('public','private') DEFAULT 'public' NOT NULL,
                blip_id int(32),
                blip_url varchar(255),
                blip_embed LONGTEXT,
                updated_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY id (id),
                UNIQUE INDEX(blip_id)
                );";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            foreach ($sql as $sqlQuery) {
                dbDelta($sqlQuery);
            }
        }
        public function RWSDevBlip_add_admin_menu() {
            add_menu_page('Blip.tv Interface','Blip.tv API','manage_options','rwsdev-blip',array($this,'RWSDevBlip_admin_page'),$this->pluginurl.'images/RWSDevBlip.png',3);
            add_submenu_page('rwsdev-blip','Blip.tv Interface Options','Settings','manage_options','rwsdev-blip',array($this,'RWSDevBlip_admin_page'));
            add_submenu_page('rwsdev-blip','Blip.tv Interface Upload','Upload','manage_options','rwsdev-blip-upload',array($this,'RWSDevBlip_upload_page'));
            add_submenu_page('rwsdev-blip','Blip.tv Interface Videos','Videos','manage_options','rwsdev-blip-videos',array($this,'RWSDevBlip_videos_page'));
        }
        public function RWSDevBlip_load_deps() {
            /* enqueue style sheet for plugin */
            $css_path = $this->pluginurl.'css/styles.css';
            wp_register_style('RWSDevBlip-style', $css_path);
            wp_enqueue_style('RWSDevBlip-style');

            /* enqueue wp jquery */
            wp_enqueue_script('jquery');

            /* enqueue plugin specific javascript */
            $pluginjs_path = $this->pluginurl.'js/RWSDevBlip.js';
            wp_register_script('RWSDevBlip-js', $pluginjs_path);
            wp_enqueue_script('RWSDevBlip-js');
        }
        public function RWSDevBlip_admin_page() {
            include('includes/settings.php');
        }
        public function RWSDevBlip_upload_page() {
            if (isset($this->post_data['submit'])) {
                $this->upload_video();
            }
            include('includes/upload.php');
        }
        public function RWSDevBlip_videos_page() {
            /* globalize $wpdb */
            global $wpdb;

            if (isset($this->post_data['submit'])) {
                $this->update_video();
            }
            $videosArr = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."RWSDevBlip_videos");

            include('includes/videos.php');
        }
        public function import_videos() {
            /* globalize $wpdb */
            global $wpdb;

            /* remove all video items from DB for refresh */
            $wpdb->query("DELETE FROM ".$wpdb->prefix."RWSDevBlip_videos");

            /* instantiate blipPHP */
            $blip = new blipPHP(get_option('RWSDevBlip_username'),get_option('RWSDevBlip_password'));

            /* Blip API documentation states it returns 100 videos per page
             * however they only return 12 and no indication of how many pages
             * a user has.  This loop will get each page in succession until it
             * encounters an error which is is the page not found error */
            $videosArr = array();
            $i = 1;
            while ($i) {
                $videosObj = $blip->usersFiles(get_option('RWSDevBlip_username'),(string)$i);
                if ($videosObj->status != 'ERROR') {
                    foreach ($videosObj->payload->asset as $videoObj) {
                        $videosArr['id:'.$videoObj->id]['blip_id'] = $videoObj->item_id;
                        $videosArr['id:'.$videoObj->id]['title'] = $videoObj->title;
                        $videosArr['id:'.$videoObj->id]['description'] = $videoObj->description;
                        $videosArr['id:'.$videoObj->id]['tags'] = $videoObj->tags;
                        $videosArr['id:'.$videoObj->id]['blip_url'] = $videoObj->embedUrl;
                        $videosArr['id:'.$videoObj->id]['blip_embed'] = $videoObj->embedCode;
                    }
                    $i++;
                } else {
                    break;
                }
            }
            $sqlQuery = "INSERT INTO ".$wpdb->prefix."RWSDevBlip_videos (title,blip_id,description,blip_url,blip_embed) VALUES ";
            foreach ($videosArr as $video) {
                $sqlQuery .= '("'.mysql_real_escape_string($video['title']).'",'.$video['blip_id'].',"'.mysql_real_escape_string($video['description']).'","'.mysql_real_escape_string($video['blip_url']).'","'.mysql_real_escape_string($video['blip_embed']).'"),';
            }
            $sqlQuery = rtrim($sqlQuery,',');
//            print json_encode($sqlQuery);
//            exit();
            if ($wpdb->query($sqlQuery)) {
                print json_encode("success");
                exit();
            } else {
                print json_encode("ERROR");
                exit();
            }
        }
        public function load_video_info() {
            global $wpdb;
            $id = $this->post_data['id'];
            $video_info = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."RWSDevBlip_videos WHERE id = ".$id,ARRAY_A);
            print json_encode($video_info);
            exit();
        }
        public function update_video() {
            /* globalize $wpdb */
            global $wpdb;
            if (check_admin_referer('RWSDevBlip-videos-form','_wpnonce')) {

                /* instantiate blipPHP */
                $blip = new blipPHP(get_option('RWSDevBlip_username'),get_option('RWSDevBlip_password'));

                $videoArr = $this->post_data['video'];

                /* send edit to blip */
                $res = $blip->edit($videoArr['blip_id'],$videoArr['title'],$videoArr['description']);

                /* if resp status is OK then update the db for the video */
                if ($res->status == 'OK') {
                    $wpdb->update($wpdb->prefix."RWSDevBlip_videos",$videoArr,array('id'=>$videoArr['id']));
                } else {
                    error_log('failed referer check');
                }
            }
        }
        public function upload_video() {
            /* globalize $wpdb */
            global $wpdb;
            if (check_admin_referer('RWSDevBlip-upload-form','_wpnonce')) {
                /* get the video file */
                if ($this->files_data['video']) {
                    $videoFile = $this->files_data;
                    /* create temp video directory */
                    $target_dir = $this->pluginpath.'/'.uniqid('vid_');
                    mkdir($target_dir,0775,true);
                    $target_path = $target_dir.'/'.$videoFile['video']['name'];
                    move_uploaded_file($videoFile['video']['tmp_name'],$target_path);
                } else {
                    error_log('No file data!');
                }

                /* instantiate blipPHP */
                $blip = new blipPHP(get_option('RWSDevBlip_username'),get_option('RWSDevBlip_password'));

                $videoArr = $this->post_data['video'];

                /* send edit to blip */
                $res = $blip->upload($target_path,$videoArr['title'],$videoArr['description']);


                /* if resp status is OK then update the db for the video */
                if ($res->status == 'OK') {
                    $videoArr['blip_id'] = $res->payload->asset->item_id;

                    /* remove local temp video */
                    unlink($target_path);
                    /* remove temp directory */
                    rmdir($target_dir);

                    /* blip does not return the embed code and url for the video upload so we must request that
                     * separately to store in the db. */
                    $res = $blip->info($res->payload->asset->item_id);
                    $videoArr['blip_embed'] = $res->payload->asset->embedCode;
                    $videoArr['blip_url'] = $res->payload->asset->embedUrl;

                    /* clean the pieces and insert */
                    $wpdb->query("INSERT INTO ".$wpdb->prefix."RWSDevBlip_videos (title, description, blip_id, blip_url, blip_embed) VALUES ('".
                            mysql_real_escape_string($videoArr['title'])."','".
                            mysql_real_escape_string($videoArr['description'])."',".
                            $videoArr['blip_id'].",'".
                            mysql_real_escape_string($videoArr['blip_url'])."','".
                            mysql_real_escape_string($videoArr['blip_embed'])."')"
                            );
                } else {
                    error_log('Received Error Response from Blip.tv');
                }
            } else {
                error_log('failed referer check');
            }
        }
        public function delete_video() {
            global $wpdb;
            $id = $this->post_data['id'];
            $reason = ($this->post_data['reason'])?$this->post_data['reason']:'No reason given';

            /* get the blip_id for local video id */
            $blip_id = $wpdb->get_var("SELECT blip_id FROM ".$wpdb->prefix."RWSDevBlip_videos WHERE id = ".$id);

            $blip = new blipPHP(get_option('RWSDevBlip_username'),get_option('RWSDevBlip_password'));
            $res = $blip->delete($blip_id,$reason);

            if ($res->payload->asset->deleted == 'true') {
                $wpdb->delete($wpdb->prefix."RWSDevBlip_videos",array('id' => $id));
                print json_encode('success');
                exit();
            } else {
//                print json_encode($blip_id);
                print json_encode('There was an error deleting the video from Blip.tv');
                exit();
            }
        }
    }
}
$RWSDevBlip = new RWSDevBlip();
?>