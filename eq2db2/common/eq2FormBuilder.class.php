<?php
/*  
    EQ2Editor:  Everquest II Database Editor v2.0
    Copyright (C) 2007 EQ2EMulator Development Team (http://www.eq2emulator.net or https://www.eq2emu.com)

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
	die();


class eq2FormBuilder
{
	private $OpenContainer = '<div id="%s">';
	private $CloseContainer = '</div>';
	public $FormBody;
	
	public function __construct()
	{
		
	}
	
	public function NewContainer($css)
	{
		printf($this->OpenContainer, $css);
		print($this->CloseContainer);
	}
	
	public function NewTable($prop)
	{
		printf('<div id="Table" class="%s">Hi</div>', $prop['div']);
	}
	
}

?>