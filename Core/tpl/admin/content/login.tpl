<div class="row form-group">
	<form action="#" method="post" accept-charset="UTF-8" id="loginForm">
		<div class="col-md-2">
			<label for="user" class="form-control">{__LOGIN__}</label>
		</div>
		<div class="col-md-2">
			<input type="text" name="user" style="display:none;" />
			<input type="text" name="user" id="user" value="{__LOGIN_VALUE__}" class="form-control" />
		</div>
		<div class="col-md-2">
			<label for="pwd" class="form-control">{__PWD__}</label>
		</div>
		<div class="col-md-2">
			<input type="password" name="pwd" style="display:none;" />
			<input type="password" name="pwd" id="pwd" class="form-control" />
		</div>
		<div style="display:none;">
			<input type="text" name="captcha" />
		</div>
		<div class="col-md-4">
			<input type="submit" value="{__VALIDATE__}" class="btn btn-default btn-md" />
		</div>
		<input type="hidden" name="app_token" value="{##APP_TOKEN##}" />
	</form>
</div>