cj(function($) {

	function noCacheParameter() {
		return('&nocache=' +(new Date()).getTime() + Math.round(Math.random() * 10000));
	}
	
	//CRM.alert(ts('caseparent.js loaded!'));
	
	// Update options for case parent types when case type is changed
	function postChange_caseTypeId(srcElm) {
		requestId = srcElm.value;
		//CRM.alert('id=' + requestId);
		requestUrl = '/civicrm/collectCaseParentTypes?&casetypeid=' + requestId; // + noCacheParameter();
		//CRM.alert(requestUrl);
		cj.ajax({
			url: requestUrl,
			beforeSend: function(data) {
				},
			success: function(data) {
				//CRM.alert('Received:\n' + data);
				cj("#case_parent_type > option:first ~ option").remove(); // remove all options but the first
				tgtElm = cj("#case_parent_type");
				xmlDoc = cj.parseXML(data);
				xml = cj(xmlDoc);
				cj(xml).find('campaigntype').each(function() {
					//CRM.alert($(this).find("id").text() + ' = ' + $(this).find("label").text());
					tgtElm.append($("<option></option>").attr("value", cj(this).find("id").text()).text(cj(this).find("label").text()));
				});
			}
		});
		// update case parent field as well
		depElm = cj("tr.crm-case-opencase-form-block-case_parent_id");
		postChange_caseParentType(depElm)
	}
	
	// Update options for case parents when case parent type is changed
	function postChange_caseParentType(srcElm) {
		requestId = srcElm.value;
		//CRM.alert('id=' + requestId);
		requestUrl = '/civicrm/collectCaseParents?&parenttypeid=' + requestId; // + noCacheParameter();
		CRM.alert(requestUrl);
		//.....
	}
	
	cj(document).on('change', '#case_type_id', function() {
		postChange_caseTypeId(this);
	});
	
	cj(document).on('change', '#case_parent_type', function() {
		postChange_caseParentType(this);
	});
	
	// TO DO:
	// - the default set op options when opened
	// - store selection
	// - the default selection when reopened
	
});
