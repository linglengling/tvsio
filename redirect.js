  
  // code ajax post bằng action hook wp-ajax của wordpress 
jQuery(document).ready(function() {
  // jQuery.noConflict(true );
    ///////////////////////////////////////////////////////////////
  /////////////////////// kiểm tra đã có danh sách tên miền chưa ////////////////////////////////////
  

  var ishaveDM = 0;
  function getishaveDM(){
   console.log("cos chay nha");
   var postdata = "action=getishaveDM";
   jQuery.post(ajaxurl, postdata, function(response) {
         
    
    var data = jQuery.parseJSON(response);
    ishaveDM = data.message;
     });
  }
  getishaveDM();
  setTimeout(function() {
   console.log("ishaveDM"+ishaveDM);
   
/////////////////////////Lấy ngẫu nhiên một tên miền ra//////////////////////////////////////////////////
if(ishaveDM == 1){
  var tempDM = "";
  var host = window.location.host;

function getrandDM(){
  var postdata = "action=getrandDM&hostsite="+ host ;
  jQuery.post(ajaxurl, postdata, function(response) {
        
   var data = jQuery.parseJSON(response);
   tempDM = data.message;
       

 });
}
getrandDM();
setTimeout(function() {
  console.log("tempDM"+tempDM);
  }, 3000);

//////////////////////////Lấy thời gian điều hướng////////////////////////////////////////////////////////

var temptimeRD = 5000;
function getimeRD(){
var postdata = "action=getimeRD";
jQuery.post(ajaxurl, postdata, function(response) {
      
  var data = jQuery.parseJSON(response);
  temptimeRD = data.message;
     

});
}
getimeRD();
setTimeout(function() {
console.log("temptimeRD"+temptimeRD);
}, 3000);
///////////////////////////hàm tăng biến đếm sô lượt mở trang của tên miền///////////////////////////////

function countMD(siteSEO){
var postdata = "action=countMD&site="+siteSEO;
jQuery.post(ajaxurl, postdata, function(response) {
  
  // console.log(response);
  var data = jQuery.parseJSON(response);
  temptimeRD = data.message;
     

});
}
// Hàm thiết lập Cookie

function setCookie(cname, cvalue, exdays) {
   var d = new Date();
   d.setTime(d.getTime() + (exdays*24*60*60*1000));
   var expires = "expires="+d.toUTCString();
   document.cookie = cname + "=" + cvalue + "; " + expires;
}
// Hàm lấy Cookie
function getCookie(cname) {
   var name = cname + "=";
   var ca = document.cookie.split(';');
   for(var i=0; i<ca.length; i++) {
       var c = ca[i];
       while (c.charAt(0)==' ') c = c.substring(1);
       if (c.indexOf(name) == 0) return c.substring(name.length, c.length);
   }
   return "";
}

////////////////////////////điều hướng tới trang ngẫu nhiên tempDM sau temptimeRD giây/////////////////////////
//  function checkCookie() {
//     var username=getCookie("username");
//     if (username!="") {
//         alert("Welcome again " + username);
//     } else {
//         username = prompt("Please enter your name:", "");
//         if (username != "" && username != null) {
//             setCookie("username", username, 365);
//         }
//     }
// }

var giatri = getCookie('RDcoockie');
setTimeout(function() {
    if(false){//giatri !== "available"
      var ll = '<button type="button"  class="btn btn-primary" data-toggle="modal" data-target="data-target="#myModal"">Small modal</button>'
      +'<div class="modal fade " id="myModal" >'//tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true"
       + '<div class=" modal-dialog modal-xl">'
       
        + ' <div class="modal-body ">'
            +'<a href="http://'+tempDM+'" target="_blank"  "><img src="http://taigamejava.org/wp-content/uploads/2022/05/Untitled-design-1.png" alt="bài viết hay" width="100%" height="25%"></a>'
         + ' </div>'
        + ' <div class="modal-content ">'
            +'<a href="http://'+tempDM+'" target="_blank"  "><img src="http://247vlog.com/wp-content/uploads/sites/9/2022/05/Bai-viet-hay-cung-chu-de-vo-cung-hap-dan-1.png" alt="bài viết hay" width="100%" height="50%"></a>'
         + ' </div>'
        + ' <div class="modal-body ">'
            +'<a href="http://'+tempDM+'" target="_blank"  "><img src="http://taigamejava.org/wp-content/uploads/2022/05/Untitled-design-1.png" alt="bài viết hay" width="100%" height="25%"></a>'
         + ' </div>'

       +' </div>'
      +'</div>'
      ;

      // var ll = httpGet("http://"+tempDM+"");
     
      if(document.getElementById('show').innerHTML =ll){
     
        jQuery('.btn')[0].click();
        jQuery('#myModal').modal({backdrop: 'static', keyboard: false});

        // var host = window.location.host;
        // jQuery.ajax({
        //   url: "http://"+host+"",
        //   data: 'your image',
        //   success: function(){window.open("http://"+tempDM+"");},
        //   async: false
        //  });
        // if(jQuery('#linkSEOsite')[0].click()){
        //  console.log("click ok");
        // }else{
        //   console.log("không click ok");
        // }
        if(countMD(tempDM)){
           console.log("click ok");
          }else{
            console.log("không click ok");
          }
        console.log("bắt đầu click");
      }
      
    }
  
  }, temptimeRD);

  setCookie("RDcoockie", "available", 2);
 }

   }, 3000);
//////////////////////////////////////
  
});