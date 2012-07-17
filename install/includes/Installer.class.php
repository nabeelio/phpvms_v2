<?php
/**
 * phpVMS - Virtual Airline Administration Software
 * Copyright (c) 2008 Nabeel Shahzad
 * For more information, visit www.phpvms.net
 *	Forums: http://www.phpvms.net/forum
 *	Documentation: http://www.phpvms.net/docs
 *
 * phpVMS is licenced under the following license:
 *   Creative Commons Attribution Non-commercial Share Alike (by-nc-sa)
 *   View license.txt in the root, or visit http://creativecommons.org/licenses/by-nc-sa/3.0/
 *
 * @author Nabeel Shahzad
 * @copyright Copyright (c) 2008, Nabeel Shahzad
 * @link http://www.phpvms.net
 * @license http://creativecommons.org/licenses/by-nc-sa/3.0/
 */

class Installer
{
	
	static $error;
	
	/**
	 * Installer::CheckServer()
	 * 
	 * @return
	 */
	public static function CheckServer() {
	   
		$noerror = true;
		$version = phpversion();
		$wf = array();
		
		// These needa be writable
        $wf[] = 'core/cache';
		$wf[] = 'core/logs';
		$wf[] = 'core/pages';
		$wf[] = 'lib/avatars';
		$wf[] = 'lib/rss';
		$wf[] = 'lib/signatures';
		
		// Check the PHP version
		if($version[0] != '5') {
			$noerror = false;
			$type = 'error';
			$message = 'You need PHP 5 (your version: '.$version.')';
		} else {
			$type = 'success';
			$message = 'OK! (your version:'.$version.')';
		}
		
		Template::Set('phpversion', '<div id="'.$type.'">'.$message.'</div>');
		
		// Check if core/site_config.inc.php is writeable
		if(!file_exists(CORE_PATH .'/local.config.php')) {
		  
			if(!$fp = fopen(CORE_PATH .'/local.config.php', 'w')) {
				$noerror = false;
				$type = 'error';
				$message = 'Could not create core/local.config.php. Create this file, blank, with write permissions.';
			} else {
				$type = 'success';
				$message = 'core/local.config.php is writeable!';
			}
            
		} else {
		  
			if(!is_writeable(CORE_PATH .'/local.config.php')) {
				$noerror = false;
				$type = 'error';
				$message = 'core/local.config.php is not writeable';
			} else {
				$type = 'success';
				$message = 'core/local.config.php is writeable!';
			}
		}
		
		Template::Set('configfile', '<div id="'.$type.'">'.$message.'</div>');
		
		// Check all of the folders for writable permissions
		$status = '';
		foreach($wf as $folder)	{
		  
			if(!is_writeable(SITE_ROOT.'/'.$folder)) {
				$noerror = false;
				$type = 'error';
				$message = $folder.' is not writeable';
			} else {
				$type = 'success';
				$message = $folder.' is writeable!';
			}
			
			$status.='<div id="'.$type.'">'.$message.'</div>';
		}
		
		Template::Set('directories', $status);
		//Template::Set('pagesdir', '<div id="'.$type.'">'.$message.'</div>');
		
		return $noerror;
	}
	
	/**
	 * Installer::WriteConfig()
	 * 
	 * @return
	 */
	public static function WriteConfig() {
	   
		$tpl = file_get_contents(SITE_ROOT . '/install/templates/config.php');
		
		$tpl = str_replace('$DBASE_USER', $_POST['DBASE_USER'], $tpl);
		$tpl = str_replace('$DBASE_PASS', $_POST['DBASE_PASS'], $tpl);
		$tpl = str_replace('$DBASE_NAME', $_POST['DBASE_NAME'], $tpl);
		$tpl = str_replace('$DBASE_SERVER', $_POST['DBASE_SERVER'], $tpl);
		$tpl = str_replace('$DBASE_TYPE', $_POST['DBASE_TYPE'], $tpl);
		$tpl = str_replace('$TABLE_PREFIX', $_POST['TABLE_PREFIX'], $tpl);
		$tpl = str_replace('$SITE_URL', $_POST['SITE_URL'], $tpl);
		
		$fp = fopen(CORE_PATH .'/local.config.php', 'w');
		
		if(!$fp) {
			self::$error = 'There was an error opening local.config.php. Please check your permissions';
			return false;
		}
		
		fwrite($fp, $tpl, strlen($tpl));
	
		fclose($fp);
		
		return true;
	}
	
