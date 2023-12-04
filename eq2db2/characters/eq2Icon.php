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
	if (isset($_GET['id']))
	{
		$id = $_GET['id'];
		$icon_width = 42;
		$icon_height = 42;

		$page = (int)($id / 36) + 1;
		if (isset($_GET['type']) && $_GET['type'] == "spells")
			$image_path = "../images/icons/icon_ss" . $page . ".png";
		else
			$image_path = "../images/icons/icon_is" . $page . ".png";
	
		$offset = $id % 36;
		$row = (int)($offset / 6);
		$column = $offset % 6;

	
		$width_offset = $column * $icon_width;
		$height_offset = $row * $icon_height;

		$img = null;
		$img = @imagecreatefromstring(file_get_contents($image_path));
		if ($img) {
			$tmp_img = imagecreatetruecolor($icon_width, $icon_height);
			imagecopyresampled($tmp_img, $img, 0, 0, $width_offset, $height_offset, $icon_width, $icon_height, $icon_width, $icon_height);
			imagedestroy($img);
			$img = $tmp_img;
		}

		# Display the image
		header("Content-type: image/png");
		imagepng($img);
		imagedestroy($img);
	}
?>