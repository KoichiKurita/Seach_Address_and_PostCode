<?php declare(strict_types=1); ?>

<!--
    Webアプリ実践課題
    郵便番号検索(result_postcode.php)
    作成者：リンクス新越谷 栗田幸一
    作成日：2023.3.31
    修正日：2023.4.8
-->

<!-- メインルーチン -->
<!-- 住所フォームの入力内容から郵便番号を検索する -->
<?php
    require_once dirname(__FILE__). '/functions.php';   // 外部関数読み込み

    // DBに接続する。
    try {
        $pdo = connect();   // DB接続
    } catch (PDOException $e) {
        echo 'データベースの接続に失敗しました。';
        echo 'エラー理由'. $e->getMessage(). PHP_EOL;
        return;
    }

    // DBに接続し、都道府県データを検索する。
    try {
        // DBから都道府県一覧データを取得する
        // 【メモ】PHPの変数は関数以外のブロックで宣言されたものはブロック外でも有効
        // 上記try文内で宣言された$pdoは下のブロックで使用できる
        $statement_prefecture = get_prefecture($pdo);
    } catch (PDOException $e) {
        echo '都道府県データの取得に失敗しました。';
        echo 'エラー理由'. $e->getMessage(). PHP_EOL;
        return;
    }

    // DBから郵便番号を検索する。
    try {

        // 町名の入力の有無で検索処理を分ける
        if (isset($_GET['town_area']) && (trim($_GET['town_area']) !== '')) { // 町名が入力されている場合
            $statement_postcode = search_post_code($pdo, $_GET['prefecture'], $_GET['municipality'], trim($_GET['town_area']));
        } else {    // 町名が入力されていない場合
            $statement_postcode = search_post_code($pdo, $_GET['prefecture'], $_GET['municipality']);
        }

        // 検索結果が0件の場合、都道府県+市区町村で再検索する
        if ( $statement_postcode->rowCount() === 0) {
            $statement_postcode = search_post_code($pdo, $_GET['prefecture'], $_GET['municipality']);
        }

    } catch (PDOException $e) {
        echo '郵便番号の検索に失敗しました。';
        echo 'エラー理由'. $e->getMessage(). PHP_EOL;
        return;
    }

?>

<!-- 以下、結果表示用Webページ -->
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>郵便番号検索結果</title>
        <link rel="stylesheet" href="css/style.css">
    </head>

    <body>
        <!-- result.phpにも検索フォームを表示する -->
        <h1>郵便番号検索</h1>
        <p id="direction">都道府県, 市区町村, 町名を入力してください。住所検索は<a href="search_address.html">こちら</a></p>
        <form name="search-post-code" action="result_postcode.php" method="GET">
            <div class="address-input-wrapper">
                <div class="address-input">
                    <label for="prefecture">都道府県<span class="required">（必須）</span>：
                        <select name="prefecture" id="prefecture" required>
                            <option value="">選択してください</option>
                            <?php while ($row_prefecture = $statement_prefecture->fetch(PDO::FETCH_ASSOC)): ?>
                                <?php
                                    // 検索ページで選択した都道府県を初期表示する
                                    $selected_flag = '';
                                    if($row_prefecture['prefecture'] === $_GET['prefecture']) {
                                        $selected_flag = 'selected';
                                    }
                                ?>
                                <option value="<?=escape($row_prefecture['prefecture'])?>" <?=$selected_flag?>><?=escape($row_prefecture['prefecture'])?></option>
                            <?php endwhile; ?>
                        </select>
                    </label>
                </div>

                <div class="address-input">
                    <label for="municipality">市区町村<span class="required">（必須）</span>：
                        <select name="municipality" id="municipality" required>
                            <option value="">選択してください</option>
                        </select>
                    </label>
                </div>

                <div class="address-input">
                    <label for="town_area">町名：
                        <input type="text" name="town_area" id="town_area">
                    </label>
                </div>
            </div>

            <button type="submit" name="operation" value="search">検索開始</button>
        </form>

        <hr size="5" color="black" noshade>     <!--- 罫線 --->

        <!-- フォームに入力された住所とデータベースで検索した結果を表形式で表示する -->
        <h1 id="result_heading">住所「<?=escape($_GET['prefecture']. $_GET['municipality']. trim($_GET['town_area']))?>」の郵便番号の検索結果：<?=$statement_postcode->rowCount()?>件</h1>
        <table border="1">
            <tr>
                <th>郵便番号</th>
                <th>都道府県</th>
                <th>市区町村</th>
                <th>町名</th>
            </tr>

            <?php while ($row_postcode = $statement_postcode->fetch(PDO::FETCH_ASSOC)): ?>
            <tr>
                <td><?=escape($row_postcode['post_code'])?></td>
                <td><?=escape($row_postcode['prefecture'])?></td>
                <td><?=escape($row_postcode['municipality'])?></td>
                <td><?=escape($row_postcode['town_area'])?></td>
            </tr>
            <?php endwhile; ?>
        </table>

        <a href='search_postcode.php'>郵便番号検索トップへ戻る</a>

        <!-- JavaScript -->
        <script src="js/result_postcode_script.js" type="module"></script>
    </body>
</html>