<?php
/*
Plugin Name: Reading List
Plugin URI: http://allmybooks.me
Description: This just creates a database table... so far.
Author: Garrett Vorbeck
Version: 1
Author URI: http://iamgarrett.com
Text Domain: reading-list
*/

/*
	Reading List Copyright (C) 2014  Joseph Garrett Vorbeck  (email : me+readinglistplugin@gmail.com)
	
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation in the Version 2.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
*/

register_activation_hook( __FILE__, 'reading_list_install' );
//UNCOMMENT THE BELOW LINE AND ITS CORRESPONDING FUNCTION TO TEST INSTALL DATA
//register_activation_hook( __FILE__, 'reading_list_install_data' );

global $reading_list_db_version;
$reading_list_db_version = "1.0";

function reading_list_install() {
   global $wpdb;
   global $reading_list_db_version;

   $table_name = $wpdb->prefix . "reading_list";
      
   $sql = "CREATE TABLE $table_name (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
  bid bigint(20) NOT NULL,
  listorder bigint(20) NOT NULL,
  UNIQUE KEY id (id)
    );";

   require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
   dbDelta( $sql );
 
   add_option( "reading_list_db_version", $reading_list_db_version );
}

/*function reading_list_install_data() {
   global $wpdb;
   $welcome_name = "Mr. WordPress";
   $welcome_bid = 1751;
   $table_name = $wpdb->prefix . "reading_list";
   $rows_affected = $wpdb->insert( $table_name, array( 'time' => current_time('mysql'), 'bid' => $welcome_bid ) );
}*/
