<?php

/**
 * @defgroup plugins_generic_lucene
 */

/**
 * @file plugins/generic/lucene/index.php
 *
 * Copyright (c) 2013-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @ingroup plugins_generic_lucene
 * @brief Wrapper for Lucene plugin.
 *
 */

require_once('LucenePlugin.inc.php');

return new LucenePlugin();

?>
