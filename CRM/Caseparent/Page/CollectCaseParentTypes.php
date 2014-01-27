<?php

require_once 'CRM/Core/Page.php';

class CRM_Caseparent_Page_CollectCaseParentTypes extends CRM_Core_Page {
  function run() {
    // Example: Set the page-title dynamically; alternatively, declare a static title in xml/Menu/*.xml
    CRM_Utils_System::setTitle(ts('CollectCaseParentTypes'));

    // Example: Assign a variable for use in a template
    $this->assign('currentTime', date('Y-m-d H:i:s'));
	
	$caseTypeId = htmlspecialchars($_GET["casetypeid"]);
	$xml_pre = ''
	. '<enveloppe>'
	. '<casetypeid>' . $caseTypeId . '</casetypeid>'
	. '<parents>';
	
	$xml_main = '';
	
	$xml_post = ''
	. '</parents>'
	. '</enveloppe>';
	
	$qry = "
	select	ogv_camp.id,
			ogv_camp.label,
			ogv_camp.name,
			ogv_camp.value
	from	civicrm_option_value ogv_camp,
			civicrm_option_group ogp_camp
	where	ogv_camp.option_group_id = ogp_camp.id
	and		ogp_camp.name='campaign_type'
	and		ogv_camp.id in (
				select	ctp.parent_campaign_type_id
				from	civicrm_case_type_parent ctp
				where	ctp.case_type_id in (
							select	distinct ogv_case.id
							from	civicrm_option_value as ogv_case,
									civicrm_option_group as ogp_case
							where	ogv_case.value = " . $caseTypeId . "
							and		ogv_case.option_group_id = ogp_case.id
							and		ogp_case.name='case_type'
				)
	)
	";
	
	$result = CRM_Core_DAO::executeQuery($qry);
	while($result->fetch()) {
		$xml_main .= '<campaigntype><id>' . $result->id . '</id><label>' . $result->label . '</label></campaigntype>';
	}
	
	echo $xml_pre . $xml_main . $xml_post;
	exit();
    parent::run();
  }
}
