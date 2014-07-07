<script>
//admin delete function
function contact_form_delete_row($id){
    	
	jQuery.ajax({
type: 'POST',
url: '/wp-content/plugins/demanes-theme-contact/demanes-theme-contact-admin-delete.php',
//url: '/wp-admin/admin-ajax.php',
data: {"row_id": $id},
success: function(response){
    jQuery('#rowid-' + $id).hide();
}


});
	
}
</script>
<?php
echo '<h1>Contact Form Submissions</h1>';


	$path = $_SERVER['DOCUMENT_ROOT'];
	include_once $path . '/wp-load.php';
	global $wpdb;
	$tablename = $wpdb->base_prefix . 'demanes_contact_form';
	$res = $wpdb->get_results("SELECT * FROM $tablename ", ARRAY_A);
	

	echo '<br/><br/>';
	echo '<table>';
	echo '<tr>';
	echo '<td>';
	echo 'Date';
	echo '</td>';
	echo '<td>';
	echo 'Name';
	echo '</td>';
	echo '<td>';
	echo 'Email';
	echo '</td>';
	echo '<td>';
	echo 'Comments';
	echo '</td>';
	echo '<td>';
	echo 'Product';
	echo '</td>';
	echo '<td>';
	echo 'Action';
	echo '</td>';
	foreach ($res as $key => $value) {
		echo '<tr id= "rowid-'. $value['id']. '">';
		echo '<td>';
		$datestamp = strtotime($value['created']);
		$datevalue = date('M d Y g:i A', $datestamp);
		echo $datevalue;
		echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		echo '</td>';
		echo '<td>';
		echo $value['name'];
		echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		echo '</td>';
		echo '<td>';
		echo $value['email'];
		echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		echo '</td>';
		echo '<td>';
		echo $value['comments'];
		echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		echo '</td>';
		echo '<td>';
		echo $value['product_title'];
		echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		echo '</td>';
		echo '<td>';
		echo '<a href="javascript:void(0);" onclick="contact_form_delete_row('.$value['id'].');">Delete</a>';
		echo '</td>';
	}
	echo '</table>';

?>