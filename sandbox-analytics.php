<?php
/*
Plugin Name: Sandbox Analytics 
Plugin URI: http://stringcan.com
Description: Plugin to add Sandbox Analytics to the footer of your WordPress pages.
Author: Stringcan
Version: 1.0
Author URI: http://stringcan.com
*/

/**
 * Prevent this file from being called directly
 *
 */
if (!defined('ABSPATH')) {
	die("Aren't you supposed to come here via WP-Admin?");
}

/**
 * Menu icon CSS
 *
 */
function sandbox_analytics_menu_styles() {
	echo '<style type="text/css">
			a.toplevel_page_sandbox_analytics_menu_page img {
			  width: 1.4em;
			}
		  </style>';
}
add_action( 'admin_head', 'sandbox_analytics_menu_styles' );

/**
 *	Enqueue CSS on Sandbox Analytics - settings page ONLY 
 *		
 */
function sandbox_analytics_styles_to_adminPage() {
	//get current screen
	$screen_page = get_current_screen();

	//add plugin css ONLY to settings page
	if( 'toplevel_page_sandbox_analytics_menu_page' == $screen_page->id ){
		wp_enqueue_style( 'sandbox-analytics', plugins_url( 'css/sandbox-analytics.css', __FILE__ ),'20140605', false );
	}
}
add_action( 'admin_enqueue_scripts', 'sandbox_analytics_styles_to_adminPage' );


/**
 * Default Options on plugin activation
 *
 */ 
function sandbox_analytics_default_options() {

	if(get_option('sandbox_analytics_settings') == ''){		

		$sandbox_analytics_settings = array (
										'enable_plugin' => false,		// Enable plugin switch
										'sandbox_acct' => '',			// GreenRope account number
									);


		update_option('sandbox_analytics_settings', $sandbox_analytics_settings);

	}

}
register_activation_hook( __FILE__, 'sandbox_analytics_default_options' );

/**
 * Default Options on plugin activation
 *
 */ 
function addanalytic_admin_notice() {

	$plugin_settings_page = '<a href="' . admin_url( 'admin.php?page=sandbox_analytics_menu_page' ) . '">' . __('plugin settings page', 'sandbox-analytics' ) . '</a>';

	$sandbox_analytics_settings = get_option('sandbox_analytics_settings');

	if ($sandbox_analytics_settings['enable_plugin']) return;

	if ( !current_user_can( 'manage_options' ) ) return;

    echo '<div class="error">

	       <p>'.__('Sandbox Analytics is disabled. Please visit the ', 'sandbox-analytics' ). $plugin_settings_page . __(' to enable.', 'sandbox-analytics' ).'</p>

	     </div>';

}
add_action('admin_notices', 'addanalytic_admin_notice');


/** 
 * Add Sandbox Analytics code to theme footer
 *
 */
