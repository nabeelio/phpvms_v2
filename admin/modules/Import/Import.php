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

class Import extends CodonModule {

	public function HTMLHead() {
        switch ($this->controller->function) {
            case '':
            default:
            case 'processimport':
                $this->set('sidebar', 'sidebar_import.php');
                break;

            case 'importaircraft':
                $this->set('sidebar', 'sidebar_aircraft.php');
                break;
        }
    }

    public function index() {
        $this->render('import_form.php');
    }

    public function export() {
        $this->render('export_form.php');
    }

	/**
	 *
	 */
	public function exportairports() {

		$allairports = OperationsData::getAllAirports();

		header('Content-Type: text/plain');
		header('Content-Disposition: attachment; filename="airports.csv"');

		$fp = fopen('php://output', 'w');

		# Then write out all of the airports
		foreach ($allairports as $ap) {
			unset($ap->id);
			fputcsv($fp, (array) $ap, ',');
		}

		fclose($fp);
	}

	/**
	 *
	 */
    public function exportaircraft() {

        $allaircraft = OperationsData::getAllAircraft(false);

        # Get the column headers
        $headers = array();
        $dbcolumns = DB::get_cols();
        foreach ($dbcolumns as $col) {
            if ($col->name == 'id' || $col->name == 'minrank' || $col->name == 'ranklevel')
                continue;

            $headers[] = $col->name;
        }

        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="aircraft.csv"');

        $fp = fopen('php://output', 'w');

        # Write out the header which is the columns
        fputcsv($fp, $headers, ',');

        # Then write out all of the aircraft
        foreach ($allaircraft as $aircraft) {
            unset($aircraft->id);
            unset($aircraft->minrank);
            unset($aircraft->ranklevel);

            $aircraft = (array )$aircraft;

            fputcsv($fp, $aircraft, ',');
        }

        fclose($fp);
    }

    public function importaircraft() {

        if (!file_exists($_FILES['uploadedfile']['tmp_name'])) {
            $this->render('import_aircraftform.php');
            return;
        }

        echo '<h3>Processing Import</h3>';

        # Get the column headers
        $allaircraft = OperationsData::getAllAircraft(false);
        $headers = array();
        $dbcolumns = DB::get_cols();
        foreach ($dbcolumns as $col) {
            if ($col->name == 'id' || $col->name == 'minrank' || $col->name == 'ranklevel')
                continue;

            $headers[] = $col->name;
        }

        # Open the import file

        # Fix for bug VMS-325
        $temp_name = $_FILES['uploadedfile']['tmp_name'];
        $new_name = CACHE_PATH . $_FILES['uploadedfile']['name'];
        move_uploaded_file($temp_name, $new_name);

        $fp = fopen($new_name, 'r');
        if (isset($_POST['header']))
            $skip = true;

        $added = 0;
        $updated = 0;
        $total = 0;
        echo '<div style="overflow: auto; height: 400px; 
					border: 1px solid #666; margin-bottom: 20px; 
					padding: 5px; padding-top: 0px; padding-bottom: 20px;">';

        while ($fields = fgetcsv($fp, 1000, ',')) {
            // Skip the first line
            if ($skip == true) {
                $skip = false;
                continue;
            }

            # Map the read in values to the columns
            $aircraft = array();
            $aircraft = @array_combine($headers, $fields);

            if (empty($aircraft))
                continue;

            # Enabled or not
            if ($aircraft['enabled'] == '1') {
                $aircraft['enabled'] = true;
            } else {
                $aircraft['enabled'] = false;
            }

            # Get the rank ID
            $rank = RanksData::getRankByName($aircraft['rank']);
            $aircraft['minrank'] = $rank->rankid;
            unset($aircraft['rank']);

            # Does this aircraft exist?
            $ac_info = OperationsData::getAircraftByReg($aircraft['registration']);
            if ($ac_info) {
                echo "Editing {$aircraft['name']} - {$aircraft['registration']}<br>";
                $aircraft['id'] = $ac_info->id;
                OperationsData::editAircraft($aircraft);
                $updated++;
            } else {
                echo "Adding {$aircraft['name']} - {$aircraft['registration']}<br>";
                OperationsData::addAircraft($aircraft);
                $added++;
            }

            $total++;
        }

        unlink($new_name);

        echo "The import process is complete, added {$added} aircraft, updated {$updated}, for a total of {$total}<br />";
    }

    public function processexport() {
        $export = '';
        $all_schedules = SchedulesData::GetSchedules('', false);

        if (!$all_schedules) {
            echo 'No schedules found!';
            return;
        }

        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="schedules.csv"');

        $fp = fopen('php://output', 'w');

        $line = file_get_contents(SITE_ROOT . '/admin/lib/template.csv');
        fputcsv($fp, explode(',', $line));

        foreach ($all_schedules as $s) {
            $line = "{$s->code},{$s->flightnum},{$s->depicao},{$s->arricao}," . "{$s->route},{$s->registration},{$s->flightlevel},{$s->distance}," .
                "{$s->deptime}, {$s->arrtime}, {$s->flighttime}, {$s->notes}, " . "{$s->price}, {$s->flighttype}, {$s->daysofweek}, {$s->enabled}, {$s->week1}, {$s->week2}, {$s->week3}, {$s->week4}";

            fputcsv($fp, explode(',', $line));
        }

        fclose($fp);
    }

