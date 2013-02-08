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
 * @package module_admin_pilots
 */

class PilotAdmin extends CodonModule {

    /**
     * PilotAdmin::HTMLHead()
     * 
     * @return
     */
    public function HTMLHead() {
        switch ($this->controller->function) {
            case 'viewpilots':
                $this->set('sidebar', 'sidebar_pilots.php');
                break;
            case 'pendingpilots':
                $this->set('sidebar', 'sidebar_pending.php');
                break;
            case 'pilotgroups':
            case 'editgroup':
            case 'addgroup':
                $this->set('sidebar', 'sidebar_groups.php');
                break;
        }
    }

    /**
     * PilotAdmin::index()
     * 
     * @return
     */
    public function index() {
        $this->viewpilots();
    }


    /**
     * PilotAdmin::viewpilots()
     * 
     * @return
     */
    public function viewpilots() {
        /* This function is called for *ANYTHING* in that popup box
        
        Preset all of the template items in this function and
        call them in the subsequent templates
        
        Confusing at first, but easier than loading each tab
        independently via AJAX. Though may be an option later
        on, but can certainly be done by a plugin (Add another
        tab through AJAX). The hook is available for whoever
        wants to use it
        */

        switch ($this->post->action) {
            case 'changepassword':

                $this->ChangePassword();
                return;

                break;

            case 'deletepilot':

                $pilotid = $this->post->pilotid;
                $pilotinfo = PilotData::getPilotData($pilotid);

                PilotData::DeletePilot($pilotid);


                CodonEvent::Dispatch('pilot_deleted', 'PilotAdmin', $pilot);


                $this->set('message', Lang::gs('pilot.deleted'));
                $this->render('core_success.php');


                LogData::addLog(Auth::$userinfo->pilotid, 'Deleted pilot ' . 
                        PilotData::getPilotCode($pilotinfo->code, $pilotinfo->pilotid) 
                        .' '.$pilotinfo->firstname.' '.$pilotinfo->lastname
                );

                break;
                /* These are reloaded into the #pilotgroups ID
                so the entire groups list is refreshed
                */
            case 'addgroup':

                $this->AddPilotToGroup();
                $this->SetGroupsData($this->post->pilotid);
                $this->render('pilots_groups.php');
                $this->render('pilots_addtogroup.tpl');
                return;

                break;

            case 'removegroup':

                $this->RemovePilotGroup();

                $this->SetGroupsData($this->post->pilotid);
                $this->render('pilots_groups.php');
                $this->render('pilots_addtogroup.tpl');
                return;

                break;

            case 'saveprofile':

                if ($this->post->firstname == '' || $this->post->lastname == '') {
                    $this->set('message', 'The first or lastname cannot be blank!');
                    $this->render('core_error.php');
                    return;
                }

                $params = array(
                    'code' => $this->post->code, 
                    'firstname' => $this->post->firstname, 
                    'lastname' => $this->post->lastname, 
                    'email' => $this->post->email, 
                    'location' => $this->post->location, 
                    'hub' => $this->post->hub, 
                    'retired' => $this->post->retired, 
                    'totalhours' => $this->post->totalhours, 
                    'totalflights' => $this->post->totalflights, 
                    'totalpay' => floatval($this->post->totalpay), 
                    'payadjust' => floatval($this->post->payadjust), 
                    'transferhours' => $this->post->transferhours, 
                    'comment' => $this->post->comment, 
                );

                PilotData::updateProfile($this->post->pilotid, $params);
                PilotData::SaveFields($this->post->pilotid, $_POST);

                /* Don't calculate a pilot's rank if this is set */
                if (Config::Get('RANKS_AUTOCALCULATE') == false) {
                    PilotData::changePilotRank($this->post->pilotid, $this->post->rank);
                } else {
                    RanksData::calculateUpdatePilotRank($this->post->pilotid);
                }

                StatsData::UpdateTotalHours();

                $this->set('message', 'Profile updated successfully');
                $this->render('core_success.php');
                
                if($this->post->resend_email == 'true') {
                    $this->post->id = $this->post->pilotid;
                    $this->resendemail(false);
                }

                $pilot = PilotData::getPilotData($this->post->pilotid);
                LogData::addLog(Auth::$userinfo->pilotid, 'Updated profile for ' 
                                .PilotData::getPilotCode($pilot->code, $pilot->pilotid) 
                                .' '.$pilot->firstname.' '.$pilot->lastname);

                return;
                break;
        }

        if ($this->get->action == 'viewoptions') {
            $this->ViewPilotDetails();
            return;
        }

        $this->ShowPilotsList();
    }

