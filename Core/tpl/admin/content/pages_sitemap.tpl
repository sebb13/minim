<h3>{__SITEMAP_PAGE_TITLE__}</h3>
<hr />
<div class="row">
	<div class="col-md-6">
		<div class="panel panel-default conf-box">
			<div class="panel-heading conf-box-header">
				{__SITEMAP_TITLE__}
			</div>
			<div class="panel-body">
				<ul>
					{__SITEMAP__}
				</ul>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="panel panel-default conf-box">
			<div class="panel-heading conf-box-header">
				{__SITEMAP_XML_TITLE__}
			</div>
			<div class="panel-body">
				<ul>
					{__SITEMAP_XML__}
				</ul>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-6">
		<div class="panel panel-default conf-box">
			<div class="panel-heading conf-box-header">
				{__PAGES_TO_IGNORE_TITLE__}
			</div>
			<div class="panel-body">
				<div class="row form-group">
					<form action="#" method="post">
						<div class="col-md-4">
							<label for="last-mod" class="form-control">{__PAGES_TO_IGNORE_LABEL__}:</label>
						</div>
						<div class="col-md-6">
							<input type="text" name="addPageToIgnore" id="addPageToIgnore" class="form-control" />
						</div>
						<div class="col-md-2">
							<button type="button" class="btn btn-success btn-sm form-control" id="addPageToIgnoreButton">
								<span class="glyphicon glyphicon-floppy-disk"></span>
							</button>
						</div>
						<input type="hidden" name="deletePageToIgnoreConfirm" id="deletePageToIgnoreConfirm" value="{__DELETE_PAGE_TO_IGNORE_CONFIRM__}" />
						<input type="hidden" name="addPageToIgnoreConfirm" id="addPageToIgnoreConfirm" value="{__ADD_PAGE_TO_IGNORE_CONFIRM__}" />
					</form>
				</div>
			</div>
			<div class="panel-footer">
				{__PAGES_TO_IGNORE__}
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="panel panel-default conf-box">
			<div class="panel-heading conf-box-header">
				{__LAST_MOD_LABEL__}: {__LAST_MOD__}
			</div>
			<div class="panel-body">
				<form action="#" method="post" class="updatesSitemap">
					<div class="row form-group">
							<div class="col-md-12">
								<strong>robots.txt:</strong><br />
								{__ROBOT_TXT__}
							</div>
					</div>
					<div class="row form-group">
							<div class="col-md-12">
								<input type="button" class="btn btn-success btn-md" id="last-mod" value="{__REGENERATE_SITEMAP__}" />
							</div>
							<input type="hidden" name="regenerateSitemapConfirm" id="regenerateSitemapConfirm" value="{__REGENERATE_SITEMAP_CONFIRM__}" />

					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- robots.txt-->