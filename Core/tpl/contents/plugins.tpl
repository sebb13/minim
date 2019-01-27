<!-- container -->
<div class="container">
	<ol class="breadcrumb">
		<li><a href="home.html">Accueil</a></li>
		<li class="active">Les modules</li>
	</ol>
	<div class="row">
		<!-- Article main content -->
		<article class="col-sm-12 maincontent">
			<header class="page-header">
				<h1 class="page-title">Les modules <i class="fa fa-puzzle-piece"></i></h1>
				<h3>Étendez les possibilités !</h3>
			</header>
		</article>
		<!-- /Article -->
	</div>
	<h2>Modules fonctionnels</h2>
	<div class="row">
		<article class="col-sm-8 sidebar sidebar-left">
			<h3>Contact</h3>
			<p>Le module "contact" permet de gérer les messages déposés sur votre application.</p>
			<p>La configuration est assez simple : </p>
			<ul class="cog-tools">
				<li>Le type de support pour les données (SQL ou XML), mais attention ! Si vous choisissez le mode SQL, la base de données doit être bien configurée et la table créée, sous peine de voir planter le module.</li>
				<li>L'adresse email où doivent être envoyées les notifications de nouveaux messages.</li>
			</ul>
		</article>
		<aside class="col-sm-4 sidebar sidebar-right">
			<div class="widget">
				<p class="txt-center">
					<a href="/img/contents/doc/contactHome.png" rel="cbox" title="accueil et configuration du module contact" class="cbox">
						<img src="/img/contents/doc/contactHome.png" alt="accueil et configuration du module contact" class="img-rounded" />
					</a>
				</p>
			</div>
		</aside>
		<article class="col-sm-8 sidebar sidebar-left">
			<h4>SQL</h4>
			<p>Par défaut, ce module stock ses données dans un fichier XML.</p>
			<p>Si votre site/application nécessite déjà une base de données, vous vous l’utiliser pour stocker les informations de ce module.</p>
		</article>
		<aside class="col-sm-4 sidebar sidebar-right">
			<div class="widget text-center">
				<img src="/img/contents/doc/logo-langage-MySQL-v2.png" alt="MySQL" class="img-rounded" />
			</div>
		</aside>
		<article class="col-sm-8 sidebar sidebar-left language-sql">
			<p>Comme on est sympa, voici la requête SQL pour créer la table.</p>
			<code>CREATE TABLE `t_contact` (</code><br />
			<code>`contact_id` int(11) NOT NULL,</code><br />
			<code>`contact_name` varchar(256) NOT NULL,</code><br />
			<code>`contact_email` varchar(256) NOT NULL,</code><br />
			<code>`contact_subject` varchar(256) DEFAULT NULL,</code><br />
			<code>`contact_msg` text NOT NULL,</code><br />
			<code>`contact_comment` varchar(512) DEFAULT NULL,</code><br />
			<code>`contact_comment_user` varchar(64) DEFAULT NULL,</code><br />
			<code>`contact_file` varchar(256) DEFAULT NULL,</code><br />
			<code>`contact_ip` varchar(64) NOT NULL,</code><br />
			<code>`contact_active` tinyint(1) NOT NULL DEFAULT '1',</code><br />
			<code>`contact_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP</code><br />
			<code>) ENGINE=InnoDB DEFAULT CHARSET=utf8;</code>
		</article>
		<aside class="col-sm-4 sidebar sidebar-right">
			<div class="widget text-center">
				<a href="/img/contents/doc/phpMyAdmin.png" rel="cbox" title="Vue dans phpMyAdmin" class="cbox">
					<img src="/img/contents/doc/phpMyAdmin.png" alt="Vue dans phpMyAdmin" class="img-rounded" />
				</a>
			</div>
		</aside>
		<article class="col-sm-8 sidebar sidebar-left">
			<p>Le module "contact" offre un aperçu rapide sur le tableau de bord du backoffice.</p>
			<p>Vous pouvez consulter rapidement les derniers messages et y accéder.</p>
		<!-- /Article -->
		</article>
		<aside class="col-sm-4 sidebar sidebar-right">
			<div class="widget">
				<p class="txt-center">
					<img src="/img/contents/doc/contactDashboard.png" alt="tableau de bord du module contact" class="img-rounded" />
				</p>
			</div>
		</aside>
		<article class="col-sm-8 sidebar sidebar-left">
			<p>La partie front propose aussi la possibilité de joindre un fichier, ce qui s'avère souvent pertinant.</p>
			<p>Une validation des données javascript et PHP est déjà en place.</p>
			<p>Une barre de progression apparaît le temps de l'upload si le formulaire a été validé : javascript d'abord, puis PHP et enfin vérification que ce n'est pas un robot (recette délivrée quand vous aurez compris les sources).</p>
		<!-- /Article -->
		</article>
		<aside class="col-sm-4 sidebar sidebar-right">
			<div class="widget">
				<p class="txt-center">
					<a href="/img/contents/doc/contactFront.png" rel="cbox" title="Partie front" class="cbox">
						<img src="/img/contents/doc/contactFront.png" alt="Partie front" class="img-rounded" />
					</a>
				</p>
			</div>
		</aside>
		<article class="col-sm-8 sidebar sidebar-left">
			<p>La partie back vous permet de consulter les messages, d'en archiver, de télécharger les pièces jointes, répondre...</p>
			<p>Vous pouvez également ajouter une note, utile dans la cadre d'un travail en équipe.</p>
			<p>Enfin la page des messages archivés vous permet de les supprimer définitivement.</p>
		<!-- /Article -->
		</article>
		<aside class="col-sm-4 sidebar sidebar-right">
			<div class="widget">
				<p class="txt-center">
					<a href="/img/contents/doc/messagesReveived.png" rel="cbox" title="artie back" class="cbox">
						<img src="/img/contents/doc/messagesReveived.png" alt="Partie back" class="img-rounded" />
					</a>
				</p>
			</div>
		</aside>
	</div>
	<hr />
	<div class="row">
		<article class="col-sm-12 maincontent">
			<h3>D'autres modules vont arriver très bientot !</h3>
		</article>
		<!-- /Article -->
	</div>
	<hr />
	<div class="row">
		<article class="col-sm-12 maincontent">
			<h3>Soumettez vos modules ou vos thèmes et nous les regrouperons ici dans un catalogue !</h3>
			<p>Tiens, un module catalogue, en voilà une bonne idée !</p>
		</article>
		<!-- /Article -->
	</div>
</div>
<!-- /container -->