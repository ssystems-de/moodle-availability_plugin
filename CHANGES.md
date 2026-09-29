moodle-availability_plugin
==========================

Changes
-------

### Unreleased

* 2026-09-29 - Bugfix: Escape the plugin name in the condition description to prevent XSS
* 2026-09-29 - Align condition::get_json() with the core convention and use it in the unit tests
* 2026-09-29 - Cleanup some dead code findings
* 2026-09-29 - Cleanup some documentation glitches in README

### v5.2-r1

* 2026-04-20 - Prepare compatibility for Moodle 5.2.

### v5.1-r1

* 2026-09-24 - Prepare compatibility for Moodle 5.1.

### v5.0-r1

* 2026-09-24 - Prepare compatibility for Moodle 5.0.

### v4.5-r3

* 2026-09-14 - Update CI integration.
               Please note: Due to this change, the Git history of the plugin has to be rewritten in Github. Existing Git tags will not be changed. If you deploy directly from Github, you should be aware of that one-time hickup.

### v4.5-r2

* 2025-10-20 - Release: Remove german language pack as translations are now managed on AMOS.
* 2025-10-20 - Bugfix: Fix incorrect condition label for to 'no' case, resolves #1

### v4.5-r1

* 2025-10-08 - Initial release
