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
 * Editing the smiley sets.
 */
function template_editsets()
{
	global $context, $scripturl, $txt, $modSettings;

	echo '
	<div id="admincenter">';

	template_show_list('smiley_set_list');

	echo '
		<br />
		<div class="cat_bar">
			<h3 class="catbg">', $txt['smiley_sets_latest'], '</h3>
		</div>
		<div class="windowbg">
			<div class="content">
				<div id="smileysLatest">', $txt['smiley_sets_latest_fetch'], '</div>
			</div>
		</div>
	</div>

	<script><!-- // --><![CDATA[
		window.elkForum_scripturl = elk_scripturl;
		window.elkForum_sessionid = elk_session_id;
		window.elkForum_sessionvar = elk_session_var;
	// ]]></script>';

	if (empty($modSettings['disable_elk_js']))
		echo '
	<script src="', $scripturl, '?action=viewadminfile;filename=latest-smileys.js"></script>';

	echo '
	<script><!-- // --><![CDATA[
		function elkSetLatestSmileys()
		{
			if (typeof(window.elkLatestSmileys) != "undefined")
				setInnerHTML(document.getElementById("smileysLatest"), window.elkLatestSmileys);';

		if (!empty($context['selected_set']))
			echo '

			changeSet("', $context['selected_set'], '");';
		if (!empty($context['selected_smiley']))
			echo '
			loadSmiley(', $context['selected_smiley'], ');';

		echo '
		}';

		echo '
			elkSetLatestSmileys();';

		echo '
	// ]]></script>';
}

/**
 * Modifying a smiley set.
 */
function template_modifyset()
{
	global $context, $scripturl, $txt, $modSettings;

	echo '
	<div id="admincenter">
		<form action="', $scripturl, '?action=admin;area=smileys;sa=editsets" method="post" accept-charset="UTF-8">
			<div class="cat_bar">
				<h3 class="catbg">
				', $context['current_set']['is_new'] ? $txt['smiley_set_new'] : $txt['smiley_set_modify_existing'], '
				</h3>
			</div>';

		// If this is an existing set, and there are still un-added smileys - offer an import opportunity.
		if (!empty($context['current_set']['can_import']))
		{
			echo '
			<div class="information">
				', $context['current_set']['can_import'] == 1 ? $txt['smiley_set_import_single'] : $txt['smiley_set_import_multiple'], ' <a href="', $scripturl, '?action=admin;area=smileys;sa=import;set=', $context['current_set']['id'], ';', $context['session_var'], '=', $context['session_id'], ';', $context['admin-mss_token_var'], '=' ,$context['admin-mss_token'],'">', $txt['here'], '</a> ', $context['current_set']['can_import'] == 1 ? $txt['smiley_set_to_import_single'] : $txt['smiley_set_to_import_multiple'], '

			</div>';
		}

		echo '
			<div class="windowbg">
				<div class="content">
					<dl class="settings">
						<dt>
							<strong><label for="smiley_sets_name">', $txt['smiley_sets_name'], '</label>: </strong>
						</dt>
						<dd>
							<input type="text" name="smiley_sets_name" id="smiley_sets_name" value="', $context['current_set']['name'], '" class="input_text" />
						</dd>
						<dt>
							<strong><label for="smiley_sets_path">', $txt['smiley_sets_url'], '</label>: </strong>
						</dt>
						<dd>
							', $modSettings['smileys_url'], '/';

		if ($context['current_set']['id'] == 'default')
			echo '<strong>default</strong><input type="hidden" name="smiley_sets_path" id="smiley_sets_path" value="default" />';
		elseif (empty($context['smiley_set_dirs']))
			echo '
							<input type="text" name="smiley_sets_path" id="smiley_sets_path" value="', $context['current_set']['path'], '" class="input_text" /> ';

		else
		{
			echo '
							<select name="smiley_sets_path" id="smiley_sets_path">';
			foreach ($context['smiley_set_dirs'] as $smiley_set_dir)
				echo '
								<option value="', $smiley_set_dir['id'], '"', $smiley_set_dir['current'] ? ' selected="selected"' : '', $smiley_set_dir['selectable'] ? '' : ' disabled="disabled"', '>', $smiley_set_dir['id'], '</option>';
			echo '
							</select> ';
		}

		echo '
							/..
						</dd>
						<dt>
							<strong><label for="smiley_sets_default">', $txt['smiley_set_select_default'], '</label>: </strong>
						</dt>
						<dd>
							<input type="checkbox" name="smiley_sets_default" id="smiley_sets_default" value="1"', $context['current_set']['selected'] ? ' checked="checked"' : '', ' class="input_check" />
						</dd>';

		// If this is a new smiley set they have the option to import smileys already in the directory.
		if ($context['current_set']['is_new'] && !empty($modSettings['smiley_enable']))
			echo '
						<dt>
							<strong><label for="smiley_sets_import">', $txt['smiley_set_import_directory'], '</label>: </strong>
						</dt>
						<dd>
							<input type="checkbox" name="smiley_sets_import" id="smiley_sets_import" value="1" class="input_check" />
						</dd>';

		echo '
					</dl>
					<hr />
					<input type="submit" name="smiley_save" value="', $txt['smiley_sets_save'], '" class="right_submit" />
					<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
					<input type="hidden" name="', $context['admin-mss_token_var'], '" value="', $context['admin-mss_token'], '" />
					<input type="hidden" name="set" value="', $context['current_set']['id'], '" />
				</div>
			</div>
		</form>
	</div>';
}

