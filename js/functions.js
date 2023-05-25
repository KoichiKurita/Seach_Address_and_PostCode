/*
    Webアプリ実践課題
    郵便番号検索・住所検索(functions.js)
    作成者：リンクス新越谷 栗田幸一
    作成日：2023.3.31
    修正日：2023.5.25
 */

// インポート
import {PATH_GET_MUNICIPALITY_PROCESS, PATTERN_HYPHEN} from "./config.js"; // 市区町村取得処理のファイルパス

/**
 * URLからクエリ文字列を取得する
 * @return { {[key: String]: String} } URLのクエリ文字列
 */
let getUrlQueryData = () => {
    // URLのクエリ文字列から、フォームで送信した値を取得する
    let query_data = {};

    if (window.location.search.length > 0) {
        // クエリ文字列の開始を表す?を除く
        let query_string = window.location.search.substring(1);

        // クエリ文字列をデータごとに分割
        let parameters = query_string.split('&');

        for (let i = 0; i < parameters.length; i++) {
            // パラメータ名と値に分割
            let parameter = parameters[i].split('=');

            let paramName = decodeURIComponent(parameter[0]);
            let paramValue = decodeURIComponent(parameter[1]);

            query_data[paramName] = paramValue;
        }
    }

    return query_data;
};


/**
 * プルダウンリストをの一番上の値をセットする
 * @param {object} select_elem セレクトボックス要素
 * @param {string} top_text プルダウンリストの一番上に表示する値
 */
let setSelectorTopOption = (select_elem, top_text) => {
    // プルダウンリストの一番上の項目を作成
    const option_elem = document.createElement('option');
    option_elem.setAttribute('value', '');
    option_elem.textContent = top_text;

    // option要素をselect要素に追加
    select_elem.appendChild(option_elem);
};


/**
 * プルダウンリストをセットする
 * @param {object} select_elem セレクトボックス要素
 * @param {Array<[key: String]: String>} option_array プルダウンリストにセットするオプション一覧
 * @param {string} InitialSelectValue 初期選択する値（デフォルト値は空白）
 */
let setSelectorOptions = (select_elem, option_array, InitialSelectValue = "") => {
    // 配列からプルダウンを作成
    option_array.forEach((option_data) => {
        // option要素を作成し、1つずつ追加する
        const option_elem = document.createElement('option');

        option_elem.setAttribute('value', option_data);
        option_elem.textContent = option_data;

        // 初期選択を設定する
        if (option_elem.getAttribute('value') === InitialSelectValue) {
            option_elem.selected = true;
        }

        // option要素をselect要素に追加
        select_elem.appendChild(option_elem);
    });

};


/**
 * プルダウンリストの中身を削除する
 * @param {object} select_elem セレクトボックス要素
 */
let removeSelectorOption = (select_elem) => {
    // select要素配下の全てのoption要素を削除する
    while (select_elem.childNodes.length > 0) {
        select_elem.removeChild(select_elem.firstChild)
    }
};


/**
 * 都道府県から対応する市区町村データをPHP経由で取得する
 * @param {string} selected_prefecture 選択された都道府県
 * @return {Array<string> | false} 市区町村一覧データ
 */
let getMunicipalityList = (selected_prefecture) => {
    // get_municipality.phpに都道府県データを渡す
    // fetchメソッドの第一引数はURLであることに注意する（ディレクトリの相対パス表記はエラーになる）
    // fetchメソッドの戻り値はPromiseオブジェクト
    fetch(PATH_GET_MUNICIPALITY_PROCESS, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(selected_prefecture)
    })
    .then(response => response.json())  // get_municipality.phpからデータを受け取る
    .then(res => {
        // PHPのデータベース処理エラーがあれば表示する
        if ('error_msg' in res) {
            console.log(error_msg);
            return false;
        } else {
            // データベース接続エラーがなければ、PHP経由で取得したデータを返す
            return res;
        }
    })
    .catch(error => console.log(error));    // fetch処理が失敗したときのエラー
};


// エクスポート
export {getUrlQueryData, setSelectorTopOption, setSelectorOptions, removeSelectorOption};