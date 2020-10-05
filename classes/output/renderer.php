<?php

namespace block_goalsahead\output;

defined('MOODLE_INTERNAL') || die();

class renderer extends \plugin_renderer_base {

    /**
     * Render a questionnaire index page.
     *
     * @param \templatable $output
     * @return string|boolean
     */
    public function render_generic(\templatable $output) {
        $data = $output->export_for_template($this);

        $output_page = $output->get_output();
        return $this->render_from_template('block_goalsahead/' . $output_page['template'], $data);
    }
}

?>