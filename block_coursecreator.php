<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Course creator block
 *
 * @package    block_coursecreator
 * @author     Laurent GUILLET <laurent.guillet@u-pem.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_coursecreator extends block_base {

    public function init() {

        $this->title = get_string('coursecreator', 'block_coursecreator');
    }

    public function get_content() {

        global $CFG;

        if ($this->content !== null) {

            return $this->content;
        }

        $systemcontext = context_system::instance();

        if (has_capability('block/coursecreator:createcourse', $systemcontext)) {

            $this->content = new stdClass;
            $this->content->text = '<a href = ' . $CFG->wwwroot . '/blocks/coursecreator/coursecreation.php>'
                    . '<img src = ' . $CFG->wwwroot . '/blocks/coursecreator/pix/addcourse.png width="50" height="50"/>'
                    . '</a>';
        }

        return $this->content;
    }

    public function has_config() {

        return true;
    }

}
