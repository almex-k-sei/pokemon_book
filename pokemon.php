<?php
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

    $colum_length = 100; //表示するデータの件数
    $page = $colum_length / $one_page; //ページ数を取得
    $page = ceil($page); // 整数に直す。
    $now_page = ($sel_page - 1) * $one_page; // OFFSET を取得 ページ数 -1 * 20

    /** PokeAPI のデータを取得する(id=11から20のポケモンのデータ) */
    $url = "https://pokeapi.co/api/v2/pokemon/?limit={$one_page}&offset={$now_page}";
    // レスポンスデータは JSON 形式なので、デコードして連想配列にする
    $response = file_get_contents($url);
    // 取得結果をループさせてポケモンの名前を表示する
    $data = json_decode($response, true);
    //OFFSET を取得 ページ数 -1 * 20
    $now_page = ($sel_page - 1) * $one_page; 
    
    //フレキシブルボックスで表示
    echo "<div class='flex'>";
    foreach ($data['results'] as $key => $value) {
        //オフセットの範囲のポケモンデータを取得
        $response = file_get_contents($value["url"]);
        $datas = json_decode($response, true);
        //idからspeciesのデータを取得
        $url2 = "https://pokeapi.co/api/v2/pokemon-species/{$datas['id']}/";
        $response2 = file_get_contents($url2);
        $species = json_decode($response2, true);

        //タイプのデータを取得(コンマ区切りで取得する)
        $type = "";
        $type_japanese = "";
        foreach ($datas["types"] as $key2 => $value2) {
            $type .= $value2["type"]["name"];
            $type_url = $value2["type"]["url"];
            $type_response = file_get_contents($type_url);
            $type_japanese_data = json_decode($type_response, true);
            $type_japanese .= $type_japanese_data["names"][2]["name"];
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
                <article class="card_02 card_02_front">
                    <div class="card__header_02">
                    <p class="card__title_02 card__title_02_front">{$value["name"]}</p>
                    <figure class="card__thumbnail_02 card__thumbnail_02_front">
                        <img src="{$datas['sprites']['front_default']}" class="image_size">
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
                    <article class="card_02 card_02_back">
                        <div class="card__header_02">
                        <p class="card__title_02 card__title_02_back">{$species["names"][0]["name"]}</p>
                        <figure class="card__thumbnail_02 card__thumbnail_02_back">
                            <img src="{$datas['sprites']['back_default']}" class="image_size">
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
    }
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
    <h1>ポケモン図鑑</h1>
    <?php card(); ?>
</body>
</html>