### 1.0.2 ###

* Fixed unexpected token error on pages with textareas with special chars in
  identifiers (#28). Credit goes to Juan Segarra Montesinos (@juancs).
* Moodle 4.5 marked as supported.

### 1.0.1 ###

* Removed obsolete option that started to print debugging output in Moodle 4.4 (#27).
* Moodle 4.4 marked as supported.

### 1.0.0 ###

* Added preference to use monospace font when editing the text (#11).
* Fixed unit tests to work again. Credit goes to Juan Segarra Montesinos (@juancs).
* Moodle 4.2 and higher is required.

### 1.0.0-beta.3 ###

* Do not pass null as a string parameter to `json_decode()` to avoid deprecation warning.

### 1.0.0-beta.2 ###

* Fixed "No permission to access this repository." error when browsing Content bank
  repository. Credit goes to Juan Segarra Montesinos (@juancs).

### 1.0.0-beta.1 ###

* Fixed missing white space between the Insert image and the Insert file buttons.
* The editor is now considered feature complete, releasing as the first beta.
* Tested under Moodle 3.9.

### 0.9.0 ###

* The master branch now contains the version with AMD modules compiled for Moodle 3.8.
  This will allow to start using ES6 features or APIs that require a polyfill.

### 0.8.x ###

* The maintenance branch for Moodle 3.4 - 3.7 forked off as 0.8.x and uses the old way
  of building the AMD modules.

### 0.8.4 ###

* Include the re-compiled AMD modules and the associated source maps.

### 0.8.3 ###

* Stop using the httpswwwroot setting (deprecated in Moodle 3.4, removed in 3.8).
* Perform travis-ci tests on PHP 7.2.
* Supported are now Moodle versions 3.4 or higher.

### 0.8.2 ###

* Notify filters about updated content in previews - makes filters like MathJax work
  then. Credit goes to Juan Segarra Montesinos (@juancs).

### 0.8.1 ###

* Fixed reported eslint warnings.

### 0.8.0 ###

* Allows to embed images from clipboard (such as screenshots).

### 0.7.0 ###

* Implements the privacy API (GDPR compliance).
* Supported are now Moodle versions 3.3 or higher.

### 0.6.2 ###

* Fixed JS coding style warnings reported by eslint

### 0.6.1 ###

* Only fixes the detected coding style issues.

### 0.6.0 ###

* Displays quick reference for the currently selected format.
* Internal refactoring and improvements in the JavaScript code.

### 0.5.1 ###

* Fixed Moodle code precheck errors and warnings.
* Tested functionality on Moodle 3.3beta.
* Added travis checks against PHP 7.1.

### 0.5.0 ###

* Do not throw database error when submitted in the preview mode (bug #9).
* Add support for the Boost theme and generally make it less dependent on
  actual theme (bug #8).
* Do not display it narrow in places like the Database module (bug #7).

### 0.4.3 ###

* Fixed preview mode in clean-based themes in 3.2 (no boost support yet)
* Format selector, image inserting button and file inserting button are now
  disabled in the preview mode.

### 0.4.2 ###

* Marklar does not attempt to load when used outside of a moodle form.
* Added self-check for the format selector presence before enabling the preview
  feature.

### 0.4.1 ###

* Small CSS fixes on some themes.

### 0.4.0 ###

* Added support for the preview of edited text. The preview is server-side
  generated so it correctly applies filters and other formatting rules.
* Improved styling of the editor to make it look nicer next to file picker
  elements.

### 0.3.1 ###

* The false debugging message "Too many params passed ..." not displayed any
  more (https://tracker.moodle.org/browse/MDL-53423).

### 0.3.0 ###

* Added button to insert link to a file, credit goes to Juan Segarra Montesinos
  (Jaume I University)
* Added user preferences page where any Marklar user can define additional
  supported Moodle formats to use Marklar with.

### 0.2.1 ###

* Added editor icon and logo.

### 0.2.0 ###

* Added support for embedding images via file picker.

### 0.1.0 ###

* First functional version of a thin wrapper implementing the text editor
  interface.