/**
 * Editing an individual smiley.
 */
function template_modifysmiley()
{
	global $context, $scripturl, $txt, $modSettings;

	echo '
	<div id="admincenter">
		<form action="', $scripturl, '?action=admin;area=smileys;sa=editsmileys" method="post" accept-charset="UTF-8" name="smileyForm" id="smileyForm">
			<div class="cat_bar">
				<h3 class="catbg">', $txt['smiley_modify_existing'], '</h3>
			</div>
			<div class="windowbg">
				<div class="content">
					<dl class="settings">
						<dt>
							<strong>', $txt['smiley_preview'], ': </strong>
						</dt>
						<dd>
							<img src="', $modSettings['smileys_url'], '/', $modSettings['smiley_sets_default'], '/', $context['current_smiley']['filename'], '" id="preview" alt="" /> (', $txt['smiley_preview_using'], ': <select name="set" onchange="updatePreview();">';

		foreach ($context['smiley_sets'] as $smiley_set)
			echo '
							<option value="', $smiley_set['path'], '"', $context['selected_set'] == $smiley_set['path'] ? ' selected="selected"' : '', '>', $smiley_set['name'], '</option>';

		echo '
							</select>)
						</dd>
						<dt>
							<strong><label for="smiley_code">', $txt['smileys_code'], '</label>: </strong>
						</dt>
						<dd>
							<input type="text" name="smiley_code" id="smiley_code" value="', $context['current_smiley']['code'], '" class="input_text" />
						</dd>
						<dt>
							<strong><label for="smiley_filename">', $txt['smileys_filename'], '</label>: </strong>
						</dt>
						<dd>';
			if (empty($context['filenames']))
				echo '
							<input type="text" name="smiley_filename" id="smiley_filename" value="', $context['current_smiley']['filename'], '" class="input_text" />';
			else
			{
				echo '
							<select name="smiley_filename" id="smiley_filename" onchange="updatePreview();">';
				foreach ($context['filenames'] as $filename)
					echo '
								<option value="', $filename['id'], '"', $filename['selected'] ? ' selected="selected"' : '', '>', $filename['id'], '</option>';
				echo '
							</select>';
			}

			echo '
						</dd>
						<dt>
							<strong><label for="smiley_description">', $txt['smileys_description'], '</label>: </strong>
						</dt>
						<dd>
							<input type="text" name="smiley_description" id="smiley_description" value="', $context['current_smiley']['description'], '" class="input_text" />
						</dd>
						<dt>
							<strong><label for="smiley_location">', $txt['smileys_location'], '</label>: </strong>
						</dt>
						<dd>
							<select name="smiley_location" id="smiley_location">
								<option value="0"', $context['current_smiley']['location'] == 0 ? ' selected="selected"' : '', '>
									', $txt['smileys_location_form'], '
								</option>
								<option value="1"', $context['current_smiley']['location'] == 1 ? ' selected="selected"' : '', '>
									', $txt['smileys_location_hidden'], '
								</option>
								<option value="2"', $context['current_smiley']['location'] == 2 ? ' selected="selected"' : '', '>
									', $txt['smileys_location_popup'], '
								</option>
							</select>
						</dd>
					</dl>
					<hr />
					<div class="submitbutton">
						<input type="submit" name="smiley_save" value="', $txt['smileys_save'], '" class="button_submit" />
						<input type="submit" name="deletesmiley" value="', $txt['smileys_delete'], '" onclick="return confirm(\'', $txt['smileys_delete_confirm'], '\');" class="button_submit" />
						<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
						<input type="hidden" name="smiley" value="', $context['current_smiley']['id'], '" />
					</div>
				</div>
			</div>
		</form>
	</div>

	<script><!-- // --><![CDATA[
		function updatePreview()
		{
			var currentImage = document.getElementById("preview");
			currentImage.src = "', $modSettings['smileys_url'], '/" + document.forms.smileyForm.set.value + "/" + document.forms.smileyForm.smiley_filename.value;
		}
	// ]]></script>';
}

