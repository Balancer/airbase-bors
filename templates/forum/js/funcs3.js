function css_load(elem, value, id, def)
{
	document.getElementById(id).style[elem] = value
	
	if(def != value)
		createCookie(id+"."+elem, value, 180)
	else
		eraseCookie(id+"."+elem)
}

function createCookie(name,value,days)
{
	if (days) 
	{
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";

	document.cookie = name+"="+value+expires+"; path=/";
}
										
function readCookie(name, def) 
{
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++)
	{
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return def;
}

function eraseCookie(name) 
{
	createCookie(name,"",-1);
}

function createSelect(title, element, values, def)
{
	id = "\"id_select_"+element+"\""
	document.write("<label class=\"tune\" for="+id+">"+title+"</label> ")
	var id = null
	if(element.indexOf('.') >= 0)
	{
		element = element.split('.')
		id = element[0];
		element = element[1];
	}
	else
		id = 'body'
	cookie = readCookie(id+"."+element);
	if(!cookie)
		cookie = def
//	alert("<select id="+id+" onChange=\"css_load('"+element+"', this.value, '"+id+"', '"+def+"')\">")
	document.write("<select id="+id+" onChange=\"css_load('"+element+"', this.value, '"+id+"', '"+def+"')\">")
	values = values.split(";")
	for(var i in values)
	{
		var value = values[i]
		if(value.indexOf(":") == -1)
			name = value
		else
		{
			value = value.split(":")
			name = value[0]
			value = value[1]
		}
			
		document.write("<option value=\""+value+"\""+(cookie == value ? " selected=\"true\"" : "")+">"+name+"</option>")
	}
	
	document.write("</select><br />")
}

function inArray(array, value) {
	for(var i in array)
		if(array[i] == value) 
			 return true

    return false
}

function removeArrayItems(array, item) {
	var i = 0;
	while (i < array.length)
		if (array[i] == item)
			array.splice(i, 1)
		else
			i++
	return array;
}

function process_form(the_form)
{
	var element_names = new Object()
	element_names["req_message"] = "Сообщение"
		
	if (document.all || document.getElementById)
	{
		for (i = 0; i < the_form.length; ++i)
		{
			var elem = the_form.elements[i]
			if (elem.name && elem.name.substring(0, 4) == "req_")
			{
				if (elem.type && (elem.type=="text" || elem.type=="textarea" || elem.type=="password" || elem.type=="file") && elem.value=='')
				{
					alert("\"" + element_names[elem.name] + "\" это поле обязательно для заполнения в этой форме.")
					elem.focus()
					return false
				}
			}
		}
	}
	return true
}

function clientSize()
{
	var myWidth = 0, myHeight = 0;
	if( typeof( window.innerWidth ) == 'number' )
	{
		//Non-IE
		myWidth = window.innerWidth
		myHeight = window.innerHeight
	}
	else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) )
	{
		//IE 6+ in 'standards compliant mode'
		myWidth = document.documentElement.clientWidth
		myHeight = document.documentElement.clientHeight
	}
	else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) )
	{
		//IE 4 compatible
		myWidth = document.body.clientWidth
		myHeight = document.body.clientHeight
	}

	return new Array(myWidth, myHeight)
}

function is_numeric( mixed_var ) { return !isNaN( mixed_var ); }

function warn_icon(o, uid) {
if(top.me_is_coordinator)
	document.write('<a href="http://balancer.ru/admin/users/'+uid+'/warnings.html?object='+o+'"><img src="http://balancer.ru/img/web/skull.gif"></a>')
}

function ptrch(p,t) { $("#ptr"+p).load("/_bors/ajax/thumb-"+t+"?object=forum_post://"+p); }

function pdsh(p)
{
	o=$("#pfo"+p);
	if(o.html())
		o.toggle(100)
	else
	{
		o.html('Загружаю...');
		o.load("/_bors/ajax/post-footer-tools?object=forum_post://"+p);
	}
}

function addLoadEvent(func) {
   var oldonload = window.onload;
   if (typeof window.onload != 'function') {
       window.onload = func;
   }
   else {
       window.onload = function() {
           oldonload();
           func();
       }
   }
}
