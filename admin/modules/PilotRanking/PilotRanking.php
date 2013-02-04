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
		switch (self::$controller->function) {
			case 'pilotranks':
			case 'calculateranks':
				$this->set('sidebar', 'sidebar_ranks.tpl');
				break;

			case 'awards':
				$this->set('sidebar', 'sidebar_awards.tpl');
				break;
		}
	}

	public function index() {
		$this->pilotranks();
	}

	public function pilotranks() {
		switch (self::$post->action) {
			case 'addrank':
				$this->add_rank_post();
				break;
			case 'editrank':
				$this->edit_rank_post();
				break;

			case 'deleterank':

				$ret = RanksData::DeleteRank(self::$post->id);

				echo json_encode(array('status' => 'ok'));

				return;
				break;
		}

		$this->set('ranks', RanksData::GetAllRanks());
		$this->render('ranks_allranks.tpl');
	}

	public function addrank() {
		$this->set('title', 'Add Rank');
		$this->set('action', 'addrank');

		$this->render('ranks_rankform.tpl');
	}

	public function editrank() {
		$this->set('title', 'Edit Rank');
		$this->set('action', 'editrank');
		$this->set('rank', RanksData::GetRankInfo(self::$get->rankid));

		$this->render('ranks_rankform.tpl');
	}

	public function awards() {
		if (isset(self::$post->action)) {
			switch (self::$post->action) {
				case 'addaward':
					$this->add_award_post();
					break;
				case 'editaward':
					$this->edit_award_post();
					break;
				case 'deleteaward':
					$ret = AwardsData::DeleteAward(self::$post->id);
					LogData::addLog(Auth::$userinfo->pilotid, 'Deleted an award');

					echo json_encode(array('status' => 'ok'));
					return;
					break;
			}
		}

		$this->set('awards', AwardsData::GetAllAwards());
		$this->render('awards_allawards.tpl');
	}

	public function addaward() {

		$this->set('title', 'Add Award');
		$this->set('action', 'addaward');

		$this->render('awards_awardform.tpl');

	}

	public function editaward() {
		$this->set('title', 'Edit Award');
		$this->set('action', 'editaward');
		$this->set('award', AwardsData::GetAwardDetail(self::$get->awardid));

		$this->render('awards_awardform.tpl');

	}

	/* Utility functions */

	protected function add_rank_post() {

		if (self::$post->minhours == '' || self::$post->rank == '') {
			$this->set('message', 'Hours and Rank must be blank');
			$this->render('core_error.tpl');
			return;
		}

		if (!is_numeric(self::$post->minhours)) {
			$this->set('message', 'The hours must be a number');
			$this->render('core_error.tpl');
			return;
		}

		self::$post->payrate = abs(self::$post->payrate);

		$ret = RanksData::AddRank(self::$post->rank, self::$post->minhours, self::$post->imageurl, self::$post->payrate);

		if (DB::errno() != 0) {
			$this->set('message', 'Error adding the rank: ' . DB::error());
			$this->render('core_error.tpl');
			return;
		}

		$this->set('message', 'Rank Added!');
		$this->render('core_success.tpl');

		LogData::addLog(Auth::$userinfo->pilotid, 'Added the rank "' . self::$post->rank . '"');
	}

	protected function edit_rank_post() {
		if (self::$post->minhours == '' || self::$post->rank == '') {
			$this->set('message', 'Hours and Rank must be blank');
			$this->render('core_error.tpl');
			return;
		}

		if (!is_numeric(self::$post->minhours)) {
			$this->set('message', 'The hours must be a number');
			$this->render('core_error.tpl');
			return;
		}

		self::$post->payrate = abs(self::$post->payrate);

		$ret = RanksData::UpdateRank(self::$post->rankid, self::$post->rank, self::$post->minhours, self::$post->rankimage, self::$post->payrate);

		if (DB::errno() != 0) {
			$this->set('message', 'Error updating the rank: ' . DB::error());
			$this->render('core_error.tpl');
			return;
		}

		$this->set('message', 'Rank Added!');
		$this->render('core_success.tpl');

		LogData::addLog(Auth::$userinfo->pilotid, 'Edited the rank "' . self::$post->rank . '"');
	}

	protected function add_award_post() {
		if (self::$post->name == '' || self::$post->image == '') {
			$this->set('message', 'The name and image must be entered');
			$this->render('core_error.tpl');
			return;
		}

		$ret = AwardsData::AddAward(self::$post->name, self::$post->descrip, self::$post->image);

		$this->set('message', 'Award Added!');
		$this->render('core_success.tpl');

		LogData::addLog(Auth::$userinfo->pilotid, "Added the award \"{self::$post->name}\"");
	}

	protected function edit_award_post() {
		if (self::$post->name == '' || self::$post->image == '') {
			$this->set('message', 'The name and image must be entered');
			$this->render('core_error.tpl');
			return;
		}

		$ret = AwardsData::EditAward(self::$post->awardid, self::$post->name, self::$post->descrip, self::$post->image);

		$this->set('message', 'Award Added!');
		$this->render('core_success.tpl');

		LogData::addLog(Auth::$userinfo->pilotid, 'Edited the award "' . self::$post->name . '"');
	}
}