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
        global $DB, $USER;

        $listObjectives = $DB->get_records('bga_objectives',  ['usercreated' => $USER->id], 'timecreated DESC');
        $listGoals = $DB->get_records('bga_goals',  ['usercreated' => $USER->id], 'timecreated DESC');

        $data = [];

        foreach ($listObjectives as $objective) {
            $item['id'] = $objective->id;
            $item['is_goal'] = false;
            $item['is_objective'] = true;
            $item['has_associate_data'] = false;
            $item['title'] = $objective->title;
            $item['progress'] = (empty($objective->timecompleted) ? 0 : 100);
            $item['complete'] = (empty($objective->timecompleted) ? false : true);
            $item['associate_data'] = [];

            array_push($data, $item);
        }

        foreach ($listGoals as $goal) {
            $item['id'] = $goal->id;
            $item['is_goal'] = true;
            $item['is_objective'] = false;
            $item['has_associate_data'] = false;
            $item['title'] = $goal->title;
            $item['progressenable'] = ($goal->progresstype <> 'D'? true : false);
            $accruedprogress = 0;
            if($goal->progresstype <> 'D'){
                $accruedprogress = $DB->get_record_sql('
                    SELECT IFNULL(SUM(gp.progress), 0) as total
                    FROM moodle.mdl_bga_goal_progress gp
                    WHERE gp.goalid = :goalid ',  
                    ['goalid' => $goal->id ]
                );
                $accruedprogress = (int) (($accruedprogress->total * 100) / (!empty($goal->progresstotal)? $goal->progresstotal : 100 ));
            }
            $item['progress'] = (empty($goal->timecompleted) ? $accruedprogress : 100);
            $item['complete'] = (empty($goal->timecompleted) ? false : true);
            $item['associate_data'] = [];

            array_push($data, $item);
        }

        return [
            'str' => [
                'dashboardtitle' => get_string('dashboardtitle', 'block_goalsahead'),
                'objectives' => get_string('objectivesname', 'block_goalsahead'),
                'goals' => get_string('goalsname', 'block_goalsahead')
            ],
            'data' => $data
        ];
    }
}
