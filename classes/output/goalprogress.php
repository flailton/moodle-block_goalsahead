<?php

namespace block_goalsahead\output;

use block_goalsahead\controller;
use block_goalsahead\form\goalprogresshtml_form;
use block_goalsahead\table\goalsprogresshtml_table;

class goalprogress extends controller
{
    private const TABLE = 'bga_goal_progress';

    public function __construct($page = 'form', $data = [])
    {
        $this->default_page = 'form';
        $this->init_outputs($page);
    }

    protected function init_outputs($page)
    {
        $param['form'] = [
            "render" => "forms",
            "route" => "output\\goalprogress",
            "call" => "goalprogress_form"
        ];

        $param['action'] = [
            "render" => "action",
            "route" => "output\\goalprogress",
            "redirect" => "form"
        ];

        $page_render = (isset($param[$page]) ? $page : $this->default_page);

        $this->set_output($param[$page_render]);
    }

    public function goalprogress_form($action = '')
    {
        global $DB;

        $mform = new goalprogresshtml_form();

        if (!$mform->is_cancelled()) {
            if ($data = $mform->get_data()) {
                if (empty($data->id)) {
                    self::insert($data);
                } else {
                    self::delete($data);
                }
            } 

            $mform->set_data($mform->get_data());
            $text  = $mform->render();

            $text .= '<form id="form_goalsahead" method="post">';
            $text .= '<input type="hidden" value="" name="goalsahead_page">';
            $text .= '</form>';
            $goalid = (!empty($data->goalid)? $data->goalid : optional_param('goalid', 0, PARAM_INT));
            $records = $DB->get_records('bga_goal_progress',  ['goalid' => $goalid], 'timecreated DESC');
            $goal = $DB->get_record('bga_goals',  ['id' => $goalid]);
            $table = new goalsprogresshtml_table($records, $goal->progresstype);
            $text .= '<br />';
            $text .= $table->render();
        }

        return $text;
    }

    private function insert($data)
    {
        global $DB;

        $goalprogress = new \stdClass();

        $goalprogress->goalid = $data->goalid;
        $goalprogress->timecreated = $data->timecreated;
        $goalprogress->progress = $data->advancementprogress;

        $goalprogress->id = $DB->insert_record(constant("self::TABLE"), $goalprogress);

        $goal = $DB->get_record('bga_goals', ['id' => $data->goalid]);
        $accruedprogress = $DB->get_record_sql('
            SELECT IFNULL(SUM(gp.progress), 0) as total
            FROM {bga_goal_progress} gp
            WHERE gp.goalid = :goalid ',  
            ['goalid' => $data->goalid ]
        );

        if($accruedprogress->total == $goal->progresstotal){
            $timecompleted = (new \DateTime())->setTimestamp(time());
            $goal->timecompleted = $timecompleted->getTimestamp();
            
            $DB->update_record('bga_goals', $goal);
        }

        return $goalprogress;
    }

    public function delete()
    {
        global $DB;

        $id = required_param('id', PARAM_INT);
        $goalid = required_param('goalid', PARAM_INT);

        $goal = $DB->get_record('bga_goals', ['id' => $goalid]);
        if(!empty($goal->timecompleted)){
            $goal->timecompleted = NULL;
            $DB->update_record('bga_goals', $goal);
        }

        return $DB->delete_records(constant("self::TABLE"), ['id' => $id]);
    }

    public function getGoalProgress($goalid)
    {
        global $DB;

        return $DB->get_record_sql(
            ' SELECT IFNULL(SUM(gp.progress), 0) as total
            FROM {bga_goal_progress} gp
            WHERE gp.goalid = :goalid ',
            ['goalid' => $goalid]
        );
    }
}
