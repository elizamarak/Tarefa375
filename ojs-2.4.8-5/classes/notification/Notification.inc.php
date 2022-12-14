<?php

/**
 * @file classes/notification/Notification.inc.php
 *
 * Copyright (c) 2013-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class OJSNotification
 * @ingroup notification
 * @see NotificationDAO
 * @brief OJS subclass for Notifications (defines OJS-specific types).
 */

/** Notification associative types. */
define('NOTIFICATION_TYPE_ARTICLE_SUBMITTED', 		0x1000001);
define('NOTIFICATION_TYPE_METADATA_MODIFIED', 		0x1000002);
define('NOTIFICATION_TYPE_SUPP_FILE_MODIFIED', 		0x1000004);
define('NOTIFICATION_TYPE_GALLEY_MODIFIED', 		0x1000006);
define('NOTIFICATION_TYPE_SUBMISSION_COMMENT', 		0x1000007);
define('NOTIFICATION_TYPE_LAYOUT_COMMENT', 			0x1000008);
define('NOTIFICATION_TYPE_COPYEDIT_COMMENT', 		0x1000009);
define('NOTIFICATION_TYPE_PROOFREAD_COMMENT', 		0x1000010);
define('NOTIFICATION_TYPE_REVIEWER_COMMENT', 		0x1000011);
define('NOTIFICATION_TYPE_REVIEWER_FORM_COMMENT', 	0x1000012);
define('NOTIFICATION_TYPE_EDITOR_DECISION_COMMENT', 0x1000013);
define('NOTIFICATION_TYPE_USER_COMMENT', 			0x10000014);
define('NOTIFICATION_TYPE_PUBLISHED_ISSUE', 		0x10000015);

// OJS-specific trivial notifications
define('NOTIFICATION_TYPE_GIFT_REDEEM_STATUS_SUCCESS',							0x2000001);
define('NOTIFICATION_TYPE_GIFT_REDEEM_STATUS_ERROR_NO_GIFT_TO_REDEEM',			0x2000002);
define('NOTIFICATION_TYPE_GIFT_REDEEM_STATUS_ERROR_GIFT_ALREADY_REDEEMED',		0x2000003);
define('NOTIFICATION_TYPE_GIFT_REDEEM_STATUS_ERROR_GIFT_INVALID',				0x2000004);
define('NOTIFICATION_TYPE_GIFT_REDEEM_STATUS_ERROR_SUBSCRIPTION_TYPE_INVALID',	0x2000005);
define('NOTIFICATION_TYPE_GIFT_REDEEM_STATUS_ERROR_SUBSCRIPTION_NON_EXPIRING',	0x2000006);

define('NOTIFICATION_TYPE_BOOK_REQUESTED',				0x3000001);
define('NOTIFICATION_TYPE_BOOK_CREATED',				0x3000002);
define('NOTIFICATION_TYPE_BOOK_UPDATED',				0x3000003);
define('NOTIFICATION_TYPE_BOOK_DELETED',				0x3000004);
define('NOTIFICATION_TYPE_BOOK_MAILED',					0x3000005);
define('NOTIFICATION_TYPE_BOOK_SETTINGS_SAVED',			0x3000006);
define('NOTIFICATION_TYPE_BOOK_SUBMISSION_ASSIGNED',	0x3000007);
define('NOTIFICATION_TYPE_BOOK_AUTHOR_ASSIGNED',		0x3000008);
define('NOTIFICATION_TYPE_BOOK_AUTHOR_DENIED',			0x3000009);
define('NOTIFICATION_TYPE_BOOK_AUTHOR_REMOVED',			0x300000A);

import('lib.pkp.classes.notification.PKPNotification');
import('lib.pkp.classes.notification.NotificationDAO');

class Notification extends PKPNotification {

	/**
	 * Constructor.
	 */
	function Notification() {
		parent::PKPNotification();
	}
}

?>
