<?php
function getCacheContents($url, $cachePath, $cacheLimit = 86400) {
    if(file_exists($cachePath) && filemtime($cachePath) + $cacheLimit > time()) {
      // キャッシュ有効期間内なのでキャッシュの内容を返す
      return file_get_contents($cachePath);
    } else {
      // キャッシュがないか、期限切れなので取得しなおす
      $data = file_get_contents($url);
      file_put_contents($cachePath, $data, LOCK_EX); // キャッシュに保存
      return $data;
    }
}
function getItems($url,$name) {
    $res = getCacheContents($url, "./cache/{$name}cache");
    return json_decode($res,true);
  }
function getImage($url,$name) {
    $res = getCacheContents($url, "./image/{$name}.png");
    return $res;
}

function card()
{
    //$ページングした時のページを格納（初期値として１を代入）
    if (!isset($_POST["sel_page"])) {
        $sel_page = 1;
    } else {
        $sel_page = $_POST["sel_page"];
    }

    //$表示する件数の値を格納（初期値として10を代入）
    if (!isset($_POST["one_page"])) {
        $one_page = 10;
    } else {
        $one_page = $_POST["one_page"];
    }

    //ページングした時の表示する件数の値を格納
    if (isset($_POST["select_page"])) {
        $one_page = $_POST["select_page"];
    }

    $colum_length = 200; //表示するデータの件数
    $page = $colum_length / $one_page; //ページ数を取得
    $page = ceil($page); // 整数に直す。
    $now_page = ($sel_page - 1) * $one_page; // OFFSET を取得 ページ数 -1 * 20

    /** PokeAPI のデータを取得する(id=11から20のポケモンのデータ) */
    $url = "https://pokeapi.co/api/v2/pokemon/?limit={$one_page}&offset={$now_page}";
    // レスポンスデータは JSON 形式なので、デコードして連想配列にする
    // $response = file_get_contents($url);
    // 取得結果をループさせてポケモンの名前を表示する
    // $data = json_decode($response, true);
    //OFFSET を取得 ページ数 -1 * 20
    $now_page = ($sel_page - 1) * $one_page; 
    $data = getItems($url,"data".$one_page.",".$now_page);
    //フレキシブルボックスで表示
    echo "<div class='flex'>";
    foreach ($data['results'] as $value) {
        //オフセットの範囲のポケモンデータを取得
        // $response = file_get_contents($value["url"]);
        // $datas = json_decode($response, true);
        // var_dump($value["name"]);
        $datas = getItems($value["url"],"pokemon".$value["name"]);
        //idからspeciesのデータを取得
        $url2 = "https://pokeapi.co/api/v2/pokemon-species/{$datas['id']}/";
        // $response2 = file_get_contents($url2);
        // $species = json_decode($response2, true);
        $species = getItems($url2,"species".$datas["id"]);

        //画像の取得
        getImage($datas['sprites']['front_default'],"front_image".$datas["id"]);
        getImage($datas['sprites']['back_default'],"back_image".$datas["id"]);
        //タイプのデータを取得(コンマ区切りで取得する)
        $type = "";
        $type_japanese = "";
        foreach ($datas["types"] as $key2 => $value2) {
            if($key2 == 0){
                $front_color = type_color($value2["type"]["name"]);
                $back_color = $front_color;
            }else{
                $back_color = type_color($value2["type"]["name"]);
            }

            $type .= $value2["type"]["name"];
            $type_url = $value2["type"]["url"];
            // $type_response = file_get_contents($type_url);
            // $type_japanese_data = json_decode($type_response, true);
            $type_japanese_data = getItems($type_url,"types".$value2["type"]["name"]);
            $type_japanese .= $type_japanese_data["names"][0]["name"];
            if ($key2 < count($datas["types"]) - 1) {
                $type .= ",";
                $type_japanese .= "、";
            }
        }


        //カード形式でポケモンの情報を表示（カードをホバーすると裏返る。表は英語の情報、裏は日本語の情報を表示する）
        echo <<<_FORM_
        <div class="card">
            <div class="back">
            <div class="l-wrapper_02 card-radius_02">
                <article class="card_02 card_02_front" style="background-color: {$front_color};">
                    <div class="card__header_02">
                    <p class="card__title_02">{$value["name"]}</p>
                    <figure class="card__thumbnail_02 card__thumbnail_02_front">
                        <img src="./image/front_image{$datas["id"]}.png" class="image_size">
                    </figure>
                    </div>
                    <div class="card__body_02">
                    <p class="card__text2_02">
                    <p><b>weight：</b>{$datas["weight"]}</p>
                    <p><b>height：</b>{$datas["height"]}</p>
                    <p><b>type：</b>{$type}</p>
                    <p><b>description:</b>{$species["flavor_text_entries"][11]["flavor_text"]}</p>
                    </p>
                    </div>    
                </article>
        </div>
            </div>
            <div class="front">
                <div class="l-wrapper_02 card-radius_02">
                    <article class="card_02 card_02_back" style="background-color: {$back_color};">
                        <div class="card__header_02">
                        <p class="card__title_02">{$species["names"][0]["name"]}</p>
                        <figure class="card__thumbnail_02 card__thumbnail_02_back">
                            <img src="./image/back_image{$datas["id"]}.png" class="image_size">
                        </figure>
                        </div>
                        <div class="card__body_02">
                        <p class="card__text2_02">
                        <p><b>重さ：</b>{$datas["weight"]}</p>
                        <p><b>高さ：</b>{$datas["height"]}</p>
                        <p><b>タイプ：</b>{$type_japanese}</p>
                        <p><b>説明:</b>{$species["flavor_text_entries"][29]["flavor_text"]}</p>
                        </p>
                        </div>    
                    </article>
                </div>
            </div>
        </div>
        _FORM_;
    }
    echo "</div>";
    // ページング機能の実装
    echo "<div class='paging'>";
    //前へボタンの実装
    if($sel_page > 1){
        $backpage = $sel_page -1;
    }else{
        $backpage = 1;
    }
    echo "
    <form action='pokemon.php' method='post'>
        <input type='hidden' name='sel_page' value='{$backpage}'>
        <input type='hidden' name='select_page' value='{$one_page}'>
        <input type='submit' class='other_btn' value='＜' class='paging'>
    </form>
    ";
    //数字ボタンの実装
    $count = 0;
    for ($i = 1; $i <= $page; $i++) {
        //現在のページの時は黄色でそれ以外は水色で表示する
        if ($i == $sel_page) {
            $button = "now_btn";
        } else {
            $button = "other_btn";
        }
        echo "
        <form action='pokemon.php' method='post'>
            <input type='hidden' name='sel_page' value='{$i}'>
            <input type='hidden' name='select_page' value='{$one_page}'>
            <input type='submit' class='{$button}' value='{$i}' class='paging'>
        </form>
        ";
        $count++;
    }
    //次へボタンの実装
    if($sel_page < $count){
        $nextpage = $sel_page + 1;
    }else{
        $nextpage = $count;
    }
    echo "
    <form action='pokemon.php' method='post'>
        <input type='hidden' name='sel_page' value='{$nextpage}'>
        <input type='hidden' name='select_page' value='{$one_page}'>
        <input type='submit' class='other_btn' value='＞' class='paging'>
    </form>
    ";
    echo "</div>";
    echo "<div style='display: inline'>";
    //セレクトボックスの実装（現在の表示件数が先頭に来るようになっている）
    if ($one_page == 10) {
        echo <<<_FORM_
        <form action='pokemon.php' method='post'>
            <select name="one_page" onchange="this.form.submit()">
                <option value="10">10ページ</option>
                <option value="20">20ページ</option>
                <option value="50">50ページ</option>
            </select>
        </form>
    _FORM_;
    } elseif ($one_page == 20) {
        echo <<<_FORM_
        <form action='pokemon.php' method='post'>
            <select name="one_page" onchange="this.form.submit()">
                <option value="20">20ページ</option>
                <option value="10">10ページ</option>
                <option value="50">50ページ</option>
            </select>
        </form>
    _FORM_;
    } else {
        echo <<<_FORM_
        <form action='pokemon.php' method='post'>
            <select name="one_page" onchange="this.form.submit()">
                <option value="50">50ページ</option>
                <option value="10">10ページ</option>
                <option value="20">20ページ</option>
            </select>
        </form>
    _FORM_;
    }
    echo "</div>";
}

