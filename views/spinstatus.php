<h2>
    Thông tin trạng thái spin
</h2>


<?php

    global  $wpdb;
    $table = $wpdb->prefix.'statusdata';
    $querystr  = "
    SELECT *  
    FROM $table
    
 ";

$items = $wpdb->get_results($querystr, OBJECT);
?>
<br>
<style>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.11.5/af-2.3.7/datatables.min.css"/>
</style>
<table id="dtBasicExample" class="display" cellspacing="0" width="100%" >
<thead>
    <tr>
        <th>Tiêu đề bài viết</th>
        <th>mã trạng thái</th>
        <th>mô tả trạng thái</th>
        
    </tr>
</thead>
<tbody>
    <?php foreach($items as $item): ?>
    <tr>
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

<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.11.5/af-2.3.7/datatables.min.js"></script>

<script>
    $('#dtBasicExample').DataTable();
  $('.dataTables_length').addClass('bs-select');
</script>
<!-- 100 Tài khoản không đủ xu hoặc hết hạn sử dụng 
101 Nội dung lớn hơn 2000 chữ 
102 Mã token chưa đúng 
103 spin phần hai không thành công
200 Spin thành công 
500 Mã token chưa đúng -->