    public function processimport() {
        $this->checkPermission(IMPORT_SCHEDULES);
        echo '<h3>Processing Import</h3>';

        if (!file_exists($_FILES['uploadedfile']['tmp_name'])) {
            $this->set('message', 'File upload failed!');
            $this->render('core_error.php');
            return;
        }

        echo '<p><strong>DO NOT REFRESH OR STOP THIS PAGE</strong></p>';

        set_time_limit(270);
        $errs = array();
        $skip = false;


        # Fix for bug VMS-325
        $temp_name = $_FILES['uploadedfile']['tmp_name'];
        $new_name = CACHE_PATH . $_FILES['uploadedfile']['name'];
        move_uploaded_file($temp_name, $new_name);

        $fp = fopen($new_name, 'r');

        if (isset($_POST['header']))
            $skip = true;

        /* Delete all schedules before doing an import */
        if (isset($_POST['erase_routes'])) {
            SchedulesData::deleteAllSchedules();
        }


        $added = 0;
        $updated = 0;
        $total = 0;
        echo '<div style="overflow: auto; height: 400px; border: 1px solid #666; margin-bottom: 20px; padding: 5px; padding-top: 0px; padding-bottom: 20px;">';

        while ($fields = fgetcsv($fp, 1000, ',')) {
            // Skip the first line
            if ($skip == true) {
                $skip = false;
                continue;
            }

            // list fields:
            $code = $fields[0];
            $flightnum = $fields[1];
            $depicao = $fields[2];
            $arricao = $fields[3];
            $route = $fields[4];
            $aircraft = $fields[5];
            $flightlevel = $fields[6];
            $distance = $fields[7];
            $deptime = $fields[8];
            $arrtime = $fields[9];
            $flighttime = $fields[10];
            $notes = $fields[11];
            $price = $fields[12];
            $flighttype = $fields[13];
            $daysofweek = $fields[14];
            $enabled = $fields[15];
            $week1 = $fields[16];
            $week2 = $fields[17];
            $week3 = $fields[18];
            $week4 = $fields[19];

            if ($code == '') {
                continue;
            }

            // Check the code:
            if (!OperationsData::GetAirlineByCode($code)) {
                echo "Airline with code $code does not exist! Skipping...<br />";
                continue;
            }

            // Make sure airports exist:
            if (!($depapt = OperationsData::GetAirportInfo($depicao))) {
                $this->get_airport_info($depicao);
            }

            if (!($arrapt = OperationsData::GetAirportInfo($arricao))) {
                $this->get_airport_info($arricao);
            }

            # Check the aircraft
            $aircraft = trim($aircraft);
            $ac_info = OperationsData::GetAircraftByReg($aircraft);

            # If the aircraft doesn't exist, skip it
            if (!$ac_info) {
                echo 'Aircraft "' . $aircraft . '" does not exist! Skipping<br />';
                continue;
            }
            $ac = $ac_info->id;

            if ($flighttype == '') {
                $flighttype = 'P';
            }

            if ($daysofweek == '')
                $daysofweek = '0123456';

            // Replace a 7 (Sunday) with 0 (since PHP thinks 0 is Sunday)
            $daysofweek = str_replace('7', '0', $daysofweek);

            # Check the distance

            if ($distance == 0 || $distance == '') {
                $distance = OperationsData::getAirportDistance($depicao, $arricao);
            }

            $flighttype = strtoupper($flighttype);

            if ($enabled == '0')
                $enabled = false;
            else
                $enabled = true;

            # This is our 'struct' we're passing into the schedule function
            #	to add or edit it

            $data = array(
                'code' => $code, 'flightnum' => $flightnum, 'depicao' => $depicao,
                'arricao' => $arricao, 'route' => $route, 'aircraft' => $ac, 'flightlevel' => $flightlevel,
                'distance' => $distance, 'deptime' => $deptime, 'arrtime' => $arrtime,
                'flighttime' => $flighttime, 'daysofweek' => $daysofweek, 'notes' => $notes,
                'enabled' => $enabled, 'price' => $price, 'flighttype' => $flighttype,
                'week1' => $week1, 'week2' => $week2, 'week3' => $week3, 'week4' => $week4
            );

            # Check if the schedule exists:
            if (($schedinfo = SchedulesData::getScheduleByFlight($code, $flightnum))) {
                # Update the schedule instead
                $val = SchedulesData::updateScheduleFields($schedinfo->id, $data);
                $updated++;
            } else {
                # Add it
                $val = SchedulesData::addSchedule($data);
                $added++;
            }

            if ($val === false) {
                if (DB::errno() == 1216) {
                    echo "Error adding $code$flightnum: The airline code, airports, or aircraft does not exist";
                } else {
                    $error = (DB::error() != '') ? DB::error() : 'Route already exists';
                    echo "$code$flightnum was not added, reason: $error<br />";
                }

                echo '<br />';
            } else {
                $total++;
                echo "Imported {$code}{$flightnum} ({$depicao} to {$arricao})<br />";
            }
        }

        CentralData::send_schedules();

        echo "The import process is complete, added {$added} schedules, updated {$updated}, for a total of {$total}<br />";

        foreach ($errs as $error) {
            echo '&nbsp;&nbsp;&nbsp;&nbsp;' . $error . '<br />';
        }

        echo '</div>';

        unlink($new_name);
    }

    protected function get_airport_info($icao) {
        echo "ICAO $icao not added... retriving information: <br />";
        $aptinfo = OperationsData::RetrieveAirportInfo($icao);

        if ($aptinfo === false) {
            echo 'Could not retrieve information for ' . $icao . ', add it manually <br />';
        } else {
            echo "Found: $icao - " . $aptinfo->name . ' (' . $aptinfo->lat . ',' . $aptinfo->
                lng . '), airport added<br /><br />';

            return $aptinfo;
        }
    }
}
