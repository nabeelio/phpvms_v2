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

class PIREPAdmin extends CodonModule {
    public function HTMLHead() {
        switch (self::$controller->function) {
            case 'viewpending':
            case 'viewrecent':
            case 'viewall':
                $this->set('sidebar', 'sidebar_pirep_pending.tpl');
                break;
        }
    }

    public function index() {
        $this->viewpending();
    }

    protected function post_action() {
        if (isset(self::$post->action)) {
            switch (self::$post->action) {
                case 'addcomment':
                    $this->add_comment_post();
                    break;

                case 'approvepirep':
                    $this->approve_pirep_post();
                    break;

                case 'deletepirep':

                    $this->delete_pirep_post();
                    break;

                case 'rejectpirep':
                    $this->reject_pirep_post();
                    break;

                case 'editpirep':
                    $this->edit_pirep_post();
                    break;
            }
        }
    }

    public function viewpending() {
        $this->post_action();

        $this->set('title', 'Pending Reports');

        if (isset(self::$get->hub) && self::$get->hub != '') {
            $params = array('p.accepted' => PIREP_PENDING, 'u.hub' => self::$get->hub, );
        } else {
            $params = array('p.accepted' => PIREP_PENDING);
        }

        $this->set('pireps', PIREPData::findPIREPS($params));
        $this->set('pending', true);
        $this->set('load', 'viewpending');
        $this->render('pireps_list.tpl');
    }


    public function pilotpireps() {
        $this->post_action();

        $this->set('pending', false);
        $this->set('load', 'pilotpireps');

        $this->set('pireps', PIREPData::findPIREPS(array('p.pilotid' => self::$get->pilotid)));
        $this->render('pireps_list.tpl');
    }


    public function rejectpirep() {
        $this->set('pirepid', self::$get->pirepid);
        $this->render('pirep_reject.tpl');
    }

    public function viewrecent() {
        $this->set('title', Lang::gs('pireps.view.recent'));
        $this->set('pireps', PIREPData::GetRecentReports());
        $this->set('descrip', 'These pilot reports are from the past 48 hours');

        $this->set('pending', false);
        $this->set('load', 'viewrecent');

        $this->render('pireps_list.tpl');
    }

    public function approveall() {
        
        echo '<h3>Approve All</h3>';

        $allpireps = PIREPData::findPIREPS(array('p.accepted' => PIREP_PENDING));
        
        $total = count($allpireps);
        $count = 0;
        foreach ($allpireps as $pirep_details) {
            
            if ($pirep_details->aircraft == '') {
                continue;
            }

            # Update pilot stats
            SchedulesData::IncrementFlownCount($pirep_details->code, $pirep_details->flightnum);
            PIREPData::ChangePIREPStatus($pirep_details->pirepid, PIREP_ACCEPTED); // 1 is accepted
            #PilotData::UpdatePilotStats($pirep_details->pilotid);

            #RanksData::CalculateUpdatePilotRank($pirep_details->pilotid);
            RanksData::CalculatePilotRanks();
            #PilotData::GenerateSignature($pirep_details->pilotid);
            #StatsData::UpdateTotalHours();

            $count++;
        }

        $skipped = $total - $count;
        echo "$count of $total were approved ({$skipped} has errors)";
    }

    public function viewall() {
        $this->post_action();

        if (!isset(self::$get->start) || self::$get->start == '')
            self::$get->start = 0;

        $num_per_page = 20;
        $this->set('title', 'PIREPs List');

        $params = array();
        if (self::$get->action == 'filter') {
            $this->set('title', 'Filtered PIREPs');

            if (self::$get->type == 'code') {
                $params = array('p.code' => self::$get->query);
            } elseif (self::$get->type == 'flightnum') {
                $params = array('p.flightnum' => self::$get->query);
            } elseif (self::$get->type == 'pilotid') {
                $params = array('p.pilotid' => self::$get->query);
            } elseif (self::$get->type == 'depapt') {
                $params = array('p.depicao' => self::$get->query);
            } elseif (self::$get->type == 'arrapt') {
                $params = array('p.arricao' => self::$get->query);
            }
        }

        if (isset(self::$get->accepted) && self::$get->accepted != 'all') {
            $params['p.accepted'] = self::$get->accepted;
        }

        $allreports = PIREPData::findPIREPS($params, $num_per_page, self::$get->start);

        if (count($allreports) >= $num_per_page) {
            $this->set('paginate', true);
            $this->set('admin', 'viewall');
            $this->set('start', self::$get->start + 20);
        }

        $this->set('pending', false);
        $this->set('load', 'viewall');

        $this->set('pireps', $allreports);

        $this->render('pireps_list.tpl');
    }

