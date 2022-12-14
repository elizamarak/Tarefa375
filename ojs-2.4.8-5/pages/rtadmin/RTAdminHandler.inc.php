<?php

/**
 * @file pages/rtadmin/RTAdminHandler.inc.php
 *
 * Copyright (c) 2013-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class RTAdminHandler
 * @ingroup pages_rtadmin
 *
 * @brief Handle Reading Tools administration requests.
 */

import('classes.rt.ojs.JournalRTAdmin');
import('classes.handler.Handler');

class RTAdminHandler extends Handler {
	/**
	 * Constructor
	 **/
	function RTAdminHandler() {
		parent::Handler();

		$this->addCheck(new HandlerValidatorJournal($this));
		$this->addCheck(new HandlerValidatorRoles($this, true, null, null, array(ROLE_ID_SITE_ADMIN, ROLE_ID_JOURNAL_MANAGER)));
	}

	/**
	 * If no journal is selected, display list of journals.
	 * Otherwise, display the index page for the selected journal.
	 */
	function index() {
		$this->validate();
		$journal = Request::getJournal();
		$user = Request::getUser();
		if ($journal) {
			$rtDao =& DAORegistry::getDAO('RTDAO');
			$rt = $rtDao->getJournalRTByJournal($journal);
			if (isset($rt)) {
				$version = $rtDao->getVersion($rt->getVersion(), $journal->getId());
			}

			// Display the administration menu for this journal.

			$this->setupTemplate();
			$templateMgr =& TemplateManager::getManager();
			$templateMgr->assign('helpTopicId', 'journal.managementPages.readingTools');
			$templateMgr->assign('versionTitle', isset($version)?$version->getTitle():null);
			$templateMgr->assign('enabled', $rt->getEnabled());

			$templateMgr->display('rtadmin/index.tpl');
		} elseif ($user) {
			// Display a list of journals.
			$journalDao =& DAORegistry::getDAO('JournalDAO');
			$roleDao =& DAORegistry::getDAO('RoleDAO');

			$journals = array();

			$allJournals =& $journalDao->getJournals();
			$allJournals =& $allJournals->toArray();

			foreach ($allJournals as $journal) {
				if ($roleDao->userHasRole($journal->getId(), $user->getId(), ROLE_ID_JOURNAL_MANAGER)) {
					$journals[] = $journal;
				}
			}

			$this->setupTemplate();
			$templateMgr =& TemplateManager::getManager();
			$templateMgr->assign_by_ref('journals', $journals);
			$templateMgr->assign('helpTopicId', 'journal.managementPages.readingTools');
			$templateMgr->display('rtadmin/journals.tpl');
		} else {
			// Not logged in.
			Validation::redirectLogin();
		}
	}

	function validateUrls($args) {
		$this->validate();

		$rtDao =& DAORegistry::getDAO('RTDAO');
		$journal = Request::getJournal();

		if (!$journal) {
			Request::redirect(null, Request::getRequestedPage());
			return;
		}

		$versionId = isset($args[0])?$args[0]:0;
		$journalId = $journal->getId();

		$version = $rtDao->getVersion($versionId, $journalId);

		if ($version) {
			// Validate the URLs for a single version
			$versions = array(&$version);
			import('lib.pkp.classes.core.ArrayItemIterator');
			$versions = new ArrayItemIterator($versions, 1, 1);
		} else {
			// Validate all URLs for this journal
			$versions = $rtDao->getVersions($journalId);
		}

		$this->setupTemplate(true, $version);
		$templateMgr =& TemplateManager::getManager();
		$templateMgr->register_modifier('validate_url', 'smarty_rtadmin_validate_url');
		$templateMgr->assign_by_ref('versions', $versions);
		$templateMgr->assign('helpTopicId', 'journal.managementPages.readingTools');
		$templateMgr->display('rtadmin/validate.tpl');
	}