	/**
	 * Write all of the SQL tables to the database
	 * 
	 * @return
	 */
	public static function AddTables() {
		
		if(!DB::init($_POST['DBASE_TYPE'])) {
			self::$error = DB::$error;
			return false;
		}
        
        DB::set_caching(false);
		
		$ret = DB::connect($_POST['DBASE_USER'], $_POST['DBASE_PASS'], $_POST['DBASE_NAME'], $_POST['DBASE_SERVER']);
		
		if($ret == false) {
			self::$error = DB::$error;
			return false;
		}
	
		if(!DB::select($_POST['DBASE_NAME'])) {
			self::$error = DB::$error;
			return false;
		}
		
		DB::$throw_exceptions = false;
		
        echo '<h2>Writing Tables...</h2>';
        
		$sqlLines = self::readSQLFile(SITE_ROOT.'/install/sql/install.sql', $_POST['TABLE_PREFIX']);	
		
		foreach($sqlLines as $sql) {
		
			DB::query($sql['sql']);
						            
			if(DB::errno() != 0) {
			     
				#echo 'failed - manually run this query: <br /><br />"'.$sql.'"';
                echo '<div id="error" style="text-align: left;">Writing "'.$sql['table'].'" table... ';
                echo "<br /><br />".DB::error();
                echo '</div>';
            } 
		}
        
        echo "Wrote {$totalTables} tables<br />";
        
        echo '<h2>Populating Initial Data...</h2>';
        $sqlLines = self::readSQLFile(SITE_ROOT.'/install/fixtures/install.sql', $_POST['TABLE_PREFIX']);
        foreach($sqlLines as $sql) {
            DB::query($sql['sql']);
            if(DB::errno() != 0) {
                
                echo '<div id="error" style="text-align: left;">Writing to "'.$sql['table'].'" table... ';
                echo "<br /><br />".DB::error();
                echo '</div>';
            }
        }
		
		return true;
	}
	
    /**
     * Return all the SQL Queries from a file, return as array
     * 
     * @param mixed $file_name
     * @return void
     */
    public static function readSQLFile($file_name, $table_prefix = '') {
   	    
        $sqlLines = array();
        
        $sql = '';
        $sql_file = file($file_name);
        $revision = file_get_contents(SITE_ROOT.'/core/version');
        
        
        foreach($sql_file as $sql_line) {
                        
            $sql .= trim($sql_line);
                        
            if(substr_count($sql, ';') > 0) {
                
                $sql = trim($sql);
                
                # See if it's a comment?
                if($sql[0] == '-' && $sql[1] == '-') {
                    $sql = '';
                    continue;
                }
                
                if($sql == '') {
                    continue;
                }
                
                $sql = str_replace('phpvms_', $table_prefix, $sql);
            	
            	preg_match("/`{$table_prefix}([A-Za-z]*)`/", $sql, $matches);
            	$tablename = $matches[1];
                
                $sqlLines[] = array(
                    'tablename' => $tablename,
                    'sql' => $sql
                );
                
                $sql = '';
            }
        }
        
        return $sqlLines;
    }
    
