{if $eventTypes == TRUE}
    <select id="event_selector" class="crm-form-select crm-select2 crm-action-menu fa-plus">
        <option value="all">{ts}All{/ts}</option>
        {foreach from=$eventTypes item=type}
            <option value="{$type}">{$type}</option>
        {/foreach}
    </select>
{/if}
<div id="calendar"></div>
{literal}
<script type="text/javascript">
    if (typeof (jQuery) != 'function') {
      var jQuery = cj;
    } else {
      var cj = jQuery;
    }

    cj(function ( ) {
      checkFullCalendarLIbrary()
              .then(function () {
                buildCalendar();
              })
              .catch(function () {
                alert('Error loading calendar, try refreshing...');
              });
    });

    /*
     * Checks if full calendar API is ready.
     *
     * @returns {Promise}
     *  if library is available or not.
     */
    function checkFullCalendarLIbrary() {
      return new Promise((resolve, reject) => {
        if (cj.fullCalendar) {
          resolve();
        } else {
          cj(document).ajaxComplete(function () {
            if (cj.fullCalendar) {
              resolve();
            } else {
              reject();
            }
          });
        }
      });
    }

    function buildCalendar( ) {
      var showTime = {/literal}{$time_display}{literal};
      var weekStartDay = {/literal}{$weekBeginDay}{literal};
      var use24HourFormat = {/literal}{$use24Hour}{literal};
      var calendarId = {/literal}{$calendar_id}{literal};

      cj('#calendar').fullCalendar({
        events: {
          url: '/civicrm/ajax/eventcalendar',
          data: {
            calendar_id: {/literal}{$calendar_id}{literal},
          }
        },
        failure: function () {
          alert('there was an error while fetching events!');
        },
        dayClick: function (date, jsEvent, view) {
          console.log('Clicked on: ' + date.format());
          location.href = "/civicrm/bookevent?date=" + date.format() + "&calendar_id=" + calendarId;
        },
        lang: 'da',
        displayEventEnd: true,
        displayEventTime: showTime ? 1 : 0,
        firstDay: weekStartDay,
        timeFormat: use24HourFormat ? 'HH:mm' : 'hh(:mm)A',
        header: {
          left: 'prev,next today',
          center: 'title',
          right: 'month,agendaWeek,agendaDay'
        },

        eventRender: function eventRender(event, element, view) {
          if (event.eventType && events_data.isfilter == "1") {
            return ['all', event.eventType].indexOf(cj('#event_selector').val()) >= 0
          }
        },
      });

      CRM.$(function ($) {
        $("#event_selector").change(function () {
          cj('#calendar').fullCalendar('rerenderEvents');
        });
      });
    }
</script>
{/literal}
