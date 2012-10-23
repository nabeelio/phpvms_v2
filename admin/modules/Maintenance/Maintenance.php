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

class Maintenance extends CodonModule {

    /**
     * Maintenance::HTMLHead()
     * 
     * @return
     */
    public function HTMLHead() {
        $this->set('sidebar', 'sidebar_maintenance');
    }

    /**
     * Maintenance::index()
     * 
     * @return
     */
    public function index() {
        $this->options();
    }

    /**
     * Maintenance::options()
     * 
     * @return
     */
    public function options() {
        $this->render('maintenance_options');
    }

    /**
     * Maintenance::resetdistances()
     * 
     * @return
     */
    public function resetdistances() {
        echo '<h3>Updating and Calculating Distances</h3>';

        # Update all of the schedules
        echo '<p><strong>Updating schedules...</strong></p>';

        //$allschedules = SchedulesData::GetSchedulesNoDistance();
        $allschedules = SchedulesData::findSchedules(array());

        if (!$allschedules) {
            echo 'No schedules to update';
            $allschedules = array();
        }

        # Check 'em
        foreach ($allschedules as $sched) {
            $distance = SchedulesData::distanceBetweenPoints($sched->deplat, $sched->deplng, $sched->arrlat, $sched->arrlng);

            $distance = sprintf("%.6f", $distance);
            echo "$sched->code$sched->flightnum - $sched->depname to $sched->arrname " . "is $distance " . Config::Get('UNIT') . '<br />';

            SchedulesData::updateScheduleFields($sched->id, array('distance' => $distance));
        }

        # Update all of the PIREPS

        echo '<p><strong>Updating PIREPs...</strong></p>';

        $allpireps = PIREPData::findPIREPS(array());

        if (!$allpireps) {
            echo 'No PIREPs need updating!';
            $allpireps = array();
        }

        foreach ($allpireps as $pirep) {

            # Find the schedule, and the distance supplied by the schedule:
            $distance = SchedulesData::distanceBetweenPoints($pirep->deplat, $pirep->deplng, $pirep->arrlat, $pirep->arrlng);

            $distance = sprintf("%.2f", $distance);

            echo "PIREP Number $pirep->pirepid ($pirep->code$pirep->flightnum) " 
                    . "$pirep->depname to $pirep->arrname is $distance " . Config::Get('UNIT') . '<br />';

            PIREPData::editPIREPFields($pirep->pirepid, array('distance' => $distance));
        }

        echo '<p>Completed!</p><br />';

        LogData::addLog(Auth::$userinfo->pilotid, 'Reset distances');
    }

    /**
     * Maintenance::resetpirepcount()
     * 
     * @return
     */
    public static function resetpirepcount() {
        
        echo '<h3>Reset PIREP Counts</h3>';
        $all_pilots = PilotData::findPilots(array());

        foreach ($all_pilots as $pilot) {
            
            $pireps = PIREPData::getReportsByAcceptStatus($pilot->pilotid, PIREP_ACCEPTED);
            $total = count($pireps);
            unset($pireps);

            $code = PilotData::getPilotCode($pilot->code, $pilot->pilotid);

            echo "{$code} - {$pilot->firstname} {$pilot->lastname} - {$total} pireps<br />";

            # Update the pireps table
            PilotData::updateProfile($pilot->pilotid, array('totalflights' => $total));
        }

        echo 'Completed!';
    }

    /**
     * Maintenance::changepilotid()
     * 
     * @return
     */
    public function changepilotid() {
        echo '<h3>Change Pilot ID</h3>';

        if (isset($this->post->submit)) {
            $error = false;

            if (!is_numeric($this->post->new_pilotid)) {
                $error = true;
                $this->set('message', 'The pilot ID isn\'t numeric!');
                $this->render('core_error');
                return;
            }

            if ($this->post->new_pilotid < 1) {
                $error = true;
                $this->set('message', 'You cannot have an ID less than 1');
                $this->render('core_error');
                return;
            }

            if (empty($this->post->new_pilotid)) {
                $error = true;
                $this->set('message', 'The pilot ID is blank!');
                $this->render('core_error');
                return;
            }

            if (empty($this->post->old_pilotid) || $this->post->old_pilotid == 0) {
                $error = true;
                $this->set('message', 'No pilot selected');
                $this->render('core_error');
                return;
            }

            $pilot = PilotData::getPilotData($this->post->new_pilotid);
            if (is_object($pilot)) {
                $error = true;
                $this->set('message', 'This ID is already used!');
                $this->render('core_error');
                return;
            }

            if ($error === false) {
                PilotData::changePilotID($this->post->old_pilotid, $this->post->new_pilotid);

                $this->set('message', "Pilot ID changed from {$this->post->old_pilotid} to {$this->post->new_pilotid}");
                $this->render('core_success');
            }
        }

        $this->set('allpilots', PilotData::findPilots(array()));
        $this->render('maintenance_changepilotid');
    }

