<?php 
abstract class PagesConf {
	protected static $aPagesConf = array (
  'default' => 
  array (
    'robots' => 'index,follow',
    'meta' => 
    array (
      'keywords' => 'website framework',
    ),
    'og' => 
    array (
      'type' => 'website',
      'title' => 'minim',
      'url' => 'https://minim.webearthquake.com',
      'description' => 'website framework',
      'site_name' => 'minim',
    ),
    'twitter' => 
    array (
      'twitter:card' => 'summary_large_image',
    ),
    'google' => 
    array (
    ),
    'view' => 'default_view',
  ),
);
	protected static $aAdminPagesConf = array(
										'default' => array('robots' => 'noindex,nofollow')
									);
}