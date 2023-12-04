function dosub(subm) 
{ 
	if (subm != "") 
	{ 
		self.location=subm; 
	} 
}

function OpenSearcher(type)
{
	window.open('scripts.php?mode=search&type=' + type +'','help','resizable,width=640,height=320,left=50,top=75,scrollbars=yes');
}

function getXmlHttpRequestObject() 
{
	if (window.XMLHttpRequest) 
	{
		return new XMLHttpRequest();
	} 
	else if(window.ActiveXObject) 
	{
		return new ActiveXObject("Microsoft.XMLHTTP");
	} 
	else 
	{
		alert("Incompatible web browser.");
	}
}

var searchReq = getXmlHttpRequestObject();
var updateReq = getXmlHttpRequestObject();

var fade = function(el,from,to,time){
  typeof el == 'string' && (el = document.getElementById(el));
  time = time || 500;
  var steps = time/25;
  var step = (to-from)/steps;
  var inter = setInterval(function(){
    from += step;
    el.style.opacity = from/100;
    el.style.filter = 'alpha(opacity=' + from + ')';
    Math.abs(from-to) < 1 && clearInterval(inter)
  },time/steps);
};

function isDirty(obj)
{
	obj.style.border = "1px solid #c00";
	obj.style.background = "#fef";
	document.getElementById("update_status").innerHTML="<span style='font-size:13px; font-weight:bold; color:#00f;'>Modified!</span>";
}
function AJAXSaveData(obj)
{
	var name	= obj.name;
	var myArr = name.split('|');
	var table	= myArr[0];
	var field	= myArr[1];
	var from	= document.getElementById(field).value;
	var to 		= obj.value;

	if( from != to )
	{
		var qString = "?table=" + table + "&field=" + field + "&from=" + from + "&to=" + to;
	
		if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		}
		else {// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		
		xmlhttp.onreadystatechange=function() {
			if (xmlhttp.readyState==4 && xmlhttp.status==200) {
				document.getElementById("update_status").innerHTML=xmlhttp.responseText;
			}
		}
		
		//var setting = document.getElementById('toggle').name;
		xmlhttp.open("GET","_admin/eq2AdminAjax.php" + qString, true);
		xmlhttp.send();
		fade('update_status',100,0,5000);
		document.getElementById(field).value = to;

		obj.style.border = "";
		obj.style.background = "";
	}
}
function ToggleEditorSettingsAJAX(obj)
{
	var name	= obj.name;
	var myArr = name.split('|');
	var table	= myArr[0];
	var field	= myArr[1];
	var from	= document.getElementById(field).value;
	var to = obj.value == 'on' ? 1 : 0;
	
	if( from != to )
	{
		var qString = "?table=" + table + "&field=" + field + "&from=" + from + "&to=" + to;
		
		if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		}
		else {// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		
		xmlhttp.onreadystatechange=function() {
			if (xmlhttp.readyState==4 && xmlhttp.status==200) {
				document.getElementById("update_status").innerHTML=xmlhttp.responseText;
			}
		}
		
		//var setting = document.getElementById('toggle').name;
		xmlhttp.open("GET","_admin/eq2AdminAjax.php" + qString, true);
		xmlhttp.send();
		fade('update_status',100,0,5000);
		document.getElementById(field).value = to;
	}
}

function handleSearchSuggest() 
{
	if (searchReq.readyState == 4) 
	{
		var ss = document.getElementById('search_suggest')
		ss.innerHTML = '';
		var str = searchReq.responseText.split("\n");
		for(i=0; i < str.length - 1; i++) 
		{
			var suggest = '<div onmouseover="javascript:suggestOver(this);" ';
			suggest += 'onmouseout="javascript:suggestOut(this);" ';
			suggest += 'onclick="javascript:setSearch(this.innerHTML);" ';
			suggest += 'class="suggest_link">' + str[i] + '</div>';
			ss.innerHTML += suggest;
		}
	}
}

function suggestOver(div_value) 
{
	div_value.className = 'suggest_link_over';
}

function suggestOut(div_value) 
{
	div_value.className = 'suggest_link';
}

function setSearch(value) 
{
	document.getElementById('txtSearch').value = value;
	document.getElementById('search_suggest').innerHTML = '';
}
