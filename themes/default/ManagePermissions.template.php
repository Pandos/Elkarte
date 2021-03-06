<?php

/**
 * @name      ElkArte Forum
 * @copyright ElkArte Forum contributors
 * @license   BSD http://opensource.org/licenses/BSD-3-Clause
 *
 * This software is a derived product, based on:
 *
 * Simple Machines Forum (SMF)
 * copyright:	2011 Simple Machines (http://www.simplemachines.org)
 * license:  	BSD, See included LICENSE.TXT for terms and conditions.
 *
 * @version 1.0 Alpha
 */

/**
 * Template for the permissions page in admin panel.
 */
function template_permission_index()
{
	global $context, $settings, $scripturl, $txt, $modSettings;

	// Not allowed to edit?
	if (!$context['can_modify'])
		echo '
	<div class="errorbox">
		', sprintf($txt['permission_cannot_edit'], $scripturl . '?action=admin;area=permissions;sa=profiles'), '
	</div>';

	echo '
	<div id="admin_form_wrapper">
		<form action="', $scripturl, '?action=admin;area=permissions;sa=quick" method="post" accept-charset="UTF-8" name="permissionForm" id="permissionForm">';

		if (!empty($context['profile']))
			echo '
			<div class="title_bar">
				<h3 class="titlebg">', $txt['permissions_for_profile'], ': &quot;', $context['profile']['name'], '&quot;</h3>
			</div>';
		else
			echo '
			<div class="title_bar">
				<h3 class="titlebg">', $txt['permissions_title'], '</h3>
			</div>';

		echo '
			<table class="table_grid">
				<thead>
					<tr class="table_head">
						<th>', $txt['membergroups_name'], '</th>
						<th style="width:10%">', $txt['membergroups_members_top'], '</th>';

			if (empty($modSettings['permission_enable_deny']))
				echo '
						<th style="width:16%">', $txt['membergroups_permissions'], '</th>';
			else
				echo '
						<th style="width:8%">', $txt['permissions_allowed'], '</th>
						<th style="width:8%">', $txt['permissions_denied'], '</th>';

			echo '
						<th class="centertext" style="width:10%; vertical-align:middle">', $context['can_modify'] ? $txt['permissions_modify'] : $txt['permissions_view'], '</th>
						<th class="centertext" style="width:4%;vertical-align:middle">
							', $context['can_modify'] ? '<input type="checkbox" class="input_check" onclick="invertAll(this, this.form, \'group\');" />' : '', '
						</th>
					</tr>
				</thead>
				<tbody>';

	$alternate = false;
	foreach ($context['groups'] as $group)
	{
		$alternate = !$alternate;
		echo '
					<tr class="windowbg', $alternate ? '2' : '', '">
						<td>
							', !empty($group['help']) ? ' <a class="help" href="' . $scripturl . '?action=quickhelp;help=' . $group['help'] . '" onclick="return reqOverlayDiv(this.href);"><img class="icon" src="' . $settings['images_url'] . '/helptopics.png" alt="' . $txt['help'] . '" /></a>' : '<img class="icon" src="' . $settings['images_url'] . '/blank.png" alt="' . $txt['help'] . '" />', '&nbsp;<span>', $group['name'], '</span>';

		if (!empty($group['children']))
			echo '
							<br />
							<span class="smalltext">', $txt['permissions_includes_inherited'], ': &quot;', implode('&quot;, &quot;', $group['children']), '&quot;</span>';

		echo '
						</td>
						<td>', $group['can_search'] ? $group['link'] : $group['num_members'], '</td>';

		if (empty($modSettings['permission_enable_deny']))
			echo '
						<td style="width:16%">', $group['num_permissions']['allowed'], '</td>';
		else
			echo '
						<td class="centertext" style="width:8%', $group['id'] == 1 ? ';font-style:italic"' : '"', '>', $group['num_permissions']['allowed'], '</td>
						<td class="centertext" style="width:8%', $group['id'] == 1 || $group['id'] == -1 ? ';font-style:italic"' : (!empty($group['num_permissions']['denied']) ? ';color: red"' : '"'), '>', $group['num_permissions']['denied'], '</td>';

		echo '
						<td class="centertext">', $group['allow_modify'] ? '<a href="' . $scripturl . '?action=admin;area=permissions;sa=modify;group=' . $group['id'] . (empty($context['profile']) ? '' : ';pid=' . $context['profile']['id']) . '">' . ($context['can_modify'] ? $txt['permissions_modify'] : $txt['permissions_view']). '</a>' : '', '</td>
						<td class="centertext">', $group['allow_modify'] && $context['can_modify'] ? '<input type="checkbox" name="group[]" value="' . $group['id'] . '" class="input_check" />' : '', '</td>
					</tr>';
	}

	echo '
				</tbody>
			</table>
			<br />';

	// Advanced stuff...
	if ($context['can_modify'])
	{
		echo '
			<div class="cat_bar">
				<h3 class="catbg">
					<img id="permissions_panel_toggle" class="panel_toggle" style="display: none;" src="', $settings['images_url'], '/', empty($context['admin_preferences']['app']) ? 'collapse' : 'expand', '.png"  alt="*" />
					<a href="#" id="permissions_panel_link">', $txt['permissions_advanced_options'], '</a>
				</h3>
			</div>
			<div id="permissions_panel_advanced" class="windowbg">
				<div class="content">
					<fieldset>
						<legend>', $txt['permissions_with_selection'], '</legend>
						<dl class="settings admin_permissions">
							<dt>
								<a class="help" href="', $scripturl, '?action=quickhelp;help=permissions_quickgroups" onclick="return reqOverlayDiv(this.href);"><img class="icon" src="' . $settings['images_url'] . '/helptopics.png" alt="' . $txt['help'] . '" /></a>', $txt['permissions_apply_pre_defined'], ':
							</dt>
							<dd>
								<select name="predefined">
									<option value="">(', $txt['permissions_select_pre_defined'], ')</option>
									<option value="restrict">', $txt['permitgroups_restrict'], '</option>
									<option value="standard">', $txt['permitgroups_standard'], '</option>
									<option value="moderator">', $txt['permitgroups_moderator'], '</option>
									<option value="maintenance">', $txt['permitgroups_maintenance'], '</option>
								</select>
							</dd>
							<dt>
								', $txt['permissions_like_group'], ':
							</dt>
							<dd>
								<select name="copy_from">
									<option value="empty">(', $txt['permissions_select_membergroup'], ')</option>';
		foreach ($context['groups'] as $group)
		{
			if ($group['id'] != 1)
				echo '
									<option value="', $group['id'], '">', $group['name'], '</option>';
		}

		echo '
								</select>
							</dd>
							<dt>
								<select name="add_remove">
									<option value="add">', $txt['permissions_add'], '...</option>
									<option value="clear">', $txt['permissions_remove'], '...</option>';
		if (!empty($modSettings['permission_enable_deny']))
			echo '
									<option value="deny">', $txt['permissions_deny'], '...</option>';
		echo '
								</select>
							</dt>
							<dd style="overflow:auto;">
								<select name="permissions">
									<option value="">(', $txt['permissions_select_permission'], ')</option>';
		foreach ($context['permissions'] as $permissionType)
		{
			if ($permissionType['id'] == 'membergroup' && !empty($context['profile']))
				continue;

			foreach ($permissionType['columns'] as $column)
			{
				foreach ($column as $permissionGroup)
				{
					if ($permissionGroup['hidden'])
						continue;

					echo '
									<option value="" disabled="disabled">[', $permissionGroup['name'], ']</option>';
					foreach ($permissionGroup['permissions'] as $perm)
					{
						if ($perm['hidden'])
							continue;

						if ($perm['has_own_any'])
							echo '
									<option value="', $permissionType['id'], '/', $perm['own']['id'], '">&nbsp;&nbsp;&nbsp;', $perm['name'], ' (', $perm['own']['name'], ')</option>
									<option value="', $permissionType['id'], '/', $perm['any']['id'], '">&nbsp;&nbsp;&nbsp;', $perm['name'], ' (', $perm['any']['name'], ')</option>';
						else
							echo '
									<option value="', $permissionType['id'], '/', $perm['id'], '">&nbsp;&nbsp;&nbsp;', $perm['name'], '</option>';
					}
				}
			}
		}
		echo '
								</select>
							</dd>
						</dl>
					</fieldset>
					<input type="submit" value="', $txt['permissions_set_permissions'], '" onclick="return checkSubmit();" class="right_submit" />
				</div>
			</div>';

		// Javascript for the advanced stuff.
		echo '
	<script><!-- // --><![CDATA[
		var oPermissionsPanelToggle = new elk_Toggle({
			bToggleEnabled: true,
			bCurrentlyCollapsed: ', empty($context['admin_preferences']['app']) ? 'true' : 'false', ',
			aSwappableContainers: [
				\'permissions_panel_advanced\'
			],
			aSwapImages: [
				{
					sId: \'permissions_panel_toggle\',
					srcExpanded: elk_images_url + \'/collapse.png\',
					altExpanded: ', JavaScriptEscape($txt['upshrink_description']), ',
					srcCollapsed: elk_images_url + \'/expand.png\',
					altCollapsed: ', JavaScriptEscape($txt['upshrink_description']), '
				}
			],
			aSwapLinks: [
				{
					sId: \'permissions_panel_link\',
					msgExpanded: ', JavaScriptEscape($txt['permissions_advanced_options']), ',
					msgCollapsed: ', JavaScriptEscape($txt['permissions_advanced_options']), '
				}
			],
			oThemeOptions: {
				bUseThemeSettings: ', $context['user']['is_guest'] ? 'false' : 'true', ',
				sOptionName: \'admin_preferences\',
				sSessionVar: elk_session_var,
				sSessionId: elk_session_id,
				sThemeId: \'1\',
				sAdditionalVars: \';admin_key=app\'
			}
		});';

		echo '

		function checkSubmit()
		{
			if ((document.forms.permissionForm.predefined.value != "" && (document.forms.permissionForm.copy_from.value != "empty" || document.forms.permissionForm.permissions.value != "")) || (document.forms.permissionForm.copy_from.value != "empty" && document.forms.permissionForm.permissions.value != ""))
			{
				alert("', $txt['permissions_only_one_option'], '");
				return false;
			}
			if (document.forms.permissionForm.predefined.value == "" && document.forms.permissionForm.copy_from.value == "" && document.forms.permissionForm.permissions.value == "")
			{
				alert("', $txt['permissions_no_action'], '");
				return false;
			}
			if (document.forms.permissionForm.permissions.value != "" && document.forms.permissionForm.add_remove.value == "deny")
				return confirm("', $txt['permissions_deny_dangerous'], '");

			return true;
		}
	// ]]></script>';

		if (!empty($context['profile']))
			echo '
			<input type="hidden" name="pid" value="', $context['profile']['id'], '" />';

		echo '
			<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
			<input type="hidden" name="', $context['admin-mpq_token_var'], '" value="', $context['admin-mpq_token'], '" />';
	}
	else
		echo '
			</table>';

	echo '
		</form>
	</div>';
}

