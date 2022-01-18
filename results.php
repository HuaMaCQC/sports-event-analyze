 <?php
  $start_html='';
  $out_url=array();
  if(isset($_POST['php_code_start'])){ 
    if($_POST['php_code_start']=="開始分析")
    { 
      $start=MY_start();
      $start_html='<table>';
      foreach($start as $date_key=>$value)
      {
        $start_html.='
        <tr>
          <th id="date" colspan="7" height="50">'.$date_key.'</th>
        </tr>
        <tr>
            <th class="title" width=200px>聯盟</th>
            <th class="title" width=80px>賽事編號</th>
            <th class="title" width=200px>客隊</th>
            <th class="title" width=200px>主隊</th>
            <th class="title" width=60px>上半場</th>
            <th class="title" width=60px>全場</th>
            <th class="title" width=100px>備註</th>
          </tr>
        ';
        foreach($value as $alliance_key=>$value_2)
        {
          $start_html.='
          <tr>
            <td rowspan="'.(sizeof($value_2)+1).'">'.$alliance_key.'</td>
          </tr>';
          foreach($value_2 as $number_key=>$value_3)
          {
            $start_html.='
            <tr>
              <td>'.$number_key.'</td>';
            foreach($value_3 as $value_4)
            { 
              $start_html.='<td >'.$value_4.'</td>';   
            }              
            $start_html.='</tr>
            ';
          }
        }
      }
      $start_html.=  '</table>';
      
      //sql練習
      $link = mysqli_connect("localhost","root","","game");  //連線
      if(mysqli_connect_errno()) {
      echo '與資料庫連線失敗';
      exit;} //測試連線是否成功
      if(!mysqli_set_charset($link,"utf8")){echo '設定失敗';} //設定萬國碼
      
      
      
      
      if(mysqli_query($link,$sql)) //執行一次sql指令
      {
        $debug='成功';     
      }else
      {
        $debug=mysqli_error($link); //失敗執行
      }     
    }
  }
  function MY_Array_insert($MY_array,$string,$int) //在陣列中差一個值(輸入陣列,插入字串,插入第至第幾個,輸出陣列)
  {
    $output_array=$MY_array;                      //複製一份$MY_array;丟入$output_array
    $temporarily_string=$MY_array[$int-1];        //將要插入的位置的資料暫存在$temporarily_string
    for($zzz = $int-1;$zzz<sizeof($MY_array)+1;$zzz++)  //從$int起始點開始插入
    { 
         //將陣列中更改的值先暫存
      if($zzz==$int-1)                       //如果執行是第一次
      {
        $output_array[$zzz]=$string;            
        $temporarily_string=$MY_array[$zzz];   //將暫存資料往裡面送        
      }
      else if($zzz>sizeof($MY_array)-1)      //如果他執行到最後一個陣列的話
      {
        $output_array[]=$temporarily_string;     //將最後一筆資料存入     
      }
      else                                    
      {
        $output_array[$zzz]=$temporarily_string;        
        $temporarily_string=$MY_array[$zzz];      //將暫存資料往裡面送     
      }
      
    }
    return $output_array;
  }
  
  function MY_array_del($my_input_array,$start_int)  //在陣列中刪除指定的一個值(陣列,刪除的位置)
  {
    $output_array=$my_input_array;                  //將陣列複製一份
    for($zzz=$start_int;$zzz<sizeof($my_input_array);$zzz++) //將陣列的後一份丟置前一份
    {
      $output_array[$zzz-1]=$my_input_array[$zzz];
    }
    array_pop($output_array);                         //刪除最後一個
    return $output_array;
  }
  function MY_start(){  
    $url_my_html='';  
    //$url='http://best9458.cc/competition.php';
    $url = './demo/my_game.html';
    $url_html=file($url);  //將$url網址裡的內容存入$url_html
    for($x=0;$x<sizeof($url_html);$x++) 
    {
      $url_my_html.=$url_html[$x]; //將抓到的陣列資料串接成$url_my_html
    }
    $url_my_html=str_replace(array("\r", "\n", "\r\n", "\n\r"),'',$url_my_html);  //將資料換行刪除
    $my_pattern='/<tr\sheight\=\"40\">(.*?)<\/tr>/'; //條件式<tr height="40"> .....</tr>
    preg_match_all($my_pattern,$url_my_html,$preg_url);   //抓取<tr height="40">表單裡面的內容
    
    global $out_url;  //分析的結果
    
    for($x=0;$x<=sizeof($preg_url[1])-1;$x++)  //將$preg_url[1]裡的資料再次抽離
    { 
      preg_match_all('/<td[^>]+>(.*?)<\/td>/',$preg_url[1][$x],$preg_url_2); //將$preg_url[1][$x] 以方法<td...>(...)</td>的方式抽離
      
      array_push($out_url,$preg_url_2[1]); //將每一場比賽存入out_url
    }
    
    $output_game=array(); //排序好 的輸出
    $game_date=array();  //暫存日期
    $gmae_alliance=array(); //暫存聯盟
    for($i=0;$i<sizeof($out_url);$i++)
    {
      $Duel_name=explode('<font style="color:#900;">對</font> ',$out_url[$i][2]); //客隊主隊名子拆開存入$Duel_name
      $out_url[$i]=MY_Array_insert($out_url[$i],$Duel_name[0],4);       //將客隊丟入適當的位置
      $out_url[$i]=MY_Array_insert($out_url[$i],$Duel_name[1],5);       //將主隊丟入適當的位置
      $out_url[$i]=MY_array_del($out_url[$i],3);                        //刪除主隊客隊欄位
      $out_url[$i][3]=trim($out_url[$i][3]," ") ; //刪除字首字尾空白
      
      
    
      $x=0;
      $c=0;
      if($i == 0) //如果是第一筆的話
      {
        $game_date[0]=$out_url[$i][4]; //將第一筆日期存入
        $gmae_alliance[0]=$out_url[$i][1]; //將第一筆聯盟存入
        $output_game=array($out_url[$i][4]=>array($out_url[$i][1]=>
                        array($out_url[$i][0]=>
                          array('guest_team'=>$out_url[$i][2],
                                'main_team'=>$out_url[$i][3],
                                'half of a game'=>$out_url[$i][5],
                                'all of a game'=>$out_url[$i][6],
                                'Remarks'=>$out_url[$i][7]))));
      }
      else  //如果不是第一筆的話
      {
        do{                               
          if($game_date[$x]==$out_url[$i][4]) //收尋是否有日期相同的
          {                                   //有相同
            do{
              if($gmae_alliance[$c]==$out_url[$i][1]) //收尋是否有聯盟相同的
              {                    //相同日期跟相同的聯盟
                $output_game[$game_date[$x]][$gmae_alliance[$c]][$out_url[$i][0]]=array('guest_team'=>$out_url[$i][2],
                                'main_team'=>$out_url[$i][3],
                                'half of a game'=>$out_url[$i][5],
                                'all of a game'=>$out_url[$i][6],
                                'Remarks'=>$out_url[$i][7]);
                $c=sizeof($gmae_alliance); //跳出迴圈
              }
              else if($c==(sizeof($gmae_alliance)-1)&!($gmae_alliance[$c]==$out_url[$i][1])) //收尋到最後一筆沒有相同聯盟
              {
                $gmae_alliance[]=$out_url[$i][1];
                $output_game[$game_date[$x]][$out_url[$i][1]]=array(
                                                $out_url[$i][0]=> array(
                                                    'guest_team'=>$out_url[$i][2],
                                                    'main_team'=>$out_url[$i][3],
                                                    'half of a game'=>$out_url[$i][5],
                                                    'all of a game'=>$out_url[$i][6],
                                                    'Remarks'=>$out_url[$i][7]
                                                    ));
                $c++;
              }
              else
              {
                $c++;
              }
            }while($c<sizeof($gmae_alliance));
            $x=sizeof($game_date); //跳出迴圈
          }
          else if($x==(sizeof($game_date)-1)&!($game_date[$x]==$out_url[$i][4])) //收尋到最後一筆沒有相同日期
          {
            $gmae_alliance=array($out_url[$i][1]);
            $game_date[]=$out_url[$i][4];
            $output_game[$out_url[$i][4]]=array($out_url[$i][1]=>
                        array($out_url[$i][0]=>
                          array('guest_team'=>$out_url[$i][2],
                                'main_team'=>$out_url[$i][3],
                                'half of a game'=>$out_url[$i][5],
                                'all of a game'=>$out_url[$i][6],
                                'Remarks'=>$out_url[$i][7])));
            $x++;
          }
          else
          { 
            $x++;
          }
        }while($x<sizeof($game_date));
      }
    }
    return $output_game;
  }
