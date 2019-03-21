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
final class Toolz_Dates {
    
	/*
	 * Returns the ISO-8601 numeric representation of the day of the week.
	 * $sDate format : 2019-08-17
	 */
    public static function getNumDay($sDate) {
		return date('N', strtotime($sDate));
	}
	
	/*
	 * Returns the week number in the ISO-8601 year, the weeks start on Monday.
	 * $sDate format : 2019-08-17
	 */
    public static function getNumWeek($sDate) {
		return date('W', strtotime($sDate));
	}
	
	/*
	 * Return the number of days in the month
	 * $sDate format : 2019-08-17
	 */
    public static function getNumberOfDays($sDate) {
		return date('t', strtotime($sDate));
	}
	
	/*
	 * Return holidays list
	 * $iYear format : 2019
	 */
	public static function getHolidays($iYear=null) {
		if ($iYear === null){
			$iYear = intval(strftime('%Y'));
		}
        $iEasterDate = easter_date($iYear);
        $sEasterDay = date('j', $iEasterDate);
        $sEasterMonth = date('n', $iEasterDate);
        $sEasterYear = date('Y', $iEasterDate);
        $aHolidays = array(
			// Jours feries fixes
			mktime(0, 0, 0, 1, 1, $iYear),// 1er janvier
			mktime(0, 0, 0, 5, 1, $iYear),// Fete du travail
			mktime(0, 0, 0, 5, 8, $iYear),// Victoire des allies
			mktime(0, 0, 0, 7, 14, $iYear),// Fete nationale
			mktime(0, 0, 0, 8, 15, $iYear),// Assomption
			mktime(0, 0, 0, 11, 1, $iYear),// Toussaint
			mktime(0, 0, 0, 11, 11, $iYear),// Armistice
			mktime(0, 0, 0, 12, 25, $iYear),// Noel
			// Jour feries qui dependent de paques
			mktime(0, 0, 0, $sEasterMonth, $sEasterDay + 1, $sEasterYear),// Lundi de paques
			mktime(0, 0, 0, $sEasterMonth, $sEasterDay + 39, $sEasterYear),// Ascension
			mktime(0, 0, 0, $sEasterMonth, $sEasterDay + 50, $sEasterYear), // Pentecote
        );
        sort($aHolidays);
        return $aHolidays;
	}
	
	/*
	 * Return the number of days between two dates
	 * $sFirstDate format : 2019-10-04
	 * $sSecondDate format : 2019-10-14
	 */
	public static function diffCalendar($sFirstDate, $sSecondDate) {
        $oFirstDate = new DateTime($sFirstDate);
        $oSecondDate = new DateTime($sSecondDate);
        return (int)($oSecondDate->getTimestamp() - $oFirstDate->getTimestamp()) / 86400;
	}

	/*
	 * Return the number of working days between two dates
	 * $sFirstDate format : 2019-10-04
	 * $sSecondDate format : 2019-10-14
	 */
	public static function diffWorkDay($sFirstDate, $sSecondDate) {
        /*
         * Pour calculer le nombre de jours ouvres, on calcule le nombre total de jours 
		 * et on soustrait les jours fériés et les week end.
         */
        $iDiffCalendar = self::diffCalendar($sFirstDate, $sSecondDate) * 86400;
        $oFirstDate = new DateTime($sFirstDate);
        $oSecondDate = new DateTime($sSecondDate);
        $iFirstYear = $oFirstDate->format('Y');
        $iSecondYear = $oSecondDate->format('Y');
        $aHolidays = array();
        /*
         * Si l'interval demande chevauche plusieurs annees
         * on doit avoir les jours feries de toutes ces annees
         */
        for($iYear = $iFirstYear; $iYear <= $iSecondYear; $iYear++){
			$aHolidays = array_merge(self::getHolidays($iYear), $aHolidays);
        }
        /*
         * On est oblige de convertir les timestamps en string a cause des decalages horaires.
         */
        $aHolidaysString = array_map(function($iValue){
            return strftime('%Y-%m-%d', $iValue);
        }, $aHolidays);
        for ($i = $oFirstDate->getTimestamp(); $i < $oSecondDate->getTimestamp(); $i += 86400) {
			/* Numero du jour de la semaine, de 1 pour lundi a 7 pour dimanche */
			$iDayNum = (int)strftime('%u', $i);
			if (in_array(strftime('%Y-%m-%d', $i), $aHolidaysString) || $iDayNum === 6 || $iDayNum === 7) {
				/* Si c'est ferie ou samedi ou dimanche, 
				 * on soustrait le nombre de secondes dans une journee. */
				$iDiffCalendar -= 86400;
			}
        }
        return (int)$iDiffCalendar / 86400;
	}
	
	/*
	 * Test if a date is a holiday or a week-end
	 */
	public static function isHoliday($iTimestamp) {
        $iDayNum = (int)strftime('%u', $iTimestamp);
        $iYear = strftime('%Y', $iTimestamp);
        $aHolidays = self::getHolidays($iYear);
        /* On est oblige de convertir les timestamps en string a cause des decalages horaires.*/
        $aHolidaysString = array_map(function($iValue){
            return strftime('%Y-%m-%d', $iValue);
        }, $aHolidays);
        if (in_array(strftime('%Y-%m-%d', $iTimestamp), $aHolidaysString) || $iDayNum === 6 || $iDayNum === 7) {
			return true;
        }
        return false;
	}
	
	/*
	 * Test if a date is a working day
	 */
	public static function isWorkingDay($iTimestamp) {
        return !self::isHoliday($iTimestamp);
	}
}