<?php

/*
  Plugin Name: SEO tool PHP team
  Version: 0.1
  Author: TP & The Rain
  Description: the auto post, auto link, auto spin for SEO task
  License: GNU General Public License v3 or later
  License URI: http://www.gnu.org/licenses/gpl-3.0.html
  Text Domain: tvs
 */


global $tvs_options, $tvs_plugin_dir, $tvs_plugin_url,$result;

$tvs_plugin_dir = untrailingslashit(plugin_dir_path(__FILE__));
$tvs_plugin_url = untrailingslashit(plugin_dir_url(__FILE__));

function tvs_init() {
    load_plugin_textdomain('tvs', false, plugin_basename(dirname(__FILE__)) . '/languages/');
}

add_filter('init', 'tvs_init');

/*  ---  Settings  ---  */

include_once $tvs_plugin_dir . '/tvs.php';
include_once $tvs_plugin_dir . '/simple_html_dom.php';

//tvs_admin = new TVS();


//add_filter('wp_insert_post_data', 'edit_content_when_saving', 10, 2);

function edit_content_when_saving($data, $postarr) {
    set_time_limit(0);

    if ($data['post_type'] == 'post' && ($data['post_status'] == 'publish' || $data['post_status'] == 'update')) {
//       var_dump($data);
//       var_dump($postarr);
//       exit();

        $post_id = $postarr["ID"];
//      $post = get_post($post_id);
        $is_spinned = get_post_meta($post_id, "is_spinned");
//      var_dump(get_post_meta($post_id));
        var_dump($is_spinned);
//      echo "".$is_spinned;
//      exit();
        if ($is_spinned[0] == "1") {
            $tieng_viet_io_token = get_option("tvs_token");
//          $content = $postarr["post_content"];
//          $content = $content ." xin chao request ở đây";
            $post_content = stripslashes($data['post_content']);
            $data["post_content"] = addslashes(  $post_content . " xin chao request ở đây" ) ;
            
            // cập nhập trang thái của custom field is_spinned
            update_post_meta( $post_id, 'is_spinned', 0 );
        }
    }
    return $data;
}
// --------------------------
//RAIN CODE start from here
//---------------------------
update_option("Jquery", 0);
register_activation_hook( __FILE__, 'Jquery_activation' );

function Jquery_activation(){
    
    add_option('Jquery', 0);
}
add_action("wp_ajax_checkcojqueychua", 'checkcojqueychua_ajax_handler');

function checkcojqueychua_ajax_handler()
{
 update_option("Jquery", 1);
}
// custom css and js
add_action('admin_enqueue_scripts', 'aaconf_css_and_js');
 
function aaconf_css_and_js($hook) {

   
    wp_enqueue_script('custo_js', plugins_url('custom.js',__FILE__ ), array(), '1.0', true);
}
//get lib
require 'vendor/autoload.php';
include_once $tvs_plugin_dir . '/tiengviet.php';
include_once $tvs_plugin_dir . '/APISEO.php';

// CREATE DATABASE
 
if ( !defined('ABSPATH') )
    define('ABSPATH', dirname(__FILE__) . '/');

/** do the active hook */
require_once dirname(__FILE__). '/includes/statusdata.php';
register_activation_hook( __FILE__, 'statusdata_create' );

register_activation_hook( __FILE__, 'aaforce_main_site_installation' );

function aaforce_main_site_installation()
{
    if ( defined( 'SITE_ID_CURRENT_SITE' )
        and SITE_ID_CURRENT_SITE !== get_current_site()->id 
    )
    {
        if ( function_exists('deactivate_plugins') )
        {
            deactivate_plugins( __FILE__ );
        }
        die( 'Install this plugin on the main site only.' );
    }
}
//make submenu 
add_action("admin_menu", "tiengviet_io_options_submenu");
function tiengviet_io_options_submenu() {
  add_submenu_page(
        'options-general.php',
        '
        ',
        'Trạng thái spin tiengvietIO',
        'administrator',
        'spin-options',
        'spin_status_settings_page' );
}
// Add custom fields for the Project post type
function prefix_add_fields_project( $meta_boxes) {
    $meta_boxes[] = [
        'title'      => 'Tiếng việt IO',
        'post_types' => 'wp_automatic',
        'fields'     => [
           
           
            [
                'type'  => 'heading',
                'name'  => 'bạn hãy thêm thẻ tag [LOẠI TAG] này vào Post template để spin',
               
            ],
            [
                'type'  => 'heading',
                'name'  => 'Thẻ loại 1 [SPIN_CONTENT_WITH_TIENGVIETIO] này nếu nhiều hơn 2000 từ sẽ chia đôi và spin hai phần',
               
            ],
            [
                'type'  => 'heading',
                'name'  => 'Thẻ loại 2 [SPIN_CONTENT_WITH_TIENGVIETIO_2000] này nếu nhiều hơn 2000 từ sẽ chia đôi và spin phần 2000 chữ đầu',
               
            ],
            [
                'type'  => 'heading',
                'name'  => '-',
                
                'class' => 'anhminhhoa',
            ],
            [
                'type' => 'heading',
                'name' => 'Để thực hiện chức năng spin với tiengvietIO(kiểm tra tài khoản tại menu tieng-viet-spin-api)',
            ],
        ],
    ];
    return  $meta_boxes;
}
add_filter( 'rwmb_meta_boxes', 'prefix_add_fields_project' );

// Add custom fields for the Project post type
function prefix_add_fields_autolink( $meta_boxes) {
    $meta_boxes[] = [
        'title'      => 'Hướng dẫn auto link',
        'post_types' => 'wp_automatic',
        'fields'     => [
           
           
            [
                'type'  => 'heading',
                'name'  => 'bạn hãy thêm thẻ tag [AUTO_BUILD_INTERNAL_LINK] này vào Post template để tự động đi link tương tự Spin AI',
               
            ],
        ],
    ];
    return  $meta_boxes;
}
add_filter( 'rwmb_meta_boxes', 'prefix_add_fields_autolink' );
// đếm số chữ trong đoạn chuỗi
function get_num_of_words($string) {
    $string = preg_replace('/\s+/', ' ', trim($string));
    $words = explode(" ", $string);
    return count($words);
}
// tách chuối thành hai nghàn chữ đầu tiên và phần còn lại
function split_2000_words_and_the_rest($string){
    $origin = $string;
    $string = preg_replace('/\s+/', ' ', trim($string));
    $words = explode(" ", $string);
    $b = "";
    $i = 0;
    foreach($words as $a){
        $i++;
        if($i<=2000){
            $b = $b.$a;
        }

   }

   $mid = strlen($b)+1999;
   $c = array();
   $c[0] = substr($origin,0,$mid); 

   $c[1] =  substr($origin,$mid+1,strlen($origin)-$mid); 

    return $c;
}
function statustoken_table() {
  //khai báo biến tại đây
  global $wpdb;
  return  $wpdb->prefix.'statustoken';
}
function statusdata_table() {
    //khai báo biến tại đây
    global $wpdb;
    return  $wpdb->prefix.'statusdata';
  }
  //khai báo table chứa thông tin thống kê