/**
 * Adding a new smiley.
 */
function template_addsmiley()
{
	global $context, $scripturl, $txt, $modSettings;

	echo '
	<div id="admincenter">
		<form action="', $scripturl, '?action=admin;area=smileys;sa=addsmiley" method="post" accept-charset="UTF-8" name="smileyForm" id="smileyForm" enctype="multipart/form-data">
			<div class="cat_bar">
				<h3 class="catbg">', $txt['smileys_add_method'], '</h3>
			</div>
			<div class="windowbg">
				<div class="content">
					<ul>
						<li>
							<label for="method-existing"><input type="radio" onclick="switchType();" name="method" id="method-existing" value="existing" checked="checked" class="input_radio" /> ', $txt['smileys_add_existing'], '</label>
						</li>
						<li>
							<label for="method-upload"><input type="radio" onclick="switchType();" name="method" id="method-upload" value="upload" class="input_radio" /> ', $txt['smileys_add_upload'], '</label>
						</li>
					</ul>
					<br />
					<fieldset id="ex_settings">
						<dl class="settings">
							<dt>
								<img src="', $modSettings['smileys_url'], '/', $modSettings['smiley_sets_default'], '/', $context['filenames'][0]['id'], '" id="preview" alt="" />
							</dt>
							<dd>
								', $txt['smiley_preview_using'], ': <select name="set" onchange="updatePreview();selectMethod(\'existing\');">

							';

		foreach ($context['smiley_sets'] as $smiley_set)
			echo '
								<option value="', $smiley_set['path'], '"', $context['selected_set'] == $smiley_set['path'] ? ' selected="selected"' : '', '>', $smiley_set['name'], '</option>';

		echo '
								</select>
							</dd>
							<dt>
								<strong><label for="smiley_filename">', $txt['smileys_filename'], '</label>: </strong>
							</dt>
							<dd>';
	if (empty($context['filenames']))
		echo '
								<input type="text" name="smiley_filename" id="smiley_filename" value="', $context['current_smiley']['filename'], '" onchange="selectMethod(\'existing\');" class="input_text" />';
	else
	{
		echo '
									<select name="smiley_filename" id="smiley_filename" onchange="updatePreview();selectMethod(\'existing\');">';
		foreach ($context['filenames'] as $filename)
			echo '
									<option value="', $filename['id'], '"', $filename['selected'] ? ' selected="selected"' : '', '>', $filename['id'], '</option>';
		echo '
								</select>';
	}

	echo '
							</dd>
						</dl>
					</fieldset>
					<fieldset id="ul_settings" style="display: none;">
						<dl class="settings">
							<dt>
								<strong>', $txt['smileys_add_upload_choose'], ':</strong><br />
								<span class="smalltext">', $txt['smileys_add_upload_choose_desc'], '</span>
							</dt>
							<dd>
								<input type="file" name="uploadSmiley" id="uploadSmiley" onchange="selectMethod(\'upload\');" class="input_file" />
							</dd>
							<dt>
								<strong><label for="sameall">', $txt['smileys_add_upload_all'], ':</label></strong>
							</dt>
							<dd>
								<input type="checkbox" name="sameall" id="sameall" checked="checked" class="input_check" onclick="swapUploads(); selectMethod(\'upload\');" />
							</dd>
						</dl>
					</fieldset>

					<dl id="uploadMore" style="display: none;" class="settings">';

	foreach ($context['smiley_sets'] as $smiley_set)
		echo '
						<dt>
							', $txt['smileys_add_upload_for1'], ' <strong>', $smiley_set['name'], '</strong> ', $txt['smileys_add_upload_for2'], ':
						</dt>
						<dd>
							<input type="file" name="individual_', $smiley_set['name'], '" onchange="selectMethod(\'upload\');" class="input_file" />
						</dd>';

	echo '
					</dl>
				</div>
			</div>

			<div class="cat_bar">
				<h3 class="catbg">', $txt['smiley_new'], '</h3>
			</div>
			<div class="windowbg">
				<div class="content">
					<dl class="settings">
						<dt>
							<strong><label for="smiley_code">', $txt['smileys_code'], '</label>: </strong>
						</dt>
						<dd>
							<input type="text" name="smiley_code" id="smiley_code" value="" class="input_text" />
						</dd>
						<dt>
							<strong><label for="smiley_description">', $txt['smileys_description'], '</label>: </strong>
						</dt>
						<dd>
							<input type="text" name="smiley_description" id="smiley_description" value="" class="input_text" />
						</dd>
						<dt>
							<strong><label for="smiley_location">', $txt['smileys_location'], '</label>: </strong>
						</dt>
						<dd>
							<select name="smiley_location" id="smiley_location">
								<option value="0" selected="selected">
									', $txt['smileys_location_form'], '
								</option>
								<option value="1">
									', $txt['smileys_location_hidden'], '
								</option>
								<option value="2">
									', $txt['smileys_location_popup'], '
								</option>
							</select>
						</dd>
					</dl>
					<hr />
					<input type="submit" name="smiley_save" value="', $txt['smileys_save'], '" class="right_submit" />
					<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
				</div>
			</div>
		</form>
	</div>';
}

