<?php
/*
Plugin Name: Order Tracking Status
Plugin URI: http://www.EtoileWebDesign.com/order-tracking/
Description: Order tracking and status management plugin letting you post order updates and project status updates, with WooCommerce sync and email notifications
Author: Etoile Web Design
Author URI: http://www.EtoileWebDesign.com/order-tracking/
Terms and Conditions: http://www.etoilewebdesign.com/plugin-terms-and-conditions/
Text Domain: order-tracking
Version: 2.11.15
*/

global $EWD_OTP_db_version;
global $EWD_OTP_orders_table_name, $EWD_OTP_order_statuses_table_name, $EWD_OTP_fields_table_name, $EWD_OTP_fields_meta_table_name, $EWD_OTP_sales_reps, $EWD_OTP_customers;
global $wpdb;
global $ewd_otp_message;
global $EWD_OTP_Full_Version;
global $Sales_Rep_Only;
$EWD_OTP_orders_table_name = $wpdb->prefix . "EWD_OTP_Orders";
$EWD_OTP_order_statuses_table_name = $wpdb->prefix . "EWD_OTP_Order_Statuses";
$EWD_OTP_sales_reps = $wpdb->prefix . "EWD_OTP_Sales_Reps";
$EWD_OTP_customers = $wpdb->prefix . "EWD_OTP_Customers";
$EWD_OTP_fields_table_name = $wpdb->prefix . "EWD_OTP_Custom_Fields";
$EWD_OTP_fields_meta_table_name = $wpdb->prefix . "EWD_OTP_Fields_Meta";
$EWD_OTP_db_version = "2.11.9";

