<form method="post" id="routingForm">
	<div class="row form-group">
		<div class="col-md-6">
			<div class="row form-group">
				<div class="col-md-6">
					<label for="moduleToConfigure" class="form-control">{__MODULE_TO_CONFIGURE__}</label>
				</div>
				<div class="col-md-6">
					<select name="moduleToConfigure" id="moduleToConfigure" class="form-control">
						{__MODULES_LIST__}
					</select>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="row form-group">
				<div class="col-md-6">
					<label for="sideToConfigure" class="form-control">{__SIDE_TO_CONFIGURE__}</label>
				</div>
				<div class="col-md-6">
					<select name="sideToConfigure" id="sideToConfigure" class="form-control">
						{__SIDES_LIST__}
					</select>
				</div>
			</div>
		</div>
	</div>
	<h1>{__PAGE_TITLE__}</h1>
	<div id="RoutesList">
		{__CONTENT__}
	</div>
	<div id="newRoutes"></div>
	<div class="row">
		<div class="col-md-5">
			<fieldset>
				<legend>{__ADD_ROUTES__}</legend>
				<div class="row form-group">
					<div class="col-md-3">
						<select name="nbRoutesToAdd" id="nbRoutesToAdd" class="form-control">
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
							<option value="5">5</option>
						</select>
					</div>
					<div class="col-md-9">
						<button type="button" class="btn btn-success btn-md" id="getNewRouteEntries" alt="{__ADD_ROUTES__}" title="{__ADD_ROUTES__}">
							<span class="glyphicon glyphicon-plus-sign"></span>
						</button>
					</div>
				</div>
			</fieldset>
		</div>
		<div class="col-md-7">
			<fieldset>
				<legend>{__CONTROLS__}</legend>
				<input type="submit" class="btn btn-success" id="saveRoutesButton" value="{__SAVE__}" />
				<button type="button" class="btn btn-danger backHomeButton">{__CANCEL__}</button>
			</fieldset>
		</div>
	</div>
	<input type="hidden" name="removeRouteConfirm" id="removeRouteConfirm" value="{__CONFIRM_DELETE_ROUTE__}" />
	<input type="hidden" name="exw_action" value="Core::saveRoutes" />
	<input type="hidden" name="app_token" value="{##APP_TOKEN##}" />
</form>
{__BENCHMARK__}