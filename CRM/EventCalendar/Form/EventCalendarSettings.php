<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_EventCalendar_Form_EventCalendarSettings extends CRM_Core_Form {

    private $_submittedValues = array();
    private $_settings = array();
    private $_calendar_type = '';

    public function buildQuickForm() {
        $this->controller->_destination = CRM_Utils_System::url('civicrm/admin/event-calendar', 'reset=1');
        $this->action = '';
        if (isset($_GET['action'])) {
            $this->action = $_GET['action'];
        }
        $this->calendar_id = $_GET['id'] ?? '';
        if (empty($this->calendar_id)) {
            $this->calendar_id = $_POST['calendar_id'] ?? '';
        }
        if (isset($_GET['resource'])) {
            $this->_calendar_type = $_GET['resource'];
        } else if (isset($this->calendar_id) && $this->calendar_id != '') {
            $this->_calendar_type = CRM_Core_DAO::singleValueQuery("SELECT `calendar_type` FROM `civicrm_event_calendar`
                                                                    WHERE `id` = {$this->calendar_id};");
        } else {
            $this->_calendar_type = $_POST['calendar_type'] ?? 'Event';
        }

        if ($this->action == 'delete') {
            $descriptions['delete_warning'] = ts('Are you sure you want to delete this calendar?');
            $this->add('hidden', 'action', $this->action);
            $this->add('hidden', 'calendar_id', $this->calendar_id);
            $this->assign('descriptions', $descriptions);
        } else {
            CRM_Core_Resources::singleton()->addScriptFile('dk.kas.eventcalendar', 'js/jscolor.js');
            CRM_Core_Resources::singleton()->addScriptFile('d.kas.eventcalendar', 'js/eventcalendar.js');

            $settings = $this->getFormSettings();
            $descriptions = array();

            $this->add('hidden', 'action', $this->action);
            $this->add('hidden', 'calendar_id', $this->calendar_id);
            $this->add('hidden', 'calendar_type', $this->_calendar_type);
            $this->add('text', 'calendar_title', ts('Calendar Title'));
            $descriptions['calendar_title'] = ts('Event calendar title.');
            $this->add('advcheckbox', 'show_past_events', ts('Show Past Events?'));
            $descriptions['show_past_events'] = ts('Show past events as well as current/future.');
            $this->add('advcheckbox', 'show_end_date', ts('Show End Date?'));
            $descriptions['show_end_date'] = ts('Show the event with start and end dates on the calendar.');
            $this->add('advcheckbox', 'show_public_events', ts('Show Public Events?'));
            $descriptions['show_public_events'] = ts('Show only public events, or all events.');
            $this->add('advcheckbox', 'events_by_month', ts('Show Events by Month?'));
            $descriptions['events_by_month'] = ts('Show the month parameter on calendar.');
            $this->add('advcheckbox', 'event_timings', ts('Show Event Times?'));
            $descriptions['event_timings'] = ts('Show the event timings on calendar.');
            $this->add('text', 'events_from_month', ts('Events from Month'));
            $descriptions['events_from_month'] = ts('Show events from how many months from current month.');
            $this->add('advcheckbox', 'event_type_filters', ts('Filter Event Types?'));
            $descriptions['event_type_filters'] = ts('Show event types filter on calendar.');
            $this->add('advcheckbox', 'week_begins_from_day', ts('Week begins on'));
            $descriptions['week_begins_from_day'] = ts('Use weekBegin settings from CiviCRM. You can override settings at Administer > Localization > Date Formats.');
            $this->add('advcheckbox', 'time_format_24_hour', ts('24 hour time format'));
            $descriptions['time_format_24_hour'] = ts('Use 24 hour time format - default is AM/PM format.');
            $this->add('advcheckbox', 'recurring_event', ts('Show recurring events'));
            $descriptions['recurring_event'] = ts('Show only recurring events.');
            $this->add('advcheckbox', 'enrollment_status', ts('Show enrollment status'));
            $descriptions['enrollment_status'] = ts('Show enrollment status on calendar event.');

            $eventTemplates = $this->getEventTemplates();

            $this->add('select', 'event_templates', ts("Select Event template(s)"), $eventTemplates,
                    FALSE, ['class' => 'crm-select2', 'multiple' => TRUE,
                'placeholder' => ts('- select template(s) -')]);

            if ($this->_calendar_type === 'Event') {
                $eventTypes = CRM_Event_PseudoConstant::eventType();
                foreach ($eventTypes as $id => $type) {
                    $this->addElement('checkbox', "eventtype_{$id}", $type, NULL,
                            array('onclick' => "showhidecolorbox('{$id}')", 'id' => "event_{$id}"));
                    $this->addElement('text', "eventcolor_{$id}", "Color",
                            array(
                                'onchange' => "updatecolor('eventcolor_{$id}', this.value);",
                                'class' => 'color',
                                'id' => "eventcolorid_{$id}",
                    ));
                }

                $this->assign('eventTypes', $eventTypes);
                $this->assign('descriptions', $descriptions);
            } else {
                $query = "SELECT `id`, `display_name`  FROM `civicrm_contact` 
                            WHERE `contact_sub_type` LIKE '%" . $this->_calendar_type . "%' 
                            ORDER BY `display_name`  ASC;";
                $dao = CRM_Core_DAO::executeQuery($query);
                $resource_list = [];
                while ($dao->fetch()) {
                    $id = $dao->id;
                    $type = $dao->display_name;
                    $this->addElement('checkbox', "resourceid_{$id}", $type, NULL,
                            array('onclick' => "showhidecolorbox('{$id}')", 'id' => "event_{$id}"));
                    $this->addElement('text', "eventcolor_{$id}", "Color",
                            array(
                                'onchange' => "updatecolor('eventcolor_{$id}', this.value);",
                                'class' => 'color',
                                'id' => "eventcolorid_{$id}",
                    ));
                }
            }
        }

        $this->addButtons(array(
            array(
                'type' => 'submit',
                'name' => ts('Submit'),
                'isDefault' => TRUE,
            ),
        ));
        // export form elements
        $this->assign('elementNames', $this->getRenderableElementNames());
        parent::buildQuickForm();
    }

    public function postProcess() {
        $submitted = $this->exportValues();
        $templates = implode(',', $submitted['event_templates']);
        $this->_calendar_type = $submitted['calendar_type'];
        foreach ($submitted as $key => $value) {
            if (!$value && $key != 'calendar_title' && $key != 'calendar_type') {
                $submitted[$key] = 0;
            }
        }

        if ($submitted['action'] == 'add') {
            $sql = "INSERT INTO civicrm_event_calendar
                (calendar_title, calendar_type, show_past_events, 
                show_end_date, show_public_events, 
                events_by_month, event_timings, events_from_month, 
                event_type_filters, week_begins_from_day, 
                time_format_24_hour, recurring_event, 
                enrollment_status, event_templates)
            VALUES
                ('{$submitted['calendar_title']}', '{$submitted['calendar_type']}', 
                {$submitted['show_past_events']}, {$submitted['show_end_date']}, 
                {$submitted['show_public_events']}, {$submitted['events_by_month']},
                {$submitted['event_timings']}, {$submitted['events_from_month']}, 
                {$submitted['event_type_filters']}, {$submitted['week_begins_from_day']}, 
                {$submitted['time_format_24_hour']}, {$submitted['recurring_event']}, 
                {$submitted['enrollment_status']}, 
                '{$templates}');";
            $dao = CRM_Core_DAO::executeQuery($sql);
            $cfId = CRM_Core_DAO::singleValueQuery('SELECT LAST_INSERT_ID()');
            foreach ($submitted as $key => $value) {
                if ("eventtype" == substr($key, 0, 9)) {
                    if ($value == 1) {
                        $id = explode("_", $key)[1];
                        $sql = "INSERT INTO civicrm_event_calendar_event_type(event_calendar_id, event_type, event_color)
                                VALUES ({$cfId}, {$id}, '{$submitted['eventcolor_' . $id]}');";
                        $dao = CRM_Core_DAO::executeQuery($sql);
                    }
                } else if ("resourceid" == substr($key, 0, 10)) {
                    $id = explode("_", $key)[1];
                    $sql = "INSERT INTO civicrm_event_calendar_participant(event_calendar_id, contact_id, event_color)
                                VALUES ({$cfId}, {$id}, '{$submitted['eventcolor_' . $id]}');";
                    $dao = CRM_Core_DAO::executeQuery($sql);
                }
            }
        }

        if ($submitted['action'] == 'update') {
            $sql = "UPDATE civicrm_event_calendar
       SET calendar_title = '{$submitted['calendar_title']}', 
            show_past_events = {$submitted['show_past_events']}, 
            show_end_date = {$submitted['show_end_date']}, 
            show_public_events = {$submitted['show_public_events']}, 
            events_by_month = {$submitted['events_by_month']},
            event_timings = {$submitted['event_timings']}, 
            events_from_month = {$submitted['events_from_month']},
            event_type_filters = {$submitted['event_type_filters']}, 
            week_begins_from_day = {$submitted['week_begins_from_day']}, 
            time_format_24_hour = {$submitted['time_format_24_hour']}, 
            recurring_event = {$submitted['recurring_event']},  
            enrollment_status = {$submitted['enrollment_status']},
            event_templates = '{$templates}'
       WHERE `id` = {$submitted['calendar_id']};";
            $dao = CRM_Core_DAO::executeQuery($sql);
            //delete current event type records to update with new ones
            $sql = "DELETE FROM civicrm_event_calendar_event_type WHERE `event_calendar_id` = {$submitted['calendar_id']};";
            $dao = CRM_Core_DAO::executeQuery($sql);
            $sql = "DELETE FROM civicrm_event_calendar_participant WHERE `event_calendar_id` = {$submitted['calendar_id']};";
            $dao = CRM_Core_DAO::executeQuery($sql);
            //insert new event type records
            foreach ($submitted as $key => $value) {
                if ("eventtype" == substr($key, 0, 9)) {
                    if ($value == 1) {
                        $id = explode("_", $key)[1];
                        $sql = "INSERT INTO civicrm_event_calendar_event_type(event_calendar_id, event_type, event_color)
                                VALUES ({$submitted['calendar_id']}, {$id}, '{$submitted['eventcolor_' . $id]}');";
                        $dao = CRM_Core_DAO::executeQuery($sql);
                    }
                } else if ("resourceid" == substr($key, 0, 10)) {
                    $id = explode("_", $key)[1];
                    $sql = "INSERT INTO civicrm_event_calendar_participant(event_calendar_id, contact_id, event_color)
                                VALUES ({$submitted['calendar_id']}, {$id}, '{$submitted['eventcolor_' . $id]}');";
                    $dao = CRM_Core_DAO::executeQuery($sql);
                }
            }
        }

        if ($submitted['action'] == 'delete') {
            $sql = "DELETE FROM civicrm_event_calendar WHERE `id` = {$submitted['calendar_id']};";
            $dao = CRM_Core_DAO::executeQuery($sql);
        }

        CRM_Core_Session::setStatus(ts('The Calendar has been saved.'), ts('Saved'), 'success');
        parent::postProcess();
    }

    /**
     * Get the fields/elements defined in this form.
     *
     * @return array (string)
     */
    public function getRenderableElementNames() {
        // The _elements list includes some items which should not be
        // auto-rendered in the loop -- such as "qfKey" and "buttons". These
        // items don't have labels. We'll identify renderable by filtering on
        // the 'label'.
        $elementNames = array();
        foreach ($this->_elements as $element) {
            $label = $element->getLabel();
            if (!empty($label)) {
                $elementNames[] = $element->getName();
            }
        }
        return $elementNames;
    }

    /**
     * Get the settings we are going to allow to be set on this form.
     *
     * @return array
     */
    public function getFormSettings() {
        if (empty($this->_settings)) {
            $sql = "SELECT * FROM civicrm_event_calendar;";
            $dao = CRM_Core_DAO::executeQuery($sql);
            while ($dao->fetch()) {
                $settings[] = $dao->toArray();
            }
            return isset($settings);
        }
    }

    /**
     * Set defaults for form.
     *
     * @see CRM_Core_Form::setDefaultValues()
     */
    public function setDefaultValues() {
        if ($this->calendar_id && ($this->action != 'delete')) {
            $existing = array();
            $sql = "SELECT * FROM civicrm_event_calendar WHERE id = {$this->calendar_id};";
            $dao = CRM_Core_DAO::executeQuery($sql);
            while ($dao->fetch()) {
                $existing[] = $dao->toArray();
            }
            $defaults = array();
            foreach ($existing as $name => $value) {
                $defaults[$name] = $value;
            }
            if ($defaults['calendar_type'] === 'Event') {
                $sql = "SELECT * FROM civicrm_event_calendar_event_type WHERE event_calendar_id = {$this->calendar_id};";
                $dao = CRM_Core_DAO::executeQuery($sql);
                $existing = array();
                while ($dao->fetch()) {
                    $existing[] = $dao->toArray();
                }
                foreach ($existing as $name => $value) {
                    $defaults[0]['eventtype_' . $value['event_type']] = 1;
                    $defaults[0]['eventcolor_' . $value['event_type']] = $value['event_color'];
                }
            } else {
                $sql = "SELECT * FROM civicrm_event_calendar_participant WHERE event_calendar_id = {$this->calendar_id};";
                $dao = CRM_Core_DAO::executeQuery($sql);
                $existing = array();
                while ($dao->fetch()) {
                    $existing[] = $dao->toArray();
                }
                foreach ($existing as $name => $value) {
                    $defaults[0]['resourceid_' . $value['contact_id']] = 1;
                    $defaults[0]['eventcolor_' . $value['contact_id']] = $value['event_color'];
                }
            }
            return $defaults[0];
        }
    }

    private function getEventTemplates() {
        $eventTemplates = [];
        $sql = "SELECT `id`, `template_title` 
            FROM `civicrm_event` 
            WHERE `is_template` 
            ORDER BY `template_title` ASC;";
        $dao = CRM_Core_DAO::executeQuery($sql);
        while ($dao->fetch()) {
            $eventTemplates[$dao->id] = $dao->template_title;
        }
        return $eventTemplates;
    }

}
