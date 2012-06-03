<?php 
require 'facebook-php-sdk/src/facebook.php';
require 'includes/function.php';

define('FACEBOOK_APP_ID', '/*insert app id*/');
define('FACEBOOK_SECRET', '/*insert facbook secret*/');
define('DISPLAY', 'page'); 
define('SCOPE', 'user_photos,publish_stream');

$redirect_url = 'http://apps.facebook.com/friends_cube/';
$oauth_url = 'http://www.facebook.com/dialog/oauth/?client_id=' . FACEBOOK_APP_ID . '&redirect_uri=' . $redirect_url . '&type=user_agent&&display=' . DISPLAY . '&scope=' . SCOPE;
$accessToken='';
$userID='';

$facebook = new Facebook(array(
  'appId' => FACEBOOK_APP_ID,
  'secret' => FACEBOOK_SECRET,
  'cookie' => true,
  'fileUpload' => true,
));

$session = $facebook->getSession();
$me = null;
         
//Try to make a Facebook request.  If its good, we have a valid session.
//Otherwise, start the authorization process
if($session)
{//we have session info
	try 
	{//try
     	$params = array('access_token' => $session['access_token']);
        $uid = $facebook->getUser();
        $me  = $facebook->api('/me', $params);       
    }//try
    catch (FacebookApiException $e) 
    {//catch
    	 doAuth($oauth_url);
    }//catch
    
   $accessToken = $params['access_token'];
}//we have session info
else
{//no session info, do auth
      doAuth($oauth_url);
}//no session info, do auth

function doAuth($url)
{//doAuth
	//header('Location: '.$url);
	$newLoc = "<script type=\"text/javascript\">\ntop.location.href = \"$url\";\n</script>";
    echo $newLoc;
	exit();
}//doAuth
 
?>
 
 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link href="css/style.css" rel="stylesheet" type="text/css" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="js/javaS.js"></script>
<script type="text/javascript" src="js/jquery-1.4.3.js"></script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Friends Cube</title>
<script type="text/javascript"> 

if(!$.browser.webkit) {
    alert("Your browser is not appropriate for this content. Use Google Chrome or Safari insted")
}
	
 
var rCub=300;var xPos=100,yPos=100;
window.document.onmousemove  = getMouseXYPos;
function getMouseXYPos(e ) {
	 xPos= e.clientX-300;yPos=e.clientY-300;}

$(function(){	
 	var properties = ['', 'Webkit', 'Moz',
                  'Ms', 'O']; 
	var propN =0;
	for(var i=0,j=properties.length;i<j;i++){
  		if(typeof $('#cube')[0].style[properties[i]+"Transform"] !== 'undefined'){
   		 prop = properties[i];
    	break;
  	 	}
	}
	
	var mouse = { start : {}},
	    touch = document.ontouchmove !== undefined,
	    viewport = {
			x: -10, 
			y: 20, 
			el: $('#cube')[0],
			move: function(coords){
				if(coords) {	
					if(typeof coords.x === "number") this.x = coords.x;
					if(typeof coords.y === "number") this.y = coords.y; 
				} 
				this.el.style[prop+"Transform"] = "rotateX("+this.x+"deg) rotateY("+this.y+"deg)" ;				 
			},reset: function() {
				this.move({x: 0, y: 0});
		}};		
		viewport.duration = function() {
			var d = touch ? 50 : 500;
			viewport.el.style[prop+"TransitionDuration"]= d + "ms";
			return d;
	}();
	var rotate= true;	 
	$(document).ready( rot= function () {
		if( rotate){		
			viewport.move({x: viewport.x - yPos/10});
			viewport.move({y: viewport.y + xPos/10}); 
			setTimeout('rot()',500);}})});
