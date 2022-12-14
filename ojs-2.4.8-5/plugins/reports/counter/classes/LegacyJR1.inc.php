<?php

/**
 * @file plugins/reports/counter/classes/LegacyJR1.inc.php
 *
 * Copyright (c) 2014 University of Pittsburgh
 * Distributed under the GNU GPL v2 or later. For full terms see the file docs/COPYING.
 *
 * @class LegacyJR1
 * @ingroup plugins_reports_counter
 *
 * @brief The Legacy COUNTER JR1 (r3) report
 */

class LegacyJR1 {

	/**
	 * @var string $_templatePath The location of the reportxml template
	*/
	var $_templatePath;

	/**
	 * Constructor
	 * @param string $templatePath
	 */
	function LegacyJR1($templatePath) {
		$this->_templatePath = $templatePath;
	}

	/**
	 * Display the JR1 (R3) report
	 * @param $request PKPRequest
	 */
	function display(&$request) {
		$oldStats = (boolean) $request->getUserVar('useOldCounterStats');
		$year = (string) $request->getUserVar('year');
		$type = (string) $request->getUserVar('type');
		switch ($type) {
			case 'report':
				$this->_report($request, $year, $oldStats);
				break;
			case 'reportxml':
				$this->_reportXml($request, $year, $oldStats);
				break;
		}
	}

	/**
	 * Generate a report file.
	 * @param $request PKPRequest
	 * @param $year string
	 */
	function _report(&$request, $year, $useLegacyStats) {
		$journal =& $request->getJournal();
		list($begin, $end) = $this->_getLimitDates($year);

		header('content-type: text/comma-separated-values');
		header('content-disposition: attachment; filename=counter-' . date('Ymd') . '.csv');

		$fp = fopen('php://output', 'wt');
		PKPString::fputcsv($fp, array(__('plugins.reports.counter.1a.title1')));
		PKPString::fputcsv($fp, array(__('plugins.reports.counter.1a.title2', array('year' => $year))));
		PKPString::fputcsv($fp, array()); // FIXME: Criteria should be here?
		PKPString::fputcsv($fp, array(__('plugins.reports.counter.1a.dateRun')));
		PKPString::fputcsv($fp, array(strftime("%Y-%m-%d")));

		$cols = array(
				'',
		__('plugins.reports.counter.1a.publisher'),
		__('plugins.reports.counter.1a.platform'),
		__('plugins.reports.counter.1a.printIssn'),
		__('plugins.reports.counter.1a.onlineIssn')
		);
		for ($i=1; $i<=12; $i++) {
			$time = strtotime($year . '-' . $i . '-01');
			strftime('%b', $time);
			$cols[] = strftime('%b-%Y', $time);
		}

		$cols[] = __('plugins.reports.counter.1a.ytdTotal');
		$cols[] = __('plugins.reports.counter.1a.ytdHtml');
		$cols[] = __('plugins.reports.counter.1a.ytdPdf');
		fputcsv($fp, $cols);

		// Display the totals first
		$totals = $this->_getMonthlyTotalRange($begin, $end, $useLegacyStats);
		$cols = array(
		__('plugins.reports.counter.1a.totalForAllJournals'),
				'-', // Publisher
				'', // Platform
				'-',
				'-'
		);
		$this->_formColumns($cols, $totals);
		fputcsv($fp, $cols);

		// Get statistics from the log.
		$journalDao =& DAORegistry::getDAO('JournalDAO');
		$journalIds = $this->_getJournalIds($useLegacyStats);
		foreach ($journalIds as $journalId) {
			$journal =& $journalDao->getById($journalId);
			if (!$journal) continue;
			$entries = $this->_getMonthlyLogRange($journalId, $begin, $end, $useLegacyStats);
			$cols = array(
				$journal->getLocalizedTitle(),
				$journal->getSetting('publisherInstitution'),
				__('common.openJournalSystems'), // Platform
				$journal->getSetting('printIssn'),
				$journal->getSetting('onlineIssn')
			);
			$this->_formColumns($cols, $entries);
			fputcsv($fp, $cols);
			unset($journal, $entry);
		}

		fclose($fp);
	}

