{crmAPI var='required_case_type_id' entity='OptionGroup' action='getvalue' sequential=1 return='id'              name='case_type'             } {* id of option group called case_type *}
{crmAPI var='current_case_type_id'  entity='OptionValue' action='getvalue' sequential=1 return='option_group_id' id=$id                       } {* id of opened option value *}
{crmAPI var='campaign_id'           entity='OptionGroup' action='getvalue' sequential=1 return='id'              name='campaign_type'         } {* id of option group called campaign_type *}
{crmAPI var='campaigns'             entity='OptionValue' action='get'      sequential=1 return='name'            option_group_id=$campaign_id } {* all option values of type campaign *}

{if $required_case_type_id==$current_case_type_id}
{*
	results for debugging:<br>
	required case type id: {$required_case_type_id}<br>
	current case type id:  {$current_case_type_id}<br>
	campaign type id:      {$campaign_id}<br>
*}
	
	<table id="temp-table">
	<tr class="crm-admin-optionvalue-form-block-parent">
	<td class="label"><label for="parent">Parent</label></td>
	<td>
	{foreach from=$campaigns.values item=elm}
		<input type="checkbox" class="form-checkbox" name="case_parent" value="{$elm.id}" />&nbsp;{$elm.name}<br />
	{/foreach}
	</td>
	</tr>
	</table>
{/if}

{literal}
<script type="text/javascript">
	cj("tr.crm-admin-optionvalue-form-block-parent").insertAfter("tr.crm-admin-optionvalue-form-block-grouping");
	cj("#temp-table").remove();
</script>
{/literal}