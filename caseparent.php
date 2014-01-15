<?php

require_once 'caseparent.civix.php';

/**
 * Implementation of hook_civicrm_config
 */
function caseparent_civicrm_config(&$config) {
  _caseparent_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 */
function caseparent_civicrm_xmlMenu(&$files) {
  _caseparent_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 */
function caseparent_civicrm_install() {
  return _caseparent_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 */
function caseparent_civicrm_uninstall() {
  return _caseparent_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 */
function caseparent_civicrm_enable() {
  return _caseparent_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 */
function caseparent_civicrm_disable() {
  return _caseparent_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 */
function caseparent_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _caseparent_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 */
function caseparent_civicrm_managed(&$entities) {
  return _caseparent_civix_civicrm_managed($entities);
}

//==========================================================================================

function _get_qry_campaign_types() {
	return "SELECT	v.id, v.label FROM civicrm_option_value AS v, civicrm_option_group AS g WHERE v.option_group_id = g.id AND g.name='campaign_type'";
}

/**
 * Implementation of hook_civicrm_buildForm
 *
 * Add additional set of checkboxes to register parent campaign(type)s on case(type)s
 */
function caseparent_civicrm_buildForm($formName, &$form) {
	//dpm($formName); // DEBUG
	switch($formName) {
	case 'CRM_Admin_Form_OptionValue':
		// when accessed via Admin > System settings > Option Groups > Case types
		$qry = _get_qry_campaign_types();
		$result = CRM_Core_DAO::executeQuery($qry);
		while($result->fetch()) {
			$elm = $form->add('checkbox', 'case_type_parent', ts('Parent'));
			$elm->_attributes['value'] = $result->id; // I do't want '1': need the recordno.
			$elm->_text = $result->label; // this is what happened when I added a custom checkbox field to a contact
		}
		break;
	case 'CRM_Admin_Form_Options':
		// no action - when accessed via Admin > Civi Case > Case Types
		break;
	default:
		// no action
	}
	//dpm($form); // DEBUG
}


/**
 * Implementation of hook_civicrm_postProcess
 *
 * Store additional module controlled (html) fields:
 *    field case_type_parent on case_type in table civicrm_case_type_parent
 *    field case_parent on case in table civicrm_case_parent
 */
function caseparent_civicrm_postProcess( $formName, &$form ) {
	// http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postProcess
	exit(); //------------------------
	 dpm($form);
	switch($formName) {
	case 'CRM_Admin_Form_OptionValue':
		// when accessed via Admin > System settings > Option Groups > Case types
		// retrieve case type id
		$case_type_id = $form->_defaultValues['id'];
		// remove existing entries for specified case type
		$sql = 'DELETE FROM `civicrm_case_type_parent` WHERE `case_type_id`=' . $case_type_id;
		try {
			$result = CRM_Core_DAO::executeQuery($sql);
			//dpm($sql);
		} catch (Exception $e) {
		}
		// store selected parent ids
		if ($form->_submitValues['case_parent']) {
			foreach($form->_submitValues['case_parent'] as $key=>$value) {
				$sql = 'INSERT INTO `civicrm_case_type_parent` (`case_type_id`, `campaign_type_id`, `start_date`, `end_date`, `is_active`) VALUES (' . $case_type_id . ', ' . $value . ', NULL, NULL, \'1\')';
				try {
					$result = CRM_Core_DAO::executeQuery($sql);
					//dpm($sql);
				} catch(Exception $e) {
				}
			}
		}
		dpm($form->_submitValues);
		break;
	case 'CRM_Admin_Form_Options':
		// no action - when accessed via Admin > Civi Case > Case Types
		break;
	default:
		// no action
	}
}