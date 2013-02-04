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
 * @package module_admin_operations
 */


class Operations extends CodonModule {

    /**
     * Operations::HTMLHead()
     * 
     * @return
     */
    public function HTMLHead() {
        switch (self::$controller->function) {
            case 'airlines':
                $this->set('sidebar', 'sidebar_airlines.tpl');
                break;
            case 'addaircraft':
            case 'aircraft':
                $this->set('sidebar', 'sidebar_aircraft.tpl');
                break;
            case 'airports':
                $this->set('sidebar', 'sidebar_airports.tpl');
                break;
            case '':
            case 'addschedule':
            case 'activeschedules':
            case 'inactiveschedules':
            case 'schedules':
                $this->set('sidebar', 'sidebar_schedules.tpl');
                break;
            case 'editschedule':
                $this->set('sidebar', 'sidebar_editschedule.tpl');
                break;
        }
    }

    /**
     * Operations::index()
     * 
     * @return
     */
    public function index() {
        $this->schedules();
    }


    /**
     * Operations::viewmap()
     * 
     * @return
     */
    public function viewmap() {
        
        if (self::$get->type === 'pirep') {
            $data = PIREPData::getReportDetails(self::$get->id);
        } elseif (self::$get->type === 'schedule') {
            $data = SchedulesData::getScheduleDetailed(self::$get->id);
        } elseif (self::$get->type === 'preview') {

            $data = new stdClass();

            $depicao = OperationsData::getAirportInfo(self::$get->depicao);
            $arricao = OperationsData::getAirportInfo(self::$get->arricao);
            
            $data->deplat = $depicao->lat;
            $data->deplng = $depicao->lng;
            $data->depname = $depicao->name;

            $data->arrlat = $arricao->lat;
            $data->arrlng = $arricao->lng;
            $data->arrname = $arricao->name;

            $data->route = self::$get->route;

            unset($depicao);
            unset($arricao);

            $data->route_details = NavData::parseRoute($data);
        }

        $this->set('mapdata', $data);
        $this->render('route_map.tpl');
    }

    /**
     * Operations::addaircraft()
     * 
     * @return
     */
    public function addaircraft() {
        
        $this->set('title', 'Add Aircraft');
        $this->set('action', 'addaircraft');
        $this->set('allranks', RanksData::getAllRanks());
        $this->render('ops_aircraftform.tpl');
    }

    /**
     * Operations::editaircraft()
     * 
     * @return
     */
    public function editaircraft() {
        
        $id = self::$get->id;

        $this->set('aircraft', OperationsData::GetAircraftInfo($id));
        $this->set('title', 'Edit Aircraft');
        $this->set('action', 'editaircraft');
        $this->set('allranks', RanksData::getAllRanks());
        $this->render('ops_aircraftform.tpl');
    }

    /**
     * Operations::addairline()
     * 
     * @return
     */
    public function addairline() {
        $this->set('title', 'Add Airline');
        $this->set('action', 'addairline');
        $this->render('ops_airlineform.tpl');
    }

    /**
     * Operations::editairline()
     * 
     * @return
     */
    public function editairline() {
        $this->set('title', 'Edit Airline');
        $this->set('action', 'editairline');
        $this->set('airline', OperationsData::GetAirlineByID(self::$get->id));

        $this->render('ops_airlineform.tpl');
    }

    /**
     * Operations::calculatedistance()
     * 
     * @param string $depicao
     * @param string $arricao
     * @return
     */
    public function calculatedistance($depicao = '', $arricao = '') {
        
        if ($depicao == '') $depicao = self::$get->depicao;
        if ($arricao == '') $arricao = self::$get->arricao;

        echo OperationsData::getAirportDistance($depicao, $arricao);
    }

    /**
     * Operations::getfuelprice()
     * 
     * @return
     */
    public function getfuelprice() {
        
        if (Config::Get('FUEL_GET_LIVE_PRICE') == false) {
            echo '<span style="color: red">Live fuel pricing is disabled!</span>';
            return;
        }

        $icao = $_GET['icao'];
        $price = FuelData::get_from_server($icao);

        if (is_bool($price) && $price === false) {
            echo '<span style="color: red">Live fuel pricing is not available for this airport</span>';
            return;
        }

        echo '<span style="color: #33CC00">OK! Found - current price: <strong>' . $price .
            '</strong></span>';
    }

