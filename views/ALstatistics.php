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

?>
<!-- Chức năng phân trang bằng PHP -->
<?php

$page = isset ( $_REQUEST ['trang'] ) ? $_REQUEST ['trang'] : 1;

$limit = 10;

$numRows = count($idlists);

$total_page = ceil($numRows / $limit);

$start = ($page - 1) * $limit;

// echo $numRows;

$data = getdataWithLimit($start, $idlists);

function getdataWithLimit($start, $idlists){
   
        $mot = array();
        $n = $start + 9;
        if($n<$numRows){
            $n = $numRows;
        }
        if($start !== 0){
            $start = $start - 1;
        }
        
        $j = 0;
        for($i= $start; $i<$n; $i++){
            
            $mot[$j] = $idlists[$i];
            $j++;
        }
        // var_dump($mot);
  return $mot;
}
?>

<!-- Bootstrap CDN -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
 <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<!-- Html bảng thống kê  -->
<h2>Bảng thống kê đi link nội bộ</h2>
<table id="customers" width="100%" >
<thead>
    <tr>
        <th>Tiêu đề bài viết</th>
        <th>incoming link</th>
        <th>outgoing link</th>
        <th>Action</th>
       
    </tr>
</thead>
<tbody>
    <?php foreach($data as $item): ?>
    <tr>
        <td><?php echo get_the_title($item);?></td>
        <td><?php echo getIncomingLinksCount($item);?></td>
        <td><?php echo getOutgoingLinksCount($item);?></td>
        <td><a href="post.php?post=<?php echo ($item);?>&action=edit"><span class="dashicons dashicons-edit"></span></a></td>
         
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