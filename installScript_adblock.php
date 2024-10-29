<?php
global $wpdb;
$table_name = $wpdb->prefix . "adblock";
$sql = "CREATE TABLE $table_name (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(20) NOT NULL,
  `usertime` int(16) NOT NULL,
  `userloginid` int(11) NOT NULL,
  `usercountry` varchar(50) NOT NULL,
  `adblock_status` varchar(3) NOT NULL,  
   PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;";
$wpdb->query($sql);

add_option( 'usermsg', 'Please disable any ad-blocker you are using in your browser.' );
add_option( 'imagepath', 'images/large.png' );

?>