function sandbox_analytics_footerscript() {

	//$addanalytic_settings = addanalytic_read_options();
	$sandbox_analytics_settings = get_option('sandbox_analytics_settings');

	echo '
	<!-- Start Sandbox Analytics -->

	<script type="text/javascript">
		document.write(\'<img src="http://app.stringcansandbox.com/wt.pl?a=' . $sandbox_analytics_settings['sandbox_acct'] . '&r=\' + window.document.referrer + \'" height="1" width="1">\')
	</script>
	<!-- End Sandbox Analytics -->';

	/*document.write('<img src="http://app.stringcansandbox.com/wct.pl?a=37673&c=CONVERSION_CODE&v=VALUE&s=STAGE_NUMBER" height="1" width="1">')*/

}
add_action('wp_footer','sandbox_analytics_footerscript');

/**
 *	Resgister Settings Page
 *
 */
function register_sandbox_analytics_menu(){
	add_menu_page( 'Sandbox', 'Sandbox', 'manage_options', 'sandbox_analytics_menu_page', 'sandbox_analytics_options_page', plugins_url('sandbox-analytics/img/sandbox-menu-icon.jpg')); //dashicons-chart-pie dashicons-chart-line
}
add_action( 'admin_menu', 'register_sandbox_analytics_menu' );

/**
 * Generate options page
 *	
 */
function sandbox_analytics_options_page(){

	//Add support for decimal numbers 
    if (!current_user_can('manage_options')) {
      wp_die( _e('You do not have sufficient permissions to access this page.', 'animated-login') );
    }

    // See if the user has posted us some information
    if( isset($_POST['sandbox_analytics_save']) ){

    	check_admin_referer( 'sandbox_analytics_em_save_form', 'sandbox_analytics_em_name_of_nonce' );
 		 
 		$sandbox_analytics_settings = get_option('sandbox_analytics_settings');

		$sandbox_analytics_settings['enable_plugin'] = $_POST['enable_plugin'] ? true : false;
		$sandbox_analytics_settings['sandbox_acct'] = sanitize_text_field($_POST['sandbox_acct']);
		
		update_option('sandbox_analytics_settings', $sandbox_analytics_settings);

		$str = '<div id="message" class="updated fade"><p>'. __('Options saved successfully.', 'sandbox_analytics') .'</p></div>';

		echo $str;
    	
	    // Fail if !is a numeric value

	}
?>
<div class="wrap">
	<h2>Sandbox Analytics - settings</h2>
	<div id="page-wrap">
		<div id="inside">
		    <div id="side">
				<div class="side-widget">
					<span class="title"><?php _e('Powered by: ') ?></span>
					<div id="donate-form">
						<p style="text-align:center">Stringcan</p>			
					</div>
				</div><!-- end side-widget -->
		    </div><!-- end side -->
		    <div id="options-div">
			    <div id="headerimage">
					<?php $sandbox_analytics_settings = get_option('sandbox_analytics_settings'); ?>
			    	<img src="<?php echo plugins_url('sandbox-analytics/img/sandbox-logo.jpg'); ?>" alt="Sandbox Analytics logo"/>
				</div>
				<form method="post" id="addanalytic_options" name="addanalytic_options" style="border: #ccc 1px solid; padding: 10px" onsubmit="return checkForm()">
					<?php wp_nonce_field( 'sandbox_analytics_em_save_form', 'sandbox_analytics_em_name_of_nonce' ); ?>		  
					<fieldset class="options">
						<table class="form-table">
							<tr style="vertical-align: top; font-size:15px;">
							 <th scope="row" style="background:#<?php //if ($addanalytic_settings['enable_plugin']) echo 'cfc'; else echo 'fcc'; ?>">
							 	<b><label for="enable_plugin" id="enable"><?php _e('Enable Tracking: ', 'sandbox-analytics'); ?></label></b>
							 </th>
							 <td style="background:#<?php //if ($addanalytic_settings['enable_plugin']) echo 'cfc'; else echo 'fcc'; ?>">
							 	<input type="checkbox" name="enable_plugin" id="enable_plugin" <?php checked('1', $sandbox_analytics_settings['enable_plugin']); ?> />
							 </td>
							</tr>
						</table>
						<br />
						<table class="form-table">
							<tr style="vertical-align: top; font-size:13.2px;">
								<th scope="row">
									<b><label for="sandbox_acct"><?php _e('Sandbox Account Number: ', 'sandbox-analytics'); ?></label></b>
								</th>
								<td>
									<input type="textbox" name="sandbox_acct" id="sandbox_acct" value="<?php echo esc_attr($sandbox_analytics_settings['sandbox_acct']); ?>" style="width:167px" />
								</td>
							</tr>
							<tr style="vertical-align: top; ">
								<td scope="row" colspan="2">
									<textarea name="addanalytic_other" id="addanalytic_other" rows="8" cols="80" readonly><script language="JavaScript" type="text/javascript"> 
<!--document.write('<img src="http://app.stringcansandbox.com/wt.pl?a=<?php echo esc_attr($sandbox_analytics_settings['sandbox_acct']); ?>&r=' + window.document.referrer + '" height="1" width="1">')
//-->
</script>
									</textarea>
								</td>
							</tr>
						</table>
					</fieldset>
					<p>
					  <input type="submit" name="sandbox_analytics_save" id="sandbox_analytics_save" value="Save" style="border:#00CC00 1px solid" />
					</p>
		    	</form>
			</div><!-- end options div -->
		</div><!-- end inside -->
	<div style="clear: both;"></div>
	</div><!-- end pagewrap -->
</div><!-- end wrap -->
<?php } // function sandbox_analytics_options_page() closed 