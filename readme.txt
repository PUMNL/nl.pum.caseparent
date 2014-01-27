Plan: link cases to campaign(s)

#1: create table civicrm_case_parent
    -> using tables created in nl.pum.campaignparent

#6: modification of form Case Type
	when accessed via Admin > System settings > Option Groups > Case types:
		Conditional form modification: READY
		Save: READY
		Load existing value(s): READY
	when accessed via Admin > Civi Case > Case Types
		Conditional form modification: READY
		Save: READY
		Load existing value(s): READY

	known limitation (both ways):
	Can't choose parents on newly created case types (no case_type_id available)

#8: add start/end dates to (certain) Cases
    OPEN
