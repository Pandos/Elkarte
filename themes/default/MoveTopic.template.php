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
 * Show an interface for selecting which board to move a post to.
 */
function template_main()
{
	global $context, $txt, $scripturl;

	echo '
	<div id="move_topic">
		<form action="', $scripturl, '?action=movetopic2;current_board=' . $context['current_board'] . ';topic=', $context['current_topic'], '.0" method="post" accept-charset="UTF-8" onsubmit="submitonce(this);">
			<div class="cat_bar">
				<h3 class="catbg">', $txt['move_topic'], '</h3>
			</div>
			<div class="windowbg centertext">
				<div class="content">
					<div class="move_topic">
						<dl class="settings">
							<dt>
								<strong>', $txt['move_to'], ':</strong>
							</dt>
							<dd>', template_select_boards('toboard'), '
							</dd>';

	// Disable the reason textarea when the postRedirect checkbox is unchecked...
	echo '
						</dl>
						<label for="reset_subject"><input type="checkbox" name="reset_subject" id="reset_subject" onclick="document.getElementById(\'subjectArea\').style.display = this.checked ? \'block\' : \'none\';" class="input_check" /> ', $txt['moveTopic2'], '.</label><br />
						<fieldset id="subjectArea" style="display: none;">
							<dl class="settings">
								<dt><strong>', $txt['moveTopic3'], ':</strong></dt>
								<dd><input type="text" name="custom_subject" size="30" value="', $context['subject'], '" class="input_text" /></dd>
							</dl>
							<label for="enforce_subject"><input type="checkbox" name="enforce_subject" id="enforce_subject" class="input_check" /> ', $txt['moveTopic4'], '.</label>
						</fieldset>
						<label for="postRedirect"><input type="checkbox" name="postRedirect" id="postRedirect" ', $context['is_approved'] ? 'checked="checked"' : '', ' onclick="', $context['is_approved'] ? '' : 'if (this.checked && !confirm(\'' . $txt['move_topic_unapproved_js'] . '\')) return false; ', 'document.getElementById(\'reasonArea\').style.display = this.checked ? \'block\' : \'none\';" class="input_check" /> ', $txt['moveTopic1'], '.</label>
						<fieldset id="reasonArea" style="margin-top: 1ex;', $context['is_approved'] ? '' : 'display: none;', '">
							<dl class="settings">
								<dt>
									', $txt['moved_why'], '
								</dt>
								<dd>
									<textarea name="reason" rows="3" cols="40">', $txt['movetopic_default'], '</textarea>
								</dd>
								<dt>
									<label for="redirect_topic">', $txt['movetopic_redirect'], '</label>
								</dt>
								<dd>
									<input type="checkbox" name="redirect_topic" id="redirect_topic" checked="checked" class="input_check" />
								</dd>
								<dt>
									', $txt['movetopic_expires'], '
								</dt>
								<dd>
									<select name="redirect_expires">
										<option value="0" selected="selected">', $txt['never'], '</option>
										<option value="1440">', $txt['one_day'], '</option>
										<option value="10080">', $txt['one_week'], '</option>
										<option value="20160">', $txt['two_weeks'], '</option>
										<option value="43200">', $txt['one_month'], '</option>
										<option value="86400">', $txt['two_months'], '</option>
									</select>
								</dd>
							</dl>
						</fieldset>
						<input type="submit" value="', $txt['move_topic'], '" onclick="return submitThisOnce(this);" accesskey="s" class="right_submit" />
					</div>
				</div>
			</div>';

	if ($context['back_to_topic'])
		echo '
			<input type="hidden" name="goback" value="1" />';

	echo '
			<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
			<input type="hidden" name="seqnum" value="', $context['form_sequence_number'], '" />
		</form>
	</div>';
}