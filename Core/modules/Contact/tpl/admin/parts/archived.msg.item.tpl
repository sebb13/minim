<div class="panel panel-default conf-box">
	<div class="panel-heading conf-box-header">
		<div class="row">
			<div class="col-md-6">
				<strong>{__CONTACT_NAME__}</strong>
			</div>
			<div class="col-md-6">
				<a href="mailto:{__CONTACT_EMAIL__}">{__CONTACT_EMAIL__}</a>
			</div>
		</div>
		<h5>{__CONTACT_DATE__}</h5>
		<div class="row contact-msg-part">
			<div class="col-md-6">
				<i>{__CONTACT_SUBJECT__}</i>
			</div>
			<div class="col-md-6">
				<a href="{__CONTACT_FILE_URL__}" download="{__CONTACT_FILE_URL__}">{__CONTACT_FILE__}</a>
			</div>
		</div>
	</div>
	<form action="#" method="POST" class="msgForm">
		<div class="panel-body">
			<div class="row form-group">
				<div class="col-md-10">
					{__CONTACT_MSG__}
				</div>
				<div class="col-md-2 text-right">
					<input type="button" class="restoreMsgButton btn btn-success btn-md" value="{__RESTORE__}" />
					<button type="button" class="btn btn-danger btn-md deleteMsgButton">
						<span class="glyphicon glyphicon-trash"></span>
					</button>
				</div>
			</div>
		</div>
		<div class="panel-footer">
			<div class="row comment-zone form-group">
				{__COMMENT__}
			</div>
			<input type="hidden" name="contact_id" class="contact_id" value="{__CONTACT_ID__}" />
			<input type="hidden" name="confirm_delete" value="{__CONFIRM_DELETE_MSG__}" />
		</div>
	</form>
</div>