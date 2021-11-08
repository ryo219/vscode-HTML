<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission3-5</title>
</head>
<p><strong>　　　Please do not enter the Enter Key!　　Press the buttons below.</strong></p>
<body>

    <?php
        $name = @$_POST["name"];
        $comment = @$_POST["comment"];
        $delete = @$_POST["delete"];
        $edit = @$_POST["edit"];
        $pass_input = @$_POST["pass_input"]; 
        $pass_delete = @$_POST["pass_delete"];         
        $pass_edit = @$_POST["pass_edit"];         
      
        $filename="3-05-inputs.txt";

        $flag_edit = @$_POST["edit_n"];
        $date = date("Y/m/d  H:i:s");
        


// --- コメント入力 ---
        if(isset($_POST["submit_com"])){
    // 編集時
            if($flag_edit){         
                if($comment){           //コメント，passの入力があるか確認　passを誤って書き改めた場合の警告はめんどくて書いてない
                    if($pass_input){
                        $edit=$_POST['edit_n'];
                        $lines = file($filename);
                        $fp = fopen($filename, "w");     
                    
                        echo "<script type='text/javascript'>alert(' Comment No.$edit   Edited');</script>";                
                
                        foreach($lines as $line){
                            $editdata = explode("< >", $line);
                            if($editdata[0] == $edit){//投稿番号と編集番号が一致したとき上書き
                                fwrite($fp,"$edit< >.< >$name< >:< >$comment< >　< >$date< >$pass_input"."\n");
                            }
                            else{
                                fwrite($fp, $line);
                            }
                        }
                        fclose($fp);                        
                    }
                }
                $flag_edit = 0;
            }
    //新規投稿時
            else{
                if($comment){           //コメント，passが入ってなければやりなおし
                    if($pass_input){    
                        echo "<script type='text/javascript'>alert(' Your Comment Accepted　:　$comment ');</script>";                
            
                        //投稿番号
                        $count = count(file($filename))+1;

                        if(!$name){                 //名前の入力なければ匿名処理
                            $name = "Anonymous";
                        }
            
                        $current = "";
                        $current .= "$count< >.< >$name< >:< >$comment< >　< >$date< >$pass_input";
                        // 結果をファイルに書き出し
                        @file_put_contents($filename, $current. "\n", FILE_APPEND);
                    }
                }
            }
        }
// ---　投稿終了　--- 
        
        
        
// --- コメント削除 ---  
        if(isset($_POST["submit_del"])){
            if($delete){                //削除番号，passがなければやりなおし
                if($pass_delete){
                    $lines = file($filename);
                    $fp = fopen($filename, "w");
                // 削除番号がデカい場合，何もせず終了
                    if($delete>count($lines)){
                        echo "<script type='text/javascript'>alert(' Comment　No.$delete   Not Found ! ');</script>";    

                        for ($i=0; $i<count($lines); $i++){
                            fwrite($fp, $lines[$i]);
                        }
                        fclose($fp);
                    }
                // ------    
                // 該当削除番号がある場合 ⇒ passの正否で分岐
                    else{
                        $line_pass = explode("< >",$lines[$delete-1]);
                        $line_pass[7] = trim($line_pass[7]);        //なぜかtxtからpassを取得すると末尾にインデントが入るから，除く
                    // passが不一致の場合
                        if( strcmp($line_pass[7], $pass_delete) != 0 ){
                            $alert_del = "<script type='text/javascript'>alert(' Wrong Pass ! ');</script>";                
                            echo $alert_del;   
                            for ($i=0; $i<count($lines); $i++){
                                fwrite($fp, $lines[$i]);
                            }
                        fclose($fp);
                        }
                    // ------
                    // passが一致の場合
                        else{
                            echo "<script type='text/javascript'>alert(' Comment　No.$delete    Deleted ');</script>";    
                            
                            for ($i=0; $i<count($lines); $i++){
                                $line = explode("< >", $lines[$i]); 
                                // 投稿番号と削除対象番号が不一致のものだけ書き込む
                                if ($line[0] != $delete){
                                    fwrite($fp, $lines[$i]);
                                } 
                            }
                            fclose($fp);        //一旦file閉じとく

                            // 削除後の投稿番号を調整
                            $lines = file($filename);
                            $fp = fopen($filename, "w");
                            for ($i=0; $i<count($lines); $i++){
                                $line = explode("< >", $lines[$i]);
                                // 投稿番号がずれてるとこだけ番号変更
                                if ($line[0] > $delete){
                                    $line[0] = $line[0]-1;
                                    $lines[$i] = implode("< >", $line);
                                }
                                fwrite($fp, $lines[$i]);
                            }
                            // ------
                            fclose($fp); 
                        }
                    }
                    // ------
                }
                // ------
            }
        }
