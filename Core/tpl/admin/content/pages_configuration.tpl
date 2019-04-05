<form action="" method="post" id="pageConfig">
	<div class="row">
		<div class="col-md-6">
			<fieldset>
				<legend>{__PAGE_TO_CONFIGURE__}</legend>
				<div class="row form-group">
					<div class="col-md-6">
						<select name="pageToConfigure" id="pageToConfigure" class="form-control">
							{__PAGE_LIST__}
						</select>
					</div>
					<div class="col-md-6">
						<input type="button" class="btn btn-default btn-md" id="deleteConf" value="{__DELETE_CONF__}">
					</div>
			</fieldset>
		</div>
		<div class="col-md-6">
			<fieldset>
				<legend>{__ACTIONS__}</legend>
				<div class="row form-group">
					<div class="col-md-6">
						<input type="text" id="newPageName" name="newPageName" placeholder="{__NEW_PAGE_NAME__}" class="form-control input-md" style="width:100%;">
					</div>
					<div class="col-md-6">
						<input type="button" id="addPage" class="btn btn-default btn-md" value="{__NEW_PAGE__}">
					</div>
				</div>
			</fieldset>
		</div>
		<div class="col-md-6">
			<fieldset>
				<legend>{__ROBOTS__}</legend>
				<div class="btn-group" data-toggle="buttons">
					<label class="btn btn-primary {__INDEX_ACTIVE__}">	
						<input type="radio" name="index" value="index" id="index"{__INDEX_CHECKED__}>index
					</label>
					<label class="btn btn-primary {__NOINDEX_ACTIVE__}">
						<input type="radio" name="index" value="noindex"id="noindex"{__NOINDEX_CHECKED__}>noindex
					</label>
				</div>
				<div class="btn-group" data-toggle="buttons">
					<label class="btn btn-primary {__FOLLOW_ACTIVE__}">	
						<input type="radio" name="follow" value="follow" id="follow"{__FOLLOW_CHECKED__}>follow
					</label>
					<label class="btn btn-primary {__NOFOLLOW_ACTIVE__}">
						<input type="radio" name="follow" value="nofollow" id="nofollow"{__NOFOLLOW_CHECKED__}>nofollow
					</label>
				</div>
				<div class="btn-group" data-toggle="buttons">
					<label class="btn btn-primary {__NOARCHIVE_ACTIVE__}">
						<input type="checkbox" name="noarchive" value="noarchive" id="noarchive"{__NOARCHIVE_CHECKED__}>noarchive
					</label>
				</div>
				<div class="btn-group">
				  {__ROBOTS_TOOLTIP__}
				</div>
			</fieldset>
			<fieldset>
				<legend>{__VIEW__}</legend>
				<div class="row form-group">
					<div class="col-md-4">
						<label for="view" class="form-control">{__VIEW_NAME_LABEL__}&nbsp;{__VIEW_NAME_TOOLTIP__}</label>
					</div>
					<div class="col-md-8">
						<select id="view" name="view" class="form-control">
							{__VIEW_LIST__}
						</select>
					</div>
				</div>
			</fieldset>
			<fieldset>
				<legend>Open Graph <a href="https://developers.facebook.com/tools/debug/" target="_blank">developers.facebook.com/tools/debug</a></legend>
				{__OPEN_GRAPH__}
			</fieldset>
		</div>
		<div class="col-md-6">
			<fieldset>
				<legend>{__META_TAGS__}</legend>
				{__META_TAGS_CONTENT__}
			</fieldset>
			<fieldset>
				<legend>{__TWITTER_TAGS__} <a href="https://cards-dev.twitter.com/validator" target="_blank">cards-dev.twitter.com/validator</a></legend>
				{__TWITTER_CONTENT__}
			</fieldset>
			<fieldset>
				<legend>{__CONTROLS__}</legend>
				<input type="hidden" id="deletePageConfConfirm" value="{__CONFIRM_DELETE_PAGE__}" />
				<input type="hidden" name="exw_action" value="Pages::savePageConfig" />
				<input type="hidden" name="app_token" value="{##APP_TOKEN##}" />
				<input type="submit" class="btn btn-success" value="{__SAVE__}" />&nbsp;
				<button type="button" class="btn btn-danger backHomeButton">{__CANCEL__}</button>
			</fieldset>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12 text-right">
			{__BENCHMARK__}
		</div>
	</div>
</form>