	/**
	 * Setup common template variables.
	 * @param $subclass boolean set to true if caller is below this handler in the hierarchy
	 * @param $version object The current version, if applicable
	 * @param $context object The current context, if applicable
	 * @param $search object The current search, if applicable
	 */
	function setupTemplate($subclass = false, $version = null, $context = null, $search = null) {
		parent::setupTemplate();
		AppLocale::requireComponents(LOCALE_COMPONENT_PKP_READER, LOCALE_COMPONENT_OJS_MANAGER);
		$templateMgr =& TemplateManager::getManager();

		$pageHierarchy = array(array(Request::url(null, 'user'), 'navigation.user'), array(Request::url(null, 'manager'), 'manager.journalManagement'));

		if ($subclass) $pageHierarchy[] = array(Request::url(null, 'rtadmin'), 'rt.readingTools');

		if ($version) {
			$pageHierarchy[] = array(Request::url(null, 'rtadmin', 'versions'), 'rt.versions');
			$pageHierarchy[] = array(Request::url(null, 'rtadmin', 'editVersion', $version->getVersionId()), $version->getTitle(), true);
			if ($context) {
				$pageHierarchy[] = array(Request::url(null, 'rtadmin', 'contexts', $version->getVersionId()), 'rt.contexts');
				$pageHierarchy[] = array(Request::url(null, 'rtadmin', 'editContext', array($version->getVersionId(), $context->getContextId())), $context->getAbbrev(), true);
				if ($search) {
					$pageHierarchy[] = array(Request::url(null, 'rtadmin', 'searches', array($version->getVersionId(), $context->getContextId())), 'rt.searches');
					$pageHierarchy[] = array(Request::url(null, 'rtadmin', 'editSearch', array($version->getVersionId(), $context->getContextId(), $search->getSearchId())), $search->getTitle(), true);
				}
			}
		}
		$templateMgr->assign('pageHierarchy', $pageHierarchy);
	}
}

function rtadmin_validate_url($url, $useGet = false, $redirectsAllowed = 5) {
	$data = parse_url($url);
	if(!isset($data['host'])) {
		return false;
	}

	$fp = @ fsockopen($data['host'], isset($data['port']) && !empty($data['port']) ? $data['port'] : 80, $errno, $errstr, 10);
	if (!$fp) {
		return false;
	}

	$req = sprintf("%s %s HTTP/1.0\r\nHost: %s\r\nUser-Agent: Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.4b) Gecko/20030516\r\n\r\n", ($useGet ? 'GET' : 'HEAD'), (isset($data['path']) && $data['path'] !== '' ? $data['path'] : '/') .  (isset($data['query']) && $data['query'] !== '' ? '?' .  $data['query'] : ''), $data['host']);

	fputs($fp, $req);

	for($res = '', $time = time(); !feof($fp) && $time >= time() - 15; ) {
		$res .= fgets($fp, 128);
	}

	fclose($fp);

	// Check result for HTTP status code.
	if(!preg_match('!^HTTP/(\d\.?\d*) (\d+)\s*(.+)[\n\r]!m', $res, $matches)) {
		return false;
	}
	list($match, $http_version, $http_status_no, $http_status_str) = $matches;

	// If HTTP status code 2XX (Success)
	if(preg_match('!^2\d\d$!', $http_status_no)) return true;

	// If HTTP status code 3XX (Moved)
	if(preg_match('!^(?:(?:Location)|(?:URI)|(?:location)): ([^\s]+)[\r\n]!m', $res, $matches)) {
		// Recursively validate the URL if an additional redirect is allowed..
		if ($redirectsAllowed >= 1) return rtadmin_validate_url(preg_match('!^https?://!', $matches[1]) ? $matches[1] : $data['scheme'] . '://' . $data['host'] . ($data['path'] !== '' && strpos($matches[1], '/') !== 0  ? $data['path'] : (strpos($matches[1], '/') === 0 ? '' : '/')) . $matches[1], $useGet, $redirectsAllowed-1);
		return false;
	}

	// If it's not found or there is an error condition
	if(($http_status_no == 403 || $http_status_no == 404 || $http_status_no == 405 || $http_status_no == 500 || strstr($res, 'Bad Request') || strstr($res, 'Bad HTTP Request') || trim($res) == '') && !$useGet) {
		return rtadmin_validate_url($url, true, $redirectsAllowed-1);
	}

	return false;
}

function smarty_rtadmin_validate_url ($search, $errors) {
	// Make sure any prior content is flushed to the user's browser.
	flush();
	ob_flush();

	if (!is_array($errors)) $errors = array();

	if (!rtadmin_validate_url($search->getUrl())) $errors[] = array('url' => $search->getUrl(), 'id' => $search->getSearchId());
	if ($search->getSearchUrl() && !rtadmin_validate_url($search->getSearchUrl())) $errors[] = array('url' => $search->getSearchUrl(), 'id' => $search->getSearchId());

	return $errors;
}
?>