function template_by_board()
{
	global $context, $scripturl, $txt;

	echo '
	<div id="admincenter">
		<form id="admin_form_wrapper" action="', $scripturl, '?action=admin;area=permissions;sa=board" method="post" accept-charset="UTF-8">
			<div class="cat_bar">
				<h3 class="catbg">', $txt['permissions_boards'], '</h3>
			</div>
			<div class="information">
				', $txt['permissions_boards_desc'], '
			</div>

			<div class="title_bar">
				<h3 id="board_permissions" class="titlebg flow_hidden">
					<span class="perm_name floatleft">', $txt['board_name'], '</span>
					<span class="perm_profile floatleft">', $txt['permission_profile'], '</span>';

	if (!$context['edit_all'])
		echo '
					<span class="floatright">
						<a class="button_link" href="', $scripturl, '?action=admin;area=permissions;sa=board;edit;', $context['session_var'], '=', $context['session_id'], '">', $txt['permissions_board_all'], '</a>
					</span>';

	echo '
				</h3>
			</div>';

	foreach ($context['categories'] as $category)
	{
		echo '
			<div class="title_bar">
				<h3 class="titlebg"><strong>', $category['name'], '</strong></h3>
			</div>';

		if (!empty($category['boards']))
			echo '
			<div class="windowbg">
				<div class="content">
					<ul class="perm_boards flow_hidden">';

		$alternate = false;

		foreach ($category['boards'] as $board)
		{
			$alternate = !$alternate;

			echo '

						<li class="flow_hidden' ,' windowbg', $alternate ? '' : '2','">
							<span class="perm_board floatleft">
								<a href="', $scripturl, '?action=admin;area=manageboards;sa=board;boardid=', $board['id'], ';rid=permissions;', $context['session_var'], '=', $context['session_id'], '">', str_repeat('-', $board['child_level']), ' ', $board['name'], '</a>
							</span>
							<span class="perm_boardprofile floatleft">';

			if ($context['edit_all'])
			{
				echo '
								<select name="boardprofile[', $board['id'], ']">';

				foreach ($context['profiles'] as $id => $profile)
					echo '
									<option value="', $id, '" ', $id == $board['profile'] ? 'selected="selected"' : '', '>', $profile['name'], '</option>';

				echo '
								</select>';
			}
			else
				echo '
								<a href="', $scripturl, '?action=admin;area=permissions;sa=index;pid=', $board['profile'], ';', $context['session_var'], '=', $context['session_id'], '"> [', $board['profile_name'], ']</a>';

			echo '
							</span>
						</li>';
		}

		if (!empty($category['boards']))
			echo '
					</ul>
				</div>
			</div>';
	}

	echo '
			<div class="content">';

	if ($context['edit_all'])
		echo '
				<input type="submit" name="save_changes" value="', $txt['save'], '" class="right_submit" />';
	else
		echo '
				<a class="linkbutton_right" href="', $scripturl, '?action=admin;area=permissions;sa=board;edit;', $context['session_var'], '=', $context['session_id'], '">', $txt['permissions_board_all'], '</a>';

	echo '
				<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
				<input type="hidden" name="', $context['admin-mpb_token_var'], '" value="', $context['admin-mpb_token'], '" />
			</div>
		</form>
	</div>';
}

