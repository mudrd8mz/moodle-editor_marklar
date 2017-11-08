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
