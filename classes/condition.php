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
 * Availability plugin - Condition
 *
 * @package    availability_plugin
 * @copyright  2025 Mahmoud Chehada, ssystems GmbH <mchehada@ssystems.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace availability_plugin;

/**
 * Availability plugin - Condition class
 *
 * @package    availability_plugin
 * @copyright  2025 Mahmoud Chehada, ssystems GmbH
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class condition extends \core_availability\condition {
    /** @var string The pluginname. */
    protected $pluginname = '';

    /**
     * Constructor method.
     *
     * @param object $structure The structure containing the plugin name property.
     * @return void
     */
    public function __construct($structure) {
        $this->pluginname = isset($structure->pluginname) && $structure->pluginname !== ''
            ? strtolower(trim((string)$structure->pluginname))
            : '';
    }

    /**
     * Saves the current plugin state and returns the corresponding data.
     *
     * @return object An object containing the type and plugin name.
     */
    public function save() {
        return (object)[
            'type' => 'plugin',
            'pluginname' => $this->pluginname,
        ];
    }

    /**
     * Determines whether a particular item is currently available
     * according to this availability condition.
     *
     * If implementations require a course or modinfo, they should use
     * the get methods in $info.
     *
     * The $not option is potentially confusing. This option always indicates
     * the 'real' value of NOT. For example, a condition inside a 'NOT AND'
     * group will get this called with $not = true, but if you put another
     * 'NOT OR' group inside the first group, then a condition inside that will
     * be called with $not = false. We need to use the real values, rather than
     * the more natural use of the current value at this point inside the tree,
     * so that the information displayed to users makes sense.
     *
     * @param bool $not Set true if we are inverting the condition
     * @param info $info Item we're checking
     * @param bool $grabthelot Performance hint: if true, caches information
     *   required for all course-modules, to make the front page and similar
     *   pages work more quickly (works only for current user)
     * @param int $userid User ID to check availability for
     * @return bool True if available
     */
    public function is_available($not, \core_availability\info $info, $grabthelot, $userid) {
        if ($this->pluginname === '') {
            return $not ? true : false;
        }
        $exists = (bool)\core_component::get_component_directory($this->pluginname);
        $result = $exists;
        return $not ? !$result : $result;
    }

    /**
     * Obtains a string describing this restriction (whether or not
     * it actually applies). Used to obtain information that is displayed to
     * students if the activity is not available to them, and for staff to see
     * what conditions are.
     *
     * The $full parameter can be used to distinguish between 'staff' cases
     * (when displaying all information about the activity) and 'student' cases
     * (when displaying only conditions they don't meet).
     *
     * If implementations require a course or modinfo, they should use
     * the get methods in $info.
     *
     * The special string <AVAILABILITY_CMNAME_123/> can be returned, where
     * 123 is any number. It will be replaced with the correctly-formatted
     * name for that activity.
     *
     * @param bool $full Set true if this is the 'full information' view
     * @param bool $not Set true if we are inverting the condition
     * @param info $info Item we're checking
     * @return string Information string (for admin) about all restrictions on
     *   this item
     */
    public function get_description($full, $not, \core_availability\info $info) {
        // If no plugin name was provided (empty or null), show a fallback string.
        // This prevents broken or empty labels in the restrict-access UI.
        if ($this->pluginname === '') {
            return get_string('missingpluginname', 'availability_plugin');
        }

        // Return a different description depending on whether the condition is negated.
        // Moodle passes $not = true when the manager selected "must NOT".
        // We use two separate language strings so the label changes accordingly.
        return $not
            ? get_string('descriptionwithvalue_not', 'availability_plugin', $this->pluginname)
            : get_string('descriptionwithvalue', 'availability_plugin', $this->pluginname);
    }

    /**
     * Generates a debug string containing plugin information.
     *
     * @return string The debug string with the plugin name.
     */
    protected function get_debug_string() {
        return 'plugin=' . $this->pluginname;
    }

    /**
     * Generates a JSON object with plugin information.
     *
     * @param object $data The input data containing the plugin name property.
     * @param object $context The context in which the plugin data is being used.
     * @return object An object representing the plugin data in JSON format.
     */
    public static function get_json($data, $context) {
        return (object)[
            'type' => 'plugin',
            'pluginname' => isset($data->pluginname) ? (string)$data->pluginname : '',
        ];
    }

    /**
     * Checks if the functionality is available for all.
     *
     * @param bool $not Determines if the condition should be inverted. Default is false.
     * @return bool Returns true.
     */
    public function is_available_for_all($not = false) {
        // Return true as the availability condition is not dependent on user-specific data.
        return true;
    }
}