// Edit permission profiles (predefined).
function template_edit_profiles()
{
	global $context, $scripturl, $txt;

	echo '
	<div id="admin_form_wrapper">
		<form action="', $scripturl, '?action=admin;area=permissions;sa=profiles" method="post" accept-charset="UTF-8">
			<div class="cat_bar">
				<h3 class="catbg">', $txt['permissions_profile_edit'], '</h3>
			</div>

			<table class="table_grid">
				<thead>
					<tr class="table_head">
						<th>', $txt['permissions_profile_name'], '</th>
						<th>', $txt['permissions_profile_used_by'], '</th>
						<th style="width:5%', !empty($context['show_rename_boxes']) ? ';display:none"' : '"', ' >', $txt['delete'], '</th>
					</tr>
				</thead>
				<tbody>';

	$alternate = false;
	foreach ($context['profiles'] as $profile)
	{
		echo '
					<tr class="', $alternate ? 'windowbg' : 'windowbg2', '">
						<td>';

		if (!empty($context['show_rename_boxes']) && $profile['can_edit'])
			echo '
							<input type="text" name="rename_profile[', $profile['id'], ']" value="', $profile['name'], '" class="input_text" />';
		else
			echo '
							<a href="', $scripturl, '?action=admin;area=permissions;sa=index;pid=', $profile['id'], ';', $context['session_var'], '=', $context['session_id'], '">', $profile['name'], '</a>';

		echo '
						</td>
						<td>
							', !empty($profile['boards_text']) ? $profile['boards_text'] : $txt['permissions_profile_used_by_none'], '
						</td>
						<td class="centertext" ', !empty($context['show_rename_boxes']) ? 'style="display:none"' : '', '>
							<input type="checkbox" name="delete_profile[]" value="', $profile['id'], '" ', $profile['can_delete'] ? '' : 'disabled="disabled"', ' class="input_check" />
						</td>
					</tr>';
		$alternate = !$alternate;
	}

	echo '
				</tbody>
			</table>
			<div class="submitbutton">
				<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
				<input type="hidden" name="', $context['admin-mpp_token_var'], '" value="', $context['admin-mpp_token'], '" />';

	if ($context['can_edit_something'])
		echo '
				<input type="submit" name="rename" value="', empty($context['show_rename_boxes']) ? $txt['permissions_profile_rename'] : $txt['permissions_commit'], '" class="button_submit" />';

	echo '
				<input type="submit" name="delete" value="', $txt['quickmod_delete_selected'], '" class="button_submit" ', !empty($context['show_rename_boxes']) ? ' style="display:none"' : '', '/>
			</div>
		</form>
		<br />
		<form action="', $scripturl, '?action=admin;area=permissions;sa=profiles" method="post" accept-charset="UTF-8">
			<div class="cat_bar">
				<h3 class="catbg">', $txt['permissions_profile_new'], '</h3>
			</div>
			<div class="windowbg">
				<div class="content">
					<dl class="settings">
						<dt>
							<strong>', $txt['permissions_profile_name'], ':</strong>
						</dt>
						<dd>
							<input type="text" name="profile_name" value="" class="input_text" />
						</dd>
						<dt>
							<strong>', $txt['permissions_profile_copy_from'], ':</strong>
						</dt>
						<dd>
							<select name="copy_from">';

	foreach ($context['profiles'] as $id => $profile)
		echo '
								<option value="', $id, '">', $profile['name'], '</option>';

	echo '
							</select>
						</dd>
					</dl>
					<hr />
					<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
					<input type="hidden" name="', $context['admin-mpp_token_var'], '" value="', $context['admin-mpp_token'], '" />
					<input type="submit" name="create" value="', $txt['permissions_profile_new_create'], '" class="right_submit" />
				</div>
			</div>
		</form>
	</div>';
}