</script>
<?php 
if(isset($_COOKIE['friendsoncube'])){
	$coocky=isset($_COOKIE['friendsoncube']) ? $_COOKIE['friendsoncube'] : ""; 
}	else{
	$_COOKIE['nfriends']=60;$_COOKIE['imgIdx']="0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17";
}	
// login or logout url will be needed depending on current user state.
if(!$me){$loginUrl = $facebook->getLoginUrl();} 
?>
<div id="fb-root"></div>
 
  <script>
      window.fbAsyncInit = function() {
        FB.init({
          appId   : '<?php echo $appid=$facebook->getAppId(); ?>',
          session : <?php echo json_encode($session); ?>, // don't refetch the session when PHP already has it
          status  : true, // check login status
          cookie  : true, // enable cookies to allow the server to access the session
          xfbml   : true // parse XFBML
        });

        // whenever the user logs in, we refresh the page
        FB.Event.subscribe('auth.login', function() {
          window.location.reload();
        });
      };

      (function() {
        var e = document.createElement('script');
        e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
        e.async = true;
        document.getElementById('fb-root').appendChild(e);
      }());
    </script> 
    <?php  	 
	if ($me):
		$url='https://graph.facebook.com/me/friends?access_token='.$accessToken;
		$x=get_data($url);
		$y=json_decode($x,true);	
		$out ='<div id="viewport"><div id="experiment"><div id="cube"></div></div></div>';												 
		$out.='<form name="picture" method="post">';
		$out.='<table id ="table">';
		$out.='<tr>';
		$out.='<td>&nbsp;</td>';
		for($i=0;$i<10;$i++){
			$out.='<td class="checkline">';
			$out.='<input onclick="changeAllH('.$i.');" type="checkbox" name="imgh'.$i.'" value="'.$i.'" />';
			$out.='</td>';
		}
		$out.= '</tr>';
		for($i=0;$i<count( $y['data']);$i++){
			$photo="http://graph.facebook.com/".$y['data'][$i]['id']."/picture?type=large";	 
			$out.=$i%10==0 ? '<tr><td class="checkline"><input onclick="changeAll('.$i.');" type="checkbox" name="imgs'.$i.'" value="'.$i.'" /></td>' : '';
			$out.='<td id="td'.$i.'" class="im" style=" background-image:url(http://graph.facebook.com/'.$y['data'][$i]['id'].'/picture);';
			$out.='background-repeat:no-repeat; background-position:center"  title="'.$y['data'][$i]['name'].'">'; 
			$out.='<img class="invisible"   name="'.$y['data'][$i]['id'].'_'.$y['data'][$i]['name'].'" src="'.$photo.'">';
			$out.='<input class="notcheckline" onclick="bg('.$i.');" type="checkbox" name="img'.$i.'" value="'.$i.'" />';	 
			$out.='</td>';
			$out.=$i%10==9 ? '<td>&nbsp;</td></tr>' : '';		  
		}
		$out.=  '<input id="sub" type="submit" onclick="checkAll('.$i.');" value="Friends On Cube - Save selection" /> </table></form>';
   		echo $out. '<script type="text/javascript"> setCheckBoxes("'. $_COOKIE["imgIdx"].'",'.$_COOKIE['nfriends'].')</script>';	 
	 endif  ?>  
  </body>  
</html> 
<script type="text/javascript">
$(document).ready(setCube=function(){ 
	
	var pict=$(".im img");
	
	if($(pict[pict.length-1]).height()>0){
 		 var imgs = $("input.notcheckline"),imgsId=new Array(),setImg,tmp,j=0,out="",a,b,c,d,isB,
		 num=new Array("one","two","tree","four","five","six") ;
 
  		for(i=0;i<imgs.length;i++) 
	  		if(imgs[i].checked)imgsId[j++]=imgs[i].value;	   
		 
		for(i=0;i<6;i++){ 
			setImg= imgsId.splice(Math.floor(Math.random()* imgsId.length ),1); 
			a=parseInt($(pict[ setImg]).width());
			b=parseInt($(pict[ setImg]).height()); 
			c=rCub/b;
			d=rCub/a;
			isB=a>b;
        	out+='<div  class="face '+num[i]+'">';
			out+='<img src="http://graph.facebook.com/'+pict[setImg].name.split("_")[0]+'/picture?type=large" '; 
			out+='style="width:'+(isB?(a*c):rCub)+'px;height:'+(isB?rCub:(b*d))+'px;';
			out+='margin: -'+ (isB?0:(b-a)*d*0.5)+'px 0 0 -'+ (isB?(a-b)*c*0.5:0)+'px" title="'+pict[setImg].name.split("_")[1]+'" /></div>' ;
	 	} 
		$("#cube").html(out);		
	}	
	else{
		setTimeout('setCube()',1000);
	}
});
$("#viewport").click( function(){setCube();});


 //$("#cube .face")[6].css({properties[n]:"rotateX(-90deg) rotate(180deg) translateZ(150px) ;"})
</script>

  