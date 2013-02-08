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

class Finance extends CodonModule {

    public function HTMLHead() {
        switch ($this->controller->function) {
            case 'addexpense':
            case 'editexpense':
            case 'viewexpenses':

                $this->set('sidebar', 'sidebar_expenses.php');

                break;
        }
    }

    public function viewcurrent() {
        $this->viewreport();
    }

    public function viewmonths() {

    }

    public function viewexpensechart() {
        $this->checkPermission(VIEW_FINANCES);
        $type = $this->get->type;
        $type = str_replace('m', '', $type);
        $check = date('Ym', $type);

        $finance_data = $this->getmonthly($check);
        $finance_data = FinanceData::calculateFinances($finance_data[0]);

        OFCharts::add_data_set('Fuel Costs', floatval($finance_data->fuelprice));
        OFCharts::add_data_set('Pilot Pay', floatval($finance_data->pilotpay));

        // Now expenses
        if (!is_array($finance_data->expenses)) {
            $finance_data->expenses = array();
        }

        foreach ($finance_data->expenses as $expense) {
            OFCharts::add_data_set($expense->name, floatval($expense->total));
        }

        echo OFCharts::create_pie_graph('Expenses breakdown');
    }

    public function viewmonthchart() {
        $this->checkPermission(VIEW_FINANCES);
        $params = $this->formfilter();

        /**
         * Check the first letter in the type
         * m#### - month
         * y#### - year
         * 
         * No type indicates to view the 'overall'
         */
        $type = $this->get->type;
        if ($type[0] == 'y') {
            $type = str_replace('y', '', $type);
            $year = date('Y', $type);

            $finance_data = $this->getyearly($year);
            $title = 'Activity for ' . $year;
        } else {
            // This should be the last 3 months overview
            # Get the last 3 months
            $months = 3;
            $params['p.accepted'] = PIREP_ACCEPTED;
            $finance_data = PIREPData::getIntervalDataByMonth($params, $months);
            $title = 'Recent Activity';
        }

        $titles = array();
        $gross_data = array();
        $fuel_data = array();
        $expense_data = array();

        foreach ($finance_data as $month) {
            $titles[] = $month->ym;
            $gross_data[] = intval($month->revenue);
            $fuel_data[] = intval($month->fuelprice);
            $expense_data[] = intval($month->expenses_total);
        }

        // Add each set
        OFCharts::add_data_set($titles, $gross_data, 'Total Revenue', '#FF6633');
        OFCharts::add_data_set($titles, $expense_data, 'Expenses', '#2EB800');
        OFCharts::add_data_set($titles, $fuel_data, 'Fuel Costs', '#008AB8');

        //echo OFCharts::create_line_graph('Months Balance Data');
        echo OFCharts::create_area_graph($title);
    }

    public function viewreport() {
        $this->checkPermission(VIEW_FINANCES);
        $type = $this->get->type;
        $params = $this->formfilter();

        /**
         * Check the first letter in the type
         * m#### - month
         * y#### - year
         * 
         * No type indicates to view the 'overall'
         */
        if ($type[0] == 'm') {
            $type = str_replace('m', '', $type);
            $period = date('F Y', $type);
            $check = date('Ym', $type);

            $finance_data = $this->getmonthly($check);
            $finance_data = FinanceData::calculateFinances($finance_data[0]);

            $this->set('title', 'Balance Sheet for ' . $period);
            $this->set('month_data', $finance_data);
            $this->render('finance_balancesheet.php');
        } elseif ($type[0] == 'y') {
            $type = str_replace('y', '', $type);
            $year = date('Y', $type);

            $all_finances = $this->getyearly($year);

            $this->set('title', 'Balance Sheet for Year ' . date('Y', $type));
            $this->set('allfinances', $all_finances);
            $this->set('year', date('Y', $type));

            $this->render('finance_summarysheet.php');
        } else {
            // This should be the last 3 months overview
            # Get the last 3 months
            $months = 3;
            $params['p.accepted'] = PIREP_ACCEPTED;
            $finance_data = PIREPData::getIntervalDataByMonth($params, $months);

            $this->set('title', 'Balance Sheet for Last 3 Months');
            $this->set('allfinances', $finance_data);
            $this->render('finance_summarysheet.php');
        }
    }

    protected function getmonthly($yearmonth) {
        $this->checkPermission(VIEW_FINANCES);
        $params = array('p.accepted' => PIREP_ACCEPTED, "DATE_FORMAT(p.submitdate, '%Y%m') = '{$yearmonth}'");

        $params = array_merge($params, $this->formfilter());
        $ret = PIREPData::getIntervalData($params);

        return $ret;
    }

