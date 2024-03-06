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
 * Tiny AMD wrapper for the Moodle filepicker, based on Atto YUI module moodle-editor_atto-editor.
 *
 * @module      editor_marklar/filepicker
 * @copyright   2016 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['core/yui'], function(Y) {

    "use strict";

    /**
     * @constructor
     * @param {array} options List of filepicker options
     */
    function EditorFilepicker(options) {
        this.options = options;
    }

    /**
     * Should we show the filepicker for the given type of content?
     *
     * @method canShowFilepicker
     * @param {string} type The content type for the file picker: image, link, media
     * @return {boolean}
     */
    EditorFilepicker.prototype.canShowFilepicker = function(type) {
        return (this.options && (typeof this.options[type] !== 'undefined'));
    };

    /**
     * Show the filepicker.
     *
     * This depends on core_filepicker, and then calls that module's show function.
     *
     * @method showFilepicker
     * @param {string} type The media type for the file picker.
     * @param {function} callback The callback to use when selecting an item of media.
     * @param {object} [context] The context from which to call the callback.
     */
    EditorFilepicker.prototype.showFilepicker = function(type, callback, context) {
        var self = this;
        Y.use('core_filepicker', function(Y) {
            var options = Y.clone(self.options[type], true);
            options.formcallback = callback;
            if (context) {
                options.magicscope = context;
            }

            M.core_filepicker.show(Y, options);
        });
    };

    return /** @alias module:editor_marklar/filepicker */ {
        init: function(options) {
            return new EditorFilepicker(options);
        }
    };
});
