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
 * @package module_admin_pilotranks
 */

class PilotRanking extends CodonModule {
	public function HTMLHead() {
		switch ($this->controller->function) {
			case 'pilotranks':
			case 'calculateranks':
				$this->set('sidebar', 'sidebar_ranks.php');
				break;

			case 'awards':
				$this->set('sidebar', 'sidebar_awards.php');
				break;
		}
	}

	public function index() {
		$this->pilotranks();
	}

	public function pilotranks() {
        $this->checkPermission(EDIT_RANKS);
		switch ($this->post->action) {
			case 'addrank':
				$this->add_rank_post();
				break;
			case 'editrank':
				$this->edit_rank_post();
				break;

			case 'deleterank':

				$ret = RanksData::DeleteRank($this->post->id);

				echo json_encode(array('status' => 'ok'));

				return;
				break;
		}

		$this->set('ranks', RanksData::GetAllRanks());
		$this->render('ranks_allranks.php');
	}

	public function addrank() {
        $this->checkPermission(EDIT_RANKS);
		$this->set('title', 'Add Rank');
		$this->set('action', 'addrank');

		$this->render('ranks_rankform.php');
	}

	public function editrank() {
        $this->checkPermission(EDIT_RANKS);
		$this->set('title', 'Edit Rank');
		$this->set('action', 'editrank');
		$this->set('rank', RanksData::GetRankInfo($this->get->rankid));

		$this->render('ranks_rankform.php');
	}

	public function awards() {
        $this->checkPermission(EDIT_AWARDS);
		if (isset($this->post->action)) {
			switch ($this->post->action) {
				case 'addaward':
					$this->add_award_post();
					break;
				case 'editaward':
					$this->edit_award_post();
					break;
				case 'deleteaward':
					$ret = AwardsData::DeleteAward($this->post->id);
					LogData::addLog(Auth::$userinfo->pilotid, 'Deleted an award');

					echo json_encode(array('status' => 'ok'));
					return;
					break;
			}
		}

		$this->set('awards', AwardsData::GetAllAwards());
		$this->render('awards_allawards.php');
	}

	public function addaward() {
        $this->checkPermission(EDIT_AWARDS);

		$this->set('title', 'Add Award');
		$this->set('action', 'addaward');

		$this->render('awards_awardform.php');

	}

	public function editaward() {
        $this->checkPermission(EDIT_AWARDS);
		$this->set('title', 'Edit Award');
		$this->set('action', 'editaward');
		$this->set('award', AwardsData::GetAwardDetail($this->get->awardid));

		$this->render('awards_awardform.php');

	}

	/* Utility functions */

	protected function add_rank_post() {
        $this->checkPermission(EDIT_RANKS);

		if ($this->post->minhours == '' || $this->post->rank == '') {
			$this->set('message', 'Hours and Rank must be blank');
			$this->render('core_error.php');
			return;
		}

		if (!is_numeric($this->post->minhours)) {
			$this->set('message', 'The hours must be a number');
			$this->render('core_error.php');
			return;
		}

		$this->post->payrate = abs($this->post->payrate);

		$ret = RanksData::AddRank($this->post->rank, $this->post->minhours, $this->post->imageurl, $this->post->payrate);

		if (DB::errno() != 0) {
			$this->set('message', 'Error adding the rank: ' . DB::error());
			$this->render('core_error.php');
			return;
		}

		$this->set('message', 'Rank Added!');
		$this->render('core_success.php');

		LogData::addLog(Auth::$userinfo->pilotid, 'Added the rank "' . $this->post->rank . '"');
	}

	protected function edit_rank_post() {
        $this->checkPermission(EDIT_RANKS);
		if ($this->post->minhours == '' || $this->post->rank == '') {
			$this->set('message', 'Hours and Rank must be blank');
			$this->render('core_error.php');
			return;
		}

		if (!is_numeric($this->post->minhours)) {
			$this->set('message', 'The hours must be a number');
			$this->render('core_error.php');
			return;
		}

		$this->post->payrate = abs($this->post->payrate);

		$ret = RanksData::UpdateRank($this->post->rankid, $this->post->rank, $this->post->minhours, $this->post->rankimage, $this->post->payrate);

		if (DB::errno() != 0) {
			$this->set('message', 'Error updating the rank: ' . DB::error());
			$this->render('core_error.php');
			return;
		}

		$this->set('message', 'Rank Added!');
		$this->render('core_success.php');

		LogData::addLog(Auth::$userinfo->pilotid, 'Edited the rank "' . $this->post->rank . '"');
	}

	protected function add_award_post() {
        $this->checkPermission(EDIT_AWARDS);
		if ($this->post->name == '' || $this->post->image == '') {
			$this->set('message', 'The name and image must be entered');
			$this->render('core_error.php');
			return;
		}

		$ret = AwardsData::AddAward($this->post->name, $this->post->descrip, $this->post->image);

		$this->set('message', 'Award Added!');
		$this->render('core_success.php');

		LogData::addLog(Auth::$userinfo->pilotid, "Added the award \"{$this->post->name}\"");
	}

	protected function edit_award_post() {
        $this->checkPermission(EDIT_AWARDS);
		if ($this->post->name == '' || $this->post->image == '') {
			$this->set('message', 'The name and image must be entered');
			$this->render('core_error.php');
			return;
		}

		$ret = AwardsData::EditAward($this->post->awardid, $this->post->name, $this->post->descrip, $this->post->image);

		$this->set('message', 'Award Added!');
		$this->render('core_success.php');

		LogData::addLog(Auth::$userinfo->pilotid, 'Edited the award "' . $this->post->name . '"');
	}
}