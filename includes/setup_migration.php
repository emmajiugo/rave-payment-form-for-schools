<?php
class Kkd_Pff_Rave_Setup_Migration {

	public static function activate() {
		global $wpdb;

		//different table
		$table_name = $wpdb->prefix . KKD_PFF_RAVE_TABLE;
		
		$sql = "CREATE TABLE IF NOT EXISTS  `".$table_name."` (
			id int(11) NOT NULL AUTO_INCREMENT,
			post_id int(11) NOT NULL,
		  user_id int(11) NOT NULL,
			email varchar(255) DEFAULT '' NOT NULL,
			currency varchar(255) DEFAULT 'NGN' NOT NULL,
		  metadata text,
		  paid int(1) NOT NULL DEFAULT '0',
		  recur int(1) NOT NULL DEFAULT '0',
		  mode varchar(255) DEFAULT 'sandbox' NOT NULL,
			plan varchar(255) DEFAULT '' NOT NULL,
  		autorenewal int(1) NOT NULL DEFAULT '0',
			reference varchar(255) DEFAULT '' NOT NULL,
			param varchar(255) DEFAULT '' NOT NULL,
			flw_reference varchar(255) DEFAULT '' NOT NULL,
		  amount varchar(255) DEFAULT '' NOT NULL,
		  ip varchar(255) NOT NULL,
			deleted_at varchar(255) DEFAULT '' NULL,
			created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  modified timestamp DEFAULT '0000-00-00 00:00:00' NOT NULL,
		  UNIQUE KEY id (id),PRIMARY KEY  (id)
		);";		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta($sql);

		//different table
		$table_name = $wpdb->prefix . KKD_PFF_RAVE_TABLE_VALIDATION;
		
		$sql = "CREATE TABLE IF NOT EXISTS  `".$table_name."` (
			id int(11) NOT NULL AUTO_INCREMENT,
			param varchar(255) DEFAULT '' NOT NULL,
			value varchar(255) DEFAULT '' NULL NULL,
		  metadata text,
		  amount varchar(255) DEFAULT NULL NULL,
		  deleted_at varchar(255) DEFAULT '' NULL,
			created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  modified timestamp DEFAULT '0000-00-00 00:00:00' NOT NULL,
		  UNIQUE KEY id (id),PRIMARY KEY  (id)
		);";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta($sql);

		// table cobtaining student details
		$table_name = $wpdb->prefix . KKD_PFF_RAVE_STUDENT_TABLE;
		
		$sql = "CREATE TABLE IF NOT EXISTS  `".$table_name."` (
			id int(11) NOT NULL AUTO_INCREMENT,
		  student_id varchar(255) DEFAULT '' NOT NULL,
		  student_name varchar(255) DEFAULT '' NOT NULL,
			student_class varchar(255) DEFAULT '' NOT NULL,
			created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  modified timestamp DEFAULT '0000-00-00 00:00:00' NOT NULL,
		  UNIQUE KEY id (id),PRIMARY KEY  (id)
		);";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta($sql);

	}



}
