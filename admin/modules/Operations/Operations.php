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
        switch ($this->controller->function) {
            case 'airlines':
                $this->set('sidebar', 'sidebar_airlines.php');
                break;
            case 'addaircraft':
            case 'aircraft':
                $this->set('sidebar', 'sidebar_aircraft.php');
                break;
            case 'airports':
                $this->set('sidebar', 'sidebar_airports.php');
                break;
            case '':
            case 'addschedule':
            case 'activeschedules':
            case 'inactiveschedules':
            case 'schedules':
                $this->set('sidebar', 'sidebar_schedules.php');
                break;
            case 'editschedule':
                $this->set('sidebar', 'sidebar_editschedule.php');
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
        
        if ($this->get->type === 'pirep') {
            $data = PIREPData::getReportDetails($this->get->id);
        } elseif ($this->get->type === 'schedule') {
            $data = SchedulesData::getScheduleDetailed($this->get->id);
        } elseif ($this->get->type === 'preview') {

            $data = new stdClass();

            $depicao = OperationsData::getAirportInfo($this->get->depicao);
            $arricao = OperationsData::getAirportInfo($this->get->arricao);
            
            $data->deplat = $depicao->lat;
            $data->deplng = $depicao->lng;
            $data->depname = $depicao->name;

            $data->arrlat = $arricao->lat;
            $data->arrlng = $arricao->lng;
            $data->arrname = $arricao->name;

            $data->route = $this->get->route;

            unset($depicao);
            unset($arricao);

            $data->route_details = NavData::parseRoute($data);
        }

        $this->set('mapdata', $data);
        $this->render('route_map.php');
    }

    /**
     * Operations::addaircraft()
     * 
     * @return
     */
    public function addaircraft() {
        $this->checkPermission(EDIT_FLEET);
        
        $this->set('title', 'Add Aircraft');
        $this->set('action', 'addaircraft');
        $this->set('allranks', RanksData::getAllRanks());
        $this->render('ops_aircraftform.php');
    }

    /**
     * Operations::editaircraft()
     * 
     * @return
     */
    public function editaircraft() {
        $this->checkPermission(EDIT_FLEET);
        $id = $this->get->id;

        $this->set('aircraft', OperationsData::GetAircraftInfo($id));
        $this->set('title', 'Edit Aircraft');
        $this->set('action', 'editaircraft');
        $this->set('allranks', RanksData::getAllRanks());
        $this->render('ops_aircraftform.php');
    }

    /**
     * Operations::addairline()
     * 
     * @return
     */
    public function addairline() {
        $this->checkPermission(EDIT_AIRLINES);
        $this->set('title', 'Add Airline');
        $this->set('action', 'addairline');
        $this->render('ops_airlineform.php');
    }

    /**
     * Operations::editairline()
     * 
     * @return
     */
    public function editairline() {
        $this->checkPermission(EDIT_AIRLINES);
        $this->set('title', 'Edit Airline');
        $this->set('action', 'editairline');
        $this->set('airline', OperationsData::GetAirlineByID($this->get->id));

        $this->render('ops_airlineform.php');
    }

    /**
     * Operations::calculatedistance()
     * 
     * @param string $depicao
     * @param string $arricao
     * @return
     */
    public function calculatedistance($depicao = '', $arricao = '') {
        
        if ($depicao == '') $depicao = $this->get->depicao;
        if ($arricao == '') $arricao = $this->get->arricao;

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
        
        $results = OperationsData::searchAirport($this->get->term);

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
        $this->checkPermission(EDIT_AIRLINES);
        if (isset($this->post->action)) {
            if ($this->post->action == 'addairline') {
                $this->add_airline_post();
            } elseif ($this->post->action == 'editairline') {
                $this->edit_airline_post();
            }
        }

        $this->set('allairlines', OperationsData::GetAllAirlines());
        $this->render('ops_airlineslist.php');
    }

    /**
     * Operations::aircraft()
     * 
     * @return
     */
    public function aircraft() {
        $this->checkPermission(EDIT_FLEET);
        /* If they're adding an aircraft, go through this pain
        */
        switch ($this->post->action) {

            case 'addaircraft':
                $this->add_aircraft_post();
                break;

            case 'editaircraft':
                $this->edit_aircraft_post();
                break;
        }

        $this->set('allaircraft', OperationsData::GetAllAircraft());
        $this->render('ops_aircraftlist.php');
    }

    /**
     * Operations::addairport()
     * 
     * @return
     */
    public function addairport() {
        $this->checkPermission(EDIT_SCHEDULES);
        $this->set('title', 'Add Airport');
        $this->set('action', 'addairport');

        $this->render('ops_airportform.php');
    }

    /**
     * Operations::editairport()
     * 
     * @return
     */
    public function editairport() {
        $this->set('title', 'Edit Airport');
        $this->set('action', 'editairport');
        $this->set('airport', OperationsData::GetAirportInfo($this->get->icao));

        $this->render('ops_airportform.php');
    }

    /**
     * Operations::airports()
     * 
     * @return
     */
    public function airports() {
        
        /* If they're adding an airport, go through this pain
        */
        if (isset($this->post->action)) {
            switch ($this->post->action) {
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
        $this->render('ops_airportlist.php');
    }

    /**
     * Operations::airportgrid()
     * 
     * @return
     */
    public function airportgrid() {
        
        $page = $this->get->page; // get the requested page
        $limit = $this->get->rows; // get how many rows we want to have into the grid
        $sidx = $this->get->sidx; // get index row - i.e. user click to sort
        $sord = $this->get->sord; // get the direction
        if (!$sidx) $sidx = 1;

        # http://dev.phpvms.net/admin/action.php/operations/
        # ?_search=true&nd=1270940867171&rows=20&page=1&sidx=flightnum&sord=asc&searchField=code&searchString=TAY&searchOper=eq

        /* Do the search using jqGrid */
        $where = array();
        if ($this->get->_search == 'true') {

            $searchstr = jqgrid::strip($this->get->filters);
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
        $this->checkPermission(EDIT_SCHEDULES);
        $this->set('title', 'Add Schedule');
        $this->set('action', 'addschedule');

        if ($this->get->reverse == '1') {
            $schedule = SchedulesData::GetSchedule($this->get->id);
            
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

        $this->render('ops_scheduleform.php');
    }

    /**
     * Operations::editschedule()
     * 
     * @return
     */
    public function editschedule() {
        $this->checkPermission(EDIT_SCHEDULES);
        $id = $this->get->id;

        $this->set('title', 'Edit Schedule');
        $this->set('schedule', SchedulesData::GetSchedule($id));

        $this->set('action', 'editschedule');

        $this->set('allairlines', OperationsData::GetAllAirlines());
        $this->set('allaircraft', OperationsData::GetAllAircraft());
        $this->set('allairports', OperationsData::GetAllAirports());
        $this->set('flighttypes', Config::Get('FLIGHT_TYPES'));

        $this->render('ops_scheduleform.php');
    }

    /**
     * Operations::activeschedules()
     * 
     * @return
     */
    public function activeschedules() {
        $this->checkPermission(EDIT_SCHEDULES);
        $this->schedules('activeschedules');
    }

    /**
     * Operations::inactiveschedules()
     * 
     * @return
     */
    public function inactiveschedules() {
        $this->checkPermission(EDIT_SCHEDULES);
        $this->schedules('inactiveschedules');
    }

    /**
     * Operations::schedulegrid()
     * 
     * @return
     */
    public function schedulegrid() {
        $this->checkPermission(EDIT_SCHEDULES);
        $page = $this->get->page; // get the requested page
        $limit = $this->get->rows; // get how many rows we want to have into the grid
        $sidx = $this->get->sidx; // get index row - i.e. user click to sort
        $sord = $this->get->sord; // get the direction
        if (!$sidx) $sidx = 1;

        # http://dev.phpvms.net/admin/action.php/operations/
        # ?_search=true&nd=1270940867171&rows=20&page=1&sidx=flightnum&sord=asc&searchField=code&searchString=TAY&searchOper=eq

        /* Do the search using jqGrid */
        $where = array();
        if ($this->get->_search == 'true') {
            $searchstr = jqgrid::strip($this->get->filters);
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
        $this->checkPermission(EDIT_SCHEDULES);
        /* These are loaded in popup box */
        if ($this->get->action == 'viewroute') {
            $id = $this->get->id;
            return;
        }


        if ($this->get->action == 'filter') {

            $this->set('title', 'Filtered Schedules');

            if ($this->get->type == 'flightnum') {
                $params = array('s.flightnum' => $this->get->query);
            } elseif ($this->get->type == 'code') {
                $params = array('s.code' => $this->get->query);
            } elseif ($this->get->type == 'aircraft') {
                $params = array('a.name' => $this->get->query);
            } elseif ($this->get->type == 'depapt') {
                $params = array('s.depicao' => $this->get->query);
            } elseif ($this->get->type == 'arrapt') {
                $params = array('s.arricao' => $this->get->query);
            }

            // Filter or don't filter enabled/disabled flights
            if (isset($this->get->enabled) && $this->get->enabled != 'all') {
                $params['s.enabled'] = $this->get->enabled;
            }

            $this->set('schedules', SchedulesData::findSchedules($params));
            $this->render('ops_schedules.php');
            return;
        }

        switch ($this->post->action) {
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

        if (!isset($this->get->start) || $this->get->start == '') {
            $this->get->start = 0;
        }

        $num_per_page = 20;
        $start = $num_per_page * $this->get->start;

        if ($type == 'schedules' || $type == 'activeschedules') {
            $params = array('s.enabled' => 1);
            $schedules = SchedulesData::findSchedules($params, $num_per_page, $start);

            $this->set('title', 'Viewing Active Schedules');
            $this->set('schedules', $schedules);

            if (count($schedules) >= $num_per_page) {

                $this->set('paginate', true);
                $this->set('start', $this->get->start + 1);

                if ($this->get->start - 1 > 0) {

                    $prev = $this->get->start - 1;
                    if ($prev == '') $prev = 0;

                    $this->set('prev', intval($prev));
                }
            }
        } else {
            $this->set('title', 'Viewing Inactive Schedules');
            $this->set('schedules', SchedulesData::findSchedules(array('s.enabled' => 0)));
        }

        $this->render('ops_schedules.php');
    }

    /**
     * Operations::add_airline_post()
     * 
     * @return
     */
    protected function add_airline_post() {
        $this->checkPermission(EDIT_AIRLINES);
        $this->post->code = strtoupper($this->post->code);

        if ($this->post->code == '' || $this->post->name == '') {
            $this->set('message', 'You must fill out all of the fields');
            $this->render('core_error.php');
            return;
        }

        if (OperationsData::GetAirlineByCode($this->post->code)) {
            $this->set('message', 'An airline with this code already exists!');
            $this->render('core_error.php');
            return;
        }

        OperationsData::AddAirline($this->post->code, $this->post->name);

        if (DB::errno() != 0) {
            if (DB::errno() == 1062) // Duplicate entry
 
                $this->set('message', 'This airline has already been added');
            else  $this->set('message', 'There was an error adding the airline');

            $this->render('core_error.php');
            return;
        }

        $this->set('message', 'Added the airline "' . $this->post->code . ' - ' . $this->post->name .
            '"');
        $this->render('core_success.php');

        LogData::addLog(Auth::$userinfo->pilotid, 'Added the airline "' . $this->post->code .
            ' - ' . $this->post->name . '"');
    }

    /**
     * Operations::edit_airline_post()
     * 
     * @return
     */
    protected function edit_airline_post() {
        $this->checkPermission(EDIT_AIRLINES);
        $this->post->code = strtoupper($this->post->code);

        if ($this->post->code == '' || $this->post->name == '') {
            $this->set('message', 'Code and name cannot be blank');
            $this->render('core_error.php');
        }

        $prevairline = OperationsData::GetAirlineByCode($this->post->code);
        if ($prevairline && $prevairline->id != $this->post->id) {
            $this->set('message', 'This airline with this code already exists!');
            $this->render('core_error.php');
            return;
        }

        if (isset($this->post->enabled)) $enabled = true;
        else  $enabled = false;

        OperationsData::EditAirline($this->post->id, $this->post->code, $this->post->name,
            $enabled);

        if (DB::errno() != 0) {
            $this->set('message', 'There was an error editing the airline');
            $this->render('core_error.php');
            return false;
        }

        $this->set('message', 'Edited the airline "' . $this->post->code . ' - ' . $this->post->name .
            '"');
        $this->render('core_success.php');

        LogData::addLog(Auth::$userinfo->pilotid, 'Edited the airline "' . $this->post->code .
            ' - ' . $this->post->name . '"');
    }

    /**
     * Operations::add_aircraft_post()
     * 
     * @return
     */
    protected function add_aircraft_post() {
        $this->checkPermission(EDIT_FLEET);
        
        if ($this->post->icao == '' || $this->post->name == '' || $this->post->fullname ==
            '' || $this->post->registration == '') {
            $this->set('message',
                'You must enter the ICAO, name, full name and the registration.');
            $this->render('core_error.php');
            return;
        }

        if ($this->post->enabled == '1') $this->post->enabled = true;
        else  $this->post->enabled = false;

        # Check aircraft registration, make sure it's not a duplicate

        $ac = OperationsData::GetAircraftByReg($this->post->registration);
        if ($ac) {
            $this->set('message', 'The aircraft registration must be unique');
            $this->render('core_error.php');
            return;
        }

        $data = array(
            'icao' => $this->post->icao, 
            'name' => $this->post->name,
            'fullname' => $this->post->fullname, 
            'registration' => $this->post->registration,
            'downloadlink' => $this->post->downloadlink, 
            'imagelink' => $this->post->imagelink,
            'range' => $this->post->range, 
            'weight' => $this->post->weight, 
            'cruise' => $this->post->cruise,
            'maxpax' => $this->post->maxpax, 
            'maxcargo' => $this->post->maxcargo, 
            'minrank' => $this->post->minrank, 
            'enabled' => $this->post->enabled
            );

        OperationsData::AddAircraft($data);

        if (DB::errno() != 0) {
            if (DB::$errno == 1062) // Duplicate entry
 
                $this->set('message', 'This aircraft already exists');
            else  $this->set('message', 'There was an error adding the aircraft');

            $this->render('core_error.php');
            return false;
        }

        $this->set('message', 'The aircraft has been added');
        $this->render('core_success.php');

        LogData::addLog(Auth::$userinfo->pilotid, 'Added the aircraft "' . $this->post->name .
            ' - ' . $this->post->registration . '"');
    }

    /**
     * Operations::edit_aircraft_post()
     * 
     * @return
     */
    protected function edit_aircraft_post() {
        $this->checkPermission(EDIT_FLEET);
        if ($this->post->id == '') {
            $this->set('message', 'Invalid ID specified');
            $this->render('core_error.php');
            return;
        }

        if ($this->post->icao == '' || $this->post->name == '' || $this->post->fullname ==
            '' || $this->post->registration == '') {
            $this->set('message',
                'You must enter the ICAO, name, full name, and registration');
            $this->render('core_error.php');
            return;
        }

        $ac = OperationsData::CheckRegDupe($this->post->id, $this->post->registration);
        if ($ac) {
            $this->set('message',
                'This registration is already assigned to another active aircraft');
            $this->render('core_error.php');
            return;
        }

        if ($this->post->enabled == '1') $this->post->enabled = true;
        else  $this->post->enabled = false;

        $data = array('id' => $this->post->id, 'icao' => $this->post->icao, 'name' => $this->post->name,
            'fullname' => $this->post->fullname, 'registration' => $this->post->registration,
            'downloadlink' => $this->post->downloadlink, 'imagelink' => $this->post->imagelink,
            'range' => $this->post->range, 'weight' => $this->post->weight, 'cruise' => $this->post->cruise,
            'maxpax' => $this->post->maxpax, 'maxcargo' => $this->post->maxcargo, 'minrank' =>
            $this->post->minrank, 'enabled' => $this->post->enabled);

        OperationsData::EditAircraft($data);

        if (DB::errno() != 0) {
            $this->set('message', 'There was an error editing the aircraft');
            $this->render('core_error.php');
            return;
        }

        LogData::addLog(Auth::$userinfo->pilotid, 'Edited the aircraft "' . $this->post->name .
            ' - ' . $this->post->registration . '"');

        $this->set('message', 'The aircraft "' . $this->post->registration .
            '" has been edited');
        $this->render('core_success.php');
    }

    /**
     * Operations::add_airport_post()
     * 
     * @return
     */
    protected function add_airport_post() {

        if ($this->post->icao == '' || $this->post->name == '' || $this->post->country ==
            '' || $this->post->lat == '' || $this->post->lng == '') {
            $this->set('message', 'Some fields were blank!');
            $this->render('core_error.php');
            return;
        }

        if ($this->post->hub == 'true') $this->post->hub = true;
        else  $this->post->hub = false;

        $data = array('icao' => $this->post->icao, 'name' => $this->post->name,
            'country' => $this->post->country, 'lat' => $this->post->lat, 'lng' => $this->post->lng,
            'hub' => $this->post->hub, 'chartlink' => $this->post->chartlink, 'fuelprice' =>
            $this->post->fuelprice);

        OperationsData::AddAirport($data);

        if (DB::errno() != 0) {
            if (DB::$errno == 1062) // Duplicate entry
 
                $this->set('message', 'This airport has already been added');
            else  $this->set('message', 'There was an error adding the airport');

            $this->render('core_error.php');
            return;
        }

        /*$this->set('message', 'The airport has been added');
        $this->render('core_success.php');*/

        LogData::addLog(Auth::$userinfo->pilotid, 'Added the airport "' . $this->post->icao .
            ' - ' . $this->post->name . '"');
    }

    /**
     * Operations::edit_airport_post()
     * 
     * @return
     */
    protected function edit_airport_post() {
        if ($this->post->icao == '' || $this->post->name == '' || $this->post->country ==
            '' || $this->post->lat == '' || $this->post->lng == '') {
            $this->set('message', 'Some fields were blank!');
            $this->render('core_message.php');
            return;
        }

        if ($this->post->hub == 'true') $this->post->hub = true;
        else  $this->post->hub = false;


        $data = array('icao' => $this->post->icao, 'name' => $this->post->name,
            'country' => $this->post->country, 'lat' => $this->post->lat, 'lng' => $this->post->lng,
            'hub' => $this->post->hub, 'chartlink' => $this->post->chartlink, 'fuelprice' =>
            $this->post->fuelprice);

        OperationsData::editAirport($data);

        if (DB::errno() != 0) {
            $this->set('message', 'There was an error adding the airport: ' . DB::$error);

            $this->render('core_error.php');
            return;
        }

        $this->set('message', '"' . $this->post->icao . '" has been edited');
        $this->render('core_success.php');

        LogData::addLog(Auth::$userinfo->pilotid, 'Edited the airport "' . $this->post->icao .
            ' - ' . $this->post->name . '"');
    }

    /**
     * Operations::add_schedule_post()
     * 
     * @return
     */
    protected function add_schedule_post() {
        $this->checkPermission(EDIT_SCHEDULES);
        
        if ($this->post->code == '' || $this->post->flightnum == '' || $this->post->deptime ==
            '' || $this->post->arrtime == '' || $this->post->depicao == '' || $this->post->arricao ==
            '') {
            $this->set('message', 'All of the fields must be filled out');
            $this->render('core_error.php');

            return;
        }

        # Check if the schedule exists
        $sched = SchedulesData::getScheduleByFlight($this->post->code, $this->post->flightnum);
        if (is_object($sched)) {
            $this->set('message', 'This schedule already exists!');
            $this->render('core_error.php');

            return;
        }

        $enabled = ($this->post->enabled == 'on') ? true : false;

        # Check the distance
        if ($this->post->distance == '' || $this->post->distance == 0) {
            $this->post->distance = OperationsData::getAirportDistance($this->post->depicao,
                $this->post->arricao);
        }

        # Format the flight level
        $this->post->flightlevel = str_replace(',', '', $this->post->flightlevel);
        $this->post->flightlevel = str_replace(' ', '', $this->post->flightlevel);

        $this->post->route = strtoupper($this->post->route);
        $this->post->route = str_replace($this->post->depicao, '', $this->post->route);
        $this->post->route = str_replace($this->post->arricao, '', $this->post->route);
        $this->post->route = str_replace('SID', '', $this->post->route);
        $this->post->route = str_replace('STAR', '', $this->post->route);

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
            'code' => $this->post->code, 
            'flightnum' => $this->post->flightnum,
            'depicao' => $this->post->depicao, 
            'arricao' => $this->post->arricao, 
            'route' => $this->post->route, 
            'aircraft' => $this->post->aircraft, 
            'flightlevel' => $this->post->flightlevel,
            'distance' => $this->post->distance, 
            'deptime' => $this->post->deptime,
            'arrtime' => $this->post->arrtime, 
            'flighttime' => $this->post->flighttime,
            'daysofweek' => $daysofweek, 
            'week1' => $week1, 'week2' => $week2, 'week3' => $week3, 'week4' => $week4, 
            'price' => $this->post->price, 
            'payforflight' => $this->post->payforflight,
            'flighttype' => $this->post->flighttype, 
            'notes' => $this->post->notes,
            'enabled' => $enabled
        );

        # Add it in
        $ret = SchedulesData::AddSchedule($data);

        if (DB::errno() != 0 && $ret == false) {
            $this->set('message',
                'There was an error adding the schedule, already exists DB error: ' . DB::error
                ());
            $this->render('core_error.php');
            return;
        }

        $this->set('message', 'The schedule "' . $this->post->code . $this->post->flightnum .
            '" has been added');
        $this->render('core_success.php');

        LogData::addLog(Auth::$userinfo->pilotid, 'Added schedule "' . $this->post->code .
            $this->post->flightnum . '"');
    }

    /**
     * Operations::edit_schedule_post()
     * 
     * @return
     */
    protected function edit_schedule_post() {
        $this->checkPermission(EDIT_SCHEDULES);
        if ($this->post->code == '' || $this->post->flightnum == '' || $this->post->deptime ==
            '' || $this->post->arrtime == '' || $this->post->depicao == '' || $this->post->arricao ==
            '') {
            $this->set('message', 'All of the fields must be filled out');
            $this->render('core_error.php');

            return;
        }

        $enabled = ($this->post->enabled == 'on') ? true : false;
        $this->post->route = strtoupper($this->post->route);

        # Format the flight level
        $this->post->flightlevel = str_replace(',', '', $this->post->flightlevel);
        $this->post->flightlevel = str_replace(' ', '', $this->post->flightlevel);

        # Clear anything invalid out of the route
        $this->post->route = strtoupper($this->post->route);
        $this->post->route = str_replace($this->post->depicao, '', $this->post->route);
        $this->post->route = str_replace($this->post->arricao, '', $this->post->route);
        $this->post->route = str_replace('SID', '', $this->post->route);
        $this->post->route = str_replace('STAR', '', $this->post->route);

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
            'code' => $this->post->code, 
            'flightnum' => $this->post->flightnum,
            'depicao' => $this->post->depicao, 
            'arricao' => $this->post->arricao, 
            'route' => $this->post->route, 
            'aircraft' => $this->post->aircraft, 
            'flightlevel' => $this->post->flightlevel,
            'distance' => $this->post->distance, 
            'deptime' => $this->post->deptime,
            'arrtime' => $this->post->arrtime, 
            'flighttime' => $this->post->flighttime,
            'daysofweek' => $daysofweek, 
            'week1' => $week1, 'week2' => $week2, 'week3' => $week3, 'week4' => $week4, 
            'price' => $this->post->price, 'payforflight' => $this->post->payforflight,
            'flighttype' => $this->post->flighttype, 'notes' => $this->post->notes,
            'enabled' => $enabled
        );

        $val = SchedulesData::editScheduleFields($this->post->id, $data);
        if (!$val) {
            $this->set('message', 'There was an error editing the schedule: ' . DB::error());
            $this->render('core_error.php');
            return;
        }

        # Parse the route:
        SchedulesData::getRouteDetails($this->post->id, $this->post->route);

        $this->set('message', 'The schedule "' . $this->post->code . $this->post->flightnum .
            '" has been edited');
        $this->render('core_success.php');

        LogData::addLog(Auth::$userinfo->pilotid, 'Edited schedule "' . $this->post->code .
            $this->post->flightnum . '"');
    }

    /**
     * Operations::delete_schedule_post()
     * 
     * @return
     */
    protected function delete_schedule_post() {
        $this->checkPermission(EDIT_SCHEDULES);

        $schedule = SchedulesData::findSchedules(array('s.id' => $this->post->id));
        SchedulesData::DeleteSchedule($this->post->id);

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
