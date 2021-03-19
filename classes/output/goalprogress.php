<?php

namespace block_goalsahead\output;

use block_goalsahead\controller;
use block_goalsahead\form\goalprogresshtml_form;

class goalprogress extends controller
{
    private const TABLE = 'bga_goal_progress';

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
            "route" => "output\\goalprogress",
            "call" => "goalprogress_form"
        );

        $param['action'] = array(
            "render" => "action",
            "route" => "output\\goalprogress"
        );

        $output = $param;

        $this->set_output($output[$page]);
    }

    public function goalprogress_form($action = '')
    {
        $mform = new goalprogresshtml_form();

        if (!$mform->is_cancelled()) {
            if ($data = $mform->get_data()) {
                if (empty($data->id)) {
                    self::insert($data);
                } else {
                    self::delete($data);
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
        global $DB;

        $goalprogress = new \stdClass();

        $goalprogress->goalid = $data->goalid;
        $goalprogress->timecreated = $data->timecreated;
        $goalprogress->progress = $data->progress;

        $goalprogress->id = $DB->insert_record(constant("self::TABLE"), $goalprogress);

        return $goalprogress;
    }

    private function delete()
    {
        global $DB;

        $id = required_param('id', PARAM_INT);
        return $DB->delete_records(constant("self::TABLE"), array('id' => $id));
    }
}
