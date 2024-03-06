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
 * @copyright   2016 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([
        'jquery',
        'core/yui',
        'core/str',
        'core/log',
        'core/ajax',
        'core/event',
        'editor_marklar/filepicker',
        'editor_marklar/imagepaster',
], function($, Y, str, log, ajax, event, filepicker, ImagePaster) {

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
        this.initSyntaxHelp();
        this.initImagePaster();
    }

    /**
     * Initialize the text area element.
     *
     * @param {Element} textarea
     */
    MarklarEditor.prototype.initTextArea = function(textarea) {
        var self = this;

        self.textarea = textarea
            .addClass('marklar-textarea')
            .css('box-sizing', 'border-box')
            .css('width', '100%')
            .css('background-color', 'white')
            .css('margin-bottom', '10px')
            .css('padding', '7px');

        if (self.initparams.monospace) {
            self.textarea.css('font-family', 'monospace');
        }
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

            switch (formatId) {
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

        // Create a subpanel within the panel with widgets that should be visible in editing mode only.
        self.editpanel = $('<div class="marklar-edit-panel"></div>').appendTo(self.panel);

        // Move the format selector to the panel.
        if (self.formatSelector) {
            self.editpanel.append(self.formatSelector);
            self.formatSelector.attr('data-marklar-widget', 'format-select');
        }

        // Create buttons placeholders in the panel so that the order or async initialization does not affect display order.
        self.panel.prepend('<span data-marklar-placeholder="preview" />');
        self.editpanel.append('<span data-marklar-placeholder="syntax" />');
        self.editpanel.append('<span data-marklar-placeholder="insert-image" />');
        self.editpanel.append('<span data-marklar-placeholder="insert-file" />');
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

        Y.use('core_filepicker', function() {
            self.filepicker = filepicker.init(self.initparams.filepickeroptions);
            if (self.filepicker.canShowFilepicker("image")) {
                str.get_string("insertimage", "editor_marklar").done(function(strinsertimage) {
                    var button = $('<button class="btn btn-default" data-marklar-widget="insert-image" />');
                    button.text(strinsertimage);
                    button.click(function(e) {
                        e.preventDefault();
                        self.filepicker.showFilepicker("image", function(data) {
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
                        self.filepicker.showFilepicker("link", function(data) {
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
     *
     * @return {boolean}
     */
    MarklarEditor.prototype.initPreview = function() {
        var self = this;

        // Check there is the format selector available.
        if (!self.formatSelector) {
            return false;
        }

        self.previewBody = $('<div class="marklar-preview" />')
            .hide();
        self.panel.before(self.previewBody);
        return str.get_strings([
                {key: 'previewon', component: 'editor_marklar'},
                {key: 'previewoff', component: 'editor_marklar'}
        ]).then(function(strings) {
            self.previewButtonOn = $('<button class="btn btn-default" data-marklar-widget="preview-on" />')
                .text(strings[0])
                .on('click', self.previewOn.bind(self));
            self.previewButtonOff = $('<button class="btn btn-default" data-marklar-widget="preview-off" >')
                .text(strings[1])
                .on('click', self.previewOff.bind(self))
                .hide();
            var buttonPreview = $('<div class="marklar-preview-controls" />')
                .append(self.previewButtonOn)
                .append(self.previewButtonOff);
            self.panel.find('[data-marklar-placeholder="preview"]').replaceWith(buttonPreview);
            return true;
        });
    };

    /**
     * Toggle preview mode on.
     *
     * @param {Event} e
     * @return {boolean}
     */
    MarklarEditor.prototype.previewOn = function(e) {
        var self = this;
        e.preventDefault();

        return str.get_string('previewloading', 'editor_marklar').then(function(strpreviewloading) {
            self.previewButtonOn.hide();
            self.previewButtonOff.show();
            self.editpanel.hide();

            self.previewBody.html('<div class="marklar-preview-loading">' + strpreviewloading + '</div>');
            self.previewBody.height(self.textarea.height());

            self.textarea.hide();
            self.previewBody.show();
            self.previewLoad();

            return true;
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
        self.editpanel.show();

        self.previewBody.hide();
        self.previewBody.html('');
        self.textarea.show();
    };

    /**
     * Load and display the text preview.
     *
     * @return {boolean}
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
            return false;

        }).then(function(response) {
            self.previewBody.html(response.html);
            event.notifyFilterContentUpdated(self.previewBody);
            return true;
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
     * @param {String} inserttext
     */
    MarklarEditor.prototype.insertText = function(inserttext) {
        var areatext = this.textarea.val();
        var selectionStart = this.textarea.prop('selectionStart');
        var selectionEnd = this.textarea.prop('selectionEnd');
        this.textarea.val(areatext.substring(0, selectionStart) + inserttext + areatext.substring(selectionEnd));
    };

    /**
     * Initialize the syntax help panel.
     *
     * @return {boolean}
     */
    MarklarEditor.prototype.initSyntaxHelp = function() {
        var self = this;

        // Check there is the format selector available.
        if (!self.formatSelector) {
            return false;
        }

        self.syntaxBody = $('<div class="marklar-syntax-help" />')
            .hide();
        self.editpanel.append(self.syntaxBody);
        str.get_strings([
                {key: 'syntaxon', component: 'editor_marklar'},
                {key: 'syntaxoff', component: 'editor_marklar'},
        ]).then(function(strings) {
            self.syntaxButtonOn = $('<button class="btn btn-link" data-marklar-widget="syntax-on" />')
                .text(strings[0])
                .on('click', self.syntaxOn.bind(self));
            self.syntaxButtonOff = $('<button class="btn btn-link" data-marklar-widget="syntax-off" />')
                .text(strings[1])
                .on('click', self.syntaxOff.bind(self))
                .hide();
            var buttonSyntax = $('<div class="marklar-syntax-controls" />')
                .append(self.syntaxButtonOn)
                .append(self.syntaxButtonOff);
            self.panel.find('[data-marklar-placeholder="syntax"]').replaceWith(buttonSyntax);
            return true;
        }).catch(function(err) {
            log.error(err);
            return false;
        });

        // If the syntax help is expanded and the format is changed, update the
        // syntax help to describe the new format.
        if (self.formatSelector) {
            self.formatSelector.on('change', function() {
                if (self.syntaxBody.is(':visible')) {
                    self.syntaxButtonOn.click();
                }
            });
        }

        return true;
    };

    /**
     * Initialize the image paster module.
     */
    MarklarEditor.prototype.initImagePaster = function() {
        var self = this;

        if (!self.initparams.filepickeroptions.image) {
            return;
        }

        ImagePaster.init(self.textarea, self.initparams.filepickeroptions.image, self.imageEmbedded.bind(this));
    };

    /**
     * Toggle syntax help on.
     *
     * @param {Event} e
     * @return {boolean}
     */
    MarklarEditor.prototype.syntaxOn = function(e) {
        var self = this;
        e.preventDefault();

        return str.get_string('syntaxloading', 'editor_marklar').then(function(strsyntaxloading) {
            self.syntaxButtonOn.hide();
            self.syntaxButtonOff.show();
            self.syntaxBody.html('<div class="marklar-syntax-loading">' + strsyntaxloading + '</div>');
            self.syntaxBody.show();
            self.syntaxLoad();
            return true;
        });
    };

    /**
     * Toggle syntax help off.
     *
     * @param {Event} e
     */
    MarklarEditor.prototype.syntaxOff = function(e) {
        var self = this;
        e.preventDefault();

        self.syntaxButtonOff.hide();
        self.syntaxButtonOn.show();
        self.syntaxBody.hide();
        self.syntaxBody.html('');
    };

    /**
     * Load and display the text syntax.
     *
     * @return {Deferred}
     */
    MarklarEditor.prototype.syntaxLoad = function() {
        var self = this;

        return str.get_string('syntax-format' + self.formatSelector.val(), 'editor_marklar').then(function(strsyntax) {
            self.syntaxBody.html(strsyntax);
            return;
        });
    };

    return /** @alias module:editor_marklar */ {
        init: function(params) {
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