    /**
     * Operations::findairport()
     * 
     * @return
     */
    public function findairport() {
        
        $results = OperationsData::searchAirport(self::$get->term);

        if (count($results) > 0) {
            $return = array();

            foreach ($results as $row) {
               $return[] = array(
                    'label' => "{$row->icao} ({$row->name})", 
                    'value' => $row->icao,
                    'id' => $row->id, 
                );
            }

            echo json_encode($return);
        }
    }

    /**
     * Operations::airlines()
     * 
     * @return
     */
    public function airlines() {
        
        if (isset(self::$post->action)) {
            if (self::$post->action == 'addairline') {
                $this->add_airline_post();
            } elseif (self::$post->action == 'editairline') {
                $this->edit_airline_post();
            }
        }

        $this->set('allairlines', OperationsData::GetAllAirlines());
        $this->render('ops_airlineslist.tpl');
    }

    /**
     * Operations::aircraft()
     * 
     * @return
     */
    public function aircraft() {
        /* If they're adding an aircraft, go through this pain
        */
        switch (self::$post->action) {

            case 'addaircraft':
                $this->add_aircraft_post();
                break;

            case 'editaircraft':
                $this->edit_aircraft_post();
                break;
        }

        $this->set('allaircraft', OperationsData::GetAllAircraft());
        $this->render('ops_aircraftlist.tpl');
    }

    /**
     * Operations::addairport()
     * 
     * @return
     */
    public function addairport() {
        $this->set('title', 'Add Airport');
        $this->set('action', 'addairport');

        $this->render('ops_airportform.tpl');
    }

    /**
     * Operations::editairport()
     * 
     * @return
     */
    public function editairport() {
        $this->set('title', 'Edit Airport');
        $this->set('action', 'editairport');
        $this->set('airport', OperationsData::GetAirportInfo(self::$get->icao));

        $this->render('ops_airportform.tpl');
    }

    /**
     * Operations::airports()
     * 
     * @return
     */
    public function airports() {
        
        /* If they're adding an airport, go through this pain
        */
        if (isset(self::$post->action)) {
            switch (self::$post->action) {
                case 'addairport':
                    $this->add_airport_post();
                    break;
                case 'editairport':
                    $this->edit_airport_post();
                    break;
            }

            return;
        }

        //$this->set('airports', OperationsData::getAllAirports());
        $this->render('ops_airportlist.tpl');
    }

    /**
     * Operations::airportgrid()
     * 
     * @return
     */
    public function airportgrid() {
        
        $page = self::$get->page; // get the requested page
        $limit = self::$get->rows; // get how many rows we want to have into the grid
        $sidx = self::$get->sidx; // get index row - i.e. user click to sort
        $sord = self::$get->sord; // get the direction
        if (!$sidx) $sidx = 1;

        # http://dev.phpvms.net/admin/action.php/operations/
        # ?_search=true&nd=1270940867171&rows=20&page=1&sidx=flightnum&sord=asc&searchField=code&searchString=TAY&searchOper=eq

        /* Do the search using jqGrid */
        $where = array();
        if (self::$get->_search == 'true') {

            $searchstr = jqgrid::strip(self::$get->filters);
            $where_string = jqgrid::constructWhere($searchstr);

            # Append to our search, add 1=1 since it comes with AND
            #	from above
            $where[] = "1=1 {$where_string}";
        }

        # Do a search without the limits so we can find how many records
        $count = count(OperationsData::findAirport($where));

        if ($count > 0) {
            $total_pages = ceil($count / $limit);
        } else {
            $total_pages = 0;
        }

        if ($page > $total_pages) {
            $page = $total_pages;
        }

        $start = $limit * $page - $limit; // do not put $limit*($page - 1)
        if ($start < 0) {
            $start = 0;
        }

        # And finally do a search with the limits
        $airports = OperationsData::findAirport($where, $limit, $start, "{$sidx} {$sord}");
        if (!$airports) {
            $airports = array();
        }

        # Form the json header
        $json = array(
            'page' => $page, 
            'total' => $total_pages, 
            'records' => $count,
            'rows' => array()
        );

        # Add each row to the above array
        foreach ($airports as $row) {
            
            if ($row->fuelprice == 0) {
                $row->fuelprice = 'Live';
            }

            if ($row->hub == 1) {
                $name = "<b>{$row->name}</b>";
            } else {
                $name = $row->name;
            }

            $edit = '<a href="#" onclick="editairport(\'' . $row->icao . '\'); return false;">Edit</a>';

            $tmp = array('id' => $row->id, 'cell' => array( # Each column, in order
                $row->icao, $name, $row->country, $row->fuelprice, $row->lat, $row->lng, $edit, ), );

            $json['rows'][] = $tmp;
        }

        header("Content-type: text/x-json");
        echo json_encode($json);
    }

