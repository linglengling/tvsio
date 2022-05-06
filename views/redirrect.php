<h1> Chức năng điều hướng tự động</h1>
<!-- Menu -->
<p><a href="options-general.php?page=RD-options" id="SEO" >Miền SEO</a> 
| <a href="options-general.php?page=RD-options&filter=PBN" id="PBN" >Miền PBN</a>
| <a href="options-general.php?page=RD-options&filter=CATE" id="CATE"  >Bảng Category</a>
<!-- | <a href="options-general.php?page=RD-options&filter=SEOCATE" id="SEOCATE"  >Thêm Category SEO</a> -->
<!-- | <a href="options-general.php?page=RD-options&filter=PBNCATE" id="PBNCATE"  >Thêm Category PBN</a> -->
</p>
<style>
table {
    font-family: arial, sans-serif;
    border-collapse: collapse;
    width: 100%;
}

td,
th {
    border: 1px solid #dddddd;
    text-align: left;
    padding: 8px;
}

tr:nth-child(even) {
    background-color: #dddddd;
}
</style>
<!-- Bootstrap CDN -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
 <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
 <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
 <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
 <?php
error_reporting(E_ERROR | E_PARSE);
if($_GET['filter']=="PBN"):

?>

<!-- ////////////////////////////////////////////////////////////////////////////////////// -->
<form action='javascript:void(0)' method="post" id='listPBN'>
        <div class="form-group">
            <label class="control-label col-sm-12" >Nhập danh sách tên miền PBN tại đây:(lưu ý các tên miền cách nhau bởi một dấu phẩy)</label>
            <div class="col-sm-12">
            <textarea class="form-control" name="list" rows="5"  placeholder="Domain1.com, domain2.com,...">
            </textarea>
            </div>
        </div>


        <br>

        <div class="col-sm-offset-6 col-sm-6">
            <button type="submit" class="btn btn-primary" name="run">Lưu danh sách PBN</button>

        </div>
    </form>
   

<?php

    global  $wpdb;
    $querystr  = "SELECT * FROM wp_pbn_site";
    $datas = $wpdb->get_results($querystr, OBJECT);
?>
<br>

<br>
<h2>Bảng PBN đã được thêm</h2>
<br>
<table id="example" >
<thead>
    <tr>
        <th>Tên miền</th>
        <th>chuyên mục</th>
        <th>Delete</th>
    </tr>
</thead>
<tbody>
    <?php foreach($datas as $item): ?>
    <tr>
        <td><?php echo $item->sitePBN;?></td>
        
        <td><?php echo $item->category;?><a href="options-general.php?page=RD-options&filter=PBNCATE&id=<?php echo $item->id; ?>" class="btn btn-success">Add</a></td>
        <td><a href="javascript:void(0)" data-id="<?php echo $item->id; ?>" class="btn btn-danger delPBN">Del</a></td>
    </tr>
    <?php endforeach;?>
    </tbody>
</table>
<!-- ///////////////////////////////////////////////////////////////////////////////////// -->
<?php

elseif($_GET['filter']=="CATE"):

?>
<!-- ////////////////////////////////////////////////////////////////////////////////////// -->
<form action='javascript:void(0)' method="post" id='listCATE'>
        <div class="form-group">
            <label class="control-label col-sm-12" >Nhập danh sách Category tại đây:(lưu ý các Category cách nhau bởi một dấu phẩy)</label>
            <div class="col-sm-12">
            <textarea class="form-control" name="list" rows="5"  placeholder="Domain1.com, domain2.com,...">
            </textarea>
            </div>
        </div>


        <br>

        <div class="col-sm-offset-6 col-sm-6">
            <button type="submit" class="btn btn-primary" name="run">Lưu danh sách Category</button>

        </div>
    </form>
   

<?php

    global  $wpdb;
    $querystr  = "SELECT * FROM wp_site_category";
    $datas = $wpdb->get_results($querystr, OBJECT);
?>
<br>

<br>
<h2>Bảng Category đã được thêm</h2>
<br>
<table id="example" >
<thead>
    <tr>
        <th>ID</th>
        <th>chuyên mục</th>
        <th>Delete</th>
    </tr>
</thead>
<tbody>
    <?php foreach($datas as $item): ?>
    <tr>
        <td><?php echo $item->id;?></td>
        
        <td><?php echo $item->category;?></td>
        <td><a href="javascript:void(0)" data-id="<?php echo $item->id; ?>" class="btn btn-danger delCATE">Del</a></td>
    </tr>
    <?php endforeach;?>
    </tbody>
</table>
<!-- ////////////////////////////////////////////////////////////////////////////////////// -->
<?php

elseif($_GET['filter']=="SEOCATE"):
    global  $wpdb;
    $id = $_GET['id'];
    $querystr  = "SELECT * FROM wp_site_category";
    $CATEs = $wpdb->get_results($querystr, OBJECT);
    $querystr1  = "SELECT * FROM wp_pbn_redirect_statistic WHERE id = $id";
    $datas = $wpdb->get_results($querystr1, OBJECT);
?>
<!-- ////////////////////////////////////////////////////////////////////////////////////// -->
<form action='javascript:void(0)' method="post" style="width:30%" id='NHAPSEOCATE'>
        <div class="form-group">
        <label class="control-label col-sm-12" >Chọn chuyên mục cho <?php echo $datas[0]->siteSEO;?>
            </label>
            <div class="col-sm-12">
            <input type="hidden" id="custId" name="custId" value="<?php echo $_GET['id']; ?>">

            <select id="CATEs" name="CATEs" class="control-label col-sm-6">
            <option value=""> chưa chọn</option>
            <?php foreach($CATEs as $CATE): ?>
                <option value="<?php echo $CATE->category;?>"> <?php echo $CATE->category;?></option>
            <?php endforeach;?>
            
           
            
            </select>
            </div>
            <label class="control-label col-sm-12" >category hiện tại(<?php echo $datas[0]->category;?>) 
            </label>
        </div>

