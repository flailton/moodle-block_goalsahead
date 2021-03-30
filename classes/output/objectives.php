<?php

namespace block_goalsahead\output;

use block_goalsahead\controller;
use block_goalsahead\form\objectiveshtml_form;

class objectives extends controller
{
    private const TABLE = 'bga_objectives';

    public function __construct($page = 'form', $data = [])
    {
        $this->default_page = 'form';        
        $this->init_outputs($page);
    }

    protected function init_outputs($page)
    {
        $param['form'] = array(
            "render" => "forms",
            "route" => "output\\objectives",
            "call" => "objectives_form"
        );
        
        $param['action'] = array(
            "render" => "action",
            "route" => "output\\objectives"
        );
        
        $page_render = (isset($param[$page]) ? $page : $this->default_page);
        $this->set_output($param[$page_render]);
    }

    public function objectives_form($action = '')
    {
        $mform = new objectiveshtml_form();

        if (!$mform->is_cancelled()) {
            if ($data = $mform->get_data()) {
                if (empty($data->id)) {
                    self::insert($data);
                } else {
                    self::update($data);
                }
            } else {
                if(empty($action)){
                    $mform->set_data($mform->get_data());
                    $text = $mform->render();
                }
            }
        }

        return $text;
    }

    private function insert($data)
    {
        global $DB, $USER;

        $objective = new \stdClass();

        $objective->title = $data->title;
        $objective->description = $data->description['text'];
        $timecreated = (new \DateTime())->setTimestamp(time());
        $objective->timecreated = $timecreated->getTimestamp();
        $objective->usercreated = $USER->id;
        $objective->starttime = $data->starttime;
        $objective->endtime = $data->endtime;

        $objective->id = $DB->insert_record(constant("self::TABLE"), $objective);

        return $objective;
    }

    private function update($data)
    {
        global $DB;

        $objective = $DB->get_record(constant("self::TABLE"), array('id' => $data->id));

        $objective->title = $data->title;
        $objective->description = $data->description['text'];
        $objective->starttime = $data->starttime;
        $objective->endtime = $data->endtime;
        
        $DB->update_record(constant("self::TABLE"), $objective);

        return $objective;
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
        $objective = $DB->get_record(constant("self::TABLE"), array('id' => $id));

        $timecompleted = (new \DateTime())->setTimestamp(time());
        $objective->timecompleted = $timecompleted->getTimestamp();
        
        $DB->update_record(constant("self::TABLE"), $objective);

        return $objective;
    }
}
