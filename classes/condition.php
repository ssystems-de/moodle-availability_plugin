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
 * Local plugin 'Guest redirect' - Version file.
 *
 * @package    availability_plugin
 * @copyright  2025 Mahmoud Chehada, ssystems GmbH <mchehada@ssystems.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Define the namespace according to Moodle's plugin structure
namespace availability_plugin;

// Prevent direct access to this file
defined('MOODLE_INTERNAL') || die();


// Define the main class of your plugin
class condition extends \core_availability\condition {

    // Constructor – runs when the condition is initialized
    public function __construct($structure) {
        // No configuration is passed or stored for this condition
    }

    // Save method – returns condition data (used when saving the availability structure)
    public function save() {
        // Since there’s no configuration, we just return the type
        return (object)['type' => 'plugin'];
    }

    // Main logic to determine if the section is available
    public function is_available($not, $info, $grabthelot, $userid) {
        global $DB;

        // Get the current course object from the availability info
        $course = $info->get_course();

        // Get the section number (e.g., 0 = general, 1 = first section, etc.)
        $sectionnum = $info->sectionnum;

        // Fetch the course section record from the database
        $section = $DB->get_record('course_sections', [
            'course' => $course->id,
            'section' => $sectionnum
        ]);

        // If section does not exist or is empty, return false (or true if inverted)
        if (!$section || empty($section->sequence)) {
            return $not ? true : false;
        }

        // Split the list of activity/module IDs in this section
        $cmids = explode(',', $section->sequence);

        // Loop through each module (cmid = course module ID)
        foreach ($cmids as $cmid) {
            // Get the course module (activity) object
            $cm = get_coursemodule_from_id(null, $cmid, 0, false, MUST_EXIST);

            // Build the plugin name (e.g. mod_quiz, mod_assign)
            $pluginname = 'mod_' . $cm->modname;

            // Check if this plugin is installed in the current Moodle system
            if (\core\plugininfo\plugininfo_base::is_plugin_folder($pluginname)) {
                // At least one plugin is installed → section is available (or not, if inverted)
                return $not ? false : true;
            }
        }

        // No installed plugin found in this section → not available
        return $not ? true : false;
    }

    // Text shown in the Moodle UI for the condition (in restriction summary)
    public function get_description($full) {
        return get_string('description', 'availability_plugin');
    }

    // Used when exporting the condition as JSON (e.g., for backup)
    public static function get_json($data, $context) {
        return (object)['type' => 'plugin'];
    }

    // Called when restoring a course – not needed for this plugin
    public static function update_after_restore($restoreid, $restoredata, $courseid, $contextid, $oldcontextid, $log) {
        // Nothing needed here
    }

    // Whether this condition applies to all users – false means it checks per user or section
    public function is_available_for_all() {
        return false;
    }
}
