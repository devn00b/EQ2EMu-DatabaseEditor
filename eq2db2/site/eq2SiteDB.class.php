<?php
/*  
    EQ2Editor:  Everquest II Database Editor v2.0
    Copyright (C) 2007  EQ2EMulator Development Team (http://www.eq2emulator.net)

    This file is part of EQ2Editor.

    EQ2Editor is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    EQ2Editor is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with EQ2Editor.  If not, see <http://www.gnu.org/licenses/>.
*/
if (!defined('IN_EDITOR'))
{
	die("Hack attempt recorded.");
}

class eq2SiteDB
{

	public function __construct()
	{	
		global $eq2;
		// Transfer instance of eq2db to local db variable
		$this->db = $eq2->eq2db;
	}
	
	
	public function GetNews($type)
	{
		$where = "WHERE s.author = u.id AND "; 
		switch($type)
		{
			case "inactive":
				$where .= "s.type = 'news2' AND s.is_active = 0";
				break;

			case "archive":
				$news_age = ( !empty($GLOBALS['config']['news_age_days']) ) ? $GLOBALS['config']['news_age_days'] : 30;
				$where .= sprintf("s.type = 'news2' AND s.is_active = 1 AND s.created_date < %lu", time() - (86400 * $news_age));
				break;
			
			default:
				$news_age = ( !empty($GLOBALS['config']['news_age_days']) ) ? $GLOBALS['config']['news_age_days'] : 30;
				$where .= sprintf("((s.type = 'news2' AND s.is_active = 1 AND s.created_date >= %lu) OR (s.is_active = 1 AND s.is_sticky = 1))", time() - (86400 * $news_age));
				break;
		}
		
		$sql = sprintf("SELECT s.title, u.username, s.description, s.created_date FROM site_text s, users u %s ORDER BY s.is_sticky, s.created_date DESC;", $where);
		return $this->db->RunQueryMulti($sql);
	}
	

} // end Class
?>