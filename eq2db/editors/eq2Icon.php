<?php
	if (isset($_GET['id']))
	{
		$id = $_GET['id'];
		$icon_width = 42;
		$icon_height = 42;

		$page = (int)($id / 36) + 1;
		$type = $_GET['type'] ?? "";
		$image_path = "../images/icons/".$type."/icon_";

		$offset = $id % 36;
		$row = (int)($offset / 6);
		$column = $offset % 6;

		switch ($type) {
			case "aa": $image_path .= "as"; break;
			case "ho": $image_path .= "os"; break;
			case "item": $image_path .= "is"; break;
			case "spell": $image_path .= "ss"; break;
			case "map": $image_path .= "map"; break;
			case "overseer": $image_path .= "mnpor"; break;
			case "macro": $image_path .= "ms"; break;
			case "transp": $image_path .="transp"; break;
		}

		$image_path .= $page.".png";
	
		$width_offset = $column * $icon_width;
		$height_offset = $row * $icon_height;

		$img = null;
		$img = @imagecreatefromstring(file_get_contents($image_path));
		if ($img) {
			$bg = GetBackgroundImage($type);
			//Copy the icon onto our background
			imagecopyresampled($bg, $img, 0, 0, $width_offset, $height_offset, $icon_width, $icon_height, $icon_width, $icon_height);
			imagedestroy($img);
			$img = $bg;
			if (!isset($_GET["iconOnly"])) {
				AddImageOverlay($type, $img);
			}
		}
		else if ($type == "ho" && $id == 65535) {
		//65535 for heroic opporunities (short -1) is no icon, return a transparent to keep our views consistent
			$img = CreateTransparentImage($icon_width, $icon_height);
		}

		# Display the image
		header("Content-type: image/png");
		imagepng($img);
		imagedestroy($img);
	}

	function GetItemTieredBackdrop() {
		$tier = $_GET['tier'] ?? 0;

		//if ($tier >= 12) {
			//celestial
			//$rarity = "uncommon";
		//}
		if ($tier >= 11) {
			$rarity = "mythical";
		}
		else if ($tier >= 9) {
			$rarity = "fabled";
		}
		else if ($tier >= 7) {
			$rarity = "legendary";
		}
		else if (isset($_GET['crafted'])) {
			if ($tier >= 5) {
				//Mastercrafted
				$rarity = "legendary";
			}
			//Handcrafted
			else $rarity = "uncommon";
		}
		else if ($tier >= 4) {
			$rarity = "treasured";
		}
		else if ($tier >= 3) {
			$rarity = "uncommon";
		}
		else $rarity = "common";

		return @imagecreatefromstring(file_get_contents("../images/icons/item/item_".$rarity.".png"));
	}

	function CreateTransparentImage($w, $h) {
		$ret = imagecreatetruecolor($w, $h);
		imagesavealpha($ret, true);
		$alpha = imagecolorallocatealpha($ret, 0, 0, 0, 127);
		imagefill($ret, 0, 0, $alpha);
		imagecolordeallocate($ret, $alpha);
		return $ret;
	}

	function GetBackgroundImage($type) {
		if ($type == "item" && !isset($_GET['iconOnly'])) {
			//bg is the colored haze
			$bg = GetItemTieredBackdrop();
			return $bg;
		}
		else if ($type == "spell") {
			$backdrop = $_GET['backdrop'];
			$o = $backdrop % 36;
			$r = intval($o / 6);
			$c = intval($o % 6);
			$p = intval($backdrop / 36) + 1;
			$isheet = @imagecreatefromstring(file_get_contents("../images/icons/spell/icon_ss".$p.".png"));
			$bg = CreateTransparentImage(42, 42);
			if ($backdrop != 65535) {
				imagecopyresampled($bg, $isheet, 0, 0, $c * 42, $r * 42, 42, 42, 42, 42);
			}
			if ($isheet) imagedestroy($isheet);
			return $bg;
		}

		return CreateTransparentImage(42, 42);
	}

	function AddImageOverlay($type, $img) {
		if ($type == "item") {
			//transp is the icon sheet with the border we want
			$transp = @imagecreatefromstring(file_get_contents("../images/icons/transp/icon_transp1.png"));
			//Copy the border onto our item icon
			imagecopyresampled($img, $transp, 0, 0, 42, 0, 42, 42, 42, 42);
			imagedestroy($transp);
		}	
	}
?>