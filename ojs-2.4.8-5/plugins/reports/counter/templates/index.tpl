{**
 * plugins/reports/counter/templates/index.tpl
 *
 * Copyright (c) 2013-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Counter plugin index
 *}
{strip}
{assign var="pageTitle" value="plugins.reports.counter"}
{include file="common/header.tpl"}
{/strip}

<p>{translate key="plugins.reports.counter.description"}</p>
<h3>{translate key="plugins.reports.counter.release"} {$release}</h3>
<ul>
	{foreach from=$available key="report" item="reportfile"}
		<li>
			{translate key="plugins.reports.counter.$report.title"|lower}: {foreach from=$years item=year}&nbsp;&nbsp;<a href="{url path="CounterReportPlugin" type="fetch" release=$release report=$report year=$year}">{$year|escape}</a>{/foreach}
		</li>
	{/foreach}
</ul>

{if $showLegacy}
<h3>{translate key="plugins.reports.counter.olderReports"}</h3>
<p>{translate key="plugins.reports.counter.1a.introduction"}</p>
<ul>
	<li>{translate key="plugins.reports.counter.1a.title"}{foreach from=$years item=year}&nbsp;&nbsp;<a href="{url path="CounterReportPlugin" type="report" year=$year}">{$year|escape}</a>{/foreach}</li>
	<li>{translate key="plugins.reports.counter.1a.xml"} {foreach from=$years item=year}&nbsp;&nbsp;<a href="{url path="CounterReportPlugin" type="reportxml" year=$year}">{$year|escape}</a>{/foreach}</li>
</ul>

{if $legacyYears}
	<p>{translate key="plugins.reports.counter.legacyStats"}</p>
	<ul>
		<li>{translate key="plugins.reports.counter.1a.title"}{foreach from=$legacyYears item=year}&nbsp;&nbsp;<a href="{url path="CounterReportPlugin" type="report" year=$year useOldCounterStats=true}">{$year|escape}</a>{/foreach}</li>
		<li>{translate key="plugins.reports.counter.1a.xml"} {foreach from=$legacyYears item=year}&nbsp;&nbsp;<a href="{url path="CounterReportPlugin" type="reportxml" year=$year useOldCounterStats=true}">{$year|escape}</a>{/foreach}</li>
	</ul>
{/if}
{/if}

{include file="common/footer.tpl"}
