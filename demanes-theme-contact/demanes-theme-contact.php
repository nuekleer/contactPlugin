<?php
/*
Plugin Name: Demanes Contact Form
Plugin URI:
Description: Creates form widget, sends emails, sets up database storage, and creates admin view. 
Version: 1.0
Author: Web Design 309
Author URI: http://www.webdesign309.com
License: GPLv2
*/
global $wpdb;
function jal_install() {
   global $wpdb;

   $table_name = $wpdb->prefix . "demanes_contact_form";
      
   $sql = "CREATE TABLE $table_name (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) DEFAULT '' NOT NULL,
  email VARCHAR(255) DEFAULT '' NOT NULL,
  created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
  comments text NOT NULL,
  product_title text NULL,
  UNIQUE KEY id (id)
    );";

   require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
   dbDelta( $sql );
 
}

register_activation_hook( __FILE__, 'jal_install' );

class Contact_Us_Widget extends WP_Widget {
    /** constructor */
    function __construct() {
        parent::WP_Widget( /* Base ID */'Contact_Us_Widget', /* Name */'Contact Us ', array( 'description' => 'Display contact form' ) );
    }

    /** @see WP_Widget::widget */
    function widget( $args, $instance ) {
        ?>
        

        <!-- customer counter -->
        
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 contactFormWidget">
            <h3><?php $contactTitle = get_option('demanes_options'); echo $contactTitle['contact_form_title'];?></h3>
            <div id="respond">
                <div id="formResponseText"></div>
                <form method="post" id="demanesContactForm">
                  <p><input type="text" id ="message_name" name="message_name" placeholder="<?php $contactFormName = get_option('demanes_options'); echo $contactFormName['contact_form_name'];?>" value="<?php if (isset($_POST['message_name'])){echo esc_attr($_POST['message_name']);} ?>"></p>
                  <p><input type="text" id = "message_email" name="message_email" placeholder="<?php $contactFormEmail = get_option('demanes_options'); echo $contactFormEmail['contact_form_email'];?>" value="<?php if (isset($_POST['message_email'])){echo esc_attr($_POST['message_email']);} ?>"></p>
                  <p><textarea type="text" id = "message_text" name="message_text" placeholder="<?php $contactFormComments = get_option('demanes_options'); echo $contactFormComments['contact_form_comments'];?>"><?php if (isset($_POST['message_text'])){echo esc_textarea($_POST['message_text']);} ?></textarea></p>
                  <input type="hidden" name="productTitle" id="productTitle" value="none" >
                  <?php wp_nonce_field('contact_form_widget_action','contact_form_widget_nounce'); ?>
                  <p><input type="image" id="formSubmit" src="wp-content/themes/demanes/images/submitform.png" name="image" width="203" height="57"></p>
                </form>
              </div>
        </div>
        
        
        
        <?php
    }

    /** @see WP_Widget::update */
    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        return $instance;
    }

    /** @see WP_Widget::form */
    function form( $instance ) {
        
    }

}

add_action( 'widgets_init', create_function( '', 'register_widget("Contact_Us_Widget");' ) );

function demanes_contact_scripts() {
  wp_enqueue_script('inkthemes', plugins_url( '/js/contact.js' , __FILE__ ) , array( 'jquery' ));
  // including ajax script in the plugin Myajax.ajaxurl
  wp_localize_script( 'inkthemes', 'MyAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php')));
}



add_action( 'wp_enqueue_scripts', 'demanes_contact_scripts' );

function post_word_count(){
$name = sanitize_text_field($_POST['name']);
$email = sanitize_text_field($_POST['email']);
$created = date('Y-m-d H:i:s');
$comments = sanitize_text_field($_POST['comments']);
$product = sanitize_text_field($_POST['productTitle']);
if(strlen($product) > 0){
    //do nothing title is good
}
else{
    $product = 'none';
}
//$nonce = $_POST['nonce'];

$validemail = is_email($email);
$validname = strlen($name);
$validcomments = strlen($comments);
//$validnonce = wp_verify_nonce($nonce);
$response = new WP_Ajax_Response;

if($validemail && $validname > 1 && $validcomments >1){
global $wpdb;
$tableName = $wpdb->base_prefix . "demanes_contact_form";
$wpdb->insert( 
    $tableName, 
    array( 
       'name' => $name,
       'email' => $email,
       'created' => $created,
       'comments' => $comments,
       'product_title' => $product
  ), 
    array( 
        '%s',
        '%s',
        '%s',
        '%s',
        '%s'
    ) 
);

$contactformemail = get_option('demanes_options');
$sendtoemail = $contactformemail['contact_form_to'];
$subject = 'New Contact from Online Form';
$emailbody = 'New message from online visitor';
$emailbody .= '

';
$emailbody .= 'Name:  ' . $name;
$emailbody .= '

';
$emailbody .= 'Email:  ' . $email;
$emailbody .= '

';
$emailbody .= 'Comments:  ' . $comments;
$emailbody .= '

';

if($product != 'none'){
    $emailbody .= 'Product:  ' . $product;
    $emailbody .= '
    
    ';
}


    $response->add(array(
      'data' => 'success', 
      'supplemental' => 'testing ajax worked, thanks'));
      
      
      mail($sendtoemail, $subject, $emailbody);
      
}else{
    $response->add(array(
      'data' => 'error', 
      'supplemental' => 'testing ajax worked, thanks'));
    
}
    $response->send();

    exit();
}
add_action('wp_ajax_post_word_count', 'post_word_count');
add_action('wp_ajax_nopriv_post_word_count', 'post_word_count');


