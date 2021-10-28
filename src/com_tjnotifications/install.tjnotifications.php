<?php
/**
 * @package     TJNotifications
 * @subpackage  com_tjnotifications
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;

use \Joomla\CMS\Factory;
use \Joomla\CMS\Object\CMSObject;
use \Joomla\CMS\Installer\InstallerHelper;
use Joomla\CMS\Table\Table;

jimport('joomla.installer.installer');
jimport('joomla.filesystem.file');

/**
 * Script file of TJNotification component
 *
 * @since  1.0.0
 **/
class Com_TjnotificationsInstallerScript
{
	/** @var array The list of extra modules and plugins to install */
	private $queue = array(
		// Plugins => { (folder) => { (element) => (published) }* }*
		'plugins' => array(
			'actionlog' => array(
				'tjnotification' => 1
			),
			'api' => array(
				'tjnotifications' => 0
			),
			'privacy' => array(
				'tjnotification' => 1,
			),
			'user' => array(
				'tjnotificationsmobilenumber' => 1
			)
		),
	);

	/** @var array Obsolete files and folders to remove*/
	private $removeFilesAndFolders = array(
		'files' => array(
		),
		'folders' => array(
		)
	);

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
 	* This method is called after a component is uninstalled.
 	*
 	* @param   \stdClass  $parent  Parent object calling this method.
 	*
 	* @return void
 	*/
	public function uninstall($parent)
	{
		jimport('joomla.installer.installer');
		$db              = Factory::getDBO();
		$status          = new CMSObject;
		$status->plugins = array();
		$src             = $parent->getParent()->getPath('source');

		// Plugins uninstallation
		if (count($this->queue['plugins']))
		{
			foreach ($this->queue['plugins'] as $folder => $plugins)
			{
				if (count($plugins))
				{
					foreach ($plugins as $plugin => $published)
					{
						$sql = $db->getQuery(true)->select($db->qn('extension_id'))
						->from($db->qn('#__extensions'))
						->where($db->qn('type') . ' = ' . $db->q('plugin'))
						->where($db->qn('element') . ' = ' . $db->q($plugin))
						->where($db->qn('folder') . ' = ' . $db->q($folder));
						$db->setQuery($sql);

						$id = $db->loadResult();

						if ($id)
						{
							$installer         = new Installer;
							$result            = $installer->uninstall('plugin', $id);
							$status->plugins[] = array(
								'name' => 'plg_' . $plugin,
								'group' => $folder,
								'result' => $result
							);
						}
					}
				}
			}
		}

		return $status;
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
		$this->fixMenuLinks();
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
		$src             = $parent->getParent()->getPath('source');
		$db              = Factory::getDbo();
		$status          = new CMSObject;
		$status->plugins = array();

		// Plugins installation
		if (count($this->queue['plugins']))
		{
			foreach ($this->queue['plugins'] as $folder => $plugins)
			{
				if (count($plugins))
				{
					foreach ($plugins as $plugin => $published)
					{
						$path = "$src/plugins/$folder/$plugin";

						if (!is_dir($path))
						{
							$path = "$src/plugins/$folder/plg_$plugin";
						}

						if (!is_dir($path))
						{
							$path = "$src/plugins/$plugin";
						}

						if (!is_dir($path))
						{
							$path = "$src/plugins/plg_$plugin";
						}

						if (!is_dir($path))
						{
							continue;
						}

						// Was the plugin already installed?
						$query = $db->getQuery(true)
							->select('COUNT(*)')
							->from($db->qn('#__extensions'))
							->where($db->qn('element') . ' = ' . $db->q($plugin))
							->where($db->qn('folder') . ' = ' . $db->q($folder));
						$db->setQuery($query);
						$count = $db->loadResult();

						$installer = new Installer;
						$result = $installer->install($path);

						$status->plugins[] = array('name' => 'plg_' . $plugin, 'group' => $folder, 'result' => $result);

						if ($published && !$count)
						{
							$query = $db->getQuery(true)
								->update($db->qn('#__extensions'))
								->set($db->qn('enabled') . ' = ' . $db->q('1'))
								->where($db->qn('element') . ' = ' . $db->q($plugin))
								->where($db->qn('folder') . ' = ' . $db->q($folder));
							$db->setQuery($query);
							$db->execute();
						}
					}
				}
			}
		}

		// Install SQL FIles
		$this->installSqlFiles($parent);

		$this->removeObsoleteFilesAndFolders($this->removeFilesAndFolders);

		$this->migrateTemplates();
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
		$db = Factory::getDbo();

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
			$queries = InstallerHelper::splitSql($buffer);

			if (count($queries) != 0)
			{
				foreach ($queries as $query)
				{
					$query = trim($query);

					if (!empty($query))
					{
						$db->setQuery($query);

						if (!$db->execute())
						{
							JError::raiseWarning(1, Text::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)));

							return false;
						}
					}
				}
			}
		}

		$config = Factory::getConfig();
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
		$db       = Factory::getDbo();
		$config   = Factory::getConfig();
		$dbprefix = $config->get('dbprefix');

		$this->fixTemplateTable($db, $dbprefix, $config);
	}

	/**
	 * Fix Duplicate menu created for Notification
	 *
	 * @return  void
	 *
	 * @Since 1.1
	 */
	public function fixMenuLinks()
	{
		$db       = Factory::getDbo();
		$link     = 'index.php?option=com_tjnotifications&view=notifications&extension=com_jticketing';
		$link1    = 'index.php?option=com_tjnotifications&extension=com_tjvendors';
		$allLinks = '"' . $link . '","' . $link1 . '"';

		// Delete the mainmenu from menu table
		$deleteMenu = $db->getQuery(true);
		$deleteMenu->delete($db->quoteName('#__menu'));
		$deleteMenu->where($db->quoteName('link') . 'IN (' . $allLinks . ')');
		$deleteMenu->where($db->quoteName('level') . " = 1");
		$db->setQuery($deleteMenu);
		$db->execute();
	}

	/**
	 * Removes obsolete files and folders
	 *
	 * @param   array  $removeFilesAndFolders  Array of the files and folders to be removed
	 *
	 * @return  void
	 *
	 * @since  1.0.3
	 */
	private function removeObsoleteFilesAndFolders($removeFilesAndFolders)
	{
		// Remove files
		if (!empty($removeFilesAndFolders['files']))
		{
			foreach ($removeFilesAndFolders['files'] as $file)
			{
				$f = JPATH_ROOT . '/' . $file;

				if (File::exists($f))
				{
					File::delete($f);
				}
			}
		}

		// Remove folders
		if (!empty($removeFilesAndFolders['folders']))
		{
			foreach ($removeFilesAndFolders['folders'] as $folder)
			{
				$f = JPATH_ROOT . '/' . $folder;

				if (Folder::exists($f))
				{
					Folder::delete($f);
				}
			}
		}
	}

	/**
 	* This method is called after a component is installed for template migration
 	*
 	* @return void
 	*/
	public function migrateTemplates()
	{
		$limit  = 200;
		$db     = Factory::getDbo();

		try
		{
			$query = $db->getQuery(true)
				->select('*')
				->from('#__tj_notification_template_configs');
			$db->setQuery($query);
			$templateConfigs = $db->loadObjectList();

			if (!empty($templateConfigs))
			{
				return false;
			}

			$query = $db->getQuery(true)
				->select('*')
				->from('#__tj_notification_templates')
				->order($db->quoteName('id') . ' ASC');
			$db->setQuery($query, 0, $limit);
			$rows = $db->loadObjectList();

			foreach ($rows as $row)
			{
				$db    = Factory::getDBO();

				if (empty($row->id))
				{
					return false;
				}

				Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjnotifications/tables');
				$templateConfigTable = Table::getInstance('Template', 'TjnotificationTable', array('dbo', $db));

				$templateConfigTable->load(array('template_id' => $row->id));

				$templateConfigTable->template_id = $row->id;
				$templateConfigTable->backend     = "email";
				$templateConfigTable->subject     = $row->email_subject;
				$templateConfigTable->body        = $row->email_body;
				$templateConfigTable->state       = $row->email_status;
				$templateConfigTable->created_on  = $row->created_on;
				$templateConfigTable->updated_on  = $row->updated_on;
				$templateConfigTable->is_override = $row->is_override;

				$templateConfigTable->save($templateConfigTable);
			}

			$query = "ALTER TABLE `#__tj_notification_templates` DROP `email_status`, DROP `sms_status`, DROP `push_status`, DROP `web_status`,
			DROP `email_body`, DROP `sms_body`, DROP `push_body`, DROP `web_body`, DROP `email_subject`, DROP `sms_subject`, DROP `push_subject`,
			DROP `web_subject`, DROP `is_override`";
			$db->setQuery($query);
			$db->execute();
		}
		catch (Exception $e)
		{
			return $e->getMessage();
		}
	}
}
