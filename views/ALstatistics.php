<?php
error_reporting(E_ERROR | E_PARSE);

    // lấy mảng id các bài có link nội bộ
    global $wpdb;
    define('AL_PLUGIN_URL', plugin_dir_url( __FILE__ ));
    $table = $wpdb->prefix.'statistics';

    $querystr1  = "SELECT * FROM
    (SELECT * FROM (SELECT DISTINCT `link_to` AS `ID` FROM $table UNION SELECT DISTINCT `link_from` AS `ID` FROM $table) AS A 
    LEFT JOIN (SELECT `link_to` , COUNT(link_to) AS income FROM $table GROUP BY `link_to` ) AS B ON A.ID = B.link_to) AS D
    INNER JOIN
    (SELECT * FROM (SELECT DISTINCT `link_to` AS `ID` FROM $table UNION SELECT DISTINCT `link_from` AS `ID` FROM $table) AS A
    LEFT JOIN (SELECT `link_from` , COUNT(link_from) AS outcome FROM $table GROUP BY `link_from` ) AS C ON A.ID = C.link_from) AS E 
    ON D.ID = E.ID LIMIT 3000 ";

    $idlists = $wpdb->get_results($querystr1, OBJECT);
  
    $link = "options-general.php?page=AL-options";
   
        
    $tableCT = $wpdb->prefix.'terms';
    $tableRL= $wpdb->prefix.'term_taxonomy';
    $categories = $wpdb->get_results("SELECT * FROM $tableCT INNER JOIN $tableRL on $tableCT.term_id = $tableRL.term_id WHERE $tableRL.taxonomy = 'category' ");
    $CT = get_option('CT_option');
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


<table id="example" class="wp-list-table example" width="100%" >
<thead>
    <tr>
        <th>Chuyên mục đang lọc: <?php echo get_option('CT_option'); ?></th>
        <th> <select id="sel_cur" name="sel" class="category-select">
           
           <option value="no" selected >chưa chọn</option>
          
           <?php   foreach($categories as $category): ?>
           <option value="<?php echo $category->name;?>"><?php echo $category->name; ?></option>
           <?php endforeach; ?>
           <?php if($CT!= "category"):?>
            <option value="category"  >Bỏ lọc</option>
           <?php endif; ?>
       </select></th>
        
  </tr>
     <tr>   
        <th>Tiêu đề bài viết</th>
        <th>category</th>
        <th>incoming link</th>
        <th>outgoing link</th>
        <th>Action</th>
       
    </tr>
</thead>
<tbody>
    <?php foreach($idlists as $item): if ("category"== $CT):?>
        <tr>
        <td><?php echo get_the_title($item->ID);?></td>
        <td><?php echo get_cat_name(wp_get_post_categories($item->ID)[0]);?></td>
        <td><a id="show<?php echo $item->ID;?>" href="<?php echo $link."&outgoingView=".$item->ID?>"   onClick="showDiv(2);"><?php echo ($item->income == NULL)?0:$item->income;?></a></td>
        <td><a id="show<?php echo $item->ID;?>" href="<?php echo $link."&incoming_View=".$item->ID?>"   onClick="showDiv(1);"><?php echo ($item->outcome == NULL)?0:$item->outcome;?></a></td>
       
      
        <td><a href="post.php?post=<?php echo ($item->ID);?>&action=edit"><span class="dashicons dashicons-edit"></span></a></td>
         
    </tr>
    <?php else : if(get_cat_name(wp_get_post_categories($item->ID)[0]) == $CT): ?>
    <tr>
        <td><?php echo get_the_title($item->ID);?></td>
        <td><?php echo get_cat_name(wp_get_post_categories($item->ID)[0]);?></td>
        <td><a id="show<?php echo $item->ID;?>" href="<?php echo $link."&outgoingView=".$item->ID?>"   onClick="showDiv(2);"><?php echo ($item->income == NULL)?0:$item->income;?></a></td>
        <td><a id="show<?php echo $item->ID;?>" href="<?php echo $link."&incoming_View=".$item->ID?>"   onClick="showDiv(1);"><?php echo ($item->outcome == NULL)?0:$item->outcome;?></a></td>
       
      
        <td><a href="post.php?post=<?php echo ($item->ID);?>&action=edit"><span class="dashicons dashicons-edit"></span></a></td>
         
    </tr>

    <?php endif; endif; endforeach;?>
   
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
    width:60%;
    height:100%;
    display:block;
    position:fixed;
    top:30%;
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
    width:60%;
    height:100%;
    display:block;
    position:fixed;
    top:30%;
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
    <?php foreach($data1 as $item):?>

    <tr>
        <td><a href="<?php echo get_permalink($item->link_to);?>"><?php echo get_the_title($item->link_to);?></a></td>
        <td><?php echo get_cat_name(wp_get_post_categories($item->link_to)[0]);?></td>  
    </tr>

    <?php endforeach;?>
   
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
    <td><a href="<?php echo get_permalink($item->link_from);?>"><?php echo get_the_title($item->link_from);?></a></td>
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
            var rowid = jQuery(this).attr("data-id");
            var postdata = "action=changeidswitch&id="+ rowid ;
            jQuery.post("<?php echo admin_url('admin-ajax.php');?>", postdata, function(response) {
            
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

    jQuery("#sel_cur").on("change", OnSelectionChange);
    function OnSelectionChange() {
        console.log("chạy changeCT");
    var select = document.getElementById('sel_cur');
    var CT = select.options[select.selectedIndex].value;
     var postdata = "action=changeCT&CT="+ CT ;
     jQuery.post("<?php echo admin_url('admin-ajax.php');?>", postdata, function(response) {
        console.log(response);
        setTimeout(function() {
                  location.reload();
              }, 1300)

     });
}
    
  </script>