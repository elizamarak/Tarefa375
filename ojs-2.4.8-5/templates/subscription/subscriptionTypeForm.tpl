{**
 * templates/subscription/subscriptionTypeForm.tpl
 *
 * Copyright (c) 2013-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Subscription type form under journal management.
 *
 *}
{strip}
{if $typeId}
{assign var="pageTitle" value="manager.subscriptionTypes.edit"}

{else}
	{assign var="pageTitle" value="manager.subscriptionTypes.create"}
{/if}
{assign var="pageId" value="manager.subscriptionTypes.subscriptionTypeForm"}
{assign var="pageCrumbTitle" value=$subscriptionTypeTitle}
{include file="common/header.tpl"}
{/strip}

{if $subscriptionTypeCreated}
<br />
{translate key="manager.subscriptionTypes.subscriptionTypeCreatedSuccessfully"}<br />
{/if}

<br />

<form id="subscriptionType" method="post" action="{url op="updateSubscriptionType"}">
{if $typeId}
<input type="hidden" name="typeId" value="{$typeId|escape}" />
{/if}

{include file="common/formErrors.tpl"}
<table class="data" width="100%">
{if count($formLocales) > 1}
	<tr valign="top">
		<td width="20%" class="label">{fieldLabel name="formLocale" key="form.formLanguage"}</td>
		<td width="80%" class="value">
			{if $typeId}{url|assign:"subscriptionTypeUrl" op="editSubscriptionType" path=$typeId escape=false}
			{else}{url|assign:"subscriptionTypeUrl" op="createSubscriptionType" escape=false}
			{/if}
			{form_language_chooser form="subscriptionType" url=$subscriptionTypeUrl}
			<span class="instruct">{translate key="form.formLanguage.description"}</span>
		</td>
	</tr>
{/if}
<tr valign="top">
	<td width="20%" class="label">{fieldLabel name="name" required="true" key="manager.subscriptionTypes.form.typeName"}</td>
	<td width="80%" class="value"><input type="text" name="name[{$formLocale|escape}]" value="{$name[$formLocale]|escape}" size="35" maxlength="80" id="name" class="textField" /></td>
</tr>
<tr valign="top">
	<td class="label">{fieldLabel name="description" key="manager.subscriptionTypes.form.description"}</td>
	<td class="value"><textarea name="description[{$formLocale|escape}]" id="description" cols="40" rows="4" class="textArea">{$description[$formLocale]|escape}</textarea></td>
</tr>
<tr valign="top">
	<td class="label">{fieldLabel name="cost" required="true" key="manager.subscriptionTypes.form.cost"}</td>
	<td class="value">
		<input type="text" name="cost" value="{$cost|escape}" size="5" maxlength="10" id="cost" class="textField" />
		<br />
		<span class="instruct">{translate key="manager.subscriptionTypes.form.costInstructions"}</span>
	</td>
</tr>
<tr valign="top">
	<td class="label">{fieldLabel name="currency" required="true" key="manager.subscriptionTypes.form.currency"}</td>
	<td><select name="currency" id="currency" class="selectMenu">{html_options options=$validCurrencies selected=$currency}</select></td>
</tr>
<tr valign="top">
	<td class="label">{fieldLabel name="format" required="true" key="manager.subscriptionTypes.form.format"}</td>
	<td><select id="format" name="format" class="selectMenu">{html_options options=$validFormats selected=$format}</select></td>
</tr>
{if !$typeId}
<tr valign="top">
	<td class="label">{fieldLabel name="duration" required="true" key="manager.subscriptionTypes.form.duration"}</td>
	<td class="value">
		<input type="radio" name="nonExpiring" id="nonExpiring-0" value="0"{if !$nonExpiring} checked="checked"{/if} />&nbsp;{translate key="manager.subscriptionTypes.form.nonExpiring.expiresAfter"} <input type="text" name="duration" value="{$duration|escape}" size="5" maxlength="10" id="duration" class="textField" /> {translate key="manager.subscriptionTypes.form.nonExpiring.months"}
	</td>
</tr>
<tr valign="top">
	<td>&nbsp;</td>
	<td class="value">
		<input type="radio" name="nonExpiring" id="nonExpiring-1" value="1"{if $nonExpiring} checked="checked"{/if} />&nbsp;{translate key="manager.subscriptionTypes.form.nonExpiring.neverExpires"}
	</td>
</tr>
{elseif $typeId && !$nonExpiring}
<tr valign="top">
	<td class="label">{fieldLabel name="duration" required="true" key="manager.subscriptionTypes.form.duration"}</td>
	<td class="value">
		<input type="text" name="duration" value="{$duration|escape}" size="5" maxlength="10" id="duration" class="textField" />
		<br />
		<span class="instruct">{translate key="manager.subscriptionTypes.form.durationInstructions"}</span>
	</td>
</tr>
{/if}
{if !$typeId}
<tr valign="top">
	<td class="label">{fieldLabel name="subscriptions" key="manager.subscriptionTypes.form.subscriptions"}</td>
	<td class="value">
		<input type="radio" name="institutional" id="institutional-0" value="0"{if !$institutional} checked="checked"{/if} />&nbsp;{translate key="manager.subscriptionTypes.form.individual"}
	</td>
</tr>
<tr valign="top">
	<td>&nbsp;</td>
	<td class="value">
		<input type="radio" name="institutional" id="institutional-1" value="1"{if $institutional} checked="checked"{/if} />&nbsp;{translate key="manager.subscriptionTypes.form.institutional"}
	</td>
</tr>
{/if}
<tr valign="top">
	<td class="label">{fieldLabel name="options" key="manager.subscriptionTypes.form.options"}</td>
	<td class="value">
		<input type="checkbox" name="membership" id="membership" value="1"{if $membership} checked="checked"{/if} />&nbsp;{fieldLabel name="membership" key="manager.subscriptionTypes.form.membership"}
	</td>
</tr>
<tr valign="top">
	<td>&nbsp;</td>
	<td class="value">
		<input type="checkbox" name="disable_public_display" id="disable_public_display" value="1"{if $disable_public_display} checked="checked"{/if} />&nbsp;{fieldLabel name="disable_public_display" key="manager.subscriptionTypes.form.public"}
	</td>
</tr>
</table>

<p><input type="submit" value="{translate key="common.save"}" class="button defaultButton" /> {if not $typeId}<input type="submit" name="createAnother" value="{translate key="manager.subscriptionTypes.form.saveAndCreateAnotherType"}" class="button" /> {/if}<input type="button" value="{translate key="common.cancel"}" class="button" onclick="document.location.href='{url op="subscriptionTypes" escape=false}'" /></p>
</form>

<p><span class="formRequired">{translate key="common.requiredField"}</span></p>

{include file="common/footer.tpl"}