    public function editpirep() {
        $this->set('pirep', PIREPData::GetReportDetails(self::$get->pirepid));
        $this->set('allairlines', OperationsData::GetAllAirlines());
        $this->set('allairports', OperationsData::GetAllAirports());
        $this->set('allaircraft', OperationsData::GetAllAircraft());
        $this->set('fielddata', PIREPData::GetFieldData(self::$get->pirepid));
        $this->set('pirepfields', PIREPData::GetAllFields());
        $this->set('comments', PIREPData::GetComments(self::$get->pirepid));

        $this->render('pirep_edit.tpl');
    }

    public function viewcomments() {
        
        $this->set('comments', PIREPData::GetComments(self::$get->pirepid));
        $this->render('pireps_comments.tpl');
    }

    public function deletecomment() {
        
        if (!isset($this->post)) {
            return;
        }

        PIREPData::deleteComment(self::$post->id);

        LogData::addLog(Auth::$userinfo->pilotid, 'Deleted a comment');

        $this->set('message', 'Comment deleted!');
        $this->render('core_success.tpl');
    }

    public function viewlog() {
        
        $this->set('report', PIREPData::GetReportDetails(self::$get->pirepid));
        $this->render('pirep_log.tpl');
    }

    public function addcomment() {
        
        if (isset(self::$post->submit)) {
            $this->add_comment_post();

            $this->set('message', 'Comment added to PIREP!');
            $this->render('core_success.tpl');
            return;
        }


        $this->set('pirepid', self::$get->pirepid);
        $this->render('pirep_addcomment.tpl');
    }

    /* Utility functions */

    protected function add_comment_post() {
        
        $comment = self::$post->comment;
        $commenter = Auth::$userinfo->pilotid;
        $pirepid = self::$post->pirepid;

        $pirep_details = PIREPData::GetReportDetails($pirepid);

        PIREPData::AddComment($pirepid, $commenter, $comment);

        // Send them an email
        $this->set('firstname', $pirep_details->firstname);
        $this->set('lastname', $pirep_details->lastname);
        $this->set('pirepid', $pirepid);

        $message = Template::GetTemplate('email_commentadded.tpl', true);
        Util::SendEmail($pirep_details->email, 'Comment Added', $message);

        LogData::addLog(Auth::$userinfo->pilotid, 'Added a comment to PIREP #' . $pirepid);
    }
    
    public function approvepirep($pirepid) {
        self::$post->id = $pirepid;
        $this->approve_pirep_post();
        
        $this->render('pirepadmin_approved.tpl');       
    }

    /**
     * Approve the PIREP, and then update
     * the pilot's data
     */
    protected function approve_pirep_post() {
        
        $pirepid = self::$post->id;
        
        if ($pirepid == '')
            return;

        $pirep_details = PIREPData::getReportDetails($pirepid);
        
        $this->set('pirep', $pirep_details);
        
        # See if it's already been accepted
        if (intval($pirep_details->accepted) == PIREP_ACCEPTED)
            return;

        # Update pilot stats
        
        PIREPData::ChangePIREPStatus($pirepid, PIREP_ACCEPTED); // 1 is accepted
        LogData::addLog(Auth::$userinfo->pilotid, 'Approved PIREP #' . $pirepid);

        # Call the event
        CodonEvent::Dispatch('pirep_accepted', 'PIREPAdmin', $pirep_details);
    }

    /**
     * Delete a PIREP
     */