function Statistics_table() {
    global $wpdb;
    return $wpdb->prefix.'statistics';
}
function spin_by_tiengviet_io($output){

    // echo $output['post_content'];
    global $wpdb;

    // tách ảnh
    $array = preg_split('/(<img[^>]+\>)/i', $output['post_content'], -1, PREG_SPLIT_DELIM_CAPTURE);
    $i = 0;
    $content ="";
    $imgarray = array();
    foreach($array as $a){
         if (strpos($a, '<img') !== false){
            $imgarray[$i] = $a;
            $a = "img_".$i;
            $i = $i+1;
            }
         $content = $content.$a;
    }
    //use for installing only
    // $imgarray = array();
    // $data = array("text" => $output['post_content']);
    // $temps = postAPISEO($data, 'tachanh');
    // $content = $temps["text"];
    // $imgarray =  $temps["imgarray"];
    //use for installing only
   
    //lây token

    $token = get_option("tvs_token");
    $wpdb->update(
     statustoken_table(),
     array( "token" => $token ), 
     array( "id" => 1)
    );
    

    //check điều kiện spin
    if (strpos($content, '[SPIN_CONTENT_WITH_TIENGVIETIO]') !== false){
            //xóa tag
             $content =  str_replace("[SPIN_CONTENT_WITH_TIENGVIETIO]", "", $content);

            // đếm từ trong chuỗi
            $len = get_num_of_words($content);

            // tai đây sẽ spin bài post
            if ($len<2000){
                $tam = $content;
                $content = tiengvietIO($content, $token);
                $content = json_decode($content, true);
                if($content["code"]=== 200){
                    $wpdb->insert(statusdata_table(), array(
                        "linkpost" => $output ['post_title']  ,
                        "spinstatus" => $content ['code'] 
                    ));
                    $wpdb->update(
                        statustoken_table(),
                        array( "coinstatus" => $content ['code']  ), 
                        array( "id" => 1)
                       );
                    $content = $content["message"];
                }else{
                    $wpdb->insert(statusdata_table(), array(
                        "linkpost" => $output ['post_title']  ,
                        "spinstatus" => $content ['code'] 
                    ));
                    $wpdb->update(
                        statustoken_table(),
                        array( "coinstatus" =>  $content ['code']  ), 
                        array( "id" => 1)
                       );
                    $content = $tam;
                }
               
            }else{
                $tam = $content;
                
                //cắt chuỗi làm đôi rồi spin
                
                $strlen=strlen($content);                 
                $half= intval($strlen * 0.5);

                $first_part=substr($content,0,$half);             
                $second_part=substr($content,$half,$strlen);

                $tampart2 = $second_part;

                $first_part= tiengvietIO($first_part, $token)   ;   
                $first_part = json_decode($first_part, true);

                if($first_part["code"]=== 200){
                    
                    $first_part = $first_part["message"];

                    $second_part= tiengvietIO($second_part, $token);
                    $second_part = json_decode($second_part, true);
                    if($second_part["code"]=== 200){
                        $wpdb->insert(statusdata_table(), array(
                            "linkpost" => $output ['post_title']  ,
                            "spinstatus" => $second_part ['code'] 
                        ));
                        $wpdb->update(
                            statustoken_table(),
                            array( "coinstatus" =>  $second_part ['code']  ), 
                            array( "id" => 1)
                           );
                        $second_part = $second_part["message"];
                        $content =   $first_part . $second_part ;
                    }else{
                        $wpdb->insert(statusdata_table(), array(
                            "linkpost" => $output ['post_title']  ,
                            "spinstatus" => 103 
                        ));
                        $wpdb->update(
                            statustoken_table(),
                            array( "coinstatus" =>  $second_part ['code']  ), 
                            array( "id" => 1)
                           );
                        $content =   $first_part . $tampart2 ;
                    }
                   

                  
                }else{
                    $wpdb->insert(statusdata_table(), array(
                        "linkpost" => $output ['post_title']  ,
                        "spinstatus" => $first_part ['code'] 
                    ));
                    $wpdb->update(
                        statustoken_table(),
                        array( "coinstatus" =>  $first_part ['code']  ), 
                        array( "id" => 1)
                       );
                    $content = $tam;
                }
               


               

            
            }
    }
    elseif (strpos($content, '[SPIN_CONTENT_WITH_TIENGVIETIO_2000]') !== false){
         //xóa tag
         $content =  str_replace("[SPIN_CONTENT_WITH_TIENGVIETIO_2000]", "", $content);

         // đếm từ trong chuỗi
         $len = get_num_of_words($content);

         // tai đây sẽ spin bài post
         if ($len<2000){
             $tam = $content;
             $content = tiengvietIO($content, $token);
             $content = json_decode($content, true);
             if($content["code"]=== 200){
                 $wpdb->insert(statusdata_table(), array(
                     "linkpost" => $output ['post_title']  ,
                     "spinstatus" => $content ['code'] 
                 ));
                 $wpdb->update(
                     statustoken_table(),
                     array( "coinstatus" => $content ['code']  ), 
                     array( "id" => 1)
                    );
                 $content = $content["message"];
             }else{
                 $wpdb->insert(statusdata_table(), array(
                     "linkpost" => $output ['post_title']  ,
                     "spinstatus" => $content ['code'] 
                 ));
                 $wpdb->update(
                     statustoken_table(),
                     array( "coinstatus" =>  $content ['code']  ), 
                     array( "id" => 1)
                    );
                 $content = $tam;
             }
            
         }else{
            $tam = $content;
             
            //cắt chuỗi làm đôi 2000 từ và phần còn lại rồi spin
            $generalpart = array();
            $generalpart = split_2000_words_and_the_rest($content);

            //use for installing only
            // $data = array("text" => $content);
            // $temps = postAPISEO($data, 'tach2000');
            // $generalpart = $temps["text"];
             //use for installing only

            $first_part = $generalpart[0];            
            $second_part =  $generalpart[1];
            $first_part = tiengvietIO($first_part, $token);
            $first_part = json_decode($first_part, true);
            if($first_part["code"]=== 200){
                $wpdb->insert(statusdata_table(), array(
                    "linkpost" => $output ['post_title']  ,
                    "spinstatus" => $first_part ['code'] 
                ));
                $wpdb->update(
                    statustoken_table(),
                    array( "coinstatus" => $first_part ['code']  ), 
                    array( "id" => 1)
                   );
                   $content = $first_part["message"] . $second_part ;
            }else{
                $wpdb->insert(statusdata_table(), array(
                    "linkpost" => $output ['post_title']  ,
                    "spinstatus" => $first_part ['code'] 
                ));
                $wpdb->update(
                    statustoken_table(),
                    array( "coinstatus" =>  $first_part ['code']  ), 
                    array( "id" => 1)
                   );
                $content = $tam;
            }
               
         }
    }
    
    
   

    //ghép ảnh vào lại vị trí cũ (duyệt mảng ngược để img_10 sẽ thay trước sau đó img_1 thay sau tránh tình trạng thay nhầm img_1 vào cả hai)
    $j=count($imgarray)-1;
    $imgarray = array_reverse($imgarray);//đảo ngược thứ tự mảng
    foreach($imgarray as $b){

            $content =  str_replace("img_".$j, $b, $content);
            $j=$j-1;

    }

    // installing only
    // $data = array("text" => $content, "imgarray" => $imgarray);
    // $temps = postAPISEO($data, 'ghepanh');
    // $content = $temps["text"];
    // installing only

    //thực hiện autolink
    $content = auto_link($content , $output['post_title']);

    $output['post_content'] =  $content ;
    return $output;
}
    
    add_filter('wp_automatic_before_insert', 'spin_by_tiengviet_io'); //MỞ CÁI NÀY RA ĐỂ TEST HIỆN KHÓA LẠI ĐỂ TIẾT KIỆM XU

