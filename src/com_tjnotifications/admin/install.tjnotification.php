<?php
/**
 * @package    Com_Tjnotification
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die(';)');
jimport('joomla.installer.installer');
jimport('joomla.filesystem.file');

/**
 * Script file of TJNotification component
 *
 * @since  1.0.0
 **/
class Com_TjnotificationsInstallerScript
{
	/**
	 * method to install the component
	 *
	 * @param   JInstaller  $parent  parent
	 *
	 * @return void
	 */
	public function install($parent)
	{
	}

	/**
	 * method to update the component
	 *
	 * @param   JInstaller  $parent  parent
	 *
	 * @return void
	 */
	public function update($parent)
	{
		// Install SQL FIles
		$this->installSqlFiles($parent);
		$this->fix_db_on_update();
	}

	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @param   JInstaller  $type    type
	 * @param   JInstaller  $parent  parent
	 *
	 * @return void
	 */
	public function preflight($type, $parent)
	{
	}

	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @param   JInstaller  $type    type
	 * @param   JInstaller  $parent  parent
	 *
	 * @return void
	 */
	public function postflight($type, $parent)
	{
		// Install SQL FIles
		$this->installSqlFiles($parent);
	}

	/**
	 * installSqlFiles
	 *
	 * @param   JInstaller  $parent  parent
	 *
	 * @return  void
	 */
	public function installSqlFiles($parent)
	{
		$db = JFactory::getDbo();

		// Obviously you may have to change the path and name if your installation SQL file
		if (method_exists($parent, 'extension_root'))
		{
			$sqlfile = $parent->getPath('extension_root') . '/admin/sql/install.mysql.utf8.sql';
		}
		else
		{
			$sqlfile = $parent->getParent()->getPath('extension_root') . '/sql/install.mysql.utf8.sql';
		}

		// Don't modify below this line
		$buffer = file_get_contents($sqlfile);

		if ($buffer !== false)
		{
			jimport('joomla.installer.helper');
			$queries = JInstallerHelper::splitSql($buffer);

			if (count($queries) != 0)
			{
				foreach ($queries as $query)
				{
					$query = trim($query);

					if ($query != '' && $query{0} != '#')
					{
						$db->setQuery($query);

						if (!$db->execute())
						{
							JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)));

							return false;
						}
					}
				}
			}
		}

		$config = JFactory::getConfig();
		$configdb = $config->get('db');

		// Get dbprefix
		$dbprefix = $config->get('dbprefix');
	}

	/**
	 * Fix template table
	 *
	 * @param   int  $db        db.
	 * @param   int  $dbprefix  dbprefix
	 * @param   int  $config    config
	 *
	 * @return  void
	 *
	 * @Since tjnotification version 1.0
	 */
	public function fixTemplateTable($db, $dbprefix, $config)
	{
		$query = "SHOW COLUMNS FROM #__tj_notification_templates WHERE `Field` = 'title'";
		$db->setQuery($query);
		$check = $db->loadResult();

		if (!$check)
		{
			$query = " ALTER TABLE  `#__tj_notification_templates` ADD  `title` varchar(100)	AFTER  `is_override`";
			$db->setQuery($query);

			if (!$db->execute())
			{
				JError::raiseError(500, $db->stderr());
			}
		}

		$query = "SHOW COLUMNS FROM #__tj_notification_templates WHERE `Field` = 'user_control'";
		$db->setQuery($query);
		$check = $db->loadResult();

		if (!$check)
		{
			$query = " ALTER TABLE  `#__tj_notification_templates` ADD  `user_control` int(1)	AFTER  `is_override`";
			$db->setQuery($query);

			if (!$db->execute())
			{
				JError::raiseError(500, $db->stderr());
			}
		}

		$query = "SHOW COLUMNS FROM #__tj_notification_templates WHERE `Field` = 'core'";
		$db->setQuery($query);
		$check = $db->loadResult();

		if (!$check)
		{
			$query = " ALTER TABLE  `#__tj_notification_templates` ADD  `core` tinyint(3)	AFTER  `user_control`";
			$db->setQuery($query);

			if (!$db->execute())
			{
				JError::raiseError(500, $db->stderr());
			}
		}

		$query = "SHOW COLUMNS FROM #__tj_notification_templates WHERE `Field` = 'replacement_tags'";
		$db->setQuery($query);
		$check = $db->loadResult();

		if (!$check)
		{
			$query = " ALTER TABLE  `#__tj_notification_templates` ADD  `replacement_tags` text	AFTER  `core`";
			$db->setQuery($query);

			if (!$db->execute())
			{
				JError::raiseError(500, $db->stderr());
			}
		}
	}

	/**
	 * Fix database on update
	 *
	 * @return  void
	 *
	 * @Since tjnotification version 1.0
	 */
	public function fix_db_on_update()
	{
		$db = JFactory::getDbo();
		$config = JFactory::getConfig();
		$dbprefix = $config->get('dbprefix');

		$xml = JFactory::getXML(JPATH_ADMINISTRATOR . '/components/com_tjnotifications/tjnotifications.xml');
		$version = (string) $xml->version;
		$this->version = (float) ($version);
		$this->fixTemplateTable($db, $dbprefix, $config);
	}
}
