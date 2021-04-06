<?php
namespace block_goalsahead\form;

//moodleform is defined in formslib.php
include_once("$CFG->libdir/formslib.php");

class goalprogresshtml_form extends \moodleform {
    //Add elements to form
    public function definition() {
        $mform = $this->_form;
        
        $mform->addElement('header','progressheader', get_string('progressheader', 'block_goalsahead'));

        $mform->addElement('hidden', 'goalsahead_page[form]', 0);
        $mform->setDefault('goalsahead_page[form]', 'goalprogress');

        $goalid = required_param('goalid', PARAM_INT);
        $mform->addElement('hidden', 'goalid', null);
        $mform->setDefault('goalid', $goalid);

        $mform->closeHeaderBefore('goalid');

        $mform->addElement('text', 'progress', get_string('progress', 'block_goalsahead'), 'maxlength="254" size="50"');
        $mform->addHelpButton('progress', 'progress', 'block_goalsahead');
        $mform->addRule('progress', get_string('progressrequired', 'block_goalsahead'), 'required', null, 'client');
        $mform->addRule('progress', get_string('progressnumeric', 'block_goalsahead'), 'numeric', null, 'client');
        $mform->setType('progress', PARAM_INT);
        
        $mform->addElement('date_time_selector', 'timecreated', get_string('date'));
        $mform->addHelpButton('timecreated', 'timecreated', 'block_goalsahead');
        $mform->addRule('timecreated', get_string('timecreatedrequired', 'block_goalsahead'), 'required', null, 'client');
        $timecreated = (new \DateTime())->setTimestamp(time());
        $mform->setDefault('timecreated', $timecreated->getTimestamp());

        // When two elements we need a group.
        $buttonarray = array();
        $classarray = array('class' => 'form-submit');
        
        $buttonarray[] = $mform->createElement('submit', 'saveandreturn', get_string('savechangesandreturn'), $classarray);
        $buttonarray[] = $mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');

    }

    //Custom validation should be added here
    function validation($data, $files) {
        global $DB;

        $errors = parent::validation($data, $files);
        if(((int) $data['progress']) <= 0){
            $errors['progress'] = get_string('progressminlength', 'block_goalsahead');
        }

        $goal = $DB->get_record('bga_goals',  ['id' => $data['goalid'] ]);
        $accruedprogress = $DB->get_record_sql('
            SELECT IFNULL(SUM(gp.progress), 0) as total
            FROM mdl_bga_goal_progress gp
            WHERE gp.goalid = :goalid ',  
            ['goalid' => $data['goalid'] ]
        );

        $progresstotal = ($goal->progresstype === 'W'? $goal->progresstotal : 100);
        if(($accruedprogress->total + $data['progress']) > $progresstotal){
            $errors['progress'] = get_string('progressovercomplete', 'block_goalsahead');
        }
        
        return $errors;
    }
    
}