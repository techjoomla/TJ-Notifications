<?php

// No direct access
defined('_JEXEC') or die;
JHtml::_('formbehavior.chosen','select');
JHtml::_('behavior.formvalidator');
$today= gmdate('Y-m-d');
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
							if(empty($this->item->id)) :
						 $class_name="";
						endif
					 ?>

					<li  class="active"><a href="#notification" aria-controls="notification" data-toggle="tab"><?php echo JText::_('COM_TJNOTIFICATIONS_VIEW_NOTIFICATION_TAB_NOTIFICATION')?></a></li>
					<li class=""><a href="#email" aria-controls="email"  data-toggle="tab"><?php echo JText::_('COM_TJNOTIFICATIONS_VIEW_NOTIFICATION_TAB_Email') ?></a></li>
<!--
					<?php
					//~ else :
					//~ echo JHTML::tooltip(JText::_('COM_TJNOTIFICATIONS_VIEW_NOTIFICATION_TOOLTIP_MESSAGE'), '','', "<h3>". $this->item->client . " / " . $this->item->key ."</h3>");
					//~ endif ?>
					<li><a href="#sms" aria-controls="sms"  data-toggle="tab"><?php echo JText::_('COM_TJNOTIFICATIONS_VIEW_NOTIFICATION_TAB_SMS') ?></a></li>
					<li><a href="#push" aria-controls="push"  data-toggle="tab"><?php echo JText::_('COM_TJNOTIFICATIONS_VIEW_NOTIFICATION_TAB_Push') ?></a></li>
					<li><a href="#web" aria-controls="web"  data-toggle="tab"><?php echo JText::_('COM_TJNOTIFICATIONS_VIEW_NOTIFICATION_TAB_Web') ?></a></li>
-->
				  </ul>


				<div class="tab-content">
					<div  class="tab-pane active" id="notification">
						<?php foreach ($this->form->getFieldset('primary_fieldset') as $field): ?>
							<?php if(empty($this->item->id)) :?>
								<div class="control-group">
									<div class="control-label"><?php echo $field->label; ?></div>
									<?php if ($this->component and $field->fieldname == 'client'):?>
										<div class="controls"><input type="text" readonly='true' name="jform[client]" id="jform_client" value="<?php echo $this->component; ?>"/></div>
									<?php else : ?>
										<div class="controls"><?php echo $field->input ; ?></div>
									<?php endif;?>
								</div>
							<?php else : ?>
							<div class="control-group">
								<div class="control-label"><?php echo $field->label; ?></div>
								<?php if ($field->fieldname == 'client'):?>
									<div class="controls"><input type="text" readonly='true' name="jform[client]" id="jform_client" value="<?php echo $this->item->client; ?>"/></div>
								<?php elseif ($field->fieldname == 'key'):?>
									<div class="controls"><input type="text" readonly='true' name="jform[client]" id="jform_client" value="<?php echo $this->item->key; ?>"/></div>
								<?php endif;?>
								<?php if ($field->fieldname == 'title' || $field->fieldname == 'user_control'):?>
									<div class="controls"><?php echo $field->input ; ?></div>
								<?php endif;?>
							</div>
							<input type="hidden" name="jform[key]" id="jform_key" value="<?php echo $this->item->key; ?>"/>
							<?php endif; ?>
						<?php endforeach;?>
					</div>

					<div  class="tab-pane" id="email">
						<?php foreach ($this->form->getFieldset('email_fieldset') as $field): ?>
                        <div class="control-group">
							<div class="control-label"><?php echo $field->label; ?></div>
                            <div class="controls"><?php echo $field->input ; ?></div>
                        </div>
                    <?php endforeach; ?>
					</div>

<!--
					<?php if(!empty($this->item->id)) : ?>
					<div  class="tab-pane" id="email">

					<?php else :?>
					<div  class="tab-pane" id="email">
					<?php endif ?>
-->


<!--
					<div  class="tab-pane" id="sms">
						<?php foreach ($this->form->getFieldset('sms_fieldset') as $field): ?>
                        <div class="control-group span8">
                            <div class="control-label"><?php echo $field->label; ?></div>
                            <div class="controls"><?php echo $field->input ; ?></div>
                        </div>
                    <?php endforeach; ?>
					</div>
-->

<!--
					<div  class="tab-pane" id="push">
						<?php foreach ($this->form->getFieldset('push_fieldset') as $field): ?>
                        <div class="control-group span8">
                            <div class="control-label"><?php echo $field->label; ?></div>
                            <div class="controls"><?php echo $field->input ; ?></div>
                        </div>
                    <?php endforeach; ?>
					</div>
-->

<!--
					<div  class="tab-pane" id="web">
						<?php foreach ($this->form->getFieldset('web_fieldset') as $field): ?>
                        <div class="control-group span8">
                            <div class="control-label"><?php echo $field->label; ?></div>
                            <div class="controls"><?php echo $field->input ; ?></div>
                        </div>
                    <?php endforeach; ?>
					</div>
-->
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
</form>
