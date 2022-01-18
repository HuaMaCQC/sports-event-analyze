<?php
  if(isset($_POST['game_form'])) //判斷收尋與分析是否有post東西過來
  {
    $game_form=json_decode($_POST['game_form'],true);
  }else
  {
    //第一次近來
    echo '<form method="post" action="./search_php.php" id="myform">
            <input type="hidden" value="first" name="first">
          </form>
          <script>document.getElementById("myform").submit();</script>';
   
  }
  if(isset($_POST['alliance']))
  {
      
    $Alliance_form=json_decode($_POST['alliance'],true);//解碼$_POST['alliance']資料
  }
?>
<!DOCTYPE html>
<html>
  <head>
  <script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.2.1.min.js"></script>
    <meta charset="UTF-8">
    <title>你好厲害</title>
    <style>
      p
      {
       display: inline; 
      }
    </style>
  </head>

  <body>
    
    <table width="50%" border="0" cellspacing="0" cellpadding="0" align="center">
      <tr>
        <th>
          <table width="100%">
            <tr>
              <td width="100%">
              
                <form method="post" action="./search_php.php" onsubmit="secarch()">
                  <p>日期區間:<input id="date_ago_id" type="date" name="date_ago">~<input id="date_id" type="date" name="date"></p>
                  <p>聯盟:
                  <select  name="search_Alliance" id="search_Alliance_id">
                  <option value="">請選擇聯盟</option>
                  <?php echo $Alliance_form;?>
                  </select>
                  </p><br>
                  <p>客隊:<input type="text" name="search_Visitors" id="search_Visitors_id"></p>
                  <p>主隊:<input type="text" name="search_Home_team" id="search_Home_team_id"></p> 
                  <input type="submit" value="尋找" name="search">
                </form>
                <form method="post" action="./data_php.php">
                  <input type="submit" value="分析">
                </form>
              </td>
            </tr>
          </table>
        </th>
      </tr>
      <tr>
        <td>
          <table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td width="70%" align="left" style="height:30px; background:url(../img/ttb.jpg) 0% 0% repeat-x; line-height:30px;">
                <img src="img/Basketball.png" border="0" align="absmiddle">
                <img src="img/Football.png" border="0" align="absmiddle">
                <img src="img/Baseball.png" border="0" align="absmiddle">
                <font style="font-size:14px;font-weight:bold; color:#FFF; padding-left:10px; line-height:30px;">（僅顯示最近七日賽事結果）</font>
              </td>
              <td width="50%" align="center" style="font-size:14px;font-weight:bold;color:#F30;height:30px; background:url(../img/ttb.jpg)0% 0% repeat-x; line-height:30px;">※ 取消比賽</td>
            </tr>
          </table>  
        </td>  
      </tr>
      <tr>
        <td>
          <table  width="100%" border="1" cellpadding="0" cellspacing="0" style="font-size:13px; border-collapse:collapse; text-align: center;" bordercolor="#ccc">
            <tr height="40">
                <th width="10%" align="center" style="color:#111; background:#d2ebf4;">賽事編號</th>
                <th width="20%" align="center" style="color:#111; background:#d2ebf4;">聯盟</th>
                <th width="23%" align="center" style="color:#111; background:#d2ebf4;">對戰隊伍<br>(客隊 對 主隊)</th>
                <th width="15%" align="center" style="color:#111; background:#d2ebf4;">比賽日期</th>
                <th width="10%" align="center" style="color:#111; background:#d2ebf4;">上半場</th>
                <th width="10%" align="center" style="color:#111; background:#d2ebf4;">全場</th>
                <th width="12%" align="center" style="color:#111; background:#d2ebf4;">備註</th>
            </tr>   
            <p id="game_form"></p>
          </table>
        </td>
      </tr>  
    </table>
  </body>
</html>
<script>
  
  getCookie("search_Visitors_cookie", "search_Visitors_id");
  getCookie("date_ago_cookie", "date_ago_id");
  getCookie("date_cookie", "date_id");
  getCookie("search_Alliance_cookie", "search_Alliance_id");
  getCookie("search_Home_team_cookie", "search_Home_team_id");
  
  var button_tr=document.querySelectorAll(".button_tr"); //抓取class=button_tr
  for(var i=0; i<button_tr.length;i++)                   //抓取的東西為陣列資料
  {
    button_tr[i].addEventListener("click", function(){   //給予class=button 的obj 給予click事件
      var tr_id = $(this).attr("id");                    //獲取典籍的id名
      $("."+tr_id).toggle("fast");                       //將class名跟id 名稱一樣的物件 給予點即顯示消失
    });
  }
  
  function secarch() //按下收尋
  {
    var date_ago=document.getElementById("date_ago_id").value;   //獲取日期~日期
    var mydate = document.getElementById("date_id").value;       
    if(date_ago!="" & mydate=="" )                         //如果兩個日期一個有值一個沒值
    {                                                      //表示要找尋當天的範圍，將有值得val傳入沒值的
      document.getElementById("date_id").value=date_ago;
      
    }else if (mydate!=""& date_ago=="")
    {
      document.getElementById("date_ago_id").value=mydate;
    }
    else if (mydate!="" & date_ago!="")                                //如果兩個都有值
    {
      if((Date.parse(date_ago)).valueOf()>(Date.parse(mydate)).valueOf())      //比大小將 時間(之前) 擺入date_ago內
      {                                                                        
        document.getElementById("date_ago_id").value=mydate;
        document.getElementById("date_id").value=date_ago;
      }
    }
    
    setCookie("search_Visitors_cookie", "search_Visitors_id");
    setCookie("date_ago_cookie", "date_ago_id");
    setCookie("date_cookie", "date_id");
    setCookie("search_Alliance_cookie", "search_Alliance_id");
    setCookie("search_Home_team_cookie", "search_Home_team_id");
    

    
    
    
    
  }
  function setCookie(cname,id) //存cookie
  {
    var x = document.getElementById(id).value;
    document.cookie = cname +"=" + x ;
  }
  
  function getCookie(cname,id) {     //抓出cookie
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            var getcn = c.substring(name.length, c.length);
            $("#"+id).val(getcn);
            document.cookie = cname +'=""; expires=Thu, 01 Jan 1970 00:00:00 UTC;';
            return;
        }
    } 
    return;
  }
</script>