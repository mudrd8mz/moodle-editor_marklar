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
 * @module      editor_marklar/editor
 * @package     editor_marklar
 * @copyright   2016 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/yui', 'core/str', 'editor_marklar/filepicker'], function($, Y, str, filepicker) {

    "use strict";

    /**
     * @constructor
     * @param {Element} textarea
     * @param {Object} initparams
     */
    function MarklarEditor(textarea, initparams) {

        this.textarea = textarea;
        this.panel = textarea.parent().next();
        this.initparams = initparams;

        if (typeof M.editor_marklar.fpoptions[initparams.elementid] !== "undefined") {
            this.initparams.filepickeroptions = M.editor_marklar.fpoptions[initparams.elementid];
        }

        this.initFilesEmbedding();
    }

    /**
     * Initialize support for embedding images via file picker.
     *
     * @method initFilesEmbedding
     */
    MarklarEditor.prototype.initFilesEmbedding = function() {

        if (!("filepickeroptions" in this.initparams)) {
            // jshint devel:true
            console.error(this.initparams.elementid + ": File picker options not found");
            return;
        }

        var self = this;

        Y.use('core_filepicker', function () {
            self.filepicker = filepicker.init(self.initparams.filepickeroptions);
            if (self.filepicker.canShowFilepicker("image")) {
                str.get_string("insertimage", "editor_marklar").done(function(strinsertimage) {
                    var button = $("<button/>");
                    button.text(strinsertimage);
                    button.click(function(e) {
                        e.preventDefault();
                        self.filepicker.showFilepicker("image", function (data) {
                            self.imageEmbedded(data);
                        });
                    });
                    self.panel.append(button);
                });
            }
            if (self.filepicker.canShowFilepicker("link")) {
                str.get_string("insertlink", "editor_marklar").done(function(strinsertlink) {
                    var button = $("<button/>");
                    button.text(strinsertlink);
                    button.click(function(e) {
                        e.preventDefault();
                        self.filepicker.showFilepicker("link", function (data) {
                            self.insertLink(data);
                        });
                    });
                    self.panel.append(button);
                });
            }
        });
    };

    /**
     * This is called once the use picks an image via filepicker.
     *
     * @param {Object} data
     */
    MarklarEditor.prototype.imageEmbedded = function(data) {
        if ("url" in data) {
            this.insertText("<img alt=\"\" class=\"img-responsive\" src=\"" + data.url + "\"/>");
        }
    };

    /**
     * This is called once the user picks a file via filepicker.
     *
     * @param {Object} data
     */
    MarklarEditor.prototype.insertLink = function(data) {
        if ("url" in data) {
            var texttoshow;
            if ("file" in data && data.file) {
                texttoshow = data.file.replace(/(\[|\])/g, "_");
            } else {
                texttoshow = "texttoshow";
            }
            this.insertText("[" + texttoshow + "](" + data.url + ")");
        }
    };

    /**
     * Inserts the given text into the editor.
     *
     * @todo respect the current caret position
     * @param {String} text
     */
    MarklarEditor.prototype.insertText = function(text) {
        this.textarea.val(this.textarea.val() + "\n\n" + text);
    };

    return /** @alias module:editor_marklar */ {
        init: function (params) {
            var textarea;

            if ("elementid" in params) {
                textarea = $(document.getElementById(params.elementid));
            } else {
                throw new Error("editor_marklar: Invalid editor init parameter - missing elementid");
            }

            if (textarea.length) {
                return new MarklarEditor(textarea, params);
            } else {
                throw new Error("Unable to find textarea element", params.elementid);
            }
        }
    };
});
