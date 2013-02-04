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
		$this->set('sidebar', 'sidebar_downloads.tpl');
	}

	public function index() {
		$this->overview();
	}

	public function overview() {
		switch (self::$post->action) {
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
		$this->render('downloads_overview.tpl');
	}

	public function addcategory() {
		$this->set('title', 'Add Category');
		$this->set('action', 'addcategory');

		$this->render('downloads_categoryform.tpl');

	}

	public function adddownload() {
		$this->set('title', 'Add Download');
		$this->set('allcategories', DownloadData::GetAllCategories());
		$this->set('action', 'adddownload');

		$this->render('downloads_downloadform.tpl');
	}

	public function editcategory() {
		$this->set('title', 'Edit Category');
		$this->set('action', 'editcategory');
		$this->set('category', DownloadData::GetAsset(self::$get->id));

		$this->render('downloads_categoryform.tpl');
	}

	public function editdownload() {
		$this->set('title', 'Edit Download');
		$this->set('action', 'editdownload');
		$this->set('allcategories', DownloadData::GetAllCategories());
		$this->set('download', DownloadData::GetAsset(self::$get->id));

		$this->render('downloads_downloadform.tpl');
	}

	protected function AddCategoryPost() {
		if (self::$post->name == '') {
			$this->set('message', 'No category name entered!');
			$this->render('core_error.tpl');
			return;
		}

		if (DownloadData::FindCategory(self::$post->name)) {
			$this->set('message', 'Category already exists');
			$this->render('core_error.tpl');
			return;
		}

		DownloadData::AddCategory(self::$post->name, '', '');

		$this->set('message', 'Category added!');
		$this->render('core_success.tpl');
	}

	protected function EditCategoryPost() {
		if (self::$post->name == '') {
			$this->set('message', 'No category name entered!');
			$this->render('core_error.tpl');
			return;
		}

		if (DownloadData::FindCategory(self::$post->name)) {
			$this->set('message', 'Category already exists');
			$this->render('core_error.tpl');
			return;
		}

		$data = array('id' => self::$post->id, 'name' => self::$post->name, 'parent_id' => '', 'description' => '', 'link' => '', 'image' => '',);

		DownloadData::EditAsset($data);

		$this->set('message', 'Category edited!');
		$this->render('core_success.tpl');

	}

	protected function DeleteCategoryPost() {
		if (self::$post->id == '') {
			$this->set('message', 'Invalid category!');
			$this->render('core_error.tpl');
			return;
		}

		DownloadData::RemoveCategory(self::$post->id);

		$this->set('message', 'Category removed!');
		$this->render('core_success.tpl');
	}

	protected function AddDownloadPost() {
		if (self::$post->name == '' || self::$post->link == '') {
			$this->set('message', 'Link and name must be entered');
			$this->render('core_error.tpl');
			return;
		}

		$data = array('parent_id' => self::$post->category, 'name' => self::$post->name, 'description' => self::$post->description, 'link' => self::$post->link, 'image' => self::$post->image,);

		$val = DownloadData::AddDownload($data);

		if ($val == false) {
			$this->set('message', DB::$error);
			$this->render('core_error.tpl');
			return;
		}
	}

	protected function EditDownloadPost() {
		if (self::$post->name == '' || self::$post->link == '') {
			$this->set('message', 'Link and name must be entered!');
			$this->render('core_error.tpl');
			return;
		}

		$data = array('id' => self::$post->id, 'parent_id' => self::$post->category, 'name' => self::$post->name, 'description' => self::$post->description, 'link' => self::$post->link, 'image' => self::$post->image,);

		DownloadData::EditAsset($data);

		$this->set('message', 'Download edited!');
		$this->render('core_success.tpl');
	}

	protected function DeleteDownloadPost() {
		$params = array();
		if (self::$post->id == '') {
			$params['status'] = 'Invalid Download ID';
			echo json_encode($params);
			return;
		}

		DownloadData::RemoveAsset(self::$post->id);
		$params['status'] = 'ok';
		echo json_encode($params);
		return;
	}
}