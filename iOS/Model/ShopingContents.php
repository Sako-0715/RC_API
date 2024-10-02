<?php
class ShopingContents
{

    public function insertShopdata($row)
    {
        $createId = $row['ID'];
        $dateTime = new DateTime();
        $date = $dateTime->format('Y-m-d H:i:s');

        if ($createId == null) {
            echo json_encode(['result' => false]);
            return;
        }

        $db = new mysqli('localhost:8889', 'root', 'root', 'mydb');
        // 日時が一番最新の行を取得
        $getNum = "SELECT ID FROM SHOPING_CONTENTS ORDER BY date DESC LIMIT 1;";
        $result = $db->query($getNum);

        if ($result->num_rows > 0) {
            $getId = $result->fetch_assoc();
            $pastId = $getId['ID'];

            // 取得できたIDの下3桁を取得
            $carvingId = substr($pastId, -3);

            // 次の番号を生成
            $nextIDNumber = str_pad(intval($carvingId) + 1, 3, '0', STR_PAD_LEFT);

            // 新しいIDを代入
            $createId = $row['ID'] . $nextIDNumber;

            // JSON形式でフロント側に返却する
            echo json_encode(['result' => true, 'ID' => $createId, 'date' => $date]);
        } else {
            // 初回挿入時
            $createId = $createId . "001";

            // JSON形式でフロント側に返却する
            echo json_encode(['result' => true, 'ID' => $createId, 'date' => $date]);
        }

        $janl =  $row['Janl'];
        $count = $row['Count'];
        $prodact =  $row['Prodact'];
        $sql = "INSERT INTO SHOPING_CONTENTS ";
        $sql .= "(ID,JANL,COUNT,PRODACTNAME,DATE) ";
        $sql .= "VALUES ";
        $sql .= "('$createId','$janl','$count','$prodact','$date')";
        $db->query($sql);
    }

