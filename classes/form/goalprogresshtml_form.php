<?php
namespace block_goalsahead\form;

//moodleform is defined in formslib.php
include_once("$CFG->libdir/formslib.php");

class goalprogresshtml_form extends \moodleform {
    //Add elements to form
    public function definition() {
        global $DB;
        $mform = $this->_form;
        
        $mform->addElement('header','progressheader', get_string('progressheader', 'block_goalsahead'));

        $mform->addElement('hidden', 'goalsahead_page[form]', 0);
        $mform->setDefault('goalsahead_page[form]', 'goalprogress');

        $goalid = required_param('goalid', PARAM_INT);
        $mform->addElement('hidden', 'goalid', null);
        $mform->setDefault('goalid', $goalid);

        $mform->closeHeaderBefore('goalid');

        $goal = $DB->get_record('bga_goals',  ['id' => $goalid ]);
        $progressmeasure = ($goal->progresstype === 'W'? ' h' : ' %');
        $advancementprogressarr[] = $mform->createElement('text', 'advancementprogress', get_string('advancementprogress', 'block_goalsahead'), ['class' => 'only-numeric']);
        $advancementprogressarr[] = $mform->createElement('html', $progressmeasure);
        $mform->addGroup($advancementprogressarr, 'advancementprogressarr', get_string('advancementprogress', 'block_goalsahead'), [' '], false);

        $mform->addHelpButton('advancementprogressarr', 'advancementprogress', 'block_goalsahead');
        $mform->addRule('advancementprogressarr', get_string('progressrequired', 'block_goalsahead'), 'required', null, 'client');
        $mform->setType('advancementprogress', PARAM_INT);
        
        $mform->addElement('date_time_selector', 'timecreated', get_string('date'));
        $mform->addHelpButton('timecreated', 'timecreated', 'block_goalsahead');
        $mform->addRule('timecreated', get_string('timecreatedrequired', 'block_goalsahead'), 'required', null, 'client');
        $timecreated = (new \DateTime())->setTimestamp(time());
        $mform->setDefault('timecreated', $timecreated->getTimestamp());

        // When two elements we need a group.
        $buttonarray = [];
        $classarray = ['class' => 'form-submit'];
        
        $buttonarray[] = $mform->createElement('submit', 'save', get_string('save'), $classarray);
        $buttonarray[] = $mform->createElement('cancel', 'cancel', get_string('cancelandback', 'block_goalsahead'));
        $mform->addGroup($buttonarray, 'buttonar', '', [' '], false);
        $mform->closeHeaderBefore('buttonar');

    }

    //Custom validation should be added here
    function validation($data, $files) {
        global $DB;

        $errors = parent::validation($data, $files);

        if(!is_int($data['advancementprogress']) || $data['advancementprogress'] <= 0){
            $errors['advancementprogressarr'] = get_string('progressnumeric', 'block_goalsahead');
            return $errors;
        }

        $goal = $DB->get_record('bga_goals',  ['id' => $data['goalid'] ]);
        $accruedprogress = $DB->get_record_sql('
            SELECT IFNULL(SUM(gp.progress), 0) as total
            FROM {bga_goal_progress} gp
            WHERE gp.goalid = :goalid ',  
            ['goalid' => $data['goalid'] ]
        );

        $progresstotal = ($goal->progresstype === 'W'? $goal->progresstotal : 100);
        if(($accruedprogress->total + $data['advancementprogress']) > $progresstotal){
            $errors['advancementprogressarr'] = get_string('progressovercomplete', 'block_goalsahead');
        }

        return $errors;
    }
    
}