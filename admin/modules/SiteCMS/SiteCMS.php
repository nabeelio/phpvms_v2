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
		switch(self::$controller->function) {
			case 'addnews':
			case 'viewnews':
				$this->set('sidebar', 'sidebar_news.tpl');
				break;
			
			case 'viewpages':
				$this->set('sidebar', 'sidebar_pages.tpl');
				break;
				
			case 'addpageform':
				$this->set('sidebar', 'sidebar_addpage.tpl');
				break;
		}
	}
	
	public function viewnews() {

		$isset = isset(self::$post->action);

		if($isset && self::$post->action == 'addnews') {
			$this->AddNewsItem();		
		} elseif($isset && self::$post->action == 'editnews') {
			$res = SiteData::EditNewsItem(self::$post->id, self::$post->subject, self::$post->body);
			
			if($res == false) {
				$this->set('message', Lang::gs('news.updated.error'));
				$this->render('core_error.tpl');
			} else {
				LogData::addLog(Auth::$userinfo->pilotid, 'Edited news item "'.self::$post->subject.'"');
				
				$this->set('message', Lang::gs('news.updated.success'));
				$this->render('core_success.tpl');
			}
		} elseif($isset && self::$post->action == 'deleteitem') {
			$this->DeleteNewsItem();	
			echo json_encode(array('status' => 'ok'));
		}
		
		$this->set('allnews', SiteData::GetAllNews());
		$this->render('news_list.tpl');
	}
	
	public function addnews() {
		$this->set('title', Lang::gs('news.add.title'));
		$this->set('action', 'addnews');
		
		$this->render('news_additem.tpl');
	}
	
	public function editnews() {
		$this->set('title', Lang::gs('news.edit.title'));
		$this->set('action', 'editnews');
		$this->set('newsitem', SiteData::GetNewsItem(self::$get->id));
		
		$this->render('news_additem.tpl');
	}
	
	public function addpageform() {
		$this->set('title', Lang::gs('page.add.title'));
		$this->set('action', 'addpage');
		
		$this->render('pages_editpage.tpl');
	}
	
	public function editpage() {

		$page = SiteData::GetPageData( self::$get->pageid);
		$this->set('pagedata', $page);
		$this->set('content', @file_get_contents(PAGES_PATH . '/' . $page->filename . PAGE_EXT));
		
		$this->set('title', Lang::gs('page.edit.title'));
		$this->set('action', 'savepage');
		$this->render('pages_editpage.tpl');
		
		LogData::addLog(Auth::$userinfo->pilotid, 'Page '. $page->pagename.' edited');
	}
	
	public function deletepage() {

		if(SiteData::DeletePage( self::$get->pageid) == false) {
			$this->set('message', Lang::gs('page.error.delete'));
			$this->render('core_error.tpl');
		} else {
			LogData::addLog(Auth::$userinfo->pilotid, 'Page '. self::$get->pageid.' deleted');
			
			$this->set('message', Lang::gs('page.deleted'));
			$this->render('core_success.tpl');
		}
	}
	
	public function viewpages() {
		
		/* This is the actual adding page process
		 */
		if(isset(self::$post->action)) {
			switch(self::$post->action) {
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
		switch(self::$get->action) {
			case 'editpage':
		
				$this->edit_page_form();
				return;
				
				break;
			case 'deletepage':
		
				$pageid = self::$get->pageid;
				SiteData::DeletePage($pageid);
				echo json_encode(array('status' => 'ok'));
				return;
				break;
		}
		
		$this->set('allpages', SiteData::GetAllPages());
		$this->render('pages_allpages.tpl');
	}

	public function bumpnews() {

		$id = self::$get->id;

		SiteData::bumpNewsItem($id);

		$this->redirect(adminurl('sitecms/viewnews'));
	}
	
	/**
	 * This is the function for adding the actual page
	 */
	protected function add_page_post() {
		
		$public = (self::$post->public == 'true') ? true : false;
		$enabled = (self::$post->enabled == 'true') ? true : false;
		
		if(!self::$post->pagename) {
			$this->set('message', 'You must have a title');
			$this->render('core_error.tpl');
			return;
		}
		
		self::$post->content = stripslashes(self::$post->content);
		if(!SiteData::AddPage(self::$post->pagename, self::$post->content, $public, $enabled)) {

			if(DB::$errno == 1062) {
				$this->set('message', Lang::gs('page.exists'));
			} else {
				$this->set('message', Lang::gs('page.create.error'));
			}
			
			$this->render('core_error.tpl');
		}
		
		LogData::addLog(Auth::$userinfo->pilotid, 'Added page "'.self::$post->pagename.'"');
		
		$this->set('message', 'Page Added!');
		$this->render('core_success.tpl');
	}
	
	protected function edit_page_post() {
		$public = (self::$post->public == 'true') ? true : false;
		$enabled = (self::$post->enabled == 'true') ? true : false;
		
		if(!SiteData::EditFile(self::$post->pageid, self::$post->content, $public, $enabled)) {
			$this->set('message', Lang::gs('page.edit.error'));
			$this->render('core_error.tpl');
		}
		
		$this->set('message', 'Content saved');
		$this->render('core_success.tpl');
		
		LogData::addLog(Auth::$userinfo->pilotid, 'Edited page "'.self::$post->pagename.'"');
	}
	
	protected function AddNewsItem() {

		if(self::$post->subject == '')
			return;
		
		if(self::$post->body == '')
			return;
			
		if(!SiteData::AddNewsItem(self::$post->subject, self::$post->body)) {
			$this->set('message', 'There was an error adding the news item');
		}
		
		$this->render('core_message.tpl');
		
		LogData::addLog(Auth::$userinfo->pilotid, 'Added news "'.self::$post->subject.'"');
	}
	
	protected function DeleteNewsItem() {

		if(!SiteData::DeleteItem(self::$post->id)) {
			$this->set('message', Lang::gs('news.delete.error'));
			$this->render('core_error.tpl');
			return;
		}
		
		$this->set('message', Lang::gs('news.item.deleted'));
		$this->render('core_success.tpl');
		
		LogData::addLog(Auth::$userinfo->pilotid, 'Deleted news '.self::$post->id);
	}
}