    /* 投稿履歴を全て取得する
    */
    public function getShopdata()
    {
        $db = new mysqli('localhost:8889', 'root', 'root', 'mydb');
        $sql = "SELECT ID,JANL,COUNT,PRODACTNAME,DATE,FAVFLAG FROM SHOPING_CONTENTS";
        $result = $db->query($sql);

        $dataArray = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $row['COUNT'] = intval($row['COUNT']);
                $dataArray[] = $row;
            }
            $js = json_encode($dataArray);
            echo $js;
        } else {
            error_log("クエリ実行エラー: " . $db->error);
        }

        $db->close();
    }

    /* ジャンルごとで全て取得する
    * $janl String  
    */
    public function getJanlShopdata($janl)
    {

        $db = new mysqli('localhost:8889', 'root', 'root', 'mydb');
        $sql = "SELECT ID,JANL,PRICE,DATE FROM SHOPING_CONTENTS ";
        $sql .= "WHERE JANL = ";
        $sql .= "$janl";
        $result = $db->query($sql);

        $dataArray = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $row['PRICE'] = intval($row['PRICE']);
                $dataArray[] = $row;
            }
            $js = json_encode($dataArray);
            echo $js;
        } else {
            error_log("クエリ実行エラー: " . $db->error);
        }

        $db->close();
    }

    /* 条件取得する
    * $janl String  
    * $prodact String
    * $count Int
    */
    public function getSearchShopData($janl, $prodact, $count)
    {
        $db = new mysqli('localhost:8889', 'root', 'root', 'mydb');
        $sql = "SELECT ID,JANL,COUNT,PRODACTNAME,DATE FROM SHOPING_CONTENTS ";

        if ($janl == "" && $prodact == "" && $count == "") {
            // 全て空の場合
            $result = $db->query($sql);
        } else if ($janl != "" && $prodact == "" && $count == "") {
            // ジャンルのみ
            $sql .= "WHERE JANL = ";
            $sql .= "'$janl'";
            $result = $db->query($sql);
        } else if ($janl == "" && $prodact != "" && $count == "") {
            //  商品名のみ
            $sql .= "WHERE PRODACTNAME = ";
            $sql .= "'$prodact'";
            $result = $db->query($sql);
        } else if ($janl == "" && $prodact == "" && $count != "") {
            // 個数のみ
            $sql .= "WHERE COUNT = ";
            $sql .= "$count";
            $result = $db->query($sql);
        } else if ($janl != "" && $prodact != "" && $count == "") {
            // ジャンルと商品のみ
            $sql .= "WHERE JANL = ";
            $sql .= "'$janl'";
            $sql .= " AND PRODACTNAME = ";
            $sql .= "'$prodact'";
            $result = $db->query($sql);
        } else if ($janl != "" && $prodact == "" && $count != "") {
            //  ジャンルと個数のみ
            $sql .= "WHERE JANL = ";
            $sql .= "'$janl'";
            $sql .= " AND COUNT = ";
            $sql .= "$count";
            $result = $db->query($sql);
        } else if ($janl == "" && $prodact != "" && $count != "") {
            // 商品名と個数のみ
            $sql .= "WHERE PRODACTNAME = ";
            $sql .= "'$prodact'";
            $sql .= " AND COUNT = ";
            $sql .= "$count";
            $result = $db->query($sql);
        } else {
            // 全部入力されている場合
            $sql .= "WHERE JANL = ";
            $sql .= "'$janl'";
            $sql .= " AND PRODACTNAME = ";
            $sql .= "'$prodact'";
            $sql .= " AND COUNT = ";
            $sql .= "$count";
            $result = $db->query($sql);
        }

        $dataArray = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $row['COUNT'] = intval($row['COUNT']);
                $dataArray[] = $row;
            }
            $js = json_encode($dataArray);
            echo $js;
        } else {
            error_log("クエリ実行エラー: " . $db->error);
        }
        $db->close();
    }

    /* 
    * 選択されたShopingDataを削除する
    */
    public function deleteShopData($row)
    {
        $deleteIds = $row['ID'];
        $db = new mysqli('localhost:8889', 'root', 'root', 'mydb');

        if ($db->connect_error) {
            // DB接続エラーが発生した場合
            echo json_encode(["status" => "error", "message" => "DB connection failed: " . $db->connect_error]);
            return;
        }

        $sql = "DELETE FROM SHOPING_CONTENTS WHERE ID IN (";
        // 選択されたID
        $first = true;
        foreach ($deleteIds as $id) {
            if ($first) {
                // 最初の要素にはカンマをつけない
                $first = false;
            } else {
                $sql .= ", ";
            }
            $sql .= "'" . $db->real_escape_string($id) . "'";
        }
        $sql .= ")";

        // クエリを実行し、成功したか確認
        if ($db->query($sql) === TRUE) {
            // 成功メッセージをJSON形式で返す
            echo json_encode(["status" => "success", "message" => "削除成功"]);
        } else {
            // エラーメッセージをJSON形式で返す
            echo json_encode(["status" => "error", "message" => "削除失敗: " . $db->error]);
        }

        // DB接続を閉じる
        $db->close();
    }

    /* 
    * 選択されたShopingDataをお気に入り登録する
    */
    public function favShopData($row)
    {
        $favId = $row['ID'];
        $db = new mysqli('localhost:8889', 'root', 'root', 'mydb');

        if ($db->connect_error) {
            // DB接続エラーが発生した場合
            echo json_encode(["status" => "error", "message" => "DB connection failed: " . $db->connect_error]);
            return;
        }

        $sql = "UPDATE SHOPING_CONTENTS SET FAVFLAG = '1' WHERE ID IN ( ";
        $first = true;
        foreach ($favId as $id) {
            if ($first) {
                // 最初の要素にはカンマをつけない
                $first = false;
            } else {
                $sql .= ", ";
            }
            $sql .= "'" . $db->real_escape_string($id) . "'";
        }
        $sql .= ")";

        // クエリを実行し、成功したか確認
        if ($db->query($sql) === TRUE) {
            // 成功メッセージをJSON形式で返す
            echo json_encode(["status" => "success", "message" => "保存成功"]);
        } else {
            // エラーメッセージをJSON形式で返す
            echo json_encode(["status" => "error", "message" => "保存失敗: " . $db->error]);
        }

        // DB接続を閉じる
        $db->close();
    }

    /* 
    * 選択されたShopingDataをお気に入りから削除する
    */
    public function favdeleteShopData($row)
    {
        $favId = $row['ID'];
        $db = new mysqli('localhost:8889', 'root', 'root', 'mydb');

        if ($db->connect_error) {
            // DB接続エラーが発生した場合
            echo json_encode(["status" => "error", "message" => "DB connection failed: " . $db->connect_error]);
            return;
        }

        $sql = "UPDATE SHOPING_CONTENTS SET FAVFLAG = '0' WHERE ID IN ( ";
        $first = true;
        foreach ($favId as $id) {
            if ($first) {
                // 最初の要素にはカンマをつけない
                $first = false;
            } else {
                $sql .= ", ";
            }
            $sql .= "'" . $db->real_escape_string($id) . "'";
        }
        $sql .= ")";

        // クエリを実行し、成功したか確認
        if ($db->query($sql) === TRUE) {
            // 成功メッセージをJSON形式で返す
            echo json_encode(["status" => "success", "message" => "保存成功"]);
        } else {
            // エラーメッセージをJSON形式で返す
            echo json_encode(["status" => "error", "message" => "保存失敗: " . $db->error]);
        }

        // DB接続を閉じる
        $db->close();
    }

    /* 投稿履歴を全て取得する
    */
    public function getFavData()
    {
        $db = new mysqli('localhost:8889', 'root', 'root', 'mydb');
        $sql = "SELECT ID,JANL,COUNT,PRODACTNAME,DATE,FAVFLAG FROM SHOPING_CONTENTS ";
        $sql .= "WHERE FAVFLAG = '1' ORDER BY DATE DESC";
        $result = $db->query($sql);

        $dataArray = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $row['COUNT'] = intval($row['COUNT']);
                $dataArray[] = $row;
            }
            $js = json_encode($dataArray);
            echo $js;
        } else {
            error_log("クエリ実行エラー: " . $db->error);
        }

        $db->close();
    }
}
