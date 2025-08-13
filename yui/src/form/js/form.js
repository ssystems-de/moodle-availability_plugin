/**
 * Availability plugin - YUI code for plugin form
 *
 * @package    availability_plugin
 * @copyright  2025 Mahmoud Chehada, ssystems GmbH <mchehada@ssystems.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * JavaScript for form editing profile conditions.
 *
 * @module moodle-availability_plugin-form
 */
M.availability_plugin = M.availability_plugin || {}; // eslint-disable-line camelcase

/*
 * @class M.availability_plugin.form
 * @extends M.core_availability.plugin
 */
M.availability_plugin.form = Y.Object(M.core_availability.plugin);

/**
 * Initialises this plugin.
 *
 * @method initInner
 */
M.availability_plugin.form.initInner = function(params) {
    var cfg = params || {};

    // List of all available Moodle plugins for autocomplete.
    M.availability_plugin._components = cfg.components || [];

    // User permission: true = can configure, false = view only.
    M.availability_plugin._canconfigure = (cfg.canconfigure !== undefined) ? cfg.canconfigure : true;
};

/**
 * Create the HTML element for the plugin condition user interface.
 *
 * @param {Object} json - Saved configuration data (contains pluginname).
 * @return {Y.Node} - The created DOM element for the user interface.
 */
M.availability_plugin.form.getNode = function(json) {
        // Extract the saved plugin name or use an empty string as default.
        var value = (json && json.pluginname) ? json.pluginname : '';

        // Get the localized label text or use a fallback.
        var labeltext = M.util.get_string('pluginnameinput', 'availability_plugin') || 'Plugin name';

        // Generate a unique ID for the datalist (for autocomplete).
        var datalistId = 'availability-plugin-list-' + Y.guid();

        // Build the HTML options for autocomplete.
        var opts = '';
        var seen = {};
        Y.Array.each(M.availability_plugin._components || [], function(cmp) {
            if (!seen[cmp]) {
                seen[cmp] = true;
                // Each plugin name is added as HTML option (with XSS protection).
                opts += '<option value="' + Y.Escape.html(cmp) + '"></option>';
            }
        });

        // Generate the HTML.
        var html = '<label><span class="pe-3">' + labeltext + '</span>' +
            '<input class="form-control" type="text" name="pluginname" list="' + datalistId + '" size="30" value="' +
            Y.Escape.html(value) + '" /></label>' +
            '<datalist id="' + datalistId + '">' + opts + '</datalist>';

        // Create DOM element.
        var node = Y.Node.create('<span>' + html + '</span>');

        // Add event listeners to the input field so changes are processed immediately.
        var input = node.one('input[name=pluginname]');
        if (input) {
            var refresh = function() {
                M.core_availability.form.update();
            };
            input.on('keyup', refresh);
            input.on('change', refresh);
            input.on('input', refresh);
        }

        return node;
    };


/**
 * Called whenever M.core_availability.form.update() is called - this is used to
 * save the value from the form into the hidden availability data.
 *
 * @param {Object} value - The target object where values are written to.
 * @param {Y.Node} node - The DOM element from which values are read.
 */
M.availability_plugin.form.fillValue = function(value, node) {
    // Set the type of availability condition.
    value.type = 'plugin';

    // Find the input field in the DOM.
    var input = node.one('input[name=pluginname]');

    // Always trim the value to remove leading/trailing spaces.
    value.pluginname = input ? Y.Lang.trim(input.get('value')) : '';
};

/**
 * Validates user input and collects error messages.
 *
 * This function is called by the Moodle framework to validate the current
 * input values. It checks whether the plugin name is filled in and adds
 * visual error indicators if necessary.
 *
 * @param {Array} errors - Array to which error messages are added.
 * @param {Y.Node} node - The DOM element to be validated.
 */
M.availability_plugin.form.fillErrors = function(errors, node) {
    // Find DOM element.
    var input = node.one('input[name=pluginname]');

    // Check if the input field exists and has a non-empty value.
    var empty = (!input || Y.Lang.trim(input.get('value')) === '');

    // If empty.
    if (empty) {
        // Add error message to global error list.
        errors.push('availability_plugin:missingpluginname');

        // Add visual error indicator to input field.
        if (input) {
            input.addClass('form-error');
        }

        // Otherwise.
    } else if (input) {
        // Remove any existing error indicators.
        input.removeClass('form-error');
    }
};