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
 
class Pilots extends CodonModule
{
	
	public function index()
	{
		$pilotdata = array();
		
		// Get all of our hubs, and list pilots by hub
		$allhubs = OperationsData::GetAllHubs();
		
		if(!$allhubs) $allhubs = array();
		
		foreach($allhubs as $hub)
		{
			$pilotdata[$hub->icao]['title'] = $hub->name;
			$pilotdata[$hub->icao]['pilots'] = PilotData::findPilots(array('p.hub'=>$hub->icao));
		}
		
		$nohub = PilotData::findPilots(array('p.hub'=>''));
		if($nohub && count($nohub) > 0)
		{
			$pilotdata['nohub']['title'] = 'No Hub';
			$pilotdata['nohub']['pilots'] = $nohub;
		}

		$this->set('pilotdata', $pilotdata);
		$this->render('pilots_list.tpl');
	}
	
	public function reports($pilotid='')
	{
		if($pilotid == '')
		{
			$this->set('message', 'No pilot specified!');
			$this->render('core_error.tpl');
			return;
		}
		
		$this->set('pireps', PIREPData::GetAllReportsForPilot($pilotid));
		$this->render('pireps_viewall.tpl');
	}
	
	
	/* Stats stuff for charts */
	
	
	public function statsdaysdata($pilotid)
	{
		$data = PIREPData::getIntervalDataByDays(array('p.pilotid'=>$pilotid), 30);
		$this->create_line_graph('Past 30 days PIREPs', $data);
	}
	
	public function statsmonthsdata($pilotid)
	{
		$data = PIREPData::getIntervalDataByMonth(array('p.pilotid'=>$pilotid), 3);
		$this->create_line_graph('Monthly Flight Stats', $data);
	}
	
	public function statsaircraftdata($pilotid)
	{
		$data = StatsData::PilotAircraftFlownCounts($pilotid);
		if(!$data) $data = array();
		
		include CORE_LIB_PATH.'/php-ofc-library/open-flash-chart.php';
		
		$d = array();
		foreach($data as $ac)
		{
			OFCharts::add_data_set($ac->aircraft, floatval($ac->hours));
		}
		
		echo OFCharts::create_pie_graph('Aircraft Flown');
	}
	
	protected function create_line_graph($title, $data)
	{	
		if(!$data)
		{
			$data = array();
		}
				
		$bar_values = array();
		$bar_titles = array();
		foreach($data as $val)
		{
			
			$bar_titles[] = $val->ym;
			$bar_values[] = floatval($val->total);
		}
	
		OFCharts::add_data_set($bar_titles, $bar_values);
		echo OFCharts::create_area_graph($title);
	}
		
	public function RecentFrontPage($count = 5)
	{
		$this->set('pilots', PilotData::GetLatestPilots($count));
		$this->render('frontpage_recentpilots.tpl');
	}
}