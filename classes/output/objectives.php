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
        $page_render = (isset($output[$page]) ? $page : $this->default_page);

        $this->init_outputs($page_render);
    }

    protected function init_outputs($page)
    {
        $param['form'] = array(
            "render" => "forms",
            "template" => "output\\objectives",
            "output" => "objectives_form"
        );

        $output = $param;

        $this->set_output($output[$page]);
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

    private function complete($id)
    {
        global $DB;

        $objective = $DB->get_record(constant("self::TABLE"), array('id' => $id));

        $timecompleted = (new \DateTime())->setTimestamp(time());
        $objective->timecompleted = $timecompleted->getTimestamp();
        
        $DB->update_record(constant("self::TABLE"), $objective);

        return $objective;
    }
}
