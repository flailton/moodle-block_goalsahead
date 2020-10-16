<?php

namespace block_goalsahead;

abstract class controller implements \renderable, \templatable {
    protected $output;
    private $default_page;

    abstract protected function init_outputs($page);

    public function get_output() {
        return $this->output;
    }

    public function set_output(array $output) {
        $this->output = $output;
    }

    public function export_for_template(\renderer_base $output) {
        $data = [];
        $action = $this->output['load_data'];

        if(!empty($action) && method_exists($this, $action)){
            $data = $this->$action();
        }

        return $data;
    }
}