//spin status page
function spin_status_settings_page(){
    require_once  "views/spinstatus.php";
}

// lấy thông tin id sau post

function get_info_post( $post_id) {

    // Only set for post_type = post!
    if ( 'post' !== get_post_type($post_id) ) {
        return;
    }
    // If this is just a revision then do no thing
    if ( wp_is_post_revision( $post_id ) ) {
        return;
        }
    // Not set for post_status = dralf
    if ( get_post($post_id)->post_status == 'pending' || get_post($post_id)->post_status == 'draft' ) {
        return;
    }
    global $wpdb;
    $table = $wpdb->prefix.'statusdata';
    $querystr  = "SELECT *  FROM $table WHERE post_id  = 0";
    $items = $wpdb->get_results($querystr, OBJECT);

    $post_title = get_the_title( $post_id );
    $id =  $post_id;
    //cập nhật bảng trang thái spin
    foreach($items as $item){

        if($post_title === $item->linkpost){
            
            $wpdb->update(
                statusdata_table(),
                array( "post_id" =>  $id ), 
                array( "id" => $item->id)
               );
        }
        
    }
  

}
add_action( 'save_post', 'get_info_post' );


// chức năng spin lại bài viết bị lỗi spin

add_action("wp_ajax_respin", "respin");
add_action("wp_ajax_nopriv_respin", "respin");

function respin()
{
    global $wpdb;
   
    //lấy content
    $content_post = get_post($_REQUEST["id"]);
    $content = $content_post->post_content;

     // tách ảnh
     $array = preg_split('/(<img[^>]+\>)/i', $output['post_content'], -1, PREG_SPLIT_DELIM_CAPTURE);
     $i = 0;
     $content ="";
     $imgarray = array();
     foreach($array as $a){
          if (strpos($a, '<img') !== false){
             $imgarray[$i] = $a;
             $a = "img_".$i;
             $i = $i+1;
             }
          $content = $content.$a;
     }
     //use for installing only
     // $imgarray = array();
     // $data = array("text" => $output['post_content']);
     // $temps = postAPISEO($data, 'tachanh');
     // $content = $temps["text"];
     // $imgarray =  $temps["imgarray"];
     //use for installing only

    //lây token

    $token = get_option("tvs_token");
    
    // đếm từ trong chuỗi
    $len = get_num_of_words($content);

    // tai đây sẽ spin bài post
    if ($len<2000){
        $tam = $content;
        $content = tiengvietIO($content, $token);
        $content = json_decode($content, true);
        if($content["code"]=== 200){
            $wpdb->update(
                statusdata_table(),
                array( "spinstatus" => $content ['code']  ), 
                array( "post_id" => $_REQUEST["id"])
               );
            $content = $content["message"];
        }else{
            $wpdb->update(
                statusdata_table(),
                array( "spinstatus" => $content ['code']  ), 
                array( "post_id" => $_REQUEST["id"])
               );
            $content = $tam;
        }
       
    }else{
       $tam = $content;
        
       //cắt chuỗi làm đôi 2000 từ và phần còn lại rồi spin
       $generalpart = array(); 
        $generalpart = split_2000_words_and_the_rest($content);

            //use for installing only
            // $data = array("text" => $content);
            // $temps = postAPISEO($data, 'tach2000');
            // $generalpart = $temps["text"];
             //use for installing only

       $first_part = $generalpart[0];            
       $second_part =  $generalpart[1];
       $first_part = tiengvietIO($first_part, $token);
       $first_part = json_decode($first_part, true);
       if($first_part["code"]=== 200){
        $wpdb->update(
            statusdata_table(),
            array( "spinstatus" => $first_part ['code']  ), 
            array( "post_id" => $_REQUEST["id"])
           );
              $content = $first_part["message"] . $second_part ;
       }else{
        $wpdb->update(
            statusdata_table(),
            array( "spinstatus" => $first_part ['code']  ), 
            array( "post_id" => $_REQUEST["id"])
           );
           $content = $tam;
       }
          
    }
    //ghép ảnh vào lại vị trí cũ (duyệt mảng ngược để img_10 sẽ thay trước sau đó img_1 thay sau tránh tình trạng thay nhầm img_1 vào cả hai)
    $j=count($imgarray)-1;
    $imgarray = array_reverse($imgarray);//đảo ngược thứ tự mảng
    foreach($imgarray as $b){

            $content =  str_replace("img_".$j, $b, $content);
            $j=$j-1;

    }

    // installing only
    // $data = array("text" => $content, "imgarray" => $imgarray);
    // $temps = postAPISEO($data, 'ghepanh');
    // $content = $temps["text"];
    // installing only

    //  Cập nhật lại nội dung bài viết
    $my_post = array(
        'ID'           => $_REQUEST["id"],
        'post_content' => $content,
    );
    wp_update_post( $my_post );

    // trả API về cho ajax
    echo json_encode(array("status"=>1, "message"=>"respin ok"));

  
}
/////////////////////////////////////////////////////////////////
//// Code cho chức năng auto link bắt đầu từ chỗ này/////////////
/////////////////////////////////////////////////////////////////

//tạo menu phụ cho bảng thống kê auto link
add_action("admin_menu", "auto_link_options_submenu");
function auto_link_options_submenu() {
  add_submenu_page(
        'options-general.php',
        '
        ',
        'Thống kê auto-link',
        'administrator',
        'AL-options',
        'Auto_Link_settings_page' );
}
function Auto_Link_settings_page(){
    require_once  "views/ALstatistics.php";
}

// bắt đầu các chức năng từ chỗ này

