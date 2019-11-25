<?php

// No direct access
defined('_JEXEC') or die;
JHtml::_('formbehavior.chosen','select');
JHtml::_('behavior.formvalidator');
use Joomla\CMS\Language\Text;

$today= gmdate('Y-m-d');

$options['relative'] = true;

JHtml::_('script', 'com_tjnotifications/template.js', $options);
?>
<script>
	jQuery(document).ready(function()
	{
		jQuery("fieldset").click(function()
		{
			status=this.id+'0';
			statusChange=this.id+'1';
			var check=(jQuery("#"+status).attr("checked"));

			if(check=="checked")
			{
				var body=(this.id).replace("status", "body_ifr");
				var bodyData=(jQuery("#"+body).contents().find("body").find("p").html());
				if(bodyData=='<br data-mce-bogus="1">')
				{
					alert('Please fill the data');
					jQuery('#'+this.id).find('label[for='+statusChange+']').attr('class','btn active btn-danger');
					jQuery('#'+this.id).find('label[for='+status+']').attr('class','btn');
					return false;
				}
				else
				{
					jQuery('#'+this.id).find('label[for='+status+']').attr('class','btn active btn-success');
					jQuery('#'+this.id).find('label[for='+statusChange+']').attr('class','btn');
				}
			}
		});
	});
</script>


<form action="<?php echo JRoute::_('index.php?option=com_tjnotifications&layout=edit&id=' . (int) $this->item->id . '&extension='.$this->component); ?>"
    method="post" name="adminForm" id="adminForm">
     <input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />


	<div class="form-horizontal">
		<div class="row-fluid">
				<div class="span12">
					<ul class="nav nav-tabs">
						<?php
							 $class_name="active";
							if(empty($this->item->id))
							{
								 $class_name="";
							}
						 ?>
					<li  class="active"><a href="#notification" aria-controls="notification" data-toggle="tab"><?php echo JText::_('COM_TJNOTIFICATIONS_VIEW_NOTIFICATION_TAB_NOTIFICATION')?></a></li>
					<li class=""><a href="#email" aria-controls="email"  data-toggle="tab"><?php echo JText::_('COM_TJNOTIFICATIONS_VIEW_NOTIFICATION_TAB_Email') ?></a></li>
				  </ul>

				<div class="tab-content">
					<div  class="tab-pane active" id="notification">
						<?php foreach ($this->form->getFieldset('primary_fieldset') as $field): ?>
							<?php if(empty($this->item->id)) :?>
								<div class="control-group">
									<div class="control-label"><?php echo $field->label; ?></div>
									<?php if ($this->component and $field->fieldname === 'client'):?>
										<div class="controls"><input type="text" readonly='true' name="jform[client]" id="jform_client" value="<?php echo $this->component; ?>"/></div>
									<?php else : ?>
										<div class="controls"><?php echo $field->input ; ?></div>
									<?php endif;?>
								</div>
							<?php else : ?>
							<div class="control-group">
								<div class="control-label"><?php echo $field->label; ?></div>
								<?php if ($field->fieldname === 'client'):?>
									<div class="controls"><input type="text" readonly='true' name="jform[client]" id="jform_client" value="<?php echo $this->item->client; ?>"/></div>
								<?php elseif ($field->fieldname === 'key'):?>
									<div class="controls"><input type="text" readonly='true' name="jform[client]" id="jform_client" value="<?php echo $this->item->key; ?>"/></div>
								<?php endif;?>
								<?php if ($field->fieldname === 'title' || $field->fieldname === 'user_control'):?>
									<div class="controls"><?php echo $field->input ; ?></div>
								<?php endif;?>
							</div>
								<input type="hidden" name="jform[key]" id="jform_key" value="<?php echo $this->item->key; ?>"/>
								<input type="hidden" name="jform[client]" id="jform_client" value="<?php echo $this->item->client; ?>"/>
							<?php endif; ?>
						<?php endforeach;?>
					</div>

						<div class="tab-pane" id="email">
							<div class="span8">
							<?php foreach ($this->form->getFieldset('email_fieldset') as $field): ?>
								<div class="control-group">
									<div class="control-label"><?php echo $field->label; ?></div>
									<div class="controls"><?php echo $field->input ; ?></div>
								</div>
							<?php endforeach; ?>
							</div>
							<?php if ($this->tags): ?>
							<div class="span4">
								<div class="alert alert-info"><?php echo JText::_('COM_TJNOTIFICATIONS_TAGS_DESC'); ?> <br/></div>
									<table class="table table-bordered">
										<thead class="thead-default">
											<tr>
												<th><?php echo JText::_('COM_TJNOTIFICATIONS_REPLACEMENT_TAGS'); ?></th>
												<th><?php echo JText::_('COM_TJNOTIFICATIONS_REPLACEMENT_TAGS_DESC'); ?></th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($this->tags as $tags): ?>
											<tr>
												<td scope="row"><?php echo('{' . $tags->name . '}'); ?></td>
												<td><?php echo($tags->description); ?></td>
											</tr>
											<?php endforeach; ?>
										</tbody>
									</table>
								</div>
							</div>
							<?php endif;?>
						</div>
						<input type="hidden" name="jform[state]" id="jform_state" value="1"/>
						<input type="hidden" name="jform[created_on]" id="jform_created_on" value="<?php echo $today; ?>"/>
						<input type="hidden" name="jform[updated_on]" id="jform_updated_on" value="<?php echo $today; ?>"/>
					</div>
				</div>
			</fieldset>
		</div>
		<input type="hidden" name="task" value="notification.edit" />
		<?php echo JHtml::_('form.token'); ?>

<!-- Modal -->
<style>
	.modal-body {
	    overflow-y: auto;
	}
</style>
<div id="templatePreview" class="modal fade" role="dialog">
	<div class="modal-dialog">
	<button type="button" class="close" data-dismiss="modal" style="width: 40px;opacity: 0.7;">&times;</button>
	<!-- Modal content-->
	<div class="modal-content">
		<div class="modal-header">
			<h4 class="modal-title"><?php echo Text::_('COM_TJNOTIFICATIONS_TEMPLATE_MODAL_PREVIEW_TITLE'); ?></h4>
			<p class="alert alert-info hide" id="show-info"><?php echo Text::_('COM_TJNOTIFICATIONS_CERTIFICATE_TEMPLATE_MODAL_HEADER_INFO'); ?></p>
		</div>
		<div class="modal-body" id="previewTempl">
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		</div>
	</div>

	</div>
</div>
</form>
<script type="text/javascript">
	jQuery(document).ready(function () {

		template.previewTemplate();
	});
</script>
