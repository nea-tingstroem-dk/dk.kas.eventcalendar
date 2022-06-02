<?php

use CRM_EventCalendar_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/quickform/
 */
class CRM_EventCalendar_Form_CreateResourceEvent extends CRM_Core_Form {
    private $_calendar_id = 0;
    private $_start_time; 
    
    public function buildQuickForm() {
        $this->_calendar_id = $_GET['calendar_id'] ?? 0;
        $this->_start_time = strtotime($_GET['date'])?? time();
        $this->assign('start_time', date('Y-m-d H:i:s', $this->_start_time));
        $this->add('hidden', 'calendar_id', $this->_calendar_id);
        
        $resources = $this->getResources($this->_calendar_id);
        $resource_options = [];
        foreach ($resources as $id => $res) {
            $resource_options[$id] = $res['name'];
        }
        $this->assign('resources', json_encode($resources));
        
        $this->add('select', 'resource', ts("Select Resource(s)"), $resource_options, 
                FALSE, ['class' => 'crm-select2', 'multiple' => TRUE, 'placeholder' => ts('- select resource(s) -')]);



        $this->add('datepicker', 'event_start_date', ts('Start Date'), 
                [
                    'minDate' => format_date($this->_start_time, 'y-m-d H:i'),
                    'maxDate' => format_date($this->_start_time + 60*60*24, 'y-m-d H:i'),
                ],
                TRUE, ['time' => TRUE]);
        $this->add('datepicker', 'event_end_date', ts('End Date'), 
                [
                    'minDate' => format_date($this->_start_time, 'y-m-d H:i'),
                    'maxDate' => format_date($this->_start_time + 60*60*48, 'y-m-d H:i'),
                ],
                TRUE, ['time' => TRUE]);
   // add form elements
        $this->addButtons(array(
            array(
                'type' => 'submit',
                'name' => E::ts('Submit'),
                'isDefault' => TRUE,
            ),
        ));

        // export form elements
        $this->assign('elementNames', $this->getRenderableElementNames());
        parent::buildQuickForm();
    }

    public function postProcess() {
        $values = $this->exportValues();
/*        CRM_Core_Session::setStatus(E::ts('You picked color "%1"', array(
                    1 => $options[$values['favorite_color']],
        )));*/
        parent::postProcess();
    }

    public function getResources() {
        $options = [];
        $start_time = date(DATE_ATOM, $this->_start_time);
        $now = date(DATE_ATOM, time());
        $query = "SELECT p.id calendar_id, p.`contact_id`,c.display_name name
                FROM `civicrm_event_calendar_participant` p
                LEFT JOIN `civicrm_contact` c on c.id=p.`contact_id`
                WHERE `event_calendar_id` = {$this->_calendar_id};";
        $dao = CRM_Core_DAO::executeQuery($query);
        while ($dao->fetch()) {
            $sql = "SELECT e.start_date FROM `civicrm_event` e
                    LEFT JOIN `civicrm_participant` p ON p.event_id = e.id
                    WHERE p.contact_id = {$dao->contact_id}
                    AND e.`start_date` > '{$start_time}'
                    AND e.`start_date` > '{$now}'
                    ORDER BY e.`start_date` ASC
                    LIMIT 1;";
            $max_time = CRM_CORE_DAO::singleValueQuery($sql);
            $sql = "SELECT e.end_date FROM `civicrm_event` e
                    LEFT JOIN `civicrm_participant` p ON p.event_id = e.id
                    WHERE p.contact_id = {$dao->contact_id}
                    AND e.`end_date` < '{$max_time}'
                    AND e.`end_date` > '{$now}'
                    ORDER BY e.`end_date` DESC
                    LIMIT 1;";
            $min_time = CRM_CORE_DAO::singleValueQuery($sql) ?? date('Y-m-d H:i', time());
            $resource = [
                'name' => $dao->name,
                'min_start' => $min_time,
                'max_end' => $max_time,
            ];
            $options[$dao->contact_id] = $resource;
        }
        return $options;
    }

    /**
     * Get the fields/elements defined in this form.
     *
     * @return array (string)
     */
    public function getRenderableElementNames() {
        // The _elements list includes some items which should not be
        // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
        // items don't have labels.  We'll identify renderable by filtering on
        // the 'label'.
        $elementNames = array();
        foreach ($this->_elements as $element) {
            /** @var HTML_QuickForm_Element $element */
            $label = $element->getLabel();
            if (!empty($label)) {
                $elementNames[] = $element->getName();
            }
        }
        return $elementNames;
    }

//    public function setDefaultValues() {
//        $defaults = [
//            'event_start_date' => format_date($this->_start_time, 'Y-m-d H:i'),
//        ];
//        return $defaults;
//    }

}