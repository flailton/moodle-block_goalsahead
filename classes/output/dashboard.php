<?php

namespace block_goalsahead\output;

use block_goalsahead\controller;

class dashboard extends controller{
    public function __construct($page = 'dashboard') {
        $this->default_page = 'dashboard';

        $this->init_outputs($page);
    }

    public function init_outputs($page = 'dashboard') {
        $output['dashboard'] = array(
            "template" => "dashboard",
            "load_data" => "load_data_dashboard"
        );

        $page_render = (isset($output[$page])? $page : $this->default_page);
        $this->set_output($output[$page_render]);
    }

    public function load_data_dashboard() {
        return [
            'page_title' => get_string('objectivesandgoals', 'block_goalsahead'),
            'btn_objectives' => get_string('objectivesname', 'block_goalsahead'),
            'btn_goals' => get_string('goalsname', 'block_goalsahead'),
            'objectives' => [
                [
                    'count' => 1,
                    'title' => 'Obj 1',
                    'progress' => 70,
                    'goals' => [
                        [
                            'title' => 'Meta 1',
                            'progress' => 12,
                            'complete' => false
                        ],
                        [
                            'title' => 'Meta 1',
                            'progress' => 100,
                            'complete' => true
                        ]
                    ]
                ],
                [
                    'count' => 2,
                    'title' => 'Obj 2',
                    'progress' => 20,
                    'goals' => [
                        [
                            'title' => 'Meta 2',
                            'progress' => 61,
                            'complete' => false
                        ]
                    ]
                ],
                [
                    'count' => 3,
                    'title' => 'Obj 3',
                    'progress' => 95,
                    'goals' => [
                        [
                            'title' => 'Meta 3',
                            'progress' => 5,
                            'complete' => false
                        ]
                    ]
                ],
            ]
        ];
    }
}