function register_passport_comment_reporting_page(){
  add_menu_page( 'Contact Form', 'Contact Form Submissions', 'manage_options', 'demanes-theme-contact/demanes-theme-contact-admin-display.php', '');
}

add_action('admin_menu', 'register_passport_comment_reporting_page');


function show_contact_form_in_page(){
    $formcode = '
    <div class="col-xs-12 col-sm-5 col-md-5 col-lg-5 contactFormInPage">
    <div id="respond">
    <div id="formResponseText"></div>
    <form method="post" id="demanesContactForm">';
    //get guts in place
    $formcode .='
    <p><input type="text" id ="message_name" name="message_name" placeholder="';
    $contactFormNameCode = get_option('demanes_options');
    $formcode .= $contactFormNameCode['contact_form_name'];
    $formcode .= '" value="';
    $formcode .= '"></p>';
    $formcode .='
    <p><input type="text" id = "message_email" name="message_email" placeholder="';
    $contactFormEmailCode = get_option('demanes_options');
    $formcode .= $contactFormEmailCode['contact_form_email'];
    $formcode .= '" value="';
    $formcode .= '"></p>';
    $formcode .= '<p><textarea type="text" id = "message_text" name="message_text" placeholder="';
    $contactFormCommentsCode = get_option('demanes_options');
    $formcode .= $contactFormCommentsCode['contact_form_comments'];
    $formcode .= '">';
    $formcode .= '</textarea></p>';
    $formcode .= '<input type="hidden" name="productTitle" id="productTitle" value="none" >';
    $formcode .= wp_nonce_field('contact_form_widget_action','contact_form_widget_nounce');
    $formcode .='
    <p><input type="image" id="formSubmit" src="wp-content/themes/demanes/images/submitform.png" name="image" width="203" height="57"></p>
                </form>
              </div>
        </div>
    ';
    return $formcode;
}
add_shortcode('contactForm', 'show_contact_form_in_page');

function show_contact_form_product($atts){
    $a = shortcode_atts( array(
        'product' => 'none',
    ), $atts );
    $formcode = '
    <div>
    <div id="respond">
    <div id="formResponseText"></div>
    <form method="post" id="demanesContactForm">';
    //get guts in place
    $formcode .='
    <p><input type="text" id ="message_name" name="message_name" placeholder="';
    $contactFormNameCode = get_option('demanes_options');
    $formcode .= $contactFormNameCode['contact_form_name'];
    $formcode .= '" value="';
    $formcode .= '"></p>';
    $formcode .='
    <p><input type="text" id = "message_email" name="message_email" placeholder="';
    $contactFormEmailCode = get_option('demanes_options');
    $formcode .= $contactFormEmailCode['contact_form_email'];
    $formcode .= '" value="';
    $formcode .= '"></p>';
    $formcode .= '<p><textarea type="text" id = "message_text" name="message_text" placeholder="';
    $contactFormCommentsCode = get_option('demanes_options');
    $formcode .= $contactFormCommentsCode['contact_form_comments'];
    $formcode .= '">';
    $formcode .= '</textarea></p>';
    $formcode .= '<input type="hidden" name="productTitle" id="productTitle" value="'. $a['product'].'" >';
    $formcode .= wp_nonce_field('contact_form_widget_action','contact_form_widget_nounce');
    $formcode .='
    <p><input type="image" id="formSubmit" src="wp-content/themes/demanes/images/submitform.png" name="image" width="203" height="57"></p>
                </form>
              </div>
        </div>
    ';
    return $formcode;
}
add_shortcode('contactFormProduct', 'show_contact_form_product');





?>