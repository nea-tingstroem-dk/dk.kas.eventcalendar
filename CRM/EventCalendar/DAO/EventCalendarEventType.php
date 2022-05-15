<?php

/**
 * @package CRM
 * @copyright CiviCRM LLC https://civicrm.org/licensing
 *
 * Generated from com.osseed.eventcalendar/xml/schema/CRM/EventCalendar/EventCalendarType.xml
 * DO NOT EDIT.  Generated by CRM_Core_CodeGen
 * (GenCodeChecksum:42c6a344cabfae23dd08c36e41133263)
 */
use CRM_EventCalendar_ExtensionUtil as E;

/**
 * Database access object for the EventCalendarEventType entity.
 */
class CRM_EventCalendar_DAO_EventCalendarEventType extends CRM_Core_DAO {
  const EXT = E::LONG_NAME;
  const TABLE_ADDED = '4.4';

  /**
   * Static instance to hold the table name.
   *
   * @var string
   */
  public static $_tableName = 'civicrm_event_calendar_event_type';

  /**
   * Should CiviCRM log any modifications to this table in the civicrm_log table.
   *
   * @var bool
   */
  public static $_log = TRUE;

  /**
   * Unique EventCalendarEventType ID
   *
   * @var int
   */
  public $id;

  /**
   * FK to Event Calendar
   *
   * @var int
   */
  public $event_calendar_id;

  /**
   * Event Type id
   *
   * @var int
   */
  public $event_type;

  /**
   * Hex code for event type display color
   *
   * @var varchar(255)
   */
  public $event_color;

  /**
   * Class constructor.
   */
  public function __construct() {
    $this->__table = 'civicrm_event_calendar_event_type';
    parent::__construct();
  }

  /**
   * Returns localized title of this entity.
   *
   * @param bool $plural
   *   Whether to return the plural version of the title.
   */
  public static function getEntityTitle($plural = FALSE) {
    return $plural ? E::ts('Event Calendar Event Types') : E::ts('Event Calendar Event Type');
  }

  /**
   * Returns foreign keys and entity references.
   *
   * @return array
   *   [CRM_Core_Reference_Interface]
   */
  public static function getReferenceColumns() {
    if (!isset(Civi::$statics[__CLASS__]['links'])) {
      Civi::$statics[__CLASS__]['links'] = static::createReferenceColumns(__CLASS__);
      Civi::$statics[__CLASS__]['links'][] = new CRM_Core_Reference_Basic(self::getTableName(), 'event_calendar_id', 'civicrm_event_calendar', 'id');
      CRM_Core_DAO_AllCoreTables::invoke(__CLASS__, 'links_callback', Civi::$statics[__CLASS__]['links']);
    }
    return Civi::$statics[__CLASS__]['links'];
  }

  /**
   * Returns all the column names of this table
   *
   * @return array
   */
  public static function &fields() {
    if (!isset(Civi::$statics[__CLASS__]['fields'])) {
      Civi::$statics[__CLASS__]['fields'] = [
        'id' => [
          'name' => 'id',
          'type' => CRM_Utils_Type::T_INT,
          'description' => E::ts('Unique EventCalendarEventType ID'),
          'required' => TRUE,
          'where' => 'civicrm_event_calendar_event_type.id',
          'table_name' => 'civicrm_event_calendar_event_type',
          'entity' => 'EventCalendarEventType',
          'bao' => 'CRM_EventCalendar_DAO_EventCalendarEventType',
          'localizable' => 0,
          'readonly' => TRUE,
          'add' => '4.4',
        ],
        'event_calendar_id' => [
          'name' => 'event_calendar_id',
          'type' => CRM_Utils_Type::T_INT,
          'description' => E::ts('FK to Event Calendar'),
          'where' => 'civicrm_event_calendar_event_type.event_calendar_id',
          'table_name' => 'civicrm_event_calendar_event_type',
          'entity' => 'EventCalendarEventType',
          'bao' => 'CRM_EventCalendar_DAO_EventCalendarEventType',
          'localizable' => 0,
          'FKClassName' => 'CRM_EventCalendar_DAO_EventCalendar',
          'add' => '4.4',
        ],
        'event_type' => [
          'name' => 'event_type',
          'type' => CRM_Utils_Type::T_INT,
          'title' => E::ts('Event Type'),
          'description' => E::ts('Event Type id'),
          'where' => 'civicrm_event_calendar_event_type.event_type',
          'table_name' => 'civicrm_event_calendar_event_type',
          'entity' => 'EventCalendarEventType',
          'bao' => 'CRM_EventCalendar_DAO_EventCalendarEventType',
          'localizable' => 0,
          'add' => '4.4',
        ],
        'event_color' => [
          'name' => 'event_color',
          'type' => CRM_Utils_Type::T_VARCHAR(255),
          'title' => E::ts('Event Color'),
          'description' => E::ts('Hex code for event type display color'),
          'where' => 'civicrm_event_calendar_event_type.event_color',
          'table_name' => 'civicrm_event_calendar_event_type',
          'entity' => 'EventCalendarEventType',
          'bao' => 'CRM_EventCalendar_DAO_EventCalendarEventType',
          'localizable' => 0,
          'add' => '4.4',
        ],
      ];
      CRM_Core_DAO_AllCoreTables::invoke(__CLASS__, 'fields_callback', Civi::$statics[__CLASS__]['fields']);
    }
    return Civi::$statics[__CLASS__]['fields'];
  }

  /**
   * Return a mapping from field-name to the corresponding key (as used in fields()).
   *
   * @return array
   *   Array(string $name => string $uniqueName).
   */
  public static function &fieldKeys() {
    if (!isset(Civi::$statics[__CLASS__]['fieldKeys'])) {
      Civi::$statics[__CLASS__]['fieldKeys'] = array_flip(CRM_Utils_Array::collect('name', self::fields()));
    }
    return Civi::$statics[__CLASS__]['fieldKeys'];
  }

  /**
   * Returns the names of this table
   *
   * @return string
   */
  public static function getTableName() {
    return self::$_tableName;
  }

  /**
   * Returns if this table needs to be logged
   *
   * @return bool
   */
  public function getLog() {
    return self::$_log;
  }

  /**
   * Returns the list of fields that can be imported
   *
   * @param bool $prefix
   *
   * @return array
   */
  public static function &import($prefix = FALSE) {
    $r = CRM_Core_DAO_AllCoreTables::getImports(__CLASS__, 'event_calendar_event_type', $prefix, []);
    return $r;
  }

  /**
   * Returns the list of fields that can be exported
   *
   * @param bool $prefix
   *
   * @return array
   */
  public static function &export($prefix = FALSE) {
    $r = CRM_Core_DAO_AllCoreTables::getExports(__CLASS__, 'event_calendar_event_type', $prefix, []);
    return $r;
  }

  /**
   * Returns the list of indices
   *
   * @param bool $localize
   *
   * @return array
   */
  public static function indices($localize = TRUE) {
    $indices = [];
    return ($localize && !empty($indices)) ? CRM_Core_DAO_AllCoreTables::multilingualize(__CLASS__, $indices) : $indices;
  }

}
