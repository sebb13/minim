<h3>{__USER_MANAGER__}</h3>
<hr />
<form action="#" method="post" id="addUserForm" accept-charset="UTF-8" autocomplete="off">
	<div class="panel panel-default conf-box">
		<div class="panel-heading conf-box-header">{__ADD_USER__}</div>
		<div class="panel-body">
			<div class="row">
				<div class="col-md-4">	
					<div class="row form-group">
						<div class="col-md-5">
							<label for="user_login" class="form-control">{__LOGIN__}</label>
						</div>
						<div class="col-md-7">
							<!-- autocomplete="off" -->
							<input type="text" name="user_login" style="display:none" />
							<input type="text" name="user_login" id="user_login" class="form-control" />
						</div>
					</div>
				</div>
				<div class="col-md-4">
					<div class="row form-group">
						<div class="col-md-6">
							<label for="user_pwd" class="form-control">{__PWD__}</label>
						</div>
						<div class="col-md-6">
							<!-- autocomplete="off" -->
							<input type="password" name="user_pwd" style="display:none">
							<input type="password" name="user_pwd" id="user_pwd" class="form-control" />
						</div>
					</div>
				</div>
				<div class="col-md-4">
					<div class="row form-group">
						<div class="col-md-8">
							<label for="user_role" class="form-control">{__USER_ACCOUNT_TYPE__}</label>
						</div>
						<div class="col-md-4">
							<select id="user_role" name="user_role" class="form-control">
								{__USER_ROLE_OPTIONS__}
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="panel-footer">
			<button type="button" class="btn btn-success" id="addUserButton">{__SAVE__}</button>
			<button type="button" class="btn btn-danger backHomeButton">{__CANCEL__}</button>
		</div>
	</div>
</form>
<h3 class="margin-bottom-20">{__MANAGE_USER_ACCOUNTS__}</h3>
<form action="#" method="post" id="updateUsersForm" accept-charset="UTF-8" autocomplete="off">
	<input type="text" style="display:none" />
	<input type="password" style="display:none" />
	{__USERS__}
	<div class="row">
		<div class="col-md-12 margin-bottom-20">
			<fieldset>
				<legend>{__CONTROLS__}</legend>
				<input type="hidden" id="confirmDeleteUserAccountMsg" value="{__CONFIRM_DELETE_USER_ACCOUNT__}" />
				<input type="hidden" id="confirmUpdateUsersAccountMsg" value="{__CONFIRM_UPDATE_USER_ACCOUNTS__}" />
				<button type="button" class="btn btn-success" id="updateUsersButton">{__SAVE__}</button>
				<button type="button" class="btn btn-danger backHomeButton">{__CANCEL__}</button>
			</fieldset>
		</div>
	</div>
</form>