<?php

namespace block_goalsahead\output;

defined('MOODLE_INTERNAL') || die();

class renderer extends \plugin_renderer_base {

    protected $output_config;

    /**
     * Render plugin content.
     *
     * @param \templatable $output
     * @return string|boolean
     */
    public function render_content(\templatable $output) {
        $page = null;

        $data = $output->export_for_template($this);

        $this->output_config = $output->get_output();
        $render_method = "render_".$this->output_config['render'];

        if(!empty($render_method) && method_exists($this, $render_method)){
            $page = $this->$render_method($output, $data);
        }

        return $page;
    }

    /**
     * Render content from template.
     *
     * @param \templatable $output
     * @return string|boolean
     */
    public function render_template($output, $data) {
        return $this->render_from_template('block_goalsahead/' . $this->output_config['route'], $data);
    }

    /**
     * Render content from html.
     *
     * @param \templatable $output
     * @return string|boolean
     */
    public function render_forms($class, $data) {        
        if(!empty($class)){
            $method = $this->output_config['call'];
            $page = $class->$method($data);
        }

        return $page;
    }

    /**
     * Call some action.
     *
     * @param \templatable $output
     * @return string|boolean
     */
    public function render_action($class, $data) {
        $page = '';

        if(!empty($class)){
            $method = required_param('action', PARAM_TEXT);
            $class->$method();

            if(!empty($this->output_config['redirect'])){
                $classRedirect = get_class($class);
                $controller = new $classRedirect($this->output_config['redirect']);

                $page = $this->render_content($controller);
            }
        }

        return $page;
    }
}

?>