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
final class Installer {
	
	
	/*
	INSTALL
	
	1- Décompression du module
	2- Mise en place des fichiers (via XML)
	3- Ajout des routes statiques
	4- Ajouts dans la conf des JS et CSS
	5- Si présent exécution du script d'initialisation (création de tables par ex...)
	6- Ajout du module à la liste des modules disponibles (ModulesMgr::setModulesAvailable)
	7- Ajout de l'entrée dans le menu du backoffice
	
	UNINSTALL
	
	1- Suppression du menu du FRONT
	2- Suppression du menu du backoffice
	3- Suppression des routes statiques
	4- Suppression de la conf des JS et CSS
	5- Suppression des fichiers 
	6- Si présent exécution du script de désinstallation (suppression de tables par ex...)
	7- Suppression du répertoire dans les modules
	8- Suppression du module de la liste des modules disponibles (ModulesMgr::setModulesAvailable)
	*/
}