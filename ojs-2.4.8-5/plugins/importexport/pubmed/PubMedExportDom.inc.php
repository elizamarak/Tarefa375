<?php

/**
 * @file plugins/importexport/pubmed/PubMedExportDom.inc.php
 *
 * Copyright (c) 2013-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class PubMedExportDom
 * @ingroup plugins_importexport_pubmed
 *
 * @brief PubMed XML export plugin DOM functions
 */

import('lib.pkp.classes.xml.XMLCustomWriter');

define('PUBMED_DTD_URL', 'http://www.ncbi.nlm.nih.gov:80/entrez/query/static/PubMed.dtd');
define('PUBMED_DTD_ID', '-//NLM//DTD PubMed 2.0//EN');

class PubMedExportDom {

	/**
	 * Build article XML using DOM elements
	 * @param $args Parameters to the plugin
	 *
	 * The DOM for this XML was developed according to the NLM
	 * Standard Publisher Data Format:
	 * http://www.ncbi.nlm.nih.gov/entrez/query/static/spec.html
	 */

	function &generatePubMedDom() {
		// create the output XML document in DOM with a root node
		$doc =& XMLCustomWriter::createDocument('ArticleSet', PUBMED_DTD_ID, PUBMED_DTD_URL);

		return $doc;
	}

	function &generateArticleSetDom(&$doc) {
		$root =& XMLCustomWriter::createElement($doc, 'ArticleSet');
		XMLCustomWriter::appendChild($doc, $root);

		return $root;
	}

