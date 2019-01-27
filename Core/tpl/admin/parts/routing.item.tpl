<div class="row">
	<div class="col-md-5">	
		<div class="row form-group">
			<div class="col-md-3">
				<label for="page-{__PAGE_NAME__}" class="form-control">{__PAGE_LEGEND__}</label>
			</div>
			<div class="col-md-9">
				<input type="text" id="page-{__PAGE_NAME__}" name="{__PAGE_NAME__}" value="{__PAGE_VALUE__}" class="form-control" />
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="row form-group">
			<div class="col-md-3">
				<label for="route-{__PAGE_NAME__}" class="form-control">{__ROUTE_LEGEND__}</label>
			</div>
			<div class="col-md-9">
				<input type="text" id="route-{__PAGE_NAME__}" name="{__ROUTE_NAME__}" value="{__ROUTE_VALUE__}" class="form-control" />
			</div>
		</div>
	</div>
	<div class="col-md-1 form-group">
		<button type="button" class="btn btn-danger btn-sm removeRoute" id="{__PAGE_VALUE__}">
			<span class="glyphicon glyphicon-trash"></span>
		</button>
	</div>
</div>