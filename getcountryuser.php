<?php

	$status = $_REQUEST['cs'];
	if($status==1)
	{
		$country = $_REQUEST['co'];
		if($country == 'nocountry'){
			echo "<h3 style=\"color:#0033FF;\"> ". __('No Country is selected','add-block')." </h3>"; die();
			
		}else{
			global $wpdb;
			$querycono = "SELECT COUNT(*) as co FROM ".$wpdb->prefix ."adblock WHERE adblock_status='NO' AND usercountry='".$country."'";
			$rowcono = $wpdb->get_row($querycono) ;		
			$querycoyes = "SELECT COUNT(*) as co FROM ".$wpdb->prefix ."adblock WHERE adblock_status='YES' AND usercountry='".$country."'";
			$rowcoyes = $wpdb->get_row($querycoyes) ;	
			$querycotno = "SELECT COUNT(*) as co FROM ".$wpdb->prefix ."adblock WHERE usercountry='".$country."'";
			$rowcotno = $wpdb->get_row($querycotno) ;
			echo "<div>
				<div> ". __('Total users that uses Add-ons :-','add-block') ." <strong>" .$rowcotno->co ."</strong></div>
				<div> ". __('Only Add-ons users :-','add-block') ."  <strong>". $rowcono->co . "</strong></div>
				<div> ". __('Users that disable Add-ons:-','add-block') ."  <strong>" .$rowcoyes->co."</strong></div>
			</div>";die();
		}
	}
	elseif($status == 3)
	{ 
		update_option(  'usermsg', 'Please disable any ad-blocker you are using in your browser.' );
		update_option(  'imagepath', 'images/large.png' );		
	}
	else
	{
		global $wpdb;
		$usermsg = $_POST['usermsg'];
		if(empty($usermsg)){$usermsg = '&nbsp;';}
		$redirectpath = admin_url().'admin.php?page=addblock';
		update_option('usermsg',$usermsg);			
		// Example of accessing data for a newly uploaded file
		$fileName = $_FILES["popimg"]["name"]; 
		$fileTmpLoc = $_FILES["popimg"]["tmp_name"];
		 
		// Path and file name			
		$path_array  = wp_upload_dir();	
		
		$pathAndName= $path_array['path'].'/' . $fileName;
		
		$moveResult = move_uploaded_file($fileTmpLoc, $pathAndName);
	
		// Evaluate the value returned from the function if needed
		if ($moveResult == true) 
		{		
			$imagepath = 	$path_array['subdir'].'/' . $fileName;	
			update_option( 'imagepath',$imagepath);	
			header("Location: $redirectpath");
		
		}else{
		
			header("Location: $redirectpath");
		}
	}
?>