    /**
     * Operations::addschedule()
     * 
     * @return
     */
    public function addschedule() {
        $this->set('title', 'Add Schedule');
        $this->set('action', 'addschedule');

        if (self::$get->reverse == '1') {
            $schedule = SchedulesData::GetSchedule(self::$get->id);
            
            # Reverse stuffs            
            unset($schedule->id);
            
            $schedule->flightnum = '';
            
            $tmp = $schedule->depicao;
            $schedule->depicao = $schedule->arricao;
            $schedule->arricao = $tmp;
            
            if($schedule->route != '') {
                $route = @explode(' ', $schedule->route);
                if(is_array($route)) {
                    $route = array_reverse($route);
                    $schedule->route = $route;
                }
            }
            
            $schedule->distance = OperationsData::getAirportDistance($schedule->depicao, $schedule->arricao);
            
            $this->set('schedule', $schedule);
        }

        $this->set('allairlines', OperationsData::GetAllAirlines());
        $this->set('allaircraft', OperationsData::GetAllAircraft());
        $this->set('allairports', OperationsData::GetAllAirports());
        //$this->set('airport_json_list', OperationsData::getAllAirportsJSON());
        $this->set('flighttypes', Config::Get('FLIGHT_TYPES'));

        $this->render('ops_scheduleform.tpl');
    }

    /**
     * Operations::editschedule()
     * 
     * @return
     */
    public function editschedule() {
        $id = self::$get->id;

        $this->set('title', 'Edit Schedule');
        $this->set('schedule', SchedulesData::GetSchedule($id));

        $this->set('action', 'editschedule');

        $this->set('allairlines', OperationsData::GetAllAirlines());
        $this->set('allaircraft', OperationsData::GetAllAircraft());
        $this->set('allairports', OperationsData::GetAllAirports());
        $this->set('flighttypes', Config::Get('FLIGHT_TYPES'));

        $this->render('ops_scheduleform.tpl');
    }

    /**
     * Operations::activeschedules()
     * 
     * @return
     */
    public function activeschedules() {
        $this->schedules('activeschedules');
    }

    /**
     * Operations::inactiveschedules()
     * 
     * @return
     */
    public function inactiveschedules() {
        $this->schedules('inactiveschedules');
    }

