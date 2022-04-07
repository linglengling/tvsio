// code ajax post bằng action hook wp-ajax của wordpress 
jQuery(document).ready(function() {
    jQuery(document).on("click", ".spinpost", function(){
     console.log("chạy spin lại");
       var post_id = jQuery(this).attr("data-id");
       var postdata = "action=respin&param=loi&id=" + post_id;
       jQuery.post("../../../wp-admin/admin-ajax.php", postdata, function(response) {
            
            console.log(response);
            setTimeout(function() {
                location.reload();
            }, 1300);
         });
       
    });
    
});
