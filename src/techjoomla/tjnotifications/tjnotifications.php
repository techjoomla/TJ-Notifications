<?php
/**
 * @package     Techjoomla.Libraries
 * @subpackage  Tjnotifications
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2020 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

BaseDatabaseModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjnotifications/models', 'NotificationsModel');
BaseDatabaseModel::addIncludePath(JPATH_SITE . '/components/com_tjnotifications/models', 'NotificationsModel');

// Load language file for lib
$lang = Factory::getLanguage();
$lang->load('lib_techjoomla', JPATH_SITE, '', true);

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
	 * The constructor
	 *
	 * @since  1.0
	 */
	public function __construct()
	{
		// Check for component
		if (!ComponentHelper::getComponent('com_tjnotifications', true)->enabled)
		{
			throw new Exception('Tjnotifications not installed');
		}
	}

	/**
	 * Method to send the form data.
	 *
	 * @param   string      $client        A requird field same as component name.
	 * @param   string      $key           Key is unique in client.
	 * @param   array       $recipients    It's an array of user objects
	 * @param   Object      $replacements  It is a object contains replacement.
	 * @param   JParameter  $options       It is a object contains Jparameters like cc,bcc.
	 *
	 * @return  boolean value.
	 *
	 * @since 1.0
	 */
	public static function send($client, $key, $recipients, $replacements, $options)
	{
		try
		{
			$model = ListModel::getInstance('Notifications', 'TjnotificationsModel', array('ignore_request' => true));

			$addRecipients = array();
			$addRecipients = self::getRecipients($client, $key, $recipients, $options);

			foreach($addRecipients as $userEmailId)
			{
				if (isset($userEmailId))
				{
					// To get User id of user
					$userid =  self::getuserID($userEmailId);
					$user   = JFactory::getUser($userid);

					// To get language from user params which are stored in json
					$json = json_decode($user->params);

					// To get user's specific language template
					$template = $model->getTemplate($client, $key, $json->language);

					//Invoke JMail Class
					$mailer = Factory::getMailer();

					if ($template->email['from_email'] != null && $template->email['from_name'] != null)
					{
						$from = array($template->email['from_email'], $template->email['from_name']);
					}
					// Backward compatibility for TJNotifications versions v1.2.5 or lower
					elseif ($options->get('from') != null && $options->get('fromname') != null)
					{
						$from = array($options->get('from'), $options->get('fromname'));
					}
					else
					{
						$config = Factory::getConfig();
						$from   = array($config->get('mailfrom'), $config->get('fromname'));
					}

					$ccList = array_map('trim', explode(',', $template->email['cc']));

					// Set cc for email
					if ($ccList[0] != null)
					{
						$mailer->addCC($ccList);
					}
					// Backward compatibility for TJNotifications versions v1.2.5 or lower
					elseif ($options->get('cc') != null)
					{
						$mailer->addCC($options->get('cc'));
					}

					$bccList = array_map('trim', explode(',', $template->email['bcc']));

					// Set bcc for email
					if ($bccList[0] != null)
					{
						$mailer->addBCC($bccList);
					}

					// Backward compatibility for TJNotifications versions v1.2.5 or lower
					elseif ($options->get('bcc') != null)
					{
						$mailer->addBCC($options->get('bcc'));
					}

					if ($options->get('replyTo') != null)
					{
						$mailer->addReplyTo($options->get('replyTo'));
					}

					if ($options->get('attachment') != null)
					{
						if ($options->get('attachmentName') != null)
						{
							$mailer->addAttachment($options->get('attachment'), $options->get('attachmentName'));
						}
						else
						{
							$mailer->addAttachment($options->get('attachment'));
						}
					}

					// If you would like to send String Attachment in email
					if ($options->get('stringAttachment') != null)
					{
						$stringAttachment = array();
						$stringAttachment = $options->get('stringAttachment');
						$encoding         = isset($stringAttachment['encoding']) ? $stringAttachment['encoding'] : '';
						$type             = isset($stringAttachment['type']) ? $stringAttachment['type'] : '';

						if (isset($stringAttachment['content']) && isset($stringAttachment['name']))
						{
							$mailer->addStringAttachment(
												$stringAttachment['content'],
												$stringAttachment['name'],
												$encoding,
												$type
										);
						}
					}

					// If you would like to send as HTML, include this line; otherwise, leave it out
					if (($options->get('isNotHTML')) != 1)
					{
						$mailer->isHTML();
					}

					// Set sender array so that my name will show up neatly in your inbox
					$mailer->setSender($from);

					// Add a recipient -- this can be a single address (string) or an array of addresses
					$mailer->addRecipient($userEmailId);

					// Set subject for email
					$mailer->setSubject(self::getSubject($template->email['subject'], $options));

					// Set body for email
					$mailer->setBody(self::getBody($template->email['body'], $replacements));

					// Send once you have set all of your options
					if ($template->email['state'] == 1)
					{
						$status = $mailer->send();

						if ($status)
						{
							$return['success'] = 1;
							$return['message'] = Text::_('LIB_TECHJOOMLA_TJNOTIFICATION_EMAIL_SEND_SUCCESSFULLY');
						}
						else
						{
							throw new Exception(Text::_('LIB_TECHJOOMLA_TJNOTIFICATION_EMAIL_SEND_FAILED'));
						}
					}
				}
				else
				{
					throw new Exception(Text::_('LIB_TECHJOOMLA_TJNOTIFICATION_ADD_RECIPIENTS_OR_CHECK_PREFERENCES'));
				}
			}

			return $return;
		}
		catch (Exception $e)
		{
			$return['success'] = 0;
			$return['message'] = $e->getMessage();

			return $return;
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
	public static function getRecipients($client, $key, $recipients, $options)
	{
		$model = ListModel::getInstance('Preferences', 'TjnotificationsModel', array('ignore_request' => true));
		$unsubscribed_users = $model->getUnsubscribedUsers($client, $key);

		$addRecipients = array();

		if (!empty($recipients))
		{
			foreach ($recipients as $recipient)
			{
				// User is not in $unsubscribed_users array.
				if (!in_array($recipient->id, $unsubscribed_users))
				{
					// Make an array of recipients.
					$addRecipients[] = $recipient->email;
				}
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

		$replacamentTags = $matches[0];
		$tags            = $matches[1];
		$index           = 0;

		if (isset($replacements))
		{
			foreach ($replacamentTags as $ind => $replacamentTag)
			{
				// Explode e.g doner.name with "." so $data[0]=doner and $data[1]=name
				$data = explode(".", $tags[$ind]);

				if (isset($data))
				{
					$key   = $data[0];
					$value = $data[1];

					if (!empty($replacements->$key->$value) || $replacements->$key->$value == 0)
					{
						$replaceWith = $replacements->$key->$value;
					}
					else
					{
						$replaceWith = "";
					}

					if (isset($replaceWith))
					{
						$body_template = str_replace($replacamentTag, $replaceWith, $body_template);
						$index++;
					}
				}
			}
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
	public static function getSubject($subject_template, $options)
	{
		$matches = self::getTags($subject_template);
		$tags    = $matches[0];
		$index   = 0;

		foreach ($tags as $tag)
		{
			// Explode e.g doner.name with "." so $data[0]=doner and $data[1]=name
			$data  = explode(".", $matches[1][$index]);
			$key   = $data[0];
			$value = $data[1];
			$replaceWith      = $options->get($key)->$value;
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
	 * Function to find the user id based on the emails in the mail object
	 *
	 * @param   string  $to  string of email addresses
	 *
	 * @return  integer  Integer or null
	 */
	public static function getuserID($to)
	{
		$email = $to;

		if (!empty($email))
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('id');
			$query->from($db->quoteName('#__users'));
			$query->where($db->quoteName('email') . " = '" . $email."'");

			$db->setQuery($query);
			$result = $db->loadResult();

			if ($result)
			{
				return $result;
			}
			else
			{
				return null;
			}
		}
		else
		{
			return null;
		}
	}
}