function auto_link($content, $title){
//check nếu có thẻ auto link thì làm
    if (strpos($content, "[AUTO_BUILD_INTERNAL_LINK]") !== false){

       
     //xóa tag
          $content =  str_replace("[AUTO_BUILD_INTERNAL_LINK]", "", $content);
          $content =  preg_replace('/<a[^>]*>Source link<\/a>/i', "", $content);
        //cắt content ra thành nhiều đoạn
        preg_match_all('/<([^\s>]+)(.*?)>((.*?)<\/\1>)?|(?<=^|>)(.+?)(?=$|<)/i',$content,$temps);
            $temps = $temps[0];

        // istalling only
        // $data = array("text" => $content);
        // $temps = postAPISEO($data, 'catnho');
        // $temps = $temps["text"];
         // istalling only
            // var_dump($temps);
            //      die();

        $content = "";
        $array_Atag = array();
        $w=0;
        foreach($temps as $temp){
           
 
           
            //nếu đoạn có thẻ a 
            if (strpos($temp, '<a') !== false){
                
                 //remove hết thẻ strong đi sau đó mới dùng getKeywordInAtags() được
            $temp=  str_replace('<strong>', '', $temp);
            $temp=  str_replace('</strong>', '', $temp);
            $temp=  str_replace('<b>', '', $temp);
            $temp=  str_replace('</b>', '', $temp);
   
                //lấy khóa ra khỏi thẻ a
                $key = getKeywordInAtags($temp);
               
                //installing only
                //  $data = array("text" => $temp);
                //  $temps = postAPISEO($data, 'layanchor');
                //  $key =  $temps["text"];
                 //installing only
               
               if ($key != "" && $key != NULL){
               
               
                    //lấy link ngẫu nhiên bằng khóa 
                    $url_income = getLink($key, $title);
                    echo "<hr>";
                    echo $url_income;
                    echo "<hr>";
                  

                    //installing only
                    // $data = array("text" => $temp);
                    // $temps = postAPISEO($data, 'xoaa');
                    // $temp =  $temps["text"];
                   //installing only

                    $mainsite = bloginfo('url');
                    if ($url_income == "NA" || $url_income == $mainsite){
                          //xóa link thẻ a cũ
                        $temp = preg_replace('/<a[^>]*>([\s\S]*?)<\/a>/i','\1', $temp);
                       
                    }else{
                        //xóa link thẻ a cũ
                          $temp = preg_replace('/<a[^>]*>([\s\S]*?)<\/a>/i','\1', $temp);
                        // thay thẻ a cũ băng link của web mình ->chức năng 2.1
                        $temp =str_replace_first( $key, '<a href="'.$url_income.'"><b style="color:blue !important;">'.$key.'</b><a/>', $temp );
                     
                        
                    }

                }else{
                     //nếu có baner quảng cáo của web gốc chứa link xóa luôn 
                     $temp = preg_replace('/<a[^>]*>/i','', $temp);
                }
             
            }
               

             
            $content = $content. $temp;
        }
      //ghép content lại(ở đây sẽ chèn link xem thêm-> chức năng 2.2 vô)

                $pq = explode('</p>', $content);

                $content = "";
                $n = count($pq);
                $k = 0;
                foreach ($pq as $o){
                    
                
                $content = $content. $o."</p>";
                if ($k== ceil($n/3)){
                    //lấy link ngẫu nhiên 
                    $url_rand = getRandomLink( $title);
                    
                    $content = $content.'<br>'.'<a href="'. get_permalink($url_rand).'"<b style="color:blue !important;">>>>Xem thêm: '. get_the_title($url_rand).'</b></a>';
                
                    }
                    if ($k== (ceil($n/1.5))){
                    //lấy link ngẫu nhiên 
                    $url_rand = getRandomLink( $title); 
                        
                    $content = $content.'<br>'.'<a href="'. get_permalink($url_rand).'"<b style="color:blue !important;">>>>Xem thêm: '. get_the_title($url_rand).'</b></a>';
                    
                    }
                $k++;
                }      
    }
   // xóa hết  text-decoration của theme
//    $content= '<style>.tatdecor a { text-decoration:none !important; color: black !important;}</style><div class="tatdecor">'.$content.'</div>';




return $content;
}
// replace mảng chỉ áp dụng  cho đối tượng đầu
function str_replace_first($search, $replace, $subject)
{
    $search = '/'.preg_quote($search, '/').'/';
    return preg_replace($search, $replace, $subject, 1);
}


// lấy khóa ra khỏi thẻ a remove installing only
function getKeywordInAtags($string) {
    $pattern = "/<a?.*>(.*)<\/a>/";
    preg_match($pattern, $string, $matches);
    return $matches[1];
}

//lấy link ngẫu nhiên bằng khóa 
function getLink($key, $title){
  
    global  $wpdb ;
   
    $tablePost = $wpdb->prefix.'posts';
    $querystr  = "SELECT *  FROM $tablePost  WHERE post_status = 'publish' AND post_type = 'post' AND post_content LIKE '%$key%' ORDER BY RAND() LIMIT 1";
    $alls = $wpdb->get_results($querystr, OBJECT);
   // nếu khóa có income post thì trả về đường dẫn của income post
    if($alls){
        foreach($alls as $item):
            $all = $item; 
        endforeach;
        
        $idfrom = $all->ID;
        $wpdb->insert(Statistics_table(), array(
            "link_from" => $idfrom  ,
            "title_to" => $title,
            "anchor" => $key
        ));

        $url_income = $all->guid;
         return $url_income;
    }
    //nếu khóa không có income post thì trả vê trang chủ
    $url_income = "NA";
    return $url_income;
    

}
//lấy link ngẫu nhiên
function getRandomLink( $title){
 
    global  $wpdb ;
   
    $tablePost = $wpdb->prefix.'posts';
    $querystr  = "SELECT *  FROM $tablePost  WHERE post_status = 'publish' AND post_type = 'post' ORDER BY RAND() LIMIT 1";
    $alls = $wpdb->get_results($querystr, OBJECT);
   
        foreach($alls as $item):
            $all = $item; 
        endforeach;
        
        $idfrom = $all->ID;
        $wpdb->insert(Statistics_table(), array(
            "link_from" => $idfrom  ,
            "title_to" => $title,
            "anchor" => "xem thêm"
        ));

         return $idfrom;
 

}

// cập nhật id cho outcome post trong bảng thống kê autolink
function get_info_post_autolink( $post_id) {
   
     
        // Only set for post_type = post!
        if ( 'post' !== get_post_type($post_id) ) {
            return;
        }
        // Not set for post_status = dralf
        if ( get_post($post_id)->post_status == 'pending' || get_post($post_id)->post_status == 'draft' ) {
            return;
        }
        // If this is just a revision then do no thing
        if ( wp_is_post_revision( $post_id ) ) {
            return;
            }
        
        global $wpdb;
        $table = $wpdb->prefix.'statistics';
        $querystr  = "SELECT *  FROM $table WHERE link_to  = 0";
        $items = $wpdb->get_results($wpdb->prepare($querystr), OBJECT);

        $post_title = get_the_title( $post_id );
        // echo "///////////////////###############".$post_title;
        // $post_title = substr($post_title,- 1).substr($post_title,0,1); 
        $id =  $post_id;
         
        //cập nhật bảng thống kê auto link
        foreach($items as $item){
            // $check = substr($item->title_to, -1).substr($item->title_to,0,1);
            // echo "//////////////////".$post_title;
            // echo "###############".$item->title_to;
           
           
            if(isUTF8($post_title) === isUTF8($item->title_to)){
                global $wpdb;
                 $wpdb->get_results($wpdb->prepare("UPDATE $table SET link_to = $id WHERE id =  $item->id"));
                
            }
            
           
       
        }

    
    
    
}

add_action( 'save_post', 'get_info_post_autolink' );

function isUTF8($string) {
    return (utf8_encode(utf8_decode($string)) == $string);
}
 /**
     * Get Incoming Links Count
     *
     */

 function getIncomingLinksCount( $id)
    {
        global  $wpdb ;
        $ilj_linkindex_table = $wpdb->prefix . "statistics";
        $incoming_links = $wpdb->get_var( "SELECT count(link_from) FROM {$ilj_linkindex_table} WHERE (link_from = '" . $id . "')" );
        return (int) $incoming_links;
    }
    
    /**
     * Get Outgoing Links Count
     *
     */
 function getOutgoingLinksCount( $id )
    {
        global  $wpdb ;
        $ilj_linkindex_table = $wpdb->prefix . "statistics";
        $outgoing = $wpdb->get_var( "SELECT count(link_to) FROM {$ilj_linkindex_table} WHERE (link_to = '" . $id . "')" );
        return (int) $outgoing;
    }

