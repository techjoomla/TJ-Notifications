<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_jticketing
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\HTML\HTMLHelper;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Uri\Uri;

HTMLHelper::_('formbehavior.chosen', 'select');

$listOrder     = $this->escape($this->state->get('list.ordering'));
$listDirn      = $this->escape($this->state->get('list.direction'));
?>

<form action="index.php?option=com_tjnotifications&view=notifications" method="post" id="adminForm" name="adminForm">
	<?php
	if (empty($this->user->authorise('core.viewlist', 'com_tjnotifications')))
	{
		$app = Factory::getApplication();
		$msg = Text::_('JERROR_ALERTNOAUTHOR');
		JError::raiseError(403, $msg);
		$app->redirect(Route::_('index.php?Itemid=0', false));
	}
	else
	{
		if (!empty($this->sidebar))
		{
			?>
			<div id="j-sidebar-container" class="span2">
				<?php echo $this->sidebar;?>
			</div>
			<div id="j-main-container" class="span10">
			<?php
		}
		else
		{
			?>
			<div id="j-main-container">
			<?php
		}
		?>
		<div class="row-fluid">
			<div class="span10">
				<?php
					echo JLayoutHelper::render(
						'joomla.searchtools.default',
						array('view' => $this)
					);
				?>
			</div>
		</div>
		<div class="btn-group pull-right hidden-phone">
			<label for="limit" class="element-invisible">
				<?php echo Text::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?>
			</label>
			<?php echo $this->pagination->getLimitBox(); ?>
		</div>
		<br>
		<div class="clearfix"></div>
		<?php
		if (empty($this->items))
		{
			?>
			<div class="clearfix">&nbsp;</div>
				<div class="alert alert-no-items">
					<?php echo Text::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_NO_MATCHING_RESULTS"); ?>
				</div>
			<?php
		}
		else
		{
			?>
			<table class="table table-striped table-hover">
				<thead>
					<tr>
						<?php
						if ($this->user->authorise('core.edit', 'com_tjnotifications') || $this->user->authorise('core.delete', 'com_tjnotifications'))
						{
							?>
							<th width="2%" class="nowrap center">
								<input type="checkbox" name="checkall-toggle" value="" title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
							</th>
							<?php
						}
						?>
						<th width="10%" class="hidden-phone">
							<?php echo HTMLHelper::_('grid.sort', Text::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_TITLE"), 'title', $listDirn, $listOrder);?>
						</th>
						<th width="10%" class="hidden-phone">
							<?php echo HTMLHelper::_('grid.sort', Text::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_KEY"), 'key', $listDirn, $listOrder);?>
						</th>
						<th width="10%" class="nowrap center">
							<?php echo HTMLHelper::_('grid.sort', Text::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_EMAIL_STATUS"), 'email_status', $listDirn, $listOrder);?>
						</th>
						<th width="10%" class="nowrap center">
							<?php echo HTMLHelper::_('grid.sort', Text::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_USER_CONTROL"), 'user_control', $listDirn, $listOrder);?>
						</th>
						<th width="10%" class="nowrap center">
							<?php echo HTMLHelper::_('grid.sort', Text::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_UNSUBCRIBED_USERS"), 'unsubcribed_users', $listDirn, $listOrder);?>
						</th>
						<th width="10%" class="nowrap center">
							<?php echo HTMLHelper::_('grid.sort', Text::_("COM_TJNOTIFICATIONS_CORE_TEMPLATE"), 'core', $listDirn, $listOrder);?>
						</th>
						<th width="10%" class="nowrap center">
							<?php echo HTMLHelper::_('grid.sort', Text::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_ID"), 'id', $listDirn, $listOrder); ?>
						</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="12">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
				<tbody>
					<?php
					if (!empty($this->items))
					{
						foreach ($this->items as $i => $row)
						{
							if ($this->component)
							{
								$link = Route::_('index.php?option=com_tjnotifications&task=notification.edit&id=' . $row->id . '&extension=' . $this->component);
							}
							else
							{
								$link = Route::_('index.php?option=com_tjnotifications&task=notification.edit&id=' . $row->id);
							}
							?>
							<tr>
								<?php
								if ($this->user->authorise('core.edit', 'com_tjnotifications') || $this->user->authorise('core.delete', 'com_tjnotifications'))
								{
									?>
									<td class="center">
										<?php echo HTMLHelper::_('grid.id', $i, $row->id); ?>
									</td>
									<?php
								}
								?>
								<td class="">
									<?php
									if ($this->user->authorise('core.edit', 'com_tjnotifications'))
									{
										?>
										<a href="<?php echo $link;?>">
											<?php echo $row->title; ?>
										</a>
										<?php
									}
									else
									{
										echo $row->title;
									}
									?>
								</td>

								<td class="">
									<?php
									if ($this->user->authorise('core.edit', 'com_tjnotifications'))
									{
										?>
										<a href="<?php echo $link; ?>">
											<?php echo $row->key; ?>
										</a>
										<?php
									}
									else
									{
										echo $row->key;
									}
									?>
								</td>

								<td class="center">
									<?php
									if ($this->user->authorise('core.emailstatus', 'com_tjnotifications'))
									{
										?>
<!--
										<a href="javascript:void(0);" class="hasTooltip" data-original-title="<?php //echo ( $row->email['state'] ) ? Text::_( 'COM_TJNOTIFICATIONS_STATE_ENABLE' ) : Text::_( 'COM_TJNOTIFICATIONS_STATE_DISABLE' );?>" onclick=" listItemTask('cb<?php //echo $i;?>','<?php //echo ( $row->email['state'] ) ? 'notifications.disableEmailStatus' : 'notifications.enableEmailStatus';?>')">
-->
										<?php
									}
										?>
										<img src="<?php echo Uri::root();?>administrator/components/com_tjnotifications/images/<?php echo ( $row->email['state'] ) ? 'publish.png' : 'unpublish.png';?>" width="16" height="16" border="0" />
									</a>
								</td>

								<td class="center">
									<?php
									if ($this->user->authorise('core.usercontrol', 'com_tjnotifications'))
									{
										?>
<!--
										<a href="javascript:void(0);" class="hasTooltip" data-original-title="<?php //echo ( $row->user_control ) ? Text::_( 'COM_TJNOTIFICATIONS_STATE_ENABLE' ) : Text::_( 'COM_TJNOTIFICATIONS_STATE_DISABLE' );?>" onclick=" listItemTask('cb<?php //echo $i;?>','<?php //echo ( $row->user_control ) ? 'notifications.disableUserControl' : 'notifications.enableUserControl';?>')">
-->
										<?php
									}
										?>
										<img src="<?php echo Uri::root();?>administrator/components/com_tjnotifications/images/<?php echo ( $row->user_control ) ? 'publish.png' : 'unpublish.png';?>" width="16" height="16" border="0" />
									</a>
								</td>

								<td class="center">
									<?php echo $this->count->name; ?>
								</td>

								<td class ="center">
									<?php
									if ($row->core)
									{
										?>
										<span class="label label-important"><?php echo Text::_("COM_TJNOTIFICATIONS_CORE_TEMPLATE_CORE_VALUE")?></span>
										<?php
									}
									else
									{
										?>
										<span class="label"><?php echo Text::_("COM_TJNOTIFICATIONS_CORE_TEMPLATE_VALUE")?></span>
										<?php
									}
									?>
								</td>

								<td class="center">
									<?php
									if ($this->user->authorise('core.edit', 'com_tjnotifications'))
									{
										?>
										<a href="<?php echo $link; ?>">
											<?php echo $row->id; ?>
										</a>
										<?php
									}
									else
									{
										echo $row->id;
									}
									?>
								</td>
							</tr>
							<?php
						}
					}
					?>
				</tbody>
			</table>
			<?php
		}
	}
	?>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="extension" value="<?php echo $this->component; ?>" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
