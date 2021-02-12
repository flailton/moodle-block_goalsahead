<?php

namespace block_goalsahead\output;

use block_goalsahead\controller;

class dashboard extends controller{
    public function __construct($page = 'dashboard', $data = []) {
        $this->default_page = 'dashboard';

        $this->init_outputs($page);
    }

    public function init_outputs($page = 'dashboard') {
        $output['dashboard'] = array(
            "render" => "template",
            "template" => "dashboard",
            "writer" => null,
            "load_data" => "load_data_dashboard"
        );

        $page_render = (isset($output[$page])? $page : $this->default_page);
        $this->set_output($output[$page_render]);
    }

    public function load_data_dashboard() {
        return [
            'str' => [
                'dashboardtitle' => get_string('dashboardtitle', 'block_goalsahead'),
                'objectives' => get_string('objectivesname', 'block_goalsahead'),
                'goals' => get_string('goalsname', 'block_goalsahead')
            ],
            'data' => [
                [
                    'id' => 1,
                    'is_goal' => false,
                    'is_objective' => true,
                    'has_associate_data' => true,
                    'title' => 'Obj 1',
                    'progress' => 70,
                    'associate_data' => [
                        [
                            'id' => 2,
                            'is_goal' => true,
                            'is_objective' => false,
                            'title' => 'Meta 1.1',
                            'progress' => 100,
                            'complete' => true
                        ],
                        [
                            'id' => 8,
                            'is_goal' => false,
                            'is_objective' => true,
                            'has_associate_data' => true,
                            'title' => 'Obj 1.1',
                            'progress' => 12,
                            'associate_data' => [
                                [
                                    'id' => 2,
                                    'is_goal' => true,
                                    'is_objective' => false,
                                    'title' => 'Meta 1.1.1',
                                    'progress' => 74
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'id' => 2,
                    'is_goal' => true,
                    'is_objective' => false,
                    'title' => 'Meta 1',
                    'progress' => 20
                ],
                [
                    'id' => 3,
                    'is_goal' => false,
                    'is_objective' => true,
                    'has_associate_data' => true,
                    'title' => 'Obj 2',
                    'progress' => 95,
                    'associate_data' => [
                        [
                            'id' => 6,
                            'is_goal' => true,
                            'is_objective' => false,
                            'title' => 'Meta 3',
                            'progress' => 5
                        ]
                    ]
                ],
                [
                    'id' => 4,
                    'is_goal' => false,
                    'is_objective' => true,
                    'title' => 'Obj 3',
                    'progress' => 0
                ]
            ]
        ];
    }
}