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
define([
        'jquery',
        'core/yui',
        'core/str',
        'core/log',
        'core/ajax',
        'editor_marklar/filepicker',
], function($, Y, str, log, ajax, filepicker) {

    "use strict";

    /**
     * @constructor
     * @param {Element} textarea
     * @param {Object} initparams
     */
    function MarklarEditor(textarea, initparams) {

        this.initparams = initparams;

        if (typeof M.editor_marklar.fpoptions[initparams.elementid] !== "undefined") {
            this.initparams.filepickeroptions = M.editor_marklar.fpoptions[initparams.elementid];
        }

        this.initTextArea(textarea);
        this.initFormatSelector();
        this.initPanel();
        this.initFilesEmbedding();
        this.initPreview();
    }

    /**
     * Initialize the text area element.
     *
     * @param {Element} textarea
     */
    MarklarEditor.prototype.initTextArea = function(textarea) {
        var self = this;

        self.textarea = textarea
            .removeAttr('cols')
            .addClass('marklar-textarea')
            .css('box-sizing', 'border-box')
            .css('width', '100%')
            .css('background-color', 'white')
            .css('margin-bottom', '10px')
            .css('padding', '7px');
    };

    /**
     * Initialize the text format selector.
     */
    MarklarEditor.prototype.initFormatSelector = function() {
        var self = this;

        // Expected name of the field with the text format.
        var fname = self.textarea.attr('name').replace('[text]', '[format]');
        var form = self.textarea.closest('form');

        if (fname === self.textarea.attr('name')) {
            // This may happen in places like mod_data or admin_setting_confightmleditor
            // that keep their own naming rules for the format.
            return;
        }

        // Locate the text format selector for our textarea.
        self.formatSelector = form.find('select[name="' + fname + '"]');

        if (self.formatSelector.length) {
            // Great, there is the drop down format selector found. We're done here.
            return;

        } else {
            // There is no dropdown menu with the format. Try to find the hidden field holding the format value.
            var formatHidden = form.find('input[name="' + fname + '"]');
            var formatId;
            var formatName;

            if (formatHidden.length) {
                formatId = parseInt(formatHidden.attr('value'));
            } else {
                // No text format specified.
                log.error('marklar: format field not found: ' + fname);
                return;
            }

            switch(formatId) {
                case 0:
                    formatName = 'formattext';
                    break;
                case 1:
                    formatName = 'formathtml';
                    break;
                case 2:
                    formatName = 'formatplain';
                    break;
                case 4:
                    formatName = 'formatmarkdown';
                    break;
                default:
                    log.error('marklar: unknown text format ' + formatId);
                    self.formatSelector = null;
                    return;
            }

            // Convert the hidden field into a single item selector.
            self.formatSelector = $('<select class="custom-select" name="' + fname + '"></select>')
                .append($('<option value="' + formatId + '">' + formatName + '</option>'));
            formatHidden.remove();

            // Localize the format name.
            str.get_string(formatName, 'core_moodle').done(function(formatTitle) {
                self.formatSelector.find('option[value="' + formatId + '"]').text(formatTitle);
            }).fail(function(ex) {
                log.error(ex);
                return;
            });
        }
    };

    /**
     * Initialize the bottom panel.
     */
    MarklarEditor.prototype.initPanel = function() {
        var self = this;

        // Wrap the textarea.
        self.textarea.wrap('<div class="marklar-wrapper"></div>');

        // Insert the panel region right after the textarea.
        self.panel = $('<div class="marklar-panel"></div>').insertAfter(self.textarea);

        // Move the format selector to the panel.
        if (self.formatSelector) {
            self.panel.append(self.formatSelector);
            self.formatSelector.attr('data-marklar-widget', 'format-select');
        }

        // Create buttons placeholders in the panel so that the order or async initialization does not affect display order.
        self.panel.append('<span data-marklar-placeholder="preview" />');
        self.panel.append('<span data-marklar-placeholder="insert-image" />');
        self.panel.append('<span data-marklar-placeholder="insert-file" />');
    };

    /**
     * Initialize support for embedding images via file picker.
     *
     * @method initFilesEmbedding
     */
    MarklarEditor.prototype.initFilesEmbedding = function() {

        if (!("filepickeroptions" in this.initparams)) {
            log.error(this.initparams.elementid + ": File picker options not found");
            return;
        }

        var self = this;

        Y.use('core_filepicker', function () {
            self.filepicker = filepicker.init(self.initparams.filepickeroptions);
            if (self.filepicker.canShowFilepicker("image")) {
                str.get_string("insertimage", "editor_marklar").done(function(strinsertimage) {
                    var button = $('<button class="btn btn-default" data-marklar-widget="insert-image" />');
                    button.text(strinsertimage);
                    button.click(function(e) {
                        e.preventDefault();
                        self.filepicker.showFilepicker("image", function (data) {
                            self.imageEmbedded(data);
                        });
                    });
                    self.panel.find('[data-marklar-placeholder="insert-image"]').replaceWith(button);
                    self.insertImageButton = button;
                });
            }
            if (self.filepicker.canShowFilepicker("link")) {
                str.get_string("insertlink", "editor_marklar").done(function(strinsertlink) {
                    var button = $('<button class="btn btn-default" data-marklar-widget="insert-file" />');
                    button.text(strinsertlink);
                    button.click(function(e) {
                        e.preventDefault();
                        self.filepicker.showFilepicker("link", function (data) {
                            self.insertLink(data);
                        });
                    });
                    self.panel.find('[data-marklar-placeholder="insert-file"]').replaceWith(button);
                    self.insertFileButton = button;
                });
            }
        });
    };

    /**
     * Initialize the context preview support.
     */
    MarklarEditor.prototype.initPreview = function() {
        var self = this;

        // Check there is the format selector available.
        if (!self.formatSelector) {
            return;
        }

        self.previewBody = $('<div class="marklar-preview" />')
            .hide();
        self.panel.before(self.previewBody);
        str.get_strings([
                {key: 'previewon', component: 'editor_marklar'},
                {key: 'previewoff', component: 'editor_marklar'}
        ]).then(function(strings) {
            self.previewButtonOn = $('<button class="btn btn-default" data-marklar-widget="preview" />')
                .text(strings[0])
                .on('click', self.previewOn.bind(self));
            self.previewButtonOff = $('<button class="btn btn-default">')
                .text(strings[1])
                .on('click', self.previewOff.bind(self))
                .hide();
            var buttonPreview = $('<div class="marklar-preview-controls" />')
                .append(self.previewButtonOn)
                .append(self.previewButtonOff);
            self.panel.find('[data-marklar-placeholder="preview"]').replaceWith(buttonPreview);
        });
    };

    /**
     * Toggle preview mode on.
     *
     * @param {Event} e
     */
    MarklarEditor.prototype.previewOn = function(e) {
        var self = this;
        e.preventDefault();

        str.get_string('previewloading', 'editor_marklar').then(function(strpreviewloading) {
            self.previewButtonOn.hide();
            self.previewButtonOff.show();

            if (self.formatSelector) {
                self.formatSelector.attr('disabled', 'disabled');
            }

            if (self.insertImageButton) {
                self.insertImageButton.attr('disabled', 'disabled');
            }

            if (self.insertFileButton) {
                self.insertFileButton.attr('disabled', 'disabled');
            }

            self.previewBody.html('<div class="marklar-preview-loading">' + strpreviewloading + '</div>');
            self.previewBody.height(self.textarea.height());

            self.textarea.hide();
            self.previewBody.show();
            self.previewLoad();
        });
    };

    /**
     * Toggle preview mode off.
     *
     * @param {Event} e
     */
    MarklarEditor.prototype.previewOff = function(e) {
        var self = this;
        e.preventDefault();

        self.previewButtonOff.hide();
        self.previewButtonOn.show();

        if (self.formatSelector) {
            self.formatSelector.removeAttr('disabled');
        }

        if (self.insertImageButton) {
            self.insertImageButton.removeAttr('disabled');
        }

        if (self.insertFileButton) {
            self.insertFileButton.removeAttr('disabled');
        }

        self.previewBody.hide();
        self.previewBody.html('');
        self.textarea.show();
    };

    /**
     * Load and display the text preview.
     *
     * @return {Deferred}
     */
    MarklarEditor.prototype.previewLoad = function() {
        var self = this;
        var args = {
            text: self.textarea.val(),
            format: self.formatSelector.val(),
            contextid: self.initparams.contextid
        };

        return ajax.call([{
            methodname: 'editor_marklar_get_preview',
            args: args
        }])[0].fail(function(err) {
            self.previewBody.html('<div class="alert alert-error"><b>Error:</b> ' + err.message + '</div>');
            log.error(err);

        }).then(function(response) {
            self.previewBody.html(response.html);
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
