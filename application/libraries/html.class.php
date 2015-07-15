<?
class HTML {
    public static function LoadJS($fileName){
        if(file_exists(ENGINE_PATH."assets/js/{$fileName}.js")){
            $data = '<script src="' . ENGINE_URL .'/assets/js/' . $fileName . '.js"></script>';
            echo $data;
        } else {
            echo 'JS файл не найден, путь: assets/js/' . $fileName . '.js<br>';
        }
    }
    public static function LoadCSS($fileName){
        if(file_exists(ENGINE_PATH."assets/css/{$fileName}.css")){
            $data = '<link href="' . ENGINE_URL .'/assets/css/'.$fileName.'.css" rel="stylesheet">';
            echo $data;
        } else {
            echo 'CSS Файл не найден, путь: assets/css/'. $fileName.'.css<br>';
        }
    }
    public static function Encode($charset) {
        echo '<meta charset="'.$charset.'" />';
    }

    public static function Meta($title, $charset, $author, $copyright){
	echo $title   = "<title>{$title}</title>";
	echo $charset = "<meta charset='{$charset}'>";
	echo $author  = "<meta name='author' content='{$author}'>";
	echo $copyright = "<meta name='copyright' content='{$copyright}'>";
    }


    public static function semanticMenu($data){
        $standartMenu = "<div class='ui large {style} secondary menu' id='menu'>{items}</div>";
        $SMenuWithRightItems = "<div class='ui large {style} secondary menu' id='menu'> {items} {rightItems}</div>";
        $simpleItem 	= "<a class='{style} {type} item' {href}> {icon} {content} {label}</a>";
        $advanceItem 	= "<div class='{type} item'> {title} {content} </div>";
        $subItem	= "<a class='{type} item' {href}> {icon} {content} </a>";
        $simpleLabel  = "<div class='ui {style} label'>{content}</div>";

        $simpleRightMenu    = "<div class='right menu'> {items} </div>";

        $menuStyle	= $data["style"];
        $itemsResult = "";
        $rightItemsResult = "";

        foreach ($data["items"] as $item => $content){
            $titleItem = $item;

            if(!empty($content["type"]))
                $typeItem = $content["type"]; else $typeItem = "";

            if(!empty($content["style"]))
                $styleItem = $content["style"];	else $styleItem = "";

            if(!empty($content["icon"]))
                $iconItem = "<i class='".$content["icon"]."'></i>";	else $iconItem  = "";

            if(empty($content["pos"])) $content["pos"] = "left";

            if($content["pos"] == "right" OR $content["pos"] == "left")
                $posItem = $content["pos"]; 	else $posItem   = "left";

            if(!empty($content["href"]))
                $hrefItem = "href = '".$content["href"]."'";	else $hrefItem = "";

            if(!empty($content["menu"])){
                $subItemsResult = "";
                foreach ($content["menu"] as $subItemName => $subContent){
                    if(!empty($subContent["type"]))
                        $subItemType = $subContent["type"]; else $subItemType = "";

                    if(!empty($subContent["href"]))
                        $subItemHref = "href = '".$subContent["href"]."'"; else $subItemHref = "";

                    if(!empty($subContent["icon"]))
                        $subItemIcon = "<i class='".$subContent["icon"]."'></i>"; else $subItemIcon = "";

                    $subItemsResult .= str_replace(
                        ["{type}", "{href}", "{icon}", "{content}"],
                        [$subItemType, $subItemHref, $subItemIcon, $subItemName],
                        $subItem
                    );
                }
            }

            if(!empty($content["label"])){
                if(!empty($content["label"]["style"]))
                    $labelStyle = $content["label"]["style"]; else $labelStyle = "";

                if(!empty($content["label"]["content"]))
                    $labelContent = $content["label"]["content"]; else $labelContent = "0";

                $labelResult = str_replace(
                    ["{style}", "{content}"],
                    [$labelStyle, $labelContent],
                    $simpleLabel
                );
            } else	$labelResult = "";

            if(!empty($subItemsResult)){
                if($posItem == "right"){
                    $rightItemsResult .= str_replace(
                        ["{type}", "{icon}", "{title}", "{content}"],
                        [$typeItem, $iconItem, $titleItem, $subItemsResult],
                        $advanceItem
                    );
                } else {
                    $itemsResult .= str_replace(
                        ["{type}", "{icon}", "{title}", "{content}"],
                        [$typeItem, $iconItem, $titleItem, $subItemsResult],
                        $advanceItem
                    );
                }
            } else {
                if($posItem == "right") {
                    $rightItemsResult .= str_replace(
                        ["{type}", "{style}", "{href}", "{icon}", "{content}", "{label}"],
                        [$typeItem, $styleItem, $hrefItem, $iconItem, $titleItem, $labelResult],
                        $simpleItem
                    );
                } else {
                    $itemsResult .= str_replace(
                        ["{type}", "{style}", "{href}", "{icon}", "{content}", "{label}"],
                        [$typeItem, $styleItem, $hrefItem, $iconItem, $titleItem, $labelResult],
                        $simpleItem
                    );
                }
            }

        }
        if(!empty($rightItemsResult)){
            $rightItemsResult = str_replace(
                ["{items}"],
                $rightItemsResult,
                $simpleRightMenu
            );
            $menuResult = str_replace(
                ["{style}", "{items}", "{rightItems}"],
                [$menuStyle, $itemsResult, $rightItemsResult],
                $SMenuWithRightItems
            );
        } else {
            $menuResult = str_replace(
                ["{style}", "{items}"],
                [$menuStyle, $itemsResult],
                $standartMenu
            );
        }
        echo $menuResult;
    }
    public static function simplePass(){
        $var = html::GetRealIp();
        $md5_1 = md5($var);
        $md5_2 = md5($md5_1);
        return substr($md5_2, 0, 9);
    }
    public static function GetRealIp()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) 
        {
            $ip=$_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else
        {
        $ip=$_SERVER['REMOTE_ADDR'];
        }
    return $ip;
    }
}