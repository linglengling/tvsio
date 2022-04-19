<!-- Bootstrap CDN -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
 <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
 <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>

 
 <?php
error_reporting(E_ERROR | E_PARSE);
global $wpdb;

$querystr = "SELECT * FROM wp_blogs";
$cpses = $wpdb->get_results($querystr, OBJECT);
$CPS =  get_option('Blog_name');
$queryCPS = "SELECT * FROM `wp_blogs` WHERE domain ='$CPS'";
$theprefix =  $wpdb->get_results($queryCPS, OBJECT);
$theprefix = "wp_".(($theprefix[0]->blog_id==1)?"":$theprefix[0]->blog_id."_");
$table = $theprefix.'automatic_camps';
$querystrsub = "SELECT * FROM $table ";
$cplists = $wpdb->get_results($querystrsub, OBJECT);
// var_dump($cplists);
?>

 <!-- Html bảng thống kê  -->
<h2>Bảng trạng thái chiến dịch </h2>


<table id="example" class="wp-list-table example" width="100%" >
<thead>
<tr>
        <th>Tên blog: <?php echo get_option('Blog_name'); ?></th>
        <th> <select id="sel_cur" name="sel" class="cpsone-select">
           
           <option value="no" selected >chưa chọn</option>
          
           <?php   foreach($cpses as $cpsone): ?>
           <option value="<?php echo $cpsone->domain;?>"><?php echo $cpsone->domain; ?></option>
           <?php endforeach; ?>
           <?php if($CPS!= "mặc định"):?>
            <option value= "mặc định" >Bỏ lọc</option>
           <?php endif; ?>
       </select></th>
       <th>Chiến dịch không còn hoạt động quá hai ngày, ngày post cuối cùng sẽ có nền xám</th>
        
  </tr>
     <tr>   
        <th>Tên trang con</th>
        <th>Tên chiến dịch</th>
        <th>Nội dung </th>
        <th>Ngày post cuối cùng</th>
        <th>Số bài đã cào</th>
        <th>Tần suất cào (phút)</th>
        <th>Chức năng</th>
       
    </tr>
</thead>
<tbody>
    <?php foreach($cplists as $item): ?>
    <tr>
       
        <td><?php echo get_option('Blog_name'); ?></td>
        <td><?php echo $item->camp_name	; ?></td>
        <td><?php echo $item->camp_post_content	; ?></td>
        <?php $temp = lastpost($item->camp_id);  
       
        ?>
        <td style="<?php if(substr($temp,-4)=="mark"){echo 'background-color:#A4A4A4;';}?>"><?php echo substr($temp,0, strlen($temp)-4); ?></td>
        <td><?php echo crawlpost($item->camp_id); ?></td>
        <td><?php echo geteachtime($item->camp_id); ?></td>
        <td><a href="<?php echo 'http://'. get_option('Blog_name'); ?>/wp-admin/post.php?post=<?php echo ($item->camp_id);?>&action=edit"><span class="dashicons dashicons-edit"></span></a></td>         
    </tr>
   

    <?php  endforeach;?>
   
    </tbody>
    
</table>

<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
 <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<!--///////////////////////////////////////////////////////-->
<script>
jQuery(document).ready(function() {
     
   
    jQuery('#example').DataTable( {
        "order": [[ 3, "desc" ]]
        } );
    

    jQuery("#sel_cur").on("change", OnSelectionChange);
    function OnSelectionChange() {
        console.log("chạy changeCPS");
    var select = document.getElementById('sel_cur');
    var CPS = select.options[select.selectedIndex].value;
     var postdata = "action=changeCPS&CPS="+ CPS ;
     jQuery.post("<?php echo admin_url('admin-ajax.php');?>", postdata, function(response) {
        console.log(response);
        setTimeout(function() {
                  location.reload();
              }, 1300)

     });
    }
})
  </script>