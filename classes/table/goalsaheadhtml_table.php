<?php

namespace block_goalsahead\table;

abstract class goalsaheadhtml_table
{
    protected $table;
    protected $head;
    protected $data;

    abstract protected function get_html_head();

    abstract protected function get_html_data($data, $progresstype);

    function __construct($data = [], $progresstype = '')
    {
        $this->table = new \html_table();
        if(!empty($data)){
            $this->table->head = $this->get_html_head();
            $this->table->data = $this->get_html_data($data, $progresstype);
        }
    }

    public function render()
    {
        return \html_writer::table($this->table);
    }

}
