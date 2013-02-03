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

class Schedules extends CodonModule {
    
    /**
     * Schedules::index()
     * 
     * @return
     */
    public function index() {
        $this->view();
    }

    /**
     * Schedules::view()
     * 
     * @return
     */
    public function view() {
        if (isset(self::$post->action) && self::$post->action == 'findflight') {
            $this->FindFlight();
            return;
        }

        $this->showSchedules();
    }

    /**
     * Schedules::detail()
     * 
     * @param string $routeid
     * @return
     */
    public function detail($routeid = '') {
        $this->details($routeid);
    }

    /**
     * Schedules::details()
     * 
     * @param string $routeid
     * @return
     */
    public function details($routeid = '') {
        //$routeid = self::$get->id;

        if (!is_numeric($routeid)) {
            preg_match('/^([A-Za-z]{3})(\d*)/', $routeid, $matches);
            $code = $matches[1];
            $flightnum = $matches[2];

            $params = array('s.code' => $code, 's.flightnum' => $flightnum);
        } else {
            $params = array('s.id' => $routeid);
        }

        $schedule = SchedulesData::getScheduleDetailed($routeid);
        $this->set('schedule', $schedule);
        $this->render('schedule_details.tpl');
        $this->render('route_map.tpl');
    }

    /**
     * Schedules::brief()
     * 
     * @param string $routeid
     * @return
     */
    public function brief($routeid = '') {
        if ($routeid == '') {
            $this->set('message', 'You must be logged in to access this feature!');
            $this->render('core_error.tpl');
            return;
        }

        $schedule = SchedulesData::getScheduleDetailed($routeid);
        $this->set('schedule', $schedule);
        $this->render('schedule_briefing.tpl');
    }

    /**
     * Schedules::boardingpass()
     * 
     * @param mixed $routeid
     * @return
     */
    public function boardingpass($routeid) {
        if ($routeid == '') {
            $this->set('message', 'You must be logged in to access this feature!');
            $this->render('core_error.tpl');
            return;
        }

        $schedule = SchedulesData::getScheduleDetailed($routeid);

        $this->set('schedule', $schedule);
        $this->render('schedule_boarding_pass.tpl');
    }

    /**
     * Schedules::bids()
     * 
     * @return
     */
    public function bids() {
        if (!Auth::LoggedIn())
            return;

        $this->set('bids', SchedulesData::GetBids(Auth::$pilot->pilotid));
        $this->render('schedule_bids.tpl');
    }

    /**
     * Schedules::addbid()
     * 
     * @return
     */
    public function addbid() {
        if (!Auth::LoggedIn())
            return;

        $routeid = self::$get->id;

        if ($routeid == '') {
            echo 'No route passed';
            return;
        }

        // See if this is a valid route
        $route = SchedulesData::findSchedules(array('s.id' => $routeid));

        if (!is_array($route) && !isset($route[0])) {
            echo 'Invalid Route';
            return;
        }

        CodonEvent::Dispatch('bid_preadd', 'Schedules', $routeid);

        /* Block any other bids if they've already made a bid
        */
        if (Config::Get('DISABLE_BIDS_ON_BID') == true) {
            $bids = SchedulesData::getBids(Auth::$pilot->pilotid);

            # They've got somethin goin on
            if (count($bids) > 0) {
                echo 'Bid exists!';
                return;
            }
        }

        $ret = SchedulesData::AddBid(Auth::$pilot->pilotid, $routeid);
        CodonEvent::Dispatch('bid_added', 'Schedules', $routeid);

        if ($ret == true) {
            echo 'Bid added';
        } else {
            echo 'Already in bids!';
        }
    }

    /**
     * Schedules::removebid()
     * 
     * @return
     */
    public function removebid() {
        if (!Auth::LoggedIn())
            return;

        SchedulesData::RemoveBid(self::$post->id);
    }

