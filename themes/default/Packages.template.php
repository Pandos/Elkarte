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

function template_main()
{
}

function template_view_package()
{
	global $context, $settings, $txt, $scripturl;

	echo '
	<div id="admincenter">
		<div class="cat_bar">
			<h3 class="catbg">', $txt[($context['uninstalling'] ? 'un' : '') . 'install_mod'], '</h3>
		</div>
		<div class="information">';

	if ($context['is_installed'])
		echo '
			<strong>', $txt['package_installed_warning1'], '</strong><br />
			<br />
			', $txt['package_installed_warning2'], '<br />
			<br />';

	echo $txt['package_installed_warning3'], '
		</div>';

	// Do errors exist in the install? If so light them up like a christmas tree.
	if ($context['has_failure'])
	{
		echo '
		<div class="errorbox">
			', sprintf($txt['package_will_fail_title'], $txt['package_' . ($context['uninstalling'] ? 'uninstall' : 'install')]), '<br />
			', sprintf($txt['package_will_fail_warning'], $txt['package_' . ($context['uninstalling'] ? 'uninstall' : 'install')]),
			!empty($context['failure_details']) ? '<br /><br /><strong>' . $context['failure_details'] . '</strong>' : '', '
		</div>';
	}

	// Display the package readme if one exists
	if (isset($context['package_readme']))
	{
		echo '
			<div class="cat_bar">
				<h3 class="catbg">', $txt['package_' . ($context['uninstalling'] ? 'un' : '') . 'install_readme'], '</h3>
			</div>
			<div class="windowbg2">
				<div class="content">
					', $context['package_readme'], '
					<span class="floatright">', $txt['package_available_readme_language'], '
						<select name="readme_language" id="readme_language" onchange="if (this.options[this.selectedIndex].value) window.location.href = elk_prepareScriptUrl(elk_scripturl + \'', '?action=admin;area=packages;sa=', $context['uninstalling'] ? 'uninstall' : 'install', ';package=', $context['filename'], ';readme=\' + this.options[this.selectedIndex].value + \';license=\' + get_selected(\'license_language\'));">';
							foreach ($context['readmes'] as $a => $b)
								echo '<option value="', $b, '"', $a === 'selected' ? ' selected="selected"' : '', '>', $b == 'default' ? $txt['package_readme_default'] : ucfirst($b), '</option>';
			echo '
						</select>
					</span>
				</div>
			</div>
			<br />';
	}

	// Did they specify a license to display?
	if (isset($context['package_license']))
	{
		echo '
			<div class="cat_bar">
				<h3 class="catbg">', $txt['package_install_license'], '</h3>
			</div>
			<div class="windowbg2">
				<div class="content">
					', $context['package_license'], '
					<span class="floatright">', $txt['package_available_license_language'], '
						<select name="license_language" id="license_language" onchange="if (this.options[this.selectedIndex].value) window.location.href = elk_prepareScriptUrl(elk_scripturl + \'', '?action=admin;area=packages;sa=install', ';package=', $context['filename'], ';license=\' + this.options[this.selectedIndex].value + \';readme=\' + get_selected(\'readme_language\'));">';
							foreach ($context['licenses'] as $a => $b)
								echo '<option value="', $b, '"', $a === 'selected' ? ' selected="selected"' : '', '>', $b == 'default' ? $txt['package_license_default'] : ucfirst($b), '</option>';
			echo '
						</select>
					</span>
				</div>
			</div>
			<br />';
	}

	echo '
		<form action="', $context['post_url'], '" onsubmit="submitonce(this);" method="post" accept-charset="UTF-8">
			<div class="cat_bar">
				<h3 class="catbg">
					', $context['uninstalling'] ? $txt['package_uninstall_actions'] : $txt['package_install_actions'], ' &quot;', $context['package_name'], '&quot;
				</h3>
			</div>';

	// Are there data changes to be removed?
	if ($context['uninstalling'] && !empty($context['database_changes']))
	{
		echo '
			<div class="windowbg2">
				<div class="content">
					<label for="do_db_changes"><input type="checkbox" name="do_db_changes" id="do_db_changes" class="input_check" />', $txt['package_db_uninstall'], '</label> [<a href="#" onclick="return swap_database_changes();">', $txt['package_db_uninstall_details'], '</a>]
					<div id="db_changes_div">
						', $txt['package_db_uninstall_actions'], ':
						<ul>';

		foreach ($context['database_changes'] as $change)
			echo '
							<li>', $change, '</li>';
		echo '
						</ul>
					</div>
				</div>
			</div>';
	}

	echo '
			<div class="information">';

	if (empty($context['actions']) && empty($context['database_changes']))
		echo '
				<br />
				<div class="errorbox">
					', $txt['corrupt_compatible'], '
				</div>
			</div>';
	else
	{
		echo '
					', $txt['perform_actions'], '
			</div>
			<table class="table_grid">
			<thead>
				<tr class="table_head">
					<th scope="col" style="width:20px"></th>
					<th scope="col" style="width:30px"></th>
					<th scope="col" class="lefttext">', $txt['package_install_type'], '</th>
					<th scope="col" class="lefttext" style="width:50%">', $txt['package_install_action'], '</th>
					<th class="lefttext" scope="col" style="width:20%">', $txt['package_install_desc'], '</th>
				</tr>
			</thead>
			<tbody>';

		$alternate = true;
		$i = 1;
		$action_num = 1;
		$js_operations = array();
		foreach ($context['actions'] as $packageaction)
		{
			// Did we pass or fail?  Need to now for later on.
			$js_operations[$action_num] = isset($packageaction['failed']) ? $packageaction['failed'] : 0;

			echo '
				<tr class="windowbg', $alternate ? '' : '2', '">
					<td>', isset($packageaction['operations']) ? '<img id="operation_img_' . $action_num . '" src="' . $settings['images_url'] . '/selected_open.png" alt="*" style="display: none;" />' : '', '</td>
					<td>', $i++, '.</td>
					<td>', $packageaction['type'], '</td>
					<td>', $packageaction['action'], '</td>
					<td>', $packageaction['description'], '</td>
				</tr>';

			// Is there water on the knee? Operation!
			if (isset($packageaction['operations']))
			{
				echo '
				<tr id="operation_', $action_num, '">
					<td colspan="5" class="standard_row">
						<table class="table_grid">';

				// Show the operations.
				$alternate2 = true;
				$operation_num = 1;
				foreach ($packageaction['operations'] as $operation)
				{
					// Determine the position text.
					$operation_text = $operation['position'] == 'replace' ? 'operation_replace' : ($operation['position'] == 'before' ? 'operation_after' : 'operation_before');

					echo '
							<tr class="windowbg', $alternate2 ? '' : '2', '">
								<td style="width:0"></td>
								<td style="width:30px" class="smalltext"><a href="' . $scripturl . '?action=admin;area=packages;sa=showoperations;operation_key=', $operation['operation_key'], ';package=', $_REQUEST['package'], ';filename=', $operation['filename'], ($operation['is_boardmod'] ? ';boardmod' : ''), (isset($_REQUEST['sa']) && $_REQUEST['sa'] == 'uninstall' ? ';reverse' : ''), '" onclick="return reqWin(this.href, 680, 400, false);"><img src="', $settings['default_images_url'], '/admin/package_ops.png" alt="" /></a></td>
								<td style="width:30px" class="smalltext">', $operation_num, '.</td>
								<td style="width:23%" class="smalltext">', $txt[$operation_text], '</td>
								<td style="width:50%" class="smalltext">', $operation['action'], '</td>
								<td style="width:20%" class="smalltext">', $operation['description'], !empty($operation['ignore_failure']) ? ' (' . $txt['operation_ignore'] . ')' : '', '</td>
							</tr>';

					$operation_num++;
					$alternate2 = !$alternate2;
				}

				echo '
						</table>
					</td>
				</tr>';

				// Increase it.
				$action_num++;
			}
			$alternate = !$alternate;
		}
					echo '
			</tbody>
			</table>
			';

		// What if we have custom themes we can install into? List them too!
		if (!empty($context['theme_actions']))
		{
			echo '
			<br />
			<div class="cat_bar">
				<h3 class="catbg">
					', $context['uninstalling'] ? $txt['package_other_themes_uninstall'] : $txt['package_other_themes'], '
				</h3>
			</div>
			<div id="custom_changes">
				<div class="information">
					', $txt['package_other_themes_desc'], '
				</div>
				<table class="table_grid">';

			// Loop through each theme and display it's name, and then it's details.
			foreach ($context['theme_actions'] as $id => $theme)
			{
				// Pass?
				$js_operations[$action_num] = !empty($theme['has_failure']);

				echo '
					<tr class="titlebg">
						<td></td>
						<td class="centertext">';

				if (!empty($context['themes_locked']))
					echo '
							<input type="hidden" name="custom_theme[]" value="', $id, '" />';

				echo '
							<input type="checkbox" name="custom_theme[]" id="custom_theme_', $id, '" value="', $id, '" class="input_check" onclick="', (!empty($theme['has_failure']) ? 'if (this.form.custom_theme_' . $id . '.checked && !confirm(\'' . $txt['package_theme_failure_warning'] . '\')) return false;' : ''), 'invertAll(this, this.form, \'dummy_theme_', $id, '\', true);" ', !empty($context['themes_locked']) ? 'disabled="disabled" checked="checked"' : '', '/>
						</td>
						<td colspan="3">
							', $theme['name'], '
						</td>
					</tr>';

				foreach ($theme['actions'] as $action)
				{
					echo '
					<tr class="windowbg', $alternate ? '' : '2', '">
						<td>', isset($packageaction['operations']) ? '<img id="operation_img_' . $action_num . '" src="' . $settings['images_url'] . '/selected_open.png" alt="*" style="display: none;" />' : '', '</td>
						<td class="centertext" style="width:30px">
							<input type="checkbox" name="theme_changes[]" value="', !empty($action['value']) ? $action['value'] : '', '" id="dummy_theme_', $id, '" class="input_check" ', (!empty($action['not_mod']) ? '' : 'disabled="disabled"'), ' ', !empty($context['themes_locked']) ? 'checked="checked"' : '', '/>
						</td>
						<td>', $action['type'], '</td>
						<td style="width:50%">', $action['action'], '</td>
						<td style="width:20%"><strong>', $action['description'], '</strong></td>
					</tr>';

					// Is there water on the knee? Operation!
					if (isset($action['operations']))
					{
						echo '
					<tr id="operation_', $action_num, '">
						<td colspan="5" class="standard_row">
							<table class="table_grid">';

						$alternate2 = true;
						$operation_num = 1;
						foreach ($action['operations'] as $operation)
						{
							// Determine the possition text.
							$operation_text = $operation['position'] == 'replace' ? 'operation_replace' : ($operation['position'] == 'before' ? 'operation_after' : 'operation_before');

							echo '
								<tr class="windowbg', $alternate2 ? '' : '2', '">
									<td style="width:0"></td>
									<td style="width:30px" class="smalltext"><a href="' . $scripturl . '?action=admin;area=packages;sa=showoperations;operation_key=', $operation['operation_key'], ';package=', $_REQUEST['package'], ';filename=', $operation['filename'], ($operation['is_boardmod'] ? ';boardmod' : ''), (isset($_REQUEST['sa']) && $_REQUEST['sa'] == 'uninstall' ? ';reverse' : ''), '" onclick="return reqWin(this.href, 600, 400, false);"><img src="', $settings['default_images_url'], '/admin/package_ops.png" alt="" /></a></td>
									<td style="width:30px" class="smalltext">', $operation_num, '.</td>
									<td style="width:23%" class="smalltext">', $txt[$operation_text], '</td>
									<td style="width:50%" class="smalltext">', $operation['action'], '</td>
									<td style="width:20%" class="smalltext">', $operation['description'], !empty($operation['ignore_failure']) ? ' (' . $txt['operation_ignore'] . ')' : '', '</td>
								</tr>';
							$operation_num++;
							$alternate2 = !$alternate2;
						}

						echo '
							</table>
						</td>
					</tr>';

						// Increase it.
						$action_num++;
					}
				}

				$alternate = !$alternate;
			}

			echo '
				</table>
			</div>';
		}
	}

	// Are we effectively ready to install?
	if (!$context['ftp_needed'] && (!empty($context['actions']) || !empty($context['database_changes'])))
	{
		echo '
			<div class="submitbutton">
				<input type="submit" value="', $context['uninstalling'] ? $txt['package_uninstall_now'] : $txt['package_install_now'], '" onclick="return ', !empty($context['has_failure']) ? '(submitThisOnce(this) &amp;&amp; confirm(\'' . ($context['uninstalling'] ? $txt['package_will_fail_popup_uninstall'] : $txt['package_will_fail_popup']) . '\'))' : 'submitThisOnce(this)', ';" class="button_submit" />
			</div>';
	}
	// If we need ftp information then demand it!
	elseif ($context['ftp_needed'])
	{
		echo '
			<div class="cat_bar">
				<h3 class="catbg">', $txt['package_ftp_necessary'], '</h3>
			</div>
			<div>
				', template_control_chmod(), '
			</div>';
	}

	echo '
			<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />', (isset($context['form_sequence_number']) && !$context['ftp_needed']) ? '
			<input type="hidden" name="seqnum" value="' . $context['form_sequence_number'] . '" />' : '', '
		</form>
	</div>';

	// Toggle options.
	echo '
	<script><!-- // --><![CDATA[
		var aOperationElements = new Array();';

		// Operations.
		if (!empty($js_operations))
		{
			foreach ($js_operations as $key => $operation)
			{
				echo '
			aOperationElements[', $key, '] = new elk_Toggle({
				bToggleEnabled: true,
				bCurrentlyCollapsed: ', $operation ? 'false' : 'true', ',
				aSwappableContainers: [
					\'operation_', $key, '\'
				],
				aSwapImages: [
					{
						sId: \'operation_img_', $key, '\',
						srcExpanded: elk_images_url + \'/selected_open.png\',
						altExpanded: \'*\',
						srcCollapsed: elk_images_url + \'/selected.png\',
						altCollapsed: \'*\'
					}
				]
			});';
			}
		}

	echo '
	// ]]></script>';

	// Get the currently selected item from a select list
	echo '
	<script><!-- // --><![CDATA[
	function get_selected(id)
	{
		var aSelected = document.getElementById(id);
		for (var i = 0; i < aSelected.options.length; i++)
		{
			if (aSelected.options[i].selected == true)
				return aSelected.options[i].value;
		}
		return aSelected.options[0];
	}
	// ]]></script>';

	// And a bit more for database changes.
	if ($context['uninstalling'] && !empty($context['database_changes']))
		echo '
	<script><!-- // --><![CDATA[
		var database_changes_area = document.getElementById(\'db_changes_div\');
		var db_vis = false;
		database_changes_area.style.display = "none";
	// ]]></script>';
}

function template_extract_package()
{
	global $context, $txt, $scripturl;

	if (!empty($context['redirect_url']))
	{
		echo '
	<script><!-- // --><![CDATA[
		setTimeout("doRedirect();", ', empty($context['redirect_timeout']) ? '5000' : $context['redirect_timeout'], ');

		function doRedirect()
		{
			window.location = "', $context['redirect_url'], '";
		}
	// ]]></script>';
	}

	echo '
	<div id="admincenter">';

	if (empty($context['redirect_url']))
	{
		echo '
			<div class="cat_bar">
				<h3 class="catbg">', $context['uninstalling'] ? $txt['uninstall'] : $txt['extracting'], '</h3>
			</div>
			<div class="information">', $txt['package_installed_extract'], '</div>';
	}
	else
		echo '
			<div class="cat_bar">
				<h3 class="catbg">', $txt['package_installed_redirecting'], '</h3>
			</div>';

	echo '
		<div class="windowbg">
			<div class="content">';

	// If we are going to redirect we have a slightly different agenda.
	if (!empty($context['redirect_url']))
	{
		echo '
				', $context['redirect_text'], '<br /><br />
				<a href="', $context['redirect_url'], '">', $txt['package_installed_redirect_go_now'], '</a> | <a href="', $scripturl, '?action=admin;area=packages;sa=browse">', $txt['package_installed_redirect_cancel'], '</a>';
	}
	elseif ($context['uninstalling'])
		echo '
				', $txt['package_uninstall_done'];
	elseif ($context['install_finished'])
	{
		if ($context['extract_type'] == 'avatar')
			echo '
				', $txt['avatars_extracted'];
		elseif ($context['extract_type'] == 'language')
			echo '
				', $txt['language_extracted'];
		else
			echo '
				', $txt['package_installed_done'];
	}
	else
		echo '
				', $txt['corrupt_compatible'];

	echo '
			</div>
		</div>';

	// Show the "restore permissions" screen?
	if (function_exists('template_show_list') && !empty($context['restore_file_permissions']['rows']))
	{
		echo '<br />';
		template_show_list('restore_file_permissions');
	}

	echo '
	</div>';
}

function template_list()
{
	global $context, $txt, $scripturl;

	echo '
	<div id="admincenter">
		<div class="cat_bar">
			<h3 class="catbg">', $txt['list_file'], '</h3>
		</div>
		<div class="title_bar">
			<h3 class="titlebg">', $txt['files_archive'], ' ', $context['filename'], ':</h3>
		</div>
		<div class="windowbg">
			<div class="content">
				<ol>';

	foreach ($context['files'] as $fileinfo)
		echo '
					<li><a href="', $scripturl, '?action=admin;area=packages;sa=examine;package=', $context['filename'], ';file=', $fileinfo['filename'], '" title="', $txt['view'], '">', $fileinfo['filename'], '</a> (', $fileinfo['size'], ' ', $txt['package_bytes'], ')</li>';

	echo '
				</ol>
				<br />
				<a class="linkbutton_right" href="', $scripturl, '?action=admin;area=packages">', $txt['back'], '</a>
			</div>
		</div>
	</div>';
}

function template_examine()
{
	global $context, $txt, $scripturl;

	echo '
	<div id="admincenter">
		<div class="cat_bar">
			<h3 class="catbg">', $txt['package_examine_file'], '</h3>
		</div>
		<div class="title_bar">
			<h3 class="titlebg">', $txt['package_file_contents'], ' ', $context['filename'], ':</h3>
		</div>
		<div class="windowbg">
			<div class="content">
				<pre class="file_content">', $context['filedata'], '</pre>
				<a href="', $scripturl, '?action=admin;area=packages;sa=list;package=', $context['package'], '">[ ', $txt['list_files'], ' ]</a>
			</div>
		</div>
	</div>';
}

function template_browse()
{
	global $context, $settings, $txt, $scripturl, $modSettings, $forum_version;

	echo '
	<div id="admincenter">';

	if ($context['sub_action'] == 'browse')
	{
		echo '
		<div id="admin_form_wrapper">
			<div class="cat_bar">
				<h3 class="catbg">
					<a href="', $scripturl, '?action=quickhelp;help=latest_packages" onclick="return reqOverlayDiv(this.href);" class="help"><img class="icon" src="', $settings['images_url'], '/helptopics_hd.png" alt="', $txt['help'], '" /></a> ', $txt['packages_latest'], '
				</h3>
			</div>
			<div class="windowbg2">
				<div class="content">
					<div id="packagesLatest">', $txt['packages_latest_fetch'], '</div>
				</div>
			</div>

			<script><!-- // --><![CDATA[
				window.elkForum_scripturl = elk_scripturl;
				window.elkForum_sessionid = elk_session_id;
				window.elkForum_sessionvar = elk_session_var;';

		// Make a list of already installed mods so nothing is listed twice ;).
		echo '
				window.elkInstalledPackages = ["', implode('", "', $context['installed_mods']), '"];
				window.ourVersion = "', $context['forum_version'], '";
			// ]]></script>';

		if (empty($modSettings['disable_elk_js']))
			echo '
			<script src="', $scripturl, '?action=viewadminfile;filename=latest-packages.js"></script>';

		echo '
			<script><!-- // --><![CDATA[
				var tempOldOnload;
				elkSetLatestPackages();
			// ]]></script>

		</div>';
	}

	$mods_available = false;
	foreach ($context['modification_types'] as $type)
	{
		if (!empty($context['available_' . $type]))
		{
			template_show_list('packages_lists_' . $type);
			$mods_available = true;
		}
	}

	if (!$mods_available)
		echo '
		<div class="information">', $context['sub_action'] == 'browse' ? $txt['no_packages'] : $txt['no_mods_installed'], '</div>';

	// the advanced (emulation) box, collapsed by default
	echo '
		<form action="', $scripturl, '?action=admin;area=packages;sa=', $context['sub_action'], '" method="get">
			<div id="advanced_box" >
				<div class="cat_bar">
					<h3 class="catbg">
						<img id="advanced_panel_toggle" class="panel_toggle" style="display: none;" src="', $settings['images_url'], '/', empty($context['admin_preferences']['pkg']) ? 'collapse' : 'expand', '.png" alt="*" />
						<a href="#" id="advanced_panel_link">', $txt['package_advanced_button'], '</a>
					</h3>
				</div>
				<div id="advanced_panel_div" class="windowbg"', empty($context['admin_preferences']['pkg']) ? '' : ' style="display: none;"', '>
					<div class="content">
						<p>
							', $txt['package_emulate_desc'], '
						</p>
						<dl class="settings">
							<dt>
								<strong>', $txt['package_emulate'], ':</strong><br />
								<span class="smalltext">
									<a href="#" onclick="document.getElementById(\'ve\').value = \'', $forum_version, '\';document.getElementsByName(\'version_emulate\')[0].value = \'', $forum_version, '\';return false">', $txt['package_emulate_revert'], '</a>
								</span>
							</dt>
							<dd>
								<input type="text" name="version_emulate" id="ve" value="', $context['forum_version'], '" size="25" class="input_text" />
							</dd>
						</dl>
						<div class="submitbutton">
							<input type="submit" value="', $txt['package_apply'], '" class="button_submit" />
						</div>
					</div>
				</div>
			</div>
			<input type="hidden" name="action" value="admin" />
			<input type="hidden" name="area" value="packages" />
			<input type="hidden" name="sa" value="', $context['sub_action'], '" />
		</form>';

	echo '
	</div>

	<script><!-- // --><![CDATA[
		var oAdvancedPanelToggle = new elk_Toggle({
			bToggleEnabled: true,
			bCurrentlyCollapsed: ', empty($context['admin_preferences']['pkg']) ? 'true' : 'false', ',
			aSwappableContainers: [
				\'advanced_panel_div\'
			],
			aSwapImages: [
				{
					sId: \'advanced_panel_toggle\',
					srcExpanded: elk_images_url + \'/collapse.png\',
					altExpanded: ', JavaScriptEscape($txt['upshrink_description']), ',
					srcCollapsed: elk_images_url + \'/expand.png\',
					altCollapsed: ', JavaScriptEscape($txt['upshrink_description']), '
				}
			],
			aSwapLinks: [
				{
					sId: \'advanced_panel_link\',
					msgExpanded: ', JavaScriptEscape($txt['package_advanced_button']), ',
					msgCollapsed: ', JavaScriptEscape($txt['package_advanced_button']), '
				}
			],
			oThemeOptions: {
				bUseThemeSettings: ', $context['user']['is_guest'] ? 'false' : 'true', ',
				sOptionName: \'admin_preferences\',
				sSessionId: elk_session_id,
				sSessionVar: elk_session_var,
				sThemeId: \'1\',
				sAdditionalVars: \';admin_key=pkg\'
			},
		});
	// ]]></script>
	<script src="', $settings['default_theme_url'], '/scripts/suggest.js?alp21"></script>
	<script><!-- // --><![CDATA[
			var oAddVersionSuggest = new smc_AutoSuggest({
			sSelf: \'oAddVersionSuggest\',
			sSessionId: elk_session_id,
			sSessionVar: elk_session_var,
			sControlId: \'ve\',
			sSearchType: \'versions\',
			bItemList: false
		});
	// ]]></script>';
}

function template_install_options()
{
	global $context, $txt, $scripturl;

	echo '
	<div id="admincenter">
		<div class="cat_bar">
			<h3 class="catbg">', $txt['package_install_options'], '</h3>
		</div>
		<div class="information">
			', $txt['package_install_options_ftp_why'], '
		</div>

		<div class="windowbg">
			<div class="content">
				<form action="', $scripturl, '?action=admin;area=packages;sa=options" method="post" accept-charset="UTF-8">
					<dl class="settings">
						<dt>
							<label for="pack_server"><strong>', $txt['package_install_options_ftp_server'], ':</strong></label>
						</dt>
						<dd>
							<input type="text" name="pack_server" id="pack_server" value="', $context['package_ftp_server'], '" size="30" class="input_text" />
						</dd>
						<dt>
							<label for="pack_port"><strong>', $txt['package_install_options_ftp_port'], ':</strong></label>
						</dt>
						<dd>
							<input type="text" name="pack_port" id="pack_port" size="3" value="', $context['package_ftp_port'], '" class="input_text" />
						</dd>
						<dt>
							<label for="pack_user"><strong>', $txt['package_install_options_ftp_user'], ':</strong></label>
						</dt>
						<dd>
							<input type="text" name="pack_user" id="pack_user" value="', $context['package_ftp_username'], '" size="30" class="input_text" />
						</dd>
						<dt>
							<label for="package_make_backups">', $txt['package_install_options_make_backups'], '</label>
						</dt>
						<dd>
							<input type="checkbox" name="package_make_backups" id="package_make_backups" value="1" class="input_check"', $context['package_make_backups'] ? ' checked="checked"' : '', ' />
						</dd>
						<dt>
							<label for="package_make_full_backups">', $txt['package_install_options_make_full_backups'], '</label>
						</dt>
						<dd>
							<input type="checkbox" name="package_make_full_backups" id="package_make_full_backups" value="1" class="input_check"', $context['package_make_full_backups'] ? ' checked="checked"' : '', ' />
						</dd>
					</dl>
					<input type="submit" name="save" value="', $txt['save'], '" class="right_submit" />
					<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
				</form>
			</div>
		</div>
	</div>';
}

function template_control_chmod()
{
	global $context, $txt;

	// Nothing to do? Brilliant!
	if (empty($context['package_ftp']))
		return false;

	if (empty($context['package_ftp']['form_elements_only']))
	{
		echo '
				', sprintf($txt['package_ftp_why'], 'document.getElementById(\'need_writable_list\').style.display = \'\'; return false;'), '<br />
				<div id="need_writable_list" class="smalltext">
					', $txt['package_ftp_why_file_list'], '
					<ul style="display: inline;">';
		if (!empty($context['notwritable_files']))
			foreach ($context['notwritable_files'] as $file)
				echo '
						<li>', $file, '</li>';

		echo '
					</ul>
				</div>';
	}

	echo '
				<div class="bordercolor" id="ftp_error_div" style="', (!empty($context['package_ftp']['error']) ? '' : 'display:none;'), 'padding: 1px; margin: 1ex;"><div class="windowbg2" id="ftp_error_innerdiv" style="padding: 1ex;">
					<tt id="ftp_error_message">', !empty($context['package_ftp']['error']) ? $context['package_ftp']['error'] : '', '</tt>
				</div></div>';

	if (!empty($context['package_ftp']['destination']))
		echo '
				<form action="', $context['package_ftp']['destination'], '" method="post" accept-charset="UTF-8" style="margin: 0;">';

	echo '
					<fieldset>
					<dl class="settings">
						<dt>
							<label for="ftp_server">', $txt['package_ftp_server'], ':</label>
						</dt>
						<dd>
							<input type="text" size="30" name="ftp_server" id="ftp_server" value="', $context['package_ftp']['server'], '" class="input_text" />
							<label for="ftp_port">', $txt['package_ftp_port'], ':&nbsp;</label> <input type="text" size="3" name="ftp_port" id="ftp_port" value="', $context['package_ftp']['port'], '" class="input_text" />
						</dd>
						<dt>
							<label for="ftp_username">', $txt['package_ftp_username'], ':</label>
						</dt>
						<dd>
							<input type="text" size="50" name="ftp_username" id="ftp_username" value="', $context['package_ftp']['username'], '" style="width: 98%;" class="input_text" />
						</dd>
						<dt>
							<label for="ftp_password">', $txt['package_ftp_password'], ':</label>
						</dt>
						<dd>
							<input type="password" size="50" name="ftp_password" id="ftp_password" style="width: 98%;" class="input_password" />
						</dd>
						<dt>
							<label for="ftp_path">', $txt['package_ftp_path'], ':</label>
						</dt>
						<dd>
							<input type="text" size="50" name="ftp_path" id="ftp_path" value="', $context['package_ftp']['path'], '" style="width: 98%;" class="input_text" />
						</dd>
					</dl>
					</fieldset>';

	if (empty($context['package_ftp']['form_elements_only']))
		echo '

					<div class="righttext" style="margin: 1ex;">
						<span id="test_ftp_placeholder_full"></span>
						<input type="submit" value="', $txt['package_proceed'], '" class="right_submit" />
					</div>';

	if (!empty($context['package_ftp']['destination']))
		echo '
					<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
				</form>';

	// Hide the details of the list.
	if (empty($context['package_ftp']['form_elements_only']))
		echo '
		<script><!-- // --><![CDATA[
			document.getElementById(\'need_writable_list\').style.display = \'none\';
		// ]]></script>';

	// Quick generate the test button.
	echo '
	<script><!-- // --><![CDATA[
		// Generate a "test ftp" button.
		var generatedButton = false;
		function generateFTPTest()
		{
			// Don\'t ever call this twice!
			if (generatedButton)
				return false;
			generatedButton = true;

			// No XML?
			if (!window.XMLHttpRequest || (!document.getElementById("test_ftp_placeholder") && !document.getElementById("test_ftp_placeholder_full")))
				return false;

			var ftpTest = document.createElement("input");
			ftpTest.type = "button";
			ftpTest.onclick = testFTP;

			if (document.getElementById("test_ftp_placeholder"))
			{
				ftpTest.value = "', $txt['package_ftp_test'], '";
				document.getElementById("test_ftp_placeholder").appendChild(ftpTest);
			}
			else
			{
				ftpTest.value = "', $txt['package_ftp_test_connection'], '";
				document.getElementById("test_ftp_placeholder_full").appendChild(ftpTest);
			}
		}
		function testFTPResults(oXMLDoc)
		{
			ajax_indicator(false);

			// This assumes it went wrong!
			var wasSuccess = false;
			var message = "', addcslashes($txt['package_ftp_test_failed'], "'"), '";

			var results = oXMLDoc.getElementsByTagName(\'results\')[0].getElementsByTagName(\'result\');
			if (results.length > 0)
			{
				if (results[0].getAttribute(\'success\') == 1)
					wasSuccess = true;
				message = results[0].firstChild.nodeValue;
			}

			document.getElementById("ftp_error_div").style.display = "";
			document.getElementById("ftp_error_div").style.backgroundColor = wasSuccess ? "green" : "red";
			document.getElementById("ftp_error_innerdiv").style.backgroundColor = wasSuccess ? "#DBFDC7" : "#FDBDBD";

			setInnerHTML(document.getElementById("ftp_error_message"), message);
		}
		generateFTPTest();
	// ]]></script>';

	// Make sure the button gets generated last.
	$context['insert_after_template'] .= '
	<script><!-- // --><![CDATA[
		generateFTPTest();
	// ]]></script>';
}

function template_ftp_required()
{
	global $txt;

	echo '
		<fieldset>
			<legend>
				', $txt['package_ftp_necessary'], '
			</legend>
			<div class="ftp_details">
				', template_control_chmod(), '
			</div>
		</fieldset>';
}

function template_view_operations()
{
	global $context, $txt, $settings;

	echo '<!DOCTYPE html>
<html ', $context['right_to_left'] ? 'dir="rtl"' : '', '>
	<head>
		<title>', $txt['operation_title'], '</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<link rel="stylesheet" href="', $settings['theme_url'], '/css/index', $context['theme_variant'], '.css?alp21" />
		<link rel="stylesheet" href="', $settings['theme_url'], '/css/admin.css?alp21" />
		<script src="', $settings['default_theme_url'], '/scripts/script.js?alp21"></script>
		<script src="', $settings['default_theme_url'], '/scripts/theme.js?alp21"></script>
	</head>
	<body>
		<div class="windowbg">
			', $context['operations']['search'], '
			<br />
				', $context['operations']['replace'], '
		</div>
	</body>
</html>';
}

function template_file_permissions()
{
	global $txt, $scripturl, $context, $settings;

	// This will handle expanding the selection.
	echo '
	<script><!-- // --><![CDATA[
		var oRadioColors = {
			0: "#D1F7BF",
			1: "#FFBBBB",
			2: "#FDD7AF",
			3: "#C2C6C0",
			4: "#EEEEEE"
		}
		var oRadioValues = {
			0: "read",
			1: "writable",
			2: "execute",
			3: "custom",
			4: "no_change"
		}
		function dynamicAddMore()
		{
			ajax_indicator(true);

			getXMLDocument(elk_prepareScriptUrl(elk_scripturl) + \'action=admin;area=packages;fileoffset=\' + (parseInt(this.offset) + ', $context['file_limit'], ') + \';onlyfind=\' + escape(this.path) + \';sa=perms;xml;', $context['session_var'], '=', $context['session_id'], '\', onNewFolderReceived);
		}

		// Getting something back?
		function onNewFolderReceived(oXMLDoc)
		{
			ajax_indicator(false);

			var fileItems = oXMLDoc.getElementsByTagName(\'folders\')[0].getElementsByTagName(\'folder\');

			// No folders, no longer worth going further.
			if (fileItems.length < 1)
			{
				if (oXMLDoc.getElementsByTagName(\'roots\')[0].getElementsByTagName(\'root\')[0])
				{
					var rootName = oXMLDoc.getElementsByTagName(\'roots\')[0].getElementsByTagName(\'root\')[0].firstChild.nodeValue;
					var itemLink = document.getElementById(\'link_\' + rootName);

					// Move the children up.
					for (i = 0; i <= itemLink.childNodes.length; i++)
						itemLink.parentNode.insertBefore(itemLink.childNodes[0], itemLink);

					// And remove the link.
					itemLink.parentNode.removeChild(itemLink);
				}
				return false;
			}
			var tableHandle = false;
			var isMore = false;
			var ident = "";
			var my_ident = "";
			var curLevel = 0;

			for (var i = 0; i < fileItems.length; i++)
			{
				if (fileItems[i].getAttribute(\'more\') == 1)
				{
					isMore = true;
					var curOffset = fileItems[i].getAttribute(\'offset\');
				}

				if (fileItems[i].getAttribute(\'more\') != 1 && document.getElementById("insert_div_loc_" + fileItems[i].getAttribute(\'ident\')))
				{
					ident = fileItems[i].getAttribute(\'ident\');
					my_ident = fileItems[i].getAttribute(\'my_ident\');
					curLevel = fileItems[i].getAttribute(\'level\') * 5;
					curPath = fileItems[i].getAttribute(\'path\');

					// Get where we\'re putting it next to.
					tableHandle = document.getElementById("insert_div_loc_" + fileItems[i].getAttribute(\'ident\'));

					var curRow = document.createElement("tr");
					curRow.className = "windowbg";
					curRow.id = "content_" + my_ident;
					curRow.style.display = "";
					var curCol = document.createElement("td");
					curCol.className = "smalltext";
					curCol.width = "40%";

					// This is the name.
					var fileName = document.createTextNode(fileItems[i].firstChild.nodeValue);

					// Start by wacking in the spaces.
					setInnerHTML(curCol, repeatString("&nbsp;", curLevel));

					// Create the actual text.
					if (fileItems[i].getAttribute(\'folder\') == 1)
					{
						var linkData = document.createElement("a");
						linkData.name = "fol_" + my_ident;
						linkData.id = "link_" + my_ident;
						linkData.href = \'#\';
						linkData.path = curPath + "/" + fileItems[i].firstChild.nodeValue;
						linkData.ident = my_ident;
						linkData.onclick = dynamicExpandFolder;

						var folderImage = document.createElement("img");
						folderImage.src = \'', addcslashes($settings['default_images_url'], "\\"), '/board.png\';
						linkData.appendChild(folderImage);

						linkData.appendChild(fileName);
						curCol.appendChild(linkData);
					}
					else
						curCol.appendChild(fileName);

					curRow.appendChild(curCol);

					// Right, the permissions.
					curCol = document.createElement("td");
					curCol.className = "smalltext";

					var writeSpan = document.createElement("span");
					writeSpan.style.color = fileItems[i].getAttribute(\'writable\') ? "green" : "red";
					setInnerHTML(writeSpan, fileItems[i].getAttribute(\'writable\') ? \'', $txt['package_file_perms_writable'], '\' : \'', $txt['package_file_perms_not_writable'], '\');
					curCol.appendChild(writeSpan);

					if (fileItems[i].getAttribute(\'permissions\'))
					{
						var permData = document.createTextNode("\u00a0(', $txt['package_file_perms_chmod'], ': " + fileItems[i].getAttribute(\'permissions\') + ")");
						curCol.appendChild(permData);
					}

					curRow.appendChild(curCol);

					// Now add the five radio buttons.
					for (j = 0; j < 5; j++)
					{
						curCol = document.createElement("td");
						curCol.style.backgroundColor = oRadioColors[j];
						curCol.align = "center";

						var curInput = createNamedElement("input", "permStatus[" + curPath + "/" + fileItems[i].firstChild.nodeValue + "]", j == 4 ? \'checked="checked"\' : "");
						curInput.type = "radio";
						curInput.checked = "checked";
						curInput.value = oRadioValues[j];

						curCol.appendChild(curInput);
						curRow.appendChild(curCol);
					}

					// Put the row in.
					tableHandle.parentNode.insertBefore(curRow, tableHandle);

					// Put in a new dummy section?
					if (fileItems[i].getAttribute(\'folder\') == 1)
					{
						var newRow = document.createElement("tr");
						newRow.id = "insert_div_loc_" + my_ident;
						newRow.style.display = "none";
						tableHandle.parentNode.insertBefore(newRow, tableHandle);
						var newCol = document.createElement("td");
						newCol.colspan = 2;
						newRow.appendChild(newCol);
					}
				}
			}

			// Is there some more to remove?
			if (document.getElementById("content_" + ident + "_more"))
			{
				document.getElementById("content_" + ident + "_more").parentNode.removeChild(document.getElementById("content_" + ident + "_more"));
			}

			// Add more?
			if (isMore && tableHandle)
			{
				// Create the actual link.
				var linkData = document.createElement("a");
				linkData.href = \'#fol_\' + my_ident;
				linkData.path = curPath;
				linkData.offset = curOffset;
				linkData.onclick = dynamicAddMore;

				linkData.appendChild(document.createTextNode(\'', $txt['package_file_perms_more_files'], '\'));

				curRow = document.createElement("tr");
				curRow.className = "windowbg";
				curRow.id = "content_" + ident + "_more";
				tableHandle.parentNode.insertBefore(curRow, tableHandle);
				curCol = document.createElement("td");
				curCol.className = "smalltext";
				curCol.width = "40%";

				setInnerHTML(curCol, repeatString("&nbsp;", curLevel));
				curCol.appendChild(document.createTextNode(\'\\u00ab \'));
				curCol.appendChild(linkData);
				curCol.appendChild(document.createTextNode(\' \\u00bb\'));

				curRow.appendChild(curCol);
				curCol = document.createElement("td");
				curCol.className = "smalltext";
				curRow.appendChild(curCol);
			}

			// Keep track of it.
			var curInput = createNamedElement("input", "back_look[]");
			curInput.type = "hidden";
			curInput.value = curPath;

			curCol.appendChild(curInput);
		}
	// ]]></script>';

		echo '
	<div class="noticebox">
		<div>
			<strong>', $txt['package_file_perms_warning'], ':</strong>
			<div class="smalltext">
				<ol style="margin-top: 2px; margin-bottom: 2px">
					', $txt['package_file_perms_warning_desc'], '
				</ol>
			</div>
		</div>
	</div>

	<form action="', $scripturl, '?action=admin;area=packages;sa=perms;', $context['session_var'], '=', $context['session_id'], '" method="post" accept-charset="UTF-8">
		<div class="title_bar">
			<h3 class="titlebg">
				<span class="floatleft">', $txt['package_file_perms'], '</span><span class="fperm floatright">', $txt['package_file_perms_new_status'], '</span>
			</h3>
		</div>
		<table class="table_grid">
			<thead>
				<tr class="table_head">
					<th class="lefttext" style="width:30%">&nbsp;', $txt['package_file_perms_name'], '&nbsp;</th>
					<th class="lefttext" style="width:30%">', $txt['package_file_perms_status'], '</th>
					<th style="width:8%"><span class="filepermissions">', $txt['package_file_perms_status_read'], '</span></th>
					<th style="width:8%"><span class="filepermissions">', $txt['package_file_perms_status_write'], '</span></th>
					<th style="width:8%"><span class="filepermissions">', $txt['package_file_perms_status_execute'], '</span></th>
					<th style="width:8%"><span class="filepermissions">', $txt['package_file_perms_status_custom'], '</span></th>
					<th style="width:8%"><span class="filepermissions">', $txt['package_file_perms_status_no_change'], '</span></th>
				</tr>
			</thead>
			<tbody>';

	foreach ($context['file_tree'] as $name => $dir)
	{
		echo '

				<tr class="windowbg2">
					<td style="width:30%"><strong>';

				if (!empty($dir['type']) && ($dir['type'] == 'dir' || $dir['type'] == 'dir_recursive'))
					echo '
						<img src="', $settings['default_images_url'], '/board.png" alt="*" />';

				echo '
						', $name, '
					</strong></td>
					<td style="width:30%">
						<span style="color: ', ($dir['perms']['chmod'] ? 'green' : 'red'), '">', ($dir['perms']['chmod'] ? $txt['package_file_perms_writable'] : $txt['package_file_perms_not_writable']), '</span>
						', ($dir['perms']['perms'] ? '&nbsp;(' . $txt['package_file_perms_chmod'] . ': ' . substr(sprintf('%o', $dir['perms']['perms']), -4) . ')' : ''), '
					</td>
					<td class="perm_read centertext" style="width:8%"><input type="radio" name="permStatus[', $name, ']" value="read" class="input_radio" /></td>
					<td class="perm_write centertext" style="width:8%"><input type="radio" name="permStatus[', $name, ']" value="writable" class="input_radio" /></td>
					<td class="perm_execute centertext" style="width:8%"><input type="radio" name="permStatus[', $name, ']" value="execute" class="input_radio" /></td>
					<td class="perm_custom centertext" style="width:8%"><input type="radio" name="permStatus[', $name, ']" value="custom" class="input_radio" /></td>
					<td class="perm_nochange centertext" style="width:8%"><input type="radio" name="permStatus[', $name, ']" value="no_change" checked="checked" class="input_radio" /></td>
				</tr>
			';

		if (!empty($dir['contents']))
			template_permission_show_contents($name, $dir['contents'], 1);
	}

	echo '
			</tbody>
		</table>
		<br />
		<div class="cat_bar">
			<h3 class="catbg">', $txt['package_file_perms_change'], '</h3>
		</div>
		<div class="windowbg">
			<div class="content">
				<fieldset>
					<dl>
						<dt>
							<input type="radio" name="method" value="individual" checked="checked" id="method_individual" class="input_radio" />
							<label for="method_individual"><strong>', $txt['package_file_perms_apply'], '</strong></label>
						</dt>
						<dd>
							<em class="smalltext">', $txt['package_file_perms_custom'], ': <input type="text" name="custom_value" value="0755" maxlength="4" size="5" class="input_text" />&nbsp;<a href="', $scripturl, '?action=quickhelp;help=chmod_flags" onclick="return reqOverlayDiv(this.href);" class="help">(?)</a></em>
						</dd>
						<dt>
							<input type="radio" name="method" value="predefined" id="method_predefined" class="input_radio" />
							<label for="method_predefined"><strong>', $txt['package_file_perms_predefined'], ':</strong></label>
							<select name="predefined" onchange="document.getElementById(\'method_predefined\').checked = \'checked\';">
								<option value="restricted" selected="selected">', $txt['package_file_perms_pre_restricted'], '</option>
								<option value="standard">', $txt['package_file_perms_pre_standard'], '</option>
								<option value="free">', $txt['package_file_perms_pre_free'], '</option>
							</select>
						</dt>
						<dd>
							<em class="smalltext">', $txt['package_file_perms_predefined_note'], '</em>
						</dd>
					</dl>
				</fieldset>';

	// Likely to need FTP?
	if (empty($context['ftp_connected']))
		echo '
				<p>
					', $txt['package_file_perms_ftp_details'], ':
				</p>
				', template_control_chmod(), '
				<div class="information">', $txt['package_file_perms_ftp_retain'], '</div>';

	echo '
				<span id="test_ftp_placeholder_full"></span>
				<input type="hidden" name="action_changes" value="1" />
				<input type="submit" value="', $txt['package_file_perms_go'], '" name="go" class="right_submit" />
			</div>
		</div>';

	// Any looks fors we've already done?
	foreach ($context['look_for'] as $path)
		echo '
			<input type="hidden" name="back_look[]" value="', $path, '" />';
	echo '
	</form><br />';
}

function template_permission_show_contents($ident, $contents, $level, $has_more = false)
{
	global $settings, $txt, $scripturl, $context;
	$js_ident = preg_replace('~[^A-Za-z0-9_\-=:]~', ':-:', $ident);

	// Have we actually done something?
	$drawn_div = false;

	foreach ($contents as $name => $dir)
	{
		if (isset($dir['perms']))
		{
			if (!$drawn_div)
			{
				$drawn_div = true;
				echo '
			</tbody>
			</table>
			<table class="table_grid" id="', $js_ident, '">
			<tbody>';
			}

			$cur_ident = preg_replace('~[^A-Za-z0-9_\-=:]~', ':-:', $ident . '/' . $name);
			echo '
			<tr class="windowbg" id="content_', $cur_ident, '">
				<td class="smalltext" style="width:30%">' . str_repeat('&nbsp;', $level * 5), '
					', (!empty($dir['type']) && $dir['type'] == 'dir_recursive') || !empty($dir['list_contents']) ? '<a id="link_' . $cur_ident . '" href="' . $scripturl . '?action=admin;area=packages;sa=perms;find=' . base64_encode($ident . '/' . $name) . ';back_look=' . $context['back_look_data'] . ';' . $context['session_var'] . '=' . $context['session_id'] . '#fol_' . $cur_ident . '" onclick="return expandFolder(\'' . $cur_ident . '\', \'' . addcslashes($ident . '/' . $name, "'\\") . '\');">' : '';

			if (!empty($dir['type']) && ($dir['type'] == 'dir' || $dir['type'] == 'dir_recursive'))
				echo '
					<img src="', $settings['default_images_url'], '/board.png" alt="*" />';

			echo '
					', $name, '
					', (!empty($dir['type']) && $dir['type'] == 'dir_recursive') || !empty($dir['list_contents']) ? '</a>' : '', '
				</td>
				<td class="smalltext">
					<span class="', ($dir['perms']['chmod'] ? 'success' : 'error'), '">', ($dir['perms']['chmod'] ? $txt['package_file_perms_writable'] : $txt['package_file_perms_not_writable']), '</span>
					', ($dir['perms']['perms'] ? '&nbsp;(' . $txt['package_file_perms_chmod'] . ': ' . substr(sprintf('%o', $dir['perms']['perms']), -4) . ')' : ''), '
				</td>
				<td class="perm_read centertext" style="width:8%"><input type="radio" name="permStatus[', $ident . '/' . $name, ']" value="read" class="input_radio" /></td>
				<td class="perm_write centertext" style="width:8%"><input type="radio" name="permStatus[', $ident . '/' . $name, ']" value="writable" class="input_radio" /></td>
				<td class="perm_execute centertext" style="width:8%"><input type="radio" name="permStatus[', $ident . '/' . $name, ']" value="execute" class="input_radio" /></td>
				<td class="perm_custom centertext" style="width:8%"><input type="radio" name="permStatus[', $ident . '/' . $name, ']" value="custom" class="input_radio" /></td>
				<td class="perm_nochange centertext" style="width:8%"><input type="radio" name="permStatus[', $ident . '/' . $name, ']" value="no_change" checked="checked" class="input_radio" /></td>
			</tr>
			<tr id="insert_div_loc_' . $cur_ident . '" style="display: none;"><td></td></tr>';

			if (!empty($dir['contents']))
				template_permission_show_contents($ident . '/' . $name, $dir['contents'], $level + 1, !empty($dir['more_files']));
		}
	}

	// We have more files to show?
	if ($has_more)
		echo '
	<tr class="windowbg" id="content_', $js_ident, '_more">
		<td class="smalltext" style="width:40%">' . str_repeat('&nbsp;', $level * 5), '
			&#171; <a href="' . $scripturl . '?action=admin;area=packages;sa=perms;find=' . base64_encode($ident) . ';fileoffset=', ($context['file_offset'] + $context['file_limit']), ';' . $context['session_var'] . '=' . $context['session_id'] . '#fol_' . preg_replace('~[^A-Za-z0-9_\-=:]~', ':-:', $ident) . '">', $txt['package_file_perms_more_files'], '</a> &#187;
		</td>
		<td colspan="6"></td>
	</tr>';

	if ($drawn_div)
	{
		// Hide anything too far down the tree.
		$isFound = false;
		foreach ($context['look_for'] as $tree)
		{
			if (substr($tree, 0, strlen($ident)) == $ident)
				$isFound = true;
		}

		if ($level > 1 && !$isFound)
			echo '
		</tbody>
		</table><script><!-- // --><![CDATA[
			expandFolder(\'', $js_ident, '\', \'\');
		// ]]></script>
		<table class="table_grid">
			<tbody>
			<tr style="display: none;"><td></td></tr>';
	}
}

function template_pause_action_permissions()
{
	global $txt, $scripturl, $context;

	// How many have we done?
	$countDown = 5;

	echo '
	<div id="admincenter">
		<div class="cat_bar">
			<h3 class="catbg">', $txt['package_file_perms_applying'], '</h3>
		</div>';

	if (!empty($context['skip_ftp']))
		echo '
		<div class="errorbox">
			', $txt['package_file_perms_skipping_ftp'], '
		</div>';

	// First progress bar for the number of directories we are working
	echo '
		<div class="windowbg">
			<div class="content">
				<div>
					<strong>', $context['progress_message'], '</strong>
					<div class="progress_bar">
						<div class="full_bar">', $context['progress_percent'], '%</div>
						<div class="blue_percent" style="width: ', $context['progress_percent'], '%;">&nbsp;</div>
					</div>
				</div>';

	// Second progress bar for progress in a specific directory?
	if ($context['method'] != 'individual' && !empty($context['total_files']))
	{
		echo '
				<br />
				<div>
					<strong>', $context['file_progress_message'], '</strong>
					<div class="progress_bar">
						<div class="full_bar">', $context['file_progress_percent'], '%</div>
						<div class="green_percent" style="width: ', $context['file_progress_percent'], '%;">&nbsp;</div>
					</div>
				</div>';
	}

	echo '
				<form action="', $scripturl, '?action=admin;area=packages;sa=perms;', $context['session_var'], '=', $context['session_id'], '" id="autoSubmit" name="autoSubmit" method="post" accept-charset="UTF-8">';

	// Put out the right hidden data.
	if ($context['method'] === 'individual')
		echo '
					<input type="hidden" name="custom_value" value="', $context['custom_value'], '" />
					<input type="hidden" name="totalItems" value="', $context['total_items'], '" />
					<input type="hidden" name="toProcess" value="', base64_encode(serialize($context['to_process'])), '" />';
	else
		echo '
					<input type="hidden" name="predefined" value="', $context['predefined_type'], '" />
					<input type="hidden" name="fileOffset" value="', $context['file_offset'], '" />
					<input type="hidden" name="totalItems" value="', $context['total_items'], '" />
					<input type="hidden" name="dirList" value="', base64_encode(serialize($context['directory_list'])), '" />
					<input type="hidden" name="specialFiles" value="', base64_encode(serialize($context['special_files'])), '" />';

	// Are we not using FTP for whatever reason.
	if (!empty($context['skip_ftp']))
		echo '
					<input type="hidden" name="skip_ftp" value="1" />';

	// Retain state.
	foreach ($context['back_look_data'] as $path)
		echo '
					<input type="hidden" name="back_look[]" value="', $path, '" />';

	// Standard fields
	echo '
					<input type="hidden" name="method" value="', $context['method'], '" />
					<input type="hidden" name="action_changes" value="1" />
					<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
					<input type="submit" name="go" id="cont" value="', $txt['not_done_continue'], '" class="right_submit" />
				</form>
			</div>
		</div>
	</div>';

	// Just the countdown stuff
	echo '
	<script><!-- // --><![CDATA[
		var countdown = ', $countDown, ';
		var txt_message = "', $txt['not_done_continue'], '";
		doAutoSubmit();
	// ]]></script>';

}