<!-- BEGIN: MAIN -->
<!-- File: messenger.admin.tpl -->
<!-- Path: plugins/messenger/tpl/messenger.admin.tpl -->
<div class="container-fluid px-0">
	{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}

	<h2 class="h4 mb-3">{ADMIN_TITLE}</h2>

	<div class="row g-2 mb-3">
		<div class="col-6 col-md"><div class="border rounded p-2 text-center"><div class="fs-4">{STAT_CHATS}</div><div class="small text-muted">{PHP.L.msgr_admin_s_chats}</div></div></div>
		<div class="col-6 col-md"><div class="border rounded p-2 text-center"><div class="fs-4">{STAT_GROUPS}</div><div class="small text-muted">{PHP.L.msgr_admin_s_groups}</div></div></div>
		<div class="col-6 col-md"><div class="border rounded p-2 text-center"><div class="fs-4">{STAT_MSGS}</div><div class="small text-muted">{PHP.L.msgr_admin_s_msgs}</div></div></div>
		<div class="col-6 col-md"><div class="border rounded p-2 text-center"><div class="fs-4">{STAT_DAY}</div><div class="small text-muted">{PHP.L.msgr_admin_s_day}</div></div></div>
		<div class="col-6 col-md"><div class="border rounded p-2 text-center"><div class="fs-4">{STAT_INVITES}</div><div class="small text-muted">{PHP.L.msgr_admin_s_invites}</div></div></div>
	</div>

	<div class="d-flex flex-wrap gap-2 mb-3">
		<a class="btn btn-sm btn-outline-secondary" href="{OLD_90_URL}" onclick="return confirm('{PHP.L.msgr_admin_confirm}');">{PHP.L.msgr_admin_old_90}</a>
		<a class="btn btn-sm btn-outline-secondary" href="{OLD_365_URL}" onclick="return confirm('{PHP.L.msgr_admin_confirm}');">{PHP.L.msgr_admin_old_365}</a>
		<a class="btn btn-sm btn-outline-secondary" href="{EMPTY_URL}" onclick="return confirm('{PHP.L.msgr_admin_confirm}');">{PHP.L.msgr_admin_empty}</a>
		<a class="btn btn-sm btn-outline-secondary" href="{NOTIFS_URL}">{PHP.L.msgr_admin_notifs}</a>
	</div>

	<ul class="nav nav-pills mb-3">
		<li class="nav-item"><a class="nav-link {FILTER_ALL_CLASS}" href="{FILTER_ALL_URL}">{PHP.L.msgr_admin_f_all}</a></li>
		<li class="nav-item"><a class="nav-link {FILTER_DM_CLASS}" href="{FILTER_DM_URL}">{PHP.L.msgr_admin_t_dm}</a></li>
		<li class="nav-item"><a class="nav-link {FILTER_GROUP_CLASS}" href="{FILTER_GROUP_URL}">{PHP.L.msgr_admin_t_group}</a></li>
	</ul>

	<div class="table-responsive">
		<table class="table table-sm table-striped align-middle">
			<thead>
				<tr>
					<th>ID</th><th>{PHP.L.msgr_admin_col_type}</th><th>{PHP.L.msgr_admin_col_title}</th><th>{PHP.L.msgr_admin_col_owner}</th>
					<th>{PHP.L.msgr_admin_col_members}</th><th>{PHP.L.msgr_admin_col_msgs}</th><th>{PHP.L.msgr_admin_col_last}</th><th></th>
				</tr>
			</thead>
			<tbody>
				<!-- BEGIN: CHAT_ROW -->
				<tr>
					<td>{CHAT_ID}</td><td>{CHAT_TYPE}</td><td>{CHAT_TITLE}</td><td>{CHAT_OWNER}</td>
					<td>{CHAT_MEMBERS}</td><td>{CHAT_MSGS}</td><td>{CHAT_LAST}</td>
					<td class="text-end"><a class="btn btn-sm btn-outline-danger" href="{CHAT_DELETE_URL}" onclick="return confirm('{PHP.L.msgr_admin_delete_confirm}');">{PHP.L.msgr_admin_delete}</a></td>
				</tr>
				<!-- END: CHAT_ROW -->
				<!-- BEGIN: EMPTY -->
				<tr><td colspan="8" class="text-center text-muted">{PHP.L.msgr_admin_no_chats}</td></tr>
				<!-- END: EMPTY -->
			</tbody>
		</table>
	</div>
	<div class="d-flex justify-content-between"><span>{PAGENAV_PREV}</span><span>{PAGENAV_PAGES}</span><span>{PAGENAV_NEXT}</span></div>

	<h3 class="h5 mt-4">{PHP.L.msgr_admin_top}</h3>
	<table class="table table-sm w-auto">
		<tbody>
			<!-- BEGIN: TOP_ROW -->
			<tr><td><a href="{TOP_URL}">{TOP_NAME}</a></td><td class="text-end">{TOP_MSGS}</td></tr>
			<!-- END: TOP_ROW -->
		</tbody>
	</table>
</div>
<!-- END: MAIN -->
