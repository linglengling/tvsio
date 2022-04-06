<?php

function statusdata_create()
{
    global  $wpdb;

    //step1:
    $DB_tb_name = $wpdb->prefix.'statusdata';
    $DB_tb_name1 = $wpdb->prefix.'statustoken';
    $DB_tb_name3 = $wpdb->prefix.'statistics';
    //step2:
    $DB_query = "CREATE TABLE $DB_tb_name(
        id int(110) NOT NULL AUTO_INCREMENT,
        linkpost varchar(255) DEFAULT '',
        post_id int(110) DEFAULT 0,
        spinstatus varchar(255) DEFAULT '',
        
        PRIMARY KEY (id)
        )";
    $DB_query1 = "CREATE TABLE $DB_tb_name1(
        id int(110) NOT NULL AUTO_INCREMENT,
        token varchar(255) DEFAULT '',
        coinstatus varchar(255) DEFAULT '',
        
        PRIMARY KEY (id)
        )";

    $DB_query2 = "INSERT INTO $DB_tb_name1 (`id`, `token`,`coinstatus`) VALUES
    (1, '56df1de9cd_dLYaNFAuROeDTENboFdIiiZ6967018', '200')";

     $DB_query3 = "CREATE TABLE $DB_tb_name3(
        id int(110) NOT NULL AUTO_INCREMENT,
        link_from BIGINT(20) DEFAULT 0,
        link_to BIGINT(20) DEFAULT 0,
        type_from VARCHAR(45) DEFAULT '',
        type_to VARCHAR(45) DEFAULT '',
        title_to varchar(255) DEFAULT '',
        anchor varchar(255) DEFAULT '',
        PRIMARY KEY (id)
        )";
    //step 3:
    require_once(ABSPATH . '/wp-admin/includes/upgrade.php');

    dbDelta($DB_query);
    dbDelta($DB_query1);
    dbDelta($DB_query2);
    dbDelta($DB_query3);
}
//a9c8c0f334_cGaMJoGDiawtgdiKFLNeii8u644525