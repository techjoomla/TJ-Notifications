<?php
/**
 * @package     Tjnotifications
 * @subpackage  com_tjnotifications
 *
 * @copyright   Copyright (C) 2009 - 2020 Techjoomla. All rights reserved.
 * @license     http:/www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('formbehavior.chosen', 'select');
HTMLHelper::_('bootstrap.tooltip');

$doc = Factory::getDocument();

$script = "
	Joomla.submitbutton = function (task) {
		if (task == 'subscription.cancel') {
			Joomla.submitform(task, document.getElementById('subscription-form'));
		}
		else {
			if (task != 'subscription.cancel' && document.formvalidator.isValid(document.id('subscription-form'))) {
				Joomla.submitform(task, document.getElementById('subscription-form'));
			}
			else {
				alert(Joomla.Text._('JGLOBAL_VALIDATION_FORM_FAILED'));
			}
		}
	}
";

$doc->addScriptDeclaration($script);

Text::script('JGLOBAL_VALIDATION_FORM_FAILED');
?>

<div class="subscription-edit row-fluid">
	<form
		action="<?php echo Route::_('index.php?option=com_tjnotifications&layout=edit&id=' . (int) $this->item->id) . '&extension=' . $this->extension; ?>"
		method="post" enctype="multipart/form-data" name="adminForm" id="subscription-form" class="form-validate form-horizontal">

		<div class="row-fluid">
			<?php echo $this->form->renderField('title'); ?>
			<?php echo $this->form->renderField('user_id'); ?>
			<?php echo $this->form->renderField('backend'); ?>
			<?php echo $this->form->renderField('address'); ?>
			<?php echo $this->form->renderField('device_id'); ?>
			<?php echo $this->form->renderField('platform'); ?>
			<?php echo $this->form->renderField('state'); ?>
			<?php echo $this->form->renderField('is_confirmed'); ?>
		</div>

		<input type="hidden" name="extension"               value="<?php echo $this->extension; ?>"/>
		<input type="hidden" name="jform[id]"               value="<?php echo $this->item->id; ?>" />
		<input type="hidden" name="jform[state]"            value="<?php echo $this->item->state; ?>" />
		<input type="hidden" name="jform[checked_out]"      value="<?php echo $this->item->checked_out; ?>" />
		<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />
		<input type="hidden" name="task"                    value=""/>

		<?php echo HTMLHelper::_('form.token'); ?>
	</form>
</div>
