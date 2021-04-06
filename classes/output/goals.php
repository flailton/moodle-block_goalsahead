<?php

namespace block_goalsahead\output;

use block_goalsahead\controller;
use block_goalsahead\form\goalshtml_form;

class goals extends controller{

    private const TABLE = 'bga_goals';

    public function __construct($page = 'form', $data = []) {
        $this->default_page = 'form';        
        $this->init_outputs($page);
    }

    protected function init_outputs($page) {
        $param['form'] = array(
            "render" => "forms",
            "route" => "output\\goals",
            "call" => "goals_form"
        );

        $param['action'] = array(
            "render" => "action",
            "route" => "output\\goals"
        );

        $page_render = (isset($param[$page]) ? $page : $this->default_page);
        $this->set_output($param[$page_render]);
    }
    
    public function load_data_form() {
        return [
            'str' => [
                'goalstitle' => get_string('goalstitle', 'block_goalsahead'),
                'objectives' => get_string('objectivesname', 'block_goalsahead'),
                'goals' => get_string('goalsname', 'block_goalsahead')
            ],
            'data' => [
                'id' => 1,
                'is_goal' => false,
                'is_objective' => true,
                'has_associate_data' => true,
                'title' => 'Obj 1',
                'progress' => 70
            ]
        ];
    }
    
    public function goals_form()
    {
        $mform = new goalshtml_form();

        if (!$mform->is_cancelled()) {
            if ($data = $mform->get_data()) {
                if (empty($data->id)) {
                    self::insert($data);
                } else {
                    self::update($data);
                }
            } else {
                $mform->set_data($mform->get_data());
                $text = $mform->render();
            }
        }

        return $text;
    }

    private function insert($data)
    {
        global $DB, $USER;

        $goal = new \stdClass();

        $goal->title = $data->title;
        $goal->description = $data->description['text'];
        $timecreated = (new \DateTime())->setTimestamp(time());
        $goal->timecreated = $timecreated->getTimestamp();
        $goal->usercreated = $USER->id;
        $goal->starttime = $data->starttime;
        $goal->endtime = $data->endtime;
        $goal->progresstype = $data->progresstype;
        $goal->progresstotal = ($data->progresstype === 'W'? $data->progresstotal : 100);

        $goal->id = $DB->insert_record(constant("self::TABLE"), $goal);

        return $goal;
    }

    private function update($data)
    {
        global $DB;
        
        $goal = $DB->get_record(constant("self::TABLE"), array('id' => $data->id));

        $goal->title = $data->title;
        $goal->description = $data->description['text'];
        $goal->starttime = $data->starttime;
        $goal->endtime = $data->endtime;
        $goal->progresstype = $data->progresstype;
        $goal->progresstotal = ($data->progresstype === 'W'? $data->progresstotal : null);
        
        $DB->update_record(constant("self::TABLE"), $goal);

        return $goal;
    }

    public function delete()
    {
        global $DB;
        $id = required_param('id', PARAM_INT);
        return $DB->delete_records(constant("self::TABLE"), array('id' => $id));
    }

    public function complete()
    {
        global $DB;

        $id = required_param('id', PARAM_INT);
        $goal = $DB->get_record(constant("self::TABLE"), array('id' => $id));

        $timecompleted = (new \DateTime())->setTimestamp(time());
        $goal->timecompleted = $timecompleted->getTimestamp();
        
        $DB->update_record(constant("self::TABLE"), $goal);

        return $goal;
    }
}