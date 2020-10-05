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
 * Form for editing goalsahead block instances.
 *
 * @package     block_goalsahead
 * @copyright   2020 Flailton Batista <flailton@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Form for editing block_goalsahead block instances.
 *
 * @package    block_goalsahead
 * @copyright  2020 Flailton Batista <flailton@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_goalsahead_edit_form extends block_edit_form {

    /**
     * Extends the configuration form for block_goalsahead.
     */
    protected function specific_definition($mform) {

        // Section header title.
        $mform->addElement('header', 'config_header', get_string('blocksettings', 'block'));

        // Please keep in mind that all elements defined here must start with 'config_'.
        // A sample string variable with a default value.
        $mform->addElement('text', 'config_title', get_string('blocktitle', 'block_goalsahead'));
    	$mform->setDefault('config_title', 'default value');
   		$mform->setType('config_title', PARAM_TEXT); 
   		
        $mform->addElement('text', 'config_text', get_string('blockstring', 'block_goalsahead'));
        $mform->setDefault('config_text', 'default value');
        $mform->setType('config_text', PARAM_RAW);   

    }
}
