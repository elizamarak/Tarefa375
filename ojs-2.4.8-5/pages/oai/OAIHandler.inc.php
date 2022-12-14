<?php

/**
 * @file pages/oai/OAIHandler.inc.php
 *
 * Copyright (c) 2013-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class OAIHandler
 * @ingroup pages_oai
 *
 * @brief Handle OAI protocol requests.
 */

define('SESSION_DISABLE_INIT', 1); // FIXME?

import('classes.oai.ojs.JournalOAI');
import('classes.handler.Handler');

class OAIHandler extends Handler {
	/**
	 * Constructor
	 **/
	function OAIHandler() {
		parent::Handler();
	}

	function index($args, $request) {
		$this->validate();
		PluginRegistry::loadCategory('oaiMetadataFormats', true);

		$oai = new JournalOAI(new OAIConfig($request->url(null, 'oai'), Config::getVar('oai', 'repository_id')));
		if (!$request->getJournal() && Request::getRequestedJournalPath() != 'index') {
			$dispatcher = $request->getDispatcher();
			return $dispatcher->handle404();
		}
		$oai->execute();
	}

	function validate() {
		// Site validation checks not applicable
		//parent::validate();

		if (!Config::getVar('oai', 'oai')) {
			Request::redirect(null, 'index');
		}
	}

	/**
	 * @see PKPHandler::requireSSL()
	 */
	function requireSSL() {
		return false;
	}
}

?>
