<?php

/**
 * @file plugins/oaiMetadataFormats/nlm/index.php
 *
 * Copyright (c) 2013-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @ingroup oai_format_nlm
 * @brief Wrapper for the OAI NLM format plugin.
 *
 */

require_once('OAIMetadataFormatPlugin_NLM.inc.php');
require_once('OAIMetadataFormat_NLM.inc.php');

return new OAIMetadataFormatPlugin_NLM();

?>
