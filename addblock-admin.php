<style type="text/css">
.adbstatus{line-height: 33px; font-size: 14px; float:left;width:47%; min-height:200px;}
.userview{float: left; width: 340px;}
#adminsettings{width:60%;border-top: 1px solid #000000;float: left;padding-top: 15px;}
#adminsettings label{vertical-align: top;font-weight: bold;margin-right: 15px;width: 160px;display: inline-block;}
#adminsettings .btn{background-color: rgb(48, 41, 41);border: 0px;padding: 8px 20px;color: #fff;font-weight: bold;font-size: 15px;border-radius: 5px;cursor:pointer;}
#adminsettings input[type=file]{margin:10px 0px;}
.border{border-bottom:1px solid #000033;padding-bottom:20px; width:90%;color:#302929;}
.countryusers{float:left;margin-top:0px;margin-right:20px;}
#countryview{margin-bottom:20px;}
</style>
<?php 
		//global $wpdb;
		$queryno = "SELECT COUNT(*) as co FROM ".$wpdb->prefix ."adblock WHERE adblock_status='NO'";
		$rowno = $wpdb->get_row($queryno) or die('nont run');		
		$queryyes = "SELECT COUNT(*) as co FROM ".$wpdb->prefix ."adblock WHERE adblock_status='YES'";
		$rowyes = $wpdb->get_row($queryyes) or die('nont run');		
		$querytno = "SELECT COUNT(*) as co FROM ".$wpdb->prefix ."adblock";
		$rowtno = $wpdb->get_row($querytno) or die('nont run');
		$query_acccity = "SELECT DISTINCT usercountry FROM ".$wpdb->prefix ."adblock WHERE usercountry != '' AND usercountry != 'NULL' ORDER BY usercountry";
		$rowtacccity = $wpdb->get_results($query_acccity, ARRAY_N) ;
		$different_city = count($rowtacccity);
		$usermsg = get_option( 'usermsg' );
		$imagepath = get_option( 'imagepath' );
		if($imagepath == "images/large.png")
		{
			 $actualImagePath = plugin_dir_url(__FILE__).$imagepath;
		}
		else{
			$path_array  = wp_upload_dir();
			 $actualImagePath = $path_array['baseurl'].$imagepath;
		}	
		
?>
		<!-- Plugin Dashboard View -->
		
       <h1 class="border"><?php _e('Adblock Blocker','add-block'); ?></h1>
	   <div class="adbstatus">
	   <h2><?php _e('Total Users Status','add-block'); ?></h2>
			<div class="userview"><?php _e('Total users that uses Adblock Add-Ons:','add-block'); ?> </div><div><span><?php echo $rowtno->co ; ?></span></div>
			<div class="userview"><?php _e('Users that enable Adblock Add-Ons :','add-block'); ?> </div><div><span><?php echo $rowno->co; ?></span></div>
			<div class="userview"><?php _e('Users that disable Adblock Add-Ons :','add-block'); ?> </div><div><span> <?php echo $rowyes->co; ?></span></div>
		</div><br/>
		
		<!-- Country Status -->
		
		<div class="adbstatus">
		<h2 class="countryusers"><?php _e('Country User Status','add-block'); ?></h2>
		<form name="countyview" action="" id="countryview" method="post">	
		<select onChange="getcountrystatus()" id="usercountry" name="usercountry">
			<option value="nocountry" selected="selected"><?php _e('Select Country','add-block'); ?></option>
			<?php for($i=0;$i< $different_city ; $i++){ ?>
				<option value="<?php  echo($rowtacccity[$i][0]); ?>"><?php echo($rowtacccity[$i][0]); ?>	</option>			
			<?php } ?>
		</select>
		</form>
		<div id="adbl_userdata">
			<h3 style="color:#0033FF;"><?php _e('No Country is selected.','add-block'); ?></h3>
		</div>
		</div>
		
		<!-- Popup Settings -->
		
		<form name="adminsettings" action="<?php echo admin_url(); ?>admin-ajax.php?action=getcountryuser&cs=2" id="adminsettings" method="POST" enctype="multipart/form-data">
		<h2><?php _e('Adblock Blocker Setting','add-block'); ?></h2><br/>
			<label><?php _e('Change Pop up Message :','add-block'); ?></label>
			<textarea name="usermsg" id="usermsg" rows="7" cols="51"><?php echo $usermsg; ?></textarea> <br/><br/>
			<label><?php _e('Change Pop up Image :','add-block'); ?></label>
			<img src="<?php echo $actualImagePath; ?>" width="444" height="265" id="adminimage"/><br/>
			<!--<img src="<?php //echo plugin_dir_url(__FILE__).$imagepath; ?>" width="444" height="265" id="adminimage"/><br/>-->
			<label>&nbsp;</label><input type="file" name="popimg" id="popimg"/>	<br/><br/>
			<label>&nbsp;</label><input type="submit" value="<?php _e('Submit','add-block'); ?>" class="btn"/>	
			<input type="button" value="<?php _e('Restore','add-block'); ?>" class="btn" onclick="restroredefault()"/>	
		</form>
		
	
		
		
<script type="text/javascript">
function getcountrystatus(){
	var selectedcon = jQuery('#usercountry :selected').val();
	jQuery.ajax({
	type : "post",
	url:"<?php echo admin_url(); ?>admin-ajax.php?action=getcountryuser&co="+selectedcon+"&cs=1",
	success: function(response) {
			jQuery("#adbl_userdata").html(response);
		}
	});
}

function restroredefault(){
	var r = confirm("Are you sure you want to restore default values.");
	if(r==true){
		jQuery.ajax({
		type : "post",
		 url : "<?php echo admin_url(); ?>admin-ajax.php?action=getcountryuser&cs=3",
		success: function(response) {			
				jQuery("#usermsg").html('Please disable any ad-blocker you are using in your browser.');	
				jQuery("#adminimage").attr('src',"<?php echo plugin_dir_url(__FILE__);?>/images/large.png");	
			}
		});
	}
}

</script>



