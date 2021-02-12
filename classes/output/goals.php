<?php

namespace block_goalsahead\output;

use block_goalsahead\controller;
use block_goalsahead\form\goalshtml_form;

class goals extends controller{
    public function __construct($page = 'form', $data = []) {
        $this->default_page = 'form';
        $page_render = (isset($output[$page])? $page : $this->default_page);

        $this->init_outputs($page_render);
    }

    protected function init_outputs($page) {
        $output['form'] = array(
            "render" => "forms",
            "template" => "output\\goals",
            "load_data" => "load_data_form",
            "writer" => "goals_form"
        );

        $this->set_output($output[$page]);
    }
    
    public function load_data_form() {
        return [
            'str' => [
                'goalstitle' => get_string('goalstitle', 'block_goalsahead'),
                'objectives' => get_string('objectivesname', 'block_goalsahead'),
                'goals' => get_string('goalsname', 'block_goalsahead')
            ],
            'data' => [
                'id' => 1,
                'is_goal' => false,
                'is_objective' => true,
                'has_associate_data' => true,
                'title' => 'Obj 1',
                'progress' => 70
            ]
        ];
    }
    
    public function goals_form() {
        $mform = new goalshtml_form();
        
        if ($mform->is_cancelled()) {
                //Handle form cancel operation, if cancel button is present on form
        } else if ($fromform == $mform->get_data()) {
            $text = $mform->render();
                //In this case you process validated data. $mform->get_data() returns data posted in form.
        } else {
                // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
                // or on the first display of the form.

                //Set default data (if any)
                $mform->set_data();

                //displays the form
                $text = $mform->render();
        }

        return $text;
    }
}