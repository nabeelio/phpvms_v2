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

class SchedulePIREPTest extends PHPUnit_Framework_TestCase {
    
    protected $CODE = 'VMS';
    protected $FLIGHTNUM = '9998';
    
    protected $samplePilotID = 1;
    
    protected $sample_schedule = array(
        'code' => '', 
        'flightnum' => '',
        'depicao' => 'KJFK', 
        'arricao' => 'KBOS', 
        'route' => 'BDR V229 HFD V3 WOONS', 
        'aircraft' => '1', 
        'flightlevel' => '300',
        'distance' => '', 
        'deptime' => '1PM EST',
        'arrtime' => '3PM EST', 
        'flighttime' => '2',
        'daysofweek' => '1234567', 
        'week1' => '1234567', 'week2' => '01234', 'week3' => '1234567', 'week4' => '1234567', 
        'price' => '300', 
        'payforflight' => '3000',
        'flighttype' => 'P', 
        'notes' => 'This is a test flight',
        'enabled' => true,
    );
    
    
    /**
     * Always find and return the test schedule, run the asserts
     * on what's found/returned too
     * 
     * @return void
     */
    protected function findSchedule() {

        $sched = SchedulesData::findSchedules(array(
            's.code' => $this->CODE, 's.flightnum' => $this->FLIGHTNUM,
            )
        );
        
        $this->assertGreaterThan(0, count($sched));
        
        # Make sure this schedule has an ID param?
        $sched = $sched [0];
        $this->assertObjectHasAttribute('id', $sched, 'Has ID');
        
        return $sched;
        
    }
    
    
    /**
     * SchedulePIREPTest::resetPilot()
     * 
     * @return void
     */
    protected function resetPilot() {
        
    }
    
    /**
     * ScheduleTest::testAddSchedule()
     * 
     * @return void
     */
    public function testAddSchedule() {
        
        $this->sample_schedule['code'] = $this->CODE;
        $this->sample_schedule['flightnum'] = $this->FLIGHTNUM;        
        
        $ret = SchedulesData::addSchedule($this->sample_schedule);
        $this->assertTrue($ret, DB::error());
    }
    
    /**
     * ScheduleTest::testScheduleIntegrity()
     * 
     * @return void
     */
    public function testScheduleIntegrity() {
        
        $sched = $this->findSchedule();
        
        $this->assertEquals($this->CODE, $sched->code);
        $this->assertEquals($this->FLIGHTNUM, $sched->flightnum);
        
        # Make sure days of week all inserted properly
        $this->assertEquals('1234560', $sched->daysofweek);
        $this->assertEquals('1234560', $sched->week1, 'Week 1, full week');
        $this->assertEquals('01234', $sched->week2, 'Week 2, partial week');        
        
    }
    
    
    /**
     * ScheduleTest::testEditSchedule()
     * 
     * @return void
     */
    public function testEditSchedule() {
        
        $sched = $this->findSchedule();
        
        
    }
    
    /**
     * ScheduleTest::testNavData()
     * 
     * @return void
     */
    public function testNavData() {
        $sched = $this->findSchedule();
        $routeDetails = SchedulesData::getRouteDetails($sched->id);
        $this->assertGreaterThan(0, count($routeDetails));
    }
    