    /**
     * Operations::schedulegrid()
     * 
     * @return
     */
    public function schedulegrid() {
        $page = self::$get->page; // get the requested page
        $limit = self::$get->rows; // get how many rows we want to have into the grid
        $sidx = self::$get->sidx; // get index row - i.e. user click to sort
        $sord = self::$get->sord; // get the direction
        if (!$sidx) $sidx = 1;

        # http://dev.phpvms.net/admin/action.php/operations/
        # ?_search=true&nd=1270940867171&rows=20&page=1&sidx=flightnum&sord=asc&searchField=code&searchString=TAY&searchOper=eq

        /* Do the search using jqGrid */
        $where = array();
        if (self::$get->_search == 'true') {
            $searchstr = jqgrid::strip(self::$get->filters);
            $where_string = jqgrid::constructWhere($searchstr);

            # Append to our search, add 1=1 since it comes with AND
            #	from above
            $where[] = "1=1 {$where_string}";
        }

        Config::Set('SCHEDULES_ORDER_BY', "{$sidx} {$sord}");

        # Do a search without the limits so we can find how many records
        $count = SchedulesData::countSchedules($where);
        if ($count > 0) {
            $total_pages = ceil($count / $limit);
        } else {
            $total_pages = 0;
        }

        if ($page > $total_pages) {
            $page = $total_pages;
        }

        $start = $limit * $page - $limit; // do not put $limit*($page - 1)
        if ($start < 0) {
            $start = 0;
        }

        # And finally do a search with the limits
        $schedules = SchedulesData::findSchedules($where, $limit, $start);
        if (!$schedules) {
            $schedules = array();
        }

        # Form the json header
        $json = array('page' => $page, 'total' => $total_pages, 'records' => $count,
            'rows' => array());

        # Add each row to the above array
        foreach ($schedules as $row) {

            if ($row->route != '') {
                $route = '<a href="#" onclick="showroute(\'' . $row->id . '\'); return false;">View</a>';
            } else {
                $route = '-';
            }

            $edit = '<a href="' . adminurl('/operations/editschedule?id=' . $row->id) .
                '">Edit</a> <a href="' . adminurl('/operations/addschedule?reverse=1&id=' . $row->id) .
                '">Reverse</a>';
            $delete = '<a href="#" onclick="deleteschedule(' . $row->id .
                '); return false;">Delete</a>';

            $tmp = array('id' => $row->id, 
                'cell' => array( # Each column, in order
                    $row->code, $row->flightnum, $row->depicao, $row->arricao, $row->aircraft, $row->registration,
                    $route, Util::GetDaysCompact($row->daysofweek), $row->distance, $row->timesflown,
                    $row->enabled, $edit, $delete, ), );

            $json['rows'][] = $tmp;
        }

        header("Content-type: text/x-json");
        echo json_encode($json);
    }

    /**
     * Operations::schedules()
     * 
     * @param string $type
     * @return
     */
    public function schedules($type = 'activeschedules') {
        /* These are loaded in popup box */
        if (self::$get->action == 'viewroute') {
            $id = self::$get->id;
            return;
        }


        if (self::$get->action == 'filter') {

            $this->set('title', 'Filtered Schedules');

            if (self::$get->type == 'flightnum') {
                $params = array('s.flightnum' => self::$get->query);
            } elseif (self::$get->type == 'code') {
                $params = array('s.code' => self::$get->query);
            } elseif (self::$get->type == 'aircraft') {
                $params = array('a.name' => self::$get->query);
            } elseif (self::$get->type == 'depapt') {
                $params = array('s.depicao' => self::$get->query);
            } elseif (self::$get->type == 'arrapt') {
                $params = array('s.arricao' => self::$get->query);
            }

            // Filter or don't filter enabled/disabled flights
            if (isset(self::$get->enabled) && self::$get->enabled != 'all') {
                $params['s.enabled'] = self::$get->enabled;
            }

            $this->set('schedules', SchedulesData::findSchedules($params));
            $this->render('ops_schedules.tpl');
            return;
        }

        switch (self::$post->action) {
            case 'addschedule':
                $this->add_schedule_post();
                break;

            case 'editschedule':
                $this->edit_schedule_post();
                break;

            case 'deleteschedule':
                $this->delete_schedule_post();
                return;
                break;
        }

        if (!isset(self::$get->start) || self::$get->start == '') {
            self::$get->start = 0;
        }

        $num_per_page = 20;
        $start = $num_per_page * self::$get->start;

        if ($type == 'schedules' || $type == 'activeschedules') {
            $params = array('s.enabled' => 1);
            $schedules = SchedulesData::findSchedules($params, $num_per_page, $start);

            $this->set('title', 'Viewing Active Schedules');
            $this->set('schedules', $schedules);

            if (count($schedules) >= $num_per_page) {

                $this->set('paginate', true);
                $this->set('start', self::$get->start + 1);

                if (self::$get->start - 1 > 0) {

                    $prev = self::$get->start - 1;
                    if ($prev == '') $prev = 0;

                    $this->set('prev', intval($prev));
                }
            }
        } else {
            $this->set('title', 'Viewing Inactive Schedules');
            $this->set('schedules', SchedulesData::findSchedules(array('s.enabled' => 0)));
        }

        $this->render('ops_schedules.tpl');
    }