    public function pilotgrouptab($pilotid) {
        $this->setGroupsData($pilotid);
        Template::Show('pilots_groups.php'); 
        Template::Show('pilots_addtogroup.php');
    }


    /**
     * PilotAdmin::pendingpilots()
     * 
     * @return
     */
    public function pendingpilots() {
        $this->checkPermission(MODERATE_REGISTRATIONS);
        
        if (isset($this->post->action)) {
            switch ($this->post->action) {
                case 'approvepilot':

                    $this->ApprovePilot();

                    break;
                case 'rejectpilot':

                    $this->RejectPilot();

                    break;
            }
        }

        $this->set('allpilots', PilotData::getPendingPilots());
        $this->render('pilots_pending.php');
    }
    
    /**
     * PilotAdmin::resendemail()
     * 
     * @return
     */
    public function resendemail($show_pending = true) {
        $this->checkPermission(MODERATE_REGISTRATIONS);
        $this->checkPermission(EMAIL_PILOTS);
                
        $pilot = PilotData::getPilotData($this->post->id);

        # Send pilot notification
        $subject = Lang::gs('email.register.accepted.subject');
        
        $this->set('pilot', $pilot);
        
        $oldPath = Template::setTemplatePath(TEMPLATES_PATH);
        $oldSkinPath = Template::setSkinPath(ACTIVE_SKIN_PATH);
        
        $message = Template::getTemplate('email_registrationaccepted.php', true, true, true);
        
        Template::setTemplatePath($oldPath);
        Template::setSkinPath($oldSkinPath);

        Util::sendEmail($pilot->email, $subject, $message);
                
        $this->set('message', 'Activation email has been re-sent to '.$pilot->firstname.' '.$pilot->lastname);
        $this->render('core_success.php');
            
        LogData::addLog(
            Auth::$userinfo->pilotid, 
            'Activation email re-sent '.PilotData::getPilotCode($pilot->code, $pilot->pilotid).' - '.$pilot->firstname.' '.$pilot->lastname
        );
        
        if($show_pending === true) {
            $this->set('allpilots', PilotData::getPendingPilots());
            $this->render('pilots_pending.php');
        }
    }

    /**
     * PilotAdmin::viewbids()
     * 
     * @return
     */
    public function viewbids() {
        if ($this->post->action == 'deletebid') {
            $ret = SchedulesData::RemoveBid($this->post->id);

            $params = array();
            if ($ret == true) {
                $params['status'] = 'ok';
            } else {
                $params['status'] = 'There was an error';
                $params['message'] = DB::error();
            }

            echo json_encode($params);
            return;
        }

        $this->set('allbids', SchedulesData::getAllBids());
        $this->render('pilots_viewallbids.php');
    }

    /**
     * PilotAdmin::pilotgroups()
     * 
     * @return
     */
    public function pilotgroups() {
        if (isset($this->post->action)) {
            if ($this->post->action == 'addgroup') {
                $this->AddGroupPost();
            } elseif ($this->post->action == 'editgroup') {
                # Process
                $this->SaveGroup();
            }
        }

        $this->ShowGroups();
    }

    /**
     * PilotAdmin::addgroup()
     * 
     * @return
     */
    public function addgroup() {
        $this->set('title', 'Add a Group');
        $this->set('action', 'addgroup');
        $this->set('permission_set', Config::Get('permission_set'));

        $this->render('groups_groupform.php');
    }

    /**
     * PilotAdmin::editgroup()
     * 
     * @return
     */
    public function editgroup() {
        if (!isset($this->get->groupid)) {
            return;
        }

        $group_info = PilotGroups::GetGroup($this->get->groupid);

        $this->set('group', $group_info);
        $this->set('title', 'Editing ' . $group_info->name);
        $this->set('action', 'editgroup');
        $this->set('permission_set', Config::Get('permission_set'));

        $this->render('groups_groupform.php');
    }

