<?php

	/**
  * @version    SVN: <svn_id>
  * @package    PIP
  * @copyright  Copyright (C) 2005 - 2014. All rights reserved.
  * @license    GNU General Public License version 2 or later; see LICENSE.txt
  * This script updates the masterlist terms into solr autosuggest core
  * Based on the config set.
  * is derivative of works licensed under the GNU General Public License or
  * other free or open source software licenses.
  */

define('_JEXEC', 1);
define('JPATH_BASE', realpath(dirname(__FILE__) . '/..'));

require_once JPATH_BASE . '/includes/defines.php';
require_once JPATH_BASE . '/includes/framework.php';
require_once JPATH_BASE . '/libraries/import.php';
require_once JPATH_CONFIGURATION . '/configuration.php';

jimport('joomla.application.cli');

/*
error_reporting(0);
ini_set('display_errors', 'On');
*/

jimport('joomla.filesystem.file');

if (!JComponentHelper::getComponent('com_tjreports', true)->enabled)
{
	return;
}

jimport('joomla.log.log');

/**
 * This is used to send dequeue TJ notifications. Currently, we've written snippet for emails which can be extented
 * to support other notifications.
 *
 * @since  1.0.0
 */
class ProcessNotifications extends JApplicationCli
{
	/**
	 * This gets data from reminder email table & sent emails.
	 *
	 * @return  Nothing
	 *
	 * @since   1.0.0
	 */
	public function execute()
	{
		$db			=	JFactory::getDBO();
		$mainframe	=	JFactory::getApplication('site');
		$query		=	$db->getQuery(true);
		$limit		=	$this->input->get('limit', 20, 'INT');

		$query->select('*');
		$query->from('#__tj_notification_queue');
		$query->where("status = 0");
		$query->setLimit($limit);
		$db->setQuery($query);
		$queueData	=	$db->loadObjectList();

		if (count($queueData) <= 0)
		{
			return $this->out('Notification queue is empty!');
		}

		$from		=	$mainframe->getCfg('mailfrom');
		$fromname	=	$mainframe->getCfg('fromname');
		$sentCount	=	0;

		foreach ($queueData as $data)
		{
			$options	= json_decode($data->options, true);
			$mailer		= JFactory::getMailer();

			if ($options['emailfrom'] != null && $options['emailfromname'] != null)
			{
				$from = array($options['emailfrom'], $options['emailfromname']);
			}
			else
			{
				$config = JFactory::getConfig();
				$from = array($config->get('mailfrom'), $config->get('fromname'));
			}

			$mailer->setSender($from);

			if ($options['emailcc'])
			{
				$mailer->addCC($options['emailcc']);
			}

			if ($options['emailbcc'])
			{
				$mailer->addBcc($options['emailbcc']);
			}

			if ($options['emailreplyto'])
			{
				$mailer->addReplyTo($options['emailreplyto']);
			}

			if ($options['attachment'])
			{
				$mailer->addAttachment($options['attachment']);
			}

			$mailer->addRecipient($data->recipient);

			$mailer->setSubject($data->subject);

			$mailer->setBody($data->body);

			$mailer->isHTML();

			$mailer->Encoding = 'base64';

			if ($mailer->send())
			{
				$myDate			=	JFactory::getDate();
				$currTime		=	$myDate->toSql();
				$data->status	=	1;
				$data->senton	=	$currTime;
				$sentCount++;

				if ($db->updateObject('#__tj_notification_queue', $data, 'queue_id'))
				{
					$this->out('Email sent sucessfully' . $data->recipient);
				}
			}
			else
			{
				JLog::add(JText::_('TJ Notifications - Mail not sent - QueueID ' . $data->queue_id), JLog::WARNING, 'jerror');
			}
		}

		$this->out('No of e-mails sent' . $sentCount);
	}
}

JApplicationCli::getInstance('ProcessNotifications')->execute();
