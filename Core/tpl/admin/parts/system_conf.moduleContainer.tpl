<form action="" method="post" class="system_conf">
	<div class="panel panel-default conf-box">
		<div class="panel-heading conf-box-header">{__MODULE_NAME__}</div>
		<div class="panel-body">
			<div class="row">
				<div class="col-md-6">
					<fieldset>
						<legend>{__CONFIGURATION__}</legend>
						{__CONF__}
					</fieldset>
				</div>
				<div class="col-md-6">
					<fieldset>
						<legend>system configuration</legend>
						{__SYS_CONF__}
					</fieldset>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<input type="hidden" name="exw_action" value="Configuration::saveGlobalConf" />
					<input type="hidden" name="moduleToUpdate" value="{__MODULE_NAME__}" />
					<input type="hidden" name="callback" value="{__CALLBACK__}" />
					<input type="hidden" name="app_token" value="{##APP_TOKEN##}" />
					<input type="submit" class="btn btn-success pull-right" value="{__SAVE__}" />
				</div>
			</div>
		</div>
	</div>
</form>