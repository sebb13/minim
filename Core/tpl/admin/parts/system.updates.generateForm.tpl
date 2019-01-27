<div class="col-md-6">
	<form action="" method="post" class="generateUpdatesForm">
		<div class="panel panel-default conf-box">
			<div class="panel-heading conf-box-header">{__MODULE_NAME__} ({__CURRENT_VERSION__})</div>
			<div class="panel-body">
				<div class="row form-group">
					<input type="hidden" name="module-name" value="{__MODULE_NAME__}" />
					<div class="col-md-3">
						<label for="release" class="form-control" >{__VERSION__}</label>
					</div>
					<div class="col-md-2">
						<input type="text" name="main-version" value="{__MAIN_VERSION__}" class="form-control" />
					</div>
					<div class="col-md-2">
						<input type="text" name="sub-version" value="{__SUB_VERSION__}" class="form-control" />
					</div>
					<div class="col-md-2">
						<input type="text" name="release" value="{__RELEASE__}" class="form-control" />
					</div>
					<div class="col-md-3">
						<button type="button" class="btn btn-success btn-md generateUpdatesButton">{__GENERATE__}</button>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>