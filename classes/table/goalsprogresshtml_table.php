<?php

namespace block_goalsahead\table;

use stdClass;

class goalsprogresshtml_table extends goalsaheadhtml_table
{

    protected function get_html_head()
    {
        return [
            get_string('date'),
            get_string('progress', 'block_goalsahead'),
            ''
        ];
    }

    protected function get_html_data($data, $progresstype)
    {
        $records = [];
        foreach($data as $val){
            $item = [];
            $item['timecreated'] = userdate($val->timecreated, get_string('strftimedatetime', 'core_langconfig'));
            $item['progress'] = $val->progress . ($progresstype === 'P'? '%' : ($progresstype === 'W'? 'h' : ''));
            $item['action']  = '<a href="javascript:void(0);" style="font-size: 20px; color: tomato;" class="btn-action" route="action" data-action="delete" data-page="goalprogress" data-goalid="'.$val->goalid.'" data-id="'.$val->id.'">';
            $item['action'] .= '<i class="fa fa-times-circle"></i>';
            $item['action'] .= '</a>';

            $records[] = $item;
        }

        return $records;
    }

}
