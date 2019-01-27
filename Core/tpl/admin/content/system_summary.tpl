<div class="row">
	<div class="col-md-6">
		<div class="panel panel-default conf-box">
			<div class="panel-heading conf-box-header">
				{__ROUTING_TITLE__}
			</div>
			<div class="panel-body">
				<h4><em>Front :</em></h4>
				<p>{__ROUTING_FRONT__}</p>
				<h4><em>Back :</em></h4>
				<p>{__ROUTING_BACK__}</p>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="panel panel-default conf-box">
			<div class="panel-heading conf-box-header">
				{__NB_ERRORS__} {__NB_ERRORS_WORDING__}
			</div>
			<div class="panel-body">
				<div class="col-md-6 form-group">
					<form action="/{##LANG##}/system/routing.html" method="post" class="systemForm">
						<input type="hidden" name="app_token" value="{##APP_TOKEN##}" />
						<input type="submit" class="btn btn-success form-control" value="{__MANAGE_ROUTES__}" />
					</form>
				</div>
				<div class="col-md-6 form-group">
					<form action="/{##LANG##}/system/user.html" method="post" class="systemForm">
						<input type="hidden" name="app_token" value="{##APP_TOKEN##}" />
						<input type="submit" class="btn btn-success form-control" value="{__MANAGE_USERS__}" />
					</form>
				</div>
				<div class="col-md-6 form-group">
					<form action="/{##LANG##}/system/conf.html" method="post" class="systemForm">
						<input type="hidden" name="app_token" value="{##APP_TOKEN##}" />
						<input type="submit" class="btn btn-success form-control" value="{__GLOBAL_CONF__}" />
					</form>
				</div>
				<div class="col-md-6 form-group">
					<form action="/{##LANG##}/system/updates.html" method="post" class="systemForm">
						<input type="hidden" name="app_token" value="{##APP_TOKEN##}" />
						<input type="submit" class="btn btn-success form-control" value="{__SYS_UPDATES__}" />
					</form>
				</div>
				<div class="col-md-6 form-group">
					<form action="/{##LANG##}/system/logs.html" method="post" class="systemForm">
						<input type="hidden" name="app_token" value="{##APP_TOKEN##}" />
						<input type="submit" class="btn btn-success form-control" value="{__TODAYS_LOGS__}" />
					</form>
				</div>
				<div class="col-md-6 form-group">
					<form action="/{##LANG##}/system/errorLogs.html" method="post" class="systemForm">
						<input type="hidden" name="app_token" value="{##APP_TOKEN##}" />
						<input type="submit" class="btn btn-success form-control" value="{__ERROR_LOGS__}" />
					</form>
				</div>
				<div class="col-md-6 form-group">
					<form action="/{##LANG##}/system/resetCache.html" method="post" class="systemForm">
						<input type="hidden" name="app_token" value="{##APP_TOKEN##}" />
						<input type="submit" class="btn btn-danger form-control" value="{__RESET_CACHE__}" />
					</form>
				</div>
				<div class="col-md-6 form-group">
					<form action="/{##LANG##}/system/sessionGC.html" method="post" class="systemForm">
						<input type="hidden" name="app_token" value="{##APP_TOKEN##}" />
						<input type="submit" class="btn btn-danger form-control" value="{__CLEAN_OLD_SESSIONS__}" />
					</form>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="panel panel-default conf-box">
			<div class="panel-heading conf-box-header">
				sysAdmin link
			</div>
			<div class="panel-body form-group text-center">
				<form>
					<input type="text" id="to-copy-target" class="form-control" value="{__SYS_ADMIN_LINK__}" readonly />
					<input type="button" class="btn btn-default btn-xs" id="to-copy" value="{__COPY_TO_CLIPBOARD__}" />
				</form>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="panel panel-default conf-box">
			<div class="panel-heading conf-box-header">
				code lines
			</div>
			<div class="panel-body">
				{__CODE_LINES__}
			</div>
		</div>
	</div>
</div>
<div class="row benchmark">
	{__BENCHMARK_LABEL__}{__BENCHMARK_VALUE__}s
</div>