<?php
/**
 * @package     Tjnotifications
 * @subpackage  tjnotifications
 *
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Table\Table;

/**
 * Table class for message
 *
 * @package  Tjnotifications
 *
 * @since    2.1.0
 */
class TjnotificationsTableOnsiteNotification extends Table
{
	/**
	 * Constructor
	 *
	 * @param   \JDatabaseDriver  &$db  \JDatabaseDriver object.
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__tjnotifications_notifications', 'id', $db);
		$this->setColumnAlias('published', 'state');
	}
}
