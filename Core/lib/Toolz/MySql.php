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
final class Toolz_MySql {
    
    private static $oPdo = null;
    
    private static function init() {
        if(is_null(self::$oPdo)) {
            self::$oPdo = SPDO::getInstance();
        }
        return true;
    }
    
    private static function execute($sQuery) {
        self::init();
        $oQuery = self::$oPdo->query($sQuery);
        $oQuery->execute();
        return $oQuery->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /*
     * Size of a specific table
     */
    public static function getTableSize($sTableName) {
        $sQuery = 'select table_name as "Name",
                    sum(data_length+index_length)/1024/1024 as "Size (MB)"
                    from information_schema.tables 
                    where table_name = '.$sTableName;
        return self::execute($sQuery);
    }
    
    /*
     * Size of a specific database
     */
    public static function getDbSize() {
        $sQuery = 'select table_schema as "Name",
                    sum(data_length+index_length)/1024/1024 as "Size (MB)"
                    from information_schema.tables';
        return self::execute($sQuery);
    }
    
    /*
     * table list
     */
    public static function getTableList() {
        $sQuery = 'SHOW TABLES';
        return self::execute($sQuery);
    }
}