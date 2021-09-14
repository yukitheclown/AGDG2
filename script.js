var mouse_x = 0;
var mouse_y = 0;
var drag_diff_x = 0;
var drag_diff_y = 0;
var dragging_reply = false;
var curr_post_options_arrow = null;

window.onload = function(){

	var reply = document.getElementById("reply_container_header");
	reply.addEventListener("mousedown", reply_mousedown, false);

	var reply_close_button = document.getElementById("reply_container_close");
	reply_close_button.addEventListener("mousedown", reply_close, false);

	window.addEventListener("mouseup", mouseup, false);
}

function mouseup(e){

	if(dragging_reply){
		dragging_reply = false;
		var reply = document.getElementById("reply_container_header");
		document.body.style.cursor = "default";
	}
}

function post_button_timer(){

	var button = document.getElementById("reply_container_post_button");

	button.value -= 1;

	if(button.value <= 0)
		button.value = "Post";
	else
		window.setTimeout(post_button_timer, 1000);

}

function set_mouse_pos(e){

	var doc = document.documentElement;

	mouse_x = e.clientX + (window.pageXOffset || doc.scrollLeft) - (doc.clientLeft || 0);
	mouse_y = e.clientY + (window.pageYOffset || doc.scrollTop) - (doc.clientTop || 0);

	if(dragging_reply){

		var reply = document.getElementById("reply_container");
		
		reply.style.left = (mouse_x - drag_diff_x) + 'px';
		reply.style.top = (mouse_y - drag_diff_y) + 'px';
	}

	var hover_post = document.getElementById("hover_post");
	var hover_post_style = window.getComputedStyle(hover_post);

	if(hover_post_style.display != "none"){

		hover_post.style.left = mouse_x + 30 + 'px';
		hover_post.style.top = (mouse_y - (parseInt(hover_post_style.height)/2)) + 'px';

	}
}

window.onscroll = function(e){

	set_mouse_pos(e);
}

window.onmousemove = function(e){

	set_mouse_pos(e);
}

function insert_post_into_elem(postID, elem){

	var post = document.getElementById("p" + postID);

	if(post){
		elem.innerHTML = post.innerHTML;
		return;
	}

	var xml_http = new XMLHttpRequest();
	
	xml_http.onreadystatechange = function(){

		if(xml_http.readyState == 4 && xml_http.status == 200){

			elem.innerHTML = xml_http.responseText;
		}
	}

	xml_http.open("GET", "get_post.php?post_id=" + postID);
	xml_http.send();
}

function click_post_quote(e, postID){

	var into = e.target.parentNode.firstChild;
	var style = window.getComputedStyle(into);

	if(style.display == "none"){

		insert_post_into_elem(postID, into);
		into.style.display = "table";
	
	} else {

		into.innerHTML = "";
		into.style.display = "none";
	}
}

function expand_video(e, width){

	var style = window.getComputedStyle(e.target);

	if(parseInt(style.maxWidth) <= width){
		e.target.style.maxWidth = (parseInt(window.innerWidth) - parseInt(e.target.offsetLeft) - 100) + "px";
		e.target.parentNode.style.setProperty("float", "none", "important");
	} else {
		e.target.parentNode.style.setProperty("float", "left", "important");
		e.target.style.maxWidth = width + "px";
	}
}

function expand_image(e, filepath, thumbnail_path){

	if(e.target.innerHTML == filepath){
		e.target.src = thumbnail_path;
		e.target.innerHTML = thumbnail_path;
		e.target.parentNode.style.setProperty("float", "left", "important");
	} else {
		e.target.src = filepath;
		e.target.innerHTML = filepath;
		e.target.parentNode.style.setProperty("float", "none", "important");
		e.target.style.maxWidth = (parseInt(window.innerWidth) - parseInt(e.target.offsetLeft) - 100) + "px";
	}
}

function backlink_mouseout(e, postID){
	var hover_post = document.getElementById("hover_post");
	hover_post.style.display = "none";
	hover_post.innerHTML = "";
}

function backlink_mouseover(e, postID){

	var hover_post = document.getElementById("hover_post");

	var style = window.getComputedStyle(hover_post);
	hover_post.style.display = "table";

	hover_post.innerHTML = "";
	insert_post_into_elem(postID, hover_post);

	hover_post.style.left = mouse_x + 30 + 'px';
	hover_post.style.top = (mouse_y - (parseInt(style.height)/2)) + 'px';
}

function reply_mousedown(e){

	var reply = document.getElementById("reply_container");

	var style = window.getComputedStyle(reply);

	drag_diff_x = mouse_x - parseInt(style.left)
	drag_diff_y = mouse_y - parseInt(style.top);

	dragging_reply = true;

	reply.style.left = (mouse_x - drag_diff_x) + 'px';
	reply.style.top = (mouse_y - drag_diff_y) + 'px';

	var reply_header = document.getElementById("reply_container_header");

	document.body.style.cursor = "move";
}

function reply_close(){
	dragging_reply = false;
	var reply = document.getElementById("reply_container");
	reply.style.display = "none";

	var reply_body = document.getElementById("reply_container_body");
	reply_body.value = "";
	document.body.style.cursor = "default";
}

function expand_post_options(ev, id){
		
	if(ev.target.className != "menu_open"){
		curr_post_options_arrow = ev.target;
		ev.target.className = "menu_open";
	}
	else {
		ev.target.className = "";

	}

	var options = document.getElementById("expand_options");
	
	if(options.style.display == "table"){
		options.style.display = "none";
		return;
	}

	options.style.display = "table";

	options.style.left = (mouse_x + 50) + 'px';
	options.style.top = mouse_y + 'px';

	options.firstChild.nextSibling.action = "post_action.php?post_id=" + id;

}

function reply(postID){

	var reply = document.getElementById("reply_container");
	var style = window.getComputedStyle(reply);

	var reply_body = document.getElementById("reply_container_body");
	reply_body.value += ">>" + postID + "\r\n";

	if(style.display != "none") return;

	var reply_header = document.getElementById("reply_container_header");
	var header_style = window.getComputedStyle(reply_header);

	dragging_reply = true;
	reply.style.display = "table";

	reply.style.left = (mouse_x - (parseInt(header_style.width) / 2)) + 'px';
	reply.style.top = (mouse_y - (parseInt(header_style.height) / 2)) + 'px';

	document.body.style.cursor = "move";

	drag_diff_x = mouse_x - parseInt(style.left);
	drag_diff_y = mouse_y - parseInt(style.top);
}