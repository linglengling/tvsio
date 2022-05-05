// code ajax post bằng action hook wp-ajax của wordpress 
jQuery(document).ready(function() {
///////////////////////////////////////////////////////////////
   
    
     /////////////////////// lưu tên miền vào danh sách ////////////////////////////////////
    jQuery("#listDM").validate({
        submitHandler: 
       
        function() {
            console.log("chạy lưu miền");
            var postdata = "action=addDM&param=crawl&" + jQuery("#listDM").serialize();
            jQuery.post(ajaxurl, postdata, function(response) {
                
                console.log(response);
                setTimeout(function() {
                    location.reload();
                }, 1300);
            });
        }
      });
       /////////////////////// chức năng spin lại post lỗi ////////////////////////////////////
    jQuery(document).on("click", ".spinpost", function(){
     console.log("chạy spin lại");
       var post_id = jQuery(this).attr("data-id");
       var postdata = "action=respin&param=loi&id=" + post_id;
       jQuery.post(ajaxurl, postdata, function(response) {
            
            console.log(response);
            setTimeout(function() {
                location.reload();
            }, 1300);
         });
       
      });
      /////////////////////// bật tắt chức năng ddieuf hướng với tên miền ////////////////////////////////////
      jQuery(document).on("click", ".battat", function(){
        console.log("chạy spin lại");
          var row_id = jQuery(this).attr("data-id");
          console.log(row_id);
          var postdata = "action=battat&param=loi&id=" + row_id;
          jQuery.post(ajaxurl, postdata, function(response) {
               
               console.log(response);
            //    setTimeout(function() {
            //        location.reload();
            //    }, 1300);
            });
          
         });
          /////////////////////// xóa miền ////////////////////////////////////
         jQuery(document).on("click", ".delcp", function(){
            var conf = confirm("Are you sure want to delete the campain?");
            if (conf) { //if(true)
            var row_id = jQuery(this).attr("data-id");
            var postdata = "action=deleteDM&id=" + row_id;
              jQuery.post(ajaxurl, postdata, function(response) {
                 
                console.log(response);
                setTimeout(function() {
                    location.reload();
                }, 1300);
                    
        
              });
            }
           });
            /////////////////////// đặt thời gian chuyển hướng ////////////////////////////////////
           jQuery("#timeRD").on("change", OnSelectionChange);
           function OnSelectionChange() {
           var select = document.getElementById('timeRD');
           var timeRD = select.options[select.selectedIndex].value;
            var postdata = "action=timeRD&time="+ timeRD ;
            jQuery.post(ajaxurl, postdata, function(response) {
               
              console.log(response);
              setTimeout(function() {
                  location.reload();
              }, 1300);
       
            });
          }

         /////////////////////////////////////////////////////////////////////////////////
 

   });