	/**
	 * Installer::SiteSetup()
	 * 
	 * @return
	 */
	public static function SiteSetup() {
	   
		/*$_POST['SITE_NAME'] == '' || $_POST['firstname'] == '' || $_POST['lastname'] == ''
					|| $_POST['email'] == '' ||  $_POST['password'] == '' || $_POST['vaname'] == ''
					|| $_POST['vacode'] == ''*/
					
		// first add the airline
		
		$_POST['vacode'] = strtoupper($_POST['vacode']);
		if(!OperationsData::addAirline($_POST['vacode'], $_POST['vaname']))	{
			self::$error = __FILE__.' '.__LINE__.' '.DB::$error;
			return false;
		}
					
		// Add the user
		$data = array(
			'firstname' => $_POST['firstname'],
			'lastname' => $_POST['lastname'],
			'email' => $_POST['email'],l,
			'password' => $_POST['password'],
			'code' => $_POST['vacode'],
			'location' => 'US',
			'hub' => 'KJFK',
			'confirm' => true
		);
		
		if(!RegistrationData::addUser($data)) {
			self::$error = __FILE__.' '.__LINE__.' '.DB::$error;
			return false;
		}
		
		RanksData::calculatePilotRanks();
		
		# Add to admin group
		$pilotdata = PilotData::getPilotByEmail($_POST['email']);
		if(!PilotGroups::addUsertoGroup($pilotdata->pilotid, 'Administrators')) {
			self::$error = __FILE__.' '.__LINE__.' '.DB::$error;
			return false;
		}
		
		# Add the final settings in
		SettingsData::SaveSetting('SITE_NAME', $_POST['SITE_NAME']);
		SettingsData::SaveSetting('ADMIN_EMAIL', $_POST['email']);
		SettingsData::SaveSetting('GOOGLE_KEY', $_POST['googlekey']);
		
		return true;
		
	}
	
	/**
	 * Installer::sql_file_update()
	 * 
	 * @param mixed $filename
	 * @return
	 */
	public static function sql_file_update($filename) {
	   
		if(isset($_GET['test']))
			return true;
			
		# Table changes, other SQL updates
        $sqlLines = self::readSQLFile($filename);
                
        foreach($sqlLines as $table => $sql) {
            $sql = str_replace('phpvms_', TABLE_PREFIX, $sql);
            DB::query($sql['sql']);
        }
        		
	}
	
	/**
	 * Add an entry into the local.config.php file
	 * 
	 * @param mixed $name
	 * @param mixed $value
	 * @param string $comment
	 * @return
	 */
	public static function add_to_config($name, $value, $comment='') {
	   
		if(isset($_GET['test']))
			return true;
			
		$config = file_get_contents(CORE_PATH.'/local.config.php');
		
		# Replace the closing PHP tag, don't need a closing tag
		$config = str_replace('?>', '', $config);
		
		# If it exists, don't add it
		if(strpos($config, $name) !== false) {
			return false;
		}
		
		if($name == 'BLANK') {
			$config = $config.PHP_EOL;
		} elseif($name == 'COMMENT'){
			// If it already exists don't add it
			if(strpos($config, '# '.$value) !== false) {
				return false;
			}
			
			$config = $config.PHP_EOL.'#'.$value.PHP_EOL;
		} else  {
		  
			$config = $config.PHP_EOL."Config::Set('$name', ";
			
			if(is_bool($value)) {
				if($value === true)
					$config .= "true";
				elseif($value === false)
					$config .= "false";
			} else {
				$config .="'$value'";
            }
			
			$config .="); ";
			if($comment!='')
				$config .='# '.$comment;
		}
		
		file_put_contents(CORE_PATH.'/local.config.php', $config);
	}
	
	
	/**
	 * Send current installation data to phpVMS server
	 * 
	 * @param string $version
	 * @return void
	 */
	public static function RegisterInstall($version='') {
	   
		if($version == '')
			$version = PHPVMS_VERSION;
			
		$ext = serialize(get_loaded_extensions());
		
		$params = new SimpleXMLElement('<registration/>');
		$params->addChild('name', SITE_NAME);
		$params->addChild('url', SITE_URL);
		$params->addChild('version', $version);
		$params->addChild('php', phpversion());
		$params->addChild('mysql', @mysql_get_server_info());
		$params->addChild('ext', $ext);
							  
		$url = 'http://api.phpvms.net/register';
							
		error_reporting(0);
		
		$file = new CodonWebService();
		$response = $file->post($url, $params->asXML());
	}
}