    /**
     * Maintenance::optimizetables()
     * 
     * @return
     */
    public function optimizetables() {
        echo '<h3>Optimizing Tables...</h3>';
        $results = MaintenanceData::optimizeTables();

        foreach ($results as $row) {
            echo "{$row->Table} - {$row->Msg_text}<br />";
        }
    }

    /**
     * Maintenance::resetacars()
     * 
     * @return
     */
    public function resetacars() {
        echo '<h3>ACARS Reset</h3>';

        ACARSData::resetFlights();

    }

    /**
     * Maintenance::clearcache()
     * 
     * @return
     */
    public function clearcache() {
        echo '<h3>Clearing Cache</h3>';

        $dir_iterator = new RecursiveDirectoryIterator(CACHE_PATH);
        $iterator = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::SELF_FIRST);

        foreach ($iterator as $file) {
            if ($file->getType() != 'file') {
                continue;
            }

            $file_name = $file->getBaseName();
            if ($file_name === 'index.php') continue;

            echo "Removing \"{$file_name}\"<br />";
            unlink($file);
        }

        echo 'Cache cleared!';
    }

    /**
     * Maintenance::calculateranks()
     * 
     * @return
     */
    public function calculateranks() {
        echo '<h3>Resetting Ranks</h3>';
        RanksData::CalculatePilotRanks();
        echo 'Done!';

        LogData::addLog(Auth::$userinfo->pilotid, 'Recalculated ranks');
    }

    /**
     * Maintenance::resetpilotpay()
     * 
     * @return
     */
    public function resetpilotpay() {
        
        echo '<h3>Resetting Pilot Pay</h3>';
        $allpilots = PilotData::GetAllPilots();

        foreach ($allpilots as $p) {
            
            #$total = PilotData::resetPilotPay($p->pilotid);
            $total = PilotData::resetLedgerforPilot($p->pilotid);
            #$total = PilotData::fillMissingLedgerForPIREPS();

            echo "{$p->firstname} {$p->lastname} - total $ {$total}<br />";
        }

        echo 'Done';

        LogData::addLog(Auth::$userinfo->pilotid, 'Reset pilot pay');
    }


    /**
     * Maintenance::resetsignatures()
     * 
     * @return
     */
    public function resetsignatures() {
        $allpilots = PilotData::GetAllPilots();

        echo '<h3>Regenerating signatures</h3>
				<strong>Generating signatures...</strong><br />';

        foreach ($allpilots as $pilot) {
            echo "Generating signature for $pilot->firstname $pilot->lastname<br />";
            PilotData::GenerateSignature($pilot->pilotid);
        }

        echo "Done";

        LogData::addLog(Auth::$userinfo->pilotid, 'Reset signatures');
    }

    /**
     * Maintenance::resethours()
     * 
     * @return
     */
    public function resethours() {
        echo '<h3>Updating Total Hours Count</h3>';

        $total = 0;
        echo '<p>Calculating hours for all pilots: <br />';
        $allpilots = PilotData::GetAllPilots();

        foreach ($allpilots as $pilot) {
            $hours = PilotData::UpdateFlightHours($pilot->pilotid);
            $total = Util::AddTime($total, $hours);
            echo PilotData::GetPilotCode($pilot->code, $pilot->pilotid) . " - found {$hours} flight hours for number <br />";
        }

        echo "Pilots have a total of <strong>$total hours</strong><br /><br />";

        echo "<strong>Now counting from PIREPS</strong><br />";

        StatsData::UpdateTotalHours();
        echo 'Found ' . StatsData::TotalHours() . ' total hours, updated<br /></p>';

        LogData::addLog(Auth::$userinfo->pilotid, 'Reset hours');
    }

    /**
     * Maintenance::resetpirepfinance()
     * 
     * @return
     */
    public function resetpirepfinance() {
        echo '<h3>Reset PIREP Data</h3> 
				Resetting PIREPs...<br />';

        //PIREPData::PopulateEmptyPIREPS();

        echo 'Complete';

        LogData::addLog(Auth::$userinfo->pilotid, 'Reset PIREP finances');
    }

    /**
     * Maintenance::resetscheduleroute()
     * 
     * @return
     */
    public function resetscheduleroute() {
        echo '<h3>Reset cached schedule routes</h3> 
				Resetting... <br />';

        SchedulesData::deleteAllScheduleDetails();

        echo 'Completed!';

        LogData::addLog(Auth::$userinfo->pilotid, 'Reset cached schedule route details');
    }

    /**
     * Maintenance::resetpireproute()
     * 
     * @return
     */
    public function resetpireproute() {
        echo '<h3>Reset cached PIREP routes</h3> 
				Resetting... <br />';

        PIREPData::deleteAllRouteDetails();

        echo 'Completed!';

        LogData::addLog(Auth::$userinfo->pilotid, 'Reset cached pirep route details');
    }
}
