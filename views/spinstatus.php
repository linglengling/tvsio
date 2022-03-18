<h2>
    Thông tin trạng thái spin
</h2>

<?php
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

<p><a href="options-general.php?page=spin-options" id="all" >Tất cả(<?php echo $all; ?>)</a> 
| <a href="options-general.php?page=spin-options&filter=hoanthanh" id="ok" >spin thành công(<?php echo $ok; ?>)</a>
| <a href="options-general.php?page=spin-options&filter=loi" id="er"  >spin lỗi(<?php echo $er; ?>)</a>
</p>

<?php
error_reporting(E_ERROR | E_PARSE);

if($_GET['filter'] == "hoanthanh"):
  global  $wpdb;
  $table = $wpdb->prefix.'statusdata';
  $querystr  = "SELECT *  FROM $table WHERE spinstatus = 200";
  $items = $wpdb->get_results($querystr, OBJECT);
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
?>
<style>
p #all {
  color: red;
}
</style>
<?php endif;?>
<br>
<style>
#customers {
  font-family: Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

#customers td, #customers th {
  border: 1px solid #ddd;
  padding: 8px;
}

#customers tr:nth-child(even){background-color: #f2f2f2;}

#customers tr:hover {background-color: #ddd;}

#customers th {
  padding-top: 12px;
  padding-bottom: 12px;
  text-align: left;
  background-color: #555;
  color: white;
}
p a{
  text-decoration: none; 
}
</style>




<table id="customers" width="100%" >
<thead>
    <tr>
        <th>Tiêu đề bài viết</th>
        <th>Id bài viết</th>
        <th>mã trạng thái</th>
        <th>mô tả trạng thái</th>
        
    </tr>
</thead>
<tbody>
    <?php foreach($items as $item): ?>
    <tr>
        <td><?php echo get_the_title($item->linkpost);?></td>
        <td><?php echo $item->linkpost;?></td>
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
                echo "spin phần sau 2000 chữ không thành công ";
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
        
    </tr>
    <?php endforeach;?>
    </tbody>
</table>


<script>
$(document).ready(function(){
  $("a").click(function(){
  $("#menu").toggleClass("active");
  });
});
</script>


