<?php

namespace block_goalsahead\output;

use block_goalsahead\controller;

class objectives extends controller{
    public function __construct($page = 'form') {
        $this->default_page = 'form';
        $page_render = (isset($output[$page])? $page : $this->default_page);

        $this->init_outputs($page_render);
    }

    protected function init_outputs($page) {
        $output['form'] = array(
            "render" => "template",
            "template" => "objective_form",
            "load_data" => "load_data_form"
        );

        
        $this->set_output($output[$page]);
    }
    
    public function load_data_form() {
        return [
            'str' => [
                'objectivestitle' => get_string('objectivestitle', 'block_objectivesahead'),
                'objectives' => get_string('objectivesname', 'block_objectivesahead'),
                'objectives' => get_string('objectivesname', 'block_objectivesahead')
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
}