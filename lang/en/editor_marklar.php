<?php
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
 * Strings for the Marklar editor.
 *
 * @package     editor_marklar
 * @category    string
 * @copyright   2016 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['insertimage'] = 'Insert image';
$string['insertlink'] = 'Insert file';
$string['pluginname'] = 'Marklar';
$string['preferences'] = 'Marklar editor preferences';
$string['preferencesediting'] = 'Editing options';
$string['preferencesformat'] = 'Additional text formats to be also edited with Marklar';
$string['preferencesformat_help'] = 'Marklar natively supports Markdown formatted texts. It can be also used for editing texts in other formats. Select all additional text formats you want to edit with Marklar, too.

By default, Marklar is used for Moodle auto-format and Plain text formatted fields. For editing HTML, another rich text editor (such as Atto or TinyMCE) will be used.';
$string['preferencesmonospace'] = 'Use monospace font';
$string['previewloading'] = 'Loading preview…';
$string['previewoff'] = 'Edit';
$string['previewon'] = 'Preview';
$string['privacy:export:preferences:format'] = 'Whether you prefer to use Marklar for editing texts with {$a->format} syntax.';
$string['syntax-format0'] = '<p>Moodle auto-format allows to type text normally, as if you were sending a plain-text email. Line breaks will be retained. You can still embed an HTML code if you want to and it will be applied.</p>';
$string['syntax-format1'] = '<dl>
<dt>Links</dt>
 <dd><code>&lt;a href="https://example.com"&gt;Link text&lt;/a&gt;</code></dd>
<dt>Emphasis and importance</dt>
 <dd><code>&lt;em&gt;Emphasized text&lt;/em&gt;</code></dd>
 <dd><code>&lt;strong&gt;Strongly important text&lt;/strong&gt;</code></dd>
<dt>Headings</dt>
 <dd><code>&lt;h2&gt;Level 2&lt;/h2&gt;</code></dd>
 <dd><code>&lt;h3&gt;Level 3&lt;/h2&gt;</code></dd>
<dt>Paragraphs and line breaks</dt>
 <dd><code>&lt;p&gt;Paragraph text&lt;/p&gt;</code></dd>
 <dd><code>Line&lt;br&gt;break</code></dd>
</dl>';
$string['syntax-format2'] = '<p>This format is useful when you need to include lots of code or HTML that you want to be displayed exactly as you wrote it. It still translates spaces and new lines, but otherwise your text isn\'t touched.</p>';
// @codingStandardsIgnoreStart
$string['syntax-format4'] = '<dl>
<dt>Links</dt>
 <dd><code>[link text](https://example.com)</code></dd>
 <dd><code>[link text](https://example.com "Link title")</code></dd>
<dt>Emphasis and importance</dt>
 <dd><code>_Emphasized text_</code></dd>
 <dd><code>*Emphasized text*</code></dd>
 <dd><code>__Strongly important text__</code></dd>
 <dd><code>**Strongly important text**</code></dd>
<dt>Headings</dt>
 <dd><code>## Level 2 ##</code></dd>
 <dd><code>### Level 3 ###</code></dd>
<dt>Paragraphs and line breaks</dt>
 <dd>Paragraphs are separated by a blank line. For a line break, end a line with two or more spaces.</dd>
<dt>Blockquotes</dt>
 <dd><code>&gt; Email-style of blockquoting</code></dd>
<dt>Lists</dt>
 <dd><code>* Bullet list item</code></dd>
 <dd><code>1. Numbered list item</code></dd>
<dt>Preformatted</dt>
 <dd><code>`function_name()`</code> (inline)</dd>
 <dd><code>&nbsp;&nbsp;&nbsp;code_block()</code> (indent with four spaces)</dd>
<dt>Horizontal rule</dt>
 <dd><code>---</code></dd>
 <dd><code>***</code></dd>
<dt>HTML</dt>
 <dd>For any markup that is not covered by Markdown syntax, simply use raw HTML.</dd>
 <dd><code>&lt;span class="badge badge-info"&gt;Notice&lt;/span&gt;</code></dd>
 <dd><code>&lt;img class="img-responsive" src="…" alt="…" /&gt;</code></dd>
</dl>
<hr>
<p><a target="_blank" href="https://daringfireball.net/projects/markdown/syntax">Full Markdown syntax documentation</a></p>';
// @codingStandardsIgnoreEnd
$string['syntaxloading'] = 'Loading syntax help…';
$string['syntaxoff'] = 'Hide syntax';
$string['syntaxon'] = 'Show syntax';
