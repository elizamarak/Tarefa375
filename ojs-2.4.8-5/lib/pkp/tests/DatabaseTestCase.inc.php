<?php

/**
 * @file tests/DatabaseTestCase.inc.php
 *
 * Copyright (c) 2013-2019 Simon Fraser University
 * Copyright (c) 2000-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class DatabaseTestCase
 * @ingroup tests
 *
 * @brief Base class for unit tests that require database support.
 *        The schema TestName.setUp.xml will be installed before each
 *        individual test case (if present). The schema TestName.tearDown.xml may
 *        be used to clean up after each test case.
 */


import('lib.pkp.tests.PKPTestCase');
import('lib.pkp.tests.PKPTestHelper');

abstract class DatabaseTestCase extends PKPTestCase {

	/**
	 * Override this method if you want to backup/restore
	 * tables before/after the test.
	 * @return array A list of tables to backup and restore.
	 */
	protected function getAffectedTables() {
		return array();
	}

	/**
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp() {
		// Backup affected tables.
		$affectedTables = $this->getAffectedTables();
		if (is_array($affectedTables)) {
			PKPTestHelper::backupTables($affectedTables, $this);
		}
		parent::setUp();
	}

	/**
	 * @see PHPUnit_Framework_TestCase::tearDown()
	 */
	protected function tearDown() {
		parent::tearDown();
		$affectedTables = $this->getAffectedTables();
		if (is_array($affectedTables)) {
			PKPTestHelper::restoreTables($this->getAffectedTables(), $this);
		} elseif ($affectedTables === PKP_TEST_ENTIRE_DB) {
			PKPTestHelper::restoreDB($this);
		}
	}
}
?>
