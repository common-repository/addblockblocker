<?php
/*
	Plugin Name: Adblock Blocker
	Plugin URI: http://www.dotsquares.com/
	Description: Adblock Blocker plugin is a great addon to your wordpress site it detects if your visitors are using any ad-blocker on their browsers to block the advertisements, if your revenue comes from advertisements then you are loosing all those clicks as people are using ad-blocker scripts and this plugin will help you track those and disallow sites functionality until they disable the ad-block scripts.

We have tested this on many popular ad-blocker scripts like fsd, sdf,fdasf etc, we will keep adding support for many others aswell, please feel free to share which you want to detect first.
	Version: 0.0.1
	Author: Dotsquares
    Text Domain: add-block
	License: GPLv2 or later
	License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

add_action('wp_head', 'addblock' );

add_action( 'admin_menu', 'register_adblockadmin' );

add_action( 'wp_ajax_adblockvisitor', 'adblockvisitor_callback' );
add_action( 'wp_ajax_nopriv_adblockvisitor', 'adblockvisitor_callback' );

add_action( 'wp_ajax_adblockvisitordis', 'adblockvisitordis_callback' );
add_action( 'wp_ajax_nopriv_adblockvisitordis', 'adblockvisitordis_callback' );

add_action( 'plugins_loaded', 'addblock_load_textdomain' );

function addblock_load_textdomain() {
  load_plugin_textdomain( 'add-block', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' ); 
}

//action hook for plugin activation
function installScript_adblock() {

    include('installScript_adblock.php');
	
}

register_activation_hook( __FILE__, 'installScript_adblock' );

// Create Menu in admin section
function register_adblockadmin(){
    add_menu_page( 'Ad Block Settings', __('Adblock Blocker','add-block'), 'manage_options', 'addblock', 'adblocker_menu_page', plugins_url( '/images/icon.png',__FILE__ ));
}

function adblocker_menu_page()
{   
	global $wpdb; 
	include('addblock-admin.php');
}

add_action( 'wp_ajax_getcountryuser', 'adblocker_ajax_callback' );
add_action('wp_ajax_nopriv_getcountryuser', 'adblocker_ajax_callback');

function adblocker_ajax_callback() {
	
	include('getcountryuser.php');
}

function adblockvisitor_callback(){
	
	// Save user information 
	
	$visitor_ip = $_SERVER['REMOTE_ADDR'];
	
	
	$ip_data = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$visitor_ip));
    $usercountry = $ip_data->geoplugin_countryName;
	$usertime = time(); 
	$cookievalue = $visitor_ip.'_'.$usertime;
	if(is_user_logged_in()){
		
			$current_user = wp_get_current_user();
			$useradblockid = $current_user->ID ;
	}
	if(isset($_COOKIE["unique_adblock"])){
		global $wpdb;
		$str = explode("_",$_COOKIE["unique_adblock"]);
		
		$queryipcount = "SELECT COUNT(*) as co FROM ".$wpdb->prefix ."adblock WHERE ip='".$str[0]."' AND usertime=".$str[1]; 
		$rowipcount = $wpdb->get_row($queryipcount) or die('not run1');
		
		
		$queryip = "SELECT ip,usertime FROM ".$wpdb->prefix ."adblock WHERE ip='".$str[0]."' AND usertime=".$str[1]; 
		$rowip = $wpdb->get_row($queryip) ;
		
		
		$countip =  $rowip->ip;
		$countusertime = $rowip->usertime;
		if($rowipcount->co > 0){
			/* When cookie is set and and user again enable the add on */
		    global $wpdb;
			$wpdb->update($wpdb->prefix.'adblock', array('adblock_status'=>'NO'), array('ip'=>$countip,'usertime' => $countusertime))  ;
		}else{
			/* When cookie is set and  user detail is not available in database */
			
			$data = array(
			'ip' => $visitor_ip,
			'usertime' => $usertime,
			'userloginid' => $useradblockid,
			'usercountry' => $usercountry,
			'adblock_status' => 'NO'
			);
			global $wpdb;
			
			$wpdb->insert($wpdb->prefix.'adblock', $data);
			
			//Reset Cookie value
			
			setcookie("unique_adblock", $cookievalue);
		}
	
		
	}else{
		// Setting Cookie
		
		setcookie("unique_adblock", $cookievalue);
		
			/* Inserting user informaion in Database */

			$data = array(
			'ip' => $visitor_ip,
			'usertime' => $usertime,
			'userloginid' => $useradblockid,
			'usercountry' => $usercountry,
			'adblock_status' => 'NO'
			);
			global $wpdb;

			$wpdb->insert($wpdb->prefix.'adblock', $data);
	}
	
	
}