// ------


// --- コメント編集 前処理 ---  
        if(isset($_POST["submit_edi"])){    //編集番号，passの入力あって編集ボタン押された場合
            if($edit){
                if($pass_edit){
                    $lines = file($filename);
                    $fp = fopen($filename, "w");
                    
                // 編集番号がデカい場合，何もせず終了
                    if($edit>count($lines)){
                        $alert_edi = "<script type='text/javascript'>alert(' Comment　No.$edit   Not Found ! ');</script>";    
                        echo $alert_edi;   
                        for ($i=0; $i<count($lines); $i++){
                            fwrite($fp, $lines[$i]);
                        }
                        fclose($fp);
                    }
                // ------
                // 編集番号に該当する投稿がある場合
                    else{
                        $line_pass = explode("< >",$lines[$edit-1]);
                        $line_pass[7] = trim($line_pass[7]);        //なぜかtxtからpassを取得すると末尾にインデントが入るから，除く

                        if( strcmp($line_pass[7], $pass_edit) != 0 ){       //pass一致の判定
                            $alert_edi = "<script type='text/javascript'>alert(' Wrong Pass ! ');</script>";                
                            echo $alert_edi;   
                            for ($i=0; $i<count($lines); $i++){
                                fwrite($fp, $lines[$i]);
                            }
                            fclose($fp);
                        }

                        else{
                            for ($i=0; $i<count($lines); $i++){
                                // 区切り文字「< >」で分割
                                $line = explode("< >", $lines[$i]);
        
                                // 投稿番号と編集対象番号が一致する場合、名前 / comment / pass を保存，投稿フォームに投げとく
                                if ($line[0] == $edit){
                                    $flag_edit = $line[0];
                                    $newname = $line[2];
                                    $newcomment = $line[4];
                                    $showpass = $pass_edit;
                                }
                                fwrite($fp, $lines[$i]);
                            }
                        fclose($fp);
                        }
                    }
                }
                // ------
            }
        }
//------
    ?>
   
   
<!-- ここから各フォームの設定 -->
    <form action="3-05.php" method="post">
        名前　　 　　　　<input type="text" name="name" value = "<?php echo @$newname ; ?>"><br>
        コメント 　　　　<input type="text" name="comment" value = "<?php echo @$newcomment ; ?>"><br>
        Pass 　　　　　　<input type="text" name="pass_input" value = "<?php echo @$showpass ; ?>"><br>
        <input type="submit" name="submit_com"><br><br>
        
        削除対象番号　　 <input type="number" name="delete" placeholder="削除対象番号"><br>
        Pass 　　　　　　<input type="text" name="pass_delete"><br>
        <input type="submit" name="submit_del" value="削除"><br><br>
        
        編集対象番号　　 <input type="number" name="edit" placeholder="編集対象番号"><br>
        Pass 　　　　　　<input type="text" name="pass_edit"><br>
        <input type="submit" name="submit_edi" value="編集"><br><br><br>
        <input type="hidden" name="edit_n" value="<?php echo $edit;?>">
    </form>
  
  
<p>---------------------<br>
<strong>　【投稿一覧】</strong></p>


    <?php
// txt内容をブラウザへ表示
        if(file_exists($filename)){
            $lines = file($filename,FILE_IGNORE_NEW_LINES);
            foreach($lines as $line){
                $l = explode("< >", $line);
                for($i=0; $i<count($l)-1; $i++){
                    echo "$l[$i] ";                    
                }
                echo "<br><br>";
            }
        }  
    ?>
</body>
</html>
