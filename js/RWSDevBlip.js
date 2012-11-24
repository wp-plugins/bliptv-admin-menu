var $jq = jQuery.noConflict();

jQuery(document).ready(function() {
    $jq('#ajax_loading').hide();
    $jq('#ajax_loading').ajaxStart(function() {
        $jq(this).show();
    });
    $jq('#ajax_loading').ajaxStop(function() {
        $jq(this).hide();
    });
});

function disp_loading() {
    $jq('#ajax_loading').show();
}

function import_videos(){
    var $jq = jQuery.noConflict();
    $jq.ajaxSetup ({  
       cache: false  
    })

    $jq.ajax({
        url: "/wp-admin/admin-ajax.php",
        cache: false,
        type: "POST",
        data: {
            action:'import_videos'
        },
        success:function(html){
                var responseArr = JSON.parse(html);
                if (responseArr == 'ERROR') {
                    alert('There was an error processing your request!');
                } else {
                    alert(responseArr);
                }
        }
    });
};
function load_video_info(x){
    var $jq = jQuery.noConflict();
    $jq.ajaxSetup ({  
       cache: false  
    })

    $jq.ajax({
        url: "/wp-admin/admin-ajax.php",
        cache: false,
        type: "POST",
        data: {
            id:x,
            action:'load_video_info'
        },
        success:function(html){
                var responseArr = JSON.parse(html);
                if (responseArr == 'ERROR') {
                    alert('There was an error processing your request!');
                } else {
                    $jq('#blip_id').val(responseArr['blip_id']);
                    $jq('#title').val(responseArr['title']);
                    $jq('#description').val(responseArr['description']);
                    $jq('#embed_div').html(responseArr['blip_embed']);
                }
        }
    });
};
function delete_video(x,y){
    var $jq = jQuery.noConflict();
    $jq.ajaxSetup ({  
       cache: false  
    })

    $jq.ajax({
        url: "/wp-admin/admin-ajax.php",
        cache: false,
        type: "POST",
        data: {
            id:x,
            reason:y,
            action:'delete_video'
        },
        success:function(html){
                var responseArr = JSON.parse(html);
                if (responseArr == 'success') {
                    alert('The video has been deleted');
                    $jq("#video_id option[value="+x+"]").remove();
                    $jq('#embed_div').html('');
                    $jq('#RWSDevBlip-videos-form')[0].reset();
                } else {
                    alert(responseArr);
                }
        }
    }); 
};
