<?php

$id = $_POST['row_id'];

$path = $_SERVER['DOCUMENT_ROOT'];
include_once $path . '/wp-load.php';
global $wpdb;
$tablename = $wpdb->base_prefix . 'demanes_contact_form';


	
//delete from database
if($id > 0){
 //query
 $wpdb->delete( $tablename, array( 'id' => $id ) );
}
echo 'worked';
?>