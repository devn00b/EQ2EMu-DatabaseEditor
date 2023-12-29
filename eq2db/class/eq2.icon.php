<?php
class EQ2Icons
{
    var $IconTypes = array(
       "aa",
       "ho",
       "item",
       "macro", 
       "map",
       "overseer",
       "spell",
       "transp"
    );

    var $Icons = array(
        "aa" => array(
            0 => array(
                'name' => '',
                'src'=> '',
                'posX'=> '',
                'posY'=> ''
            )
        ),
        "ho"=> array(

        ),
        "item" => array(

        ),
        "macro" => array(

        ),
        "map" => array(

        ),
        "overseer" => array(

        ),
        "spell" => array(

        ),
        "transp" => array(

        )
    );

    public function __construct() 
	{
		$this->Page = sprintf("page=%s", $_GET['page']);
		$this->Link = sprintf("%s?%s",$_SERVER['SCRIPT_NAME'], $this->Page);
	}

    private function PopulateIcons($type)
    {
        global $eq2;

        switch($type)
        {
            case "aa":
                //do stuff;
			    break;
            case "ho":
                $imgroot = '../images/icons/ho/';
                $query = "SELECT * FROM icons WHERE type = 'ho';";
                $data = $eq2->RunQueryMulti($query);
                foreach($data as $row)
                {
                    $this->Icons[$type][$row['icon_id']]['name'] = $row['name'];
                    $this->Icons[$type][$row['icon_id']]['src'] = $imgroot . $row['src'];
                    $this->Icons[$type][$row['icon_id']]['posX'] = $row['posX'];
                    $this->Icons[$type][$row['icon_id']]['posY'] = $row['posY'];
                }
                break;
            case "item":
                //do stuff;
                break;
            case "macro":
                //do stuff;
                break;
            case "map":
                //do stuff;
                break;
            case "overseer":
                //do stuff;
                break;
            case "spells":
                $imgroot = '../images/icons/spell/';
                $query = "SELECT * FROM icons WHERE type = 'spells';";
                $data = $eq2->RunQueryMulti($query);
                foreach($data as $row)
                {
                    $this->Icons[$type][$row['icon_id']]['name'] = $row['name'];
                    $this->Icons[$type][$row['icon_id']]['src'] = $imgroot . $row['src'];
                    $this->Icons[$type][$row['icon_id']]['posX'] = $row['posX'];
                    $this->Icons[$type][$row['icon_id']]['posY'] = $row['posY'];
                }
                break;
            case "transp":
                //do stuff;
                break;                                            
        }
    }

    public function GetIcons($type){
        $this->PopulateIcons($type);
        //var_dump($this->Icons[$type]);
        return($this->Icons[$type]);
    }

    //print("[->" . $this->GetIcons('ho') . "<-]");
}