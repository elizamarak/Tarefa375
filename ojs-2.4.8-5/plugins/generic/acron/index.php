<?php

/**
 * @defgroup plugins_generic_acron
 */

/**
 * @file plugins/generic/index.php
 *
 * Copyright (c) 2013-2019 Simon Fraser University
 * Copyright (c) 2000-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @brief Wrapper for acron plugin
 *
 * @ingroup plugins_generic_acron
 */

require_once('AcronPlugin.inc.php');

return new AcronPlugin();

?>
