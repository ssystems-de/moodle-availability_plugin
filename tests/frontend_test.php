<?php
// This file is part of Moodle - http://moodle.org/.
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
 * Availability plugin - Tests for the frontend
 *
 * @package    availability_plugin
 * @copyright  2026 Alexander Bias, ssystems GmbH <abias@ssystems.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace availability_plugin;

/**
 * Unit tests for the frontend.
 *
 * @package    availability_plugin
 * @copyright  2026 Alexander Bias, ssystems GmbH <abias@ssystems.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class frontend_test extends \advanced_testcase {
    /**
     * Test that the list of installed plugins is only shipped to users who are allowed to add the condition.
     *
     * @covers \availability_plugin\frontend::get_javascript_init_params
     */
    public function test_get_javascript_init_params_requires_capability(): void {
        global $DB;

        // Startup.
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $page = $generator->create_module('page', ['course' => $course->id]);
        $cm = get_fast_modinfo($course)->get_cm($page->cmid);
        $teacher = $generator->create_and_enrol($course, 'editingteacher');
        $frontend = new \availability_plugin\frontend();

        // Check that the admin gets the list of installed plugins.
        $this->setAdminUser();
        $params = $frontend->get_javascript_init_params($course, $cm);
        $this->assertContains('availability_plugin', $params[0]['components']);
        $this->assertContains('mod_page', $params[0]['components']);

        // Check that a teacher without the capability does not get the list.
        $this->setUser($teacher);
        $params = $frontend->get_javascript_init_params($course, $cm);
        $this->assertSame([], $params[0]['components']);

        // Check that the teacher gets the list as soon as the capability is granted.
        $roleid = $DB->get_field('role', 'id', ['shortname' => 'editingteacher']);
        assign_capability('availability/plugin:addinstance', CAP_ALLOW, $roleid, \context_course::instance($course->id));
        $params = $frontend->get_javascript_init_params($course, $cm);
        $this->assertContains('mod_page', $params[0]['components']);
    }
}
