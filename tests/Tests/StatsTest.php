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

class StatsTest extends PHPUnit_Framework_TestCase {
    
    public function __construct() {
        CodonCache::setEnabled(false);
    }

    /**
     * StatsTest::testPilotCount()
     * 
     * @return void
     */
    public function testPilotCount() {
                
        $total = DB::get_row('SELECT COUNT(*) AS `total` FROM phpvms_pilots');
        $this->assertEquals($total->total, StatsData::PilotCount(), 'StatsData::getPilotCount(NO CODE)');
        
        $airlines = OperationsData::getAllAirlines(true);
        foreach($airlines as $airline) {
            
            $pilotCount = StatsData::PilotCount($airline->code);
            
            $total = DB::get_row('SELECT COUNT(*) AS `total` 
                                    FROM phpvms_pilots 
                                    WHERE `code`=\''.$airline->code.'\' 
                                    GROUP BY `code`');
                                    
            $this->assertEquals($total->total, $pilotCount, 'StatsData::getPilotCount('.$airline->code.')');
        }
    }
    
    /**
     * StatsTest::testPaxCount()
     * 
     * @return void
     */
    public function testPaxCount() {
        
        $total = DB::get_row(
            'SELECT SUM(`load`) AS `total` 
                FROM phpvms_pireps WHERE `flighttype`=\'P\' AND `accepted`='.PIREP_ACCEPTED);
                
        $this->assertEquals($total->total, StatsData::TotalPaxCarried(), 'StatsData::TotalPaxCarried(NO CODE)');
        
        $airlines = OperationsData::getAllAirlines(true);
        foreach($airlines as $airline) {
            
            $pilotCount = StatsData::TotalPaxCarried($airline->code);
            
            $total = DB::get_row(
                'SELECT SUM(`load`) AS `total` 
                 FROM phpvms_pireps
                 WHERE accepted ='. PIREP_ACCEPTED.' 
                    AND flighttype = \'P\' 
                    AND code = \''.$airline->code.'\''
            );
                                    
            $this->assertEquals($total->total, $pilotCount, 'StatsData::TotalPaxCarried('.$airline->code.')');
        }
    }
    
    public function testTotalTime() {
        
        StatsData::updateTotalHours();
        
    }
    
}









