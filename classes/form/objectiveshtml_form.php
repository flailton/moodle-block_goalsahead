<?php
namespace block_goalsahead\form;

//moodleform is defined in formslib.php
include_once("$CFG->libdir/formslib.php");

class objectiveshtml_form extends \moodleform {
    //Add elements to form
    public function definition() {
        global $CFG, $DB, $USER;

        $mform = $this->_form;
 
        if ($pending = $DB->get_records('course_request', array('requester' => $USER->id))) {
            $mform->addElement('header', 'pendinglist', get_string('coursespending'));
            $list = array();
            foreach ($pending as $cp) {
                $list[] = format_string($cp->fullname);
            }
            $list = implode(', ', $list);
            $mform->addElement('static', 'pendingcourses', get_string('courses'), $list);
        }

        $mform->addElement('header','objectivedetails', get_string('objectiverequestdetails', 'block_goalsahead'));

        $mform->addElement('text', 'name', get_string('objectivename', 'block_goalsahead'), 'maxlength="254" size="50"');
        $mform->addHelpButton('name', 'objectivename');
        $mform->addRule('name', get_string('objectivename', 'block_goalsahead'), 'required', null, 'client');
        $mform->setType('name', PARAM_TEXT);

        $mform->addElement('editor', 'description_editor', get_string('description'), null);
        $mform->addHelpButton('description_editor', 'description');
        $mform->setType('summary_editor', PARAM_RAW);
        
        $mform->addElement('date_time_selector', 'enddateobjective', get_string('enddateobjective', 'block_goalsahead'));
        $mform->addHelpButton('enddate', 'enddateobjective');
        $date = (new \DateTime())->setTimestamp(usergetmidnight(time()));
        $date->modify('+1 day');
        $mform->setDefault('enddateobjective', $date->getTimestamp());
        
        $mform->addElement('header','objectivelinks', get_string('objectivelinks', 'block_goalsahead'));

        // When two elements we need a group.
        $buttonarray = array();
        $classarray = array('class' => 'form-submit');
        
        $buttonarray[] = $mform->createElement('submit', 'saveandreturn', get_string('savechangesandreturn'), $classarray);
        $buttonarray[] = $mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');

        $mform->addElement('hidden', 'id', null);
        $mform->setType('id', PARAM_INT);

    }
    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}