// setup Category option
register_activation_hook( __FILE__, 'changeCT_activation' );

function changeCT_activation(){
    
    add_option('CT_option', "category");
    add_option('Blog_name', "mặc định");
}

// Đổi chuyên mục
add_action("wp_ajax_changeCT", "changeCT");
add_action("wp_ajax_nopriv_changeCT", "changeCT");

function changeCT()
{
    update_option('CT_option', $_REQUEST["CT"]);
    // trả API về cho ajax
    echo json_encode(array("status"=>1, "message"=>"changeCT ok"));
   
}

// --------------------------------------------------Tạo page thống kê chiến dịch----------------------------------------------


//tạo menu phụ
add_action("admin_menu", "thongkechiendich_options_submenu");
function thongkechiendich_options_submenu() {
  add_submenu_page(
        'options-general.php',
        '
        ',
        'Trạng thái chiến dịch',
        'administrator',
        'CPS-options',
        'cp_status_settings_page' );
}

function cp_status_settings_page(){
    require_once  "views/cp_status.php";
}

// Đổi blog
add_action("wp_ajax_changeCPS", "changeCPS");
add_action("wp_ajax_nopriv_changeCPS", "changeCPS");

function changeCPS()
{
    update_option('Blog_name', $_REQUEST["CPS"]);
    // trả API về cho ajax
   
    echo json_encode(array("status"=>1, "message"=>"changeCPS ok"));
   
}
// ngày cào cuối cùng
function lastpost($camp_id, $domainname){
    global $wpdb;
    $CPS =  $domainname;
    $queryCPS = "SELECT * FROM `wp_blogs` WHERE domain ='$CPS'";
    $theprefix =  $wpdb->get_results($queryCPS, OBJECT);
    $theprefix = "wp_".(($theprefix[0]->blog_id==1)?"":$theprefix[0]->blog_id."_");
    @$key='Posted:'.$camp_id;
    $table = $theprefix.'automatic_log';
    //getting count from wplb_log
    $query="SELECT `date`  FROM  $table WHERE action ='$key'  ORDER BY `date` DESC LIMIT 1";
    $ress= $wpdb->get_results($query, OBJECT);
   
    @$res= $ress[0];
    // @$resold = $ress[1];

    date_default_timezone_set('Asia/Ho_Chi_Minh');
    $now = time();
    $current =  $res->date;
    // $old =  $resold->date;
    $time = date_parse_from_format('Y-m-d H:i:s', $current);
    $time_stamp = mktime($time['hour'],$time['minute'],$time['second'],$time['month'],$time['day'],$time['year']);
    
    // $time_stampminute = floor(abs(strtotime($current) - strtotime($old))/(24*60)); 
  
    $status = "live";
    if(($now - $time_stamp) > 2*24*60*60){
        $status =  'stop';
    }
    return $res->date."->".$status;
}
// lượng bài đã cào
function crawlpost($camp_id, $domainname){
    global $wpdb;
    $CPS =  $domainname;
    $queryCPS = "SELECT * FROM `wp_blogs` WHERE domain ='$CPS'";
    $theprefix =  $wpdb->get_results($queryCPS, OBJECT);
    $theprefix = "wp_".(($theprefix[0]->blog_id==1)?"":$theprefix[0]->blog_id."_");
    @$key='Posted:'.$camp_id;
    $table = $theprefix.'automatic_log';
    //getting count from wplb_log
    $query="SELECT COUNT(`id`) as numpost FROM  $table WHERE action ='$key'";
    $res= $wpdb->get_results($query, OBJECT);
   
    @$res=$res[0];
   return $res->numpost;
      
}
// thời gian giữa hai lần cào
function geteachtime($camp_id, $domainname){
    global $wpdb;
    $CPS =  $domainname;
    $queryCPS = "SELECT * FROM `wp_blogs` WHERE domain ='$CPS'";
    $theprefix =  $wpdb->get_results($queryCPS, OBJECT);
    $theprefix = "wp_".(($theprefix[0]->blog_id==1)?"":$theprefix[0]->blog_id."_");
    $table = $theprefix.'automatic_camps';
    $querycp = "SELECT * FROM $table WHERE camp_id = $camp_id ";
    $cp =   $wpdb->get_results($querycp, OBJECT);
    $camp_general = $cp[0]->camp_general;
    $camp_general = unserialize ( base64_decode ( $camp_general ));
    $post_every = $camp_general ['cg_update_every'] * $camp_general ['cg_update_unit'];
    return $post_every;

}

//send mail

   /////////    ///////////    /////////////    ////        ////
////            ////    ////   ////     ////    ////////    ////
////            ////  ////     ////     ////    //// ////   ////
////            /////////      ////     ////    ////  ////  ////  
////            ////    ////   ////     ////    ////    ////////
   /////////    ////     ////  /////////////    ////        ////

//lib mail 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
// // set up for cron
// register_activation_hook( __FILE__, 'cronsetup_activation' );

// function cronsetup_activation(){
//     add_option('sendmail', FALSE);
// }
// Bật tắt cronjob
// add_action("wp_ajax_changeSMswitch", "changeSMswitch");
// add_action("wp_ajax_nopriv_changeSMswitch", "changeSMswitch");

// function changeSMswitch()
// {
//     update_option('sendmail', !get_option('sendmail'));
//     // trả API về cho ajax
//     echo json_encode(array("status"=>1, "message"=>"changeSMswitch ok"));
//     wp_unschedule_hook('sendmail_cron');
// }
//adding custom interval
add_filter( 'cron_schedules', 'My_a_day_cron_interval' );
function My_a_day_cron_interval( $schedules ) { 
    $schedules['motngay'] = array(
        'interval' =>86400,
        //nếu muốn test đổi 86400 này thành 60, tương ứng 1 phút làm một lần sau đó qua bên cp_status.php làm như dưới đây
        ////////////////tìm dòng này trong cp_status.php Chú ý mỏ cái này để reset thời gian mới cho cronjob. sau đó load trang này rồi khóa lại
        'display'  => esc_html__( 'Everyday' ), );
    return $schedules;
}
add_filter( 'cron_schedules', 'My_thirty_mitute_cron_interval' );
function My_thirty_mitute_cron_interval( $schedules ) { 
    $schedules['thirty_minute'] = array(
        'interval' => 1800,
        'display'  => esc_html__( 'Every thirty minutes' ), );
    return $schedules;
}
add_filter( 'cron_schedules', 'My_one_hour_cron_interval' );
function My_one_hour_cron_interval( $schedules ) { 
    $schedules['one_hour'] = array(
        'interval' => 3600,
        'display'  => esc_html__( 'Every one hour' ), );
    return $schedules;
}

//setting my custom hook wp cron job
add_action('sendmail_cron', 'sendmail_cron_implement');

//the event function

