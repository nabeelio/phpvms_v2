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
 * @package module_admin_sitecms
 */
 
class SiteCMS extends CodonModule
{
	public function HTMLHead() {
		switch($this->controller->function) {
			case 'addnews':
			case 'viewnews':
				$this->set('sidebar', 'sidebar_news.php');
				break;
			
			case 'viewpages':
				$this->set('sidebar', 'sidebar_pages.php');
				break;
				
			case 'addpageform':
				$this->set('sidebar', 'sidebar_addpage.php');
				break;
		}
	}
	
	public function viewnews() {
        $this->checkPermission(EDIT_NEWS);
		$isset = isset($this->post->action);

		if($isset && $this->post->action == 'addnews') {
			$this->AddNewsItem();		
		} elseif($isset && $this->post->action == 'editnews') {
			$res = SiteData::EditNewsItem($this->post->id, $this->post->subject, $this->post->body);
			
			if($res == false) {
				$this->set('message', Lang::gs('news.updated.error'));
				$this->render('core_error.php');
			} else {
				LogData::addLog(Auth::$userinfo->pilotid, 'Edited news item "'.$this->post->subject.'"');
				
				$this->set('message', Lang::gs('news.updated.success'));
				$this->render('core_success.php');
			}
		} elseif($isset && $this->post->action == 'deleteitem') {
			$this->DeleteNewsItem();	
			echo json_encode(array('status' => 'ok'));
		}
		
		$this->set('allnews', SiteData::GetAllNews());
		$this->render('news_list.php');
	}
	
	public function addnews() {
        $this->checkPermission(EDIT_NEWS);
		$this->set('title', Lang::gs('news.add.title'));
		$this->set('action', 'addnews');
		
		$this->render('news_additem.php');
	}
	
	public function editnews() {
        $this->checkPermission(EDIT_NEWS);
		$this->set('title', Lang::gs('news.edit.title'));
		$this->set('action', 'editnews');
		$this->set('newsitem', SiteData::GetNewsItem($this->get->id));
		
		$this->render('news_additem.php');
	}
	
	public function addpageform() {
        $this->checkPermission(EDIT_PAGES);
		$this->set('title', Lang::gs('page.add.title'));
		$this->set('action', 'addpage');
		
		$this->render('pages_editpage.php');
	}
	
	public function editpage() {
        $this->checkPermission(EDIT_PAGES);
		$page = SiteData::GetPageData( $this->get->pageid);
		$this->set('pagedata', $page);
		$this->set('content', @file_get_contents(PAGES_PATH . '/' . $page->filename . PAGE_EXT));
		
		$this->set('title', Lang::gs('page.edit.title'));
		$this->set('action', 'savepage');
		$this->render('pages_editpage.php');
		
		LogData::addLog(Auth::$userinfo->pilotid, 'Page '. $page->pagename.' edited');
	}
	
	public function deletepage() {
        $this->checkPermission(EDIT_PAGES);
		if(SiteData::DeletePage( $this->get->pageid) == false) {
			$this->set('message', Lang::gs('page.error.delete'));
			$this->render('core_error.php');
		} else {
			LogData::addLog(Auth::$userinfo->pilotid, 'Page '. $this->get->pageid.' deleted');
			
			$this->set('message', Lang::gs('page.deleted'));
			$this->render('core_success.php');
		}
	}
	
	public function viewpages() {
        $this->checkPermission(EDIT_PAGES);
		/* This is the actual adding page process
		 */
		if(isset($this->post->action)) {
			switch($this->post->action) {
				case 'addpage':
					$this->add_page_post();
					break;
				case 'savepage':
					$this->edit_page_post();
					break;
			}
		}
		
		/* this is the popup form edit form
		 */
		switch($this->get->action) {
			case 'editpage':
		
				$this->edit_page_form();
				return;
				
				break;
			case 'deletepage':
		
				$pageid = $this->get->pageid;
				SiteData::DeletePage($pageid);
				echo json_encode(array('status' => 'ok'));
				return;
				break;
		}
		
		$this->set('allpages', SiteData::GetAllPages());
		$this->render('pages_allpages.php');
	}

	public function bumpnews() {
        $this->checkPermission(EDIT_NEWS);
		$id = $this->get->id;

		SiteData::bumpNewsItem($id);

		$this->redirect(adminurl('sitecms/viewnews'));
	}
	
	/**
	 * This is the function for adding the actual page
	 */
	protected function add_page_post() {
        $this->checkPermission(EDIT_PAGES);
		$public = ($this->post->public == 'true') ? true : false;
		$enabled = ($this->post->enabled == 'true') ? true : false;
		
		if(!$this->post->pagename) {
			$this->set('message', 'You must have a title');
			$this->render('core_error.php');
			return;
		}
		
		$this->post->content = stripslashes($this->post->content);
		if(!SiteData::AddPage($this->post->pagename, $this->post->content, $public, $enabled)) {

			if(DB::$errno == 1062) {
				$this->set('message', Lang::gs('page.exists'));
			} else {
				$this->set('message', Lang::gs('page.create.error'));
			}
			
			$this->render('core_error.php');
		}
		
		LogData::addLog(Auth::$userinfo->pilotid, 'Added page "'.$this->post->pagename.'"');
		
		$this->set('message', 'Page Added!');
		$this->render('core_success.php');
	}
	
	protected function edit_page_post() {
        $this->checkPermission(EDIT_PAGES);
		$public = ($this->post->public == 'true') ? true : false;
		$enabled = ($this->post->enabled == 'true') ? true : false;
		
		if(!SiteData::EditFile($this->post->pageid, $this->post->content, $public, $enabled)) {
			$this->set('message', Lang::gs('page.edit.error'));
			$this->render('core_error.php');
		}
		
		$this->set('message', 'Content saved');
		$this->render('core_success.php');
		
		LogData::addLog(Auth::$userinfo->pilotid, 'Edited page "'.$this->post->pagename.'"');
	}
	
	protected function AddNewsItem() {
        $this->checkPermission(EDIT_NEWS);
		if($this->post->subject == '')
			return;
		
		if($this->post->body == '')
			return;
			
		if(!SiteData::AddNewsItem($this->post->subject, $this->post->body)) {
			$this->set('message', 'There was an error adding the news item');
		}
		
		$this->render('core_message.php');
		
		LogData::addLog(Auth::$userinfo->pilotid, 'Added news "'.$this->post->subject.'"');
	}
	
	protected function DeleteNewsItem() {
        $this->checkPermission(EDIT_NEWS);
		if(!SiteData::DeleteItem($this->post->id)) {
			$this->set('message', Lang::gs('news.delete.error'));
			$this->render('core_error.php');
			return;
		}
		
		$this->set('message', Lang::gs('news.item.deleted'));
		$this->render('core_success.php');
		
		LogData::addLog(Auth::$userinfo->pilotid, 'Deleted news '.$this->post->id);
	}
}