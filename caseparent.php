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

function _get_qry_campaign_types($id) {
	if ($id) {
		return "
		SELECT		r.*,
					(p.case_type_id=" . $id . ") AS `is_selected`
		FROM 		(
						SELECT	v.id AS `campaign_type_id`,
								v.label AS `campaign_type_label`
						FROM	civicrm_option_value AS v,
								civicrm_option_group AS g
						WHERE	v.option_group_id = g.id
						AND		g.name = 'campaign_type'
					)	AS r
		LEFT JOIN	civicrm_case_type_parent as p
		ON			r.campaign_type_id=p.parent_campaign_type_id
		ORDER BY	r.campaign_type_label
		";
	} else {
		return "
		SELECT	v.id AS `campaign_type_id`,
				v.label AS `campaign_type_label`,
				0 AS `is_selected`
		FROM	civicrm_option_value AS v,
				civicrm_option_group AS g
		WHERE	v.option_group_id = g.id
		AND		g.name = 'campaign_type'
		";
	}
}

/**
 * handler function for hook_civicrm_buildForm, when handling following $formNames:
 * - CRM_Admin_Form_OptionValue
 * - CRM_Admin_Form_Options
 */
function _handler_buildForm_optionValue_caseType(&$form) {
	if (array_key_exists('id', $form->_defaultValues)) {
		$id = $form->_defaultValues['id'];
	} else {
		$id = NULL;
	}
	$qry = _get_qry_campaign_types($id);
	$result = CRM_Core_DAO::executeQuery($qry);
	while($result->fetch()) {
		$elm = $form->add('checkbox', 'case_type_parent_' . $result->campaign_type_id, ts('Parent'));
		$elm->_attributes['value'] = $result->campaign_type_id; // I do't want '1': need the recordno.
		if ($result->is_selected==1) {
			$elm->_attributes['checked'] = 'checked';
		}
		$elm->_text = $result->campaign_type_label; // this is what happened when I added a custom checkbox field to a contact
	}
	//dpm($form);
}

/**
 * handler function for hook_civicrm_buildForm, when handling following $formNames:
 * - CRM_Case_Form_Case
 * - CRM_Case_Form_Activity
 */
function _handler_buildForm_optionValue_case(&$form) {
	// load additional javascript for AJAX functionalities
	CRM_Core_Resources::singleton()->addScriptFile('nl.pum.caseparent', 'CaseParent.js');
	// add field for case parent type (=campaign type) selection; default = text '', value ''
	$elm = $form->add('select', 'case_parent_type');
	$elm->_options = array(
		array(
			'text' => ts('-select-'),
			'attr' => array (
				'value' => ''
			)
		)
	);
	// add field for case parent (=campaign) selection; default = text '', value ''
	$elm = $form->add('select', 'case_parent');
	$elm->_options = array(
		array(
			'text' => ts('-select-'),
			'attr' => array (
				'value' => ''
			)
		)
	);
	//dpm($form);
}

/**
 * handler function for hook_civicrm_postProcess, when handling following $formNames:
 * - CRM_Admin_Form_OptionValue
 * - CRM_Admin_Form_Options
 */
function _handler_postProcess_optionValue(&$form) {
	// retrieve case type id
	if (array_key_exists('id', $form->_defaultValues)) {
		$case_type_id = $form->_defaultValues['id'];
	} else {
		$case_type_id = NULL;
	}
	if ($case_type_id) {
		// remove existing entries for specified case type
		$sql = 'DELETE FROM `civicrm_case_type_parent` WHERE `case_type_id`=' . $case_type_id;
		try {
			$result = CRM_Core_DAO::executeQuery($sql);
			//dpm($sql);
		} catch (Exception $e) {
		}
		// store selected parent ids
		foreach($form->_submitValues as $key=>$value) {
			if (strpos($key, 'case_type_parent')===0) {
				$sql = 'INSERT INTO `civicrm_case_type_parent` (`case_type_id`, `parent_campaign_type_id`) VALUES (' . $case_type_id . ', ' . $value . ')';
//				echo $sql . '<br>';
				try {
					$result = CRM_Core_DAO::executeQuery($sql);
					//dpm($sql);
				} catch(Exception $e) {
				}
//			} else {
//				echo 'skip: ' . $key . '<br>';
			}
		}
		//dpm($form->_submitValues);
	}
}

/**
 * Implementation of hook_civicrm_buildForm
 *
 * Add additional set of checkboxes to register parent campaign(type)s on case(type)s
 */
function caseparent_civicrm_buildForm($formName, &$form) {
	//dpm($form);
	switch($formName) {
	case 'CRM_Admin_Form_OptionValue':
		// when accessed via Admin > System settings > Option Groups > Case types
		_handler_buildForm_optionValue_caseType($form);
		break;
	case 'CRM_Admin_Form_Options':
		// when accessed via Admin > Civi Case > Case Types
		_handler_buildForm_optionValue_caseType($form);
		break;
	case 'CRM_Case_Form_Case':
		// new case
		_handler_buildForm_optionValue_case($form);
		break;
	case 'CRM_Case_Form_Activity':
		// editing an existing case
		_handler_buildForm_optionValue_case($form);
		break;
	default:
		// no modifications
		//dpm($formName);
		break;
	}
	//dpm($form);
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
	switch($formName) {
	case 'CRM_Admin_Form_OptionValue':
		// when accessed via Admin > System settings > Option Groups > Case types
		_handler_postProcess_optionValue($form);
		break;
	case 'CRM_Admin_Form_Options':
		// when accessed via Admin > Civi Case > Case Types
		_handler_postProcess_optionValue($form);
		break;
	case 'CRM_Case_Form_Case':
		// new case
		// store selected parent_type_id and/or selected parent_id in cache
		dpm($form);
		$ip = ip_address();
		$caseTypeParentId = $form->_submitValues['case_parent_type'];
		dpm($caseTypeParentId);
		cache_set('caseparent_' . $ip . '_casetypeparent', $caseTypeParentId, 'cache', strtotime('+2 minutes'));
		break;
	default:
		// no action
	}
}

/**
 * Implementation of hook_civicrm_caseSummary
 *
 */
function caseparent_civicrm_caseSummary($caseID) {
	$ip = ip_address();
	$caseParentTypeId = cache_get('caseparent_' . $ip . '_casetypeparent');
	dpm($caseParentTypeId);
	//also obtain the actual caseParentId (=CampaignId) + start/end date and insert into civicrm_case_parent
}