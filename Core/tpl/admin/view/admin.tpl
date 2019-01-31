<!doctype html>
<html lang="{__LANG__}">
<head>
	<title>{##DOMAIN_NAME##}</title>
	<!-- METAS -->
	{__METATAGS__}
	<!-- CSS -->
	{__CSS__}
</head>
<body>{__DEV_BANNER__}
	<div class="container">
		<nav class="navbar navbar-default">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
			</div>
			<!-- Collect the nav links, forms, and other content for toggling -->
			<div class="collapse navbar-collapse menu-zone" id="bs-example-navbar-collapse-1">
				<ul class="nav navbar-nav menu">
					{__MENU__}
				</ul>
				<ul class="nav navbar-nav admin-right-zone">
					<li class="header-logo">
						<a href="{##FRONT_URL##}" target="_blank">
							<img src="/img/core/logo_admin.png" alt="{##FRONT_URL##}" title="{##FRONT_URL##}" />
						</a>
					</li>
					<li id="langSwitcher">
						{__FLAGS__}
					</li>
					<li>
						<form action="#" method="post">
							<input type="hidden" name="exw_action" value="User::getUserPage" />
							<input type="hidden" name="app_token" value="{##APP_TOKEN##}" />
							<button class="btn btn-success btn-sm userHeaderButton" title="{__LOGIN__}" id="editUserButton">{__LOGIN_VALUE__}</button>
						</form>
					</li>
					<li>
						<form action="/{##LANG##}/login.html" method="post">
							<input type="hidden" name="exw_action" value="Core::logout" />
							<input type="hidden" name="app_token" value="{##APP_TOKEN##}" />
							<button type="submit" class="btn btn-danger btn-sm userHeaderButton" title="{__LOGOUT__}" id="logoutButton">
								<span class="glyphicon glyphicon-off"></span>
							</button>
						</form>
					</li>
				</ul>
			</div>
		</nav>
		<div id="ajaxFrame">{__CONTENT__}</div>
		<div id="qn"></div>
		<div class="cTop"></div>
		<!-- END CONTENT - FOOTER -->
	</div>
	{__JS__}
</body>
</html>