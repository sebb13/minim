<form action="" method="post" class="updatesForm">
	<div class="panel panel-default conf-box">
		<div class="panel-heading conf-box-header">
			<div class="row">
				<div class="col-md-6">
					<h4>{__PAGE_NAME__}</h4>
				</div>
				<div class="col-md-6 text-right">
					<input type="hidden" name="{__PAGE_NAME__}value" value="{__PAGE_NAME__}"/>
					<input type="button" class="btn btn-danger purgeVersionsButton" id="{__PAGE_NAME__}button" value="{__PURGE_VERSIONS__}" />
				</div>
			</div>
		</div>
		<div class="panel-body">
			{__VERSIONS__}
		</div>
	</div>
</form>





