<?php
/**
 * @package     Tjnotifications
 * @subpackage  com_tjnotifications
 *
 * @copyright   Copyright (C) 2009 - 2020 Techjoomla. All rights reserved.
 * @license     http:/www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Table\Table;

/**
 * Table class for subscription
 *
 * @package  Tjnotifications
 *
 * @since    2.0.0
 */
class TjnotificationsTableSubscription extends Table
{
	/**
	 * Constructor
	 *
	 * @param   \JDatabaseDriver  &$db  \JDatabaseDriver object.
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__tjnotifications_subscriptions', 'id', $db);
		$this->setColumnAlias('published', 'state');
	}
}
