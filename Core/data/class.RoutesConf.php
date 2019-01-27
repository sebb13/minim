<?php 
class RoutesConf {
	public static $aRoutesFrontConf = array (
  'contact' => 'Contact::getContactPage',
  'documentation_operation' => 'Websiteproperties::getPageArticle',
  'documentation_configurations' => 'Websiteproperties::getPageArticle',
  'documentation_tools' => 'Websiteproperties::getPageArticle',
  'documentation_backend' => 'Websiteproperties::getPageArticle',
  'documentation_routing' => 'Websiteproperties::getPageArticle',
  'documentation_modular' => 'Websiteproperties::getPageArticle',
);
	public static $aRoutesBackConf = array (
  'system_summary' => 'Core::getSystemPage',
  'system_conf' => 'Configuration::getGlobalConfPage',
  'system_resetCache' => 'Cache::resetCache',
  'system_logs' => 'Logs::getDaysLogs',
  'system_sessionGC' => 'Core::sessionGC',
  'pages_configuration' => 'Pages::getPageConfig',
  'login' => 'Core::login',
  'translations_front' => 'Translations::getTranslationsInterface',
  'translations_back' => 'Translations::getTranslationsInterface',
  'system_routing' => 'Routing::getRoutingInterface',
  'translations_common' => 'Translations::getTranslationsInterface',
  'pages_versions' => 'Versions::getPagesVersionsInterface',
  'system_errorLogs' => 'Logs::getErrorLogs',
  'system_updates' => 'Updates::getHomePage',
  'home' => 'Core::getHomePage',
  'minim' => 'Core::getMinimPage',
  'system_user' => 'User::getUserManager',
  'user' => 'User::getUserPage',
  'pages_sitemap' => 'Pages::getSitemapPage',
  'contact_home' => 'Contact::getHomePage',
  'contact_messagesReceived' => 'Contact::getMsgsPage',
  'contact_archivedMessages' => 'Contact::getArchivesPage',
);
}