	/**
	* Internal function to form some of the CSV columns
	 * @param $cols array() by reference
	 * @param $entries array()
	 * $cols will be modified
	 */
	function _formColumns(&$cols, $entries) {
		$allMonthsTotal = 0;
		$currTotal = 0;
		$htmlTotal = 0;
		$pdfTotal = 0;
		for ($i = 1; $i <= 12; $i++) {
			$currTotal = 0;
			foreach ($entries as $entry) {
				$month = (int) substr($entry[STATISTICS_DIMENSION_MONTH], 4, 2);
				if ($i== $month) {
					$metric = $entry[STATISTICS_METRIC];
					$currTotal += $metric;
					if ($entry[STATISTICS_DIMENSION_FILE_TYPE] == STATISTICS_FILE_TYPE_HTML) {
						$htmlTotal += $metric;
					} else if ($entry[STATISTICS_DIMENSION_FILE_TYPE] == STATISTICS_FILE_TYPE_PDF) {
						$pdfTotal += $metric;
					}
				}
			}
			$cols[]=$currTotal;
			$allMonthsTotal += $currTotal;
		}
		$cols[] = $allMonthsTotal;
		$cols[] = $htmlTotal;
		$cols[] = $pdfTotal;
	}

	/**
	 * Internal function to assign information for the Counter part of a report
	 * @param $templateManager PKPTemplateManager
	 * @param $begin string
	 * @param $end string
	 * @param $useLegacyStats boolean
	 */
	function _assignTemplateCounterXML(&$templateManager, $begin, $end='', $useLegacyStats) {
		$journal =& Request::getJournal();

		$journalDao =& DAORegistry::getDAO('JournalDAO');
		$journalIds = $this->_getJournalIds($useLegacyStats);

		if ($end == '') $end = $begin;

		$i=0;

		foreach ($journalIds as $journalId) {
			$journal =& $journalDao->getById($journalId);
			if (!$journal) continue;
			$entries = $this->_getMonthlyLogRange($journalId, $begin, $end, $useLegacyStats);

			$journalsArray[$i]['entries'] = $this->_arrangeEntries($entries);
			$journalsArray[$i]['journalTitle'] = $journal->getLocalizedTitle();
			$journalsArray[$i]['publisherInstitution'] = $journal->getSetting('publisherInstitution');
			$journalsArray[$i]['printIssn'] = $journal->getSetting('printIssn');
			$journalsArray[$i]['onlineIssn'] = $journal->getSetting('onlineIssn');
			$i++;
		}

		$siteSettingsDao =& DAORegistry::getDAO('SiteSettingsDAO');
		$siteTitle = $siteSettingsDao->getSetting('title',AppLocale::getLocale());

		$base_url =& Config::getVar('general','base_url');

		$reqUser =& Request::getUser();
		if ($reqUser) {
			$templateManager->assign('reqUserName', $reqUser->getUsername());
			$templateManager->assign('reqUserId', $reqUser->getUserId());
		} else {
			$templateManager->assign('reqUserName', __('plugins.reports.counter.1a.anonymous'));
			$templateManager->assign('reqUserId', '');
		}

		$templateManager->assign_by_ref('journalsArray', $journalsArray);

		$templateManager->assign('siteTitle', $siteTitle);
		$templateManager->assign('base_url', $base_url);
	}

	/**
	* Internal function to collect structures for output
	 * @param $entries array()
	 */
	function _arrangeEntries($entries) {
		$ret = array();

		$i = 0;

		foreach ($entries as $entry) {
			$year = substr($entry['month'], 0, 4);
			$month = substr($entry['month'], 4, 2);
			$start = date("Y-m-d", mktime(0, 0, 0, $month, 1, $year));
			$end = date("Y-m-t", mktime(0, 0, 0, $month, 1, $year));

			$rangeExists = false;
			foreach ($ret as $key => $record) {
				if ($record['start'] == $start && $record['end'] == $end) {
					$rangeExists = true;
					break;
				}
			}

			if (!$rangeExists) {
				$workingKey = $i;
				$i++;

				$ret[$workingKey]['start'] = $start;
				$ret[$workingKey]['end'] = $end;
			} else {
				$workingKey = $key;
			}

			if (array_key_exists('count_total', $ret[$workingKey])) {
				$totalCount = $ret[$workingKey]['count_total'];
			} else {
				$totalCount = 0;
			}
			$ret[$workingKey]['count_total'] = $entry[STATISTICS_METRIC] + $totalCount;
			if ($entry[STATISTICS_DIMENSION_FILE_TYPE] == STATISTICS_FILE_TYPE_HTML) {
				$ret[$workingKey]['count_html'] = $entry[STATISTICS_METRIC];
			} else if ($entry[STATISTICS_DIMENSION_FILE_TYPE] == STATISTICS_FILE_TYPE_PDF) {
				$ret[$workingKey]['count_pdf'] = $entry[STATISTICS_METRIC];
			}
		}

		return $ret;
	}

