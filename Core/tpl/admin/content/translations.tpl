<h3>{__TRANSLATIONS__} : {__ORIGINE__}</h3>
<hr />
<form action="" method="post" id="translations">
	<div class="row">
		<div class="col-md-6">
			<div class="row form-group">
				<div class="col-md-5">
					<label for="sFileToTranslate" class="form-control text-center">{__FILE_TO_TRANSLATE__}</label>
				</div>
				<div class="col-md-5">
					<select name="sFileToTranslate" id="sFileToTranslate" class="form-control">
						{__FILE_LIST__}
					</select>
				</div>
			</div>
		</div>
		<div class="col-md-3">
			<div class="row form-group">
				<div class="col-md-8">
					<label for="refLang" class="form-control text-center">{__REF_LANG__}</label>
				</div>
				<div class="col-md-4">
					<select name="sRefLang" id="sRefLang" class="form-control">
						{__REF_LANG_LIST__}
					</select>
				</div>
			</div>
		</div>
		<div class="col-md-3">
			<div class="row form-group">
				<div class="col-md-8">
					<label for="langToTranslate" class="form-control text-center">{__LANG_TO_TRANSLATE__}</label>
				</div>
				<div class="col-md-4">
					<select name="sLangToTranslate" id="sLangToTranslate" class="form-control">
						{__LANG_TO_TRANS_LIST__}
					</select>
				</div>
			</div>
		</div>
	</div>
	<div id="translations_container">{__CONTENTS__}</div>
	<input type="hidden" name="exw_action" value="Core::saveTranslations" />
	<input type="hidden" name="app_token" value="{##APP_TOKEN##}" />
	<div class="row">
		<div class="col-md-6">
			<fieldset>
				<legend>{__MANAGING_DRAFTS__}</legend>
				{__TRANSLATES_PREVIEW__}
			</fieldset>
		</div>
		<div class="col-md-6">
			{__DRAFT_CONTROLS__}
		</div>
	</div>
</form>
<br />