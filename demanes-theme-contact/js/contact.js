jQuery(document).ready(function(){
jQuery("#formSubmit").click(function(e){
	e.preventDefault();
	var name = jQuery("#message_name").val();
    var email = jQuery("#message_email").val();
    var comments = jQuery("#message_text").val();
    var product = jQuery("#productTitle").val();
    //var nonce = jQuery("contact_form_widget_nounce").val();
jQuery.ajax({
type: 'POST',
url: MyAjax.ajaxurl,
//url: '/wp-admin/admin-ajax.php',
data: {"action": "post_word_count", "name":name, "email":email, "comments":comments, "productTitle":product},
success: function(response){
    var status  = $(response).find('response_data').text();
    if(status == 'success'){
$('#formResponseText').html('Your message has been sent');
document.getElementById("demanesContactForm").reset();
}else{
    $('#formResponseText').html('Please fill out the form');
}
},
error: function(response) {
	$('#formResponseText').html('Please fill out the form');
 }
});
});



});