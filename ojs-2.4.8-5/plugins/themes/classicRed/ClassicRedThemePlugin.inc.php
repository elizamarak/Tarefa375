<?php

/**
 * @file plugins/themes/classicRed/ClassicRedThemePlugin.inc.php
 *
 * Copyright (c) 2013-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class ClassicRedThemePlugin
 * @ingroup plugins_themes_classicRed
 *
 * @brief "ClassicRed" theme plugin
 */

import('classes.plugins.ThemePlugin');

class ClassicRedThemePlugin extends ThemePlugin {
	/**
	 * Get the name of this plugin. The name must be unique within
	 * its category.
	 * @return String name of plugin
	 */
	function getName() {
		return 'ClassicRedThemePlugin';
	}

	function getDisplayName() {
		return 'ClassicRed Theme';
	}

	function getDescription() {
		return 'Classic red layout';
	}

	function getStylesheetFilename() {
		return 'classicRed.css';
	}

	function getLocaleFilename($locale) {
		return null; // No locale data
	}
}

?>
