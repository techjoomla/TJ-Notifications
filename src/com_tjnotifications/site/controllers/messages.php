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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

/**
 * Controller for list of messages
 *
 * @package  Tjnotifications
 *
 * @since    2.1.0
 */
class TjnotificationsControllerMessages extends AdminController
{
	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  \JModelLegacy|boolean  Model object on success; otherwise false on failure.
	 *
	 * @since   2.1.0
	 */
	public function &getModel($name = 'Message', $prefix = 'TjnotificationsModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}

	/**
	 * Method to get list of notification messages
	 *
	 * @param   string  $type  Type of messages
	 *
	 * @return  array
	 *
	 * @since   2.1.0
	 */
	protected function getNotifications($type = 'new')
	{
		$result = array();

		$model  = $this->getModel('Messages');
		$jinput = Factory::getApplication()->input;
		$userid = $jinput->get('userid', '', 'int');

		// Return result related to specified activity type
		if (empty($userid))
		{
			$result['success'] = false;
			$result['message'] = Text::_('COM_TJNOTIFICATIONS_ERROR_NO_USERID_PASSED');

			return $result;
		}

		if ($type == 'new')
		{
			// Set model state
			// $model->setState("filter.recepient", $userid);
			$notifications = $model->getUndeliveredNotifications($userid);

			// To mark these as delivered, get keys
			$notificationPks = array();

			foreach ($notifications as $notficationItem)
			{
				$notificationPks[] = $notficationItem->id;
			}

			BaseDatabaseModel::addIncludePath(JPATH_SITE . '/components/com_tjnotifications/models', 'MessageformModel');
			$messageFormModel = BaseDatabaseModel::getInstance('Messageform', 'TjnotificationsModel');

			// Mark these as delivered
			$messageFormModel->updateNotificationStatus('delivered', $notificationPks, 1, $userid);
		}
		elseif ($type == 'all')
		{
			$start = $jinput->get('start', '0');
			$limit = $jinput->get('limit');

			// Set model state
			$model->setState("list.limit", $limit);
			$model->setState("list.start", $start);
			$model->setState("filter.recepient", $userid);
			$notifications = $model->getItems();
		}

		// If no activities found then return the error message
		if (empty($notifications))
		{
			$result['success'] = false;
			$result['message'] = Text::_("COM_TJNOTIFICATIONS_MSG_NO_NOTIFICATIONS_FOUND");
		}
		else
		{
			$result['success']                		= true;
			$result['notifications']          		= $notifications;
			$result['total'] 				  		= $model->getTotal();
			$result['unread_notifications_count'] 	= (int) $model->getUnreadNotificationsCount($userid);
		}

		return $result;
	}

	/**
	 * Method to get list of notification messages
	 *
	 * @return  string
	 *
	 * @since   2.1.0
	 */
	public function getMessages()
	{
		$notifications = $this->getNotifications('all');
		echo json_encode($notifications);
		jexit();
	}

	/**
	 * Method to get list of unread messages as SSE - server sent events
	 * Code based on com_easysocial/polling.php [v4.0]
	 *
	 * @return  string
	 *
	 * @since   2.1.0
	 */
	public function getNewMessagesStream()
	{
		header("Content-Type: text/event-stream");
		header("Cache-Control: no-cache");
		header("X-Accel-Buffering: no");
		header("Access-Control-Allow-Origin: *");

		while (true)
		{
			$notifications = $this->getNotifications('new');

			ob_start();
			?>

<?php
if (is_object($notifications) || is_array($notifications))
{
	?>
data: <?php echo json_encode($notifications); ?>
	<?php
}
else
{
	?>
data: <?php echo $notifications; ?>
	<?php
}
			?>

			<?php // Required to fulfill minimum buffer size on certain server ?>
			buffer: <?php echo str_repeat(' ', 1024 * 64); ?>

			<?php
			$contents = ob_get_contents();
			ob_end_clean();
			echo $contents;
			echo "\n\n";
			@ob_end_flush();
			@flush();

			// If the connection has been closed by the client we better exit the loop
			if (connection_aborted())
			{
				return;
			}

			// Some 3rd party plugin could block multiple request on same session.
			// Refresh the session for each loop.
			session_write_close();

			// @usleep(1000000);
			sleep(3);
		}

		jexit();
	}
}
