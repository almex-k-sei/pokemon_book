<?php
function card(){
    
    # $sel_pageの初期値は1とする。
    if(!isset($_POST["sel_page"])){
        $sel_page = 1;
    }else{
        $sel_page = $_POST["sel_page"];
    }

    if(!isset($_POST["one_page"])){
        $one_page = 10;
    }else{
        $one_page = $_POST["one_page"];
    }

    $colum_length = 100;
    // $one_page = 10;
    $page = $colum_length / $one_page; # ページ数を取得
    $page = ceil($page); # 整数に直す。
	$now_page = ($sel_page - 1) * $one_page; # OFFSET を取得 ページ数 -1 * 20

    /** PokeAPI のデータを取得する(id=11から20のポケモンのデータ) */
    $url = "https://pokeapi.co/api/v2/pokemon/?limit={$one_page}&offset={$now_page}";
    $response = file_get_contents($url);
    // レスポンスデータは JSON 形式なので、デコードして連想配列にする
    $data = json_decode($response, true);
    // 取得結果をループさせてポケモンの名前を表示する

    $now_page = ($sel_page - 1) * $one_page; # OFFSET を取得 ページ数 -1 * 20

    echo "<div class='flex'>";
    foreach($data['results'] as $key => $value){
        $response = file_get_contents($value["url"]);
        $datas = json_decode($response, true);
        $url2 = "https://pokeapi.co/api/v2/pokemon-species/{$datas['id']}/";
        $response2 = file_get_contents($url2);
        $species= json_decode($response2, true);

        $type = "";
        foreach($datas["types"] as $key2  => $value2){
            $type .= $value2["type"]["name"];
            if($key2 < count($datas["types"]) - 1 ){
                $type .= ",";
            }
        }
        echo <<< _FORM_
        <div class="card">

            <div class="back">
            <div class="l-wrapper_02 card-radius_02">
                <article class="card_02">
                    <div class="card__header_02">
                    <p class="card__title_02">{$value["name"]}</p>
                    <figure class="card__thumbnail_02">
                        <img src="{$datas['sprites']['front_default']}" class="image_size">
                    </figure>
                    </div>
                    <div class="card__body_02">
                    <p class="card__text2_02">
                    <p><b>weight：</b>{$datas["weight"]}</p>
                    <p><b>height：</b>{$datas["height"]}</p>
                    <p><b>type：</b>{$type}</p>
                    <p><b>description:</b>{$species["flavor_text_entries"][0]["flavor_text"]}</p>
                    </p>
                    </div>    
                </article>
        </div>
            </div>

            <div class="front">
                <div class="l-wrapper_02 card-radius_02">
                    <article class="card_02">
                        <div class="card__header_02">
                        <p class="card__title_02">{$value["name"]}</p>
                        <figure class="card__thumbnail_02">
                            <img src="{$datas['sprites']['back_default']}" class="image_size">
                        </figure>
                        </div>
                        <div class="card__body_02">
                        <p class="card__text2_02">
                        <p><b>重さ：</b>{$datas["weight"]}</p>
                        <p><b>高さ：</b>{$datas["height"]}</p>
                        <p><b>タイプ：</b>{$type}</p>
                        <p><b>説明:</b>{$species["flavor_text_entries"][0]["flavor_text"]}</p>
                        </p>
                        </div>    
                    </article>
                </div>
            </div>
        </div>
        _FORM_;
    }
    echo "</div>";
    # ページの数を取得し、表示
    echo "<div class='paging'>";
    for($i=1; $i<=$page; $i++){
        echo "
        <form action='pokemon.php' method='post'>
            <input type='hidden' name='sel_page' value='{$i}'>
            <input type='submit' class='page_btn' value='{$i}' class='paging'>
        </form>
        ";
    }
    echo "</div>";

    if($one_page == 10){
    echo <<< _FORM_
        <form action='pokemon.php' method='post'>
            <select name="one_page" onchange="this.form.submit()">
                <option value="10">10ページ</option>
                <option value="20">20ページ</option>
                <option value="50">50ページ</option>
            </select>
        </form>
    _FORM_;
    }
    elseif($one_page == 20){
    echo <<< _FORM_
        <form action='pokemon.php' method='post'>
            <select name="one_page" onchange="this.form.submit()">
                <option value="20">20ページ</option>
                <option value="10">10ページ</option>
                <option value="50">50ページ</option>
            </select>
        </form>
    _FORM_;
    }else{
    echo <<< _FORM_
        <form action='pokemon.php' method='post'>
            <select name="one_page" onchange="this.form.submit()">
                <option value="50">50ページ</option>
                <option value="10">10ページ</option>
                <option value="20">20ページ</option>
            </select>
        </form>
    _FORM_;
    }
}


?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="style/style.css" rel="stylesheet">
    <title>ポケモン図鑑　勢井</title>
</head>

<body>
        <?php card();?>

</body>

</html>

