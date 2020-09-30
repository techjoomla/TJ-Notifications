<?php
/**
 * @package     JTicketing
 * @subpackage  com_jticketing
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2020 Techjoomla. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die();

jimport('joomla.form.helper');
jimport('joomla.filesystem.file');
jimport('techjoomla.tjmoney.tjmoney');

JFormHelper::loadFieldClass('list');
JFormHelper::loadFieldClass('groupedlist');

/**
 * JFormFieldCurrencyList class
 *
 * @package     JTicketing
 * @subpackage  component
 * @since       1.0
 */

class JFormFieldJoomlaSms extends JFormFieldGroupedList
{
	/**
	 * The form field type.
	 *
	 * @var   string
	 * @since 1.6
	 */
	protected $type = 'joomlasms';

	/**
	 * Fiedd to decide if options are being loaded externally and from xml
	 *
	 * @var   integer
	 * @since 2.2
	 */
	protected $loadExternally = 0;

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return array An array of JHtml options.
	 *
	 * @since   11.4
	 */
	protected function getGroups()
	{
        //load fields helper
        JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');

        // get custom field names by users
        $customFieldnames = FieldsHelper::getFields('com_users.user');
        $comFieldOptions = array();

        $comFieldOptions[] = JHtml::_('select.option', '', JText::_('COM_TJNOTIFICATIONS_SUBSCRIPTIONS_SELECT_FIELD'));

        foreach ($customFieldnames as $key => $field)
        {
        	$comFieldOptions['custom_fields'][] = JHtml::_('select.option', 'com_fields.' . $field->name, $field->title);
        }

		
		$db = JFactory::getDBO();
		$columnArr = $db->getTableColumns("#__users");

		foreach ($columnArr as $key => $value)
		{
		    if ($value == 'int' || $value == 'text' || $value == 'varchar')
		    {
		    	$comFieldOptions['joomla'][] = JHtml::_('select.option', $key, $key);
		    }
		}

		return array_merge(parent::getGroups(), $comFieldOptions);
	}
}
