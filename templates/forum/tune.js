function css_load(elem, value)
{
	document.getElementById("body").style[elem] = value
	createCookie(elem, value, 3)
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

function createSelect(title, element, values)
{
	id = "\"id_select_"+element+"\""
	cookie = readCookie(element);
	document.write("<label class=\"tune\" for="+id+">"+title+"</label> ")
	document.write("<select id="+id+" onChange=\"css_load('"+element+"', this.value)\">")
	values = values.split(" ")
	for(var i in values)
		document.write("<option value=\""+values[i]+"\""+(cookie == values[i] ? " selected=\"true\"" : "")+">"+values[i]+"</option>")
	document.write("</select><br />")
}

function onLoad()
{
	cookie_vars = "fontSize fontFamily".split(" ");
	for(var i in cookie_vars)
	{
		name = cookie_vars[i]
		value = readCookie(name)
		if(value)
			css_load(name, value)
	}
}

function inArray(array, value)
{
	for(var i in array)
		if(array[i] == value) 
			 return true

    return false
}
