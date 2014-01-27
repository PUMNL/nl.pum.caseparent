{crmAPI var='required_case_type_id' entity='OptionGroup' action='getvalue' sequential=1 return='id'              name='case_type'             } {* id of option group called case_type *}
{crmAPI var='current_case_type_id'  entity='OptionValue' action='getvalue' sequential=1 return='option_group_id' id=$id                       } {* id of opened option value *}

{if $required_case_type_id==$current_case_type_id}
	<table id="temp-table">
		<tr class="crm-admin-optionvalue-form-block-parent">
			<td class="label"><label for="parent">Parent</label></td>
				<td>
					{foreach from=$form item=elm}
						{if $elm|@count gt 1}
							{if $elm.name|truncate:16:"":true eq "case_type_parent"}
								{$elm.html}<br>
							{/if}
						{/if}
					{/foreach}
				</td>
			</tr>
	</table>
	
{/if}


{literal}
<script type="text/javascript">
	cj("tr.crm-admin-optionvalue-form-block-parent").insertAfter("tr.crm-admin-options-form-block-is_default");
	cj("#temp-table").remove();
</script>
{/literal}
