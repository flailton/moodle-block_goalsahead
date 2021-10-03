<?php

namespace block_goalsahead\form;

use block_goalsahead\output\objectives;

//moodleform is defined in formslib.php
include_once("$CFG->libdir/formslib.php");

class objectiveshtml_form extends \moodleform
{
    //Add elements to form
    public function definition()
    {
        global $DB, $USER;

        $mform = $this->_form;

        $mform->addElement('hidden', 'goalsahead_page[form]', 0);
        $mform->setDefault('goalsahead_page[form]', 'objectives');

        $mform->addElement('header', 'objectivename', get_string('objectivename', 'block_goalsahead'));

        $mform->addElement('text', 'title', get_string('name'), 'maxlength="254" size="50"');
        $mform->addHelpButton('title', 'title', 'block_goalsahead');
        $mform->addRule('title', get_string('titlerequired', 'block_goalsahead'), 'required', null, 'client');
        $mform->addRule('title', get_string('titlemaxlength', 'block_goalsahead'), 'maxlength', 255, 'client');
        $mform->addRule('title', get_string('titleminlength', 'block_goalsahead'), 'minlength', 1, 'client');
        $mform->setType('title', PARAM_TEXT);

        $mform->addElement('editor', 'description', get_string('description'), null, $this->get_description_editor_options());
        $mform->addHelpButton('description', 'description', 'block_goalsahead');
        $mform->setType('description', PARAM_RAW);

        $mform->addElement('date_time_selector', 'starttime', get_string('starttime', 'block_goalsahead'));
        $mform->addHelpButton('starttime', 'starttime', 'block_goalsahead');
        $mform->addRule('starttime', get_string('starttimerequired', 'block_goalsahead'), 'required', null, 'client');
        $startDate = (new \DateTime())->setTimestamp(usergetmidnight(time()));
        $mform->setDefault('starttime', $startDate->getTimestamp());

        $mform->addElement('date_time_selector', 'endtime', get_string('endtime', 'block_goalsahead'), ['optional' => true]);
        $mform->addHelpButton('endtime', 'endtime', 'block_goalsahead');
        $endDate = (new \DateTime())->setTimestamp(usergetmidnight(time()));
        $endDate->modify('+1 month');
        $mform->setDefault('endtime', $endDate->getTimestamp());

        $id = optional_param('id', 0, PARAM_INT);
        $objectivenames = $this->get_autocomplete_data('objectives', $id);
        $goalnames = $this->get_autocomplete_data('goals');

        if(!empty($objectivenames) || !empty($goalnames)){   
            $mform->addElement('header', 'objectivelinks', get_string('objectivelinks', 'block_goalsahead'));
            $mform->addElement('static', 'objectiveslinkinfo', '', get_string('objectiveslinkinfo', 'block_goalsahead'));
            
            if (!empty($objectivenames)) {
                $mform->addElement('autocomplete', 'searchobjectives', '', $objectivenames, $this->get_autocomplete_options('noselectedobjectives'));
                $mform->addHelpButton('searchobjectives', 'searchobjectives', 'block_goalsahead');
            }
            
            if (!empty($goalnames)) {
                $mform->addElement('autocomplete', 'searchgoals', '', $goalnames, $this->get_autocomplete_options('noselectedgoals'));
                $mform->addHelpButton('searchgoals', 'searchgoals', 'block_goalsahead');
            }
        }

        $mform->addElement('hidden', 'id', null);
        $mform->setType('id', PARAM_INT);

        if ($objective = $DB->get_record('bga_objectives', ['id' => $id, 'usercreated' => $USER->id])) {
            $mform->setDefault('title', $objective->title);
            $mform->setDefault('description', ['text' => $objective->description, 'format' => 1]);
            $mform->setDefault('starttime', $objective->starttime);
            $mform->setDefault('endtime', $objective->endtime);
            $mform->setDefault('id', $objective->id);

            $objectiveselected = $this->get_autocomplete_selected_data('bga_objectives_objectives', ['objectiveid' => $id]);
            $mform->setDefault('searchobjectives', $objectiveselected);

            $goalselected = $this->get_autocomplete_selected_data('bga_objectives_goals', ['objectiveid' => $id]);
            $mform->setDefault('searchgoals', $goalselected);
        }

        // When two elements we need a group.
        $buttonarray = [];
        $classarray = ['class' => 'form-submit'];

        $buttonarray[] = $mform->createElement('submit', 'saveandreturn', get_string('savechangesandreturn'), $classarray);
        $buttonarray[] = $mform->createElement('cancel', 'cancel', get_string('cancelandback', 'block_goalsahead'));
        $mform->addGroup($buttonarray, 'buttonar', '', [' '], false);
        $mform->closeHeaderBefore('buttonar');
    }

    public function get_description_editor_options()
    {
        return [
            'subdirs' => 0,
            'maxbytes' => 0,
            'maxfiles' => 0,
            'changeformat' => 0,
            'context' => null,
            'noclean' => 0,
            'trusttext' => 0,
            'enable_filemanagement' => false
        ];
    }

    public function get_autocomplete_options($noselectionstring = '')
    {
        return [
            'multiple' => true,
            'noselectionstring' => get_string($noselectionstring, 'block_goalsahead')
        ];
    }

    public function get_autocomplete_data($classRoute = '', $id = 0)
    {
        global $USER;

        $arr = [];
        $path = '\\block_goalsahead\\output\\';
        $classpath = (class_exists($path . $classRoute) ? $path . $classRoute : false);
        if (empty($classpath)) {
            return $arr;
        }

        $class = new $classpath();
        $arrdata = ($class instanceof objectives
            ? $this->getDataObjectives($class, $id, ['usercreated' => $USER->id])
            : $class->get(['usercreated' => $USER->id]));
        foreach ($arrdata as $data) {
            if($id != $data->id){
                $arr[$data->id] = $data->title;
            }
        }

        return $arr;
    }

    public function get_autocomplete_selected_data($table = '', $cond)
    {
        global $DB;

        $data = $DB->get_records($table, $cond);

        $column = (strpos($table, 'goal') === false? 'subobjectiveid' : 'goalid');
        foreach($data as $row){
            $arr[] = $row->$column;
        }

        return $arr;
    }

    private function getDataObjectives($class, $id, $cond = []) 
    {
        global $DB;

        $arr = $class->getData(['main' => $cond], false, true);

        if(!empty($id) && !empty($DB->get_records('bga_objectives_objectives', ['subobjectiveid' => $id]))){
            $arr = [];
        }

        return $arr;
    }

    //Custom validation should be added here
    function validation($data, $files)
    {
        global $DB;

        $errors = parent::validation($data, $files);
        if (!empty($data['endtime']) && $data['starttime'] > $data['endtime']) {
            $errors['endtime'] = get_string('endtimebeforestarttime', 'block_goalsahead');
        }

        if (!empty($data['id']) && !empty($data['searchobjectives'])) {
            if (!empty($DB->get_records('bga_objectives_objectives', ['subobjectiveid' => $data['id']]))) {
                $errors['searchobjectives'] = get_string('cantlinkobjectives', 'block_goalsahead');
            }
        }

        return $errors;
    }
}
