<?php

/**
 * @file plugins/importexport/users/UserExportDom.inc.php
 *
 * Copyright (c) 2013-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class UserExportDom
 * @ingroup plugins_importexport_users
 *
 * @brief User plugin DOM functions for export
 */

import('lib.pkp.classes.xml.XMLCustomWriter');

define('USERS_DTD_URL', 'http://pkp.sfu.ca/ojs/dtds/users.dtd');
define('USERS_DTD_ID', '-//PKP/OJS Users XML//EN');

class UserExportDom {

	function UserExportDom() {
		return true;
	}

	function &exportUsers(&$journal, &$users, $allowedRoles = null) {
		$roleDao =& DAORegistry::getDAO('RoleDAO');

		$doc =& XMLCustomWriter::createDocument('users', USERS_DTD_ID, USERS_DTD_URL);
		$root =& XMLCustomWriter::createElement($doc, 'users');

		foreach ($users as $user) {
			$userNode =& XMLCustomWriter::createElement($doc, 'user');

			XMLCustomWriter::createChildWithText($doc, $userNode, 'username', $user->getUserName(), false);
			$passwordNode =& XMLCustomWriter::createChildWithText($doc, $userNode, 'password', $user->getPassword());
			XMLCustomWriter::setAttribute($passwordNode, 'encrypted', Config::getVar('security', 'encryption'));
			XMLCustomWriter::createChildWithText($doc, $userNode, 'salutation', $user->getSalutation(), false);
			XMLCustomWriter::createChildWithText($doc, $userNode, 'first_name', $user->getFirstName());
			XMLCustomWriter::createChildWithText($doc, $userNode, 'middle_name', $user->getMiddleName(), false);
			XMLCustomWriter::createChildWithText($doc, $userNode, 'last_name', $user->getLastName());
			XMLCustomWriter::createChildWithText($doc, $userNode, 'initials', $user->getInitials(), false);
			XMLCustomWriter::createChildWithText($doc, $userNode, 'gender', $user->getGender(), false);
			XMLCustomWriter::createChildWithText($doc, $userNode, 'email', $user->getEmail());
			XMLCustomWriter::createChildWithText($doc, $userNode, 'url', $user->getUrl(), false);
			XMLCustomWriter::createChildWithText($doc, $userNode, 'phone', $user->getPhone(), false);
			XMLCustomWriter::createChildWithText($doc, $userNode, 'fax', $user->getFax(), false);
			XMLCustomWriter::createChildWithText($doc, $userNode, 'mailing_address', $user->getMailingAddress(), false);
			XMLCustomWriter::createChildWithText($doc, $userNode, 'country', $user->getCountry(), false);
			if (is_array($user->getAffiliation(null))) {
				foreach($user->getAffiliation(null) as $locale => $value) {
					$affiliationNode =& XMLCustomWriter::createChildWithText($doc, $userNode, 'affiliation', $value, false);
					if ($affiliationNode) {
						XMLCustomWriter::setAttribute($affiliationNode, 'locale', $locale);
					}
					unset($affiliationNode);
				}
			}
			if (is_array($user->getSignature(null))) {
				foreach($user->getSignature(null) as $locale => $value) {
					$signatureNode =& XMLCustomWriter::createChildWithText($doc, $userNode, 'signature', $value, false);
					if ($signatureNode) {
						XMLCustomWriter::setAttribute($signatureNode, 'locale', $locale);
					}
					unset($signatureNode);
				}
			}
			import('lib.pkp.classes.user.InterestManager');
			$interestManager = new InterestManager();
			$interests = $interestManager->getInterestsForUser($user);
			if (is_array($interests)) {
				foreach ($interests as $interest) {
					XMLCustomWriter::createChildWithText($doc, $userNode, 'interests', $interest, false);
				}
			}
			if (is_array($user->getGossip(null))) {
				foreach($user->getGossip(null) as $locale => $value) {
					$gossipNode =& XMLCustomWriter::createChildWithText($doc, $userNode, 'gossip', $value, false);
					if ($gossipNode) {
						XMLCustomWriter::setAttribute($gossipNode, 'locale', $locale);
					}
					unset($gossipNode);
				}
			}
			if (is_array($user->getBiography(null))) {
				foreach($user->getBiography(null) as $locale => $value) {
					$biographyNode =& XMLCustomWriter::createChildWithText($doc, $userNode, 'biography', $value, false);
					if ($biographyNode) {
						XMLCustomWriter::setAttribute($biographyNode, 'locale', $locale);
					}
					unset($biographyNode);
				}
			}
			XMLCustomWriter::createChildWithText($doc, $userNode, 'locales', join(':', $user->getLocales()), false);
			$roles =& $roleDao->getRolesByUserId($user->getId(), $journal->getId());
			foreach ($roles as $role) {
				$rolePath = $role->getRolePath();
				if ($allowedRoles !== null && !in_array($rolePath, $allowedRoles)) {
					continue;
				}
				$roleNode =& XMLCustomWriter::createElement($doc, 'role');
				XMLCustomWriter::setAttribute($roleNode, 'type', $rolePath);
				XMLCustomWriter::appendChild($userNode, $roleNode);
				unset($roleNode);
			}

			XMLCustomWriter::appendChild($root, $userNode);
		}

		XMLCustomWriter::appendChild($doc, $root);

		return $doc;
	}
}

?>
