{* HEADER *}

{* FIELD EXAMPLE: OPTION 1 (AUTOMATIC LAYOUT) *}

{foreach from=$elementNames item=elementName}
    <div class="crm-section">
        <div class="label">{$form.$elementName.label}</div>
        <div class="content">{$form.$elementName.html}</div>
        <div class="clear"></div>
    </div>
{/foreach}

{* FIELD EXAMPLE: OPTION 2 (MANUAL LAYOUT)

<div>
<span>{$form.favorite_color.label}</span>
<span>{$form.favorite_color.html}</span>
</div>

{* FOOTER *}
<div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
{literal}
<script type="text/javascript">
    CRM.$(function ($) {
      const resources = {/literal}{$resources}{literal};
      const start_date = new Date(Date.parse('{/literal}{$start_time}{literal}'));
      $('#resource').change(function () {
        if ($(this).val()) {
          let min_start = Date.now();
          let max_end = start_date;
          for (id of $(this).val() ?? []) {
            let obj = resources[id];
            let min = Date.parse(obj.min_start);
            let max = Date.parse(obj.max_end);
            min_start = Math.max(min, min_start);
            max_end = Math.min(max, max_end);
          }
          if (min_start < Date.now()) {
              min_start = Date.now();
          }
              
          $('#event_start_date').attr('mindate', ).trigger('change');
        }
      });
      $('#event_start_date').val('{/literal}{$start_time}{literal}').trigger('change');
      console.log("and here");
    });
</script>
{/literal}
