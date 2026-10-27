moodle-availability_plugin
==========================

[![Moodle Plugin CI](https://github.com/ssystems-de/moodle-availability_plugin/actions/workflows/moodle-plugin-ci.yml/badge.svg?branch=MOODLE_501_STABLE)](https://github.com/ssystems-de/moodle-availability_plugin/actions?query=workflow%3A%22Moodle+Plugin+CI%22+branch%3AMOODLE_501_STABLE)

Moodle availability plugin which lets users restrict resources and activities based on the existence of particular plugins


Requirements
------------

This plugin requires Moodle 5.1+


Motivation for this plugin
--------------------------

There may come the day in a Moodle admin's life when you need to restrict particular course assets based on the existence of particular Moodle plugins. A good example for this need might be if you are running multiple Moodle instances with different plugins sets and have a common Moodle onboarding course which contains material about each of these plugins. In this case, it might come handy if you can maintain the course on a central Moodle instance, copy it over to all other Moodle instances as needed and can rely on the fact that endusers on the other Moodle instances just see the content for which the mentioned plugins are installed. If you have a situation like this, this plugin is for you.


Installation
------------

Install the plugin like any other plugin to folder
/availability/condition/plugin

See http://docs.moodle.org/en/Installing_plugins for details on installing Moodle plugins


Usage & Settings
----------------

After installing the plugin, it is ready to use without the need for any configuration.

Admins (and other roles to whom you grant the availability/plugin:addinstance capability) can add the "Plugin" availability condition to activities / resources in their courses. While adding the condition, they have to set the technical name of the plugin which has to be installed for the activity / resource to be visible for students in the course.

If you want to learn more about using availability plugins in Moodle, please see https://docs.moodle.org/en/Restrict_access.


Restricting usage
-----------------

The feature which availability_plugin provides is necessary / useful in specialized scenarios / environments. However, you might not want every teacher in your Moodle instance to be able to add such conditions to their activities / resources.

Because of that and in contrast to other availability plugins, availability_plugin supports a capability availability/plugin:addinstance which lets you control who is able to add this condition to activities / resources and who is not. By default, the capability is not granted to any role at plugin installation time and thus just available to admins. Feel free to change this setup within your Moodle role configuration as needed.

Please be aware that this capability only controls the condition editor user interface, it is not an access-control boundary:

* The capability decides whether the "Plugin" condition is offered in the "Add restriction" dialogue and whether the list of installed plugins is sent to the browser for autocompletion.
* Moodle core does not check the capability again when the activity / resource or section settings form is saved. A user who is able to edit an activity / resource or section can therefore still add the condition by manipulating the submitted form data, even without this capability.
* If teacher A who has this capability adds the condition to an activity / resource and teacher B who has not the capability edits this activity / resource, B is able to see, edit and delete the condition of this particular activity / resource.

The impact of this is limited: The condition can only be added to content which the user is able to edit anyway and it only reveals whether a plugin with a given name is installed on the server. However, please do not rely on this capability if you need to strictly prevent users from adding the condition.


Capabilities
------------

This plugin also introduces these additional capabilities:

### availability/plugin:addinstance

This capability controls who is offered the plugin condition in the condition editor user interface of activities and sections.
It is not assigned to any role by default.
It only controls the user interface and is not enforced when the form is saved, see the "Restricting usage" section above.


Scheduled Tasks
---------------

This plugin does not add any additional scheduled tasks.


How this plugin works / Pitfalls
--------------------------------

This availability condition simply checks if the given plugin is installed or not. A plugin is considered installed if its code is present on disk and its installation has been completed. Plugins which are missing from disk or which are waiting for their installation are not considered installed. Whether the plugin is enabled or disabled does not matter. Moodle core and its subsystems (e.g. core_course) are not plugins and are never considered installed.

To do this check, it requires admins to enter the technical name (frankenstyle name) of the plugin in the condition form. The condition form supports autocompletion of plugin names, but just for the plugins which are currently installed. You are able to set arbitrary other plugin names within the condition form as well, but please double-check if the plugin name is set correctly in this case as otherwise the condition would never match.


Theme support
-------------

This plugin is developed and tested on Moodle Core's Boost theme.
It should also work with Boost child themes, including Moodle Core's Classic theme. However, we can't support any other theme than Boost.


Plugin repositories
-------------------

This plugin is published and regularly updated in the Moodle plugins repository:
http://moodle.org/plugins/view/availability_plugin

The latest development version can be found on Github:
https://github.com/ssystems-de/moodle-availability_plugin


Bug and problem reports
-----------------------

This plugin is carefully developed and thoroughly tested, but bugs and problems can always appear.

Please report bugs and problems on Github:
https://github.com/ssystems-de/moodle-availability_plugin/issues


Community feature proposals
---------------------------

The functionality of this plugin is primarily implemented for the needs of our clients and published as-is to the community. We are aware that members of the community will have other needs and would love to see them solved by this plugin.

Please issue feature proposals on Github:
https://github.com/ssystems-de/moodle-availability_plugin/issues

Please create pull requests on Github:
https://github.com/ssystems-de/moodle-availability_plugin/pulls


Paid support
------------

We are always interested to read about your issues and feature proposals or even get a pull request from you on Github. However, please note that our time for working on community Github issues is limited.

As solution provider, we also offer paid support for this plugin. If you are interested, please have a look at our services on [ssystems.de](https://www.ssystems.de/) or get in touch with us directly via vertrieb@ssystems.de.


Moodle release support
----------------------

This plugin is only maintained for the most recent major release of Moodle as well as the most recent LTS release of Moodle. Bugfixes are backported to the LTS release. However, new features and improvements are not necessarily backported to the LTS release.

Apart from these maintained releases, previous versions of this plugin which work in legacy major releases of Moodle are still available as-is without any further updates in the Moodle Plugins repository.

There may be several weeks after a new major release of Moodle has been published until we can do a compatibility check and fix problems if necessary. If you encounter problems with a new major release of Moodle - or can confirm that this plugin still works with a new major release - please let us know on Github.

If you are running a legacy version of Moodle, but want or need to run the latest version of this plugin, you can get the latest version of the plugin, remove the line starting with $plugin->requires from version.php and use this latest plugin version then on your legacy Moodle. However, please note that you will run this setup completely at your own risk. We can't support this approach in any way and there is an undeniable risk for erratic behavior.


Translating this plugin
-----------------------

This Moodle plugin is shipped with an english language pack only. All translations into other languages must be managed through AMOS (https://lang.moodle.org) by what they will become part of Moodle's official language pack.

As the plugin creator, we manage the translation into german for our own local needs on AMOS. Please contribute your translation into all other languages in AMOS where they will be reviewed by the official language pack maintainers for Moodle.


Right-to-left support
---------------------

This plugin has not been tested with Moodle's support for right-to-left (RTL) languages.
If you want to use this plugin with a RTL language and it doesn't work as-is, you are free to send us a pull request on Github with modifications.


Maintainers
-----------

The plugin is maintained by\
ssystems GmbH


Copyright
---------

The copyright of this plugin is held by\
ssystems GmbH

Individual copyrights of individual developers are tracked in PHPDoc comments and Git commits.
