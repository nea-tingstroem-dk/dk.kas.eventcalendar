<?php
use CRM_EventCalendar_ExtensionUtil as E;

class CRM_EventCalendar_BAO_EventCalendarParticipant extends CRM_EventCalendar_DAO_EventCalendarParticipant {

  /**
   * Create a new EventCalendarParticipant based on array-data
   *
   * @param array $params key-value pairs
   * @return CRM_EventCalendar_DAO_EventCalendarParticipant|NULL
   *
  public static function create($params) {
    $className = 'CRM_EventCalendar_DAO_EventCalendarParticipant';
    $entityName = 'EventCalendarParticipant';
    $hook = empty($params['id']) ? 'create' : 'edit';

    CRM_Utils_Hook::pre($hook, $entityName, CRM_Utils_Array::value('id', $params), $params);
    $instance = new $className();
    $instance->copyValues($params);
    $instance->save();
    CRM_Utils_Hook::post($hook, $entityName, $instance->id, $instance);

    return $instance;
  } */

}