	function &generateArticleDom(&$doc, &$journal, &$issue, &$section, &$article) {

		// register the editor submission DAO for use later
		$editorSubmissionDao =& DAORegistry::getDAO('EditorSubmissionDAO');

		/* --- Article --- */
		$root =& XMLCustomWriter::createElement($doc, 'Article');

		/* --- Journal --- */
		$journalNode =& XMLCustomWriter::createElement($doc, 'Journal');
		XMLCustomWriter::appendChild($root, $journalNode);

		$publisherInstitution = $journal->getSetting('publisherInstitution');
		$publisherNode = XMLCustomWriter::createChildWithText($doc, $journalNode, 'PublisherName', $publisherInstitution);

		XMLCustomWriter::createChildWithText($doc, $journalNode, 'JournalTitle', $journal->getTitle($journal->getPrimaryLocale()));

		// check various ISSN fields to create the ISSN tag
		if ($journal->getSetting('printIssn') != '') $ISSN = $journal->getSetting('printIssn');
		elseif ($journal->getSetting('issn') != '') $ISSN = $journal->getSetting('issn');
		elseif ($journal->getSetting('onlineIssn') != '') $ISSN = $journal->getSetting('onlineIssn');
		else $ISSN = '';

		if ($ISSN != '') XMLCustomWriter::createChildWithText($doc, $journalNode, 'Issn', $ISSN);

		XMLCustomWriter::createChildWithText($doc, $journalNode, 'Volume', $issue->getVolume());
		XMLCustomWriter::createChildWithText($doc, $journalNode, 'Issue', $issue->getNumber(), false);

		$datePublished = $article->getDatePublished();
		if (!$datePublished) $datePublished = $issue->getDatePublished();
		if ($datePublished) {
			$pubDateNode =& PubMedExportDom::generatePubDateDom($doc, $datePublished, 'epublish');
			XMLCustomWriter::appendChild($journalNode, $pubDateNode);
		}

		/* --- Replaces --- */
		// this creates a blank replaces tag since OJS doesn't contain PMID metadata
//		XMLCustomWriter::createChildWithText($doc, $root, 'Replaces', '');

		/* --- ArticleTitle / VernacularTitle --- */
		// PubMed requires english titles for ArticleTitle
		if ($article->getLocale() == 'en_US') {
			XMLCustomWriter::createChildWithText($doc, $root, 'ArticleTitle', $article->getTitle($article->getLocale()));
		} else {
			XMLCustomWriter::createChildWithText($doc, $root, 'VernacularTitle', $article->getTitle($article->getLocale()));
		}

		/* --- FirstPage / LastPage --- */
		// there is some ambiguity for online journals as to what
		// "page numbers" are; for example, some journals (eg. JMIR)
		// use the "e-location ID" as the "page numbers" in PubMed
		$pages = $article->getPages();
		if (preg_match("/([0-9]+)\s*-\s*([0-9]+)/i", $pages, $matches)) {
			// simple pagination (eg. "pp. 3-8")
			XMLCustomWriter::createChildWithText($doc, $root, 'FirstPage', $matches[1]);
			XMLCustomWriter::createChildWithText($doc, $root, 'LastPage', $matches[2]);
		} elseif (preg_match("/(e[0-9]+)\s*-\s*(e[0-9]+)/i", $pages, $matches)) { // e9 - e14, treated as page ranges
			XMLCustomWriter::createChildWithText($doc, $root, 'FirstPage', $matches[1]);
			XMLCustomWriter::createChildWithText($doc, $root, 'LastPage', $matches[2]);
		} elseif (preg_match("/(e[0-9]+)/i", $pages, $matches)) {
			// single elocation-id (eg. "e12")
			XMLCustomWriter::createChildWithText($doc, $root, 'FirstPage', $matches[1]);
			XMLCustomWriter::createChildWithText($doc, $root, 'LastPage', $matches[1]);
		} else {
			// we need to insert something, so use the best ID possible
			XMLCustomWriter::createChildWithText($doc, $root, 'FirstPage', $article->getBestArticleId($journal));
			XMLCustomWriter::createChildWithText($doc, $root, 'LastPage', $article->getBestArticleId($journal));
		}

		/* --- DOI --- */
		if ($doi = $article->getPubId('doi')) {
			$doiNode =& XMLCustomWriter::createChildWithText($doc, $root, 'ELocationID', $doi, false);
			XMLCustomWriter::setAttribute($doiNode, 'EIdType', 'doi');
		}

		/* --- Language --- */
		XMLCustomWriter::createChildWithText($doc, $root, 'Language', strtoupper($article->getLanguage()), false);

		/* --- AuthorList --- */
		$authorListNode =& XMLCustomWriter::createElement($doc, 'AuthorList');
		XMLCustomWriter::appendChild($root, $authorListNode);

		$authorIndex = 0;
		foreach ($article->getAuthors() as $author) {
			$authorNode =& PubMedExportDom::generateAuthorDom($doc, $author, $article, $authorIndex++);
			XMLCustomWriter::appendChild($authorListNode, $authorNode);
		}

		/* --- ArticleIdList --- */
		// Pubmed will accept two types of article identifier: pii and doi
		// how this is handled is journal-specific, and will require either
		// configuration in the plugin, or an update to the core code.
		// this is also related to DOI-handling within OJS
		if ($article->getPubId('publisher-id')) {
			$articleIdListNode =& XMLCustomWriter::createElement($doc, 'ArticleIdList');
			XMLCustomWriter::appendChild($root, $articleIdListNode);

			$articleIdNode =& XMLCustomWriter::createChildWithText($doc, $articleIdListNode, 'ArticleId', $article->getPubId('publisher-id'));
			XMLCustomWriter::setAttribute($articleIdNode, 'IdType', 'pii');
		}

		/* --- History --- */
		$historyNode =& XMLCustomWriter::createElement($doc, 'History');
		XMLCustomWriter::appendChild($root, $historyNode);

		// date manuscript received for review
		$receivedNode =& PubMedExportDom::generatePubDateDom($doc, $article->getDateSubmitted(), 'received');
		XMLCustomWriter::appendChild($historyNode, $receivedNode);

		// accepted for publication
		$editordecisions = $editorSubmissionDao->getEditorDecisions($article->getId());

		// if there are multiple decisions, make sure we get the accepted date
		$editordecision = array_pop($editordecisions);
		while ($editordecision['decision'] != SUBMISSION_EDITOR_DECISION_ACCEPT && count($editordecisions) > 0) $editordecision = array_pop($editordecisions);

		if ($editordecision != '') {
			$acceptedNode =& PubMedExportDom::generatePubDateDom($doc, $editordecision['dateDecided'], 'accepted');
			XMLCustomWriter::appendChild($historyNode, $acceptedNode);
		}

		// article revised by publisher or author
		// check if there is a revised version; if so, generate a revised tag
		$revisedFileID = $article->getRevisedFileId();
		if (!empty($revisedFileID)) {
			$articleFileDao =& DAORegistry::getDAO('ArticleFileDAO');
			$articleFile =& $articleFileDao->getArticleFile($revisedFileID);

			if ($articleFile) {
				$revisedNode =& PubMedExportDom::generatePubDateDom($doc, $articleFile->getDateModified(), 'revised');
				XMLCustomWriter::appendChild($historyNode, $revisedNode);
			}
		}

		/* --- Abstract --- */
		if ($article->getAbstract($article->getLocale())) {
			$abstractNode = XMLCustomWriter::createChildWithText($doc, $root, 'Abstract', PKPString::html2utf(strip_tags($article->getAbstract($article->getLocale()))), false);
		}

		if ($subject = $article->getSubject($article->getLocale())) {
			$objectListNode = XMLCustomWriter::createElement($doc, 'ObjectList');
			XMLCustomWriter::appendChild($root, $objectListNode);
			foreach (explode(';', $subject) as $keyword) {
				$objectNode = XMLCustomWriter::createElement($doc, 'Object');
				$objectNode->setAttribute('Type', 'keyword');
				$paramNode =& XMLCustomWriter::createChildWithText($doc, $objectNode, 'Param', $keyword);
				$paramNode->setAttribute('Name', 'value');
				XMLCustomWriter::appendChild($objectListNode, $objectNode);
				unset($keywordNode, $paramNode);
			}
		}

		return $root;
	}

