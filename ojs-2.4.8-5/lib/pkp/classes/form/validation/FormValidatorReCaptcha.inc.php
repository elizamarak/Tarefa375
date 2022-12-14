<?php

/**
 * @file classes/form/validation/FormValidatorReCaptcha.inc.php
 *
 * Copyright (c) 2013-2019 Simon Fraser University
 * Copyright (c) 2000-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class FormValidatorReCaptcha
 * @ingroup form_validation
 *
 * @brief Form validation check reCaptcha values.
 */

class FormValidatorReCaptcha extends FormValidator {
	/** @var string reCaptcha challenge form field */
	var $_challengeField;

	/** @var string reCaptcha response form field */
	var $_responseField;

	/** @var string */
	var $_userIp;

	/** @var string hostname to enforce on response */
	var $_hostEnforced;

	/**
	 * Constructor.
	 * @param $form object
	 * @param $userIp string IP address of user request
	 * @param $message string Key of message to display on mismatch
	 * @param $host string A hostname to enforce
	 */
	function FormValidatorReCaptcha(&$form, $challengeField, $responseField, $userIp, $message, $host = '') {
		parent::FormValidator($form, $challengeField, FORM_VALIDATOR_REQUIRED_VALUE, $message);
		$this->_challengeField = $challengeField;
		$this->_responseField = $responseField;
		$this->_userIp = $userIp;
		$this->_hostEnforced = $host;
	}


	//
	// Public methods
	//
	/**
	 * @see FormValidator::isValid()
	 * Determine whether or not the form meets this ReCaptcha constraint.
	 * @return boolean
	 */
	function isValid() {
		import('lib.pkp.lib.recaptcha.recaptchalib');
		$privateKey = Config::getVar('captcha', 'recaptcha_private_key');
		$reCaptchaVersion = intval(Config::getVar('captcha', 'recaptcha_version', RECAPTCHA_VERSION_LEGACY));
		$form =& $this->getForm();
		$challengeField = $form->getData($this->_challengeField);
		$responseField = $form->getData($this->_responseField);

		$checkResponse = recaptcha_versioned_check_answer (
			$reCaptchaVersion,
			$this->_hostEnforced,
			$privateKey,
			$this->_userIp,
			$challengeField,
			$responseField
		);

		if ($checkResponse && $checkResponse->is_valid) {
			return true;
		} else {
			return false;
		}
	}
}

?>
