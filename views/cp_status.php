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
$querystrsub = "SELECT * FROM $table WHERE camp_post_status = 'publish' ";
$cplists = $wpdb->get_results($querystrsub, OBJECT);
// wp_unschedule_hook('sendmail_cron'); ////////////////Chú ý mỏ cái này để reset thời gian mới cho cronjob. sau đó load trang này rồi khóa lại

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

// var_dump($cplists);


?>

 <!-- Html bảng thống kê  -->
<h2>Bảng trạng thái chiến dịch </h2>


<table id="example" class="wp-list-table example" width="100%" >

<thead>
<tr>
        <th>Tên blog: <?php echo get_option('Blog_name'); ?></th>
        <th> <select id="sel_cur" name="sel" class="cpsone-select">
           
           <option value="no" selected >chọn domain</option>
          
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
        <?php $temp = lastpost($item->camp_id,get_option('Blog_name'));  
       
        ?>
        <td style="<?php if(substr($temp,-4)=="stop"){echo 'background-color:#A4A4A4;';}?>"><?php echo substr($temp,0, strlen($temp)-4); ?></td>
        <td><?php echo crawlpost($item->camp_id,get_option('Blog_name')); ?></td>
        <td><?php echo geteachtime($item->camp_id,get_option('Blog_name')); ?></td>
        <td><a target="_blank" href="<?php echo 'http://'. get_option('Blog_name'); ?>/wp-admin/post.php?post=<?php echo ($item->camp_id);?>&action=edit"><span class="dashicons dashicons-edit"></span></a></td>         
    </tr>
  

    <?php  endforeach;?>
    <!-- xem tat ca -->
    <?php if( get_option('Blog_name')=="mặc định"){foreach ($data_array as $record){
    ?>
        <tr>
       
       <td><?php echo $record[0]; ?></td>
       <td><?php echo $record[1]; ?></td>
       <td><?php echo $record[2]; ?></td>
       <td><?php echo $record[3]; ?></td>
       <td><?php echo $record[4]; ?></td>
       <td><?php echo $record[5]; ?></td>
       <td>chọn domain để sửa </td>
      </tr>

     <?php
          }}
     ?>
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