    /**
     * Operations::add_airline_post()
     * 
     * @return
     */
    protected function add_airline_post() {
        self::$post->code = strtoupper(self::$post->code);

        if (self::$post->code == '' || self::$post->name == '') {
            $this->set('message', 'You must fill out all of the fields');
            $this->render('core_error.tpl');
            return;
        }

        if (OperationsData::GetAirlineByCode(self::$post->code)) {
            $this->set('message', 'An airline with this code already exists!');
            $this->render('core_error.tpl');
            return;
        }

        OperationsData::AddAirline(self::$post->code, self::$post->name);

        if (DB::errno() != 0) {
            if (DB::errno() == 1062) // Duplicate entry
 
                $this->set('message', 'This airline has already been added');
            else  $this->set('message', 'There was an error adding the airline');

            $this->render('core_error.tpl');
            return;
        }

        $this->set('message', 'Added the airline "' . self::$post->code . ' - ' . self::$post->name .
            '"');
        $this->render('core_success.tpl');

        LogData::addLog(Auth::$userinfo->pilotid, 'Added the airline "' . self::$post->code .
            ' - ' . self::$post->name . '"');
    }

    /**
     * Operations::edit_airline_post()
     * 
     * @return
     */
    protected function edit_airline_post() {
        self::$post->code = strtoupper(self::$post->code);

        if (self::$post->code == '' || self::$post->name == '') {
            $this->set('message', 'Code and name cannot be blank');
            $this->render('core_error.tpl');
        }

        $prevairline = OperationsData::GetAirlineByCode(self::$post->code);
        if ($prevairline && $prevairline->id != self::$post->id) {
            $this->set('message', 'This airline with this code already exists!');
            $this->render('core_error.tpl');
            return;
        }

        if (isset(self::$post->enabled)) $enabled = true;
        else  $enabled = false;

        OperationsData::EditAirline(self::$post->id, self::$post->code, self::$post->name,
            $enabled);

        if (DB::errno() != 0) {
            $this->set('message', 'There was an error editing the airline');
            $this->render('core_error.tpl');
            return false;
        }

        $this->set('message', 'Edited the airline "' . self::$post->code . ' - ' . self::$post->name .
            '"');
        $this->render('core_success.tpl');

        LogData::addLog(Auth::$userinfo->pilotid, 'Edited the airline "' . self::$post->code .
            ' - ' . self::$post->name . '"');
    }

    /**
     * Operations::add_aircraft_post()
     * 
     * @return
     */
    protected function add_aircraft_post() {
        
        if (self::$post->icao == '' || self::$post->name == '' || self::$post->fullname ==
            '' || self::$post->registration == '') {
            $this->set('message',
                'You must enter the ICAO, name, full name and the registration.');
            $this->render('core_error.tpl');
            return;
        }

        if (self::$post->enabled == '1') self::$post->enabled = true;
        else  self::$post->enabled = false;

        # Check aircraft registration, make sure it's not a duplicate

        $ac = OperationsData::GetAircraftByReg(self::$post->registration);
        if ($ac) {
            $this->set('message', 'The aircraft registration must be unique');
            $this->render('core_error.tpl');
            return;
        }

        $data = array(
            'icao' => self::$post->icao, 
            'name' => self::$post->name,
            'fullname' => self::$post->fullname, 
            'registration' => self::$post->registration,
            'downloadlink' => self::$post->downloadlink, 
            'imagelink' => self::$post->imagelink,
            'range' => self::$post->range, 
            'weight' => self::$post->weight, 
            'cruise' => self::$post->cruise,
            'maxpax' => self::$post->maxpax, 
            'maxcargo' => self::$post->maxcargo, 
            'minrank' => self::$post->minrank, 
            'enabled' => self::$post->enabled
            );

        OperationsData::AddAircraft($data);

        if (DB::errno() != 0) {
            if (DB::$errno == 1062) // Duplicate entry
 
                $this->set('message', 'This aircraft already exists');
            else  $this->set('message', 'There was an error adding the aircraft');

            $this->render('core_error.tpl');
            return false;
        }

        $this->set('message', 'The aircraft has been added');
        $this->render('core_success.tpl');

        LogData::addLog(Auth::$userinfo->pilotid, 'Added the aircraft "' . self::$post->name .
            ' - ' . self::$post->registration . '"');
    }