/* Add On Disable Hook */

function adblockvisitordis_callback(){
	
	$str = explode("_",$_COOKIE["unique_adblock"]);
	global $wpdb;

	$wpdb->update($wpdb->prefix.'adblock', array('adblock_status'=>'YES'), array('ip'=>$str[0],'usertime'=>$str[1])) or die('nor run') ;
	

}

function addblock() {
$userumsg =  get_option( 'usermsg' ) ;
$userimagepath =  get_option( 'imagepath' ) ;

if($userimagepath == "images/large.png")
		{
			 $actualImagePath = plugin_dir_url(__FILE__).$userimagepath;
		}
		else{
			$path_array  = wp_upload_dir();
			 $actualImagePath = $path_array['baseurl'].$userimagepath;
		}	
?>
<div class="afs_ads">&nbsp;</div>
<!-- Pop up Message -->
<div style="display:none;">
    <div id="dialog">

	<!--<img src="<?php //echo plugins_url($userimagepath, __FILE__ ); ?>" width="444" height="265"/>-->
	<img src="<?php echo $actualImagePath; ?>" width="444" height="265"/>
      <p><?php echo $userumsg ; ?></p>
    </div>
<script>
(function() {

        // Define a function for showing the message.
        // Set a timeout of 1 seconds to give adblocker
     	// Detecting Add on is enable or disable
		
        var tryMessage = function() {
            setTimeout(function() {
                if(!document.getElementsByClassName) return;
                var ads = document.getElementsByClassName('afs_ads'),
                    ad  = ads[ads.length - 1];
                if(!ad
                    || ad.innerHTML.length == 0
                    || ad.clientHeight === 0) { 
						jQuery.ajax({
						 type : "post",
						 url : "<?php echo admin_url(); ?>admin-ajax.php?action=adblockvisitor",
								success: function(response) {
									//console.log(response);
								}
							});
						jQuery("#dialog").dialog({
								title:'*Stop* seems like you are using ad-blocker',
								closeOnEscape:false,
								modal: true,
								draggable: false,
								height: 420,
								width: 533,
								create: function( event, ui ) {
									jQuery(".ui-dialog .ui-dialog-titlebar #ui-id-1").css({'text-align':'center','width':'100%'})
									}
						   });						 
									 
						}else {
						 	jQuery.ajax({
						 	type : "post",
						 	url : "<?php echo admin_url(); ?>admin-ajax.php?action=adblockvisitordis",
								success: function(response) {
									//alert(response);
								}
							});
						}

            }, 1000);
        }

        /* Attach a listener for page load ... then show the message */
        if(window.addEventListener) {
            window.addEventListener('load', tryMessage, false);
			
        } else {
            window.attachEvent('onload', tryMessage); //IE
        }
})();
</script>
 </div>
<?php
} 

/* Including Files in Plugin */

function addScriptAdblocker(){
	
	wp_enqueue_script( 'jquery-ui-core' );  
    wp_enqueue_script( 'jquery-ui-dialog' ); 
	wp_enqueue_style (  'wp-jquery-ui-dialog'); 
	wp_enqueue_style('jqueryuicss', plugins_url('style.css', __FILE__ ));
	
}

add_action('wp_enqueue_scripts', 'addScriptAdblocker');


?>