    /**
     * Loop through month by month, and pull any data for that month.
     * If there's nothing for that month, then blank it 
     */
    protected function getyearly($year) {
        $this->checkPermission(VIEW_FINANCES);
        $params = $this->formfilter();
        $all_finances = array();

        $months = StatsData::getMonthsInRange('January ' . $year, 'December ' . $year);
        foreach ($months as $month) {
            $date_filter = array("DATE_FORMAT(p.submitdate, '%Y%m') = '" . date('Ym', $month) . "'");
            $this_filter = array_merge($date_filter, $params);

            $data = PIREPData::getIntervalData($this_filter);

            if (!$data) {
                $data = new stdClass();
                $data->ym = date('Y-m', $month);
                $data->timestamp = $month;
                $data->total = 0;
                $data->revenue = 0;
                $data->gross = 0;
                $data->fuelprice = 0;
                $data->price = 0;
                $data->expenses = 0;
                $data->pilotpay = 0;
            } else {
                $data = FinanceData::calculateFinances($data[0]);
            }

            $all_finances[] = $data;
        }

        return $all_finances;
    }

    protected function formfilter() {
        $params = array();
        if ($this->get->action == 'filter' && !empty($this->get->query)) {
            if ($this->get->type == 'flightnum') {
                $params = array('p.flightnum' => $this->get->query);
            } elseif ($this->get->type == 'code') {
                $params = array('p.code' => $this->get->query);
            } elseif ($this->get->type == 'aircraft') {
                $params = array('p.registration' => $this->get->query);
            } elseif ($this->get->type == 'depapt') {
                $params = array('p.depicao' => $this->get->query);
            } elseif ($this->get->type == 'arrapt') {
                $params = array('p.arricao' => $this->get->query);
            }
        }

        return $params;
    }

    public function viewexpenses() {
        $this->checkPermission(VIEW_FINANCES);
        if ($this->post->action == 'addexpense' || $this->post->action == 'editexpense') {
            $this->processExpense();
        }

        if ($this->post->action == 'deleteexpense') {
            FinanceData::removeExpense($this->post->id);
            FinanceData::setExpensesforMonth(time());

            echo json_encode(array('status' => 'ok'));
            return;
        }

        $this->set('allexpenses', FinanceData::GetAllExpenses());
        $this->render('finance_expenselist.php');
    }

    public function addexpense() {
        $this->checkPermission(EDIT_EXPENSES);
        $this->set('title', 'Add Expense');
        $this->set('action', 'addexpense');

        $this->render('finance_expenseform.php');
    }

    public function editexpense($id) {
        $this->checkPermission(EDIT_EXPENSES);
        $this->set('title', 'Edit Expense');
        $this->set('action', 'editexpense');
        $this->set('expense', FinanceData::getExpenseDetail($id));

        $this->render('finance_expenseform.php');
    }

    public function processExpense() {
        $this->checkPermission(EDIT_EXPENSES);
        if ($this->post->name == '' || $this->post->cost == '') {
            $this->set('message', 'Name and cost must be entered');
            $this->render('core_error.php');
            return;
        }

        if (!is_numeric($this->post->cost)) {
            $this->set('message', 'Cost must be a numeric amount, no symbols');
            $this->render('core_error.php');
            return;
        }

        if ($this->post->action == 'addexpense') {
            # Make sure it doesn't exist
            if (FinanceData::GetExpenseByName($this->post->name)) {
                $this->set('message', 'Expense already exists!');
                $this->render('core_error.php');
                return;
            }

            $ret = FinanceData::AddExpense($this->post->name, $this->post->cost, $this->post->type);
            $this->set('message', 'The expense "' . $this->post->name . '" has been added');

            FinanceData::setExpensesforMonth(time());

            LogData::addLog(Auth::$userinfo->pilotid, 'Added expense "' . $this->post->name . '"');
        } elseif ($this->post->action == 'editexpense') {
            $ret = FinanceData::EditExpense($this->post->id, $this->post->name, $this->post->cost, $this->post->type);
            $this->set('message', 'The expense "' . $this->post->name . '" has been edited');

            FinanceData::setExpensesforMonth(time());

            LogData::addLog(Auth::$userinfo->pilotid, 'Edited expense "' . $this->post->name . '"');
        }

        if (!$ret) {
            $this->set('message', 'Error: ' . DB::error());
            $this->render('core_error.php');

            return;
        }

        $this->render('core_success.php');
    }
}
