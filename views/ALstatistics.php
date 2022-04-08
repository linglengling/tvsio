<?php
error_reporting(E_ERROR | E_PARSE);

    // lấy mảng id các bài có link nội bộ
    global $wpdb;
    $table = $wpdb->prefix.'statistics';
    $querystr1  = "SELECT * FROM $table ";
    $idlists = $wpdb->get_results($querystr1, OBJECT);
    $idlist1s = array();
    $idlist2s = array();
    foreach($idlists as $idlist){
      
        array_push($idlist1s, $idlist->link_from);
        
       
        array_push($idlist2s, $idlist->link_to);
    }

    //Trộn hai mảng id outcome và income 
    $idlists = array_merge($idlist1s, $idlist2s);

    //lọc trùng
    $result = array();
    foreach ($idlists as $key => $value){
        if(!in_array($value, $result))
          $result[$key]=$value;
      }

    // đánh số index lại cho mảng lọc trùng
    $idlist3s = array();
    $i = 0;
    foreach ($result as $re){
       $idlist3s[$i] = $re;
       $i++;
      }

    //Trả kết quả 
   $idlists = $idlist3s;
  
   $link = "options-general.php?page=AL-options";
?>


<!-- Bootstrap CDN -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
 <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
 <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
 <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<!-- Html bảng thống kê  -->
<h2>Bảng thống kê đi link nội bộ</h2>

<?php


 if ( FALSE == get_option('guesslink_enabled') ) {	// hiển thị trạng thái nút tùy chọn đi link tự động
    $idswitch = "";
}else{
    $idswitch = "checked";
}
?>
<a>Nếu bạn muốn tự động đi link guess post cho các bài viết cũ ấn vào nút bên cạnh để bật hoặc tắt</a>
<input id="toggle-event" type="checkbox" <?php echo $idswitch; ?> data-toggle="toggle" data-on="Mở" data-off="Tắt">
<a><b style="color: balck; ">Số bài cũ đã được đi link guess post :</b><b style="color: red; ">(<?php echo get_option('guesslink_total'); ?> bài)</b></a>

<table id="example" class="wp-list-table example" width="100%" >
<thead>
    <tr>
        <th>Tiêu đề bài viết</th>
        <th>category</th>
        <th>incoming link</th>
        <th>outgoing link</th>
        <th>Action</th>
       
    </tr>
</thead>
<tbody>
    <?php foreach($idlists as $item): if( $item != 0):?>

    <tr>
        <td><?php echo get_the_title($item);?></td>
        <td><?php echo get_cat_name(wp_get_post_categories($item)[0]);?></td>
        <td><a id="show<?php echo $item;?>" href="<?php echo $link."&incoming_View=".$item?>"  data-id="<?php echo  $item; ?>" onClick="showDiv(1);"><?php echo getIncomingLinksCount($item);?></a></td>
        <td><a id="show<?php echo $item;?>" href="<?php echo $link."&outgoingView=".$item?>"  data-id="<?php echo  $item; ?>" onClick="showDiv(2);"><?php echo getOutgoingLinksCount($item);?></a></td>
      
        <td><a href="post.php?post=<?php echo ($item);?>&action=edit"><span class="dashicons dashicons-edit"></span></a></td>
         
    </tr>

    <?php endif; endforeach;?>
   
    </tbody>
    
</table>
<!--///////////////////////////////////////////////////////-->
<style>
#lbltipAddedComment{
    color: black;
}
.red{

    color: red !important;
    font-size: 24px;
}

</style>

<!--///////////////////////////////////////////////////////-->
<?php if($_GET['incoming_View']):?>
    <?php

    global $wpdb;
    $table = $wpdb->prefix.'statistics';
    $check = (int)$_GET['incoming_View'];
    $querystr1  = "SELECT * FROM $table WHERE link_from = $check ";
    $data1 = $wpdb->get_results($querystr1, OBJECT);
?>
    <style>
#incoming_View{
    background-color:#fff;
    width:1200px;
    height:900px;
    display:block;
    position:fixed;
    top:20%;
    left:30%;
    margin:-150px 0 0 -150px;
}

    </style> 
<?php  else:?>
    <style>
#incoming_View{
   
    display:none;
   
}

    </style> 
<?php  endif;?>

<!--///////////////////////////////////////////////////////-->
<?php if($_GET['outgoingView']):?>
<?php

    global $wpdb;
    $table = $wpdb->prefix.'statistics';
    $check = (int)$_GET['outgoingView'];
    $querystr1  = "SELECT * FROM $table WHERE link_to = $check ";
    $data2 = $wpdb->get_results($querystr1, OBJECT);
?>
    <style>
#outgoingView{
    background-color:#fff;
    width:1200px;
    height:900px;
    display:block;
    position:fixed;
    top:20%;
    left:30%;
    margin:-150px 0 0 -150px;
}
        
    </style> 

<?php  else:?>
    <style>
#outgoingView{
   
    display:none;
   
}

    </style> 
<?php  endif;?>

<!--///////////////////////////////////////////////////////-->
<div id="incoming_View">
<label id="lbltipAddedComment">Bảng các link incoming</label><a  style="float:right; " href="<?php echo $link;?>" onClick="showDiv(1);">Close viewbox</a>
    <table id="example" class="wp-list-table example" >
    <thead>
    <tr>
        <th>Tiêu đề bài viết</th>
        <th>category</th>
        
       
    </tr>
    </thead>
    <tbody>
    <?php foreach($data1 as $item): if( $item < 10):?>

    <tr>
        <td><?php echo get_the_title($item->link_to);?></td>
        <td><?php echo get_cat_name(wp_get_post_categories($item->link_to)[0]);?></td>  
    </tr>

    <?php endif; endforeach;?>
   
    </tbody>
    </table>
    
</div>
<!--///////////////////////////////////////////////////////-->


<div id="outgoingView">
<label id="lbltipAddedComment">Bảng các link outgoing </label><a style="float:right; " href="<?php echo $link;?>" onClick="showDiv(2);">Close viewbox</a>
    <table id="example" class="wp-list-table example" >
    <thead>
    <tr>
        <th>Tiêu đề bài viết</th>
        <th>category</th>
        
       
    </tr>
    </thead>
    <tbody>
    <?php foreach($data2 as $item): ?>

    <tr>
        <td><?php echo get_the_title($item->link_from);?></td>
        <td><?php echo get_cat_name(wp_get_post_categories($item->link_from)[0]);?></td>  
    </tr>

    <?php  endforeach;?>
   
    </tbody>
    </table> 
    
</div>
<!--///////////////////////////////////////////////////////-->
  <script>
       jQuery(document).ready(function() {
      jQuery(function() {
        jQuery('#toggle-event').change(function() {
            var postdata = "action=changeidswitch"
            jQuery.post("../../../wp-admin/admin-ajax.php", postdata, function(response) {
            
                console.log(response);
                // setTimeout(function() {
                //     location.reload();
                // }, 300);
             });
        })
    })
   
    jQuery('#example').DataTable( {
        "order": [[ 3, "desc" ]]
        } );
    } );
    function showDiv(pageid)
    {
        jQuery("#show"+jQuery(this).attr("data-id")).toggleClass("red");
        if (pageid ==1){
            jQuery("#incoming_View").slideToggle(1000, function() {
            
            });
        }else{
            jQuery("#outgoingView").slideToggle(1000, function() {
            
            });
        }
        
    }
    
  </script>