function template_modify_group()
{
	global $context, $scripturl, $txt, $modSettings;

	// Cannot be edited?
	if (!$context['profile']['can_modify'])
	{
		echo '
		<div class="errorbox">
			', sprintf($txt['permission_cannot_edit'], $scripturl . '?action=admin;area=permissions;sa=profiles'), '
		</div>';
	}
	else
	{
		echo '
		<script><!-- // --><![CDATA[
			window.elk_usedDeny = false;

			function warnAboutDeny()
			{
				if (window.elk_usedDeny)
					return confirm("', $txt['permissions_deny_dangerous'], '");
				else
					return true;
			}
		// ]]></script>';
	}

	echo '
	<div id="admincenter">
		<form id="admin_form_wrapper" action="', $scripturl, '?action=admin;area=permissions;sa=modify2;group=', $context['group']['id'], ';pid=', $context['profile']['id'], '" method="post" accept-charset="UTF-8" name="permissionForm" onsubmit="return warnAboutDeny();">';

	if (!empty($modSettings['permission_enable_deny']) && $context['group']['id'] != -1)
		echo '
			<div class="information">
				', $txt['permissions_option_desc'], '
			</div>';

	echo '
			<div class="cat_bar">
				<h3 class="catbg">';
	if ($context['permission_type'] == 'board')
		echo '
				', $txt['permissions_local_for'], ' &quot;', $context['group']['name'], '&quot; ', $txt['permissions_on'], ' &quot;', $context['profile']['name'], '&quot;';
	else
		echo '
				', $context['permission_type'] == 'membergroup' ? $txt['permissions_general'] : $txt['permissions_board'], ' - &quot;', $context['group']['name'], '&quot;';
	echo '
				</h3>
			</div>
			<div class="flow_hidden">';

	// Draw out the main bits.
	template_modify_group_classic($context['permission_type']);

	echo '
			</div>';

	// If this is general permissions also show the default profile.
	if ($context['permission_type'] == 'membergroup')
	{
		echo '
			<br />
			<div class="cat_bar">
				<h3 class="catbg">', $txt['permissions_board'], '</h3>
			</div>
			<div class="information">
				', $txt['permissions_board_desc'], '
			</div>
			<div class="flow_hidden">';

		template_modify_group_classic('board');

		echo '
			</div>';
	}

	if ($context['profile']['can_modify'])
		echo '
			<input type="submit" value="', $txt['permissions_commit'], '" class="right_submit" />';

	echo '
			<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
			<input type="hidden" name="', $context['admin-mp_token_var'], '" value="', $context['admin-mp_token'], '" />
		</form>
	</div>';
}