	/**
	 * Generate the Author node DOM for the specified author.
	 * @param $doc DOMDocument
	 * @param $author PKPAuthor
	 * @param $article Article
	 * @param $authorIndex 0-based index of current author
	 */
	function &generateAuthorDom(&$doc, &$author, &$article, $authorIndex) {
		$root =& XMLCustomWriter::createElement($doc, 'Author');

		XMLCustomWriter::createChildWithText($doc, $root, 'FirstName', ucfirst($author->getFirstName()));
		XMLCustomWriter::createChildWithText($doc, $root, 'MiddleName', ucfirst($author->getMiddleName()), false);
		XMLCustomWriter::createChildWithText($doc, $root, 'LastName', ucfirst($author->getLastName()));

		if ($authorIndex == 0) {
			// See http://pkp.sfu.ca/bugzilla/show_bug.cgi?id=7774
			XMLCustomWriter::createChildWithText($doc, $root, 'Affiliation', $author->getAffiliation($article->getLocale()) . '. ' . $author->getEmail(), false);
		}

		return $root;
	}

	function &generatePubDateDom(&$doc, $pubdate, $pubstatus) {
		$root =& XMLCustomWriter::createElement($doc, 'PubDate');

		XMLCustomWriter::setAttribute($root, 'PubStatus', $pubstatus);

		XMLCustomWriter::createChildWithText($doc, $root, 'Year', date('Y', strtotime($pubdate)) );
		XMLCustomWriter::createChildWithText($doc, $root, 'Month', date('m', strtotime($pubdate)), false );
		XMLCustomWriter::createChildWithText($doc, $root, 'Day', date('d', strtotime($pubdate)), false );

		return $root;
	}

}

?>
