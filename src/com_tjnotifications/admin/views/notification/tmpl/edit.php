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

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.formvalidator');

Text::script('COM_TJNOTIFICATIONS_NOTIFICATION_SMS_REMAINING_CHARACTER');
Text::script('COM_TJNOTIFICATIONS_NOTIFICATION_SMS_REMAINING_EXCEEDED');

$doc    = Factory::getDocument();
$script = 'jQuery(document).ready(function() {
	var childElements = document.getElementById("sms").querySelector(".ui-sortable").children;

	for (item of childElements) 
	{
		if(item.querySelector("textarea") !==null)
		{
			tjnotificationsAdmin.notification.validateSmsLength(item.querySelector("textarea"));
		}
	} 

	document.formvalidator.setHandler("json", function(value) {
		try {
			var params = JSON.stringify(JSON.parse(value));
			return true;
		}
		catch(e) {
			return false;
		}
	});

	jQuery("fieldset").click(function() {
		status=this.id+"0";
		statusChange=this.id+"1";
		var check=(jQuery("#"+status).attr("checked"));

		if (check=="checked") {
			var body=(this.id).replace("status", "body_ifr");
			var bodyData=(jQuery("#"+body).contents().find("body").find("p").html());

			if (bodyData == "<br data-mce-bogus=\"1\">") {
				alert("Please fill the data");
				jQuery("#"+this.id).find("label[for="+statusChange+"]").attr("class","btn active btn-danger");
				jQuery("#"+this.id).find("label[for="+status+"]").attr("class","btn");

				return false;
			}
			else {
				jQuery("#"+this.id).find("label[for="+status+"]").attr("class","btn active btn-success");
				jQuery("#"+this.id).find("label[for="+statusChange+"]").attr("class","btn");
			}
		}
	});
});

Joomla.submitbutton = function(task) {
	if (task== "notification.save" || task == "notification.save2new" || task == "notification.apply") {
		var isFormValid = document.formvalidator.isValid(document.getElementById("adminForm"));
		if (isFormValid == true) {
			Joomla.submitform(task, document.getElementById("adminForm"));
		}
		else {
			alert("' . Text::_('JGLOBAL_VALIDATION_FORM_FAILED') . '");
			return false;
		}
	}
	else if (task == "notification.cancel") {
		Joomla.submitform(task, document.getElementById("adminForm"));
	}
}';

$doc->addScriptDeclaration($script);
?>

<form
	action="<?php echo Route::_('index.php?option=com_tjnotifications&layout=edit&id=' . (int) $this->item->id . '&extension=' . $this->component); ?>"
	method="post" name="adminForm" id="adminForm" class="form-horizontal form-validate">
	<div class="row-fluid">
		<div class="span12">
			<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'notification')); ?>
				<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'notification', JText::_('COM_TJNOTIFICATIONS_VIEW_NOTIFICATION_TAB_NOTIFICATION')); ?>
					<?php
					foreach ($this->form->getFieldset('primary_fieldset') as $field)
					{
						if (empty($this->item->id))
						{
							?>
							<div class="control-group">
								<div class="control-label">
									<?php echo $field->label; ?>
								</div>

								<?php
								if ($this->component && $field->fieldname === 'client')
								{
									?>
									<div class="controls">
										<input type="text" readonly='true' name="jform[client]" id="jform_client" value="<?php echo $this->component; ?>"/>
									</div>
									<?php
								}
								else
								{
									?>
									<div class="controls">
										<?php echo $field->input; ?>
									</div>
									<?php
								}
								?>
							</div>
							<?php
						}
						else
						{
							?>
							<div class="control-group">
								<div class="control-label">
									<?php echo $field->label; ?>
								</div>

								<?php
								if ($field->fieldname === 'client')
								{
									?>
									<div class="controls">
										<input type="text" readonly='true' name="jform[client]" id="jform_client" value="<?php echo $this->item->client; ?>"/>
									</div>
									<?php
								}
								elseif ($field->fieldname === 'key')
								{
									?>
									<div class="controls">
										<input type="text" readonly='true' name="jform[client]" id="jform_client" value="<?php echo $this->item->key; ?>"/>
									</div>
									<?php
								}

								if ($field->fieldname === 'title' || $field->fieldname === 'user_control')
								{
									?>
									<div class="controls">
										<?php echo $field->input; ?>
									</div>
									<?php
								}
								?>
							</div>
							<?php
						}
					}
					?>
				<?php echo JHtml::_('bootstrap.endTab'); ?>

				<?php
				$backendsArray = explode(',', TJNOTIFICATIONS_CONST_BACKENDS_ARRAY);

				foreach ($backendsArray as $keyBackend => $backend)
				{
					echo JHtml::_(
						'bootstrap.addTab',
						'myTab',
						$backend,
						JText::_('COM_TJNOTIFICATIONS_VIEW_NOTIFICATION_TAB_' . strtoupper($backend))
					);
					?>

						<div class="span4">
							<?php
							foreach ($this->form->getFieldset($backend . '_fieldset') as $field)
							{
								if ($field->type != "Subform")
								{
									?>
									<div class="control-group">
										<div class="control-label"><?php echo $field->label; ?></div>
										<div class="controls"><?php echo $field->input; ?></div>
									</div>
									<?php
								}
							}

							if (!empty($this->item->replacement_tags))
							{
								echo $this->loadTemplate('replacement_tags');
							}
							?>
						</div>

						<div class="span8">
							<?php
							foreach ($this->form->getFieldset($backend . '_fieldset') as $field)
							{
								if ($field->type == "Subform")
								{
									?>
									<div class="control-group">
										<div class=""><?php echo $field->label; ?></div>
										<div class=""><?php echo $field->input; ?></div>
									</div>
									<?php
								}
							}
							?>
						</div>

					<?php
					echo JHtml::_('bootstrap.endTab');
				}
				?>

			<?php echo JHtml::_('bootstrap.endTabSet'); ?>
		</div>
	</div>

	<?php
	if (!empty($this->item->id))
	{
		?>
		<input type="hidden" name="jform[key]"    id="jform_key"    value="<?php echo $this->item->key; ?>"/>
		<input type="hidden" name="jform[client]" id="jform_client" value="<?php echo $this->item->client; ?>"/>
		<?php
	}
	?>

	<input type="hidden" name="jform[state]"  value="<?php echo $this->item->state; ?>" />
	<input type="hidden" name="jform[id]"     value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="task"          value="" />

	<?php echo HTMLHelper::_('form.token'); ?>
</form>