    /**
     * ScheduleTest::testPIREPFile()
     * 
     * @return void
     */
    public function testPIREPPerFlightPay() {
        
        $this->resetPilot();
        $sched = $this->findSchedule();
        
        # Update this schedule to only pay per-hour
        SchedulesData::editScheduleFields($sched->id, array(
                'payforflight' => $this->sample_schedule['payforflight']
            )
        );
        
        $sched = $this->findSchedule();
        $this->assertEquals($this->sample_schedule['payforflight'], $sched->payforflight, 'Pay per-flight set to 0');
        
        Config::Set('PIREP_CHECK_DUPLICATE', false);
        Config::Set('EMAIL_SEND_PIREP', false);
        
        $pirep_test = array(
        	'pilotid' => $this->samplePilotID,
        	'code' => $sched->code,
        	'flightnum' => $sched->flightnum,
        	'route' => $sched->route,
        	'depicao' => $sched->depicao,
        	'arricao' => $sched->arricao,
        	'aircraft' => $sched->aircraft,
        	'flighttime' => $sched->flighttime,
        	'submitdate' => 'NOW()',
        	'fuelused' => 6000,
        	'source' => 'unittest',
        	'comment' => 'Test Flight',
        );
                
        # File the flight report
        $pirepid = PIREPData::fileReport($pirep_test);
		$this->assertGreaterThan(0, $pirepid, PIREPData::$lasterror);
        
        $pirepdata = PIREPData::findPIREPS(array('p.pirepid' => $pirepid));     
        $this->assertGreaterThan(0, count($pirepdata), 'No PIREPs returned');   
        
        # Work on one...
        $pirepdata = $pirepdata[0];
        $this->checkPIREP($pirep_test, $pirepdata->pirepid);
      }
    
    
    /**
     * SchedulePIREPTest::testPIREPPerHourPay()
     * 
     * @return void
     */
    public function testPIREPPerHourPay() {
        
        $this->resetPilot();
        $sched = $this->findSchedule();
        
        Config::Set('PIREP_CHECK_DUPLICATE', false);
        Config::Set('EMAIL_SEND_PIREP', false);
        
        # Update this schedule to only pay per-hour
        SchedulesData::editScheduleFields($sched->id, array(
                'payforflight' => 0
            )
        );
        
        $sched = $this->findSchedule();
        $this->assertEquals(0, $sched->payforflight, 'Pay per-flight set to 0');
        
        $pirep_test = array(
        	'pilotid' => $this->samplePilotID,
        	'code' => $sched->code,
        	'flightnum' => $sched->flightnum,
        	'route' => $sched->route,
        	'depicao' => $sched->depicao,
        	'arricao' => $sched->arricao,
        	'aircraft' => $sched->aircraft,
        	'flighttime' => $sched->flighttime,
        	'submitdate' => 'NOW()',
        	'fuelused' => 6000,
        	'source' => 'unittest',
        	'comment' => 'Test Flight',
        );
        
        # Update Pilot Pay to be set to zero
        $pilot_data = PilotData::getPilotData($this->samplePilotID);
                
        # File the flight report
        $pirepid = PIREPData::fileReport($pirep_test);
		$this->assertGreaterThan(0, $pirepid, PIREPData::$lasterror);
        
        $pirepdata = PIREPData::findPIREPS(array('p.pirepid' => $pirepid));     
        $this->assertGreaterThan(0, count($pirepdata), 'No PIREPs returned');   
        
        # Work on one...
        $pirepdata = $pirepdata[0];
        
        $this->checkPIREP($pirep_test, $pirepdata->pirepid);
    }
    
    
    /**
     * SchedulePIREPTest::checkPIREP()
     * 
     * @param mixed $pirepdata
     * @return void
     */
    protected function checkPIREP($pirep_test, $pirepid) {
        
        $sched = $this->findSchedule();
        
        $pirepdata = PIREPData::findPIREPS(array('p.pirepid' => $pirepid));
        $pirepdata = $pirepdata[0];
        
        $this->checkPIREPFinances($pirep_test, $pirepdata);        
                        
        # Check the pilot pay        
        $pilot_data = PilotData::getPilotData($this->samplePilotID);
        
        # Change PIREP Status
        $status = PIREPData::changePIREPStatus($pirepdata->pirepid, PIREP_ACCEPTED);
        $pirepdata = PIREPData::findPIREPS(array('p.pirepid' => $pirepid));
        $this->assertEquals(PIREP_ACCEPTED, $pirepdata[0]->accepted, 'changePIREPStatus to ACCEPTED');
        $pirepdata = $pirepdata[0];
        
        # Check the schedule flown count:
        $post_accept = $this->findSchedule();
        $this->assertEquals($sched->timesflown + 1, $post_accept->timesflown, "Schedule increment count");
        
        # Check the pilot pay
        $post_pilot_data = PilotData::getPilotData($this->samplePilotID);
        $pay_log = LedgerData::getPaymentByPIREP($pirepid);
        
        if($pirepdata->paytype == PILOT_PAY_SCHEDULE) {
            
            $this->assertEquals($this->sample_schedule['payforflight'], $pirepdata->pilotpay, 
                                'PIREP Pay Amount');
            
            $this->assertEquals($this->sample_schedule['payforflight'], $pay_log->amount, 
                                'Check pilot pay after PIREP ACCEPT');
            
        } else {
            $this->assertEquals(
                PilotData::getPilotPay($this->sample_schedule['flighttime'], $pilot_data->payrate), 
                $pay_log->amount, 
                'Check pilot pay after PIREP ACCEPT'
            );
            
        }
        
        $this->assertEquals($pilot_data->totalflights + 1, $post_pilot_data->totalflights, 'Total Flights');
               
        # Check total hours
        $this->assertGreaterThan($pilot_data->totalhours, $post_pilot_data->totalhours, 'Checking total hours');
                
        # Reject the PIREP and then check the pilot pay
        $status = PIREPData::changePIREPStatus($pirepdata->pirepid, PIREP_REJECTED);
        $pirepdata = PIREPData::findPIREPS(array('p.pirepid' => $pirepid)); 
        $this->assertEquals(PIREP_REJECTED, $pirepdata[0]->accepted, 'changePIREPStatus to REJECTED');
        $pirepdata = $pirepdata[0];
        
        # Check the schedule flown count:
        $post_accept = $this->findSchedule();
        $this->assertEquals($sched->timesflown, $post_accept->timesflown, "Schedule increment count");
        
        $post_pilot_data = PilotData::getPilotData($this->samplePilotID);
        $this->assertEquals($pilot_data->totalpay, $post_pilot_data->totalpay, 'Check pilot pay after PIREP REJECT');
        $this->assertEquals($pilot_data->totalflights, $post_pilot_data->totalflights, 'Total Flights after REJECT');
        
        # Delete the PIREP
        PIREPData::deletePIREP($pirepid);
		
		# Verify delete
		$data = PIREPData::findPIREPS(array('p.pirepid' => $pirepid));
		$this->assertEmpty($data, 'PIREPDdata::deletePIREP()');
        
    }
    
    
    /**
     * Check the finances for a PIREP
     * 
     * @return void
     */
    protected function checkPIREPFinances($pirep_test, $pirepdata) {
        
        # Fuel prices
        $fuelCost = FuelData::getFuelPrice($pirep_test['depicao']);
        
        $this->assertEquals($fuelCost, $pirepdata->fuelunitcost, 'FUEL UNIT COST');       
        $this->assertEquals($pirep_test['fuelused'], $pirepdata->fuelused, 'FUEL USED');
        
        # Figure out what fuel should cost, + the surcharge amount
        $fuelShouldCost = $pirep_test['fuelused'] * $fuelCost;
        $fuelShouldCost += ((Config::Get('FUEL_SURCHARGE') / 100) * $pirep_test['fuelused']) * $fuelCost;
        $this->assertEquals($fuelShouldCost, $pirepdata->fuelprice, 'FUEL SHOULD COST');
        
        # Check revenue...
        $revenue = $pirepdata->load * $pirepdata->price;
        $this->assertEquals($revenue, $pirepdata->gross, 'GROSS REVENUE');
    }
    
    
    /**
     * SchedulePIREPTest::testPIREPRejected()
     * 
     * @return void
     */
    public function testPIREPRejected() {
        
        $this->resetPilot();
        $sched = $this->findSchedule();
        
        Config::Set('PIREP_CHECK_DUPLICATE', false);
        Config::Set('EMAIL_SEND_PIREP', false);
        
        # Update this schedule to only pay per-hour
        SchedulesData::editScheduleFields($sched->id, array(
                'payforflight' => 0
            )
        );
        
        $sched = $this->findSchedule();
        $this->assertEquals(0, $sched->payforflight, 'Pay per-flight set to 0');
        
        $pirep_test = array(
        	'pilotid' => $this->samplePilotID,
        	'code' => $sched->code,
        	'flightnum' => $sched->flightnum,
        	'route' => $sched->route,
        	'depicao' => $sched->depicao,
        	'arricao' => $sched->arricao,
        	'aircraft' => $sched->aircraft,
        	'flighttime' => $sched->flighttime,
        	'submitdate' => 'NOW()',
        	'fuelused' => 6000,
        	'source' => 'unittest',
        	'comment' => 'Test Flight',
        );
        
        # Update Pilot Pay to be set to zero
        PilotData::updateProfile($this->samplePilotID, array('totalpay' => 0));
        
        $pilot_data = PilotData::getPilotData($this->samplePilotID);
        $this->assertEquals($pilot_data->totalpay, 0, 'Reset Pilot Pay to 0');
                
        # File the flight report
        $pirepid = PIREPData::fileReport($pirep_test);
		$this->assertGreaterThan(0, $pirepid, PIREPData::$lasterror);
        
        $pirepdata = PIREPData::findPIREPS(array('p.pirepid' => $pirepid));     
        $this->assertGreaterThan(0, count($pirepdata), 'No PIREPs returned');   
        
        # Work on one...
        $pirepdata = $pirepdata[0];
        
        # Verify the little bits of this PIREP....
        $this->assertEquals(PILOT_PAY_HOURLY, $pirepdata->paytype , 'PIREP Pay Type');
        $this->assertEquals($pilot_data->payrate, $pirepdata->pilotpay, 'PIREP Pay Amount');
        
        # Check the pilot pay        
        $pilot_data = PilotData::getPilotData($this->samplePilotID);
        $this->assertEquals(0, $pilot_data->totalpay, 'Check pilot pay after PIREP FILE');
        
        # Reject the PIREP and then check the pilot pay
        $status = PIREPData::changePIREPStatus($pirepdata->pirepid, PIREP_REJECTED);
        $pirepdata = PIREPData::findPIREPS(array('p.pirepid' => $pirepid)); 
        $this->assertEquals(PIREP_REJECTED, $pirepdata[0]->accepted, 'changePIREPStatus to ACCEPTED');
        $pirepdata = $pirepdata[0];
        
        # Check the schedule flown count:
        $post_accept = $this->findSchedule();
        $this->assertEquals($sched->timesflown, $post_accept->timesflown, "Schedule increment count");
        
        $post_pilot_data = PilotData::getPilotData($this->samplePilotID);
        
        $this->assertEquals(0, $post_pilot_data->totalpay, 'Check pilot pay after PIREP REJECT');
        $this->assertEquals($pilot_data->totalflights, $post_pilot_data->totalflights, 'Total Flights');
        
        # Delete the PIREP
        PIREPData::deletePIREP($pirepid);
		
		# Verify delete
		$data = PIREPData::findPIREPS(array('p.pirepid' => $pirepid));
		$this->assertEmpty($data, 'PIREPDdata::deletePIREP()');
    }
    
    /**
     * ScheduleTest::deleteTestSchedule()
     * 
     * @return void
     */
    public function deleteTestSchedule() {
        
        $sched = $this->findSchedule();
        $ret = SchedulesData::deleteSchedule($sched->id);
        
    }
}









