{**
 * templates/sectionEditor/setDueDate.tpl
 *
 * Copyright (c) 2013-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Form to set the due date for a review.
 *
 *}
{strip}
{assign var="pageTitle" value="submission.dueDate"}
{include file="common/header.tpl"}
{/strip}
<div id="setDueDate">
<h3>{translate key="editor.article.designateDueDate"}</h3>

<p>{translate key="editor.article.designateDueDateDescription"}</p>

<form method="post" action="{url op=$actionHandler path=$articleId|to_array:$reviewId}">
	<table class="data" width="100%">
		<tr valign="top">
			<td class="label" width="20%">{translate key="editor.article.todaysDate"}</td>
			<td class="value" width="80%">{$todaysDate|escape}</td>
		</tr>
		<tr valign="top">
			<td class="label">{translate key="editor.article.requestedByDate"}</td>
			<td class="value">
				<input type="text" size="11" maxlength="10" name="dueDate" value="{if $dueDate}{$dueDate|date_format:"%Y-%m-%d"}{/if}" class="textField" onfocus="this.form.numWeeks.value=''" />
				<span class="instruct">{translate key="editor.article.dueDateFormat"}</span>
			</td>
		</tr>
		<tr valign="top">
			<td>&nbsp;</td>
			<td class="value"><span class="instruct">{translate key="common.or"}</span></td>
		</tr>
		<tr valign="top">
			<td class="label">{translate key="editor.article.numberOfWeeks"}</td>
			<td class="value"><input type="text" name="numWeeks" value="{if not $dueDate}{$numWeeksPerReview|escape}{/if}" size="3" maxlength="2" class="textField" onfocus="this.form.dueDate.value=''" /></td>
		</tr>
	</table>
<p><input type="submit" value="{translate key="common.continue"}" class="button defaultButton" /> <input type="button" class="button" onclick="history.go(-1)" value="{translate key="common.cancel"}" /></p>
</form>
</div>
{include file="common/footer.tpl"}

