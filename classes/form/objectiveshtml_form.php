<?php
namespace block_goalsahead\form;

//moodleform is defined in formslib.php
include_once("$CFG->libdir/formslib.php");

class objectiveshtml_form extends \moodleform {
    //Add elements to form
    public function definition() {
        global $DB, $USER;

        $mform = $this->_form;

        $mform->addElement('hidden', 'goalsahead_page[form]', 0);
        $mform->setDefault('goalsahead_page[form]', 'objectives');

        $mform->addElement('header','objectivename', get_string('objectivename', 'block_goalsahead'));

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

        $mform->addElement('date_time_selector', 'endtime', get_string('endtime', 'block_goalsahead'), array('optional' => true));
        $mform->addHelpButton('endtime', 'endtime', 'block_goalsahead');
        $endDate = (new \DateTime())->setTimestamp(usergetmidnight(time()));
        $endDate->modify('+1 month');
        $mform->setDefault('endtime', $endDate->getTimestamp());

        $mform->addElement('header','objectivelinks', get_string('objectivelinks', 'block_goalsahead'));

        $options = [
            'objectives' => get_string('objectivesname', 'block_goalsahead'), 
            'goals' => get_string('goalsname', 'block_goalsahead')
        ];

        $mform->addElement('select', 'searchtype', get_string('searchtype', 'block_goalsahead'), $options);
        $mform->addHelpButton('searchtype', 'searchtype', 'block_goalsahead');

        # TODO implementar autocomplemento
        $mform->addElement('text', 'search', null, 'size="100"');

        $mform->addElement('hidden', 'id', null);
        $mform->setType('id', PARAM_INT);

        $id = optional_param('id', 0, PARAM_INT);
        if ($objective = $DB->get_record('bga_objectives', array('id' => $id, 'usercreated' => $USER->id))) {
            $mform->setDefault('title', $objective->title);
            $mform->setDefault('description', ['text' => $objective->description, 'format' => 1]);
            $mform->setDefault('starttime', $objective->starttime);
            $mform->setDefault('endtime', $objective->endtime);
            $mform->setDefault('id', $objective->id);
        }

        // When two elements we need a group.
        $buttonarray = array();
        $classarray = array('class' => 'form-submit');
        
        $buttonarray[] = $mform->createElement('submit', 'saveandreturn', get_string('savechangesandreturn'), $classarray);
        $buttonarray[] = $mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');

    }

    public function get_description_editor_options() {
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
    function validation($data, $files) {
        global $DB;

        $errors = parent::validation($data, $files);
        if(!empty($data['endtime']) && $data['starttime'] > $data['endtime']){
            $errors['endtime'] = get_string('endtimebeforestarttime', 'block_goalsahead');
        }
        
        return $errors;
    }
    
}