    /**
     * Operations::edit_aircraft_post()
     * 
     * @return
     */
    protected function edit_aircraft_post() {
        if (self::$post->id == '') {
            $this->set('message', 'Invalid ID specified');
            $this->render('core_error.tpl');
            return;
        }

        if (self::$post->icao == '' || self::$post->name == '' || self::$post->fullname ==
            '' || self::$post->registration == '') {
            $this->set('message',
                'You must enter the ICAO, name, full name, and registration');
            $this->render('core_error.tpl');
            return;
        }

        $ac = OperationsData::CheckRegDupe(self::$post->id, self::$post->registration);
        if ($ac) {
            $this->set('message',
                'This registration is already assigned to another active aircraft');
            $this->render('core_error.tpl');
            return;
        }

        if (self::$post->enabled == '1') self::$post->enabled = true;
        else  self::$post->enabled = false;

        $data = array('id' => self::$post->id, 'icao' => self::$post->icao, 'name' => self::$post->name,
            'fullname' => self::$post->fullname, 'registration' => self::$post->registration,
            'downloadlink' => self::$post->downloadlink, 'imagelink' => self::$post->imagelink,
            'range' => self::$post->range, 'weight' => self::$post->weight, 'cruise' => self::$post->cruise,
            'maxpax' => self::$post->maxpax, 'maxcargo' => self::$post->maxcargo, 'minrank' =>
            self::$post->minrank, 'enabled' => self::$post->enabled);

        OperationsData::EditAircraft($data);

        if (DB::errno() != 0) {
            $this->set('message', 'There was an error editing the aircraft');
            $this->render('core_error.tpl');
            return;
        }

        LogData::addLog(Auth::$userinfo->pilotid, 'Edited the aircraft "' . self::$post->name .
            ' - ' . self::$post->registration . '"');

        $this->set('message', 'The aircraft "' . self::$post->registration .
            '" has been edited');
        $this->render('core_success.tpl');
    }

    /**
     * Operations::add_airport_post()
     * 
     * @return
     */
    protected function add_airport_post() {

        if (self::$post->icao == '' || self::$post->name == '' || self::$post->country ==
            '' || self::$post->lat == '' || self::$post->lng == '') {
            $this->set('message', 'Some fields were blank!');
            $this->render('core_error.tpl');
            return;
        }

        if (self::$post->hub == 'true') self::$post->hub = true;
        else  self::$post->hub = false;

        $data = array('icao' => self::$post->icao, 'name' => self::$post->name,
            'country' => self::$post->country, 'lat' => self::$post->lat, 'lng' => self::$post->lng,
            'hub' => self::$post->hub, 'chartlink' => self::$post->chartlink, 'fuelprice' =>
            self::$post->fuelprice);

        OperationsData::AddAirport($data);

        if (DB::errno() != 0) {
            if (DB::$errno == 1062) // Duplicate entry
 
                $this->set('message', 'This airport has already been added');
            else  $this->set('message', 'There was an error adding the airport');

            $this->render('core_error.tpl');
            return;
        }

        /*$this->set('message', 'The airport has been added');
        $this->render('core_success.tpl');*/

        LogData::addLog(Auth::$userinfo->pilotid, 'Added the airport "' . self::$post->icao .
            ' - ' . self::$post->name . '"');
    }

    /**
     * Operations::edit_airport_post()
     * 
     * @return
     */
    protected function edit_airport_post() {
        if (self::$post->icao == '' || self::$post->name == '' || self::$post->country ==
            '' || self::$post->lat == '' || self::$post->lng == '') {
            $this->set('message', 'Some fields were blank!');
            $this->render('core_message.tpl');
            return;
        }

        if (self::$post->hub == 'true') self::$post->hub = true;
        else  self::$post->hub = false;


        $data = array('icao' => self::$post->icao, 'name' => self::$post->name,
            'country' => self::$post->country, 'lat' => self::$post->lat, 'lng' => self::$post->lng,
            'hub' => self::$post->hub, 'chartlink' => self::$post->chartlink, 'fuelprice' =>
            self::$post->fuelprice);

        OperationsData::editAirport($data);

        if (DB::errno() != 0) {
            $this->set('message', 'There was an error adding the airport: ' . DB::$error);

            $this->render('core_error.tpl');
            return;
        }

        $this->set('message', '"' . self::$post->icao . '" has been edited');
        $this->render('core_success.tpl');

        LogData::addLog(Auth::$userinfo->pilotid, 'Edited the airport "' . self::$post->icao .
            ' - ' . self::$post->name . '"');
    }

