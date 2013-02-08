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

class Downloads extends CodonModule {
	public function HTMLHead() {
		$this->set('sidebar', 'sidebar_downloads.php');
	}

	public function index() {
		$this->overview();
	}

	public function overview() {
		switch ($this->post->action) {
			case 'addcategory':
				$this->AddCategoryPost();
				break;

			case 'editcategory':
				$this->EditCategoryPost();
				break;

			case 'deletecategory':
				$this->DeleteCategoryPost();
				break;

			case 'adddownload':
				$this->AddDownloadPost();
				break;

			case 'editdownload':
				$this->EditDownloadPost();
				break;

			case 'deletedownload':
				$this->DeleteDownloadPost();
				return;
				break;

		}

		$this->set('allcategories', DownloadData::GetAllCategories());
		$this->render('downloads_overview.php');
	}

	public function addcategory() {
        $this->checkPermission(EDIT_DOWNLOADS);
		$this->set('title', 'Add Category');
		$this->set('action', 'addcategory');

		$this->render('downloads_categoryform.php');

	}

	public function adddownload() {
        $this->checkPermission(EDIT_DOWNLOADS);
		$this->set('title', 'Add Download');
		$this->set('allcategories', DownloadData::GetAllCategories());
		$this->set('action', 'adddownload');

		$this->render('downloads_downloadform.php');
	}

	public function editcategory() {
        $this->checkPermission(EDIT_DOWNLOADS);
		$this->set('title', 'Edit Category');
		$this->set('action', 'editcategory');
		$this->set('category', DownloadData::GetAsset($this->get->id));

		$this->render('downloads_categoryform.php');
	}

	public function editdownload() {
        $this->checkPermission(EDIT_DOWNLOADS);
		$this->set('title', 'Edit Download');
		$this->set('action', 'editdownload');
		$this->set('allcategories', DownloadData::GetAllCategories());
		$this->set('download', DownloadData::GetAsset($this->get->id));

		$this->render('downloads_downloadform.php');
	}

	protected function AddCategoryPost() {
        $this->checkPermission(EDIT_DOWNLOADS);
		if ($this->post->name == '') {
			$this->set('message', 'No category name entered!');
			$this->render('core_error.php');
			return;
		}

		if (DownloadData::FindCategory($this->post->name)) {
			$this->set('message', 'Category already exists');
			$this->render('core_error.php');
			return;
		}

		DownloadData::AddCategory($this->post->name, '', '');

		$this->set('message', 'Category added!');
		$this->render('core_success.php');
	}

	protected function EditCategoryPost() {
        $this->checkPermission(EDIT_DOWNLOADS);
		if ($this->post->name == '') {
			$this->set('message', 'No category name entered!');
			$this->render('core_error.php');
			return;
		}

		if (DownloadData::FindCategory($this->post->name)) {
			$this->set('message', 'Category already exists');
			$this->render('core_error.php');
			return;
		}

		$data = array('id' => $this->post->id, 'name' => $this->post->name, 'parent_id' => '', 'description' => '', 'link' => '', 'image' => '',);

		DownloadData::EditAsset($data);

		$this->set('message', 'Category edited!');
		$this->render('core_success.php');

	}

	protected function DeleteCategoryPost() {
        $this->checkPermission(EDIT_DOWNLOADS);
		if ($this->post->id == '') {
			$this->set('message', 'Invalid category!');
			$this->render('core_error.php');
			return;
		}

		DownloadData::RemoveCategory($this->post->id);

		$this->set('message', 'Category removed!');
		$this->render('core_success.php');
	}

	protected function AddDownloadPost() {
        $this->checkPermission(EDIT_DOWNLOADS);
		if ($this->post->name == '' || $this->post->link == '') {
			$this->set('message', 'Link and name must be entered');
			$this->render('core_error.php');
			return;
		}

		$data = array('parent_id' => $this->post->category, 'name' => $this->post->name, 'description' => $this->post->description, 'link' => $this->post->link, 'image' => $this->post->image,);

		$val = DownloadData::AddDownload($data);

		if ($val == false) {
			$this->set('message', DB::$error);
			$this->render('core_error.php');
			return;
		}
	}

	protected function EditDownloadPost() {
        $this->checkPermission(EDIT_DOWNLOADS);
		if ($this->post->name == '' || $this->post->link == '') {
			$this->set('message', 'Link and name must be entered!');
			$this->render('core_error.php');
			return;
		}

		$data = array('id' => $this->post->id, 'parent_id' => $this->post->category, 'name' => $this->post->name, 'description' => $this->post->description, 'link' => $this->post->link, 'image' => $this->post->image,);

		DownloadData::EditAsset($data);

		$this->set('message', 'Download edited!');
		$this->render('core_success.php');
	}

	protected function DeleteDownloadPost() {
        $this->checkPermission(EDIT_DOWNLOADS);
		$params = array();
		if ($this->post->id == '') {
			$params['status'] = 'Invalid Download ID';
			echo json_encode($params);
			return;
		}

		DownloadData::RemoveAsset($this->post->id);
		$params['status'] = 'ok';
		echo json_encode($params);
		return;
	}
}