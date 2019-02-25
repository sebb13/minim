<div class="col-md-9">	
	<div class="row form-group">
		<div class="col-md-5">
			<label for="file-{__FILE_ID__}" class="form-control">{__FILE_ID__}</label>
		</div>
		<div class="col-md-5">
			<input type="text" readonly id="file-{__FILE_ID__}" name="{__FILE_ID__}" value="{__FILE_NAME__}" class="form-control" />
		</div>
		<div class="col-md-2">
			<input type="text" name="copy-{__FILE_ID__}" id="copy-{__FILE_ID__}" value="/{##LANG##}/downloadFile.html?file_id={__FILE_ID__}" class="form-control" />
		</div>
	</div>
</div>
<div class="col-md-1">
	<button type="button" class="btn btn-success btn-sm to-copy" data-to-copy="copy-{__FILE_ID__}">
		<span class="glyphicon glyphicon-copy"></span>
	</button>
</div>
<div class="col-md-1">
	<a href="/{##LANG##}/downloadFile.html?file_id={__FILE_ID__}">
		<button type="button" class="btn btn-success btn-sm">
			<span class="glyphicon glyphicon-download"></span>
		</button>
	</a>
</div>
<div class="col-md-1">
	<button type="button" class="btn btn-danger btn-sm removeFile" id="remove-{__FILE_ID__}">
		<span class="glyphicon glyphicon-trash"></span>
	</button>
</div>