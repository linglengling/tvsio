<h2>
    Thông tin trạng thái spin
</h2>
<!-- Bootstrap CDN -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<?php
//lấy tổng số để thống kê
global  $wpdb;
$table = $wpdb->prefix.'statusdata';
$querystr  = "SELECT *  FROM $table";
$totals = $wpdb->get_results($querystr, OBJECT);

$all = count($totals);
$ok = 0; 
$er = 0;
foreach ($totals as $total){
  if ($total->spinstatus == 200){
    $ok ++;
  }
  if ($total->spinstatus != 200){
    $er ++;
  }
}

?>
<!-- Menu -->
<p><a href="options-general.php?page=spin-options" id="all" >Tất cả(<?php echo $all; ?>)</a> 
| <a href="options-general.php?page=spin-options&filter=hoanthanh" id="ok" >spin thành công(<?php echo $ok; ?>)</a>
| <a href="options-general.php?page=spin-options&filter=loi" id="er"  >spin lỗi(<?php echo $er; ?>)</a>
</p>
<!-- Lấy dữ liệu theo filter -->
<?php
error_reporting(E_ERROR | E_PARSE);

if($_GET['filter'] == "hoanthanh"):
  global  $wpdb;
  $table = $wpdb->prefix.'statusdata';
  $querystr  = "SELECT *  FROM $table WHERE spinstatus = 200";
  $items = $wpdb->get_results($querystr, OBJECT);
  $link = "options-general.php?page=spin-options&filter=hoanthanh";
?>
<style>
p #ok {
  color: red;
}
</style>
<?php

elseif($_GET['filter'] == "loi"):
  global  $wpdb;
  $table = $wpdb->prefix.'statusdata';
  $querystr  = "SELECT *  FROM $table WHERE spinstatus != 200";
  $items = $wpdb->get_results($querystr, OBJECT);
  $link = "options-general.php?page=spin-options&filter=loi";
?>
<style>
p #er {
  color: red;
}
</style>
<?php

else:
  global  $wpdb;
  $table = $wpdb->prefix.'statusdata';
  $querystr  = "SELECT *  FROM $table";
  $items = $wpdb->get_results($querystr, OBJECT);
  $link = "options-general.php?page=spin-options";
?>
<style>
p #all {
  color: red;
}
</style>

<?php endif;?>

<br>
<!-- Chức năng phân trang bằng PHP -->
<?php

$page = isset ( $_REQUEST ['trang'] ) ? $_REQUEST ['trang'] : 1;

$limit = 10;

$numRows = count($items);

$total_page = ceil($numRows / $limit);

$start = ($page - 1) * $limit;

$data = getdataWithLimit($start,$limit);

$items = $data;

function getdataWithLimit($start,$limit){
  if($_GET['filter'] == "hoanthanh"){
    global  $wpdb;
    $table = $wpdb->prefix.'statusdata';
    $querystr  = "SELECT *  FROM $table WHERE spinstatus = 200 LIMIT $start, $limit";
    $data = $wpdb->get_results($querystr, OBJECT);
  }elseif($_GET['filter'] == "loi"){
    global  $wpdb;
    $table = $wpdb->prefix.'statusdata';
    $querystr  = "SELECT *  FROM $table WHERE spinstatus != 200 LIMIT $start, $limit";
    $data = $wpdb->get_results($querystr, OBJECT);
  }else{
    global  $wpdb;
    $table = $wpdb->prefix.'statusdata';
    $querystr  = "SELECT *  FROM $table LIMIT $start, $limit";
    $data = $wpdb->get_results($querystr, OBJECT);
  }
  return $data;
}
?>

 
<!-- Html bảng trạng thái spin -->

<table id="customers" width="100%" >
<thead>
    <tr>
        <th>Tiêu đề bài viết</th>
        <th>Id bài viết</th>
        <th>mã trạng thái</th>
        <th>mô tả trạng thái</th>
        <?php if($_GET['filter'] == "loi"): ?><th>spin lại</th><?php endif; ?>
    </tr>
</thead>
<tbody>
    <?php foreach($items as $item): ?>
    <tr>
        <td><?php echo get_the_title($item->post_id);?></td>
        <td><?php echo $item->post_id;?></td>
        <td><?php echo $item->spinstatus;?></td>
        <td><?php 
        switch ($item->spinstatus) {
            case 100:
                echo "Tài khoản không đủ xu hoặc hết hạn sử dụng ";
              break;
            case 101:
                echo "Nội dung lớn hơn 2000 chữ ";
              break;
            case 102:
                echo "Mã token chưa đúng ";
              break;
            case 103:
                echo "spin phần 1 thành công phần 2 không thành công ";
              break;
            case 200:
                echo "Spin thành công ";
                break;
            case 500:
                echo "Mã token chưa đúng ";
            break;
            
            default:
            echo "Spin thành công ";
          }
        
        ?></td>
          <?php if($_GET['filter'] == "loi"): ?>
         <td><a href="javascript:void(0)" data-id="<?php echo  $item->post_id; ?>" class="btn btn-default spinpost">chạy spin</a></td>
         <?php endif; ?>
    </tr>

    <?php endforeach;?>
   
    </tbody>
    
</table>
<!-- Html link phân trang -->
<table>  
    <tr>
   
   <ul class="pagination">
   <li><a><?php echo $total_page ;?> trang</a></li>

   <li><a href="<?php echo "$link&trang=1" ;?>"><<</a></li>
   <li class="<?php if($page <= 1){ echo 'disabled'; } ?>">
       <a href="<?php if($page <= 1){ echo '#'; } else { echo "$link&trang=".($page - 1); } ?>"><</a>
   </li>

   <li><a><?php echo $page ;?> trên <?php echo $total_page ;?> </a></li>

   <li class="<?php if($page >= $total_page){ echo 'disabled'; } ?>">
       <a href="<?php if($page >= $total_page){ echo '#'; } else { echo "$link&trang=".($page + 1); } ?>">></a>
   </li>
   <li><a href="<?php echo "$link&trang=$total_page ";?>">>></a></li>

  </ul>
  </tr>
  
  </table>


