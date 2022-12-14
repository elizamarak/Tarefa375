{**
 * templates/layoutEditor/index.tpl
 *
 * Copyright (c) 2013-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Layout editor index.
 *
 *}
{strip}
{assign var="pageTitle" value="user.role.layoutEditor"}
{include file="common/header.tpl"}
{/strip}

<div id="submissions">
<h3>{translate key="article.submissions"}</h3>

<ul>
	<li><a href="{url op="submissions" path="active"}">{translate key="common.queue.short.active"}</a></li>
	<li><a href="{url op="submissions" path="completed"}">{translate key="common.queue.short.completed"}</a></li>
</ul>
</div>

<div id="issues">
<h3>{translate key="editor.navigation.issues"}</h3>

<ul>
	<li><a href="{url op="futureIssues"}">{translate key="editor.navigation.futureIssues"}</a></li>
	<li><a href="{url op="backIssues"}">{translate key="editor.navigation.issueArchive"}</a></li>
</ul>
</div>

{include file="common/footer.tpl"}
