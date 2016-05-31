<?php 
/*
* Plugin Name: flickrblendr
* Description: custom post a flickr slideshow two photos at a time blended
* Version: 0.1.1
* Author: John Johnston
* Author URI: http://johnjohnston.info
* License:     GPL2
* License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/
 
 
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
	register_setting( 'flickrblendr-settings-group', 'flickrblendr_showmode' );
	 
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
	<p>You can create/find your api key at <a href="https://www.flickr.com/services/api/keys/">Flickr API Keys</a>. You will need to sign in.</p>	 
 	<form method="post" action="options.php">
	<?php settings_fields( 'flickrblendr-settings-group' ); ?>
	<?php do_settings_sections( 'flickrblendr-settings-group' ); ?>
	<table class="form-table">	
	<tr valign="top">
	<th scope="row">Flickr API Key</th>
	<td>
<input type="text" name="flickrblendr_apikey" value="<?php echo esc_attr( get_option('flickrblendr_apikey') ); ?>" />
</td>
	</tr>
	<tr>
	<td>Use mode popup: <input name="flickrblendr_showmode" type="checkbox" value="1" <?php checked( '1', get_option( 'flickrblendr_showmode' ) ); ?> />
	<em>This dosen't work yet, the popup is always there.</em>
	</td></tr>
	</table> 

	<?php submit_button(); ?>
 
	</form>
	</div>
	<?php 
	echo '</div>';
}
add_action( 'admin_menu', 'flickrblendr_plugin_menu' );
 
	
	/*
		Custom post type
	*/
	
	function my_custom_post_flickrblendr() {
		$labels = array(
			'name'               => _x( 'FlickrBlendr', 'post type general name' ),
			'singular_name'      => _x( 'FlickrBlendr', 'post type singular name' ),
			'add_new'            => _x( 'Add New', 'FlickrBlendr' ),
			'add_new_item'       => __( 'Add New FlickrBlendr' ),
			'edit_item'          => __( 'Edit FlickrBlendr' ),
			'new_item'           => __( 'New FlickrBlendr' ),
			'all_items'          => __( 'All FlickrBlendrs' ),
			'view_item'          => __( 'View FlickrBlendr' ),
			'search_items'       => __( 'Search FlickrBlendr' ),
			'not_found'          => __( 'No FlickrBlendrs found' ),
			'not_found_in_trash' => __( 'No FlickrBlendrs found in the Trash' ), 
			'parent_item_colon'  => '',
			'menu_name'          => 'FlickrBlendrs'
		);
		$args = array(
			'labels'        => $labels,
			'description'   => 'Holds FlickrBlendrs and show specific data',
			'public'        => true,
			'menu_position' => 5,
			'hierarchical'	=> true,
			'supports'      => array( 'title', 'editor', 'thumbnail','custom-fields' ),
			'has_archive'   => false,
		);
		register_post_type( 'flickrblendr', $args );	
	}
	
	add_action( 'init', 'my_custom_post_flickrblendr' );
	
	
	add_filter( 'the_content', 'flickrblendr_before_content' ); 
 
	 function flickrblendr_before_content( $content ) { 
		 if ( is_singular('flickrblendr') ) {
			$flickrblendr= flickrblendr_content();
			$content = $flickrblendr . $content;
			}
			return $content;
	}
	
function flickrblendrpage_enqueue_scripts() {
	if ( is_singular('flickrblendr') ) {
		//enqueue here so only add script & styles when needed
		//echo esc_attr( get_option('flickrblendr_apikey') )
		$flickrapikey=get_option('flickrblendr_apikey');
	
		$flickrfeed="https://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=".$flickrapikey."&license=7%2C8%2C2%2C4%2C5&sort=interestingness-desc&extras=url_c%2Clicense%2Cowner_name&per_page=500&format=json";
	
		wp_enqueue_script( 'flickrblendr');		
		wp_enqueue_script( 'flickrfeed', $flickrfeed, array( 'json2','jquery','flickrblendr' ),false,true );
		wp_enqueue_style ( 'flickrblendr' );	
			
		}
}
	
add_action( 'wp_enqueue_scripts', 'flickrblendrpage_enqueue_scripts' );
	
	function flickrblendr_content(){
		$flickrblendrsearch="calm";
		 $flickrblendrurl=plugin_dir_url( __FILE__ ) ;
		
		$r='<div id="flickrblendrholder">
	<div id="flickrblendr"><div id="controlwrap"><div id="info">i</div><div id="controls">
		<div id="expand" onclick="fullscreen();">
		
			<img src="'.$flickrblendrurl.'assets/fullscreen.png"></div>
			Blend Mode: <select name="mode" id="mode"  onchange="swapmode();return true;" size="1">
				<option value="multiply" >multiply</option>
				<option value="screen">screen</option>
				<option value="overlay">overlay</option>
				<option value="darken">darken</option>
				<option value="lighten">lighten</option>
				<option value="color-dodge">color-dodge</option>
				<option value="color-burn">color-burn</option>
				<option value="hard-light">hard-light</option>
				<option value="soft-light">soft-light</option>
				<option value="difference">difference</option>
				<option value="exclusion" selected>exclusion</option>
				<option value="hue">hue</option>
				<option value="saturation">saturation</option>
				<option value="color">color</option>
				<option value="luminosity">luminosity</option>		
			</select>    </div></div>

		<div id="pic" data-flickrblendr="'.$flickrblendrsearch.'" class="flickrblendrfeed"  ><p id="licenses" class="info"></p><img src="'.$flickrblendrurl.'assets/Ajax-loader.gif" id="flickrblenderloader""></div>
		</div></div>';
		return $r;
	}

	function flickrblendr_activate() {
	    // register taxonomies/post types here
	    flush_rewrite_rules();
	}

	register_activation_hook( __FILE__, 'flickrblendr_activate' );

	function flickrblendr_deactivate() {
	    flush_rewrite_rules();
	}
	register_deactivation_hook( __FILE__, 'flickrblendr_deactivate' );
	
	
?>