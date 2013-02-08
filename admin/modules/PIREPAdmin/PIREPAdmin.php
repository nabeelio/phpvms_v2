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
        switch ($this->controller->function) {
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
        if (isset($this->post->action)) {
            switch ($this->post->action) {
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

        if (isset($this->get->hub) && $this->get->hub != '') {
            $params = array('p.accepted' => PIREP_PENDING, 'u.hub' => $this->get->hub, );
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

        $this->set('pireps', PIREPData::findPIREPS(array('p.pilotid' => $this->get->pilotid)));
        $this->render('pireps_list.tpl');
    }


    public function rejectpirep() {
        $this->set('pirepid', $this->get->pirepid);
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
            CodonEvent::Dispatch('pirep_accepted', 'PIREPAdmin', $pirep_details);

            $count++;
        }

        $skipped = $total - $count;
        echo "$count of $total were approved ({$skipped} has errors)";
    }

    public function viewall() {
        $this->post_action();

        if (!isset($this->get->start) || $this->get->start == '')
            $this->get->start = 0;

        $num_per_page = 20;
        $this->set('title', 'PIREPs List');

        $params = array();
        if ($this->get->action == 'filter') {
            $this->set('title', 'Filtered PIREPs');

            if ($this->get->type == 'code') {
                $params = array('p.code' => $this->get->query);
            } elseif ($this->get->type == 'flightnum') {
                $params = array('p.flightnum' => $this->get->query);
            } elseif ($this->get->type == 'pilotid') {
                $params = array('p.pilotid' => $this->get->query);
            } elseif ($this->get->type == 'depapt') {
                $params = array('p.depicao' => $this->get->query);
            } elseif ($this->get->type == 'arrapt') {
                $params = array('p.arricao' => $this->get->query);
            }
        }

        if (isset($this->get->accepted) && $this->get->accepted != 'all') {
            $params['p.accepted'] = $this->get->accepted;
        }

        $allreports = PIREPData::findPIREPS($params, $num_per_page, $this->get->start);

        if (count($allreports) >= $num_per_page) {
            $this->set('paginate', true);
            $this->set('admin', 'viewall');
            $this->set('start', $this->get->start + 20);
        }

        $this->set('pending', false);
        $this->set('load', 'viewall');

        $this->set('pireps', $allreports);

        $this->render('pireps_list.tpl');
    }

    public function editpirep() {
        $this->set('pirep', PIREPData::GetReportDetails($this->get->pirepid));
        $this->set('allairlines', OperationsData::GetAllAirlines());
        $this->set('allairports', OperationsData::GetAllAirports());
        $this->set('allaircraft', OperationsData::GetAllAircraft());
        $this->set('fielddata', PIREPData::GetFieldData($this->get->pirepid));
        $this->set('pirepfields', PIREPData::GetAllFields());
        $this->set('comments', PIREPData::GetComments($this->get->pirepid));

        $this->render('pirep_edit.tpl');
    }

    public function viewcomments() {
        
        $this->set('comments', PIREPData::GetComments($this->get->pirepid));
        $this->render('pireps_comments.tpl');
    }

    public function deletecomment() {
        
        if (!isset($this->post)) {
            return;
        }

        PIREPData::deleteComment($this->post->id);

        LogData::addLog(Auth::$userinfo->pilotid, 'Deleted a comment');

        $this->set('message', 'Comment deleted!');
        $this->render('core_success.tpl');
    }

    public function viewlog() {
        
        $this->set('report', PIREPData::GetReportDetails($this->get->pirepid));
        $this->render('pirep_log.tpl');
    }

    public function addcomment() {
        
        if (isset($this->post->submit)) {
            $this->add_comment_post();

            $this->set('message', 'Comment added to PIREP!');
            $this->render('core_success.tpl');
            return;
        }


        $this->set('pirepid', $this->get->pirepid);
        $this->render('pirep_addcomment.tpl');
    }

    /* Utility functions */

    protected function add_comment_post() {
        
        $comment = $this->post->comment;
        $commenter = Auth::$userinfo->pilotid;
        $pirepid = $this->post->pirepid;

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
        $this->post->id = $pirepid;
        $this->approve_pirep_post();
        
        $this->render('pirepadmin_approved.tpl');       
    }

    /**
     * Approve the PIREP, and then update
     * the pilot's data
     */
    protected function approve_pirep_post() {
        
        $pirepid = $this->post->id;
        
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
        $pirepid = $this->post->id;
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
        $pirepid = $this->post->pirepid;
        $comment = $this->post->comment;

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
        if ($this->post->code == '' || $this->post->flightnum == '' 
                || $this->post->depicao == '' || $this->post->arricao == '' 
                || $this->post->aircraft == '' || $this->post->flighttime == ''
            ) {
                
            $this->set('message', 'You must fill out all of the required fields!');
            $this->render('core_error.tpl');
            return false;
        }

        $pirepInfo = PIREPData::getReportDetails($this->post->pirepid);
        if (!$pirepInfo) {
            $this->set('message', 'Invalid PIREP!');
            $this->render('core_error.tpl');
            return false;
        }

        $this->post->fuelused = str_replace(' ', '', $this->post->fuelused);
        $this->post->fuelused = str_replace(',', '', $this->post->fuelused);
        $fuelcost = $this->post->fuelused * $this->post->fuelunitcost;

        # form the fields to submit
        $data = array(
            'pirepid' => $this->post->pirepid, 
            'code' => $this->post->code, 
            'flightnum' => $this->post->flightnum, 
            'depicao' => $this->post->depicao, 
            'arricao' => $this->post->arricao, 
            'aircraft' => $this->post->aircraft, 
            'flighttime' => $this->post->flighttime, 
            'load' => $this->post->load, 
            'price' => $this->post->price, 
            'pilotpay' => $this->post->pilotpay, 
            'fuelused' => $this->post->fuelused, 
            'fuelunitcost' => $this->post->fuelunitcost, 
            'fuelprice' => $fuelcost, 
            'expenses' => $this->post->expenses
        );

        if (!PIREPData::updateFlightReport($this->post->pirepid, $data)) {
            $this->set('message', 'There was an error editing your PIREP');
            $this->render('core_error.tpl');
            return false;
        }

        PIREPData::SaveFields($this->post->pirepid, $_POST);

        //Accept or reject?
        $this->post->id = $this->post->pirepid;
        $submit = strtolower($this->post->submit_pirep);

        // Add a comment
        if (trim($this->post->comment) != '' && $submit != 'reject pirep') {
            PIREPData::AddComment($this->post->pirepid, Auth::$userinfo->pilotid, $this->post->comment);
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

        LogData::addLog(Auth::$userinfo->pilotid, 'Edited PIREP #' . $this->post->id);
        return true;
    }
}
