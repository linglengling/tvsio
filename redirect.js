  
  // code ajax post bằng action hook wp-ajax của wordpress 
jQuery(document).ready(function() {
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
   }, 3000);

/////////////////////////Lấy ngẫu nhiên một tên miền ra//////////////////////////////////////////////////
if(ishaveDM == 1){
   var tempDM = "";
 function getrandDM(){
   var postdata = "action=getrandDM";
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
     if(giatri !== "available"){
        window.open("http://"+tempDM , '_blank');
        countMD(tempDM);
     }
   
   }, temptimeRD);

   setCookie("RDcoockie", "available", 2);
//////////////////////////////////////
  }
});