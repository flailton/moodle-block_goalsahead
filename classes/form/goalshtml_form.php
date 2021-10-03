<?php

namespace block_goalsahead\form;

//moodleform is defined in formslib.php
include_once("$CFG->libdir/formslib.php");

class goalshtml_form extends \moodleform
{
    //Add elements to form
    public function definition()
    {
        global $DB, $USER;

        $mform = $this->_form;

        $mform->addElement('hidden', 'goalsahead_page[form]', 0);
        $mform->setDefault('goalsahead_page[form]', 'goals');

        $mform->addElement('header', 'goalname', get_string('goalname', 'block_goalsahead'));

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

        $mform->addElement('date_time_selector', 'endtime', get_string('endtime', 'block_goalsahead'));
        $mform->addHelpButton('endtime', 'endtime', 'block_goalsahead');
        $mform->addRule('endtime', get_string('endtimerequired', 'block_goalsahead'), 'required', null, 'client');
        $endDate = (new \DateTime())->setTimestamp(usergetmidnight(time()));
        $endDate->modify('+1 month');
        $mform->setDefault('endtime', $endDate->getTimestamp());

        $progressTypeOptions = [
            'A' => get_string('automatic', 'block_goalsahead'), 
            'M' => get_string('manual', 'block_goalsahead')
        ];

        $mform->addElement('select', 'progresstype', get_string('progresstype', 'block_goalsahead'), $progressTypeOptions);
        $mform->addHelpButton('progresstype', 'progresstype', 'block_goalsahead');
        $mform->addRule('progresstype', get_string('progresstyperequired', 'block_goalsahead'), 'required', null, 'client');

        $progressMeasurementOptions = [
            'P' => get_string('percent', 'block_goalsahead'),
            'W' => get_string('workload', 'block_goalsahead')
        ];

        $mform->addElement('select', 'progressmeasurement', get_string('progressmeasurement', 'block_goalsahead'), $progressMeasurementOptions);
        $mform->addHelpButton('progressmeasurement', 'progressmeasurement', 'block_goalsahead');
        
        $mform->disabledIf('progressmeasurement', 'progresstype', 'neq', 'M');
        $mform->hideIf('progressmeasurement', 'progresstype', 'neq', 'M');

        $progresstotalarr[] = $mform->createElement('html', get_string('total') . ' &nbsp;');
        $progresstotalarr[] = $mform->createElement('text', 'progresstotal', '', 'size="1"');
        $progresstotalarr[] = $mform->createElement('html', ' h');
        $mform->addGroup($progresstotalarr, 'progresstotalarr', '', [' '], false);

        $mform->disabledIf('progresstotal', 'progresstype', 'neq', 'M');
        $mform->hideIf('progresstotalarr', 'progresstype', 'neq', 'M');

        $mform->disabledIf('progresstotal', 'progressmeasurement', 'neq', 'W');
        $mform->hideIf('progresstotalarr', 'progressmeasurement', 'neq', 'W');

        // $mform->addElement('header', 'goallinks', get_string('goallinks', 'block_goalsahead'));

        # TODO implementar autocomplemento
        // $searcharray[] = $mform->createElement('text', 'searchcourse', get_string('course'));
        // $searcharray[] = $mform->createElement('button', 'searchbutton', '<i class="icon fa fa-search fa-fw " title="'.get_string('search').'" aria-label="'.get_string('search').'"></i>');
        // $mform->addGroup($searcharray, 'searcharr', '', [' '], false);

        // $mform->disabledIf('searchcourse', 'progresstype', 'neq', 'D');
        // $mform->disabledIf('searchbutton', 'progresstype', 'neq', 'D');

        $mform->addElement('hidden', 'id', null);
        $mform->setType('id', PARAM_INT);

        $id = optional_param('id', 0, PARAM_INT);
        if ($goal = $DB->get_record('bga_goals',['id' => $id, 'usercreated' => $USER->id])) {
            $mform->setDefault('title', $goal->title);
            $mform->setDefault('description', ['text' => $goal->description, 'format' => 1]);
            $mform->setDefault('starttime', $goal->starttime);
            $mform->setDefault('endtime', $goal->endtime);
            if($goal->progresstype !== 'D'){
                $mform->setDefault('progresstype', 'M');
            }
            $mform->setDefault('progressmeasurement', $goal->progresstype);
            $mform->setDefault('progresstotal', $goal->progresstotal);
            $mform->setDefault('id', $goal->id);
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

    //Custom validation should be added here
    function validation($data, $files)
    {
        global $DB;

        $errors = parent::validation($data, $files);
        if (!empty($data['endtime']) && $data['starttime'] > $data['endtime']) {
            $errors['endtime'] = get_string('endtimebeforestarttime', 'block_goalsahead');
        }

        if ($data['progresstype'] === 'M' && $data['progressmeasurement'] === 'W' && empty($data['progresstotal'])) {
            $errors['progresstotalarr'] = get_string('progresstotalrequired', 'block_goalsahead');
        }

        return $errors;
    }
}
