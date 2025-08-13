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
 * Availability plugin - Tests for plugin restrictions
 *
 * @package    availability_plugin
 * @copyright  2018 Ulm University <kathrin.osswald@uni-ulm.de>
 * @copyright  2025 Mahmoud Chehada, ssystems GmbH <mchehada@ssystems.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace availability_plugin;

/**
 * Unit tests for the condition.
 *
 * @package    availability_plugin
 * @copyright  2018 Ulm University <kathrin.osswald@uni-ulm.de>
 * @copyright  2025 Mahmoud Chehada, ssystems GmbH <mchehada@ssystems.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class condition_test extends \advanced_testcase {
    /**
     * Test saving and restoring condition data.
     *
     * @covers \availability_plugin\condition::__construct
     * @covers \availability_plugin\condition::save
     */
    public function test_save_and_constructor(): void {
        // Startup.
        $this->resetAfterTest();

        // Create and save condition.
        $data = (object)['pluginname' => 'mod_assign'];
        $cond = new \availability_plugin\condition($data);
        $saved = $cond->save();

        // Check assertions.
        $this->assertEquals('plugin', $saved->type);
        $this->assertEquals('mod_assign', $saved->pluginname);
    }

    /**
     * Test is_available() with negation logic.
     *
     * @covers \availability_plugin\condition::is_available
     */
    public function test_is_available_with_negation(): void {
        // Startup.
        $this->resetAfterTest();
        $info = $this->createMock(\core_availability\info::class);

        // Create condition.
        $data = (object)['pluginname' => 'mod_quiz'];
        $cond = new \availability_plugin\condition($data);

        // Check assertions.
        $this->assertTrue($cond->is_available(false, $info, false, 2));
        $this->assertFalse($cond->is_available(true, $info, false, 2));
    }

    /**
     * Test student visibility depending on whether plugin exists.
     *
     * @covers \availability_plugin\condition::is_available
     */
    public function test_student_visibility_depends_on_plugin(): void {
        // Startup.
        $this->resetAfterTest();
        $info = $this->createMock(\core_availability\info::class);

        // Case 1: Plugin exists (mod_assign).
        $data = (object)['pluginname' => 'mod_assign'];
        $cond = new \availability_plugin\condition($data);
        $this->assertTrue($cond->is_available(false, $info, false, 5));

        // Case 2: Plugin does not exist (faked).
        $data = (object)['pluginname' => 'mod_doesnotexist'];
        $cond = new \availability_plugin\condition($data);
        $this->assertFalse($cond->is_available(false, $info, false, 5));
    }
}
