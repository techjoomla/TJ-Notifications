<?php
/**
 * @package     Tjnotifications
 * @subpackage  com_tjnotifications
 *
 * @copyright   Copyright (C) 2009 - 2020 Techjoomla. All rights reserved.
 * @license     http:/www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access.
defined('_JEXEC') or die();
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\HTML\HTMLHelper;

FormHelper::loadFieldClass('groupedlist');

/**
 * JFormFieldMobilenumberfields class
 *
 * @package     Tjnotifications
 * @subpackage  component
 * @since       __DEPLOY_VERSION__
 */

class JFormFieldMobilenumberfields extends JFormFieldGroupedList
{
	/**
	 * The form field type.
	 *
	 * @var   string
	 * @since __DEPLOY_VERSION__
	 */
	protected $type = 'mobilenumberfields';

	/**
	 * Fiedd to decide if options are being loaded externally and from xml
	 *
	 * @var   integer
	 * @since __DEPLOY_VERSION__
	 */
	protected $loadExternally = 0;

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return array An array of HTMLHelper options.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function getGroups()
	{
		// Load fields helper
		JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');

		// Get custom field names by users
		$customFieldnames = FieldsHelper::getFields('com_users.user');
		$comFieldOptions  = array();

		$comFieldOptions[][] = HTMLHelper::_('select.option', '', JText::_('COM_TJNOTIFICATIONS_SUBSCRIPTIONS_SELECT_FIELD'));

		foreach ($customFieldnames as $key => $field)
		{
			$comFieldOptions['custom_fields'][] = HTMLHelper::_('select.option', 'com_fields.' . $field->name, $field->title);
		}

		$db        = Factory::getDBO();
		$columnArr = $db->getTableColumns("#__users");

		foreach ($columnArr as $key => $value)
		{
			if ($value == 'int' || $value == 'text' || $value == 'varchar')
			{
				$comFieldOptions['joomla'][] = HTMLHelper::_('select.option', $key, $key);
			}
		}

		return array_merge(parent::getGroups(), $comFieldOptions);
	}
}
