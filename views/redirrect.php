<h1> Chức năng điều hướng tự động</h1>
<!-- Bootstrap CDN -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
 <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
 <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
 <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
 
<form action='javascript:void(0)' method="post" id='listDM'>
        <div class="form-group">
            <label class="control-label col-sm-12" >Nhập danh sách tên miền tại đây:(lưu ý các tên miền cách nhau bởi một dấu phẩy)</label>
            <div class="col-sm-12">
            <textarea class="form-control" name="list" rows="5"  placeholder="Domain1.com, domain2.com,...">
            </textarea>
            </div>
        </div>


        <br>

        <div class="col-sm-offset-6 col-sm-6">
            <button type="submit" class="btn btn-primary" name="run">Lưu danh sách tên miền</button>

        </div>
    </form>
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

<?php

    global  $wpdb;
    $querystr  = "SELECT * FROM wp_pbn_redirect_statistic";
    $datas = $wpdb->get_results($querystr, OBJECT);
?>
<br>
<br>
<h2>Bảng tên miền đã được thêm</h2>
<br>
<table id="myTable" >
<thead>
    <tr>
        <th>CAMPAIGNS NAME</th>
        <th>CAMPAIGNS URL</th>
        <th>CATEGORY</th>
        <th>NEW POST STATUS</th>
        <th>VIEW/EDIT</th>
        <th>Delete</th>
    </tr>
</thead>
<tbody>
    <?php foreach($datas as $item): ?>
    <tr>
        <td><?php echo $item->namecp;?></td>
        <td><?php echo $item->	urlcatagory;?></td>
        <td><?php echo  get_cat_name($item->category);?></td>
        <td><?php echo $item->publishstatus;?></td>
        <td>
            <a href="admin.php?page=AI-menu&viewid=<?php echo $item->id; ?>&viewname=<?php echo $item->namecp; ?>" class="btn btn-primary">View</a>
            <a href="admin.php?page=AI-menu&autoid=<?php echo $item->id; ?>&viewname=<?php echo $item->namecp; ?>" class="btn btn-info">Auto run</a>
            <a href="admin.php?page=AI-menu&crawlid=<?php echo $item->id; ?>&viewname=<?php echo $item->namecp; ?>" class="btn btn-warning">Crawler all</a>
            <a href="admin.php?page=AI-menu&Editid=<?php echo $item->id; ?>&viewname=<?php echo $item->namecp; ?>" class="btn btn-success">Edit</a>
        </td>
        <td><a href="javascript:void(0)" data-id="<?php echo $item->id; ?>" class="btn btn-danger delcp">Del</a></td>
    </tr>
    <?php endforeach;?>
    </tbody>
</table>