function sendmail_cron_implement(){
    addPBN_ajax_handler();
    global $wpdb;

    $querystr = "SELECT * FROM wp_blogs";
    $cpes = $wpdb->get_results($querystr, OBJECT);
    $cp = array();
    $webname = array();
    $theprefix = array();
    $k = 1;
    foreach ($cpes as $cpe){
        $webname[$k] = $cpe->domain;
        $theprefix[$k]  = "wp_".(($cpe->blog_id==1)?"":$cpe->blog_id."_");
        $table = $theprefix[$k].'automatic_camps';
        $querystrsub = "SELECT * FROM $table WHERE camp_post_status = 'publish' ";
        $cp[$k] = $wpdb->get_results($querystrsub, OBJECT);
        $k++;
    }
    $data_array = array ();
    for($j=1; $j<=$k; $j++){
        foreach($cp[$j] as $item){
            $temp = array ($webname[$j],$item->camp_name,$item->camp_post_content,lastpost($item->camp_id, $webname[$j]),crawlpost($item->camp_id, $webname[$j]),geteachtime($item->camp_id, $webname[$j]));
            // var_dump($temp);
            array_push($data_array,$temp );
        }
    }
    
   
    if( get_current_blog_id() === 1 ){
        // Return something if the site ID matches the number one...
           
            
            $csv = "domain,camp_name,content,lastpost,numofpost,frequently \n";//Column headers
            foreach ($data_array as $record){
            $csv.=  str_replace("\r\n","",str_replace("<br>", "", $record[0])).','. str_replace("\r\n","",str_replace("<br>", "", $record[1])).','. str_replace("\r\n","",str_replace("<br>", "", $record[2])).','. str_replace("\r\n","",str_replace("<br>", "", $record[3])).','. str_replace("\r\n","",str_replace("<br>", "", $record[4])).','. str_replace("\r\n","",str_replace("<br>", "", $record[5]))."\n"; //Append data to csv
            }
            $csvname = ABSPATH  .'report.csv';
            //If the file exists and is writeable
            if(is_writable($csvname)){
                //Delete the file
                $deleted = unlink($csvname);
            }
            $csv_handler = fopen ($csvname,'w') ;

            chmod($csvname, 0777);

            file_put_contents($csvname, $csv);

            $mail = new PHPMailer(true);

            try {
                //Server settings
                $mail->SMTPDebug = 2;                                        //Enable verbose debug output
                $mail->isSMTP();                                            //Send using SMTP
                $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
                $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
                $mail->Username   = 'vuvandai2024@gmail.com';                     //SMTP username
                $mail->Password   = 'bean1991';                               //SMTP password
                $mail->SMTPSecure = 'tls';                                  //Enable implicit TLS encryption
                $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

                //Recipients
                $mail->setFrom('vuvandai2024@gmail.com',$webname[1]);
                $mail->addAddress('vnae88888886@gmail.com', 'admin'); 
                // $mail->addAddress('tuuvv.uit@gmail.com', 'admin'); //get_option("tvs_email")
                $mail->addCC('tuuvv.uit@gmail.com');

                //Attachments
                $mail->addAttachment($csvname , 'report.csv');           //Optional name

                //Content
                $mail->isHTML(true);                                     //Set email format to HTML
                $mail->Subject = 'mail daily camp status report';
                $mail->Body    = '<h1>File báo cáo được đính kèm trong mail vui lòng tải về và đọc</h1>';

                return $mail->Send();
                echo 'Message has been sent';
                } catch (Exception $e) {
                    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }

    }
 

   
    
}

//scheduling recurring event
if(! wp_next_scheduled( "sendmail_cron" )){
    wp_schedule_event( time(), 'motngay', 'sendmail_cron' );
}


   /////////    ///////////    /////////////    ////        ////
////            ////    ////   ////     ////    ////////    ////
////            ////  ////     ////     ////    //// ////   ////
////            /////////      ////     ////    ////  ////  ////  
////            ////    ////   ////     ////    ////    ////////
   /////////    ////     ////  /////////////    ////        ////

//tạo menu phụ cho chức năng chuyển hướng về SEO
add_action("admin_menu", "redirrect_options_submenu");
function redirrect_options_submenu() {
  add_submenu_page(
        'options-general.php',
        '
        ',
        'Điều hướng tăng view',
        'administrator',
        'RD-options',
        'redirrect_settings_page' );
}
function redirrect_settings_page(){
    require_once  "views/redirrect.php";
}
// biến điều kiện đã có danh sách chưa
register_activation_hook( __FILE__, 'listDM_activation' );

function listDM_activation(){
    
    add_option('Is_has_DM', false);
    add_option('timeRD', 5000);
}
// lưu danh sách miền vào cơ sở dữ liệu
function allDM_table() {
    
    return 'wp_pbn_redirect_statistic'; 
}
add_action("wp_ajax_addDM", 'addDM_ajax_handler');

function addDM_ajax_handler()
{


            global $wpdb;

            update_option('Is_has_DM', true);

            $querystr  = "SELECT * FROM wp_pbn_redirect_statistic";
            $datas = $wpdb->get_results($querystr, OBJECT);
            $arr = explode(",", $_REQUEST['list'],);

            var_dump($datas) ;
            $ishave = false;
            if($datas){
                foreach ($arr as $item) {
                    $item = trim($item );
                    foreach($datas as $data){
                        if ($item == $data->siteSEO){
                            $ishave = true;
                        }
                    }
                    if($ishave==false){
                        $wpdb->insert(allDM_table(), array(
                            "siteSEO" => $item ,
            
                        )); 
                    }
                    $ishave = false;
                }
            }else{
                foreach ($arr as $item) {
                    $item = trim($item );
                    $wpdb->insert(allDM_table(), array(
                        "siteSEO" => $item ,
        
                    )); 
    
                   
                }
            }
            
            
            echo json_encode(array("status"=>1, "message"=>"Save campaigns successfull"));
            wp_die( );

   



}

///////// bậy tắt điều hướng cho tên miền
add_action("wp_ajax_battat", 'battat_ajax_handler');

function battat_ajax_handler()
{

    global $wpdb;

    $id = $_REQUEST["id"];
    $querystr  = "SELECT * FROM wp_pbn_redirect_statistic WHERE id =".$id." LIMIT 1";
    $item = $wpdb->get_results($querystr, OBJECT);
    echo $item[0]->onoff ;

    if($item[0]->onoff == 1){
        $wpdb->update(
            'wp_pbn_redirect_statistic',
            array( "onoff" => 0 ), 
            array( "id" => $id )
           );
    }else{
        $wpdb->update(
            'wp_pbn_redirect_statistic',
            array( "onoff" => 1 ), 
            array( "id" => $id )
           );
    }


    echo json_encode(array("status"=>1, "message"=>"update domain onoff status successfull"));
    wp_die( );
}

///////////// xóa miền............................
add_action("wp_ajax_deleteDM", 'deleteDM_ajax_handler');

function deleteDM_ajax_handler()
{

    global $wpdb;

    
    $wpdb->delete('wp_pbn_redirect_statistic', array(
        "id" => $_REQUEST['id']
    ));


    echo json_encode(array("status"=>1, "message"=>"delete domains successfull"));
    wp_die( );
}
/////////////////// chọn thời gian điều hướng./////////////////////////////////////
add_action("wp_ajax_timeRD", 'timeRD_ajax_handler');