    /**
     * PilotAdmin::pilotawards()
     * 
     * @return
     */
    public function pilotawards() {
        if (isset($this->post->action)) {
            if ($this->post->action == 'addaward') {
                $this->AddAward();
            } elseif ($this->post->action == 'deleteaward') {
                $this->DeleteAward();
            }
        }

        $this->set('allawards', AwardsData::GetPilotAwards($_REQUEST['pilotid']));
        $this->render('pilots_awards.php');
    }

    /**
     * PilotAdmin::ShowPilotsList()
     * 
     * @return
     */
    protected function ShowPilotsList() {
        $this->render('pilots_list.php');
    }

    /**
     * PilotAdmin::getpilotsjson()
     * 
     * @return
     */
    public function getpilotsjson() {
        
        $page = $this->get->page; // get the requested page
        $limit = $this->get->rows; // get how many rows we want to have into the grid
        $sidx = $this->get->sidx; // get index row - i.e. user click to sort
        $sord = $this->get->sord; // get the direction
        if (!$sidx)
            $sidx = 1;

        /* Do the search using jqGrid */
        $where = array();
        if ($this->get->_search == 'true') {
            $searchstr = jqgrid::strip($this->get->filters);
            $where_string = jqgrid::constructWhere($searchstr);

            # Append to our search, add 1=1 since it comes with AND
            #	from above
            $where[] = "1=1 {$where_string}";
        }

        Config::Set('PILOT_ORDER_BY', "{$sidx} {$sord}");

        # Do a search without the limits so we can find how many records
        $count = count(PilotData::findPilots($where));

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
        $allpilots = PilotData::findPilots($where, $limit, $start);
        if (!$allpilots) {
            $allpilots = array();
        }

        # Form the json header
        $json = array('page' => $page, 'total' => $total_pages, 'records' => $count, 'rows' => array());

        $statuses = Config::get('PILOT_STATUS_TYPES');
        
        # Add each row to the above array
        foreach ($allpilots as $row) {
            
            $pilotid = PilotData::getPilotCode($row->code, $row->pilotid);
            
            foreach($statuses as $id => $details) {
                if($row->retired == $id) {
                    $status = $details['name'];
                    break;
                }
            }
            
            $location = '<img src="' . Countries::getCountryImage($row->location) . '" alt="' . $row->location . '" />';
            $edit = '<a href="' . adminurl('/pilotadmin/viewpilots?action=viewoptions&pilotid=' . $row->pilotid) . '">Edit</a>';

            $tmp = array(
                'id' => $row->id, 
                'cell' => array( # Each column, in order
                    $row->id, 
                    $pilotid, 
                    $row->firstname, 
                    $row->lastname, 
                    $row->email, 
                    $location, 
                    $status, 
                    $row->rank, 
                    $row->totalflights, 
                    $row->totalhours, 
                    $row->lastip, 
                    $edit
                )
            );

            $json['rows'][] = $tmp;
        }

        header("Content-type: text/x-json");
        echo json_encode($json);
    }

    /**
     * PilotAdmin::ViewPilotDetails()
     * 
     * @return
     */
    protected function ViewPilotDetails() {
        //This is for the main tab
        
        if(
        PilotGroups::group_has_perm(Auth::$usergroups, EDIT_PILOTS) ||
        PilotGroups::group_has_perm(Auth::$usergroups, EDIT_GROUPS) ||
        PilotGroups::group_has_perm(Auth::$usergroups, EDIT_AWARDS) ||
        PilotGroups::group_has_perm(Auth::$usergroups, MODERATE_PIREPS)
        ) {
        $this->set('pilotinfo', PilotData::GetPilotData($this->get->pilotid));
        $this->set('customfields', PilotData::GetFieldData($this->get->pilotid, true));
        $this->set('allawards', AwardsData::GetPilotAwards($this->get->pilotid));
        $this->set('pireps', PIREPData::GetAllReportsForPilot($this->get->pilotid));
        $this->set('countries', Countries::getAllCountries());

        $this->SetGroupsData($this->get->pilotid);

        // For the PIREP list
        $this->set('pending', false);
        $this->set('load', 'pilotpireps');

        $this->render('pilots_detailtabs.php');
        }else{
        	Debug::showCritical('Unauthorized access - Invalid Permissions.');
        	die();
        }
    }

