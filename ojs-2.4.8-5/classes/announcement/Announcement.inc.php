<?php

/**
 * @defgroup announcement
 */

/**
 * @file classes/announcement/Announcement.inc.php
 *
 * Copyright (c) 2013-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class Announcement
 * @ingroup announcement
 * @see AnnouncementDAO
 *
 * @brief Basic class describing a announcement.
 *
 */

import('lib.pkp.classes.announcement.PKPAnnouncement');

class Announcement extends PKPAnnouncement {
	/**
	 * Constructor
	 */
	function Announcement() {
		parent::PKPAnnouncement();
	}
}

?>
