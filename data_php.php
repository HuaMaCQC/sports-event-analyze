<?php
  $link = mysqli_connect("localhost","root","","test");  //連線
  if(mysqli_connect_errno()) {echo '與資料庫連線失敗';exit;} //測試連線是否成功
  if(!mysqli_set_charset($link,"utf8")){echo '設定失敗';} //設定萬國碼
  $url_my_html='';
  // $url='http://best9458.cc/competition.php';
  $url = './demo/my_game.html';
  $url_html=file($url); //將$url網址裡的內容存入$url_html
  for($x=0;$x<sizeof($url_html);$x++) 
  {
    $url_my_html.=$url_html[$x]; //將抓到的陣列資料串接成$url_my_html
  }
  $url_my_html=str_replace(array("\r", "\n", "\r\n", "\n\r"),'',$url_my_html);  //將資料換行刪除
  $my_pattern='/<tr\sheight\=\"40\">(.*?)<\/tr>/'; //條件式<tr height="40"> .....</tr>
  preg_match_all($my_pattern,$url_my_html,$preg_url);   //抓取<tr height="40">表單裡面的內容
  
  for($x=0;$x<sizeof($preg_url[1]);$x++)  //將$preg_url[1]裡的資料再次抽離
  { 
    preg_match_all('/<td[^>]+>(.*?)<\/td>/',$preg_url[1][$x],$preg_url_2); //將$preg_url[1][$x] 以方法<td...>(...)</td>的方式抽離
    $Duel_name=explode('<font style="color:#900;">對</font>',$preg_url_2[1][2]);
    $out_url[$x][]=$preg_url_2[1][0];
    $out_url[$x][]=$preg_url_2[1][1];
    $out_url[$x][]=$Duel_name[0];
    $out_url[$x][]=$Duel_name[1];
    $out_url[$x][]=$preg_url_2[1][3];
    $out_url[$x][]=$preg_url_2[1][4];
    $out_url[$x][]=$preg_url_2[1][5];
    $out_url[$x][]=$preg_url_2[1][6];
    $out_url[$x][3]=trim($out_url[$x][3]," ") ; //刪除字首字尾空白
  }
  $ADD_quantity=0; //重製新增筆數
  $upd=0;
  $game_array_add=[];
  $game_array_upd=[];
  for($x=0;$x<count($out_url);$x++) //逐行新增or更新
  {
    $sql="SELECT*FROM `game_data` WHERE Date='".$out_url[$x][4]."' AND number='".$out_url[$x][0]."'"; //sql:收尋出日期與編號
    $result=mysqli_query($link,$sql); //執行sql指令     
    if(mysqli_num_rows($result)>0) //判斷是否有重複
    {//重複=>更新        
      //echo "進入重複";
      $sql_upd="UPDATE `game_data` SET `Alliance`='".$out_url[$x][1]."',
      `Visitors`='".$out_url[$x][2]."',`Home team`='".$out_url[$x][3]."',`First half`='".$out_url[$x][5]."',
      `All_Game`='".$out_url[$x][6]."',`Remarks`='".$out_url[$x][7]."' WHERE Date=
      '".$out_url[$x][4]."' AND number='".$out_url[$x][0]."'";
      mysqli_query($link,$sql_upd);  //執行更新
      $result = mysqli_query($link, $sql);
      while($data=mysqli_fetch_array($result,MYSQLI_ASSOC))
      {
        $game_array_upd[ $data['Date'] ][ $data['Alliance'] ][ $data['Id'] ]=array( //資料排序
          'Visitors'=>$data['Visitors'],
          'Home team'=>$data['Home team'],
          'First half'=>$data['First half'],
          'All_Game'=>$data['All_Game'],
          'Remarks'=>$data['Remarks'],
          'number'=>$data['number']);
      }
      $upd+=1;
    }else
    {//沒重複=>新增        
      if($ADD_quantity==0)
      {
        $sql_add="INSERT INTO `game_data`(`Date`, `Alliance`, 
                                          `Visitors`, `Home team`, 
                                          `First half`, `All_Game`, 
                                          `Remarks`, `number`) VALUES 
                                          ('".$out_url[$x][4]."','".$out_url[$x][1]."',
                                          '".$out_url[$x][2]."','".$out_url[$x][3]."',
                                          '".$out_url[$x][5]."','".$out_url[$x][6]."',
                                          '".$out_url[$x][7]."','".$out_url[$x][0]."')"; //新增用sql
        $sql_add_show="SELECT*FROM `game_data` WHERE (Date='".$out_url[$x][4]."' AND number='".$out_url[$x][0]."') ";//顯示用sql
        
        
      }else
      {
        $sql_add.=",('".$out_url[$x][4]."','".$out_url[$x][1]."',
                     '".$out_url[$x][2]."','".$out_url[$x][3]."',
                     '".$out_url[$x][5]."','".$out_url[$x][6]."',
                     '".$out_url[$x][7]."','".$out_url[$x][0]."')";
        $sql_add_show.="OR (Date='".$out_url[$x][4]."' AND number='".$out_url[$x][0]."')";
      }
      $ADD_quantity+=1;
      //echo "新增了".$ADD_quantity."筆資料";
    }
  }
  if($ADD_quantity!=0) //執行新增
  {
    mysqli_query($link,$sql_add);
    $result = mysqli_query($link, $sql_add_show);
    while($data=mysqli_fetch_array($result,MYSQLI_ASSOC))
    {
      $game_array_add[ $data['Date'] ][ $data['Alliance'] ][ $data['Id'] ]=array( //資料排序
        'Visitors'=>$data['Visitors'],
        'Home team'=>$data['Home team'],
        'First half'=>$data['First half'],
        'All_Game'=>$data['All_Game'],
        'Remarks'=>$data['Remarks'],
        'number'=>$data['number']);
    }
    
  }
  $game_array['ADD'][$ADD_quantity]=$game_array_add;  //  $ADD_quantity 新增的筆數
  $game_array['upd'][$upd]=$game_array_upd;           //  $upd 更新的筆數

  
  echo json_encode($game_array);
  
?>