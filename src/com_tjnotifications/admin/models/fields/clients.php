<?php
/**
 * @package     Tjnotifications
 * @subpackage  com_tjnotification
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2020 Techjoomla. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ListField;


/**
 * Custom field to list all client of tjnotification
 *
 * @since  2.0.1
 */
class FormFieldClients extends ListField
{
	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array		An array of JHtml options.
	 *
	 * @since   2.0.1
	 */
	protected function getOptions()
	{
		$options = array();
		$db = Factory::getDbo();

		$options[] = HTMLHelper::_('select.option', '', Text::_('COM_TJNOTIFICATIONS_FIELD_CLIENT_OPTION'));

		// Create a new query object.
		$query = $db->getQuery(true);

		$query->select('DISTINCT (`client`)');
		$query->from('#__tj_notification_templates');
		$db->setQuery($query);

		$listobjects = $db->loadObjectList();

		if (!empty($listobjects))
		{
			foreach ($listobjects as $obj)
			{
				$client = explode('_', $obj->client);

				if (!empty($client[1]))
				{
					$options[] = HTMLHelper::_('select.option', $obj->client, ucfirst($client[1]));
				}
				else
				{
					$options[] = HTMLHelper::_('select.option', $obj->client, ucfirst($client[0]));
				}
			}
		}

		return $options;
	}
}
