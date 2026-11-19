<!-- BEGIN: MAIN -->
<!-- File: messenger.app.tpl -->
<!-- Path: plugins/messenger/tpl/messenger.app.tpl -->
{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}
<!-- IF {MSGR_LOGGED} -->
<div class="msgr-app" id="msgr-app" data-msgr-app="1">
	<div class="msgr-loading">{PHP.L.msgr_loading}</div>
	<noscript>{PHP.L.msgr_need_js}</noscript>
</div>
<!-- ELSE -->
<div class="alert alert-warning d-flex align-items-center justify-content-between flex-wrap gap-2" role="alert">
	<span>{PHP.L.msgr_login_required}</span>
	<a class="btn btn-primary btn-sm" href="{MSGR_LOGIN_URL}">{PHP.L.msgr_btn_login}</a>
</div>
<!-- ENDIF -->
<!-- END: MAIN -->
