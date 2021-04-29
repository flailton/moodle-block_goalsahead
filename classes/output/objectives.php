<?php

namespace block_goalsahead\output;

use block_goalsahead\controller;
use block_goalsahead\form\objectiveshtml_form;
use goals;

class objectives extends controller
{
    private const TABLE = 'bga_objectives';
    private const TABLE_OBJECTIVES_GOALS = 'bga_objectives_goals';
    private const TABLE_OBJECTIVES_OBJECTIVES = 'bga_objectives_objectives';

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

        $this->linkdata($objective->id, $data);

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

        $this->unlinkdata($data->id);
        $this->linkdata($data->id, $data);

        return $objective;
    }

    public function delete()
    {
        global $DB;

        $id = required_param('id', PARAM_INT);
        $this->unlinkdata($id);

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

    public function get($cond = [])
    {
        global $DB;

        $goals = $DB->get_records(constant("self::TABLE"), $cond, 'title ASC');

        return $goals;
    }

    public function linkdata($id, $data)
    {
        global $DB;

        if(!empty($data->searchgoals)){
            $objectivesGoals = new \stdClass();
            
            $objectivesGoals->objectiveid = $id;
            $timecreated = (new \DateTime())->setTimestamp(time());
            $objectivesGoals->timecreated = $timecreated->getTimestamp();
            $objectivesGoals->displayorder = 0;
            
            foreach($data->searchgoals as $searchgoal){
                $objectivesGoals->goalid = (int) $searchgoal;
                $DB->insert_record(constant("self::TABLE_OBJECTIVES_GOALS"), $objectivesGoals);
            }
        }
        
        if(!empty($data->searchobjectives)){
            $objectivesObjectives = new \stdClass();
            
            $objectivesObjectives->objectiveid = $id;
            $timecreated = (new \DateTime())->setTimestamp(time());
            $objectivesObjectives->timecreated = $timecreated->getTimestamp();
            $objectivesObjectives->displayorder = 0;
            
            foreach($data->searchobjectives as $searchobjective){
                $objectivesObjectives->subobjectiveid = (int) $searchobjective;
                $DB->insert_record(constant("self::TABLE_OBJECTIVES_OBJECTIVES"), $objectivesObjectives);
            }
        }
    }

    public function unlinkdata($id)
    {
        global $DB;

        $DB->delete_records(constant("self::TABLE_OBJECTIVES_GOALS"), array('objectiveid' => $id));
        $DB->delete_records(constant("self::TABLE_OBJECTIVES_OBJECTIVES"), array('objectiveid' => $id));
    }
}