    /**
     * Operations::add_schedule_post()
     * 
     * @return
     */
    protected function add_schedule_post() {
        
        if (self::$post->code == '' || self::$post->flightnum == '' || self::$post->deptime ==
            '' || self::$post->arrtime == '' || self::$post->depicao == '' || self::$post->arricao ==
            '') {
            $this->set('message', 'All of the fields must be filled out');
            $this->render('core_error.tpl');

            return;
        }

        # Check if the schedule exists
        $sched = SchedulesData::getScheduleByFlight(self::$post->code, self::$post->flightnum);
        if (is_object($sched)) {
            $this->set('message', 'This schedule already exists!');
            $this->render('core_error.tpl');

            return;
        }

        $enabled = (self::$post->enabled == 'on') ? true : false;

        # Check the distance
        if (self::$post->distance == '' || self::$post->distance == 0) {
            self::$post->distance = OperationsData::getAirportDistance(self::$post->depicao,
                self::$post->arricao);
        }

        # Format the flight level
        self::$post->flightlevel = str_replace(',', '', self::$post->flightlevel);
        self::$post->flightlevel = str_replace(' ', '', self::$post->flightlevel);

        self::$post->route = strtoupper(self::$post->route);
        self::$post->route = str_replace(self::$post->depicao, '', self::$post->route);
        self::$post->route = str_replace(self::$post->arricao, '', self::$post->route);
        self::$post->route = str_replace('SID', '', self::$post->route);
        self::$post->route = str_replace('STAR', '', self::$post->route);

        if(is_array($_POST['daysofweek'])) {
            $daysofweek = implode('', $_POST['daysofweek']);
        } else {
            $daysofweek = '0123456'; # default activate for all days
        }
        
        if (is_array($_POST['week1'])) {
            $week1 = implode('', $_POST['week1']);
        } else {
            $week1 = '';
        }

        if (is_array($_POST['week2'])) {
            $week2 = implode('', $_POST['week2']);
        } else {
            $week2 = '';
        }

        if (is_array($_POST['week3'])) {
            $week3 = implode('', $_POST['week3']);
        } else {
            $week3 = '';
        }

        if (is_array($_POST['week4'])) {
            $week4 = implode('', $_POST['week4']);
        } else {
            $week4 = '';
        }
        
        $data = array(
            'code' => self::$post->code, 
            'flightnum' => self::$post->flightnum,
            'depicao' => self::$post->depicao, 
            'arricao' => self::$post->arricao, 
            'route' => self::$post->route, 
            'aircraft' => self::$post->aircraft, 
            'flightlevel' => self::$post->flightlevel,
            'distance' => self::$post->distance, 
            'deptime' => self::$post->deptime,
            'arrtime' => self::$post->arrtime, 
            'flighttime' => self::$post->flighttime,
            'daysofweek' => $daysofweek, 
            'week1' => $week1, 'week2' => $week2, 'week3' => $week3, 'week4' => $week4, 
            'price' => self::$post->price, 
            'payforflight' => self::$post->payforflight,
            'flighttype' => self::$post->flighttype, 
            'notes' => self::$post->notes,
            'enabled' => $enabled
        );

        # Add it in
        $ret = SchedulesData::AddSchedule($data);

        if (DB::errno() != 0 && $ret == false) {
            $this->set('message',
                'There was an error adding the schedule, already exists DB error: ' . DB::error
                ());
            $this->render('core_error.tpl');
            return;
        }

        $this->set('message', 'The schedule "' . self::$post->code . self::$post->flightnum .
            '" has been added');
        $this->render('core_success.tpl');

        LogData::addLog(Auth::$userinfo->pilotid, 'Added schedule "' . self::$post->code .
            self::$post->flightnum . '"');
    }