?>


<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>分析 你好厲害 賽程資料</title>
    
    <style>
      table{
        border-collapse:collapse;
      }
      #date{
       background-color:#196AA9;
       font-size : 24px;
       color : rgb(255,255,255);
      }
      .title{
        background-color:#C1DFF6;
        height:40px;
        font-size:18px;
      }
      td{
        border-bottom: 1px solid #CCE9FF;
        text-align: center;
        height : 30px;
      }
      #my_head{
        height:200px;
        border: 3px solid red;
        
      }
      #my_table{
        position:relative;
        left:35%;
        width:50%;
        border: 3px solid green;
      }
      #left_mid{
      position: absolute;
      left: 15%;
      top: 0;
      height:900px;
      width:19%;
      border: 3px solid black;
      }
      p,li
      {
       display: inline; 
      }
    </style>
  </head>
  <body style="margin:0;">
    <p><?php print_r($debug);?></p>
    <div id="my_head" style="text-align:center;>
      <form method="post" action="results" >
        <p>
          <img src="/img/logo.png" width="220">
        </p>
        <p style="width:70px;">帳號:</p>
          <p style="width:167px;">
            <input name="username_login" id="username_login" type="text">
          </p>
        <p style="width:50px; text-align:right;">密碼</p>
        <p style="width:167px;">
          <input name="password_login" id="password_login" type="text">
        </p>
        <p style="width:67px;">
          <button type="button"><img src="img/login_a_bg.jpg"></button>
        </p>
        <p style="width:114px;">
          <button type="button"><img src="img/join_us_img.png" width="114" height="31"></button>
        </p>
        <p style="width:110px; text-indent:10px; color:red;">
          忘記密碼?
        </p>
      </form>
      <table  style="">
        <tr>
          <td>
            <ul>
              <li>網站首頁<li>
              <li>會員中心<li>
              <li>運彩投注<li>
              <li>賽事結果<li>
              <li>客服留言<li>
              <li>常見問題<li>
              <li>來去你好厲害<li>
            </ul>
          </td>
        </tr>
      </table>
   </div>
     
    <div style="position: -webkit-sticky; /* Safari */position: sticky; top:0;
         height:0; border: 3px solid black;">
        <div id="left_mid"></div>
      
    </div>
    
    <div id="my_table">
      <form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
        <input type="submit" value="開始分析" name="php_code_start">
      </form>
      <P> <?PHP echo($start_html); ?></P>
   
    </div>
    
  </body>
</html>