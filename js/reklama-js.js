function scroll() {
	if(self != top) {
		var div = document.getElementById("reklama-wplugin");
		var rect = div.getBoundingClientRect();
		window.scrollBy(0, rect.top - 50);
	} else {
		/* Nic nedělá, není totiž v IFRAME */
	}
}