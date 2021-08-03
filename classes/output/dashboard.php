<?php

namespace block_goalsahead\output;

use block_goalsahead\controller;

class dashboard extends controller
{
    public function __construct($page = 'dashboard', $data = [])
    {
        $this->default_page = 'dashboard';

        $this->init_outputs($page);
    }

    public function init_outputs($page = 'dashboard')
    {
        $output['dashboard'] = array(
            "render" => "template",
            "route" => "dashboard",
            "load_data" => "load_data_dashboard"
        );

        $page_render = (isset($output[$page]) ? $page : $this->default_page);
        $this->set_output($output[$page_render]);
    }

    public function load_data_dashboard()
    {
        global $USER;

        $cond['main']['usercreated'] = $USER->id;

        return [
            'str' => $this->getStringDashboard(),
            'data' => $this->getDataDashboard($cond)
        ];
    }

    private function getStringDashboard()
    {
        return [
            'dashboardtitle' => get_string('dashboardtitle', 'block_goalsahead'),
            'objectives' => get_string('objectivesname', 'block_goalsahead'),
            'goals' => get_string('goalsname', 'block_goalsahead'),
            'objectivesgoalsnotfound' => get_string('objectivesgoalsnotfound', 'block_goalsahead'),
            'completeobjective' => get_string('completeobjective', 'block_goalsahead'),
            'editobjective' => get_string('editobjective', 'block_goalsahead'),
            'deleteobjective' => get_string('deleteobjective', 'block_goalsahead'),
            'completegoal' => get_string('completegoal', 'block_goalsahead'),
            'editgoal' => get_string('editgoal', 'block_goalsahead'),
            'deletegoal' => get_string('deletegoal', 'block_goalsahead'),
            'progresstrackgoal' => get_string('progresstrackgoal', 'block_goalsahead'),
            'newobjective' => get_string('newobjective', 'block_goalsahead'),
            'newgoal' => get_string('newgoal', 'block_goalsahead'),
        ];
    }

    private function getDataDashboard($cond = [], $linked = false)
    {
        $listObjectives = $this->getUserData($cond, 'objectives', $linked);
        $listGoals = $this->getUserData($cond, 'goals', $linked);

        $data = [];

        foreach ($listObjectives as $objective) {
            $item['id'] = $objective->id;
            $item['is_goal'] = false;
            $item['is_objective'] = true;
            $item['title'] = $objective->title;
            $item['timecreated'] = $objective->timecreated;
            $subCond['sub'] = ['objectiveid' => $item['id']];
            $item['associate_data'] = $this->getDataDashboard($subCond, true);
            $countAssociateData = count($item['associate_data']);
            $item['has_associate_data'] = ($countAssociateData > 0? true : false);
            $progress = 0;
            foreach($item['associate_data'] as $linkedData){
                $progress += $linkedData['progress'];
            }

            $totalProgress = 0;
            if($item['has_associate_data']){
                $totalProgress = (int) ($progress / $countAssociateData);
            }
            $item['progress'] = (empty($objective->timecompleted) ? $totalProgress : 100);
            $item['complete'] = ($item['progress'] < 100 ? false : true);

            array_push($data, $item);
        }

        foreach ($listGoals as $goal) {
            $item['id'] = $goal->id;
            $item['is_goal'] = true;
            $item['is_objective'] = false;
            $item['has_associate_data'] = false;
            $item['title'] = $goal->title;
            $item['timecreated'] = $goal->timecreated;
            $item['progressenable'] = ($goal->progresstype <> 'D' ? true : false);
            $accruedprogress = 0;
            if ($goal->progresstype <> 'D') {
                $accruedprogress = $this->getGoalProgress($goal->id);
                $accruedprogress = (int) (($accruedprogress->total * 100) / (!empty($goal->progresstotal) ? $goal->progresstotal : 100));
            }
            $item['progress'] = (empty($goal->timecompleted) ? $accruedprogress : 100);
            $item['complete'] = ($item['progress'] < 100 ? false : true);
            $item['associate_data'] = [];

            array_push($data, $item);
        }

        usort($data, function($a, $b) {
            if($a['progress'] == $b['progress']){
                return $a['timecreated'] <=> $b['timecreated'];
            }
            
            if($a['progress'] == 100){
                return 1;
            }

            return $b['progress'] <=> $a['progress'];
        });

        return $data;
    }

    private function getUserData($cond = [], $table, $linked)
    {
        $method = 'getData' . ucfirst($table);
        if (method_exists($this, $method)) {
            $list = $this->$method($cond, $linked);
        }

        return $list;
    }

    private function getGoalProgress($goalid)
    {
        global $DB;

        return $DB->get_record_sql(
            ' SELECT IFNULL(SUM(gp.progress), 0) as total
            FROM mdl_bga_goal_progress gp
            WHERE gp.goalid = :goalid ',
            ['goalid' => $goalid]
        );
    }

    private function getDataObjectives($cond, $linked = false)
    {
        global $DB;

        $sql  = ' SELECT * FROM mdl_bga_objectives o ';
        $sql .= ' WHERE 1 = 1 ';

        foreach ($cond['main'] as $key => $item) {
            $sql .= ' AND ' . $key . ' = :' . $key;
            $condSql[$key] = $item;
        }

        $linked = (!empty($linked)? '' : 'NOT');
        $sql .= ' AND ' . $linked . ' EXISTS(
            SELECT 1
            FROM mdl_bga_objectives_objectives oo
            WHERE oo.subobjectiveid = o.id ';

        foreach ($cond['sub'] as $key => $item) {
            $sql .= ' AND ' . $key . ' = :' . $key;
            $condSql[$key] = $item;
        }

        foreach($cond as $condition){

        }
        $sql .= ' ) ';

        return $DB->get_records_sql($sql, $condSql);
    }

    private function getDataGoals($cond, $linked = false)
    {
        global $DB;

        $sql  = ' SELECT * FROM mdl_bga_goals g ';
        $sql .= ' WHERE 1 = 1 ';

        foreach ($cond['main'] as $key => $item) {
            $sql .= ' AND ' . $key . ' = :' . $key;
            $condSql[$key] = $item;
        }

        $link = (!empty($linked)? '' : 'NOT');
        $sql .= ' AND ' . $link . ' EXISTS(
            SELECT 1
            FROM mdl_bga_objectives_goals og
            WHERE og.goalid = g.id ';

        foreach ($cond['sub'] as $key => $item) {
            $sql .= ' AND ' . $key . ' = :' . $key;
            $condSql[$key] = $item;
        }

        $sql .= ' ) ';
        
        return $DB->get_records_sql($sql, $condSql);
    }
}
