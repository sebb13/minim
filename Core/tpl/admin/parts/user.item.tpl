<div class="panel panel-default conf-box">
	<div class="panel-heading conf-box-header">{__LOGIN_VALUE__}</div>
	<div class="panel-body">
		<div class="row">
			<div class="col-md-3">	
				<div class="row form-group">
					<div class="col-md-5">
						<label for="login-{__USER_ID__}" class="form-control">{__LOGIN__}</label>
					</div>
					<div class="col-md-7">
						<!-- autocomplete="off" -->
						<input type="text" name="login-{__USER_ID__}"  style="display:none;" />
						<input type="text" name="login-{__USER_ID__}" id="login-{__USER_ID__}" value="{__LOGIN_VALUE__}" class="form-control" />
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="row form-group">
					<div class="col-md-6">
						<label for="pwd-{__USER_ID__}" class="form-control">{__PWD__}</label>
					</div>
					<div class="col-md-6">
						<!-- autocomplete="off" -->
						<input type="password" name="pwd-{__USER_ID__}" style="display:none;" />
						<input type="password" name="pwd-{__USER_ID__}" id="pwd-{__USER_ID__}" class="form-control" />
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="row form-group">
					<div class="col-md-8">
						<label for="role-{__USER_ID__}" class="form-control">{__USER_ACCOUNT_TYPE__}</label>
					</div>
					<div class="col-md-4">
						<select id="role-{__USER_ID__}" name="role-{__USER_ID__}" class="form-control">
							{__USER_ROLE_OPTIONS__}
						</select>
					</div>
				</div>
			</div>
			<div class="col-md-1 form-group">
				<input type="hidden" name="user-{__USER_ID__}" value="{__LOGIN_VALUE__}">
				<button type="button" class="btn btn-danger btn-md removeUserButton">
					<span class="glyphicon glyphicon-trash"></span>
				</button>
			</div>
		</div>
	</div>
</div>