// The classic way of looking at permissions.
function template_modify_group_classic($type)
{
	global $context, $settings, $scripturl, $txt, $modSettings;

	$permission_type = &$context['permissions'][$type];
	$disable_field = $context['profile']['can_modify'] ? '' : 'disabled="disabled" ';

	echo '
				<div class="windowbg2">
					<div class="content">';

	foreach ($permission_type['columns'] as $column)
	{
		echo '
						<table style="width:49%" class="table_grid perm_classic floatleft">';

		foreach ($column as $permissionGroup)
		{
			if (empty($permissionGroup['permissions']))
				continue;

			// Are we likely to have something in this group to display or is it all hidden?
			$has_display_content = false;
			if (!$permissionGroup['hidden'])
			{
				// Before we go any further check we are going to have some data to print otherwise we just have a silly heading.
				foreach ($permissionGroup['permissions'] as $permission)
					if (!$permission['hidden'])
						$has_display_content = true;

				if ($has_display_content)
				{
					echo '
							<tr class="table_head">
								<th class="lefttext" colspan="2" style="width:100%">
									<strong class="smalltext">', $permissionGroup['name'], '</strong>
								</th>';

					if (empty($modSettings['permission_enable_deny']) || $context['group']['id'] == -1)
						echo '
								<th></th><th></th><th></th>';
					else
						echo '
								<th><div>', $txt['permissions_option_on'], '</div></th>
								<th><div>', $txt['permissions_option_off'], '</div></th>
								<th><div>', $txt['permissions_option_deny'], '</div></th>';
					echo '
							</tr>';
				}
			}

			$alternate = false;
			foreach ($permissionGroup['permissions'] as $permission)
			{
				// If it's hidden keep the last value.
				if ($permission['hidden'] || $permissionGroup['hidden'])
				{
					echo '
							<tr style="display: none;">
								<td>';

					if ($permission['has_own_any'])
					{
						// Guests can't have own permissions.
						if ($context['group']['id'] != -1)
							echo '
									<input type="hidden" name="perm[', $permission_type['id'], '][', $permission['own']['id'], ']" value="', $permission['own']['select'] == 'denied' && !empty($modSettings['permission_enable_deny']) ? 'deny' : $permission['own']['select'], '" />';

						echo '
									<input type="hidden" name="perm[', $permission_type['id'], '][', $permission['any']['id'], ']" value="', $permission['any']['select'] == 'denied' && !empty($modSettings['permission_enable_deny']) ? 'deny' : $permission['any']['select'], '" />';
					}
					else
						echo '
									<input type="hidden" name="perm[', $permission_type['id'], '][', $permission['id'], ']" value="', $permission['select'] == 'denied' && !empty($modSettings['permission_enable_deny']) ? 'deny' : $permission['select'], '" />';
					echo '
								</td>
							</tr>';
				}
				else
				{
					echo '
							<tr class="', $alternate ? 'windowbg' : 'windowbg2', '">
								<td style="width:10px">
									', $permission['show_help'] ? '<a href="' . $scripturl . '?action=quickhelp;help=permissionhelp_' . $permission['id'] . '" onclick="return reqOverlayDiv(this.href);" class="help"><img src="' . $settings['images_url'] . '/helptopics.png" alt="' . $txt['help'] . '" /></a>' : '', '
								</td>';

					if ($permission['has_own_any'])
					{
						echo '
								<td class="lefttext" colspan="4" style="width:100%">', $permission['name'], '</td>
							</tr>
							<tr class="', $alternate ? 'windowbg' : 'windowbg2', '">';

						// Guests can't do their own thing.
						if ($context['group']['id'] != -1)
						{
							echo '
								<td></td>
								<td class="smalltext righttext" style="width:100%">', $permission['own']['name'], ':</td>';

							if (empty($modSettings['permission_enable_deny']))
								echo '
								<td colspan="3">
									<input type="checkbox" name="perm[', $permission_type['id'], '][', $permission['own']['id'], ']"', $permission['own']['select'] == 'on' ? ' checked="checked"' : '', ' value="on" id="', $permission['own']['id'], '_on" class="input_check" ', $disable_field, '/>
								</td>';
							else
								echo '
								<td style="width:10px">
									<input type="radio" name="perm[', $permission_type['id'], '][', $permission['own']['id'], ']"', $permission['own']['select'] == 'on' ? ' checked="checked"' : '', ' value="on" id="', $permission['own']['id'], '_on" class="input_radio" ', $disable_field, '/>
								</td>
								<td style="width:10px">
									<input type="radio" name="perm[', $permission_type['id'], '][', $permission['own']['id'], ']"', $permission['own']['select'] == 'off' ? ' checked="checked"' : '', ' value="off" class="input_radio" ', $disable_field, '/>
								</td>
								<td style="width:10px">
									<input type="radio" name="perm[', $permission_type['id'], '][', $permission['own']['id'], ']"', $permission['own']['select'] == 'denied' ? ' checked="checked"' : '', ' value="deny" class="input_radio" ', $disable_field, '/>
								</td>';

							echo '
							</tr>
							<tr class="', $alternate ? 'windowbg' : 'windowbg2', '">';
						}

						echo '
								<td></td>
								<td class="smalltext righttext" style="width:100%">', $permission['any']['name'], ':</td>';

						if (empty($modSettings['permission_enable_deny']) || $context['group']['id'] == -1)
							echo '
								<td colspan="3">
									<input type="checkbox" name="perm[', $permission_type['id'], '][', $permission['any']['id'], ']"', $permission['any']['select'] == 'on' ? ' checked="checked"' : '', ' value="on" class="input_check" ', $disable_field, '/>
								</td>';
						else
							echo '
								<td>
									<input type="radio" name="perm[', $permission_type['id'], '][', $permission['any']['id'], ']"', $permission['any']['select'] == 'on' ? ' checked="checked"' : '', ' value="on" onclick="document.forms.permissionForm.', $permission['own']['id'], '_on.checked = true;" class="input_radio" ', $disable_field, '/>
								</td>
								<td>
									<input type="radio" name="perm[', $permission_type['id'], '][', $permission['any']['id'], ']"', $permission['any']['select'] == 'off' ? ' checked="checked"' : '', ' value="off" class="input_radio" ', $disable_field, '/>
								</td>
								<td>
									<input type="radio" name="perm[', $permission_type['id'], '][', $permission['any']['id'], ']"', $permission['any']['select']== 'denied' ? ' checked="checked"' : '', ' value="deny" id="', $permission['any']['id'], '_deny" onclick="window.elk_usedDeny = true;" class="input_radio" ', $disable_field, '/>
								</td>';

						echo '
							</tr>';
					}
					else
					{
						echo '
								<td class="lefttext" style="width:100%">', $permission['name'], '</td>';

						if (empty($modSettings['permission_enable_deny']) || $context['group']['id'] == -1)
							echo '
								<td colspan="3">
									<input type="checkbox" name="perm[', $permission_type['id'], '][', $permission['id'], ']"', $permission['select'] == 'on' ? ' checked="checked"' : '', ' value="on" class="input_check" ', $disable_field, '/>
								</td>';
						else
							echo '
								<td>
									<input type="radio" name="perm[', $permission_type['id'], '][', $permission['id'], ']"', $permission['select'] == 'on' ? ' checked="checked"' : '', ' value="on" class="input_radio" ', $disable_field, '/>
								</td>
								<td>
									<input type="radio" name="perm[', $permission_type['id'], '][', $permission['id'], ']"', $permission['select'] == 'off' ? ' checked="checked"' : '', ' value="off" class="input_radio" ', $disable_field, '/>
								</td>
								<td>
									<input type="radio" name="perm[', $permission_type['id'], '][', $permission['id'], ']"', $permission['select'] == 'denied' ? ' checked="checked"' : '', ' value="deny" onclick="window.elk_usedDeny = true;" class="input_radio" ', $disable_field, '/>
								</td>';

						echo '
							</tr>';
					}
				}
				$alternate = !$alternate;
			}

			if (!$permissionGroup['hidden'] && $has_display_content)
				echo '
							<tr class="windowbg2">
								<td colspan="5" style="width:100%"><!--separator--></td>
							</tr>';
		}
	echo '
						</table>';
	}
	echo '
				</div>
			</div>';
}