/**
 * Ordering smileys.
 */
function template_setorder()
{
	global $context, $settings, $scripturl, $txt, $modSettings;

	echo '
	<div id="admincenter">';

	foreach ($context['smileys'] as $location)
	{
		echo '
		<form action="', $scripturl, '?action=admin;area=smileys;sa=editsmileys" method="post" accept-charset="UTF-8">
			<div class="cat_bar">
				<h3 class="catbg">', $location['title'], '</h3>
			</div>
			<div class="information">
				', $location['description'], '
			</div>
			<div class="windowbg">
				<div class="content">
					<strong>', empty($context['move_smiley']) ? $txt['smileys_move_select_smiley'] : $txt['smileys_move_select_destination'], '...</strong><br />';
		foreach ($location['rows'] as $row)
		{
			if (!empty($context['move_smiley']))
				echo '
					<a href="', $scripturl, '?action=admin;area=smileys;sa=setorder;location=', $location['id'], ';source=', $context['move_smiley'], ';row=', $row[0]['row'], ';reorder=1;', $context['session_var'], '=', $context['session_id'], '"><img src="', $settings['images_url'], '/smiley_select_spot.png" alt="', $txt['smileys_move_here'], '" /></a>';

			foreach ($row as $smiley)
			{
				if (empty($context['move_smiley']))
					echo '<a href="', $scripturl, '?action=admin;area=smileys;sa=setorder;move=', $smiley['id'], '"><img src="', $modSettings['smileys_url'], '/', $modSettings['smiley_sets_default'], '/', $smiley['filename'], '" style="padding: 2px; border: 0px solid black;" alt="', $smiley['description'], '" /></a>';
				else
					echo '<img src="', $modSettings['smileys_url'], '/', $modSettings['smiley_sets_default'], '/', $smiley['filename'], '" style="padding: 2px; border: ', $smiley['selected'] ? '2px solid red' : '0px solid black', ';" alt="', $smiley['description'], '" /><a href="', $scripturl, '?action=admin;area=smileys;sa=setorder;location=', $location['id'], ';source=', $context['move_smiley'], ';after=', $smiley['id'], ';reorder=1;', $context['session_var'], '=', $context['session_id'], '" title="', $txt['smileys_move_here'], '"><img src="', $settings['images_url'], '/smiley_select_spot.png" alt="', $txt['smileys_move_here'], '" /></a>';
			}

			echo '
					<br />';
		}
		if (!empty($context['move_smiley']))
			echo '
					<a href="', $scripturl, '?action=admin;area=smileys;sa=setorder;location=', $location['id'], ';source=', $context['move_smiley'], ';row=', $location['last_row'], ';reorder=1;', $context['session_var'], '=', $context['session_id'], '"><img src="', $settings['images_url'], '/smiley_select_spot.png" alt="', $txt['smileys_move_here'], '" /></a>';
		echo '
				</div>
			</div>
		<input type="hidden" name="reorder" value="1" />
	</form>';
	}

	echo '
	</div>';
}