function timeRD_ajax_handler()
{
    update_option('timeRD',  $_REQUEST['time']);

    echo json_encode(array("status"=>1, "message"=>"ok"));
    wp_die( );
}
/////////////////////////  wp_localize_script( 'ajax-script', 'my_ajax_object',
add_action('wp_body_open', 'my_callback');
add_action('wp_head', 'myplugin_ajaxurl');
function my_callback() {
    $tempDM = getrandDM();
         echo "<div id='show'></div>";
         echo //'<button type="button"  class="btn btn-primary" data-toggle="modal" data-target="data-target="#myModal"">Small modal</button>'+
         '<div class="myModal " id="myModal" >'
         .'<div class=" dialog ">'
          
          .' <div class="content ">'
              .'<a href="http://'.$tempDM.'" onclick="count()" target="_blank"  "><img src="https://image.shutterstock.com/image-vector/click-here-button-hand-pointer-260nw-1557349979.jpg" alt="bài viết hay" width="100%" height="50%"></a>'
           .' </div>'
         
   
         .' </div>'
        .'</div>';
 }
function myplugin_ajaxurl() {
    $tempDM = getrandDM();
    
    error_reporting(E_ERROR | E_PARSE);
 
    echo '<script type="text/javascript">
jQuery(document).ready(function() {
    
    function count(){
        var postdata = "action=countMD&site="+"'.$tempDM.'";
        jQuery.post(ajaxurl, postdata, function(response) {
          
          // console.log(response);
          var data = jQuery.parseJSON(response);
          temptimeRD = data.message;
        });
        }
    function setCookie(cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays*24*60*60*1000));
        var expires = "expires="+d.toUTCString();
        document.cookie = cname + "=" + cvalue + "; " + expires;
     }
     // Hàm lấy Cookie
     function getCookie(cname) {
        var name = cname + "=";
        var ca = document.cookie.split(";");
        for(var i=0; i<ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0)==" ") c = c.substring(1);
            if (c.indexOf(name) == 0) return c.substring(name.length, c.length);
        }
        return "";
     }
    var ajaxurl = "' . admin_url('admin-ajax.php') . '";
    var giatri = getCookie("RDcoockie");
    console.log(giatri);
    let panel = document.querySelectorAll(".myModal");
    if(giatri == "available"){
        document.getElementById("show").innerHTML =" <style>.myModal {  display:none !important;}";
    }
    setCookie("RDcoockie", "available", 2);
    jQuery(".myModal").onclick = myFunction();
    function myFunction() {
      window.open("http://'.$tempDM.'");
    }
})
  </script>';
         echo ' <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
          <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
         <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>';

        

         echo '
         <style>
.myModal {
    background-color:gray;
    opacity: 50%;
    display:block;
  position: absolute;
  width:100%;
  height:  100%;
  left: 0px;
  top: 0px;
  z-index: 2;
}
.content{
    width:100%;
   
    margin: 0;
    position: absolute;
    top: 50%;
    -ms-transform: translateY(-50%);
    transform: translateY(-50%);
}
</style>
         ';
        
      
}

 function getrandDM()
{
    global $wpdb;
    global $wpdb;
    $table = $wpdb->prefix.'options';
    $querys  = "SELECT * FROM $ WHERE 	option_name = siteurl";
    $cursites = $wpdb->get_results($querys, OBJECT);
    $hostsite = @$cursites[0]->option_value;
    $hostsite =str_replace("https://", "", $hostsite);
    $hostsite =str_replace("http://", "", $hostsite);

    $query  = "SELECT * FROM wp_pbn_site ";
    $querystr  = "SELECT * FROM wp_pbn_redirect_statistic WHERE onoff = 1 ";
    $SEOs = $wpdb->get_results($querystr, OBJECT);
    $PBNs = $wpdb->get_results($query, OBJECT);

    foreach($PBNs as $PBN){
        if($PBN->sitePBN == $hostsite){
            $TempPBN = $PBN;
        }
    }
@$TempPBN = explode(",", $TempPBN->category);
    $ArrRand = array();
    $i =0;


    foreach($TempPBN as $motmuc){
        foreach ($SEOs as $SEO){
            if (strpos($SEO->category, $motmuc) !== false){
                $ArrRand[$i] = $SEO->siteSEO;
                $i++;
            }
        }
    }

    // echo 'SEO' .var_dump($SEOs);
    // echo "PBN" .var_dump($TempPBN);
    // echo "rand" .var_dump($ArrRand);

    if(count($ArrRand)==0){
        $n = array_rand($SEOs,1);
        $Randsite = $SEOs[$n]->siteSEO;
    }else{
        $n = array_rand($ArrRand,1);
        $Randsite = $ArrRand[$n];
    }
  
    
    
   
    return $Randsite ;
    
}

add_action( 'wp_enqueue_scripts', 'wpshare247_register_scripts' );
function wpshare247_register_scripts() {
      if(get_option("Jquery")==0){
     
    // wp_enqueue_script('Jquery.new', plugins_url('jquery.min.js',__FILE__ ), array(), '1.0', true );
    wp_enqueue_script('jquery-migrate-1.4.1.min.js', plugins_url('jquery-migrate-1.4.1.min.js',__FILE__ ), array(), '1.0', true );
   }
   wp_enqueue_style('bo_css', plugins_url('custom.css',__FILE__ ), array(), '1.0', true);
    wp_enqueue_script( 'my_custom_2.js', plugins_url('redirect.js',__FILE__ ) , array(), '1.0', true );
    
}
/////////////////////////kiểm tra có danh sách miền hay chưa//////////////////////////////

add_action("wp_ajax_getishaveDM", 'getishaveDM_ajax_handler');
add_action("wp_ajax_nopriv_getishaveDM", 'getishaveDM_ajax_handler');

function getishaveDM_ajax_handler()
{
    global $wpdb;
    $querystr  = "SELECT * FROM wp_options WHERE option_name = 'Is_has_DM' ";
    $Is_has_DM = $wpdb->get_results($querystr, OBJECT);
    
    echo json_encode(array("status"=>1, "message"=> $Is_has_DM[0]->option_value));
    wp_die( );
}
//////////////////////////////lấy một miền ngẫu nhiên có trạng thái bật//////////////////////////////
add_action("wp_ajax_getrandDM", 'getrandDM_ajax_handler');
add_action("wp_ajax_nopriv_getrandDM", 'getrandDM_ajax_handler');

function getrandDM_ajax_handler()
{
    global $wpdb;
    $hostsite = $_REQUEST["hostsite"];
    $query  = "SELECT * FROM wp_pbn_site ";
    $querystr  = "SELECT * FROM wp_pbn_redirect_statistic WHERE onoff = 1 ";
    $SEOs = $wpdb->get_results($querystr, OBJECT);
    $PBNs = $wpdb->get_results($query, OBJECT);

    foreach($PBNs as $PBN){
        if($PBN->sitePBN == $hostsite){
            $TempPBN = $PBN;
        }
    }
    $TempPBN = explode(",", $TempPBN->category);
    $ArrRand = array();
    $i =0;


    foreach($TempPBN as $motmuc){
        foreach ($SEOs as $SEO){
            if (strpos($SEO->category, $motmuc) !== false){
                $ArrRand[$i] = $SEO->siteSEO;
                $i++;
            }
        }
    }

    // echo 'SEO' .var_dump($SEOs);
    // echo "PBN" .var_dump($TempPBN);
    // echo "rand" .var_dump($ArrRand);

    if(count($ArrRand)==0){
        $n = array_rand($SEOs,1);
        $Randsite = $SEOs[$n]->siteSEO;
    }else{
        $n = array_rand($ArrRand,1);
        $Randsite = $ArrRand[$n];
    }
  
    
    
   
    echo json_encode(array("status"=>1, "message"=>$Randsite ));
    wp_die( );
    
}
////////////////////////////// lấy thời gian điều hướng hiện tại//////////////////////////////
add_action("wp_ajax_getimeRD", 'getimeRD_ajax_handler');
add_action("wp_ajax_nopriv_getimeRD", 'getimeRD_ajax_handler');

