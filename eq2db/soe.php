<?php

class Spells
{
	var $Name;
	
	function Spells()
	{
		echo "Hi!";
	}
}

class SpellMasterList
{
	function SpellMasterList()
	{
		$spell_list = new Spells();
		$spell_list->Name = "Test";
	}
}

$spell_list = new SpellMasterList();

print_r($spell_list);

exit;
// First, verify the data still exists on census.daybreakgames.com
$DataURL = sprintf("http://census.daybreakgames.com/json/get/eq2/spell/?crc=%s&c:limit=20&c:sort=tier", $spell_id);
//printf("DataURL: %s<br />", $DataURL);

//$this->PrintDebugTimer("census.daybreakgames.com", 1, $spell_id);
$spell_array = json_decode($this->file_get_contents_curl($DataURL), true);
//$this->PrintDebugTimer("census.daybreakgames.com", 0);




/*include('config.php');

$sql = 'SELECT id, soe_item_id, name, item_type FROM '.LIVE_DB.'.items WHERE name NOT IN (SELECT name FROM '.DEV_DB.'.items) AND item_type <> "Scroll"';
if($result = $eq2->db->sql_query($sql))
{
	$i = 0;
	print('<table border="1">');
	while($row = $eq2->db->sql_fetchrow($result))
	{
		print('<tr>');
		printf('<td>%s</td>', $row['name']);
		printf('<td width="70"><a href="http://census.daybreakgames.com/xml/get/eq2/item/?displayname=%s" target="_blank">SOE</a></td>', $row['name']);
		printf('<td width="70"><a href="http://eq2.zam.com/db/itemlist.html?name=%s" target="_blank">ZAM</a></td>', $row['name']);
		printf('<td width="70"><a href="http://eq2.wikia.com/wiki/index.php?search=%s&fulltext=Search" target="_blank">Wikia</a></td>', $row['name']);
		printf('<td width="70"><a href="http://www.lootdb.com/eq2/item/%s" target="_blank">LootDB</a></td>', $row['soe_item_id']);
		printf('<td width="150" align="right">%s</td>', $row['soe_item_id']);
		print('</tr>');
		$i++;
	}
	printf("<tr><td colspan='5'>Total Records: %s</td></tr></table>", $i);
}
*/


function file_get_contents_curl($url,$json=false)
{
		$ch = curl_init();
		$headers = array();
		if($json) {
				$headers[] = 'Content-type: application/json';
				$headers[] = 'X-HTTP-Method-Override: GET';
		}
		$options = array(
				CURLOPT_URL => $url,
				CURLOPT_HTTPHEADER => array($headers),
				CURLOPT_TIMEOUT => 5,
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_HEADER => 0,
				CURLOPT_FOLLOWLOCATION => 1,
				CURLOPT_MAXREDIRS => 3,
				CURLOPT_USERAGENT => 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0)'
		);
		curl_setopt_array($ch,$options);
		$response = curl_exec($ch);
		if($response === false || curl_error($ch)) {
				curl_close($ch);
				return false;
		} else {
				curl_close($ch);
				return $response;
		}
}

?>