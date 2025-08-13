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

        // Determine context (module or course).
        $context = $cm
            ? \context_module::instance($cm->id)
            : \context_course::instance($course->id);

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
     * Provides a textual description based on provided data.
     *
     * @param array $data The input data array, expected to contain a 'pluginname' key.
     * @param mixed $not A parameter indicating negation condition (not used in this implementation).
     * @param bool $short Determines whether the description should be short or detailed (not used in this implementation).
     * @return string A localized description string containing the plugin name or an error message if 'pluginname' is missing.
     */
    public function get_description($data, $not, $short) {
        $pluginname = isset($data['pluginname']) ? trim((string)$data['pluginname']) : '';
        if ($pluginname === '') {
            return get_string('missingpluginname', 'availability_plugin');
        }
        return get_string('descriptionwithvalue', 'availability_plugin', s($pluginname));
    }

    /**
     * Retrieves an array of JavaScript string identifiers.
     *
     * @return array An array of string identifiers used for JavaScript localization or dynamic text loading.
     */
    public function get_javascript_strings() {
        return ['pluginnameinput', 'missingpluginname', 'descriptionwithvalue'];
    }

    /**
     * Populates or processes the provided data set and returns structured information.
     *
     * @param array $data A reference to the data array being processed.
     * @param mixed $not An unused parameter in the current implementation.
     * @param mixed $jumpto A reference to a variable, its purpose is not utilized in the current logic.
     * @return array An associative array containing a sanitized 'pluginname' key with its corresponding value.
     *               Defaults to an empty string if conditions are not met.
     */
    public function fill_set_data(&$data, $not, &$jumpto) {
        if (!empty($data) && is_array($data) && array_key_exists('pluginname', $data)) {
            return ['pluginname' => trim((string)$data['pluginname'])];
        }
        return ['pluginname' => ''];
    }

    /**
     * Validates the configuration data for a given plugin.
     *
     * @param array $data The configuration data to validate.
     * @param mixed $not A placeholder parameter, not used directly in validation.
     * @param \stdClass|null $info Additional information or context, null if not provided.
     * @return array An array of error messages, empty if no validation errors are found.
     */
    public function validate_config($data, $not, ?\stdClass $info = null) {
        $errors = [];
        $pluginname = trim((string)($data['pluginname'] ?? ''));
        if ($pluginname === '') {
            $errors[] = get_string('missingpluginname', 'availability_plugin');
        }
        return $errors;
    }
}
