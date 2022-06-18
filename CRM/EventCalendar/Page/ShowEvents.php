<?php

/*
  +--------------------------------------------------------------------+
  | CiviCRM version 4.3                                                |
  +--------------------------------------------------------------------+
  | Copyright CiviCRM LLC (c) 2004-2013                                |
  +--------------------------------------------------------------------+
  | This file is a part of CiviCRM.                                    |
  |                                                                    |
  | CiviCRM is free software; you can copy, modify, and distribute it  |
  | under the terms of the GNU Affero General Public License           |
  | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
  |                                                                    |
  | CiviCRM is distributed in the hope that it will be useful, but     |
  | WITHOUT ANY WARRANTY; without even the implied warranty of         |
  | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
  | See the GNU Affero General Public License for more details.        |
  |                                                                    |
  | You should have received a copy of the GNU Affero General Public   |
  | License and the CiviCRM Licensing Exception along                  |
  | with this program; if not, contact CiviCRM LLC                     |
  | at info[AT]civicrm[DOT]org. If you have questions about the        |
  | GNU Affero General Public License or the licensing of CiviCRM,     |
  | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
  +--------------------------------------------------------------------+
 */

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2011
 * $Id$
 *
 */
require_once 'CRM/Core/Page.php';

class CRM_EventCalendar_Page_ShowEvents extends CRM_Core_Page {

    public function run() {
        CRM_Core_Resources::singleton()->addScriptFile('com.osseed.eventcalendar', 'js/moment.js', 5);
        CRM_Core_Resources::singleton()->addScriptFile('com.osseed.eventcalendar', 'js/fullcalendar/fullcalendar.js', 10);
        CRM_Core_Resources::singleton()->addScriptFile('com.osseed.eventcalendar', 'js/fullcalendar/locale/da.js', 15);
        CRM_Core_Resources::singleton()->addStyleFile('com.osseed.eventcalendar', 'css/civicrm_events.css');
        CRM_Core_Resources::singleton()->addStyleFile('com.osseed.eventcalendar', 'css/fullcalendar.css');

        $eventTypesFilter = array();
        $eventSettings = [];
        $civieventTypesList = CRM_Event_PseudoConstant::eventType();

        $config = CRM_Core_Config::singleton();
        //get settings
        $calendarId = isset($_GET['id']) ? $_GET['id'] : false;
        if (!$calendarId) {
            return;
        }
        $settings = $this->_eventCalendar_getSettings($calendarId);
        //set title from settings; allow empty value so we don't duplicate titles
        CRM_Utils_System::setTitle(ts($settings['calendar_title']));

        $this->assign('calendar_id', $calendarId);
        $this->assign('time_display', !empty($settings['event_time']) ?: 'false');
        $this->assign('displayEventEnd', $settings['event_end_date']);

        //Check weekBegin settings from calendar configuration
        if (isset($settings['week_begins_from_day']) && $settings['week_begins_from_day'] == 1) {
            //Get existing setting for weekday from civicrm start & set into event calendar.
            $weekBegins = Civi::settings()->get('weekBegins');
        }
        $weekBegins = $weekBegins ? $weekBegins : 0;
        $this->assign('weekBeginDay', $weekBegins);

        $this->assign('use24Hour', isset($settings['time_format_24_hour']) ?
                $settings['time_format_24_hour']: false);
        parent::run();
    }

    /**
     * retrieve and reconstruct extension settings
     */
    public static function _eventCalendar_getSettings($calendarId = 0) {
        $settings = array();

        if ($calendarId) {
            $settings['calendar_id'] = $calendarId;
            $sql = "SELECT * FROM civicrm_event_calendar WHERE `id` = {$calendarId};";
            $dao = CRM_Core_DAO::executeQuery($sql);
            while ($dao->fetch()) {
                $settings['calendar_title'] = $dao->calendar_title;
                $settings['calendar_type'] = $dao->calendar_type;
                $settings['event_past'] = $dao->show_past_events;
                $settings['event_end_date'] = $dao->show_end_date;
                $settings['event_is_public'] = $dao->show_public_events;
                $settings['event_month'] = $dao->events_by_month;
                $settings['event_from_month'] = $dao->events_from_month;
                $settings['event_time'] = $dao->event_timings;
                $settings['event_event_type_filter'] = $dao->event_type_filters;
                $settings['time_format_24_hour'] = $dao->time_format_24_hour;
                $settings['week_begins_from_day'] = $dao->week_begins_from_day;
                $settings['recurring_event'] = $dao->recurring_event;
                $settings['enrollment_status'] = $dao->enrollment_status;
            }
            $eventTypes = array();
            $resources = array();
            if ($settings['calendar_type'] === 'Event') {
                $sql = "SELECT * FROM civicrm_event_calendar_event_type WHERE `event_calendar_id` = {$calendarId};";
                $dao = CRM_Core_DAO::executeQuery($sql);
                while ($dao->fetch()) {
                    $eventTypes[] = $dao->toArray();
                }
            } else {
                $sql = "SELECT * FROM civicrm_event_calendar_participant WHERE `event_calendar_id` = {$calendarId};";
                $dao = CRM_Core_DAO::executeQuery($sql);
                while ($dao->fetch()) {
                    $resources[] = $dao->toArray();
                }
            }
        } elseif ($calendarId == 0) {
            $settings['calendar_title'] = 'Event Calendar';
            $settings['event_is_public'] = 1;
            $settings['event_past'] = 1;
            $settings['enrollment_status'] = 1;
        }

        if (!empty($eventTypes)) {
            foreach ($eventTypes as $eventType) {
                $settings['event_types'][$eventType['event_type']] = $eventType['event_color'];
            }
        }
        if (!empty($resources)) {
            foreach ($resources as $resource) {
                $settings['resources'][$resource['contact_id']] = $resource['event_color'];
            }
        }

        return $settings;
    }

    /*
     * Return contrast color on the basis the hex color passed
     *
     * Referred from https://stackoverflow.com/questions/1331591
     */

    static function _getContrastTextColor($hexColor) {
        // hexColor RGB
        $R1 = hexdec(substr($hexColor, 1, 2));
        $G1 = hexdec(substr($hexColor, 3, 2));
        $B1 = hexdec(substr($hexColor, 5, 2));

        // Black RGB
        $blackColor = "#000000";
        $R2BlackColor = hexdec(substr($blackColor, 1, 2));
        $G2BlackColor = hexdec(substr($blackColor, 3, 2));
        $B2BlackColor = hexdec(substr($blackColor, 5, 2));

        // Calc contrast ratio
        $L1 = 0.2126 * pow($R1 / 255, 2.2) +
                0.7152 * pow($G1 / 255, 2.2) +
                0.0722 * pow($B1 / 255, 2.2);

        $L2 = 0.2126 * pow($R2BlackColor / 255, 2.2) +
                0.7152 * pow($G2BlackColor / 255, 2.2) +
                0.0722 * pow($B2BlackColor / 255, 2.2);

        $contrastRatio = 0;
        if ($L1 > $L2) {
            $contrastRatio = (int) (($L1 + 0.05) / ($L2 + 0.05));
        } else {
            $contrastRatio = (int) (($L2 + 0.05) / ($L1 + 0.05));
        }

        // If contrast is more than 5, return black color
        if ($contrastRatio > 5) {
            return '#000000';
        } else {
            // if not, return white color.
            return '#FFFFFF';
        }
    }

}
