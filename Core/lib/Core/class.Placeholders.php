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
final class Placeholders {
	
	private $oView = NULL;
	private $oAssetsLinker = NULL;
	
	public function __construct(View $oView) {
		$this->oView = $oView;
		$this->oAssetsLinker = new AssetsLinker();
	}
	
	public function getLang() {
		return SessionLang::getLang();
	}
	
	public function getMetaTags() {
		return MetasTags::getMetaTags($this->oView->aPageConfig);
	}
	
	public function getCss() {
		return $this->oAssetsLinker->getCSS();
	}
	
	public function getDev_Banner() {
		return Toolz_Main::getDevBanner();
	}
	
	public function getLogin_Value() {
		return SessionUser::get('user') !== false ? SessionUser::get('user') : '-';
	}
	
	public function getFlags() {
		return LangSwitcher::getFlags(UserRequest::getRequest(), SessionCore::getLangObject());
	}
	
	public function getMenu() {
		return $this->oView->getContent('menu');
	}
	
	public function getContent() {
		return $this->oView->getContent();
	}
	
	public function getSocial_networks() {
		return SocialNetwork::getAll('html');
	}
	
	public function getJs() {
		return $this->oAssetsLinker->getJS().SocialNetwork::getAll('js');
	}
	
	public function getCookies_Consent() {
		return CookiesConsent::getCookiesConsentBanner();
	}
	
	public function getMinim_Version() {
		return trim(file_get_contents(DATA_PATH.'minim.version'));
	}
}