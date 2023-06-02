<?php
function card(){
    
    # $sel_pageの初期値は1とする。
    if(!isset($_POST["sel_page"])){
        $sel_page = 1;
    }else{
        $sel_page = $_POST["sel_page"];
    }

    $colum_length = 1000;
    $one_page = 20;
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
        echo <<< _FORM_
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
                <p>重さ：{$datas["weight"]}</p>
                <p>高さ：{$datas["height"]}</p>
                </p>
                </div>    
            </article>
        </div>
        _FORM_;
    }
    echo "</div>";
    # ページの数を取得し、表示
    echo "<p class='paging'>";
    for($i=1; $i<=$page; $i++){
        echo "
        <form action='pokemon.php' method='post'>
        <input type='hidden' name='sel_page' value='{$i}'>
        <input type='submit' class='page_btn' value='{$i}' class='paging'>
        </form>
        ";
    }
    echo "</p>";
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

