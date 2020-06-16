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
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('formbehavior.chosen', 'select');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$doc       = Factory::getDocument();
$style     = "
.reset-table,
.reset-table table tr,
.reset-table tr td,
.reset-table table tbody,
.reset-table table thead,
.reset-table table tfoot,
.reset-table table tr th,
.reset-table table tfoot tr tf
{
    margin:0;
    /*padding:0;*/
    background:none;
    background-color: transparent !important;
    border:none;
    border-collapse:collapse;
    border-spacing:0;
    background-image:none;
}";

$doc->addStyleDeclaration($style);
?>

<form action="index.php?option=com_tjnotifications&view=notifications" method="post" id="adminForm" name="adminForm">
	<?php
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
					<?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
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

							<th width="23%" class="hidden-phone">
								<?php echo HTMLHelper::_(
									'grid.sort', Text::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_TITLE"), 'title', $listDirn, $listOrder
									); ?>
							</th>

							<th width="20%" class="hidden-phone">
								<?php echo Text::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_TRANSLATED_IN") ?>
							</th>

							<th width="20%" class="hidden-phone">
								<?php echo Text::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_TRANSLATED_NOT_IN") ?>
							</th>

							<th width="15%" class="">
								<?php echo Text::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_BACKEND_STATUS") ?>
							</th>

							<th width="5%" class="center">
								<?php echo HTMLHelper::_(
									'grid.sort',
									Text::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_USER_CONTROL"), 'user_control', $listDirn, $listOrder
								);?>
							</th>

							<th width="5%" class="center">
								<?php echo Text::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_UNSUBCRIBED_USERS") ?>
							</th>

							<th width="5%" class="center">
								<?php echo HTMLHelper::_('grid.sort', Text::_("COM_TJNOTIFICATIONS_CORE_TEMPLATE"), 'core', $listDirn, $listOrder);?>
							</th>

							<th width="5%" class="center">
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

										<div class="small">
											<?php echo Text::_('COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_KEY') . ': ' . $row->key; ?>
										</div>
									</td>

									<td class="d-md-table-cell">
										<table class="reset-table">
										<?php
										foreach (TJNOTIFICATIONS_CONST_BACKENDS_ARRAY as $keyBackend => $backend)
										{
											if ($row->$backend['languages'])
											{
												?>
												<tr>
													<td>
														<?php echo Text::_('COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_' . strtoupper($backend) . '_TITLE'); ?>
													</td>

													<td>
														<?php
														foreach ($row->$backend['languages'] as $language)
														{
															if ($language == "*")
															{
																echo Text::_('JALL');
																echo (count($row->$backend['languages']) > 1) ? ', ' : '';
															}
															else
															{
																$language = $this->languages[$language];

																if ($language->image)
																{
																	echo HTMLHelper::_(
																		'image',
																		'mod_languages/' . $language->image . '.gif',
																		$language->title_native,
																		array ('title' => $language->title_native), true
																	) . ' ';
																}
																else
																{
																	?>
																	<span class="badge badge-secondary" title="<?php echo $language->title_native; ?>">
																		<?php echo strtoupper($language->sef) . ' '; ?>
																	</span>
																	<?php
																}
															}
														}
														?>
													</td>
												</tr>
												<?php
											}
										}
										?>
										</table>
									</td>

									<td class="d-md-table-cell">
										<table class="reset-table">
										<?php
										foreach (TJNOTIFICATIONS_CONST_BACKENDS_ARRAY as $keyBackend => $backend)
										{
											if ($row->$backend['languages'])
											{
												?>
												<tr>
													<td>
														<?php echo Text::_('COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_' . strtoupper($backend) . '_TITLE'); ?>
													</td>

													<td>
														<?php
														foreach ($this->languages as $language)
														{
															if (!in_array($language->lang_code, $row->$backend['languages']))
															{
																if ($language->image)
																{
																	echo HTMLHelper::_(
																		'image',
																		'mod_languages/' . $language->image . '.gif',
																		$language->title_native,
																		array ('title' => $language->title_native), true
																	) . ' ';
																}
																else
																{
																	?>
																	<span class="badge badge-secondary" title="<?php echo $language->title_native; ?>">
																		<?php echo strtoupper($language->sef) . ' '; ?>
																	</span>
																	<?php
																}
															}
														}
														?>
													</td>
												</tr>
												<?php
											}
										}
										?>
										</table>
									</td>

									<td class="">
										<table class="reset-table">
										<?php
										foreach (TJNOTIFICATIONS_CONST_BACKENDS_ARRAY as $keyBackend => $backend)
										{
											if (!isset($row->$backend['state']))
											{
												continue;
											}
											?>

											<tr>
												<td>
													<?php echo Text::_('COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_' . strtoupper($backend) . '_TITLE'); ?>
												</td>

												<td>
													<?php
													if ($this->user->authorise('core.emailstatus', 'com_tjnotifications'))
													{
														?>
														<!--
														<a href="javascript:void(0);" class="hasTooltip"
															data-original-title="<?php // @echo ( ($row->$backend['state'] ) ? Text::_( 'COM_TJNOTIFICATIONS_STATE_ENABLE' ) : Text::_( 'COM_TJNOTIFICATIONS_STATE_DISABLE' );?>"
															onclick=" listItemTask('cb<?php // @echo $i;?>','<?php // @echo ( ($row->$backend['state'] ) ? 'notifications.disableEmailStatus' : 'notifications.enableEmailStatus'; ?>')">
														</a>
														-->
														<?php
													}
													?>

													<img src="<?php echo Uri::root() .
														'administrator/components/com_tjnotifications/images/' .
														(!empty($row->$backend['state']) ? 'publish.png' : 'unpublish.png'); ?>"
														width="16" height="16" border="0" />
												</td>
											</tr>
											<?php
										}
										?>
										</table>
									</td>

									<td class="center">
										<?php
										if ($this->user->authorise('core.usercontrol', 'com_tjnotifications'))
										{
											?>
											<!--
											<a href="javascript:void(0);" class="hasTooltip"
												data-original-title="<?php // @echo ( $row->user_control ) ? Text::_( 'COM_TJNOTIFICATIONS_STATE_ENABLE' ) : Text::_( 'COM_TJNOTIFICATIONS_STATE_DISABLE' );?>"
												onclick=" listItemTask('cb<?php // @echo $i;?>','<?php // @echo ( $row->user_control ) ? 'notifications.disableUserControl' : 'notifications.enableUserControl'; ?>')">
											</a>
											-->
											<?php
										}
										?>

										<img src="<?php echo Uri::root() .
											'administrator/components/com_tjnotifications/images/' .
											($row->user_control) ? 'publish.png' : 'unpublish.png'; ?>"
											 width="16" height="16" border="0" />
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
			?>
		</div>

	<input type="hidden" name="task"       value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="extension"  value="<?php echo $this->component; ?>" />

	<input type="hidden" name="filter_order"     value="<?php echo $listOrder; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>

	<?php echo HTMLHelper::_('form.token'); ?>
</form>