function template_inline_permissions()
{
	global $context, $txt, $modSettings;

	echo '
		<fieldset id="', $context['current_permission'], '">
			<legend><a href="javascript:void(0);" onclick="document.getElementById(\'', $context['current_permission'], '\').style.display = \'none\';document.getElementById(\'', $context['current_permission'], '_groups_link\').style.display = \'block\'; return false;">', $txt['avatar_select_permission'], '</a></legend>';

	if (empty($modSettings['permission_enable_deny']))
		echo '
			<ul class="permission_groups">';
	else
		echo '
			<div class="information">', $txt['permissions_option_desc'], '</div>
			<dl class="settings">
				<dt>
					<span class="perms"><strong>', $txt['permissions_option_on'], '</strong></span>
					<span class="perms"><strong>', $txt['permissions_option_off'], '</strong></span>
					<span class="perms" style="color: red;"><strong>', $txt['permissions_option_deny'], '</strong></span>
				</dt>
				<dd>
				</dd>';

	foreach ($context['member_groups'] as $group)
	{
		if (!empty($modSettings['permission_enable_deny']))
			echo '
				<dt>';
		else
			echo '
				<li>';

		if (empty($modSettings['permission_enable_deny']))
			echo '
					<input type="checkbox" name="', $context['current_permission'], '[', $group['id'], ']" value="on"', $group['status'] == 'on' ? ' checked="checked"' : '', ' class="input_check" />';
		else
			echo '
					<span class="perms"><input type="radio" name="', $context['current_permission'], '[', $group['id'], ']" value="on"', $group['status'] == 'on' ? ' checked="checked"' : '', ' class="input_radio" /></span>
					<span class="perms"><input type="radio" name="', $context['current_permission'], '[', $group['id'], ']" value="off"', $group['status'] == 'off' ? ' checked="checked"' : '', ' class="input_radio" /></span>
					<span class="perms"><input type="radio" name="', $context['current_permission'], '[', $group['id'], ']" value="deny"', $group['status'] == 'deny' ? ' checked="checked"' : '', ' class="input_radio" /></span>';

		if (!empty($modSettings['permission_enable_deny']))
			echo '
				</dt>
				<dd>
					<span', $group['is_postgroup'] ? ' style="font-style: italic;"' : '', '>', $group['name'], '</span>
				</dd>';
		else
			echo '
					<span', $group['is_postgroup'] ? ' style="font-style: italic;"' : '', '>', $group['name'], '</span>
				</li>';
	}

	if (empty($modSettings['permission_enable_deny']))
		echo '
			</ul>';
	else
		echo '
			</dl>';

	echo '
		</fieldset>

		<a href="javascript:void(0);" onclick="document.getElementById(\'', $context['current_permission'], '\').style.display = \'block\'; document.getElementById(\'', $context['current_permission'], '_groups_link\').style.display = \'none\'; return false;" id="', $context['current_permission'], '_groups_link" style="display: none;">[ ', $txt['avatar_select_permission'], ' ]</a>

		<script><!-- // --><![CDATA[
			document.getElementById("', $context['current_permission'], '").style.display = "none";
			document.getElementById("', $context['current_permission'], '_groups_link").style.display = "";
		// ]]></script>';
}