/**
 * Editing an individual message icon.
 */
function template_editicon()
{
	global $context, $scripturl, $txt;

	echo '
	<div id="admincenter">
		<form action="', $scripturl, '?action=admin;area=smileys;sa=editicon;icon=', $context['new_icon'] ? '0' : $context['icon']['id'], '" method="post" accept-charset="UTF-8">
			<div class="cat_bar">
				<h3 class="catbg">
					', $context['new_icon'] ? $txt['icons_new_icon'] : $txt['icons_edit_icon'], '
				</h3>
			</div>
			<div class="windowbg">
				<div class="content">
					<dl class="settings">';

	if (!$context['new_icon'])
		echo '
						<dt>
							<strong>', $txt['smiley_preview'], ': </strong>
						</dt>
						<dd>
							<img src="', $context['icon']['image_url'], '" alt="', $context['icon']['title'], '" />
						</dd>';

	echo '
						<dt>
							<strong><label for="icon_filename">', $txt['smileys_filename'], '</label>: </strong><br /><span class="smalltext">', $txt['icons_filename_all_png'], '</span>
						</dt>
						<dd>
							<input type="text" name="icon_filename" id="icon_filename" value="', !empty($context['icon']['filename']) ? $context['icon']['filename'] . '.png' : '', '" class="input_text" />
						</dd>
						<dt>
							<strong><label for="icon_description">', $txt['smileys_description'], '</label>: </strong>
						</dt>
						<dd>
							<input type="text" name="icon_description" id="icon_description" value="', !empty($context['icon']['title']) ? $context['icon']['title'] : '', '" class="input_text" />
						</dd>
						<dt>
							<strong><label for="icon_board_select">', $txt['icons_board'], '</label>: </strong>
						</dt>
						<dd>', template_select_boards('icon_board'), '
						</dd>
						<dt>
							<strong><label for="icon_location">', $txt['smileys_location'], '</label>: </strong>
						</dt>
						<dd>
							<select name="icon_location" id="icon_location">
								<option value="0"', empty($context['icon']['after']) ? ' selected="selected"' : '', '>', $txt['icons_location_first_icon'], '</option>';

	// Print the list of all the icons it can be put after...
	foreach ($context['icons'] as $id => $data)
		if (empty($context['icon']['id']) || $id != $context['icon']['id'])
			echo '
								<option value="', $id, '"', !empty($context['icon']['after']) && $id == $context['icon']['after'] ? ' selected="selected"' : '', '>', $txt['icons_location_after'], ': ', $data['title'], '</option>';

	echo '
							</select>
						</dd>
					</dl>';

	if (!$context['new_icon'])
		echo '
					<input type="hidden" name="icon" value="', $context['icon']['id'], '" />';

	echo '
					<hr />
					<input type="submit" name="icons_save" value="', $txt['smileys_save'], '" class="right_submit" />
					<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
				</div>
			</div>
		</form>
	</div>';
}