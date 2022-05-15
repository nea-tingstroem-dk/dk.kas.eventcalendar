<?php
// This file declares a new entity type. For more details, see "hook_civicrm_entityTypes" at:
// https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
return [
  [
    'name' => 'EventCalendarParticipant',
    'class' => 'CRM_EventCalendar_DAO_EventCalendarParticipant',
    'table' => 'civicrm_event_calendar_participant',
  ],
];