    /**
     * Operations::edit_schedule_post()
     * 
     * @return
     */
    protected function edit_schedule_post() {
        if (self::$post->code == '' || self::$post->flightnum == '' || self::$post->deptime ==
            '' || self::$post->arrtime == '' || self::$post->depicao == '' || self::$post->arricao ==
            '') {
            $this->set('message', 'All of the fields must be filled out');
            $this->render('core_error.tpl');

            return;
        }

        $enabled = (self::$post->enabled == 'on') ? true : false;
        self::$post->route = strtoupper(self::$post->route);

        # Format the flight level
        self::$post->flightlevel = str_replace(',', '', self::$post->flightlevel);
        self::$post->flightlevel = str_replace(' ', '', self::$post->flightlevel);

        # Clear anything invalid out of the route
        self::$post->route = strtoupper(self::$post->route);
        self::$post->route = str_replace(self::$post->depicao, '', self::$post->route);
        self::$post->route = str_replace(self::$post->arricao, '', self::$post->route);
        self::$post->route = str_replace('SID', '', self::$post->route);
        self::$post->route = str_replace('STAR', '', self::$post->route);

        if(is_array($_POST['daysofweek'])) {
            $daysofweek = implode('', $_POST['daysofweek']);
        } else {
            $daysofweek = '0123456'; # default activate for all days
        }
        #var_dump($_POST);
        if (is_array($_POST['week1'])) {
            $week1 = implode('', $_POST['week1']);
        } else {
            $week1 = '';
        }

        if (is_array($_POST['week2'])) {
            $week2 = implode('', $_POST['week2']);
        } else {
            $week2 = '';
        }

        if (is_array($_POST['week3'])) {
            $week3 = implode('', $_POST['week3']);
        } else {
            $week3 = '';
        }

        if (is_array($_POST['week4'])) {
            $week4 = implode('', $_POST['week4']);
        } else {
            $week4 = '';
        }

        $data = array(
            'code' => self::$post->code, 
            'flightnum' => self::$post->flightnum,
            'depicao' => self::$post->depicao, 
            'arricao' => self::$post->arricao, 
            'route' => self::$post->route, 
            'aircraft' => self::$post->aircraft, 
            'flightlevel' => self::$post->flightlevel,
            'distance' => self::$post->distance, 
            'deptime' => self::$post->deptime,
            'arrtime' => self::$post->arrtime, 
            'flighttime' => self::$post->flighttime,
            'daysofweek' => $daysofweek, 
            'week1' => $week1, 'week2' => $week2, 'week3' => $week3, 'week4' => $week4, 
            'price' => self::$post->price, 'payforflight' => self::$post->payforflight,
            'flighttype' => self::$post->flighttype, 'notes' => self::$post->notes,
            'enabled' => $enabled
        );

        $val = SchedulesData::editScheduleFields(self::$post->id, $data);
        if (!$val) {
            $this->set('message', 'There was an error editing the schedule: ' . DB::error());
            $this->render('core_error.tpl');
            return;
        }

        # Parse the route:
        SchedulesData::getRouteDetails(self::$post->id, self::$post->route);

        $this->set('message', 'The schedule "' . self::$post->code . self::$post->flightnum .
            '" has been edited');
        $this->render('core_success.tpl');

        LogData::addLog(Auth::$userinfo->pilotid, 'Edited schedule "' . self::$post->code .
            self::$post->flightnum . '"');
    }

    /**
     * Operations::delete_schedule_post()
     * 
     * @return
     */
    protected function delete_schedule_post() {

        $schedule = SchedulesData::findSchedules(array('s.id' => self::$post->id));
        SchedulesData::DeleteSchedule(self::$post->id);

        $params = array();
        if (DB::errno() != 0) {
            $params['status'] = 'There was an error deleting the schedule';
            $params['error'] = DB::error();
            echo json_encode($params);
            return;
        }

        $params['status'] = 'ok';
        echo json_encode($params);

        LogData::addLog(Auth::$userinfo->pilotid, 'Deleted schedule "' . $schedule->code .
            $schedule->flightnum . '"');
    }
}
