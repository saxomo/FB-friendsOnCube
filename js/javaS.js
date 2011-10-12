//set old setting from cookie
function setCheckBoxes(x,n){
	var check=x .split(","); 
	for(i=0;i<n ;i++) { $('#td'+ i ).css("opacity","0.7");}
	for(i=0;i<check.length  ;i++){ 
		document.picture['img'+check[i]].checked=true ;
		$('#td'+ check[i]).css({"border": "solid","borderWidth":"thick"   ,"borderColor":"black","opacity":"1"});
	} 
}
//set css if horizontal inputs are checked
function changeAll(x){
	var is =document.picture['imgs'+x].checked;
 	for(i=0;i<10&&document.picture['img'+(x+i)];i++) {
		$('#td'+(x+i)).css({"border": "solid","borderWidth":is?"thick":"thin" ,"borderColor": is ? "black":"white","opacity":(is ?"1":"0.7")});
		document.picture['img'+(x+i)].checked= is?true:false;
	  } setCube();
}
//set css if vertical inputs are checked
function changeAllH(x){
	var is =document.picture['imgh'+x].checked ;
	for(i=0;i<10&&document.picture['img'+(10*i+x )];i++){ 
		document.picture['img'+(10*i+x )].checked=is?true:false;
		$('#td'+ (10*i+x) ).css({"border": "solid","borderWidth":is?"thick":"thin" ,"borderColor": is ? "black":"white","opacity":(is ?"1":"0.7")});
	}setCube();
}
//set css if input is checked
function bg(x){
	var is =document.picture['img'+x].checked ;
	$('#td'+ x).css({"border": "solid","borderWidth":is?"thick":"thin", "borderColor":is?"black":"white","opacity":(is?"1":"0.7")});
	setCube();	 
}
//this
function uncheckAll(field){
	for (i = 0; i < field.length; i++)
	field[i].checked = false ;
}
//set cookie when submit
function checkAll(x){var a ="";  
	var d = $('.im img');   
	var picture="";
	var idx="";
	for(i=0;i<x;i++){  
		 if(document.picture['img'+i].checked){
			 a+= (d[i ]).name +","+$( d[i ]).width()+","+$(d[i ]) .height()+",";
			 idx+=i+",";
			}  
	}Set_Cookie("imgIdx",idx,7200) ;
	 Set_Cookie("friendsoncube",a,7200) ;
	 Set_Cookie("nfriends",x,7200) ;
}
function getCheckedValue(radioObj) {
	if(!radioObj)
		return "";
	var radioLength = radioObj.length;
	if(radioLength == undefined)
		if(radioObj.checked)
			return radioObj.value;
		else
			return "";
	for(var i = 0; i < radioLength; i++) {
		if(radioObj[i].checked) {
			return radioObj[i].value;
		}
	}
	return "";
}
//this
function Set_Cookie( name, value, expires, path, domain, secure ){
	// set time, it's in milliseconds
	var today = new Date();
	today.setTime( today.getTime() );
	if ( expires ){
	expires = expires * 1000 * 60 * 60 * 2;
	}
	var expires_date = new Date( today.getTime() + (expires) );

	document.cookie = name + "=" +escape( value ) +
	( ( expires ) ? ";expires=" + expires_date.toGMTString() : "" ) +
	( ( path ) ? ";path=" + path : "" ) +
	( ( domain ) ? ";domain=" + domain : "" ) +
	( ( secure ) ? ";secure" : "" );
}
//this
function Get_Cookie( check_name ) {
	
	var a_all_cookies = document.cookie.split( ';' );
	var a_temp_cookie = '';
	var cookie_name = '';
	var cookie_value = '';
	var b_cookie_found = false;

	for ( i = 0; i < a_all_cookies.length; i++ )
	{
		a_temp_cookie = a_all_cookies[i].split( '=' );
		cookie_name = a_temp_cookie[0].replace(/^\s+|\s+$/g, '');
		if ( cookie_name == check_name )
		{
			b_cookie_found = true;
			if ( a_temp_cookie.length > 1 )
			{
				cookie_value = unescape( a_temp_cookie[1].replace(/^\s+|\s+$/g, '') );
			}
			return cookie_value;
			break;
		}
		a_temp_cookie = null;
		cookie_name = '';
	}
	if ( !b_cookie_found )
	{
		return null;
	}
}