define( 'EWD_OTP_CD_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'EWD_OTP_CD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/*define('WP_DEBUG', true);
error_reporting(E_ALL);
$wpdb->show_errors(); */

/* When plugin is activated */
register_activation_hook(__FILE__,'Install_EWD_OTP');
register_activation_hook(__FILE__,'EWD_OTP_Default_Statuses');
register_activation_hook(__FILE__,'Run_EWD_OTP_Tutorial');
register_activation_hook(__FILE__,'EWD_OTP_Show_Dashboard_Link');

/* When plugin is deactivation*/
register_deactivation_hook( __FILE__, 'Remove_EWD_OTP' );
register_deactivation_hook( __FILE__, 'EWD_OTP_Revert_WC_Statuses' );

/* Creates the admin menu for the contests plugin */
if ( is_admin() ){
	add_action('admin_menu', 'EWD_OTP_Plugin_Menu');
	add_action('admin_menu', 'EWD_OTP_Sales_Rep_Menu');
	add_action('admin_head', 'EWD_OTP_Admin_Options');
	add_action('admin_init', 'Add_EWD_OTP_Scripts');
	add_action('init', 'Update_EWD_OTP_Content', 12);
	add_action('admin_notices', 'EWD_OTP_Error_Notices');
} else {
	add_action('init', 'Update_EWD_OTP_Non_Admin_Content');
}

//add_action('admin_head', 'EWD_OTP_Default_Statuses');
function EWD_OTP_Default_Statuses() {
	$StatusString = get_option("EWD_OTP_Statuses");
	$Statuses_Array = get_option("EWD_OTP_Statuses_Array");
	//if (!is_array($Statuses_Array) and $StatusString == "") {
	if ((!is_array($Statuses_Array) or ($Statuses_Array[0]['Status'] == "")) and $StatusString == "") {
		$Save_Statuses_Array = array(
			array("Status" => "Pending Payment", "Percentage" => "25", "Message" => "Default", "Internal" => "No"),
			array("Status" => "Processing", "Percentage" => "50", "Message" => "Default", "Internal" => "No"),
			array("Status" => "On Hold", "Percentage" => "50", "Message" => "Default", "Internal" => "No"),
			array("Status" => "Completed", "Percentage" => "100", "Message" => "Default", "Internal" => "No"),
			array("Status" => "Cancelled", "Percentage" => "0", "Message" => "Default", "Internal" => "No"),
			array("Status" => "Refunded", "Percentage" => "0", "Message" => "Default", "Internal" => "No"),
			array("Status" => "Failed", "Percentage" => "0", "Message" => "Default", "Internal" => "No")
		);
		update_option("EWD_OTP_Statuses_Array", $Save_Statuses_Array);
	}
}

function Remove_EWD_OTP() {
  	/* Deletes the database field */
	delete_option('EWD_OTP_db_version');
}


/* Admin Page setup */
function EWD_OTP_Plugin_Menu() {
	$Access_Role = get_option("EWD_OTP_Access_Role");

	if ($Access_Role == "") {$Access_Role = "administrator";}
	if (current_user_can($Access_Role)) {
		add_menu_page('Order Tracking Plugin', 'Tracking', $Access_Role, 'EWD-OTP-options', 'EWD_OTP_Output_Options', 'dashicons-location' , '50.8');
		add_submenu_page('EWD-OTP-options', 'OTP Orders', 'Orders', $Access_Role, 'EWD-OTP-options&DisplayPage=Orders', 'EWD_OTP_Output_Options');
		add_submenu_page('EWD-OTP-options', 'OTP Statuses', 'Statuses', $Access_Role, 'EWD-OTP-options&DisplayPage=Statuses', 'EWD_OTP_Output_Options');
		add_submenu_page('EWD-OTP-options', 'OTP Locations', 'Locations', $Access_Role, 'EWD-OTP-options&DisplayPage=Locations', 'EWD_OTP_Output_Options');
		add_submenu_page('EWD-OTP-options', 'OTP SalesReps', 'Sales Reps', $Access_Role, 'EWD-OTP-options&DisplayPage=SalesReps', 'EWD_OTP_Output_Options');
		add_submenu_page('EWD-OTP-options', 'OTP Customers', 'Customers', $Access_Role, 'EWD-OTP-options&DisplayPage=Customers', 'EWD_OTP_Output_Options');
		add_submenu_page('EWD-OTP-options', 'OTP Emails', 'Emails', $Access_Role, 'EWD-OTP-options&DisplayPage=Emails', 'EWD_OTP_Output_Options');
		add_submenu_page('EWD-OTP-options', 'OTP CustomFields', 'Custom Fields', $Access_Role, 'EWD-OTP-options&DisplayPage=CustomFields', 'EWD_OTP_Output_Options');
		add_submenu_page('EWD-OTP-options', 'OTP Options', 'Options', $Access_Role, 'EWD-OTP-options&DisplayPage=Options', 'EWD_OTP_Output_Options');
	}
}

function EWD_OTP_Sales_Rep_Menu() {
	global $wpdb, $EWD_OTP_sales_reps;
	
	$Current_User = wp_get_current_user();
	$Sql = "SELECT Sales_Rep_ID FROM $EWD_OTP_sales_reps WHERE Sales_Rep_WP_ID='" . $Current_User->ID . "'";
	$Sales_Rep_ID = $wpdb->get_var($Sql);

	if ($Sales_Rep_ID != "") {add_menu_page('Order Tracking Plugin', 'Order Tracking', 'read', 'EWD-OTP-options', 'EWD_OTP_Output_Sales_Rep_Options', null, '50.9');}
}

/* Add localization support */
function EWD_OTP_localization_setup() {
		load_plugin_textdomain('order-tracking', false, dirname(plugin_basename(__FILE__)) . '/lang/');
}
add_action('after_setup_theme', 'EWD_OTP_localization_setup');

// Add settings link on plugin page
function EWD_OTP_plugin_settings_link($links) { 
  $settings_link = '<a href="admin.php?page=EWD-OTP-options">Settings</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}
 
$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'EWD_OTP_plugin_settings_link' );

function Add_EWD_OTP_Scripts() {
	global $EWD_OTP_db_version;

	wp_enqueue_script('ewd-otp-review-ask', plugins_url("js/ewd-otp-dashboard-review-ask.js", __FILE__, array('jquery'), $EWD_OTP_db_version));
	if (isset($_GET['page']) && $_GET['page'] == 'EWD-OTP-options') {
		wp_enqueue_script('jquery-ui-sortable');
		$url_six = plugins_url("order-tracking/js/sorttable.js");
		wp_enqueue_script('sorttable', $url_six, array('jquery'), $EWD_OTP_db_version);
		$url_one = plugins_url("order-tracking/js/Admin.js");
		wp_enqueue_script('PageSwitch', $url_one, array('jquery', 'sorttable'), $EWD_OTP_db_version);
		$url_two = plugins_url("order-tracking/js/jquery.confirm.min.js");
		wp_enqueue_script('EWD_OTP_Confirmation', $url_two, array('jquery'), $EWD_OTP_db_version);
		$url_three = plugins_url("order-tracking/js/bootstrap.min.js");
		wp_enqueue_script('EWD_OTP_Bootstrap', $url_three, array('jquery'), $EWD_OTP_db_version);
		$url_five = plugins_url("order-tracking/js/spectrum.js");
		wp_enqueue_script('specttrum', $url_five, array('jquery'), $EWD_OTP_db_version);
	}

	if (isset($_GET['page']) && $_GET['page'] == 'ewd-otp-getting-started') {
		wp_enqueue_script('ewd-otp-getting-started', EWD_OTP_CD_PLUGIN_URL . 'js/ewd-otp-getting-started.js', array('jquery'), $EWD_OTP_db_version);
		wp_enqueue_script('spectrum', EWD_OTP_CD_PLUGIN_URL . 'js/spectrum.js', array('jquery'), $EWD_OTP_db_version);
		wp_enqueue_script('PageSwitch', EWD_OTP_CD_PLUGIN_URL . 'js/Admin.js', array('jquery', 'jquery-ui-sortable', 'spectrum'), $EWD_OTP_db_version);
	}
}


add_action( 'wp_enqueue_scripts', 'Add_EWD_OTP_FrontEnd_Scripts' );
function Add_EWD_OTP_FrontEnd_Scripts() {
	global $EWD_OTP_db_version;

	wp_enqueue_script('ewd-otp-js', plugins_url( '/js/ewd-otp-js.js' , __FILE__ ), array( 'jquery' ), $EWD_OTP_db_version);
}


add_action( 'wp_enqueue_scripts', 'EWD_OTP_Add_Stylesheet' );
function EWD_OTP_Add_Stylesheet() {
	global $EWD_OTP_db_version;

    $Mobile_Stylesheet = get_option("EWD_OTP_Mobile_Stylesheet");

    wp_register_style( 'ewd-otp-style', plugins_url('css/otp-styles.css', __FILE__), array(), $EWD_OTP_db_version );
    if ($Mobile_Stylesheet == "Yes") {wp_register_style( 'ewd-otp-style-mobile', plugins_url('css/otp-styles-mobile.css', __FILE__), array(), $EWD_OTP_db_version );}
	wp_register_style( 'yahoo-pure-buttons', plugins_url('css/pure-buttons.css', __FILE__), array(), $EWD_OTP_db_version );
	wp_register_style( 'yahoo-pure-forms', plugins_url('css/pure-forms.css', __FILE__), array(), $EWD_OTP_db_version );
	wp_register_style( 'yahoo-pure-forms-nr', plugins_url('css/pure-forms-nr.css', __FILE__), array(), $EWD_OTP_db_version );
	wp_register_style( 'yahoo-pure-grids', plugins_url('css/pure-grids.css', __FILE__), array(), $EWD_OTP_db_version );
	wp_register_style( 'yahoo-pure-grids-nr', plugins_url('css/pure-grids-nr.css', __FILE__), array(), $EWD_OTP_db_version );
    wp_enqueue_style( 'ewd-otp-style' );
    if ($Mobile_Stylesheet == "Yes") {wp_enqueue_style( 'ewd-otp-style-mobile' );}
	wp_enqueue_style( 'yahoo-pure-buttons' );
	wp_enqueue_style( 'yahoo-pure-forms' );
	wp_enqueue_style( 'yahoo-pure-forms-nr' );
	wp_enqueue_style( 'yahoo-pure-grids' );
	wp_enqueue_style( 'yahoo-pure-grids-nr' );
}


function EWD_OTP_Admin_Options() {
	global $EWD_OTP_db_version;
	
	wp_enqueue_style( 'ewd-otp-admin-css', plugins_url("order-tracking/css/Admin.css"), array(), $EWD_OTP_db_version);
	wp_enqueue_style( 'ewd-otp-spectrum', plugins_url("order-tracking/css/spectrum.css"), array(), $EWD_OTP_db_version);
	wp_enqueue_style( 'ewd-oto-welcome-screen', EWD_OTP_CD_PLUGIN_URL . 'css/ewd-otp-welcome-screen.css', array(), $EWD_OTP_db_version);
}

function Run_EWD_OTP_Tutorial() {
	update_option("EWD_OTP_Run_Tutorial", "Yes");
}

if ((isset($_GET['page'])) and get_option("EWD_OTP_Run_Tutorial") == "Yes" and $_GET['page'] == 'EWD-OTP-options') {
	add_action( 'admin_enqueue_scripts', 'EWD_OTP_Set_Pointers', 10, 1);
}

function EWD_OTP_Set_Pointers($page) {
	  $Pointers = EWD_OTP_Return_Pointers();
	
	  //Arguments: pointers php file, version (dots will be replaced), prefix
	  $manager = new EWD_OTP_PointersManager( $Pointers, '1.0', 'ewd_otp_admin_pointers' );
	  $manager->parse();
	  $pointers = $manager->filter( $page );
	  if ( empty( $pointers ) ) { // nothing to do if no pointers pass the filter
	    return;
	  }
	  wp_enqueue_style( 'wp-pointer' );
	  $js_url = plugins_url( 'js/ewd-otp-pointers.js', __FILE__ );
	  wp_enqueue_script( 'ewd_otp_admin_pointers', $js_url, array('wp-pointer'), NULL, TRUE );
	  //data to pass to javascript
	  $data = array(
	    'next_label' => __( 'Next' ),
	    'close_label' => __('Close'),
	    'pointers' => $pointers
	  );
	  wp_localize_script( 'ewd_otp_admin_pointers', 'MyAdminPointers', $data );
	update_option("EWD_OTP_Run_Tutorial", "No");
}

add_action('activated_plugin','save_otp_error');
function save_otp_error(){
	update_option('plugin_error',  ob_get_contents());
	file_put_contents("Error.txt", ob_get_contents());
}

$EWD_OTP_Full_Version = get_option("EWD_OTP_Full_Version");

if (isset($_POST['EWD_OTP_Upgrade_To_Full'])) {
	add_action('admin_init', 'EWD_OTP_Upgrade_To_Full');
}

function EWD_OTP_Show_Dashboard_Link() {
	set_transient('ewd-otp-getting-started', true, 30);
}

$Show_TinyMCE = get_option("EWD_OTP_Show_TinyMCE");
if ($Show_TinyMCE == "Yes") {
	add_filter( 'mce_buttons', 'EWD_OTP_Register_TinyMCE_Buttons' );
	add_filter( 'mce_external_plugins', 'EWD_OTP_Register_TinyMCE_Javascript' );
	add_action('admin_head', 'EWD_OTP_Output_TinyMCE_Vars');
}

function EWD_OTP_Register_TinyMCE_Buttons( $buttons ) {
   array_push( $buttons, 'separator', 'OTP_Shortcodes' );
   return $buttons;
}
 
function EWD_OTP_Register_TinyMCE_Javascript( $plugin_array ) {
   $plugin_array['OTP_Shortcodes'] = plugins_url( '/js/tinymce-plugin.js',__FILE__ );

   return $plugin_array;
}

function EWD_OTP_Output_TinyMCE_Vars() {
	global $EWD_OTP_Full_Version;
	
	$Statuses_Array = get_option("EWD_OTP_Statuses_Array");
	$Locations_Array = get_option("EWD_OTP_Locations_Array");
	if (!is_array($Statuses_Array)) {$Statuses_Array = array();}
	if (!is_array($Locations_Array)) {$Locations_Array = array();}

	echo "<script type='text/javascript'>";
	echo "var otp_premium = '" . $EWD_OTP_Full_Version . "';\n";
	echo "var order_statuses = " . json_encode($Statuses_Array) . ";\n";
	echo "var order_locations = " . json_encode($Locations_Array) . ";\n";
	echo "</script>";
}

include "blocks/ewd-otp-blocks.php";
include "Functions/DisplayGraph.php";
include "Functions/Error_Notices.php";
include "Functions/EWD_OTP_Deactivation_Survey.php";
include "Functions/EWD_OTP_Export_To_Excel.php";
include "Functions/EWD_OTP_Help_Pointers.php";
include "Functions/EWD_OTP_Initial_Data.php";
include "Functions/EWD_OTP_Output_Buffering.php";
include "Functions/EWD_OTP_Output_Options.php";
include "Functions/EWD_OTP_Pointers_Manager_Interface.php";
include "Functions/EWD_OTP_Pointers_Manager_Class.php";
include "Functions/EWD_OTP_Return_Results.php";
include "Functions/EWD_OTP_Statistics.php";
include "Functions/EWD_OTP_Styling.php";
include "Functions/EWD_OTP_UWPM_Email_Integration.php";
include "Functions/EWD_OTP_Version_Reversion.php";
include "Functions/EWD_OTP_Widgets.php";
include "Functions/EWD_OTP_Woo_Commerce_Integration.php";
include "Functions/EWD_OTP_Zendesk_Integration.php";
include "Functions/FrontEndAjaxUrl.php";
include "Functions/Full_Upgrade.php";
include "Functions/Install_EWD_OTP.php";
include "Functions/Prepare_Data_For_Insertion.php";
include "Functions/Process_Ajax.php";
include "Functions/Update_Admin_Databases.php";
include "Functions/Update_EWD_OTP_Content.php";
include "Functions/Update_EWD_OTP_Tables.php";
include "Functions/Version_Upgrade.php";
include "Functions/EWD_OTP_IPN.php"; //needs to be last

include "Shortcodes/InsertCustomerForm.php";
include "Shortcodes/InsertCustomerOrderForm.php";
include "Shortcodes/InsertSalesRepForm.php";
include "Shortcodes/InsertTrackingForm.php";

// Updates the OTP database when required
if (get_option('EWD_OTP_db_version') != $EWD_OTP_db_version) {
	Update_EWD_OTP_Tables();
}

if (get_option("EWD_OTP_Remove_Old_Statistics_Checked") != date("Y-m-d")) {
	EWD_OTP_Remove_Old_Statistics();
}

?>