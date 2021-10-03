<?php

namespace block_goalsahead\output;

use block_goalsahead\controller;
use block_goalsahead\output\objectives;
use block_goalsahead\output\goals;
use block_goalsahead\output\goalprogress;

class dashboard extends controller
{
    public function __construct($page = 'dashboard', $data = [])
    {
        $this->objectives = new objectives();
        $this->goals = new goals();
        $this->goalprogress = new goalprogress();
        
        $this->default_page = 'dashboard';

        $this->init_outputs($page);
    }

    public function init_outputs($page = 'dashboard')
    {
        $output['dashboard'] = [
            "render" => "template",
            "route" => "dashboard",
            "load_data" => "load_data_dashboard"
        ];

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
            'unfinishedobjective' => get_string('unfinishedobjective', 'block_goalsahead'),
            'unfinishedgoal' => get_string('unfinishedgoal', 'block_goalsahead'),
            'finishedobjective' => get_string('finishedobjective', 'block_goalsahead'),
            'finishedgoal' => get_string('finishedgoal', 'block_goalsahead'),
        ];
    }

    private function getDataDashboard($cond = [], $linked = false)
    {
        $listObjectives = $this->getUserData($cond, 'objectives', $linked);
        $listGoals = $this->getUserData($cond, 'goals', $linked);

        $data = [];

        $timecurrent = (new \DateTime())->setTimestamp(time());
        foreach ($listObjectives as $objective) {
            $item['id'] = $objective->id;
            $item['is_goal'] = false;
            $item['is_objective'] = true;
            $item['title'] = $objective->title;
            $item['timecreated'] = $objective->timecreated;
            $subCond['sub'] = [
                'objectiveid' => $item['id']
            ];
            $subCond['fix']['sub'] = [
                'subobjectiveid' => 'id'
            ];
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
            $objectivetimecompleted = $objective->timecompleted;
            if(empty($objectivetimecompleted) && $item['is_complete']){
                //$objectivetimecompleted = $this->getLastTimecompleted(['objectiveid' => $item['id']]);
            }
            $item['progress'] = (!empty($objectivetimecompleted) && !$item['has_associate_data']  ? 100 : $totalProgress);
            $item['is_complete'] = !empty($objectivetimecompleted) || $item['progress'] == 100;
            $item['endtimeformat'] = userdate($objective->endtime, get_string('strftimedatefullshort', 'core_langconfig'));
            $item['titleendtime'] = get_string('titleendtime', 'block_goalsahead', [
                'type' => get_string('articleobjectivename', 'block_goalsahead'), 
                'value' => $item['endtimeformat'], 
                'end' => get_string('completedobjective', 'block_goalsahead')
            ]);
            $item['is_over_time'] = (!empty($objective->endtime) && ($timecurrent->getTimestamp() > $objective->endtime));
            $item['timecompletedformat'] = userdate($objectivetimecompleted, get_string('strftimedatefullshort', 'core_langconfig'));
            $item['texttimecompletedobjective'] = get_string('texttimecompletedobjective', 'block_goalsahead', $item['timecompletedformat']);
            $item['textoverdue'] = get_string('textoverdue', 'block_goalsahead', get_string('objectivename', 'block_goalsahead'));
            $item['is_unfinished'] = (!empty($objectivetimecompleted) && ($item['progress'] < 100));
            
            array_push($data, $item);
        }

        foreach ($listGoals as $goal) {
            $item['associate_data'] = [];
            $item['id'] = $goal->id;
            $item['is_goal'] = true;
            $item['is_objective'] = false;
            $item['has_associate_data'] = false;
            $item['title'] = $goal->title;
            $item['timecreated'] = $goal->timecreated;
            $item['progressenable'] = ($goal->progresstype <> 'D' ? true : false);
            $accruedprogress = 0;
            if ($goal->progresstype <> 'D') {
                $accruedprogress = $this->goalprogress->getGoalProgress($goal->id);
                $accruedprogress = (int) (($accruedprogress->total * 100) / (!empty($goal->progresstotal) ? $goal->progresstotal : 100));
            }
            $goaltimecompleted = $goal->timecompleted;
            if(empty($goaltimecompleted) && $item['is_complete']){
                //$goaltimecompleted = $this->getLastTimecompleted(['objectiveid' => $item['id']]);
            }
            $item['progress'] = (!empty($goaltimecompleted) && !$item['progressenable'] ? 100 : $accruedprogress);
            $item['is_complete'] = !empty($goaltimecompleted);
            $item['endtimeformat'] = userdate($goal->endtime, get_string('strftimedatefullshort', 'core_langconfig'));
            $item['titleendtime'] = get_string('titleendtime', 'block_goalsahead', [
                'type' => get_string('articlegoalname', 'block_goalsahead'),
                'value' => $item['endtimeformat'], 
                'end' => get_string('completedgoal', 'block_goalsahead')
            ]);
            $item['is_over_time'] = (!empty($goal->endtime) && ($timecurrent->getTimestamp() > $goal->endtime));
            $item['timecompletedformat'] = userdate($goaltimecompleted, get_string('strftimedatefullshort', 'core_langconfig'));
            $item['texttimecompletedgoal'] = get_string('texttimecompletedgoal', 'block_goalsahead', $item['timecompletedformat']);
            $item['textoverdue'] = get_string('textoverdue', 'block_goalsahead', get_string('goalname', 'block_goalsahead'));
            $item['is_unfinished'] = (!empty($goaltimecompleted) && ($item['progress'] < 100));

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

    private function getUserData($cond = [], $table, $linked, $method = 'getData')
    {
        $class = $this->$table;
        if (method_exists($class, $method)) {
            $list = $class->$method($cond, $linked);
        }

        return $list;
    }
}
