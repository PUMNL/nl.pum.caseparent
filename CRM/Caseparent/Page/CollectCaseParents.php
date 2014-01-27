<?php

require_once 'CRM/Core/Page.php';

class CRM_Caseparent_Page_CollectCaseParents extends CRM_Core_Page {
  function run() {
    // Example: Set the page-title dynamically; alternatively, declare a static title in xml/Menu/*.xml
    CRM_Utils_System::setTitle(ts('CollectCaseParents'));

	// Example: Assign a variable for use in a template
	$this->assign('currentTime', date('Y-m-d H:i:s'));
	
	$caseTypeId = htmlspecialchars($_GET["casetypeid"]);
	$xml_pre = ''
	. '<enveloppe>'
	. '<caseparenttypeid>' . $caseTypeId . '</caseparenttypeid>'
	. '<parents>';
	
	$xml_main = '';
	
	$xml_post = ''
	. '</parents>'
	. '</enveloppe>';
	
	$qry = "
	"
	
//	$result = CRM_Core_DAO::executeQuery($qry);
//	while($result->fetch()) {
//		$xml_main .= '<campaign><id>' . $result->id . '</id><label>' . $result->label . '</label></campaign>';
//	}

	echo $xml_pre . $xml_main . $xml_post;
	exit();
	parent::run();
  }
}
