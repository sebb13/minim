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
		<div class="row">
			<div class="col-md-6">
				<i>{__CONTACT_SUBJECT__}</i>
			</div>
			<div class="col-md-6">
				<a href="{__CONTACT_FILE_URL__}" download="{__CONTACT_FILE_URL__}">{__CONTACT_FILE__}</a>
			</div>
		</div>
	</div>
	<form action="#" method="post" class="msgForm">
		<div class="panel-body">
			<div class="row form-group">
				<div class="col-md-11">
					{__CONTACT_MSG__}
				</div>
				<div class="col-md-1 text-right">
					<input type="button" class="archiveMsgButton btn btn-danger btn-sm form-control" value="{__ARCHIVE__}" />
				</div>
			</div>
		</div>
		<div class="panel-footer">
			<div class="row comment-zone form-group">
				{__COMMENT__}
			</div>
			<input type="hidden" name="contact_id" class="contact_id" value="{__CONTACT_ID__}" />
			<input type="hidden" name="confirmArchiveConfirm" value="{__CONFIRM_ARCHIVE_MSG__}" />
		</div>
	</form>
</div>