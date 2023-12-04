<?php 
/*  
    EQ2Editor:  Everquest II Database Editor v1.0
    Copyright (C) 2008-2013  EQ2Emulator Development Team (http://eq2emulator.net)

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
define('IN_EDITOR', true);

include("header.php");

if ( !$eq2->CheckAccess(M_HOME) )
	die("ACCESS: Denied!");

// include the news.php page, or don't if you don't need it.
include_once("news.php");

include("footer.php");