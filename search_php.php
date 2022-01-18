<?php
  error_reporting(0);
  
  $link = mysqli_connect("localhost","root","","game");  //連線
  if(mysqli_connect_errno()) { 
    echo 'connect error!';
    exit;
  } //測試連線是否成功
  mysqli_set_charset($link,"utf8");  //設定萬國碼
  
  if($_POST['first']) { //如果是地一次抓資料
    $mydate=date("Y-m-d",mktime(0,0,0,date("m"),date("d"),date("Y"))); //現在時間
    $mydate_ago=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-7,date("Y")));//7日前時間
    $sql="SELECT * FROM `game_data` WHERE `Date` BETWEEN '".$mydate_ago."
          ' AND '".$mydate."' ORDER BY `Date` DESC";

     //聯盟下拉式選單
    $sql_allince="SELECT `Alliance` FROM `game_data`"; //印出所有聯盟
    $result = mysqli_query($link, $sql_allince);       //將結果丟入result
    while($data=mysqli_fetch_array($result,MYSQLI_ASSOC)) 
    {
      $my_Alliance[$data['Alliance']]=$data['Alliance']; //將聯盟丟進陣列並不重複
    }
    $output['my_Alliance']=$my_Alliance;
    
  }else if($_POST['date']=='' & $_POST['date_ago']=='' & $_POST['search_Alliance']=='' & $_POST['search_Visitors']=='' & $_POST['search_Home_team']=='') 
  {
    $mydate=date("Y-m-d",mktime(0,0,0,date("m"),date("d"),date("Y"))); //現在時間
    $mydate_ago=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-7,date("Y")));//7日前時間
    $sql="SELECT * FROM `game_data` WHERE `Date` BETWEEN '".$mydate_ago."
         ' AND '".$mydate."' ORDER BY `Date` DESC";
  }    //如果是收尋過來的話
  else
  {
    $sql="SELECT * FROM `game_data` WHERE ";
    $sql_num=0;
    if($_POST['date']=='' & $_POST['date_ago']!='')
    {
     $sql.= "`Date` = '".$_POST['date_ago']."'"; 
     $sql_num++;
    }else if($_POST['date_ago']=='' & $_POST['date']!='')
    {
      $sql.= "`Date` = '".$_POST['date']."'";
      $sql_num++;
    }else if($_POST['date']!=''& $_POST['date_ago']!='')
    {
      $sql.= "(`Date` BETWEEN '".$_POST['date_ago']."
       ' AND '".$_POST['date']."')";
       $sql_num++;
    }
    if($_POST['search_Alliance']!='')  //聯盟
    {
      if($sql_num>0){$sql.=" AND ";}
      $sql.="`Alliance` = '".$_POST['search_Alliance']."'";
    }
    if($_POST['search_Visitors']!='') //客隊
    {
      if($sql_num>0){$sql.=" AND ";}
      $sql.="`Visitors` LIKE '%".$_POST['search_Visitors']."%'";
    }
    if($_POST['search_Home_team']!='') //主隊
    {
      if($sql_num>0){$sql.=" AND ";}
      $sql.="`Home Team` LIKE '%".$_POST['search_Home_team']."%'";
    }
    $sql.=" ORDER BY `Date` DESC";
  }
    
    
  $result=mysqli_query($link,$sql);
  if(mysqli_num_rows($result)>0)
  { //有找到資料 
    while($data= mysqli_fetch_array($result)) //將資料轉換陣列key為欄位名
    {
      $game_array[ $data['Date'] ][ $data['Alliance'] ][ $data['Id'] ]=array( //資料排序
        'Visitors'=>$data['Visitors'],
        'Home team'=>$data['Home team'],
        'First half'=>$data['First half'],
        'All_Game'=>$data['All_Game'],
        'Remarks'=>$data['Remarks'],
        'number'=>$data['number']);
    }
  }else
  {
     $game_array='找不到';
  }
  
  $output['game_array']=$game_array;
  
  
  //回傳
  echo json_encode($output); 
?>

