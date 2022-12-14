<?php

/**
 * @file plugins/importexport/medra/classes/form/MedraSettingsForm.inc.php
 *
 * Copyright (c) 2013-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class MedraSettingsForm
 * @ingroup plugins_importexport_medra_classes_form
 *
 * @brief Form for journal managers to setup the mEDRA plug-in.
 */


if (!class_exists('DOIExportSettingsForm')) { // Bug #7848
	import('plugins.importexport.medra.classes.form.DOIExportSettingsForm');
}

class MedraSettingsForm extends DOIExportSettingsForm {

	//
	// Constructor
	//
	/**
	 * Constructor
	 * @param $plugin MedraExportPlugin
	 * @param $journalId integer
	 */
	function MedraSettingsForm(&$plugin, $journalId) {
		// Configure the object.
		parent::DOIExportSettingsForm($plugin, $journalId);

		// Add form validation checks.
		$this->addCheck(new FormValidator($this, 'registrantName', FORM_VALIDATOR_REQUIRED_VALUE, 'plugins.importexport.medra.settings.form.registrantNameRequired'));
		$this->addCheck(new FormValidator($this, 'fromCompany', FORM_VALIDATOR_REQUIRED_VALUE, 'plugins.importexport.medra.settings.form.fromCompanyRequired'));
		$this->addCheck(new FormValidator($this, 'fromName', FORM_VALIDATOR_REQUIRED_VALUE, 'plugins.importexport.medra.settings.form.fromNameRequired'));
		$this->addCheck(new FormValidatorEmail($this, 'fromEmail', FORM_VALIDATOR_REQUIRED_VALUE, 'plugins.importexport.medra.settings.form.fromEmailRequired'));
		$this->addCheck(new FormValidatorInSet($this, 'exportIssuesAs', FORM_VALIDATOR_REQUIRED_VALUE, 'plugins.importexport.medra.settings.form.exportIssuesAs', array(O4DOI_ISSUE_AS_WORK, O4DOI_ISSUE_AS_MANIFESTATION)));
		$this->addCheck(new FormValidatorInSet($this, 'publicationCountry', FORM_VALIDATOR_REQUIRED_VALUE, 'plugins.importexport.medra.settings.form.publicationCountry', array_keys($this->_getCountries())));
		// The username is used in HTTP basic authentication and according to RFC2617 it therefore may not contain a colon.
		$this->addCheck(new FormValidatorRegExp($this, 'username', FORM_VALIDATOR_OPTIONAL_VALUE, 'plugins.importexport.medra.settings.form.usernameRequired', '/^[^:]+$/'));
		$this->addCheck(new FormValidatorCustom($this, 'username', 'required', 'plugins.importexport.medra.settings.form.usernameRequired', create_function('$username,$form', 'if ($form->getData(\'automaticRegistration\') && empty($username)) { return false; } return true;'), array(&$this)));
		$this->addCheck(new FormValidatorCustom($this, 'password', 'required', 'plugins.importexport.medra.settings.form.passwordRequired', create_function('$password,$form', 'if ($form->getData(\'automaticRegistration\') && empty($password)) { return false; } return true;'), array(&$this)));
	}


	//
	// Implement template methods from Form
	//
	/**
	 * @see Form::display()
	 */
	function display($request) {
		$templateMgr =& TemplateManager::getManager($request);
		$plugin = $this->_plugin;
		$templateMgr->assign('unregisteredURL', $request->url(null, null, 'importexport', array('plugin', $plugin->getName(), 'all')));

		// Issue export options.
		$exportIssueOptions = array(
			O4DOI_ISSUE_AS_WORK => __('plugins.importexport.medra.settings.form.work'),
			O4DOI_ISSUE_AS_MANIFESTATION => __('plugins.importexport.medra.settings.form.manifestation'),
		);
		$templateMgr->assign('exportIssueOptions', $exportIssueOptions);

		// Countries.
		$templateMgr->assign_by_ref('countries', $this->_getCountries());
		parent::display($request);
	}


	//
	// Implement template methods from DOIExportSettingsForm
	//
	/**
	 * @see DOIExportSettingsForm::getFormFields()
	 */
	function getFormFields() {
		return array(
			'registrantName' => 'string',
			'fromCompany' => 'string',
			'fromName' => 'string',
			'fromEmail' => 'string',
			'publicationCountry' => 'string',
			'exportIssuesAs' => 'int',
			'username' => 'string',
			'password' => 'string',
			'automaticRegistration' => 'bool'
		);
	}

	/**
	 * @see DOIExportSettingsForm::isOptional()
	 */
	function isOptional($settingName) {
		return in_array($settingName, array('username', 'password', 'automaticRegistration'));
	}

	//
	// Private helper methods
	//
	/**
	 * Return a list of countries eligible as publication countries.
	 * @return array
	 */
	function &_getCountries() {
		$countryDao =& DAORegistry::getDAO('CountryDAO'); /* @var $countryDao CountryDAO */
		$countries =& $countryDao->getCountries();
		return $countries;
	}
}

?>
