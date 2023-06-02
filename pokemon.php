<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ポケモン図鑑　勢井</title>
</head>
<body>
    
</body>
</html>

<?php
/** PokeAPI のデータを取得する(URL末尾の数字はポケモン図鑑のID) */
$url = 'https://pokeapi.co/api/v2/pokemon/1/';
$response = file_get_contents($url);
// レスポンスデータは JSON 形式なので、デコードして連想配列にする
$data = json_decode($response, true);
print("<pre>");
var_dump($data['name']); // 名前
var_dump($data['sprites']['front_default']); // 正面向きのイメージ
var_dump($data['height']); // たかさ
var_dump($data['weight']); // おもさ
print("</pre>");

/** PokeAPI のデータを取得する(id=11から20のポケモンのデータ) */
$url = 'https://pokeapi.co/api/v2/pokemon/?limit=10&offset=0';
$response = file_get_contents($url);
// レスポンスデータは JSON 形式なので、デコードして連想配列にする
$data = json_decode($response, true);
// 取得結果をループさせてポケモンの名前を表示する
print("<pre>");
foreach($data['results'] as $key => $value){
var_dump($value['name']);
}
print("</pre>");
