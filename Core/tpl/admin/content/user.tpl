<form action="#" method="post" accept-charset="UTF-8" id="userPageForm">
	<div class="panel panel-default conf-box">
		<div class="panel-heading conf-box-header">{__EDIT_MY_INFORMATION__}</div>
		<div class="panel-body">
			<div class="row">
				<div class="col-md-6">
					<div class="row form-group">
						<div class="col-md-5">
							<label for="login" class="form-control">{__LOGIN__}</label>
						</div>
						<div class="col-md-7">
							<input type="text" id="login" name="login" value="{__LOGIN_VALUE__}" class="form-control" />
						</div>
					</div>
					<div class="row form-group">
						<div class="col-md-5">
							<label for="pwd" class="form-control">{__NEW_PWD__}</label>
						</div>
						<div class="col-md-7">
							<input type="password" id="pwd" name="pwd" value="" autocomplete="off" class="form-control" />
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="row form-group">
						<div class="col-md-5">
							<label for="currentPwd" class="form-control text-danger">{__ENTER_CURRENT_PWD__}</label>
						</div>
						<div class="col-md-7">
							<input type="password" id="currentPwd" name="currentPwd" class="form-control" />
						</div>
					</div>
					<div class="row form-group text-right">
						<div class="col-md-12">
							<input type="hidden" id="confirmUpdateUserAccountMsg" value="{__CONFIRM_UPDATE_USER_ACCOUNT__}" />
							<input type="hidden" id="currentUserLogin" value="{__LOGIN_VALUE__}" />
							<button type="button" class="btn btn-success" id="updateUserButton">{__SAVE__}</button>
							<button type="button" class="btn btn-danger backHomeButton">{__CANCEL__}</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>