{crmScript ext=com.osseed.eventcalendar file=js/jscolor.js}
{crmScript ext=com.osseed.eventcalendar file=js/eventcalendar.js}

<div class="help">
    <p>{ts}Manage your Event Calendars here{/ts}</p>
</div>

<div class="crm-content-block crm-block">
    {if $rows}
        <div id="ltype">
            {strip}
                {* handle enable/disable actions*}
                {include file="CRM/common/enableDisableApi.tpl"}
                {include file="CRM/common/jsortable.tpl"}
                <table id="options" class="display">
                    <thead>
                        <tr>
                            <th id="sortable">{ts}Calendar Title{/ts}</th>
                            <th id="sortable">{ts}Calendar Type{/ts}</th>
                            <th id="sortable">{ts}ID{/ts}</th>
                            <th></th>
                        </tr>
                    </thead>
                    {foreach from=$rows item=row}
                        <tr id="calendar-{$row.id}" class="crm-entity {cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if} ">
                            <td class="crm-calendar-title" data-field="calendar_title">{$row.calendar_title}</td>
                            <td class="crm-calendar-title" data-field="calendar_type">{$row.calendar_type}</td>
                            <td class="crm-calendar-id" data-field="id">{$row.id}</td>
                            <td>{$row.action|replace:'xx':$row.id}</td>
                        </tr>
                    {/foreach}
                </table>
            {/strip}
        </div>
    {else}
        <div class="messages status no-popup">
            <img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/>
            {ts}None found.{/ts}
        </div>
    {/if}
    <div class="action-link">
        {crmButton p="civicrm/eventcalendarsettings" q="action=add&reset=1" icon="plus-circle"}{ts}Add Event Calendar{/ts}{/crmButton}
        {if $resources}
            {foreach from=$resources item=resource}
                <a href="/civicrm/eventcalendarsettings?action=add&resource={$resource.resource_id}&reset=1" 
                   class="button">
                    <span>
                        <i class="crm-i fa-plus-circle" aria-hidden="true"></i> Add {$resource.label} Calendar
                    </span>
                </a>
            {/foreach}
        {/if}
        {crmButton p="civicrm/admin" q="reset=1" class="cancel" icon="times"}{ts}Done{/ts}{/crmButton}
    </div>

</div>