    /**
     * PilotAdmin::SetGroupsData()
     * 
     * @param mixed $pilotid
     * @return
     */
    protected function SetGroupsData($pilotid) {
        
        # This is for the groups tab
        $freegroups = array();

        $allgroups = PilotGroups::GetAllGroups();
        foreach ($allgroups as $group) {
            if (!PilotGroups::CheckUserInGroup($pilotid, $group->groupid)) {
                array_push($freegroups, $group->name);
            }
        }

        $this->set('pilotid', $pilotid);
        $this->set('pilotgroups', PilotData::GetPilotGroups($pilotid));
        $this->set('freegroups', $freegroups);
    }

    /**
     * PilotAdmin::AddGroupPost()
     * 
     * @return
     */
    protected function AddGroupPost() {
        if ($this->post->name == '') {
            $this->set('message', Lang::gs('group.no.name'));
            $this->render('core_error.php');
            return;
        }

        $permissions = 0;
        foreach ($this->post->permissions as $perm) {
            $permissions = PilotGroups::set_permission($permissions, $perm);
        }

        $ret = PilotGroups::AddGroup($this->post->name, $permissions);

        if (DB::errno() != 0) {
            $this->set('message', sprintf(Lang::gs('error'), DB::$error));
            $this->render('core_error.php');
        } else {
            $this->set('message', sprintf(Lang::gs('group.added'), $this->post->name));
            $this->render('core_success.php');

            LogData::addLog(Auth::$userinfo->pilotid, 'Added group "' . $this->post->name . '"');
        }
    }

    /**
     * PilotAdmin::SaveGroup()
     * 
     * @return
     */
    protected function SaveGroup() {
        $permissions = 0;
        foreach ($this->post->permissions as $perm) {
            $permissions = PilotGroups::set_permission($permissions, $perm);
        }

        PilotGroups::EditGroup($this->post->groupid, $this->post->name, $permissions);

        if (DB::errno() != 0) {
            $this->set('message', sprintf(Lang::gs('error'), DB::$error));
            $this->render('core_error.php');
        } else {
            $this->set('message', sprintf(Lang::gs('group.saved'), $this->post->name));
            $this->render('core_success.php');

            LogData::addLog(Auth::$userinfo->pilotid, 'Edited group "' . $this->post->name . '"');
        }
    }


    /**
     * PilotAdmin::AddPilotToGroup()
     * 
     * @return
     */
    protected function AddPilotToGroup() {
        if (PilotGroups::CheckUserInGroup($this->post->pilotid, $this->post->groupname)) {
            $this->set('message', Lang::gs('group.pilot.already.in'));
            $this->render('core_error.php');
            return;
        }

        $ret = PilotGroups::AddUsertoGroup($this->post->pilotid, $this->post->groupname);

        if (DB::errno() != 0) {
            $this->set('message', Lang::gs('group.add.error'));
            $this->render('core_error.php');
        } else {
            LogData::addLog(Auth::$userinfo->pilotid, 'Added pilot #' . $this->post->pilotid . ' to group "' . $this->post->groupname . '"');
        }
    }

    /**
     * PilotAdmin::RemovePilotGroup()
     * 
     * @return
     */
    protected function RemovePilotGroup() {
        $pilotid = $this->post->pilotid;
        $groupid = $this->post->groupid;

        PilotGroups::RemoveUserFromGroup($pilotid, $groupid);

        if (DB::errno() != 0) {
            $this->set('message', 'There was an error removing');
            $this->render('core_error.php');
        } else {
            LogData::addLog(Auth::$userinfo->pilotid, 'Removed pilot #' . $this->post->pilotid . ' from group "' . $this->post->groupid . '"');
        }
    }

    /**
     * PilotAdmin::ShowGroups()
     * 
     * @return
     */
    protected function ShowGroups() {
        $this->set('allgroups', PilotGroups::GetAllGroups());
        $this->render('groups_grouplist.php');
    }

