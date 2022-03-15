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
 
    wp_enqueue_style('boot_css', plugins_url('custom.css',__FILE__ ));
   
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

// Function to count the words
function get_num_of_words($string) {
    $string = preg_replace('/\s+/', ' ', trim($string));
    $words = explode(" ", $string);
    return count($words);
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
                        "linkpost" => $content ['post_title']  ,
                        "spinstatus" => $content ['code'] 
                    ));
                    $content = $content["message"];
                }else{
                    $wpdb->insert(statusdata_table(), array(
                        "linkpost" => $output ['post_title']  ,
                        "spinstatus" => $content ['code'] 
                    ));
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
                        $second_part = $second_part["message"];
                        $content =   $first_part . $second_part ;
                    }else{
                        $wpdb->insert(statusdata_table(), array(
                            "linkpost" => $output ['post_title']  ,
                            "spinstatus" => "103"
                        ));
                        $content =   $first_part . $tampart2 ;
                    }
                   

                  
                }else{
                    $wpdb->insert(statusdata_table(), array(
                        "linkpost" => $output ['post_title']  ,
                        "spinstatus" => $first_part ['code'] 
                    ));
                    $content = $tam;
                }
               


                $content =   $first_part . $second_part ;

            
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

