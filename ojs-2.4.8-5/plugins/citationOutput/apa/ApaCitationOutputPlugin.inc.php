<?php

/**
 * @defgroup plugins_citationOutput_apa
 */

/**
 * @file plugins/citationOutput/apa/ApaCitationOutputPlugin.inc.php
 *
 * Copyright (c) 2013-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class ApaCitationOutputPlugin
 * @ingroup plugins_citationOutput_apa
 *
 * @brief APA citation style plug-in.
 */


import('lib.pkp.plugins.citationOutput.apa.PKPApaCitationOutputPlugin');

class ApaCitationOutputPlugin extends PKPApaCitationOutputPlugin {
	/**
	 * Constructor
	 */
	function ApaCitationOutputPlugin() {
		parent::PKPApaCitationOutputPlugin();
	}
}

?>
