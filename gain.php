<?php
    $url_error='';    //記錄錯誤訊息
    if($_SERVER['REQUEST_METHOD']=='POST') //如果我的網頁目前是POST的話
    {
      $url = $_POST['my_url'];  //將網址存入$url
      
      //正則表達式
      $my_text ='/(ht|f)tp(s?)\:\/\/[0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*(:(0-9)*)*(\/?)([a-zA-Z0-9\-\.\?\,\'\/\\\+&amp;%\$#_]*)?/';
      $mytest='/http(s?)\:\/\/[0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*(:(0-9)*)*(\/?)([a-zA-Z0-9\-\.\?\,\'\/\\\+&amp;%\$#_]*)?/';
      if(preg_match($my_text,$url,$hereArray))
      { //如果輸入的是網址
        $url_my_html='';
        $url_html=file($url);  //將$url網址裡的內容存入$url_html *是陣列
        $my_bool=false;        //用來偵測抓到的資料是否都存入$url_my_html
        //將抓到的陣列資料串接成$url_my_html
        for($x=0;$x<sizeof($url_html);$x++) 
        {
          $url_my_html.=$url_html[$x];
          if($x==(sizeof($url_html)-1)) //偵測串到最後一筆了沒有
          {
            $my_bool=true;
          }
        }
        if($my_bool) //已經資料串接完後
        {
          $url_my_html=str_replace(array("\r", "\n", "\r\n", "\n\r"),'',$url_my_html);  //將資料換行刪除
          preg_match_all('/<table[^>]+>(.*?)<\/table>/',$url_my_html,$out_url_1);   //抓取table表單裡面的內容
          for($e=0;$e<sizeof($out_url_1[0]);$e++)
          {
            print_r('第'.($e+1).'筆資料'.$out_url_1[0][$e].'<br>'); //印出[0]的陣列
          }
        }
        
      }else
      {
        //如果輸入的不是網址
       $url_error='您輸入'.$url.'不是網址 請從新輸入';
      }
    }
    Function clear_url()
    {
      global $url,$url_error;
      $url_error='';
      $url='';
      
    }
    function my_array()
    {
      $my_earray=array('開始','結束');
      $my_earray_1=array('開始','結束');
      $aee=array('紅','黃','藍');
      $my_array[]=$aee;
      array_push($my_earray_1,$aee);
        print_r($my_earray);
        print_r("<br>"."<br>"."<br>"."<br>");
        print_r($my_earray_1);
    }
    
    
  
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Title of the document</title>
  </head>

  <body>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
      <h3>請輸入要抓取的網址</h3>
      <input type="text" name="my_url" style="width:60%">
      <h4><?php echo $url_error; clear_url();?></h4>
      <input type="submit" value="抓取的按鈕">      
    </form>
    <h3><?php my_array(); ?></h3>
  </body>
</html>