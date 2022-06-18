<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

class CRM_EventCalendar_Page_AJAX {

    public static function getEvents() {
        $events = [];

        $calendarId = isset($_GET['calendar_id']) ? $_GET['calendar_id'] : '';
        $settings = CRM_EventCalendar_Page_ShowEvents::_eventCalendar_getSettings($calendarId);

        $whereCondition = '';
        if (array_key_exists("event_types", $settings)) {
            $eventTypes = $settings['event_types'];
        }
        if (array_key_exists("resources", $settings)) {
            $resources = $settings['resources'];
        }

        if (!empty($resources)) {
            $contactList = implode(',', array_keys($resources));
            $whereCondition .= " AND p.contact_id in ({$contactList})";
        } else if (!empty($eventTypes)) {
            $eventTypesList = implode(',', array_keys($eventTypes));
            $whereCondition .= " AND e.event_type_id in ({$eventTypesList})";
        } else {
            $whereCondition .= ' AND e.event_type_id in (0)';
        }

        $whereCondition .= " AND e.start_date >= '{$_GET['start']}'";
        $whereCondition .= " AND e.start_date <= '{$_GET['end']}'";

        //Show/Hide Public Events
        if (!empty($settings['event_is_public'])) {
            $whereCondition .= " AND e.is_public = 1";
        }

        if (empty($resources)) {
            //Check recurringEvent is available or not.
            if (isset($settings['recurring_event']) && $settings['recurring_event'] == 1) {
                $query = "
                    SELECT r.`entity_id` id, `title` title, `start_date` start, `end_date` end ,`event_type_id` event_type
                    FROM `civicrm_event` e
                    LEFT JOIN civicrm_recurring_entity r ON r.entity_id = e.id
                    WHERE r.entity_table='civicrm_event'
                      AND e.is_active = 1
                      AND e.is_template = 0
                  ";
            } else {
                $query = "
                    SELECT e.`id`, e.`title`, e.`start_date` start, e.`end_date` end, e.`event_type_id` event_type
                    FROM `civicrm_event` e
                    WHERE e.is_active = 1
                      AND e.is_template = 0
                  ";
            }
        } else {
            $query = "
                    SELECT e.`id` id, e.`title`, e.`start_date` start, e.`end_date` end, p.`contact_id`, c.display_name
                    FROM `civicrm_event` e
                    LEFT JOIN `civicrm_participant` p ON p.event_id = e.id
                    LEFT JOIN `civicrm_contact`c on c.id=p.contact_id
                    WHERE e.is_active = 1
                      AND e.is_template = 0
                    ";
        }

        $query .= $whereCondition;
        
        $dao = CRM_Core_DAO::executeQuery($query);
        $eventCalendarParams = array('title' => 'title', 'start' => 'start', 'url' => 'url');

        if (!empty($settings['event_end_date'])) {
            $eventCalendarParams['end'] = 'end';
        }

        while ($dao->fetch()) {
            $eventData = array();
            $dao->url = html_entity_decode(CRM_Utils_System::url('civicrm/event/info', 'id=' . $dao->id ?: NULL));
            foreach ($eventCalendarParams as $k) {
                $eventData[$k] = $dao->$k;
            }
            if (!empty($resources)) {
                $eventData['backgroundColor'] = "#{$resources[$dao->contact_id]}";
                $eventData['textColor'] = CRM_EventCalendar_Page_ShowEvents::_getContrastTextColor($eventData['backgroundColor']);
                $eventData['title'] .= "\n" . $dao->display_name;
            } else if (!empty($eventTypes)) {
                $eventData['backgroundColor'] = "#{$eventTypes[$dao->event_type]}";
                $eventData['textColor'] = CRM_EventCalendar_Page_ShowEvents::_getContrastTextColor($eventData['backgroundColor']);
                $eventData['eventType'] = $civieventTypesList[$dao->event_type];
            } elseif ($calendarId == 0) {
                $eventData['backgroundColor'] = "";
                $eventData['textColor'] = CRM_EventCalendar_Page_ShowEvents::_getContrastTextColor($eventData['backgroundColor']);
                $eventData['eventType'] = $civieventTypesList[$dao->event_type];
            }

            $enrollment_status = civicrm_api3('Event', 'getsingle', [
                'return' => ['is_full'],
                'id' => $dao->id,
            ]);

            // Show/Hide enrollment status
            if (!empty($settings['enrollment_status'])) {
                if (!(isset($enrollment_status['is_error'])) && ( $enrollment_status['is_full'] == "1" )) {
                    $eventData['url'] = '';
                    $eventData['title'] .= ' FULL';
                }
            }
            $events[] = $eventData;
        }

        CRM_Utils_JSON::output($events);
    }

}
