<?php

/*
  Plugin Name: tieng viet spin API
  Version: 0.1
  Author: Space-Themes.com
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

// custom css and js
add_action('admin_enqueue_scripts', 'conf_css_and_js');
 
function conf_css_and_js($hook) {
    // your-slug => The slug name to refer to this menu used in "add_submenu_page"
        // tools_page => refers to Tools top menu, so it's a Tools' sub-menu page
    // wp_enqueue_style('dataTables_css', plugins_url('css/jquery.dataTables.min.css',__FILE__ ));
    wp_enqueue_style('boot_css', plugins_url('custom.css',__FILE__ ));
    // wp_enqueue_script('dataTables_js', plugins_url('js/jquery.dataTables.min.js',__FILE__ ));
}
//get lib
require 'vendor/autoload.php';
include_once $tvs_plugin_dir . '/tiengviet.php';

// CREATE DATABASE
 
if ( !defined('ABSPATH') )
    define('ABSPATH', dirname(__FILE__) . '/');

/** do the active hook */
require_once dirname(__FILE__). '/includes/statusdata.php';
register_activation_hook( __FILE__, 'statusdata_create' );

register_activation_hook( __FILE__, 'force_main_site_installation' );

function force_main_site_installation()
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
                'name'  => 'bạn hãy thêm thẻ tag  [SPIN_CONTENT_WITH_TIENGVIETIO] này vào Post template để spin',
               
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
function spin_by_tiengviet_io($output){

    
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
                            "spinstatus" => "part2".$second_part ['code']  
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
    if (strpos($content, '[SPIN_CONTENT_WITH_TIENGVIETIO_2000]') !== false){
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
                   $content = $first_part["message"] + $second_part ;
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
    
    global $wpdb;
    $table = $wpdb->prefix.'statusdata';
    $querystr  = "SELECT *  FROM $table WHERE linkpost REGEXP '.*[^0-9].*'";
    $items = $wpdb->get_results($querystr, OBJECT);
    $post_title = get_the_title( $post_id );
    $post_url = get_permalink( $post_id );
    foreach($items as $item){

        if($post_title === $item->linkpost){
            $wpdb->update(
                statusdata_table(),
                array( "linkpost" =>  $post_id ), 
                array( "id" => $item->id)
               );
        }
        
    }
    
   
    
}
add_action( 'save_post', 'get_info_post' );