{**
 * templates/user/login.tpl
 *
 * Copyright (c) 2013-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * User login form.
 *
 *}
{strip}
{assign var="helpTopicId" value="user.registerAndProfile"}
{assign var="registerOp" value="register"}
{assign var="registerLocaleKey" value="user.login.registerNewAccount"}
{include file="core:user/login.tpl"}
{/strip}