function getimeRD_ajax_handler()
{

    echo json_encode(array("status"=>1, "message"=> get_option('timeRD')));
    wp_die( );
}
add_action("wp_ajax_countMD", 'countMD_ajax_handler');
add_action("wp_ajax_nopriv_countMD", 'countMD_ajax_handler');

function countMD_ajax_handler()
{
    global $wpdb;
    $siteRD = $_REQUEST["site"];
    $querystr  = "SELECT * FROM wp_pbn_redirect_statistic ";
    $items = $wpdb->get_results($querystr, OBJECT);
    
//    echo $siteRD;
    // var_dump($items) ;
    foreach($items as $ite){
        if($ite->siteSEO== $siteRD){
            $wpdb->update(
                'wp_pbn_redirect_statistic',
                array( "countRD" => $ite->countRD+1 ), 
                array( "siteSEO" => $siteRD )
            );
        }
    }
    
    echo json_encode(array("status"=>1, "message"=> 'kook'));
    wp_die( );
}
// lưu danh sách miền PBN vào cơ sở dữ liệu
function allPBN_table() {
    
    return 'wp_pbn_site'; 
}
add_action("wp_ajax_addPBN", 'addPBN_ajax_handler');

function addPBN_ajax_handler()
{


            global $wpdb;
            $querystr  = "SELECT * FROM wp_pbn_site";
            $PBNs = $wpdb->get_results($querystr, OBJECT);
            $queryCPS = "SELECT * FROM `wp_blogs` ";
            $wpPBNs =  $wpdb->get_results($queryCPS, OBJECT);
           
            // var_dump($wpPBNs);
            // var_dump($PBNs);

            $ishave = false;
           
                foreach ($wpPBNs as $item) {
                   
                    if($PBNs){
                        foreach($PBNs as $PBN){
                            
                            if ($item->domain == $PBN->sitePBN){
                                $ishave = true;
                            }
                        }
                    }
                   
                    if($ishave==false){
                        // echo $item->domain;
                        $wpdb->insert(allPBN_table(), array(
                            "sitePBN" => $item->domain ,
            
                        )); 
                    }
                    $ishave = false;
                }
           
            
            echo json_encode(array("status"=>1, "message"=>"Save PBN successfull"));
            // wp_die( );

   



}
///////////// xóa miền PBN............................
add_action("wp_ajax_deletePBN", 'deletePBN_ajax_handler');

function deletePBN_ajax_handler()
{

    global $wpdb;

    
    $wpdb->delete('wp_pbn_site', array(
        "id" => $_REQUEST['id']
    ));


    echo json_encode(array("status"=>1, "message"=>"delete domains successfull"));
    wp_die( );
}


// lưu danh sách miền CATE vào cơ sở dữ liệu
function allCATE_table() {
    
    return 'wp_site_category'; 
}
add_action("wp_ajax_addCATE", 'addCATE_ajax_handler');

function addCATE_ajax_handler()
{


            global $wpdb;
            $querystr  = "SELECT * FROM wp_site_category";
            $datas = $wpdb->get_results($querystr, OBJECT);
            $arr = explode(",", $_REQUEST['list'],);

            var_dump($datas) ;
            $ishave = false;
            if($datas){
                foreach ($arr as $item) {
                    $item = trim($item );
                    foreach($datas as $data){
                        if ($item == $data->category){
                            $ishave = true;
                        }
                    }
                    if($ishave==false){
                        $wpdb->insert(allCATE_table(), array(
                            "category" => $item ,
            
                        )); 
                    }
                    $ishave = false;
                }
            }else{
                foreach ($arr as $item) {
                    $item = trim($item );
                    $wpdb->insert(allCATE_table(), array(
                        "category" => $item ,
        
                    )); 
    
                   
                }
            }
            
            
            echo json_encode(array("status"=>1, "message"=>"Save CATE successfull"));
            wp_die( );

   



}
///////////// xóa miền CATE............................
add_action("wp_ajax_deleteCATE", 'deleteCATE_ajax_handler');

function deleteCATE_ajax_handler()
{

    global $wpdb;

    
    $wpdb->delete('wp_site_category', array(
        "id" => $_REQUEST['id']
    ));


    echo json_encode(array("status"=>1, "message"=>"delete domains successfull"));
    wp_die( );
}


/////////////////////// lưu tên CATE vào một site SEO ////////////////////////////////////
add_action("wp_ajax_NHAPSEOCATE", 'NHAPSEOCATE_ajax_handler');



function NHAPSEOCATE_ajax_handler()
{


            global $wpdb;
            $id =  $_REQUEST['custId'];
            $querystr  = "SELECT * FROM wp_pbn_redirect_statistic WHERE id = $id";
            $datas = $wpdb->get_results($querystr, OBJECT);
           

           
            if($datas[0]->category!=""){
                $wpdb->update(
                    'wp_pbn_redirect_statistic',
                    array( "category" => $datas[0]->category.",". $_REQUEST['CATEs'] ), 
                    array( "id" => $id)
                   );
            }else{
                $wpdb->update(
                    'wp_pbn_redirect_statistic',
                    array( "category" => $datas[0]->category. $_REQUEST['CATEs'] ), 
                    array( "id" => $id)
                   );
            }
            
            
            echo json_encode(array("status"=>1, "message"=>$datas[0]->siteSEO));
            wp_die( );

   



}

/////////////////////// lưu tên CATE vào một site PBN ////////////////////////////////////
add_action("wp_ajax_NHAPPBNCATE", 'NHAPPBNCATE_ajax_handler');



function NHAPPBNCATE_ajax_handler()
{


            global $wpdb;
            $id =  $_REQUEST['custId'];
            $querystr  = "SELECT * FROM wp_pbn_site WHERE id = $id";
            $datas = $wpdb->get_results($querystr, OBJECT);
           

          
          
            if($datas[0]->category!=""){
                $wpdb->update(
                    'wp_pbn_site',
                    array( "category" => $datas[0]->category.",". $_REQUEST['CATEs'] ), 
                    array( "id" => $id)
                   );
            }else{
                $wpdb->update(
                    'wp_pbn_site',
                    array( "category" => $datas[0]->category. $_REQUEST['CATEs'] ), 
                    array( "id" => $id)
                   );
            }
            
            
            echo json_encode(array("status"=>1, "message"=>$datas[0]->sitePBN));
            wp_die( );

   



}
///////////// lấy HTMl domain random...........................
add_action("wp_ajax_getrandDMMTHL", 'getrandDMMTHL_ajax_handler');

function getrandDMMTHL_ajax_handler()
{
    // $htmldomain = file_get_contents("http://".$_REQUEST["domain"]);
    $htmldomain = "<h1>test cái đã</h1>";
 echo "##########".$htmldomain;


    echo json_encode(array("status"=>1, "message"=>$htmldomain));
    wp_die( );
}