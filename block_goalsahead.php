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
        global $PAGE, $OUTPUT;

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
            $path = '\\block_goalsahead\\output\\';
            $page_output = optional_param('page', '', PARAM_TEXT);
            $template_output = optional_param('template', '', PARAM_TEXT);
            $class = $path . (class_exists($path . $page_output)? $page_output : 'dashboard');
            $controller = new $class($template_output);
            $text = $output->render_generic($controller);
            $this->content->text = $text;
        }

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
        echo '<pre>';
        var_dump($var);
        echo '</pre>';
    }
}
