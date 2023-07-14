<?php
/**
 * @package     TJNotifications
 * @subpackage  com_tjnotifications
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

use Joomla\CMS\Uri\Uri;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\HTML\HTMLHelper;

$language = Factory::getLanguage();
$language->load('com_tjnotification', JPATH_SITE, 'en-GB', true);
$language->load('com_tjnotification', JPATH_SITE, null, true);

HTMLHelper::_('script', '/jquery.min.js');
HTMLHelper::_('script','https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js');
Factory::getDocument()->addScriptDeclaration('
	const tjnBaseurl = "' . Uri::root() . '";
	jQuery.noConflict();
	jQuery(".btn-group > .btn").click(function(){
		jQuery(this).addClass("active").siblings().removeClass("active");
	});

	function addPreferance(pId,client,provider,key)
	{
		if (pId)
		{
			jQuery.ajaxSetup({
				global: false,
				type:"post",
				url:tjnBaseurl+"index.php?option=com_tjnotifications&task=preferences.save",
				dataType:"json",
				beforeSend: function () {
					jQuery("#ajax-loader"+pId).show();
				jQuery("#ajax-loader"+pId).html("<img src=\'' . Uri::root() . 'components/com_tjnotifications/images/ajax-loader.gif\'><style=\'display:block\'>");
				},
				complete: function () {
					jQuery("#ajax-loader"+pId).hide();
					jQuery("#tick"+pId).show();
					jQuery("#tick"+pId).html("<img src=\'' . Uri::root() . 'components/com_tjnotifications/images/tick.png\'><style=\'display:block\'>");

					setTimeout(function() {
						jQuery("#tick"+pId).hide();
					}, 5000);
				}
			});
			jQuery.ajax({
				data:
				{
					client_name:client,
					provider_name:provider,
					key:key,
				},
				success: function (response)
				{
					jQuery("#display_info").html("Item successfully saved");
				}
			});
		}
		else
		{
			jQuery("#display_info").html("Item not successfully saved");
		}
	}
	function removePreferance(pId,client,provider,key)
	{
		if (pId)
		{
			jQuery.ajaxSetup({
				global: false,
				type:"post",
				url:tjnBaseurl+"index.php?option=com_tjnotifications&task=preferences.delete",

				beforeSend: function () {
					jQuery("#ajax-loader"+pId).show();
				jQuery("#ajax-loader"+pId).html("<img src=\'' . Uri::root() . 'components/com_tjnotifications/images/ajax-loader.gif\'><style=\'display:block\'>");
				},
				complete: function () {
					jQuery("#ajax-loader"+pId).hide();
					jQuery("#tick"+pId).show();
					jQuery("#tick"+pId).html("<img src=\'' . Uri::root() . 'components/com_tjnotifications/images/tick.png\'><style=\'display:block\'>");
						setTimeout(function() {
						jQuery("#tick"+pId).hide();
					}, 5000);
				}
			});
			jQuery.ajax({
			dataType:"json",
			data:
			{
				client_name:client,
				provider_name:provider,
				key:key,
			},
			success: function (response)
			{
				jQuery("#display_info").html("Item successfully saved");
			}
			});
		}
		else
		{
			jQuery("#display_info").html("Item not successfully saved");
		}
	}
');
?>

<form action="index.php?option=com_tjnotifications&view=preferences" method="post" id="adminForm" name="adminForm">
	<div class="row">
		<div class="col-xs-12 col-md-6">
		</div>
	</div>

	<div id="display_info"></div>
	<ul class="nav nav-tabs" id="myTab" role="tablist">
		<?php if (!empty($this->clients)) : ?>
		<?php foreach ($this->clients as $i => $menu) :?>
		<li class="nav-item" role="presentation">
			<button type="button" id="<?php echo($menu->client) . '-tab'; ?>" role="tab" data-bs-target="#<?php echo($menu->client); ?>" data-bs-toggle="tab" class="nav-link <?php echo ($i == 0) ? ' active ' : ''?>">
				<?php echo str_replace("com_","",$menu->client); ?>
			</button>
		</li>
		<?php endforeach; ?>
		<?php endif; ?>
	</ul>

	<div class="tab-content">
		<?php foreach ($this->clients as $i => $menu) :?>
		<div role="tabpanel" class="tab-pane fade <?php echo ($i == 0) ? ' active show' : ''?>" id="<?php echo($menu->client);?>">
			<table class="table table-striped table-hover">
				<thead>
					<tr>
						<th width="20%">

						</th>
						<?php if (!empty($this->providers)) : ?>
						<?php foreach ($this->providers as $i => $head) :?>
						<th width="30%">
							<?php echo($head->provider); ?>
						</th>
						<?php endforeach; ?>
						<?php endif; ?>
					</tr>
				</thead>
				<tbody>
					<div class="row">
						<div class="col-md-3">
							<?php foreach ($this->keys[$menu->client] as $key=>$values) : ?>
							<?php foreach ($values as $value ) : ?>
							<tr>
								<td align="center">
									<?php echo $value; ?>
								</td>
								<?php foreach ($this->providers as $i => $head) :?>
								<td>
									<?php  $temp=0; ?>
									<?php foreach ($this->adminPreferences[$head->provider] as $adminKey => $admin) :?>
									<?php if ($admin->client == $menu->client && $admin->key == $value) : $temp++; ?>

									<?php if (empty($this->preferences)) :  ?>

									<div class="control">
										<fieldset class="btn-group btn-group-yesno radio pull-left">
											<input type="radio" id="<?php echo $value.$i; ?>" name="prefer" value="1" onclick="removePreferance('<?php echo $value.$i; ?>','<?php echo($menu->client); ?>','<?php echo($head->provider); ?>','<?php echo $value; ?>')" checked="checked" />
											<input type="radio" id="<?php echo $key.$i; ?>" name="prefer1" value="0" onclick="addPreferance('<?php echo $value.$i; ?>','<?php echo($menu->client); ?>','<?php echo($head->provider); ?>','<?php echo $value; ?>')" />
											<label class="btn-success" for="<?php echo $value.$i; ?>"><?php echo Text::_('COM_TJNOTIFICATION_VIEWS_PREFERENCES_FIELD_ENABLE'); ?></label>
											<label class="btn" for="<?php echo $key.$i; ?>"><?php echo Text::_('COM_TJNOTIFICATION_VIEWS_PREFERENCES_FIELD_DISABLE'); ?></label>
										</fieldset>
									</div>

									<div class="pull-right" id="<?php echo 'ajax-loader'.$value.$i; ?>"></div>
									<div class="pull-right" id="<?php echo 'tick'.$value.$i; ?>"></div>
									<?php else: $count=0; ?>

									<?php foreach ($this->preferences as $j => $prefer) :?>
									<?php if ($prefer->client == $menu->client && $prefer->key == $value && $prefer->provider == $head->provider) : ?>
									<?php $count++; ?>
									<div class="control">
										<fieldset class="btn-group btn-group-yesno radio pull-left">
											<input type="radio" id="<?php echo $menu->client.$value.$i; ?>" name="prefer" value="1" onclick="removePreferance('<?php echo $menu->client.$value.$i; ?>','<?php echo($menu->client); ?>','<?php echo($head->provider); ?>','<?php echo $value; ?>')" />
											<input type="radio" id="<?php echo $menu->client.$key.$i; ?>" name="prefer1" value="0" onclick="addPreferance('<?php echo $menu->client.$value.$i; ?>','<?php echo($menu->client); ?>','<?php echo($head->provider); ?>','<?php echo $value; ?>')" checked="checked" />
											<label class="btn" for="<?php echo $menu->client.$value.$i; ?>"><?php echo Text::_('COM_TJNOTIFICATION_VIEWS_PREFERENCES_FIELD_ENABLE'); ?></label>
											<label class="btn-danger" for="<?php echo $menu->client.$key.$i; ?>"><?php echo Text::_('COM_TJNOTIFICATION_VIEWS_PREFERENCES_FIELD_DISABLE'); ?></label>
										</fieldset>
									</div>
									<?php endif;?>
									<?php endforeach; ?>

									<?php if ($count==0): ?>
									<div class="control">
										<fieldset class="btn-group btn-group-yesno radio pull-left">
											<input type="radio" id="<?php echo $menu->client.$value.$i; ?>" name="prefer" value="1" onclick="removePreferance('<?php echo $menu->client.$value.$i; ?>','<?php echo($menu->client); ?>','<?php echo($head->provider); ?>','<?php echo $value; ?>')" checked="checked" />
											<input type="radio" id="<?php echo $menu->client.$key.$i; ?>" name="prefer1" value="0" onclick="addPreferance('<?php echo $menu->client.$value.$i; ?>','<?php echo($menu->client); ?>','<?php echo($head->provider); ?>','<?php echo $value; ?>')" />
											<label class="btn-success" for="<?php echo $menu->client.$value.$i; ?>"><?php echo Text::_('COM_TJNOTIFICATION_VIEWS_PREFERENCES_FIELD_ENABLE'); ?></label>
											<label class="btn" for="<?php echo $menu->client.$key.$i; ?>"><?php echo Text::_('COM_TJNOTIFICATION_VIEWS_PREFERENCES_FIELD_DISABLE'); ?></label>
										</fieldset>
									</div>
									<?php endif; ?>

									<?php endif;?>

									<?php endif;?>

									<?php endforeach; ?>

									<?php if ($temp == 0): ?>
									<span class="label label-warning"><?php echo Text::_('COM_TJNOTIFICATIONS_VIEW_PREFERENCES_TAB_UNSUBSCRIBED'); ?></span>
									<?php endif; ?>
									<div class="pull-right" id="<?php echo 'ajax-loader'.$menu->client.$value.$i; ?>"></div>
									<div class="pull-right" id="<?php echo 'tick'.$menu->client.$value.$i; ?>"></div>
								</td>
								<?php endforeach; ?>
							</tr>
							<?php endforeach; ?>
							<?php endforeach; ?>
						</div>
					</div>
				</tbody>
			</table>
		</div>
		<?php endforeach;?>
	</div>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
