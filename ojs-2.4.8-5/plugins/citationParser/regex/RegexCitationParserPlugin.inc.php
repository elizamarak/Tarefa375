<?php

/**
 * @defgroup plugins_citationParser_regex
 */

/**
 * @file plugins/citationParser/regex/RegexCitationParserPlugin.inc.php
 *
 * Copyright (c) 2013-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class RegexCitationParserPlugin
 * @ingroup plugins_citationParser_regex
 *
 * @brief Regular extraction based citation extraction plug-in.
 */


import('lib.pkp.plugins.citationParser.regex.PKPRegexCitationParserPlugin');

class RegexCitationParserPlugin extends PKPRegexCitationParserPlugin {
	/**
	 * Constructor
	 */
	function RegexCitationParserPlugin() {
		parent::PKPRegexCitationParserPlugin();
	}
}

?>
