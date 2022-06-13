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
          var start = new Date(min_start);
          const date_string = start.toISOString().slice(0,10) + ' ' + start.toTimeString().slice(0,8);
             
          $('#event_start_date').attr('mindate', date_string ).trigger('change');
        }
      });
      $('#event_start_date').change(function() {
          console.log($(this).val());
          const start = new Date($(this).val());
          var default_end = new Date();
          default_end.setDate(start.getDate()+1);
          default_end.setHours(start.getHours());
          default_end.setMinutes(start.getMinutes());
          default_end.setSeconds(start.getSeconds());
          console.log(default_end);
          const date_string = default_end.toISOString().slice(0,10) + ' ' + default_end.toTimeString().slice(0,8);
          $('#event_end_date').val(date_string).trigger('change');
      });       
      $('#event_start_date').val('{/literal}{$start_time}{literal}').trigger('change');
      $('#resource').trigger('change');
    });
</script>
{/literal}
