{**
 * plugins/blocks/user/block.tpl
 *
 * Copyright (c) 2013-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Common site sidebar menu -- user tools.
 *
 *}
<div class="block" id="sidebarUser">
	{if !$implicitAuth || $implicitAuth === $smarty.const.IMPLICIT_AUTH_OPTIONAL}
		<span class="blockTitle">{translate key="navigation.user"}</span>
	{/if}

	{if $isUserLoggedIn}
		{translate key="plugins.block.user.loggedInAs"}<br />
		<strong>{$loggedInUsername|escape}</strong>
		<ul>
			{if $hasOtherJournals}
				<li><a href="{url journal="index" page="user"}">{translate key="plugins.block.user.myJournals"}</a></li>
			{/if}
			<li><a href="{url page="user" op="profile"}">{translate key="plugins.block.user.myProfile"}</a></li>
			<li><a href="{url page="login" op="signOut"}">{translate key="plugins.block.user.logout"}</a></li>
			{if $userSession->getSessionVar('signedInAs')}
				<li><a href="{url page="login" op="signOutAsUser"}">{translate key="plugins.block.user.signOutAsUser"}</a></li>
			{/if}
		</ul>
	{else}
		{if $implicitAuth}
			{if $implicitAuth === $smarty.const.IMPLICIT_AUTH_OPTIONAL}
				<span class="blockTitle">{translate key="user.login.implicitAuth"}</span>
			{/if}
			<a href="{url page="login" op="implicitAuthLogin"}">{translate key="plugins.block.user.implicitAuthLogin"}</a>
			{if $implicitAuth === $smarty.const.IMPLICIT_AUTH_OPTIONAL}
				<span class="blockTitle">{translate key="user.login.localAuth"}</span>
			{/if}
		{/if}
		{if !$implicitAuth || $implicitAuth === $smarty.const.IMPLICIT_AUTH_OPTIONAL}
			{if $userBlockLoginSSL}
				<a href="{$userBlockLoginUrl}">{translate key="user.login"}</a>
			{else}
				<form method="post" action="{$userBlockLoginUrl}">
					<table>
						<tr>
							<td><label for="sidebar-username">{translate key="user.username"}</label></td>
							<td><input type="text" id="sidebar-username" name="username" value="" size="12" maxlength="32" class="textField" /></td>
						</tr>
						<tr>
							<td><label for="sidebar-password">{translate key="user.password"}</label></td>
							<td><input type="password" id="sidebar-password" name="password" value="" size="12" class="textField" /></td>
						</tr>
						<tr>
							<td colspan="2"><input type="checkbox" id="remember" name="remember" value="1" /> <label for="remember">{translate key="plugins.block.user.rememberMe"}</label></td>
						</tr>
						<tr>
							<td colspan="2"><input type="submit" value="{translate key="user.login"}" class="button" /></td>
						</tr>
					</table>
				</form>
			{/if}
		{/if}
	{/if}
</div>
