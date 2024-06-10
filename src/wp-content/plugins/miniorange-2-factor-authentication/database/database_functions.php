<?php

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	class MoWpnsDB
	{
		private $transactionTable;
		private $blockedIPsTable;
		private $whitelistIPsTable;
		private $emailAuditTable;
		private $malwarereportTable;
		private $scanreportdetails;
		private $skipfiles;
		private $hashfile;

		function __construct()
		{
			global $wpdb;
			$this->transactionTable		= $wpdb->base_prefix.'mo2f_network_transactions';
			$this->blockedIPsTable 		= $wpdb->base_prefix.'mo2f_network_blocked_ips';
			$this->attackList			= $wpdb->base_prefix.'wpns_attack_logs';
			$this->whitelistIPsTable	= $wpdb->base_prefix.'mo2f_network_whitelisted_ips';
			$this->emailAuditTable		= $wpdb->base_prefix.'mo2f_network_email_sent_audit';
			$this->IPrateDetails 		= $wpdb->base_prefix.'wpns_ip_rate_details';
			$this->malwarereportTable	= $wpdb->base_prefix.'wpns_malware_scan_report';
			$this->scanreportdetails	= $wpdb->base_prefix.'wpns_malware_scan_report_details';
			$this->skipfiles			= $wpdb->base_prefix.'wpns_malware_skip_files';
			$this->hashfile 			= $wpdb->base_prefix.'wpns_malware_hash_file';
			$this->filescan 			= $wpdb->base_prefix.'wpns_files_scan';
		}

		function mo_plugin_activate()
		{
			global $wpdb;
			if(!get_option('mo_wpns_dbversion')||get_option('mo_wpns_dbversion')<MoWpnsConstants::DB_VERSION){
				update_option('mo_wpns_dbversion', MoWpnsConstants::DB_VERSION );
				$this->generate_tables();
			} else {
				$current_db_version = get_option('mo_wpns_dbversion');
				if($current_db_version < MoWpnsConstants::DB_VERSION){
					update_option('mo_wpns_dbversion', MoWpnsConstants::DB_VERSION );
					
				}
			}
		}

		function generate_tables(){
			global $wpdb;
			
			$tableName = $this->transactionTable;
			if($wpdb->get_var("show tables like '$tableName'") != $tableName) 
			{
				$sql = "CREATE TABLE " . $tableName . " (
				`id` bigint NOT NULL AUTO_INCREMENT, `ip_address` mediumtext NOT NULL ,  `username` mediumtext NOT NULL ,
				`type` mediumtext NOT NULL , `url` mediumtext NOT NULL , `status` mediumtext NOT NULL , `created_timestamp` int, UNIQUE KEY id (id) );";
				dbDelta($sql);
			}

			$tableName = $this->blockedIPsTable;
			if($wpdb->get_var("show tables like '$tableName'") != $tableName) 
			{
				$sql = "CREATE TABLE " . $tableName . " (
				`id` int NOT NULL AUTO_INCREMENT, `ip_address` mediumtext NOT NULL , `reason` mediumtext, `blocked_for_time` int,
				`created_timestamp` int, UNIQUE KEY id (id) );";
				dbDelta($sql);
			}
			

			$tableName = $this->whitelistIPsTable;
			if($wpdb->get_var("show tables like '$tableName'") != $tableName) 
			{
				$sql = "CREATE TABLE " . $tableName . " (
				`id` int NOT NULL AUTO_INCREMENT, `ip_address` mediumtext NOT NULL , `created_timestamp` int, UNIQUE KEY id (id) );";
				dbDelta($sql);
			}
			
			$result= $wpdb->get_var("SHOW COLUMNS FROM `$tableName` LIKE 'plugin_path'");
			if(is_null($result)){
				$sql = "ALTER TABLE  `$tableName` ADD  `plugin_path` mediumtext AFTER  `created_timestamp` ;";
				$results1 = $wpdb->query($sql);
			}

			$tableName = $this->emailAuditTable;
			if($wpdb->get_var("show tables like '$tableName'") != $tableName) 
			{
				$sql = "CREATE TABLE " . $tableName . " (
				`id` int NOT NULL AUTO_INCREMENT, `ip_address` mediumtext NOT NULL , `username` mediumtext NOT NULL, `reason` mediumtext, `created_timestamp` int, UNIQUE KEY id (id) );";
				dbDelta($sql);
			}
			$tableName = $this->IPrateDetails;
			if($wpdb->get_var("show tables like '$tableName'") != $tableName) 
			{
				$sql = "CREATE TABLE " . $tableName . " (
				ip varchar(20) , time bigint );";
				dbDelta($sql);
			}

			$tableName = $this->attackList;
			if($wpdb->get_var("show tables like '$tableName'") != $tableName) 
			{
				$sql = "create table ". $tableName ." (
						ip varchar(20),
						type varchar(20),
						time bigint,
						input mediumtext );";
				//dbDelta($sql);
				$results = $wpdb->get_results($sql);
				
			}
			$tableName = $this->malwarereportTable;
			if($wpdb->get_var("show tables like '$tableName'") != $tableName)
			{
				$sql = "CREATE TABLE " . $tableName . " (
				`id` bigint NOT NULL AUTO_INCREMENT, `scan_mode` mediumtext NOT NULL, `scanned_folders` mediumtext NOT NULL, `scanned_files` int NOT NULL, `malware_count` int NOT NULL DEFAULT 0, `repo_issues` int NOT NULL DEFAULT 0, `malicious_links` int NOT NULL DEFAULT 0, `repo_key` mediumtext, `net_connection` int, `start_timestamp` int, `completed_timestamp` int, UNIQUE KEY id (id) );";
				dbDelta($sql);
			}
			$result= $wpdb->get_var("SHOW COLUMNS FROM `$tableName` LIKE 'scan_mode'");
			if(is_null($result)){
				$sql = "ALTER TABLE  `$tableName` ADD  `scan_mode` mediumtext AFTER  `id` ;";
				$results1 = $wpdb->query($sql);
				$sql1= "UPDATE $this->malwarereportTable SET `scan_mode`='Custom Scan';";
				$resluts = $wpdb->query($sql1);
			}
			

			$tableName = $this->scanreportdetails;
			if($wpdb->get_var("show tables like '$tableName'") != $tableName)
			{
				$sql = "CREATE TABLE " . $tableName . " (
				`id` bigint NOT NULL AUTO_INCREMENT, `report_id` bigint, `filename` mediumtext NOT NULL, `report` mediumtext NOT NULL ,  `created_timestamp` int, UNIQUE KEY id (id) );";
				dbDelta($sql);
			}

			$tableName = $this->skipfiles;
			if($wpdb->get_var("show tables like '$tableName'") != $tableName)
			{
				$sql = "CREATE TABLE " . $tableName . " (
				`id` bigint NOT NULL AUTO_INCREMENT, `path` mediumtext NOT NULL , `signature` mediumtext, `created_timestamp` int, UNIQUE KEY id (id) );";
				dbDelta($sql);
			}

			$tableName = $this->filescan;
			if($wpdb->get_var("show tables like '$tableName'") != $tableName)
			{
				$sql = "CREATE TABLE " . $tableName . " (
				`id` bigint NOT NULL AUTO_INCREMENT, `path` mediumtext NOT NULL, `name_hash` varchar(45) NOT NULL, `malware_service` int NOT NULL, `repo_check` int NOT NULL, `link_check` int NOT NULL, `repo_key` mediumtext NOT NULL, PRIMARY KEY id (id), UNIQUE KEY name_hash (name_hash) );";
				dbDelta($sql);
			}
			$result= $wpdb->get_var("SHOW COLUMNS FROM `$tableName` LIKE 'repo_key'");
			if(is_null($result)){
				$sql = "ALTER TABLE  `$tableName` ADD  `repo_key` mediumtext AFTER  `link_check` ;";
				$results1 = $wpdb->query($sql);
			}

			$tableName = $this->hashfile;
			if($wpdb->get_var("show tables like '$tableName'") != $tableName)
			{
				$sql = "CREATE TABLE " . $tableName . " (
			 	`id` bigint(20) NOT NULL AUTO_INCREMENT,`file name` varchar(500) NOT NULL,`file hash` mediumtext NOT NULL, `scan_data` mediumtext NOT NULL, PRIMARY KEY (`id`), UNIQUE KEY `id` (`id`), UNIQUE KEY `file name` (`file name`),                    UNIQUE KEY `id_2`(`id`));";
			 	dbDelta($sql);
			}
			
			$row1 = $wpdb->get_results(  "SHOW COLUMNS FROM ".$this->malwarereportTable." LIKE 'malware_count'" );
			$row2 = $wpdb->get_results(  "SHOW COLUMNS FROM ".$this->malwarereportTable." LIKE 'repo_issues'"  );
			$row3 = $wpdb->get_results(  "SHOW COLUMNS FROM ".$this->malwarereportTable." LIKE 'malicious_links'" );
	        if(empty($row1) && empty($row1) && empty($row1)){
	            $result = $wpdb->query("ALTER TABLE $this->malwarereportTable ADD COLUMN `malware_count` INT NOT NULL DEFAULT 0 AFTER `scanned_files`, ADD COLUMN `repo_issues` INT NOT NULL DEFAULT 0 AFTER `malware_count`, ADD COLUMN `malicious_links` INT NOT NULL DEFAULT 0 AFTER `repo_issues`");
	            if($result){
	            	$report_ids = $wpdb->get_results("SELECT id FROM $this->malwarereportTable");
					foreach ($report_ids as $key => $value) {
						$scan_detail = $wpdb->get_results("SELECT report FROM $this->scanreportdetails WHERE report_id='".$report_ids[$key]->id."'");
						$result = $this->mo_wpns_get_scan_count($scan_detail);
						$wpdb->query("UPDATE $this->malwarereportTable SET `malware_count`= '".$result['scan']."', `repo_issues`='".$result['repo']."', `malicious_links`='".$result['extl']."' WHERE id='".$report_ids[$key]->id."'");
					}
	            }
	        }
	        $rowhash = $wpdb->get_results(  "SHOW COLUMNS FROM ".$this->hashfile." LIKE 'scan_data'"  );
	        if(empty($rowhash)){
	        	$result = $wpdb->query("ALTER TABLE $this->hashfile ADD COLUMN `scan_data` mediumtext NOT NULL");
	        }
	        $result= $wpdb->get_results("SHOW COLUMNS FROM ".$this->malwarereportTable." LIKE 'repo_key'");
			if(empty($result)){
				$sql = "ALTER TABLE  $this->malwarereportTable ADD  `repo_key` mediumtext AFTER  `malicious_links` ;";
				$results1 = $wpdb->query($sql);
				$sql1= "UPDATE $this->malwarereportTable SET `repo_key`= NULL;";
				$resluts = $wpdb->query($sql1);
			}
			$result= $wpdb->get_results("SHOW COLUMNS FROM ".$this->malwarereportTable." LIKE 'net_connection'");
			if(empty($result)){
				$sql = "ALTER TABLE $this->malwarereportTable ADD  `net_connection` mediumtext AFTER  `repo_key` ;";
				$results1 = $wpdb->query($sql);
				$sql1= "UPDATE $this->malwarereportTable SET `net_connection`= 0;";
				$resluts = $wpdb->query($sql1);
			}
		}

	     

		
		function get_ip_blocked_count($ipAddress)
		{
			global $wpdb;
			return $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM ".$this->blockedIPsTable." WHERE ip_address = '%s'", array($ipAddress)) );
		}
		function get_total_blocked_ips()
		{
			global $wpdb;
			return $wpdb->get_var( "SELECT COUNT(*) FROM ".$this->blockedIPsTable);
		}
		function get_total_manual_blocked_ips()
		{
			global $wpdb;
			return $wpdb->get_var( "SELECT COUNT(*) FROM ".$this->blockedIPsTable." WHERE reason = 'Blocked by Admin';");
		}
		function get_total_blocked_ips_waf()
		{
			global $wpdb;
			$totalIPBlocked = $wpdb->get_var( "SELECT COUNT(*) FROM ".$this->blockedIPsTable);
			return $totalIPBlocked - $wpdb->get_var( "SELECT COUNT(*) FROM ".$this->blockedIPsTable." WHERE reason = 'Blocked by Admin';");
		}
		function get_blocked_attack_count($attack)
		{
			global $wpdb;
			return $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM ".$this->attackList." WHERE type = '%s'" ,array($attack)));
		}
		
		function get_count_of_blocked_ips(){
			global $wpdb;
			return $wpdb->get_var("SELECT COUNT(*) FROM ".$this->blockedIPsTable); 
		}


		function get_blocked_ip($entryid)
		{
			global $wpdb;
			return $wpdb->get_results( $wpdb->prepare("SELECT ip_address FROM ".$this->blockedIPsTable." WHERE id= %d" , array($entryid)) );
		}

		function get_blocked_ip_list()
		{
			global $wpdb;
			return $wpdb->get_results("SELECT id, reason, ip_address, created_timestamp FROM ".$this->blockedIPsTable);
		}

		function get_blocked_ips_realtime()
		{
			global $wpdb;
			return $wpdb->get_results("SELECT ip_address FROM ".$this->blockedIPsTable);
		}
		function get_blocked_attack_list()
		{
			global $wpdb;
			return $wpdb->get_results("SELECT ip, type, time, input FROM ".$this->attackList);
		}

		function get_blocked_sqli_list()
		{
			global $wpdb;
			return $wpdb->get_results("SELECT ip, type, time, input FROM ".$this->attackList."WHERE type='SQL attack'");
		}
		function get_blocked_rfi_list()
		{
			global $wpdb;
			return $wpdb->get_results("SELECT ip, type, time, input FROM ".$this->attackList."WHERE type='RFI attack'");
		}
		function get_blocked_lfi_list()
		{
			global $wpdb;
			return $wpdb->get_results("SELECT ip, type, time, input FROM ".$this->attackList."WHERE type='LFI attack'");
		}
		function get_blocked_rce_list()
		{
			global $wpdb;
			return $wpdb->get_results("SELECT ip, type, time, input FROM ".$this->attackList."WHERE type='RCE attack'");
		}
		function get_blocked_xss_list()
		{
			global $wpdb;
			return $wpdb->get_results("SELECT ip, type, time, input FROM ".$this->attackList."WHERE type='XSS attack'");
		}

		function insert_blocked_ip($ipAddress,$reason,$blocked_for_time)
		{
			global $wpdb;
			$wpdb->insert( 
				$this->blockedIPsTable, 
				array( 
					'ip_address' => $ipAddress, 
					'reason' => $reason,
					'blocked_for_time' => $blocked_for_time,
					'created_timestamp' => current_time( 'timestamp' )
				),
				array('%s','%s','%d','%d')
			);
			return;
		}

		function delete_blocked_ip($entryid)
		{
			global $wpdb;
			$wpdb->query( 
				$wpdb->prepare("DELETE FROM ".$this->blockedIPsTable." WHERE id = %d",array($entryid))
			);
			return;
		}

		function get_whitelisted_ip_count($ipAddress)
		{
			global $wpdb;
			return $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM ".$this->whitelistIPsTable." WHERE ip_address = %s",array($ipAddress)) );
		}

		function insert_whitelisted_ip($ipAddress)
		{
			global $wpdb;
			$wpdb->insert( 
				$this->whitelistIPsTable, 
				array( 
					'ip_address' => $ipAddress, 
					'created_timestamp' => current_time( 'timestamp' )
				),
				array('%s','%d')
			);
		}

		function get_number_of_whitelisted_ips(){
			global $wpdb;
			return $wpdb->get_var("SELECT COUNT(*) FROM ".$this->whitelistIPsTable."");
		}

		function delete_whitelisted_ip($entryid)
		{
			global $wpdb;
			$wpdb->query( 
				$wpdb->prepare("DELETE FROM ".$this->whitelistIPsTable." WHERE id = %d",array($entryid))
			);
			return;
		}

		function get_whitelisted_ips_list()
		{
			global $wpdb;
			return $wpdb->get_results( "SELECT id, ip_address, created_timestamp FROM ".$this->whitelistIPsTable );
		}

		function get_email_audit_count($ipAddress,$username)
		{
			global $wpdb;
			return $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM ".$this->emailAuditTable." WHERE ip_address = '%s' AND username= '%s'",array($ipAddress,$username)) );
		}

		function insert_email_audit($ipAddress,$username,$reason)
		{
			global $wpdb;
			$wpdb->insert( 
				$this->emailAuditTable, 
				array( 
					'ip_address' => $ipAddress,
					'username' => $username,
					'reason' => $reason,
					'created_timestamp' => current_time( 'timestamp' )
				),
				array('%s','%s','%s','%d')
			);
			return;
		}

		function insert_transaction_audit($ipAddress,$username,$type,$status,$url=null)
		{
			global $wpdb;
			$data 		= array( 
							'ip_address' 		=> $ipAddress, 
							'username' 	 		=> $username,
							'type' 		 		=> $type,
							'status' 	 		=> $status,
							'created_timestamp' => current_time( 'timestamp' )
						);
			$format = array('%s','%s','%s','%s','%d');
			$data['url'] = is_null($url) ? '' : $url;  
			$url = sanitize_url($url);
			$wpdb->insert(  $this->transactionTable, $data , $format );
			return;
		}

		function get_transasction_list()
		{
			global $wpdb;
			return $wpdb->get_results( "SELECT ip_address, username, type, status, created_timestamp FROM ".$this->transactionTable." order by id desc limit 5000" );
		}

		function get_login_transaction_report()
		{
			global $wpdb;
			return $wpdb->get_results( "SELECT ip_address, username, status, created_timestamp FROM ".$this->transactionTable." WHERE type='User Login' order by id desc limit 5000" );
		}

		function get_error_transaction_report()
		{
			global $wpdb;
			return $wpdb->get_results( "SELECT ip_address, username, url, type, created_timestamp FROM ".$this->transactionTable." WHERE type <> 'User Login' order by id desc limit 5000" );
		}

		function update_transaction_table($where,$update)
		{
			global $wpdb;

			$sql = "UPDATE ".$this->transactionTable." SET ";
			$i = 0;
			foreach($update as $key=>$value)
			{
				if($i%2!=0)
					$sql .= ' , ';
				if($key == 'created_timestamp' || $key == 'id'){
					$sql .= $wpdb->prepare("%1s = '%d'",array($key,$value));
				}else{
					$sql .= $wpdb->prepare("%1s = '%s'",array($key,$value));
				}
				$i++;
			}
			$sql .= " WHERE ";
			$i = 0;
			foreach($where as $key=>$value)
			{
				if($i%2!=0)
					$sql .= ' AND ';
					if($key == 'created_timestamp' || $key == 'id'){
						$sql .= $wpdb->prepare("%1s = '%d'",array($key,$value));
					}else{
						$sql .= $wpdb->prepare("%1s = '%s'",array($key,$value));
					}
				$i++;
			}
			
			$wpdb->query($sql);
			return;
		}

		function get_count_of_attacks_blocked(){
			global $wpdb;
			return $wpdb->get_var( "SELECT COUNT(*) FROM ".$this->transactionTable." WHERE status = '".MoWpnsConstants::FAILED."' OR status = '".MoWpnsConstants::PAST_FAILED."'" );
		}

		function get_failed_transaction_count($ipAddress)
		{
			global $wpdb;
			return $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM ".$this->transactionTable." WHERE ip_address = '%s' AND status = '".MoWpnsConstants::FAILED."'", array($ipAddress)) );
		}

		function delete_transaction($ipAddress)
		{
			global $wpdb;
			$wpdb->query( 
				$wpdb->prepare("DELETE FROM ".$this->transactionTable." WHERE ip_address = '%s' AND status='".MoWpnsConstants::FAILED."'",array($ipAddress))
			);
			return;
		}

		function create_scan_report($folderNames, $scan_type, $start_timestamp, $repo_check_status_code){
			global $wpdb;
			$wpdb->insert( 
				$this->malwarereportTable, 
				array( 
					'scan_mode' => $scan_type,
					'scanned_folders' => $folderNames,
					'scanned_files' => 0,
					'start_timestamp' => $start_timestamp,
					'malware_count' => 0,
					'repo_issues' => $repo_check_status_code,
					'malicious_links' => 0
				),
				array('%s','%s','%d','%d','%d','%d','%d')
			);
			$result = $wpdb->get_results( "SELECT * FROM ".$this->malwarereportTable." order by id DESC LIMIT 1");
			if($result){
				$record = $result[0];
				return $record->id;
			}
		}

		function mo2f_update_net_issue($reportid){
			global $wpdb;
			$wpdb->update(
				$this->malwarereportTable,
				array(
					'net_connection' => 1
				),
				array(
					'id' => $reportid
				),
				array('%d'),
				array('%d')
			);
		}

		function mo2f_update_repo_issue($reportid, $issue){
			global $wpdb;
			$wpdb->update(
				$this->malwarereportTable,
				array(
					'repo_key' => $issue
				),
				array(
					'id' => $reportid
				),
				array('%s'),
				array('%d')
			);
		}

		function add_report_details($reportid, $filename, $report){
			global $wpdb;
			$wpdb->insert( 
				$this->scanreportdetails, 
				array( 
					'report_id' => $reportid,
					'filename' => $filename,
					'report' => serialize($report),
					'created_timestamp' => current_time('timestamp')
				),
				array('%d','%s','%s','%d')
			);
		}

		function scan_report_complete($recordId, $no_of_scanned_files, $malware_count, $repo_issues, $malicious_links){
			global $wpdb;
			$wpdb->query( 
				$wpdb->prepare("UPDATE ".$this->malwarereportTable." set completed_timestamp = ".current_time('timestamp').", scanned_files= %d , malware_count= %d , repo_issues= %d , malicious_links= %d WHERE id = %d",array($no_of_scanned_files,$malware_count,$repo_issues,$malicious_links,$recordId))
			);
		}

		function count_files(){
			global $wpdb;
			$sql= $wpdb->get_results("SELECT SUM(`scanned_files`) AS scan_count FROM ".$this->malwarereportTable);
			return $sql[0]->scan_count;
		}

		function count_malicious_files(){
			global $wpdb;
			$sql= $wpdb->get_results("SELECT COUNT(*) AS total_mal FROM ".$this->scanreportdetails);
			return $sql[0]->total_mal;
		}

		function count_files_last_scan($reportid){
			global $wpdb;
			$sql= $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$this->malwarereportTable.' WHERE `id`= %d',array($reportid)));
			return $sql[0]->scanned_files;
		}

		function count_malicious_last_scan($reportid){
			global $wpdb;
			$sql= $wpdb->get_results($wpdb->prepare('SELECT COUNT(*) AS mal_file FROM '.$this->scanreportdetails.' WHERE `report_id`= %d',array($reportid)));
			return $sql[0]->mal_file;
		}

		function check_hash($hash_of_file){
			global $wpdb;
			$sql= $wpdb->prepare("SELECT * FROM ".$this->hashfile." WHERE `file hash`= '%s'",array($hash_of_file));
			$result=$wpdb->get_results( $sql );
			return $result;
		}

		function insert_hash($source_file_path,$hash_of_file, $scan_data){
			global $wpdb;
			$source_file_path = addslashes($source_file_path);
			$query= $wpdb->prepare("INSERT INTO ".$this->hashfile."(`file name`,`file hash`,`scan_data`) VALUES('%s', '%s', '%s') ON DUPLICATE KEY UPDATE `file hash`='%s' AND `scan_data`='%s'",array($source_file_path,$hash_of_file,serialize($scan_data),$hashfile,serialize($scan_data)));
			$res=$wpdb->query( $query );
		}

		function update_hash($source_file_path, $hash_of_file, $scan_data){
			global $wpdb;
			$source_file_path = addslashes($source_file_path);
			$query= $wpdb->prepare("UPDATE ".$this->hashfile." SET `file hash`='%s',`scan_data`='%s' WHERE `file name`='%s'",array($hash_of_file,serialize($scan_data),$source_file_path));
			$res=$wpdb->query( $query );
		}

		function delete_hash($source_file_path){
			global $wpdb;
			$query= $wpdb->prepare("DELETE FROM ".$this->hashfile." WHERE `file name` = '%s'",array($source_file_path));
			$res=$wpdb->query( $query );
		}

		function get_infected_file($filename){
			global $wpdb;
			$filename = addslashes($filename);
			$result = $wpdb->get_results( $wpdb->prepare("SELECT * FROM ".$this->scanreportdetails." where filename= %s",array($filename)) );
			return $result;
		}

		function insert_files_in_parts($file_path_array){
			global $wpdb;
			if(!empty($file_path_array)){
				$size=sizeof($file_path_array);
				$default=0;
				$query="INSERT INTO ".$this->filescan."(`path`, `name_hash`, `malware_service`, `repo_check`, `link_check`, `repo_key`) VALUES";
				for ($i=1; $i <= $size ; $i++) { 
					$value= $file_path_array[$i];
					$file_path = $value['file'];
					$file_path = addslashes($file_path);
					$hash_value= md5($file_path);
					$repo_key = $value['key'];
					$query.= $wpdb->prepare("('%s', '%s', '%d', '%d', '%d', '%s')",array($file_path,$hash_value,$default,$default,$default,$repo_key));
					if($i < $size){
						$query.= ",";
					}
				}
				$query.=";";
				$res=$wpdb->query( $query );
			}
		}

		function update_files_scan($file_path_array, $file_count){
			global $wpdb;
			$query="INSERT INTO ".$this->filescan."(`name_hash`, `malware_service`) VALUES";
			for ($i=0; $i < $file_count ; $i++) { 
				$value= $file_path_array[$i]->path;
				$value = addslashes($value);
				$value = md5($value);
				$query.= "('".$value."', 1)";
				if($i < $file_count-1){
					$query.= ",";
				}
			}
			$query.=" ON DUPLICATE KEY UPDATE `malware_service`= VALUES(malware_service);";
			$res=$wpdb->query( $query );
		}

		function update_files_scan_ext_link($file_path_array, $file_count){
			global $wpdb;
			$query="INSERT INTO ".$this->filescan."(`name_hash`, `link_check`) VALUES";
			for ($i=0; $i < $file_count ; $i++) { 
				$value= $file_path_array[$i]->path;
				$value = addslashes($value);
				$value = md5($value);
				$query.= $wpdb->prepare("('%s', 1)",array($value));
				if($i < $file_count-1){
					$query.= ",";
				}
			}
			$query.=" ON DUPLICATE KEY UPDATE `link_check`= VALUES(link_check);";
			$res=$wpdb->query( $query );
		}

		function update_files_scan_repo($file_path_array, $file_count){
			global $wpdb;
			$query="INSERT INTO ".$this->filescan."(`name_hash`, `repo_check`) VALUES";
			for ($i=0; $i < $file_count ; $i++) { 
				$value= $file_path_array[$i]->path;
				$value = addslashes($value);
				$value = md5($value);
				$query.= $wpdb->prepare("('%s', 1)",array($value));
				if($i < $file_count-1){
					$query.= ",";
				}
			}
			$query.=" ON DUPLICATE KEY UPDATE `repo_check`= VALUES(repo_check);";
			$res=$wpdb->query( $query );
		}

		function get_files_in_parts(){
			global $wpdb;
			$sql= 'SELECT * FROM '.$this->filescan.' WHERE `malware_service`= 0 LIMIT 100';
			$result=$wpdb->get_results($sql);
			return $result;
		}

		function get_files_for_link(){
			global $wpdb;
			$sql= 'SELECT * FROM '.$this->filescan.' WHERE `link_check`= 0 LIMIT 100';
			$result=$wpdb->get_results($sql);
			return $result;
		}

		function get_files_for_repo($repo_key){
			global $wpdb;
			$sql= $wpdb->prepare('SELECT * FROM '.$this->filescan.' WHERE `repo_check`= 0 AND `repo_key`= "%s" LIMIT 100',array($repo_key));
			$result=$wpdb->get_results($sql);
			return $result;
		}

		function delete_files_parts(){
			global $wpdb;
			$sql= "TRUNCATE TABLE ".$this->filescan.";";
			$res=$wpdb->query($sql);
		}

		function get_last_id(){
			global $wpdb;
			$result= $wpdb->get_results("SELECT MAX(Id) AS max FROM ".$this->malwarereportTable);
			return $result;
		}

		function get_report_with_id($reportid){
			global $wpdb;
			$result = $wpdb->get_results( $wpdb->prepare("SELECT * FROM ".$this->malwarereportTable." where id= %d",array($reportid)) );
			return $result;
		}

		function delete_report($reportid){
			global $wpdb;
			$wpdb->query( 
				$wpdb->prepare("DELETE FROM ".$this->malwarereportTable." WHERE id =  %d",array($reportid))
			);
			$warning_count=0;
			$malware_count=0;
			$last_id=$this->get_last_id();
			$send_id=$last_id[0]->max;
			if(!is_null($send_id)){
				$res = $this->get_report_with_id($send_id);
				$record = $res[0];
				if($record->malware_count >= 0){
					$malware_count = $record->malware_count;
				}
				if($record->repo_issues < 0){
					$warning_count = $record->malicious_links;
				}else{
					$warning_count = $record->repo_issues + $record->malicious_links;
				}
			}

			update_option('mo_wpns_infected_files', $malware_count);
			update_option('mo_wpns_warning_files', $warning_count);
			
		}

		function get_report(){
			global $wpdb;
			$result = $wpdb->get_results( "SELECT * FROM ".$this->malwarereportTable." order by id desc" );
			return $result;
		}

		function get_vulnerable_files_count_for_reportid($reportid){
			global $wpdb;
			$result = $wpdb->get_results( $wpdb->prepare("SELECT count(*) as  count FROM ".$this->scanreportdetails." where report_id= %d", array($reportid)) );
			return $result;
		}

		function ignorefile($filename){
			$signature = md5_file($filename);
			global $wpdb;
			$result = $wpdb->get_results( $wpdb->prepare("SELECT * FROM ".$this->skipfiles." where path = '%s'",array($filename)) );
			if($result){
				$wpdb->query( 
					$wpdb->prepare("UPDATE ".$this->skipfiles." SET signature = '%s' WHERE path = '%s'",array($signature,$filename))
				);
			} else {
				$wpdb->insert(
					$this->skipfiles, 
					array( 
						'path' => $filename,
						'signature' => $signature,
						'created_timestamp' => current_time('timestamp')
					),
					array('%s','%s','%d')
				);
			}
		}

		function ignorechangedfile($recordId){
			global $wpdb;
			$result = $wpdb->get_results( $wpdb->prepare("SELECT * FROM ".$this->skipfiles." where id = %d ",array($recordId)) );
			if($result){
				$record = $result[0];
				$signature = md5_file($record->path);
				$wpdb->query( 
					$wpdb->prepare("UPDATE ".$this->skipfiles." set signature = '%s' WHERE id = %d",array($signature,$recordId))
				);
			}
		}

		function getlistofignorefiles(){
			global $wpdb;
			$result = $wpdb->get_results( "SELECT * FROM ".$this->skipfiles."" );
			return $result;
		}

		function get_detail_report_with_id($reportid){
			global $wpdb;
			$result = $wpdb->get_results( $wpdb->prepare("SELECT * FROM ".$this->scanreportdetails." where report_id= %d" ,array($reportid)) );
			return $result;
		}

		function mo_wpns_upgrade_process_complete(){
			$current_db_version = get_option('mo_wpns_dbversion');
			if($current_db_version < MoWpnsConstants::DB_VERSION){
				update_option('mo_wpns_dbversion', MoWpnsConstants::DB_VERSION );
				$row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '".$this->malwarereportTable."' AND column_name = 'malware_count' AND column_name='repo_issues' AND column_name='malicious_links'"  );
		        if(empty($row)){
		            $result = $wpdb->query("ALTER TABLE $this->malwarereportTable ADD COLUMN `malware_count` INT NOT NULL DEFAULT 0 AFTER `scanned_files`, ADD COLUMN `repo_issues` INT NOT NULL DEFAULT 0 AFTER `malware_count`, ADD COLUMN `malicious_links` INT NOT NULL DEFAULT 0 AFTER `repo_issues`");
		            if($result){
		            	$report_ids = $wpdb->get_results("SELECT id FROM $this->malwarereportTable");
						foreach ($report_ids as $key => $value) {
							$scan_detail = $wpdb->get_results("SELECT report FROM $this->scanreportdetails WHERE report_id='".$report_ids[$key]->id."'");
							$result = $this->mo_wpns_get_scan_count($scan_detail);
							$wpdb->query("UPDATE $this->malwarereportTable SET 'malware_count'= '".$result['scan']."', `repo_issues`='".$result['repo']."', `malicious_links`='".$result['extl']."' WHERE id='".$report_ids[$key]->id."'");
						}
		            }
		        }
		        $rowhash = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '".$this->hashfile."' AND column_name = 'scan_data'"  );
		        if(empty($rowhash)){
		        	$result = $wpdb->query("ALTER TABLE $this->hashfile ADD COLUMN `scan_data` mediumtext NOT NULL");
		        }
			}
		}

		function mo_wpns_get_scan_count($result){
			$scan_count = 0;
			$repo_count = 0;
			$link_count = 0;
			$total = 0;
			foreach ($result as $key => $value) {
				$total+=1;
				$temp = unserialize($result[$key]->report);
				if(isset($temp['scan'])&&isset($temp['repo'])&&isset($temp['extl'])){
					$scan_count++;
					$repo_count++;
					$link_count++;
				}else if(isset($temp['scan'])&&isset($temp['repo'])){
					$scan_count++;
					$repo_count++;
				}else if(isset($temp['scan'])&&isset($temp['extl'])){
					$scan_count++;
					$link_count++;
				}else if(isset($temp['repo'])&&isset($temp['extl'])){
					$repo_count++;
					$link_count++;
				}else if(isset($temp['scan'])){
					$scan_count++;
				}else if(isset($temp['repo'])){
					$repo_count++;
				}else if(isset($temp['extl'])){
					$link_count++;
				}
			}
			return array('scan'=>$scan_count, 'repo'=>$repo_count, 'extl'=>$link_count);
		}
	}