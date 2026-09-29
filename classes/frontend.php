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
 * Availability plugin - Frontend class
 *
 * @package    availability_plugin
 * @copyright  2025 Mahmoud Chehada, ssystems GmbH <mchehada@ssystems.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace availability_plugin;

/**
 * Availability plugin - Frontend class
 *
 * @package    availability_plugin
 * @copyright  2025 Mahmoud Chehada, ssystems GmbH
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class frontend extends \core_availability\frontend {
    /**
     * Retrieves the initialization parameters required for JavaScript.
     *
     * @param mixed $course The course object or identifier.
     * @param \cm_info|null $cm The course module information, null if not applicable.
     * @param \section_info|null $section The section information, null if not applicable.
     * @return array An associative array containing initialization components for JavaScript.
     */
    public function get_javascript_init_params($course, ?\cm_info $cm = null, ?\section_info $section = null) {
        $components = [];
        $pm = \core_plugin_manager::instance();
        foreach ($pm->get_plugins() as $type => $typedplugins) {
            foreach ($typedplugins as $name => $info) {
                $components[] = $type . '_' . $name;
            }
        }
        sort($components, SORT_STRING);

        return [[
            'components'   => $components,
        ]];
    }

    /**
     * Determines whether adding is allowed within the given context.
     *
     * @param mixed $course The course object or identifier.
     * @param \cm_info|null $cm The course module information, null if not applicable.
     * @param \section_info|null $section The section information, null if not applicable.
     * @return bool True if adding is allowed, otherwise false.
     */
    public function allow_add($course, ?\cm_info $cm = null, ?\section_info $section = null) {
        $context = $cm
            ? \context_module::instance($cm->id)
            : \context_course::instance($course->id);

        return has_capability('availability/plugin:addinstance', $context);
    }

    /**
     * Retrieves an array of JavaScript string identifiers.
     *
     * @return array An array of string identifiers used for JavaScript localization or dynamic text loading.
     */
    public function get_javascript_strings() {
        return ['pluginnameinput', 'missingpluginname'];
    }
}
