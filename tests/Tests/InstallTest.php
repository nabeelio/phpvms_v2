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

class InstallTest extends PHPUnit_Framework_TestCase {
    
    /**
     * Ensure that all database tables are included as designed
     * 
     * @return void
     */
    public function testDatabaseTables() {
        
        if (!file_exists(SITE_ROOT.'/install/sql/structure.xml')) {
            $this->markTestSkipped('The install folder is unavailable, can\'t run this test.');
        }
        
        $db = simplexml_load_file(SITE_ROOT.'/install/sql/structure.xml');
        
        foreach($db->database->table_structure as $table) {
            
        	$tablename = str_replace('phpvms_', TABLE_PREFIX, $table['name']);
            
        	DB::query('SELECT * FROM '.$tablename.' WHERE 1=1 LIMIT 1');
        	
            $this->assertNotEquals('1146', DB::errno(), "\"{$tablename}\" is missing");
        	if(DB::$errno == '1146')   {
        		continue;
        	}
        	
        	/* loop through all the columns returned by the above query and all the columns
        		from the fields in the xml file, and make sure they all match up, with the
        		fieldlist from the xml being the "master" outside loop which it looks up against */
        	$anyerrors = false;
        	$colinfo = DB::$DB->col_info;
        	foreach($table->field as $field) {
        		$found = false;
        		foreach($colinfo as $column) {
        			if($column->name == $field['Field']) {
        				$found = true;
        				break;
        			}
        		}
        		
                $this->assertTrue($found, "Column {$field['Field']} from {$tablename} missing");
        	}
        }
    }
    
    /**
     * Check if all the files on the server are valid
     * 
     * @return void
     */
    public function testInstallFiles() {
        
        #$this->markTestSkipped('Install files check');
        
    }
    
}