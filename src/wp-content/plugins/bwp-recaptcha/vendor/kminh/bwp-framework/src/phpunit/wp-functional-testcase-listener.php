<?php

/**
 * Copyright (c) 2015 Khang Minh <contact@betterwp.net>
 * @license http://www.gnu.org/licenses/gpl.html GNU GENERAL PUBLIC LICENSE VERSION 3.0 OR LATER
 */

/**
 * This listener should be used when a functional testcase needs to be tested
 * in separate processes
 *
 * @author Khang Minh <contact@betterwp.net>
 */
class BWP_Framework_PHPUnit_WP_Functional_TestListener extends PHPUnit_Framework_BaseTestListener
{
	public function startTestSuite(PHPUnit_Framework_TestSuite $suite)
	{
		if (!$this->isClassTestSuite($suite)) {
			return;
		}

		// only showing testsuite's name for functional tests
		if (stripos($suite->getName(), 'functional') === false) {
			return;
		}

		printf("\n" . 'Running TestSuite: %s', $suite->getName());
	}

	public function endTestSuite(PHPUnit_Framework_TestSuite $suite)
	{
		global $_tests_dir;

		// we only care about suite name that is an actual test class
		if (!$this->isClassTestSuite($suite)) {
			return;
		}

		// remove installed.lock file so next tests can install WP if needed
		if (file_exists($_tests_dir . '/installed.lock')) {
			unlink($_tests_dir . '/installed.lock');
		}
	}

	protected function isClassTestSuite(PHPUnit_Framework_TestSuite $suite)
	{
		$suite_name = $suite->getName();

		if (!empty($suite_name) && class_exists($suite_name, false)) {
			return true;
		}

		return false;
	}
}
