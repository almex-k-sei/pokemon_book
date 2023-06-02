<?php

# ページング処理
function show(){
	# $sel_pageの初期値は1とする。
    if(!isset($_POST["sel_page"])){
        $sel_page = 1;
    }else{
        $sel_page = $_POST["sel_page"];
    }

	$colum_length = 500;
    $offset = 
    $one_page = 20;
    $page = $colum_length / $one_page; # ページ数を取得
	$page = ceil($page); # 整数に直す。

	$now_page = ($sel_page - 1) * $one_page; # OFFSET を取得 ページ数 -1 * 20


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
<html lang='ja'>

<head>
	<meta charset='UTF-8'>
	<title>在庫管理</title>
	<link rel='stylesheet' href='./CSS/main_product.css'>
</head>

<body>
	<main>
		<div class="product">
			<div id="session">
			</div>
			<div id="list_product">
				<h2>商品一覧</h2><br>
				<?php
			?>
				<table border='1'>
					<tr>
						<th>商品ID</th>
						<th>商品名</th>
						<th>在庫</th>
						<th>値段</th>
						<th colspan=2></th>
					</tr>
					<?php show(); ?>
				</table>
			</div>
	</main>
</body>

</html>
