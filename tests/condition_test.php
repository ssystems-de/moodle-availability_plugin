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
 * @copyright  2025 Mahmoud Chehada, ssystems GmbH <mchehada@ssystems.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace availability_plugin;

/**
 * Unit tests for the condition.
 *
 * @package    availability_plugin
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
        $data = \availability_plugin\condition::get_json('mod_assign');
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
        $data = \availability_plugin\condition::get_json('mod_quiz');
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
        $data = \availability_plugin\condition::get_json('mod_assign');
        $cond = new \availability_plugin\condition($data);
        $this->assertTrue($cond->is_available(false, $info, false, 5));

        // Case 2: Plugin does not exist (faked).
        $data = \availability_plugin\condition::get_json('mod_doesnotexist');
        $cond = new \availability_plugin\condition($data);
        $this->assertFalse($cond->is_available(false, $info, false, 5));
    }

    /**
     * Data provider for test_is_available_for_all().
     *
     * @return array
     */
    public static function is_available_for_all_provider(): array {
        return [
            'Plugin exists' => ['mod_assign', false, true],
            'Plugin exists, negated' => ['mod_assign', true, false],
            'Plugin does not exist' => ['mod_doesnotexist', false, false],
            'Plugin does not exist, negated' => ['mod_doesnotexist', true, true],
            'Empty plugin name' => ['', false, false],
            'Empty plugin name, negated' => ['', true, true],
        ];
    }

    /**
     * Test that is_available_for_all() returns the real availability result
     * and is consistent with is_available().
     *
     * @dataProvider is_available_for_all_provider
     * @covers \availability_plugin\condition::is_available_for_all
     * @covers \availability_plugin\condition::is_available
     * @param string $pluginname The plugin name of the condition
     * @param bool $not Whether the condition is negated
     * @param bool $expected The expected availability
     */
    public function test_is_available_for_all(string $pluginname, bool $not, bool $expected): void {
        // Startup.
        $this->resetAfterTest();
        $info = $this->createMock(\core_availability\info::class);

        // Create condition.
        $data = \availability_plugin\condition::get_json($pluginname);
        $cond = new \availability_plugin\condition($data);

        // Check assertions.
        $this->assertSame($expected, $cond->is_available_for_all($not));
        $this->assertSame($expected, $cond->is_available($not, $info, false, 5));
    }

    /**
     * Test that the plugin name is escaped in the description.
     *
     * @covers \availability_plugin\condition::get_description
     */
    public function test_get_description_escapes_pluginname(): void {
        // Startup.
        $this->resetAfterTest();
        $info = $this->createMock(\core_availability\info::class);

        // Create condition with HTML in the plugin name.
        $data = \availability_plugin\condition::get_json('<img src=x onerror=alert(1)>');
        $cond = new \availability_plugin\condition($data);

        // Check assertions for both the normal and the negated description.
        foreach ([false, true] as $not) {
            $description = $cond->get_description(true, $not, $info);
            $this->assertStringNotContainsString('<img', $description);
            $this->assertStringContainsString('&lt;img src=x onerror=alert(1)&gt;', $description);
        }
    }
}
