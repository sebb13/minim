<h3>{__SITEMAP_PAGE_TITLE__}</h3>
<hr />
<div class="row">
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
			<div class="panel-footer">
				<div class="row form-group">
					<form action="#" method="post" class="updatesSitemap">
						<div class="col-md-7">
							<label for="last-mod" class="form-control">{__LAST_MOD_LABEL__}: {__LAST_MOD__}</label>
						</div>
						<div class="col-md-5">
							<input type="button" class="btn btn-success btn-md" id="last-mod" value="{__REGENERATE_SITEMAP__}" />
						</div>
						<input type="hidden" name="regenerateSitemapConfirm" id="regenerateSitemapConfirm" value="{__REGENERATE_SITEMAP_CONFIRM__}" />
					</form>
				</div>
			</div>
		</div>
	</div>
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
				robots.txt
			</div>
			<div class="panel-body">
				{__ROBOT_TXT__}
			</div>
		</div>
	</div>
</div>
<!-- robots.txt-->