    /**
     * PilotAdmin::ApprovePilot()
     * 
     * @return
     */
    protected function ApprovePilot() {
        $this->checkPermission(MODERATE_REGISTRATIONS);
        
        PilotData::AcceptPilot($this->post->id);
        RanksData::CalculatePilotRanks();

        $pilot = PilotData::getPilotData($this->post->id);

        # Send pilot notification
        $subject = Lang::gs('email.register.accepted.subject');
        $this->set('pilot', $pilot);
        
        $oldPath = Template::setTemplatePath(TEMPLATES_PATH);
        $oldSkinPath = Template::setSkinPath(ACTIVE_SKIN_PATH);
        
        $message = Template::getTemplate('email_registrationaccepted.php', true, true, true);
        
        Template::setTemplatePath($oldPath);
        Template::setSkinPath($oldSkinPath);

        Util::SendEmail($pilot->email, $subject, $message);

        CodonEvent::Dispatch('pilot_approved', 'PilotAdmin', $pilot);

        LogData::addLog(Auth::$userinfo->pilotid, 'Approved ' . PilotData::getPilotCode($pilot->code, $pilot->pilotid) . ' - ' . $pilot->firstname . ' ' . $pilot->lastname);
    }

    /**
     * PilotAdmin::RejectPilot()
     * 
     * @return
     */
    protected function RejectPilot() {
        $this->checkPermission(MODERATE_REGISTRATIONS);
        $pilot = PilotData::GetPilotData($this->post->id);

        # Send pilot notification

        $subject = Lang::gs('email.register.rejected.subject');

        $this->set('pilot', $pilot);
        
        $oldPath = Template::setTemplatePath(TEMPLATES_PATH);
        $oldSkinPath = Template::setSkinPath(ACTIVE_SKIN_PATH);
        
        $message = Template::Get('email_registrationdenied.php', true, true, true);
        
        Template::setTemplatePath($oldPath);
        Template::setSkinPath($oldSkinPath);
        
        Util::SendEmail($pilot->email, $subject, $message);

        # Reject in the end, since it's delted
        PilotData::RejectPilot($this->post->id);

        CodonEvent::Dispatch('pilot_rejected', 'PilotAdmin', $pilot);

        LogData::addLog(
            Auth::$userinfo->pilotid, 
            'Approved '.PilotData::getPilotCode($pilot->code, $pilot->pilotid).' - '.$pilot->firstname.' '.$pilot->lastname
        );
    }

    /**
     * PilotAdmin::ChangePassword()
     * 
     * @return
     */
    protected function ChangePassword() {
        $password1 = $this->post->password1;
        $password2 = $this->post->password2;

        // Check password length
        if (strlen($password1) <= 5) {
            $this->set('message', Lang::gs('password.wrong.length'));
            $this->render('core_message.php');
            return;
        }

        // Check is passwords are the same
        if ($password1 != $password2) {
            $this->set('message', Lang::gs('password.no.match'));
            $this->render('core_message.php');
            return;
        }

        RegistrationData::ChangePassword($this->post->pilotid, $password1);

        if (DB::errno() != 0) {
            $this->set('message', 'There was an error, administrator has been notified');
            $this->render('core_error.php');
        } else {
            $this->set('message', Lang::gs('password.changed'));
            $this->render('core_success.php');
        }

        $pilot = PilotData::getPilotData($this->post->pilotid);
        LogData::addLog(
            Auth::$userinfo->pilotid, 
            'Changed the password for '.PilotData::getPilotCode($pilot->code, $pilot->pilotid).' - '.$pilot->firstname.' '.$pilot->lastname
        );
    }

    /**
     * PilotAdmin::AddAward()
     * 
     * @return
     */
    protected function AddAward() {

        if ($this->post->awardid == '' || $this->post->pilotid == '')
            return;

        # Check if they already have this award
        $award = AwardsData::GetPilotAward($this->post->pilotid, $this->post->awardid);
        if ($award) {
            $this->set('message', Lang::gs('award.exists'));
            $this->render('core_error.php');
            return;
        }

        AwardsData::AddAwardToPilot($this->post->pilotid, $this->post->awardid);

        $pilot = PilotData::getPilotData($this->post->pilotid);
        LogData::addLog(Auth::$userinfo->pilotid, 'Added and award to ' . PilotData::getPilotCode($pilot->code, $pilot->pilotid) . ' - ' . $pilot->firstname . ' ' . $pilot->lastname);
    }

    /**
     * PilotAdmin::DeleteAward()
     * 
     * @return
     */
    protected function DeleteAward() {
        AwardsData::DeletePilotAward($this->post->id);

        if ($award) {
            $this->set('message', Lang::gs('award.deleted'));
            $this->render('core_success.php');
            return;
        }
    }
}
