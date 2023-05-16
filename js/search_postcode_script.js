/*
    Webアプリ実践課題
    郵便番号検索(search_postcode_script.js)
    作成者：リンクス新越谷 栗田幸一
    作成日：2023.3.3
    修正日：2023.4.8
 */


// 都道府県のプルダウンの値に応じて、市区町村のプルダウンの内容を変化させる
// 参考サイト：https://noveblo.com/javascript-select-factory/

// javascriptからPHPへのデータの受け渡しについて
// fetch：https://brainlog.jp/programming/javascript/post-3129/
// jQueryの.Ajax：https://brainlog.jp/programming/javascript/post-530/

// インポート
import {getUrlQueryData,
        setSelectorTopOption,
        setSelectorOptions,
        removeSelectorOption
    } from "./functions.js"; // 外部関数

// Webページロード時にJavaScriptを実行する
document.addEventListener('DOMContentLoaded', function() {

    // 都道府県と市区町村のselect要素を取得
    const prefectureSelect = document.getElementById('prefecture');
    const municipalitySelect = document.getElementById('municipality');

    // 都道府県が選択されたら、市区町村のプルダウンを生成する
    prefectureSelect.addEventListener('input', async function() {

        // 市区町村のプルダウンをリセットする
        removeSelectorOption(municipalitySelect);

        // 市区町村のプルダウンがリセットされたので、プルダウンに「選択してください」を追加
        setSelectorTopOption(municipalitySelect, '選択してください');

        // 都道府県から対応する市区町村一覧データを取得する
        let municipality_list = [];

        // await fetch('http://localhost/search_address_postcode_modif/get_municipality.php', {    // 市区町村データ取得プログラムのアクセス（ローカル環境）
        await fetch('http://xd868156.php.xdomain.jp/search_address_postcode/get_municipality.php', {    // 市区町村データ取得プログラムのアクセス（デプロイ環境）
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(prefectureSelect.value)
        })
        .then(response => response.json())  // get_municipality.phpからデータを受け取る
        .then(res => {
            // PHPのデータベース処理エラーがあれば表示する
            if ('error_msg' in res) {
                console.log(error_msg);
            } else {
                // データベース接続エラーがなければ、PHP経由で取得したデータをセットする
                municipality_list = res;
            }

        })
        .catch(error => console.log(error));    // fetch処理が失敗したときのエラー

        // 市区町村のプルダウンをセットする
        setSelectorOptions(municipalitySelect, municipality_list);

        // 市区町村を選択可能にする
        municipalitySelect.disabled = false;

        // 都道府県が選択されていない（「選択してください」が表示されている）とき、市区町村を選択できないようにする
        if (prefectureSelect.value === '') {
            municipalitySelect.disabled = true;
        }
    }, false);
}, false);