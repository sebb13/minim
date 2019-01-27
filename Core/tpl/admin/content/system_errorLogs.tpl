<div class="row">
	<div class="col-md-3">
		<div class="panel panel-default conf-box">
			<div class="panel-heading conf-box-header">{__PAGE_TITLE__}</div>
			<div class="panel-body">
				<div id="errorLogsList">
					<ul>
						{__LOGS_LIST__}
					</ul>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-9">
		<div class="panel panel-default conf-box">
			<div class="panel-heading conf-box-header">{__ERROR_LOGS_TITLE__}</div>
			<div class="panel-body">
				<div id="chart_div"></div>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<form action="#">
			<fieldset>
				<legend>{__CONTROLS__}</legend>
				<div class="col-md-9">
					<input type="hidden" id="purgeOldLogsConfirm" name="purgeOldLogsConfirm" value="{__PURGE_OLD_LOGS_CONFIRM__}" />
					<input type="button" class="btn btn-danger" id="purgeOldLogsButton" value="{__PURGE_OLD_LOGS__}" />
				</div>
				{__BENCHMARK__}
			</fieldset>
		</form>
	</div>
</div>