    protected function delete_pirep_post() {
        $pirepid = self::$post->id;
        if ($pirepid == '')
            return;

        # Call the event
        CodonEvent::Dispatch('pirep_deleted', 'PIREPAdmin', $pirepid);

        PIREPData::deleteFlightReport($pirepid);
        StatsData::UpdateTotalHours();
    }

    /**
     * Reject the report, and then send them the comment
     * that was entered into the report
     */
    protected function reject_pirep_post() {
        $pirepid = self::$post->pirepid;
        $comment = self::$post->comment;

        if ($pirepid == '' || $comment == '')
            return;

        PIREPData::changePIREPStatus($pirepid, PIREP_REJECTED); // 2 is rejected
        $pirep_details = PIREPData::getReportDetails($pirepid);

        // Send comment for rejection
        if ($comment != '') {
            $commenter = Auth::$userinfo->pilotid; // The person logged in commented
            PIREPData::AddComment($pirepid, $commenter, $comment);

            // Send them an email
            $this->set('firstname', $pirep_details->firstname);
            $this->set('lastname', $pirep_details->lastname);
            $this->set('pirepid', $pirepid);

            $message = Template::GetTemplate('email_commentadded.tpl', true);
            Util::SendEmail($pirep_details->email, 'Comment Added', $message);
        }

        LogData::addLog(Auth::$userinfo->pilotid, 'Rejected PIREP #' . $pirepid);

        # Call the event
        CodonEvent::Dispatch('pirep_rejected', 'PIREPAdmin', $pirep_details);
    }

    protected function edit_pirep_post() {
        if (self::$post->code == '' || self::$post->flightnum == '' 
                || self::$post->depicao == '' || self::$post->arricao == '' 
                || self::$post->aircraft == '' || self::$post->flighttime == ''
            ) {
                
            $this->set('message', 'You must fill out all of the required fields!');
            $this->render('core_error.tpl');
            return false;
        }

        $pirepInfo = PIREPData::getReportDetails(self::$post->pirepid);
        if (!$pirepInfo) {
            $this->set('message', 'Invalid PIREP!');
            $this->render('core_error.tpl');
            return false;
        }

        self::$post->fuelused = str_replace(' ', '', self::$post->fuelused);
        self::$post->fuelused = str_replace(',', '', self::$post->fuelused);
        $fuelcost = self::$post->fuelused * self::$post->fuelunitcost;

        # form the fields to submit
        $data = array(
            'pirepid' => self::$post->pirepid, 
            'code' => self::$post->code, 
            'flightnum' => self::$post->flightnum, 
            'depicao' => self::$post->depicao, 
            'arricao' => self::$post->arricao, 
            'aircraft' => self::$post->aircraft, 
            'flighttime' => self::$post->flighttime, 
            'load' => self::$post->load, 
            'price' => self::$post->price, 
            'pilotpay' => self::$post->pilotpay, 
            'fuelused' => self::$post->fuelused, 
            'fuelunitcost' => self::$post->fuelunitcost, 
            'fuelprice' => $fuelcost, 
            'expenses' => self::$post->expenses
        );

        if (!PIREPData::updateFlightReport(self::$post->pirepid, $data)) {
            $this->set('message', 'There was an error editing your PIREP');
            $this->render('core_error.tpl');
            return false;
        }

        PIREPData::SaveFields(self::$post->pirepid, $_POST);

        //Accept or reject?
        self::$post->id = self::$post->pirepid;
        $submit = strtolower(self::$post->submit_pirep);

        // Add a comment
        if (trim(self::$post->comment) != '' && $submit != 'reject pirep') {
            PIREPData::AddComment(self::$post->pirepid, Auth::$userinfo->pilotid, self::$post->comment);
        }

        if ($submit == 'accept pirep') {
            $this->approve_pirep_post();
        } elseif ($submit == 'reject pirep') {
            $this->reject_pirep_post();
        }

        StatsData::UpdateTotalHours();

        # Refresh the PIREP
        # $pirepInfo = PIREPData::getReportDetails($this->post_action->pirepid);
        PilotData::updatePilotStats($pirepInfo->pilotid);

        LogData::addLog(Auth::$userinfo->pilotid, 'Edited PIREP #' . self::$post->id);
        return true;
    }
}