<br><br><br>
       

        <div class=" col-sm-6">
            <button type="submit" class="btn left btn-primary" name="run">Lưu chuyên mục cho miền SEO</button>

        </div>
    </form>
<!-- ////////////////////////////////////////////////////////////////////////////////////// -->
<?php

elseif($_GET['filter']=="PBNCATE"):
    global  $wpdb;
    $id = $_GET['id'];
    $querystr  = "SELECT * FROM wp_site_category";
    $CATEs = $wpdb->get_results($querystr, OBJECT);
    $querystr1  = "SELECT * FROM wp_pbn_site WHERE id = $id";
    $datas = $wpdb->get_results($querystr1, OBJECT);
?>
<!-- ////////////////////////////////////////////////////////////////////////////////////// -->
<form action='javascript:void(0)' method="post" style="width:30%" id='NHAPPBNCATE'>
        <div class="form-group">
            <label class="control-label col-sm-12" >Chọn chuyên mục cho <?php echo $datas[0]->sitePBN;?>
            </label>
            <div class="col-sm-12">
            <input type="hidden" id="custId" name="custId" value="<?php echo $_GET['id']; ?>">
            <select id="CATEs" name="CATEs" class="control-label col-sm-6">
            <option value=""> chưa chọn</option>
            <?php foreach($CATEs as $CATE): ?>
                <option value="<?php echo $CATE->category;?>"> <?php echo $CATE->category;?></option>
            <?php endforeach;?>
            
           
            
            </select>
            </div>
            <label class="control-label col-sm-12" >category hiện tại(<?php echo $datas[0]->category;?>) 
            </label>
        </div>


    <br><br><br>

        <div class="col-sm-6">
            <button type="submit" class="btn left btn-primary" name="run">Lưu chuyên mục miền PBN
            </button>

        </div>
    </form>
<!-- ////////////////////////////////////////////////////////////////////////////////////// -->
<?php


else:

?>
<!-- ////////////////////////////////////////////////////////////////////////////////////// -->
 <label class="control-label col-sm-6" for="cars">chọn thời gian điều hướng(time hiện tại:<?php  echo get_option( 'timeRD') ?>milisecond)</label>

<select id="timeRD" class="control-label col-sm-6">

  <option value="5000"> chưa chọn</option>
  <option value="5000"> 5 giây</option>
  <option value="6000"> 6 giây</option>
  <option value="7000"> 7 giây</option>
  <option value="8000"> 8 giây</option>
  <option value="9000"> 9 giây</option>
  <option value="10000"> 10 giây</option>
  
</select>
<form action='javascript:void(0)' method="post" id='listDM'>
        <div class="form-group">
            <label class="control-label col-sm-12" >Nhập danh sách tên miền SEO tại đây:(lưu ý các tên miền cách nhau bởi một dấu phẩy)</label>
            <div class="col-sm-12">
            <textarea class="form-control" name="list" rows="5"  placeholder="Domain1.com, domain2.com,...">
            </textarea>
            </div>
        </div>


        <br>

        <div class="col-sm-offset-6 col-sm-6">
            <button type="submit" class="btn btn-primary" name="run">Lưu danh sách tên miền SEO</button>

        </div>
    </form>
   

<?php

    global  $wpdb;
    $querystr  = "SELECT * FROM wp_pbn_redirect_statistic";
    $datas = $wpdb->get_results($querystr, OBJECT);
?>
<br>

<br>
<h2>Bảng tên miền SEO đã được thêm</h2>
<br>
<table id="example" >
<thead>
    <tr>
        <th>Tên miền</th>
        <th>số lần tải trang</th>
        <th>bật tắt điều hướng</th>
        <th>chuyên mục</th>
        <th>Delete</th>
    </tr>
</thead>
<tbody>
    <?php foreach($datas as $item): ?>
    <tr>
        <td><?php echo $item->siteSEO;?></td>
        <td><?php echo $item->countRD;?></td>
        <td><a href="javascript:void(0)" data-id="<?php echo $item->id; ?>" class="btn battat"><input id="toggle-event" type="checkbox" 
        <?php
            if ($item->onoff == 0 ) {	// hiển thị trạng thái nút tùy chọn 
                $idswitch = "";
            }else{
                $idswitch = "checked";
            }
        
        echo $idswitch;
        ?>
         data-toggle="toggle" data-on="Bật" data-off="Tắt" data-id="<?php echo $item->id; ?>"></a>
        </td>
        <td><?php echo $item->category;?><a href="options-general.php?page=RD-options&filter=SEOCATE&id=<?php echo $item->id; ?>" class="btn btn-success">Add</a></td>
        <td><a href="javascript:void(0)" data-id="<?php echo $item->id; ?>" class="btn btn-danger delcp">Del</a></td>
    </tr>
    <?php endforeach;?>
    </tbody>
</table>
<!-- ////////////////////////////////////////////////////////////////////////////////////// -->
<?php

        endif;

?>
<!--///////////////////////////////////////////////////////-->
<script>
jQuery(document).ready(function() {
    jQuery('#example').DataTable( {
        } );
})
</script>