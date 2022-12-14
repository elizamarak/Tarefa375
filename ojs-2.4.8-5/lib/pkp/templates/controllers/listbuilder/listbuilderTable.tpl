{**
 * templates/controllers/listbuilder/listbuilderBody.tpl
 *
 * Copyright (c) 2013-2019 Simon Fraser University
 * Copyright (c) 2000-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * The table part of a Listbuilder object
 *}

<table id="{$gridTableId|escape}">
	{if $columns|@count > 1}{* include column titles as th elements if there is more than one column *}
		<thead>
			<tr>
			{foreach from=$columns item=column}
				<th>{$column->getLocalizedTitle()|escape}</th>
			{/foreach}
			</tr>
		</thead>
	{/if}
	<tbody>
		{foreach from=$rows item=lb_row}
			{$lb_row}
		{/foreach}
		{**
			We need the last (=empty) line even if we have rows
			so that we can restore it if the user deletes all rows.
		**}
		<tr class="empty"{if $rows} style="display: none;"{/if}>
			<td colspan="{$columns|@count}">{translate key="grid.noItems"}</td>
		</tr>
		<div class="newRow"></div>
	</tbody>
</table>
