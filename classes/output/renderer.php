<?php

namespace block_goalsahead\output;

defined('MOODLE_INTERNAL') || die();

class renderer extends \plugin_renderer_base {

    /**
     * Render plugin content.
     *
     * @param \templatable $output
     * @return string|boolean
     */
    public function render_content(\templatable $output) {
        $page = null;

        $data = $output->export_for_template($this);

        $output_config = $output->get_output();
        $render_method = "render_".$output_config['render'];

        if(!empty($render_method) && method_exists($this, $render_method)){
            $page = $this->$render_method($output_config, $data);
        }

        return $page;
    }

    /**
     * Render content from template.
     *
     * @param \templatable $output
     * @return string|boolean
     */
    public function render_template($output_config, $data) {
        return $this->render_from_template('block_goalsahead/' . $output_config['template'], $data);
    }

    /**
     * Render content from html.
     *
     * @param \templatable $output
     * @return string|boolean
     */
    public function render_forms($output_config, $data) {
        $page = null;
        $path = '\\block_goalsahead\\';
        $class = (class_exists($path . $output_config['template'])? $path . $output_config['template'] : null);
        
        if(!empty($class)){
            $view = new $class();
            $method = $output_config['writer'];
            $page = $view->$method($data);
        }

        return $page;
    }
}

?>