    /**
     * Schedules::showSchedules()
     * 
     * @return
     */
    public function showSchedules() {
        
        $depapts = OperationsData::GetAllAirports();
        $equip = OperationsData::GetAllAircraftSearchList(true);
        $airlines = OperationsData::GetAllAirlines();
        
        $this->set('airlines', $airlines); #deprecated
        $this->set('airline_list', $airlines);
        
        $this->set('depairports', $depapts);
        
        $this->set('equipment', $equip); # deprecated
        $this->set('aircraft_list', $equip);
        
        $this->render('schedule_searchform.tpl');

        # Show the routes. Remote this to not show them.
        
        $schedules = SchedulesData::getSchedules();
        
        # Do some filtering and whatnots, take it out of the template...      
        $today = getdate();
        $week_number = intval(($today['mday'] - 1) / 7) + 1;
        $current_day == date('w');
        $var_name = 'week'.$week_number;
        
        # query once, save for later
        if(Config::get('SCHEDULES_ONLY_LAST_PIREP') === true && Auth::LoggedIn() == true) {
   	    	$pirep_list = PIREPData::findPIREPS(array(
    			'p.pilotid' => Auth::$pilot->pilotid,
    			'p.accepted' => PIREP_ACCEPTED
    		  ), 1); // return only one
        }
        
        foreach($schedules as $key => $s) {
            
            # should we skip schedules based on day of week?
            if(Config::get('CHECK_SCHEDULE_DAY_OF_WEEK') === true) {
                
                if(isset($s->{$var_name}) && !empty($s->{$var_name})) {
                    # check if today is in the active list for this week
                    if(@substr_count($s->{$var_name}, $current_day) == 0) {
                        unset($schedules[$key]);
                        continue;
                    }
                } else {
                    if(substr_count($s->daysofweek, date('w')) == 0) {
                        unset($schedules[$key]);
                        continue;
                    }
                }
            }
            
            # remove this schedule from the list if there's a bid on it
        	if(Config::get('DISABLE_SCHED_ON_BID') === true && $route->bidid != 0) {
        		unset($schedules[$key]);
                continue;
        	}
            
            
            /*	This means the aircraft rank level is higher than
        		what the pilot's ranklevel, so just do "continue"
       			and move onto the next route in the list  */
            if(Config::get('RESTRICT_AIRCRAFT_RANKS') === true && Auth::LoggedIn()) {
        		if($route->aircraftlevel > Auth::$pilot->ranklevel) {
        			unset($schedules[$key]);
                    continue;
        		}
        	}
            
            if(Config::get('SCHEDULES_ONLY_LAST_PIREP') === true && Auth::LoggedIn() == true) {
        		if(count($pirep_list) > 0) {
        			# IF the arrival airport doesn't match the departure airport
        			if($pirep_list[0]->arricao != $s->depicao) {
        				unset($schedules[$key]);
                        continue;
        			}
        		}
            }
            
        } // end foreach schedules
        
        $this->set('allroutes', $schedules);
        $this->set('schedule_list', $schedules);
        $this->render('schedule_list.tpl');
    }

    /**
     * Schedules::findFlight()
     * 
     * @return
     */
    public function findFlight() {
        
        
        $params = array();
        if(self::$post->airlines != '') {
            $params['s.code'] = self::$post->airlines;
        }

        if (self::$post->depicao != '') {
            $params['s.depicao'] = self::$post->depicao;
        }

        if (self::$post->arricao != '') {
            $params['s.arricao'] = self::$post->arricao;
        }

        if (self::$post->equipment != '') {
            $params['a.name'] = self::$post->equipment;
        }

        if (self::$post->distance != '') {
            if (self::$post->type == 'greater')
                $value = '> ';
            else
                $value = '< ';

            $value .= self::$post->distance;

            $params['s.distance'] = $value;
        }

        $params['s.enabled'] = 1;
        
        $schedule_list = SchedulesData::findSchedules($params);
        $this->set('allroutes', $schedule_list); #deprecated
        $this->set('schedule_list', $schedule_list);
        
        $this->render('schedule_results.tpl');
    }

    /**
     * Schedules::statsdaysdata()
     * 
     * @param mixed $routeid
     * @return
     */
    public function statsdaysdata($routeid) {
        
        $schedule = SchedulesData::findSchedules(array('s.id' => $routeid));
        $schedule = $schedule[0];

        // Last 30 days stats
        $data = PIREPData::getIntervalDataByDays(array(
            'p.code' => $schedule->code,
            'p.flightnum' => $schedule->flightnum, 
            ), 30);

        $this->create_line_graph('Schedule Flown Counts', $data);
    }

    /**
     * Schedules::create_line_graph()
     * 
     * @param mixed $title
     * @param mixed $data
     * @return
     */
    protected function create_line_graph($title, $data) {
        
        if (!$data) {
            $data = array();
        }

        $titles = array();
        $bar_titles = array();
        foreach ($data as $val) {
            $titles[] = $val->ym;
            $values[] = floatval($val->total);
        }

        OFCharts::add_data_set($titles, $values);
        echo OFCharts::create_line_graph($title);
    }
}
