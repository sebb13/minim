<div class="row">
	<div class="col-md-6">
		<div class="panel panel-default conf-box">
			<div class="panel-heading conf-box-header"><a href="{__SITE_URL__}" target="_blank">{__SITE_URL__}</a></div>
			<div class="panel-body iframe-home">
				<iframe 
					src="{__SITE_URL__}" 
					frameborder="0" 
					scrolling="yes" 
					width="100%" 
					height="475px" 
					id="iniframe" 
					style="border:0; -moz-border-radius:5px;border-radius:5px;">
				</iframe>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="panel panel-default conf-box">
			<div class="panel-heading conf-box-header">{__ERROR_LOGS_TITLE__}</div>
			<div class="panel-body chart-box">
				<div id="chart_div_home"></div>
			</div>
		</div>
	</div>
</div>
<div class="row dashboard">
	{__DASHBOARD__}
</div>
{__BENCHMARK__}