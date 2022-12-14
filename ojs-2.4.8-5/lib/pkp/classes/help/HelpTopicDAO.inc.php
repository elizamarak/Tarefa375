<?php

/**
 * @file classes/help/HelpTopicDAO.inc.php
 *
 * Copyright (c) 2013-2019 Simon Fraser University
 * Copyright (c) 2000-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class HelpTopicDAO
 * @ingroup help
 * @see HelpTopic
 *
 * @brief Operations for retrieving HelpTopic objects.
 */


import('lib.pkp.classes.help.HelpTopic');
import('lib.pkp.classes.help.PKPHelp');

class HelpTopicDAO extends XMLDAO {
	/**
	 * Constructor
	 */
	function HelpTopicDAO() {
		parent::XMLDAO();
	}

	function &_getCache($topicId) {
		$cache =& Registry::get('helpTopicCache', true, null);
		$locale = PKPHelp::getLocale();
		if (!isset($cache[$locale][$topicId])) {
			$help =& PKPHelp::getHelp();
			$cacheManager =& CacheManager::getManager();
			$cache[$locale][$topicId] = $cacheManager->getFileCache('help-topic-' . $locale, $topicId, array($this, '_cacheMiss'));

			// Check to see if the cache info is outdated.
			$cacheTime = $cache[$locale][$topicId]->getCacheTime();
			if ($cacheTime !== null && $cacheTime < filemtime($this->getFilename($topicId))) {
				// The cached data is out of date.
				$cache[$locale][$topicId]->flush();
			}
		}
		return $cache[$locale][$topicId];
	}

	function &getMappingFile($topicId) {
		$help =& PKPHelp::getHelp();
		$mappingFiles =& $help->getMappingFiles();

		for ($i = 0; $i < count($mappingFiles); $i++) {
			// "foreach by reference" hack
			$mappingFile =& $mappingFiles[$i];
			if ($mappingFile->containsTopic($topicId)) return $mappingFile;
			unset($mappingFile);
		}
		$returner = null;
		return $returner;
	}

	function getFilename($topicId) {
		$mappingFile =& $this->getMappingFile($topicId);
		return $mappingFile?$mappingFile->getTopicFilename($topicId):null;
	}

	function _cacheMiss(&$cache, $id) {
		$data =& Registry::get('helpTopicData', true, null);
		if ($data === null) {
			$helpFile = $this->getFilename($cache->getCacheId());
			$data =& $this->parseStruct($helpFile);

			// check if data exists before saving it to cache
			if ($data === false) {
				$returner = false;
				return $returner;
			}
			$cache->setEntireCache($data);
		}
		return null;
	}

	/**
	 * Retrieve a topic by its ID.
	 * @param $topicId string
	 * @return HelpTopic
	 */
	function &getTopic($topicId) {
		$cache =& $this->_getCache($topicId);
		$data = $cache->getContents();

		// check if data exists after loading
		if (!is_array($data)) {
			$returner = false;
			return $returner;
		}

		$topic = new HelpTopic();

		$topic->setId($data['topic'][0]['attributes']['id']);
		$topic->setTitle($data['topic'][0]['attributes']['title']);
		$topic->setTocId($data['topic'][0]['attributes']['toc']);
		if (isset($data['topic'][0]['attributes']['subtoc'])) {
			$topic->setSubTocId($data['topic'][0]['attributes']['subtoc']);
		}

		if (isset($data['section'])) {
			foreach ($data['section'] as $sectionData) {
				$section = new HelpTopicSection();
				$section->setTitle(isset($sectionData['attributes']['title']) ? $sectionData['attributes']['title'] : null);
				$section->setContent($sectionData['value']);
				$topic->addSection($section);
			}
		}

		if (isset($data['related_topic'])) {
			foreach ($data['related_topic'] as $relatedTopic) {
				$relatedTopicArray = array('id' => $relatedTopic['attributes']['id'], 'title' => $relatedTopic['attributes']['title']);
				$topic->addRelatedTopic($relatedTopicArray);
			}
		}

		return $topic;
	}

	/**
	 * Returns a set of topics matching a specified keyword.
	 * @param $keyword string
	 * @return array matching HelpTopics
	 */
	function &getTopicsByKeyword($keyword) {
		$keyword = PKPString::strtolower($keyword);
		$matchingTopics = array();
		$help =& PKPHelp::getHelp();
		foreach ($help->getSearchPaths() as $searchPath => $mappingFile) {
			$dir = opendir($searchPath);
			while (($file = readdir($dir)) !== false) {
				$currFile = $searchPath . DIRECTORY_SEPARATOR . $file;
				if (is_dir($currFile) && $file != 'toc' && $file != '.' && $file != '..') {
					HelpTopicDAO::searchDirectory($mappingFile, $matchingTopics,$keyword,$currFile);
				}
			}
			closedir($dir);
		}

		krsort($matchingTopics);
		$topics = array_values($matchingTopics);

		return $topics;
	}

	/**
	 * Parses deeper into folders if subdirectories exists otherwise scans the topic xml files
	 * @param $mappingFile array The responsible mapping file
	 * @param $matchingTopics array stores topics that match the keyword
	 * @param $keyword string
	 * @param $dir string
	 * @modifies $matchingTopics array by reference by making appropriate calls to functions
	 */
	function searchDirectory(&$mappingFile, &$matchingTopics,$keyword,$dir) {
		$currDir = opendir($dir);
		while (($file = readdir($currDir)) !== false) {
			$currFile = sprintf('%s/%s',$dir,$file);
			if (is_dir($currFile) && $file != '.' && $file != '..' && $file != 'toc') {
				HelpTopicDAO::searchDirectory($mappingFile, $matchingTopics,$keyword,$currFile);
			} else {
				HelpTopicDAO::scanTopic($mappingFile, $matchingTopics,$keyword,$dir,$file);
			}
		}
		closedir($currDir);
	}

	/**
	 * Scans topic xml files for keywords
	 * @param $mappingFile object The responsible mapping file
	 * @param $matchingTopics array stores topics that match the keyword
	 * @param $keyword string
	 * @param $dir string
	 * @param $file string
	 * @modifies $matchingTopics array by reference
	 */
	function scanTopic(&$mappingFile, &$matchingTopics,$keyword,$dir,$file) {
		if (preg_match('/^\d{6,6}\.xml$/', $file)) {
			$topicId = $mappingFile->getTopicIdForFilename($dir . DIRECTORY_SEPARATOR . $file);
			$topic =& $this->getTopic($topicId);

			if ($topic) {
				$numMatches = PKPString::substr_count(PKPString::strtolower($topic->getTitle()), $keyword);

				foreach ($topic->getSections() as $section) {
					$numMatches += PKPString::substr_count(PKPString::strtolower($section->getTitle()), $keyword);
					$numMatches += PKPString::substr_count(PKPString::strtolower($section->getContent()), $keyword);
				}

				if ($numMatches > 0) {
					$matchingTopics[($numMatches << 16) + count($matchingTopics)] = $topic;
				}
			}
		}
	}
}

?>
