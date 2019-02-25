<?php
/*
 *	minim - PHP framework
    Copyright (C) 2019  Sébastien Boulard

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; see the file COPYING. If not, write to the
    Free Software Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
 */
final class Websiteproperties extends CoreCommon {
	
	public function __construct() {
		parent::__construct();
	}

	public function getArticle() {
		UserRequest::setParams('article_id', str_replace('_bottom', '', UserRequest::getParams('article_id')));
		$sContent = file_get_contents(
						ModulesMgr::getFilePath(__CLASS__, 'data').'articles/'.UserRequest::getParams('article_id').'.html'
					);
		die(Minifier::minifyHtml(
					$this->oTplMgr->buildSimpleCacheTpl(
												$sContent, 
												ModulesMgr::getFilePath('minim', 'locales', $this->oLang->LOCALE).'plugins.xml'
											)
					)
			);
	}
	
	public function getPageArticle() {
		$sPage = substr(UserRequest::getPage(), 0, strpos(UserRequest::getPage(), '_'));
		if(empty($sPage)) {
			$sPage = UserRequest::getPage();
		}
		if(UserRequest::getParams('article_id') === false) {
			UserRequest::setParams(
					'article_id', 
					str_replace($sPage.'_', '', UserRequest::getPage()
				)
			);
		}
		if(UserRequest::getParams('article_id') === 'documentation') {
			UserRequest::setParams('article_id', 'operation');
		}
		if(UserRequest::getParams('article_id') === 'plugins') {
			UserRequest::setParams('article_id', 'contactPlugin');
		}
		$sContent = str_replace(
						'{__ARTICLE-CONTENTS__}', 
						file_get_contents(
							ModulesMgr::getFilePath(__CLASS__, 'data').'articles/'.UserRequest::getParams('article_id').'.html'
						),
						file_get_contents(
							CONTENT_TPL_PATH.$sPage.'.tpl'
						)
					);
		return array(
			'content' => $this->oTplMgr->buildSimpleCacheTpl(
												$sContent, 
												ModulesMgr::getFilePath('minim', 'locales', $this->oLang->LOCALE).$sPage.'.xml'
											),
			'sPage'	=> $sPage.'_'.UserRequest::getParams('article_id')
		);
	}
}