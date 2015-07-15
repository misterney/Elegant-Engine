<?
class Model_Main extends Model {	

	function __construct(){
		parent::__construct();
		$this->libraries->load('session');
		$this->libraries->load('html');
	}
	public function getData()
	{
		$this->data["html"]["title"] = "ElegantCMS";
		$this->data["html"]["items"] = $this->getListItems("1");
		$this->data["html"]["tablePrepayment"] = $this->getTablePrepayment();
		$this->data["html"]["itemsList"] = $this->getListItemsBought();

		return $this->data;
	}
	public function getListItems($type = "")
	{
		switch($type){
		case "1":
			return $this->database->fetch_array_all(
			"SELECT * FROM `el_shop_items` WHERE `improve` = 1"
			);
		case "2":
			return $this->database->fetch_array_all(
			"SELECT * FROM `el_shop_items` WHERE `item` = 1"
			);
		case "3":
			return $this->database->fetch_array_all(
			"SELECT * FROM `el_shop_items` WHERE `service` = 1"
			);
		default:
			return $items = $this->database->fetch_array_all(
			"SELECT * FROM `el_shop_items`"
			);
		}
	}
	public function getTablePrepayment(){
		$items = $this->getListItems("1");
		for($i=0; $i <= count($items) -1; $i++){
			$preItem["prepayment"] = "";
			$preItem["price"]  = $items[$i]["price"];
			$preItem["name"] = $items[$i]["name"];
			$preItems[] = $preItem;
		}
		$items = $preItems;

		for($i=0; $i <= count($items) - 1; $i++){
			if($i == 0){
				$items[$i]["prepayment"] = "false";
			} else {
				for($ii=0; $ii <= $i; $ii++){
					if($ii !== $i){
						if($items[$ii]["price"] <= $items[$i]["price"]){
							$price = $items[$i]["price"] - $items[$ii]["price"];
							$items[$i]["prepayment"] .= "<tr><td>".
											$items[$ii]["name"].
											"</td><td>".
											$price.
											"<i class='ruble icon'></i></td></tr>";
						}
					}
				}
			}
		}
		return $items;
	}
	public function getListItemsBought(){
		$items = $this->getListItems();

		$count["improve"] = 0;
		$count["item"]    = 0;
		$count["service"] = 0;	

		foreach($items as $keys=>$key){
			if($key["improve"] == "1"){

				$item["id"] = $key["id"];
				$item["name"] = $key["name"];
				$item["price"] = $key["price"];
				
				$postItems["improve"][] = $item;

				$count["improve"]++;
			}

			if($key["item"] == "1"){

				$item["id"] = $key["id"];
				$item["name"] = $key["name"];
				$item["price"] = $key["price"];
			
				$postItems["item"][] = $item;

				$count["item"]++;
			}

			if($key["service"] == "1"){

				$item["id"] = $key["id"];
				$item["name"] = $key["name"];
				$item["price"] = $key["price"];
				
				$postItems["service"][] = $item;

				$count["service"]++;

			}

		}
		if($count["improve"] == 0) $postItems["improve"] = 0;
		if($count["item"] == 0)    $postItems["item"] = 0;
		if($count["service"] == 0) $postItems["service"] = 0;


		return $postItems;
	}
}