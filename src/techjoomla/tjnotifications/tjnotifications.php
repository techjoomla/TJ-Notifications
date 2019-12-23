<?php
/**
 * @package    Techjoomla.Libraries
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjnotifications/models', 'NotificationsModel');
JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_tjnotifications/models', 'NotificationsModel');

use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;

/**
 * Tjnotifications
 *
 * @package     Techjoomla.Libraries
 * @subpackage  Tjnotifications
 * @since       1.0
 */
class Tjnotifications
{
	/**
	 * Method to send the form data.
	 *
	 * @param   string      $client        A requird field same as component name.
	 * @param   string      $key           Key is unique in client.
	 * @param   array       $recipients    It's an array of user objects
	 * @param   array       $replacements  It is a object contains replacement.
	 * @param   JParameter  $options       It is a object contains Jparameters like cc,bcc.
	 *
	 * @return  boolean value.
	 *
	 * @since 1.0
	 */
	public static function send($client, $key, $recipients, $replacements, $options)
	{
		$model = JModelList::getInstance('Notifications', 'TjnotificationsModel', array('ignore_request' => true));

		$template = $model->getTemplate($client, $key);
		$addRecipients = self::getRecipients($client, $key, $recipients, $options);

		if ($addRecipients)
		{
			// Invoke JMail Class
			$mailer = JFactory::getMailer();

			if ($options->get('from') != null && $options->get('fromname') != null)
			{
				$from = array($options->get('from'),$options->get('fromname'));
			}
			else
			{
				$config = JFactory::getConfig();
				$from = array($config->get('mailfrom'), $config->get('fromname'));
			}

			// Set cc for email
			if ($options->get('cc') != null)
			{
				$mailer->addCC($options->get('cc'));
			}

			// Set bcc for email
			if ($options->get('bcc') != null)
			{
				$mailer->addBcc($options->get('bcc'));
			}

			// Set bcc for email
			if ($options->get('replyTo') != null)
			{
				$mailer->addReplyTo($options->get('replyTo'));
			}

			if ($options->get('attachment') != null)
			{
				$mailer->addAttachment($options->get('attachment'));
			}

			// Set sender array so that my name will show up neatly in your inbox
			$mailer->setSender($from);

			// Add a recipient -- this can be a single address (string) or an array of addresses
			$mailer->addRecipient($addRecipients);

			// Set subject for email
			$mailer->setSubject(self::getSubject($template->email_subject, $options));

			// Set body for email
			$mailer->setBody(self::getBody($template->email_body, $replacements));

			// If you would like to send as HTML, include this line; otherwise, leave it out
			$mailer->isHTML();

			// Send once you have set all of your options
			if ($template->email_status)
			{
				$status = $mailer->send();
			}

			if ($status)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}

	/**
	 * Method to get Recipients.
	 *
	 * @param   string      $client      A requird field same as component name.
	 * @param   string      $key         Key is unique in client.
	 * @param   array       $recipients  It's an array of user objects
	 * @param   JParameter  $options     It is a object contains Jparameters like cc,bcc.
	 *
	 * @return  array Reciepients.
	 *
	 * @since 1.0
	 */
	public static function getRecipients($client,$key,$recipients,$options)
	{
		$model = JModelList::getInstance('Preferences', 'TjnotificationsModel', array('ignore_request' => true));
		$unsubscribed_users = $model->getUnsubscribedUsers($client, $key);

		foreach ($recipients as $recipient)
		{
			/* $unsubscribed_users array is not empty.
			 * $recipient->id is not in $unsubscribed_users array.
			 * $recipient->block is empty or not set.
			*/
			if (!empty($unsubscribed_users) && !in_array($recipient->id, $unsubscribed_users) && !($recipient->block))
			{
				// Make an array of recipients.
				$addRecipients[] = $recipient->email;
			}
			/*$recipient->block is empty or not set.*/
			elseif (!isset($recipient->block))
			{
				// Make an array of recipients.
				$addRecipients[] = $recipient->email;
			}
		}

		if ($options->get('guestEmails') != null)
		{
			foreach ($options->get('guestEmails') as $guestEmail)
			{
				$addRecipients[] = $guestEmail;
			}
		}

		return $addRecipients;
	}

	/**
	 * Method to get Body.
	 *
	 * @param   string  $body_template  A template body for email.
	 * @param   array   $replacements   It is a object contains replacement.
	 *
	 * @return  string  $body
	 *
	 * @since 1.0
	 */
	public static function getBody($body_template, $replacements)
	{
		$matches = self::getTags($body_template);

		$tags = $matches[0];

		$index = 0;

		foreach ($tags as $tag)
		{
			// Explode e.g doner.name with "." so $data[0]=doner and $data[1]=name
			$data = explode(".", $matches[1][$index]);
			$key = $data[0];
			$value = $data[1];
			$replaceWith = $replacements->$key->$value;
			$body_template = str_replace($tag, $replaceWith, $body_template);
			$index++;
		}

		return $body_template;
	}

	/**
	 * Method to get Subject.
	 *
	 * @param   string  $subject_template  A template body for email.
	 * @param   array   $options           It is a object contains replacement.
	 *
	 * @return  string  $subject
	 *
	 * @since 1.0
	 */
	public static function getSubject($subject_template,$options)
	{
			$matches = self::getTags($subject_template);
			$tags = $matches[0];
			$index = 0;

		foreach ($tags as $tag)
		{
			// Explode e.g doner.name with "." so $data[0]=doner and $data[1]=name
			$data = explode(".", $matches[1][$index]);
			$key = $data[0];
			$value = $data[1];
			$replaceWith = $options->get($key)->$value;
			$subject_template = str_replace($tag, $replaceWith, $subject_template);
			$index++;
		}

		return $subject_template;
	}

	/**
	 * Method to get Tags.
	 *
	 * @param   string  $data_template  A template.
	 *
	 * @return  array   $matches
	 *
	 * @since 1.0
	 */
	public static function getTags($data_template)
	{
		//  Pattern for {text};
			$pattern = "/{([^}]*)}/";

			preg_match_all($pattern, $data_template, $matches);

		//  $matches[0]= stores tag like {doner.name} and $matches[1] stores doner.name. Explode it and make it doner->name
			return $matches;
	}

	/**
	 * Method to get Sample data for email template.
	 *
	 * @return  string  $body
	 *
	 * @since 1.0
	 */
	public static function getSampleBodyData()
	{
		$input = Factory::getApplication()->input;
		$id = $input->get('id');

		Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjnotifications/tables');
		$storeTable = Table::getInstance('Notification', 'TjnotificationTable');
		$storeTable->load($id);

		$bodyTemplate = $storeTable->email_body;

		$matches = self::getTags($bodyTemplate);
		$replacamentTags = $matches[1];

		$index = 0;
		$replacementsdata = $storeTable->replacement_tags;
		$replacements = json_decode($replacementsdata);

		foreach ($replacamentTags as $ind => $replacamentTag)
		{
			$replaceWith = '';
			$replacamentTagName = '{' . $replacamentTag . '}';

			foreach ($replacements as $value)
			{
				if ($replacamentTagName == $value->name)
				{
					$replaceWith = !empty($value->sampledata) ? $value->sampledata : " ";
				}

				if (!empty($replaceWith))
				{
					$bodyTemplate = str_replace($replacamentTagName, $replaceWith, $bodyTemplate);
					break;
				}
			}
		}

		return $bodyTemplate;
	}
}
