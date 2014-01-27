{* === Why does this field occur twice? === *}

{if $form.case_parent_type}
	<table id="temp-table">
		<tr class="crm-case-opencase-form-block-case_parent_type_id">
			<td class="label">
				<label for="case_parent_type_id">Case Parent Type</label>
			</td>
			<td>
				{$form.case_parent_type.html}
			</td>
		</tr>
		<tr class="crm-case-opencase-form-block-case_parent_id">
			<td class="label">
				<label for="case_parent_id">Case Parent</label>
			</td>
			<td>
				{$form.case_parent.html}
			</td>
		</tr>
	</table>
	
{/if}


{literal}
<script type="text/javascript">
	cj("tr.crm-case-opencase-form-block-case_parent_type_id").insertAfter("tr.crm-case-opencase-form-block-case_type_id");
	cj("tr.crm-case-opencase-form-block-case_parent_id").insertAfter("tr.crm-case-opencase-form-block-case_parent_type_id");
	
	cj("#temp-table").remove();
	
	cj("tr.crm-case-opencase-form-block-case_parent_type_id ~ .crm-case-opencase-form-block-case_parent_type_id").remove();
	cj("tr.crm-case-opencase-form-block-case_parent_id ~ .crm-case-opencase-form-block-case_parent_id").remove();
</script>
{/literal}