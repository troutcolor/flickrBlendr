<?php 
/*
* Plugin Name: flickrblendr
* Description: shortcode for displaying a flickr slideshow two photos at a time blended
* Version: 0.1
* Author: John Johnston
* Author URI: http://johnjohnston.info
* License:     GPL2
* License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/
define( 'FLICKRBLENDR_URL', \plugin_dir_url( __FILE__ ) );

///just_a_test.mp3
function flickrblendr_shortcode_routine( $args ) {
	extract( shortcode_atts( array( 'flickrblendrsearch' => 'calm'), $args ) );
	$return = "";	
	// pay attention john
	$return= sprintf(
		"<div id='pic' data-flickrblendr='%s' class='flickrblendrfeed'  ><p id='licenses' class='info'></p><img src='".FLICKRBLENDR_URL."assets/Ajax-loader.gif' id='flickrblenderloader'></div>",esc_url( $flickrblendrsearch ) );
	//enqueue here so only add script & styles when needed
	
	//echo esc_attr( get_option('flickrblendr_apikey') )
	$flickrfeed="https://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=7177ae43badab8b5428ef2e2c7a66aac&license=7%2C8%2C2%2C4%2C5&sort=interestingness-desc&extras=url_c%2Clicense%2Cowner_name&per_page=500&format=json";
	wp_enqueue_script( 'flickrblendr');	
	
	wp_enqueue_script( 'flickrfeed', $flickrfeed, array( 'json2','jquery','flickrblendr' ),false,true );
	
	wp_enqueue_style ( 'flickrblendr' );
	// return the result
	return $return;
}

//add_shortcode('flickrblendr', 'flickrblendr_shortcode_routine');
//Above comment out because:  however when running in the plugin context you must hook the shortcode registration to init.
//see https://developer.wordpress.org/plugins/shortcodes/basic-shortcodes/

function flickrblendr_register_shortcode() {
    add_shortcode( 'flickrblendr', 'flickrblendr_shortcode_routine' );
}
 
add_action( 'init', 'flickrblendr_register_shortcode' );

 
function add_flickrblendr_scripts_basic(){
// wp_register_script Registers a script file in WordPress to be linked to a page later using the wp_enqueue_script() function, which safely handles the script dependencies.
	wp_register_script( 'flickrblendr', plugins_url( 'assets/flickrblendr.js', __FILE__ ), array( 'json2','jquery' ),false,true );	
	wp_register_style ( 'flickrblendr', plugins_url( 'assets/flickrblendr.css', __FILE__ ) );
}

add_action( 'init', 'add_flickrblendr_scripts_basic' );


/* Settings  */
 


add_action( 'admin_init', 'flickrblendr_plugin_settings' );

function flickrblendr_plugin_settings() {
	register_setting( 'flickrblendr-settings-group', 'flickrblendr_apikey' );
	 
}


 function flickrblendr_plugin_menu() {
	add_options_page( 'FlickrBlendr Options', 'FlickrBlendr', 'manage_options', __FILE__, 'flickrblendr_plugin_options' );
}

 
function flickrblendr_plugin_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	 
	?>
	<div class="wrap">
	<h2>FlickrBlendr Settings</h2>
 <p>You can find your api key at ..../</p>	 
 	<form method="post" action="options.php">
	<?php settings_fields( 'flickrblendr-settings-group' ); ?>
	<?php do_settings_sections( 'flickrblendr-settings-group' ); ?>
	<table class="form-table">
	
	<tr valign="top">
	<th scope="row">Flickr API Key</th>
	<td><input type="text" name="flickrblendr_apikey" value="<?php echo esc_attr( get_option('flickrblendr_apikey') ); ?>" /></td>
	</tr>
	 
	</table> 

	<?php submit_button(); ?>
 
	</form>
	</div>
	<?php 
	echo '</div>';
}
add_action( 'admin_menu', 'flickrblendr_plugin_menu' );
?>