function type_color($type){
    switch ($type) {
        case "normal":
            $color = "orange";
            break;
        case "grass":
            $color = "green";
            break;
        case "poison":
            $color = "purple";
            break;
        case "fire":
            $color = "red";
            break;
        case "flying":
            $color = "gold";
            break;
        case "water":
            $color = "blue";
            break;
        case "bug":
            $color = "darkgreen";
            break;
        case "electric":
            $color = "#999900";
            break;
        case "ground":
            $color = "#955629";
            break;
        case "fairy":
            $color = "pink";
            break;
        case "fighting":
            $color = "	#666699";
            break;
        case "ice":
            $color = "lightblue";
            break;
        case "psychic":
            $color = "#CD853F";
            break;
        case "rock":
            $color = "gray";
            break;
        case "steel":
            $color = "lightgray";
            break;
        case "ghost":
            $color = "darkglay";
            break;
        case "dragon":
            $color = "darkred";
            break;
        case "dark":
            $color = "black";
            break;
    }
    return $color;
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="style/style.css" rel="stylesheet">
    <title>ポケモンずかん　勢井</title>
</head>
<body>
    <header>
        <div class="label"></div>
        <h1>ポケモンずかん</h1>
        <div class="base">
        <div class="center">
            <button class="center-button"></button>
        </div>
        </div>
        <div class="shadow"></div>
    </header>
    <main>
        <?php card(); ?>
    </main>
</body>
</html>