// Edit post moderation permissions.
function template_postmod_permissions()
{
	global $context, $settings, $scripturl, $txt, $modSettings;

	echo '
	<div id="admin_form_wrapper">
		<form action="', $scripturl, '?action=admin;area=permissions;sa=postmod;', $context['session_var'], '=', $context['session_id'], '" method="post" name="postmodForm" id="postmodForm" accept-charset="UTF-8">
			<div class="cat_bar">
				<h3 class="catbg">', $txt['permissions_post_moderation'], '</h3>
			</div>';

	// Got advanced permissions - if so warn!
	if (!empty($modSettings['permission_enable_deny']))
		echo '
				<div class="information">', $txt['permissions_post_moderation_deny_note'], '</div>';

	echo '
				<div class="submitbutton">
					', $txt['permissions_post_moderation_select'], ':
					<select name="pid" onchange="document.forms.postmodForm.submit();">';

	foreach ($context['profiles'] as $profile)
		if ($profile['can_modify'])
			echo '
						<option value="', $profile['id'], '" ', $profile['id'] == $context['current_profile'] ? 'selected="selected"' : '', '>', $profile['name'], '</option>';

	echo '
					</select>
					<input type="submit" value="', $txt['go'], '" class="button_submit" />
				</div>
				<table class="table_grid">
				<thead>
					<tr class="table_head">
						<th></th>
						<th class="centertext" colspan="3">
							', $txt['permissions_post_moderation_new_topics'], '
						</th>
						<th class="centertext" colspan="3">
							', $txt['permissions_post_moderation_replies_own'], '
						</th>
						<th class="centertext" colspan="3">
							', $txt['permissions_post_moderation_replies_any'], '
						</th>
						<th class="centertext" colspan="3">
							', $txt['permissions_post_moderation_attachments'], '
						</th>
					</tr>
					<tr class="titlebg">
						<th style="width:30%">
							', $txt['permissions_post_moderation_group'], '
						</th>
						<th><img src="', $settings['default_images_url'], '/admin/post_moderation_allow.png" alt="', $txt['permissions_post_moderation_allow'], '" title="', $txt['permissions_post_moderation_allow'], '" /></th>
						<th><img src="', $settings['default_images_url'], '/admin/post_moderation_moderate.png" alt="', $txt['permissions_post_moderation_moderate'], '" title="', $txt['permissions_post_moderation_moderate'], '" /></th>
						<th><img src="', $settings['default_images_url'], '/admin/post_moderation_deny.png" alt="', $txt['permissions_post_moderation_disallow'], '" title="', $txt['permissions_post_moderation_disallow'], '" /></th>
						<th><img src="', $settings['default_images_url'], '/admin/post_moderation_allow.png" alt="', $txt['permissions_post_moderation_allow'], '" title="', $txt['permissions_post_moderation_allow'], '" /></th>
						<th><img src="', $settings['default_images_url'], '/admin/post_moderation_moderate.png" alt="', $txt['permissions_post_moderation_moderate'], '" title="', $txt['permissions_post_moderation_moderate'], '" /></th>
						<th><img src="', $settings['default_images_url'], '/admin/post_moderation_deny.png" alt="', $txt['permissions_post_moderation_disallow'], '" title="', $txt['permissions_post_moderation_disallow'], '" /></th>
						<th><img src="', $settings['default_images_url'], '/admin/post_moderation_allow.png" alt="', $txt['permissions_post_moderation_allow'], '" title="', $txt['permissions_post_moderation_allow'], '" /></th>
						<th><img src="', $settings['default_images_url'], '/admin/post_moderation_moderate.png" alt="', $txt['permissions_post_moderation_moderate'], '" title="', $txt['permissions_post_moderation_moderate'], '" /></th>
						<th><img src="', $settings['default_images_url'], '/admin/post_moderation_deny.png" alt="', $txt['permissions_post_moderation_disallow'], '" title="', $txt['permissions_post_moderation_disallow'], '" /></th>
						<th><img src="', $settings['default_images_url'], '/admin/post_moderation_allow.png" alt="', $txt['permissions_post_moderation_allow'], '" title="', $txt['permissions_post_moderation_allow'], '" /></th>
						<th><img src="', $settings['default_images_url'], '/admin/post_moderation_moderate.png" alt="', $txt['permissions_post_moderation_moderate'], '" title="', $txt['permissions_post_moderation_moderate'], '" /></th>
						<th><img src="', $settings['default_images_url'], '/admin/post_moderation_deny.png" alt="', $txt['permissions_post_moderation_disallow'], '" title="', $txt['permissions_post_moderation_disallow'], '" /></th>
					</tr>
				</thead>
				<tbody>';

	foreach ($context['profile_groups'] as $group)
	{
		echo '
					<tr>
						<td style="width:40%" class="windowbg">
							<span ', ($group['color'] ? 'style="color: ' . $group['color'] . '"' : ''), '>', $group['name'], '</span>';
			if (!empty($group['children']))
				echo '
							<br /><span class="smalltext">', $txt['permissions_includes_inherited'], ': &quot;', implode('&quot;, &quot;', $group['children']), '&quot;</span>';

			echo '
						</td>
						<td class="windowbg2 centertext"><input type="radio" name="new_topic[', $group['id'], ']" value="allow" ', $group['new_topic'] == 'allow' ? 'checked="checked"' : '', ' class="input_radio" /></td>
						<td class="windowbg2 centertext"><input type="radio" name="new_topic[', $group['id'], ']" value="moderate" ', $group['new_topic'] == 'moderate' ? 'checked="checked"' : '', ' class="input_radio" /></td>
						<td class="windowbg2 centertext"><input type="radio" name="new_topic[', $group['id'], ']" value="disallow" ', $group['new_topic'] == 'disallow' ? 'checked="checked"' : '', ' class="input_radio" /></td>
						<td class="windowbg centertext"><input type="radio" name="replies_own[', $group['id'], ']" value="allow" ', $group['replies_own'] == 'allow' ? 'checked="checked"' : '', ' class="input_radio" /></td>
						<td class="windowbg centertext"><input type="radio" name="replies_own[', $group['id'], ']" value="moderate" ', $group['replies_own'] == 'moderate' ? 'checked="checked"' : '', ' class="input_radio" /></td>
						<td class="windowbg centertext"><input type="radio" name="replies_own[', $group['id'], ']" value="disallow" ', $group['replies_own'] == 'disallow' ? 'checked="checked"' : '', ' class="input_radio" /></td>
						<td class="windowbg2 centertext"><input type="radio" name="replies_any[', $group['id'], ']" value="allow" ', $group['replies_any'] == 'allow' ? 'checked="checked"' : '', ' class="input_radio" /></td>
						<td class="windowbg2 centertext"><input type="radio" name="replies_any[', $group['id'], ']" value="moderate" ', $group['replies_any'] == 'moderate' ? 'checked="checked"' : '', ' class="input_radio" /></td>
						<td class="windowbg2 centertext"><input type="radio" name="replies_any[', $group['id'], ']" value="disallow" ', $group['replies_any'] == 'disallow' ? 'checked="checked"' : '', ' class="input_radio" /></td>
						<td class="windowbg centertext"><input type="radio" name="attachment[', $group['id'], ']" value="allow" ', $group['attachment'] == 'allow' ? 'checked="checked"' : '', ' class="input_radio" /></td>
						<td class="windowbg centertext"><input type="radio" name="attachment[', $group['id'], ']" value="moderate" ', $group['attachment'] == 'moderate' ? 'checked="checked"' : '', ' class="input_radio" /></td>
						<td class="windowbg centertext"><input type="radio" name="attachment[', $group['id'], ']" value="disallow" ', $group['attachment'] == 'disallow' ? 'checked="checked"' : '', ' class="input_radio" /></td>
					</tr>';
	}

	echo '
				</tbody>
			</table>
			<div class="submitbutton">
				<input type="submit" name="save_changes" value="', $txt['permissions_commit'], '" class="button_submit" />
				<input type="hidden" name="', $context['admin-mppm_token_var'], '" value="', $context['admin-mppm_token'], '" />
			</div>
		</form>
		<p class="smalltext" style="padding-left: 10px;">
			<strong>', $txt['permissions_post_moderation_legend'], ':</strong><br />
			<img src="', $settings['default_images_url'], '/admin/post_moderation_allow.png" alt="', $txt['permissions_post_moderation_allow'], '" /> - ', $txt['permissions_post_moderation_allow'], '<br />
			<img src="', $settings['default_images_url'], '/admin/post_moderation_moderate.png" alt="', $txt['permissions_post_moderation_moderate'], '" /> - ', $txt['permissions_post_moderation_moderate'], '<br />
			<img src="', $settings['default_images_url'], '/admin/post_moderation_deny.png" alt="', $txt['permissions_post_moderation_disallow'], '" /> - ', $txt['permissions_post_moderation_disallow'], '
		</p>
	</div>';
}