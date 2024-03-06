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
 * Allows images to be pasted into textarea editor field.
 *
 * @module      editor_marklar/imagepaster
 * @copyright   2018 David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/log', 'core/config'], function($, Log, Config) {

    "use strict";

    /**
     * Prepare a new image paster instance.
     *
     * @constructor
     * @param {jQuery} textarea - Editor's textarea element.
     * @param {Object} imagepickeroptions - Filepicker component used for uploading.
     * @param {function(Object)} callback - Function to run when the pasted image has been uploaded.
     */
    function ImagePaster(textarea, imagepickeroptions, callback) {
        var self = this;

        self.textarea = textarea;
        self.imagepickeroptions = imagepickeroptions;
        self.callback = callback;

        self.initPasteListener();
    }

    /**
     * Register a handler listening to the paste event in the textarea.
     */
    ImagePaster.prototype.initPasteListener = function() {
        var self = this;

        self.textarea.on('paste', function(e) {
            var items = e.originalEvent.clipboardData.items;
            for (var i = 0; i < items.length; i++) {
                var item = items[i];
                if (item.type.indexOf('image/') === 0) {
                    self.uploadImage(item.getAsFile());
                }
            }
        });
    };

    /**
     * Upload the pasted file to Moodle.
     *
     * @param {File} file - Pasted file.
     */
    ImagePaster.prototype.uploadImage = function(file) {
        var self = this;

        var filename = (Math.random() * 1000).toString().replace('.', '') + '_' + file.name;
        var repositorykeys = window.Object.keys(self.imagepickeroptions.repositories);
        var formdata = new window.FormData();
        var uploadrepofound = false;

        for (var i = 0; i < repositorykeys.length; i++) {
            if (self.imagepickeroptions.repositories[repositorykeys[i]].type === 'upload') {
                formdata.append('repo_id', self.imagepickeroptions.repositories[repositorykeys[i]].id);
                uploadrepofound = true;
                break;
            }
        }

        if (!uploadrepofound) {
            return;
        }

        formdata.append('repo_upload_file', file, filename);
        formdata.append('itemid', self.imagepickeroptions.itemid);
        formdata.append('author', self.imagepickeroptions.author);
        formdata.append('env', self.imagepickeroptions.env);
        formdata.append('sesskey', Config.sesskey);
        formdata.append('client_id', self.imagepickeroptions.client_id);

        if (self.imagepickeroptions.context.id) {
            formdata.append('ctx_id', self.imagepickeroptions.context.id);
        }

        $.ajax(Config.wwwroot + '/repository/repository_ajax.php?action=upload', {
            type: 'POST',
            data: formdata,
            dataType: 'json',
            processData: false,
            contentType: false,
            async: true
        }).done(function(res) {
            if ('error' in res) {
                Log.error('imagepaster: error uploading image: ' + res.errorcode + ': ' + res.error);
            } else {
                self.callback(res);
            }
        }).fail(function(error) {
            Log.error('imagepaster: error uploading image: ' + error.status + ' ' + error.statusText);
        });
    };

    return {
        /**
         * Initialize the image paster module.
         *
         * The callback will be run when the file is sucessfully uploaded. It will
         * be given the object returned by repository_upload::process_upload():
         *  - url: draftfile URL of the uploaded file
         *  - id: itemid of the uploaded file
         *  - file: name of the uploaded file
         *
         * @method
         * @param {jQuery|Element|string} textareaorid - Editor's textarea element or its id.
         * @param {Object} imagepickeroptions - Filepicker component used for uploading.
         * @param {function(Object)} callback - Function to run when the pasted image has been uploaded.
         * @returns {ImagePaster|bool} - ImagePaster instance or false on error.
         */
        init: function(textareaorid, imagepickeroptions, callback) {

            var textarea;

            if (typeof textareaorid === 'string') {
                textarea = $(document.getElementById(textareaorid));

            } else {
                textarea = $(textareaorid);
            }

            if (!textarea.length) {
                Log.error('imagepaster: invalid editor textarea element');
                return false;
            }

            if (!imagepickeroptions) {
                Log.error('imagepaster: invalid image picker options');
                return false;
            }

            if (!callback || typeof callback != 'function') {
                Log.error('imagepaster: invalid callback specified');
                return false;
            }

            return new ImagePaster(textarea, imagepickeroptions, callback);
        }
    };
});
