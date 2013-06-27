<?php

/**
 * @name      ElkArte Forum
 * @copyright ElkArte Forum contributors
 * @license   BSD http://opensource.org/licenses/BSD-3-Clause
 *
 * @version 1.0 Alpha
 *
 * This file is holds low-level database work used by the Stats.
 * Some functions/queries (or all :P) might be duplicate, along Elk.
 * They'll be here to avoid including many files in action_stats, and
 * perhaps for use of add-ons in a similar way they were using some
 * SSI functions.
 * The purpose of this file is experimental and might be deprecated in
 * favor of a better solution.
 *
 */

if (!defined('ELK'))
	die('No access...');

/**
 *
 * Return the number of currently online members.
 */
function onlineCount()
{
	$db = database();

	$result = $db->query('', '
		SELECT COUNT(*)
		FROM {db_prefix}log_online',
		array(
		)
	);
	list ($users_online) = $db->fetch_row($result);
	$db->free_result($result);

	return $users_online;
}