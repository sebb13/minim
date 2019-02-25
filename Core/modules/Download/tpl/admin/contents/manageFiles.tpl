<h3>{__MANAGE_FILES__}</h3>
<hr />
<div class="row">
	<div class="col-md-12">
		<h4>{__ADD_FILE__}</h4>
	</div>
	<form action="#" method="POST" id="addFileForm" enctype="multipart/form-data" accept-charset="UTF-8">
		<div class="col-md-9">	
			<div class="row form-group">
				<div class="col-md-5">
					<input type="file" id="addFileInput" name="addFileInput" class="form-control" />
				</div>
				<div class="col-md-7">
					<input type="text" placeholder="ID" name="addFileId" id="addFileId" class="form-control" maxlength="20" />
				</div>
			</div>
		</div>
		<div class="col-md-2">
			<button type="button" class="btn btn-success btn-sm" id="addFileButton">
				<span class="glyphicon glyphicon-upload"></span>
			</button> {__FILE_ID_TOOLTIP__}
		</div>
		<div class="col-md-12 progress" style="display:none">
			<div id="progressbar" class="progress-bar-success progress-bar-striped active text-center" style="width:0">
				0%
			</div>
		</div>
		<input type="hidden" id="error_missing_file_id" value="{__ERROR_MISSING_FILE_ID__}" />
		<input type="hidden" id="error_file_id_already_exist" value="{__ERROR_FILE_ID_ALREADY_EXIST__}" />
		<input type="hidden" id="error_file_id_must_begin_with_letter" value="{__ERROR_FILE_ID_MUST_BEGIN_WITH_LETTER__}" />
		<input type="hidden" id="error_file_id_must_contain_only_nums_and_letters" value="{__ERROR_FILE_ID_MUST_CONTAIN_ONLY_NUMS_AND_LETTERS__}" />
	</form>
</div>
<hr />
<div class="row">
	<div class="col-md-12">
		<h4>{__MANAGE_FILES__}</h4>
	</div>
	<form>
		{__FILES__}
		<input type="hidden" name="confirmDeleteFile" value="{__CONFIRM_DELETE_FILE__}" />
	</form>
</div>