	/**
	 * Return the begin and end dates
	 * based on the passed year.
	 * @param $year string
	 * @return array
	 */
	function _getLimitDates($year) {
		$begin = "$year-01-01";
		$end = "$year-12-01";

		return array($begin, $end);
	}

	/**
	 * Get the valid journal IDs for which log entries exist in the DB.
	 * @param $useLegacyStats boolean Use the old counter plugin data.
	 * @return array
	 */
	function _getJournalIds($useLegacyStats = false) {
		$metricsDao =& DAORegistry::getDAO('MetricsDAO'); /* @var $metricsDao MetricsDAO */
		if ($useLegacyStats) {
			$results = $metricsDao->getMetrics(OJS_METRIC_TYPE_LEGACY_COUNTER, array(STATISTICS_DIMENSION_ASSOC_ID));
			$fieldId = STATISTICS_DIMENSION_ASSOC_ID;
		} else {
			$filter = array(STATISTICS_DIMENSION_ASSOC_TYPE => ASSOC_TYPE_GALLEY);
			$results = $metricsDao->getMetrics(OJS_METRIC_TYPE_COUNTER, array(STATISTICS_DIMENSION_CONTEXT_ID), $filter);
			$fieldId = STATISTICS_DIMENSION_CONTEXT_ID;
		}
		$journalIds = array();
		foreach($results as $record) {
			$journalIds[] = $record[$fieldId];
		}
		return $journalIds;
	}

	/**
	 * Retrieve a monthly log entry range.
	 * @param $journalId int
	 * @param $begin
	 * @param $end
	 * @param $useLegacyStats boolean Use the old counter plugin data.
	 * @return 2D array
	 */
	function _getMonthlyLogRange($journalId = null, $begin, $end, $useLegacyStats = false) {
		$begin = date('Ym', strtotime($begin));
		$end = date('Ym', strtotime($end));

		$metricsDao =& DAORegistry::getDAO('MetricsDAO'); /* @var $metricsDao MetricsDAO */
		$columns = array(STATISTICS_DIMENSION_MONTH, STATISTICS_DIMENSION_FILE_TYPE);
		$filter = array(
			STATISTICS_DIMENSION_MONTH => array('from' => $begin, 'to' => $end)
		);

		if ($useLegacyStats) {
			$dimension = STATISTICS_DIMENSION_ASSOC_ID;
			$metricType = OJS_METRIC_TYPE_LEGACY_COUNTER;
		} else {
			$dimension = STATISTICS_DIMENSION_CONTEXT_ID;
			$metricType = OJS_METRIC_TYPE_COUNTER;
			$filter[STATISTICS_DIMENSION_ASSOC_TYPE] = ASSOC_TYPE_GALLEY;
		}

		if ($journalId) {
			$columns[] = $dimension;
			$filter[$dimension] = $journalId;
		}

		$results = $metricsDao->getMetrics($metricType, $columns, $filter);
		return $results;
	}

	/**
	 * Retrieve a monthly log entry range.
	 * @param $begin
	 * @param $end
	 * @param $useLegacyStats boolean Use the old counter plugin data.
	 * @return 2D array
	 */
	function _getMonthlyTotalRange($begin, $end, $useLegacyStats = false) {
		return $this->_getMonthlyLogRange(null, $begin, $end, $useLegacyStats);
	}

	/**
	 *
	 * Counter report in XML
	 * @param $request PKPRequest
	 * @param $year string
	 * @param $useLegacyStats boolean
	 */
	function _reportXML(&$request, $year, $useLegacyStats) {
		$templateManager =& TemplateManager::getManager();
		list($begin, $end) = $this->_getLimitDates($year);

		$this->_assignTemplateCounterXML($templateManager, $begin, $end, $useLegacyStats);
		$templateManager->display($this->_templatePath . 'reportxml.tpl', 'text/xml');
	}

}

?>
