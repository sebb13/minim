<div class="row">
	<div class="col-md-7">
		<h3>{__ROUTING_TITLE__}</h3>
		<h4><em>Front :</em></h4>
		<p>{__ROUTING_FRONT__}</p>
		<h4><em>Back :</em></h4>
		<p>{__ROUTING_BACK__}</p>
	</div>
	<div class="col-md-4">
		<h3>{__NB_ERRORS__} {__NB_ERRORS_WORDING__}</h3>
		<p>
			sysAdmin link : <a href="{__SYS_ADMIN_LINK__}">{__SYS_ADMIN_LINK__}</a>
		</p>
		<br />
		<form action="/{##LANG##}/system/routing.html" method="post" class="systemForm">
			<input type="hidden" name="app_token" value="{##APP_TOKEN##}" />
			<input type="submit" class="btn btn-success" value="{__MANAGE_ROUTES__}" />
		</form>
		<br />
		<form action="/{##LANG##}/system/user.html" method="post" class="systemForm">
			<input type="hidden" name="app_token" value="{##APP_TOKEN##}" />
			<input type="submit" class="btn btn-success" value="{__MANAGE_USERS__}" />
		</form>
		<br />
		<form action="/{##LANG##}/system/conf.html" method="post" class="systemForm">
			<input type="hidden" name="app_token" value="{##APP_TOKEN##}" />
			<input type="submit" class="btn btn-success" value="{__GLOBAL_CONF__}" />
		</form>
		<br />
		<form action="/{##LANG##}/system/logs.html" method="post" class="systemForm">
			<input type="hidden" name="app_token" value="{##APP_TOKEN##}" />
			<input type="submit" class="btn btn-success" value="{__TODAYS_LOGS__}" />
		</form>
		<br />
		<form action="/{##LANG##}/system/errorLogs.html" method="post" class="systemForm">
			<input type="hidden" name="app_token" value="{##APP_TOKEN##}" />
			<input type="submit" class="btn btn-success" value="{__ERROR_LOGS__}" />
		</form>
		<br />
		<form action="/{##LANG##}/system/updates.html" method="post" class="systemForm">
			<input type="hidden" name="app_token" value="{##APP_TOKEN##}" />
			<input type="submit" class="btn btn-success" value="{__SYS_UPDATES__}" />
		</form>
		<br />
		<form action="/{##LANG##}/system/resetCache.html" method="post" class="systemForm">
			<input type="hidden" name="app_token" value="{##APP_TOKEN##}" />
			<input type="submit" class="btn btn-danger" value="{__RESET_CACHE__}" />
		</form>
		<br />
		<form action="/{##LANG##}/system/sessionGC.html" method="post" class="systemForm">
			<input type="hidden" name="app_token" value="{##APP_TOKEN##}" />
			<input type="submit" class="btn btn-danger" value="{__CLEAN_OLD_SESSIONS__}" />
		</form>
		<br />
		<p>
			code lines : 
			<br />
			{__CODE_LINES__}
		</p>
		<div class="row benchmark">
			{__BENCHMARK_LABEL__}{__BENCHMARK_VALUE__}s
		</div>
	</div>
</div>