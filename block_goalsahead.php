<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Block goalsahead is defined here.
 *
 * @package     block_goalsahead
 * @copyright   2020 Flailton Batista <flailton@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * goalsahead block.
 *
 * @package    block_goalsahead
 * @copyright  2020 Flailton Batista <flailton@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_goalsahead extends block_base {

    /**
     * Initializes class member variables.
     */
    public function init() {
        // Needed by Moodle to differentiate between blocks.
        $this->title = get_string('pluginname', 'block_goalsahead');
    }

    /**
     * Returns the block contents.
     *
     * @return stdClass The block contents.
     */
    public function get_content() {
        global $CFG;

        if ($this->content !== null) {
            return $this->content;
        }

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->items = array();
        $this->content->icons = array();

        $output = $this->page->get_renderer('block_goalsahead');
        if (!empty($this->config->text)) {
            $this->content->text = $this->config->text;
        } else {
            # TODO substituir valores fixos por constantes
            //$this->debug($_POST);
            //$this->debug(enrol_get_my_courses());

            $templatePage = isset($_POST['goalsahead_page']) && key($_POST['goalsahead_page']) !== null? key($_POST['goalsahead_page']) : "";
            $pageOutput = isset($_POST['goalsahead_page'][$templatePage])? $_POST['goalsahead_page'][$templatePage] : "";
            $dataPage = isset($_POST['goalsahead_page']['data'])? $_POST['goalsahead_page']['data'] : [];

            $templateAction = isset($_POST['goalsahead_action']) && key($_POST['goalsahead_action']) !== null? key($_POST['goalsahead_action']) : "";
            $action = isset($_POST['goalsahead_page'][$templateAction]) && key($_POST['goalsahead_page'][$templateAction]) !== null? key($_POST['goalsahead_page'][$templateAction]) : "";
            $pageAction = isset($_POST['goalsahead_page'][$templateAction][$action])? $_POST['goalsahead_page'][$templateAction][$action] : "";
            $dataAction = isset($_POST['goalsahead_page']['data'])? $_POST['goalsahead_page']['data'] : [];

            $path = '\\block_goalsahead\\output\\';

            if(!empty($action)){
                # TODO chamar a ação
                $classAction = $path . (class_exists($path . $pageAction)? $pageAction : false);
                if($classAction){
                    $controllerAction = new $classAction($templateAction, $dataAction);
                    if(method_exists($controllerAction, $action)){
                        $controllerAction->$action();
                    }
                }
            }

            $class = $path . (class_exists($path . $pageOutput)? $pageOutput : 'dashboard');
            $controller = new $class($templatePage, $dataPage);
            $text = $output->render_content($controller);

            $this->content->text = (!empty($text)? $text : "");
            
        }
        
        $this->page->requires->jquery();
        $this->page->requires->js(new moodle_url($CFG->wwwroot . '/blocks/goalsahead/webroot/js/block_goalsahead.js'));

        return $this->content;
    }

    /**
     * Defines configuration data.
     *
     * The function is called immediatly after init().
     */
    public function specialization() {

        // Load user defined title and make sure it's never empty.
        if (empty($this->config->title)) {
            $this->title = get_string('pluginname', 'block_goalsahead');
        } else {
            $this->title = $this->config->title;
        }
    }

    /**
     * Enables global configuration of the block in settings.php.
     *
     * @return bool True if the global configuration is enabled.
     */
    function has_config() {
        return true;
    }

    public function debug($var) {
        echo '<pre><br><br><br><br>';
        var_dump($var);
        echo '</pre>';
    }
}
