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

<?php
define('IN_EDITOR', true);
include_once("common/header_short.php");

if (isset($_GET['type']))
{
	switch ($_GET['type'])
	{
		case "item_search":
			include_once("popups/eq2ItemSearch.class.php");
			$Search = new eq2ItemSearch;
			$Search->Start();
			break;
	}
}

include_once("common/footer_short.php");
?>