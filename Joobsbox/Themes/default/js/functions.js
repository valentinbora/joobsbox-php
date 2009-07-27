// Contains global site functions (mainly helpers)

// Translate any string using the global site translation
function translate(string) {
	if(translateHash[string]) {
		return translateHash[string];
	} else {
		return string;
	}
}

$(function() {
  $("div.box, div.plain").corner("round top");
  $("div.inner").corner();
}
