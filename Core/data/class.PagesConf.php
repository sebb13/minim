<?php 
abstract class PagesConf {
	protected static $aPagesConf = array (
  'default' => 
  array (
    'robots' => 'index,follow',
    'meta' => 
    array (
      'description' => 'minim est un framework PHP léger, évolutif et open source !',
      'keywords' => 'website framework',
      'author' => 'Sébastien Boulard',
    ),
    'og' => 
    array (
      'type' => 'website',
      'title' => 'minim',
      'url' => 'https://minim.webearthquake.com',
      'image' => 'https://minim.webearthquake.com/img/design/minim.png',
      'description' => 'website framework',
      'locale' => 'FR',
      'site_name' => 'minim',
    ),
    'twitter' => 
    array (
      'twitter:card' => 'summary_large_image',
    ),
    'google' => 
    array (
      'name' => 'minim',
      'description' => 'minim est un framework PHP léger, évolutif et open source !',
      'image' => 'https://minim.webearthquake.com/img/design/minim.png',
    ),
    'view' => 'default_view',
  ),
);
	protected static $aAdminPagesConf = array(
										'default' => array('robots' => 'noindex,nofollow')
									);
}