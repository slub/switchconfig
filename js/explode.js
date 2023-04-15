function boom() {
	container.style.display = "none";

	var text = document.createElement("h1");
	text.style.position = "fixed";
	text.style.textAlign = "center";
	text.style.top = "80px";
	text.style.left = "0px";
	text.style.width = "100%";
	text.style.color = "red";
	text.innerHTML = "Internet Successfully Deleted.";
	document.body.appendChild(text);

	var img = document.createElement("img");
	img.style.position = "fixed";
	img.style.top = "0px";
	img.style.left = "0px";
	img.style.width = "100%";
	img.style.height = "100%";
	img.style.transition = "opacity 1s";
	img.style.pointerEvents = "none";
	img.src = "img/explosion.gif";
	document.body.appendChild(img);

	setTimeout(function(){
		img.style.opacity = 0;
	}, 1650);
}
