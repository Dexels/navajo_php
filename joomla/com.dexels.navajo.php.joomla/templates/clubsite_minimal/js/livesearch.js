/*
// +----------------------------------------------------------------------+
// | Copyright (c) 2004 Bitflux GmbH                                      |
// +----------------------------------------------------------------------+
// | Licensed under the Apache License, Version 2.0 (the "License");      |
// | you may not use this file except in compliance with the License.     |
// | You may obtain a copy of the License at                              |
// | http://www.apache.org/licenses/LICENSE-2.0                           |
// | Unless required by applicable law or agreed to in writing, software  |
// | distributed under the License is distributed on an "AS IS" BASIS,    |
// | WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or      |
// | implied. See the License for the specific language governing         |
// | permissions and limitations under the License.                       |
// +----------------------------------------------------------------------+
// | Author: Bitflux GmbH <devel@bitflux.ch>                              |
// +----------------------------------------------------------------------+
*/
var liveSearchReq = false;
var t = null;
var liveSearchLast = "";
var isIE = false;
var url = '';

// on !IE we only have to initialize it once
if (window.XMLHttpRequest) {
	liveSearchReq = new XMLHttpRequest();
	newMastheadReq = new XMLHttpRequest();
}
function liveSearchInit() {

	// Turns off autocomplete on the input element
	var searchInput = document.getElementById('livesearch');
	if( searchInput == null ) return;
	searchInput.setAttribute('autocomplete', 'off');

	if (navigator.userAgent.indexOf("Safari") > 0) {
		document.getElementById('livesearch').addEventListener("keydown",liveSearchKeyPress,false);
	} else if (navigator.product == "Gecko") {
		document.getElementById('livesearch').addEventListener("keypress",liveSearchKeyPress,false);
	} else {
		document.getElementById('livesearch').attachEvent('onkeydown',liveSearchKeyPress);
		isIE = true;
	}
}
function liveSearchKeyPress(event) {
	
	if (event.keyCode == 40 )
	//KEY DOWN
	{
		highlight = document.getElementById("LSHighlight");
		if (!highlight) {
			try {
			highlight = document.getElementById("LSRes").firstChild;
			} catch (exception) { }
		} else {
			highlight.removeAttribute("id");
			highlight = highlight.nextSibling;
		}
		if (highlight) {
			highlight.setAttribute("id","LSHighlight");
		}
		if (!isIE) { event.preventDefault(); }
	}
	//KEY UP
	else if (event.keyCode == 38 ) {
		highlight = document.getElementById("LSHighlight");
		if (!highlight) {
			try {
			highlight = document.getElementById("LSRes").lastChild;
			} catch (exception) {}
		}
		else {
			highlight.removeAttribute("id");
			highlight = highlight.previousSibling;
		}
		if (highlight) {
				highlight.setAttribute("id","LSHighlight");
		}
		if (!isIE) { event.preventDefault(); }
	}
	//ESC
	else if (event.keyCode == 27) {
		highlight = document.getElementById("LSHighlight");
		if (highlight) {
			highlight.removeAttribute("id");
		}
		document.getElementById("LSResult").style.display = "none";
	} 
	//BACKSPACE - required for IE
	else if (event.keyCode == 8 && isIE) {
		liveSearchStart(this.url);
	}

}
function closeLiveSearch() {
	highlight = document.getElementById("LSHighlight");
	if (highlight) {
		highlight.removeAttribute("id");
	}
	document.getElementById("LSResult").style.display = "none";
	document.forms.searchform.searchword.value = 'live search...';
}
function liveSearchStart(url) {
	this.url = url;
	if (t) { window.clearTimeout(t); }
	t = window.setTimeout("liveSearchDoSearch()",200);
}
function liveSearchDoSearch() {
	if (liveSearchLast != document.forms.searchform.searchword.value) {
	if (liveSearchReq && liveSearchReq.readyState < 4) {
		liveSearchReq.abort();
	}
	if ( searchTrimAll(document.forms.searchform.searchword.value) == "") {
		document.getElementById("LSResult").style.display = "none";
		highlight = document.getElementById("LSHighlight");
		if (highlight) {
			highlight.removeAttribute("id");
		}
		return false;
	}
	if (window.XMLHttpRequest) {
	// branch for IE/Windows ActiveX version
	} else if (window.ActiveXObject) {
		liveSearchReq = new ActiveXObject("Microsoft.XMLHTTP");
	}
	liveSearchReq.onreadystatechange= liveSearchProcessReqChange;
	liveSearchReq.open("GET", this.url + "/livesearch/livesearch.php?s="+ document.forms.searchform.searchword.value);
	liveSearchLast = document.forms.searchform.searchword.value;
	liveSearchReq.send(null);
	}
}
function liveSearchProcessReqChange() {
	if (liveSearchReq.readyState == 4) {
		var res = document.getElementById("LSResult");
		res.style.display = "block";
		res.firstChild.innerHTML = '</devel@bitflux.ch><div id="LSHeader">use arrow keys &amp; enter <a href="javascript://" title="Close results" onclick="closeLiveSearch()">close (esc)</a><br /></div>'+liveSearchReq.responseText;//;
	}
}
function liveSearchSubmit() {
	var highlight = document.getElementById("LSHighlight");
	if (highlight && highlight.firstChild) {
		window.location = highlight.firstChild.getAttribute("href");
		return false;
	} else {
		var searchInput = document.getElementById('searchform');
	  searchInput.submit();
		return false;
	}
}

function liveSearchByContext(thelink,theText) {
var searchTerm = theText;
	if (liveSearchLast != searchTerm) {
	if (liveSearchReq && liveSearchReq.readyState < 4) {
		liveSearchReq.abort();
	}

	if (window.XMLHttpRequest) {
	// branch for IE/Windows ActiveX version
	} else if (window.ActiveXObject) {
		liveSearchReq = new ActiveXObject("Microsoft.XMLHTTP");
	}
	liveSearchReq.onreadystatechange=function() {
	if (liveSearchReq.readyState == 4) {
		var newDiv = document.createElement('div');
		newDiv.id = 'liveLinkRes';
		newDiv.style.display = 'none';
		newDiv.innerHTML = '<div id="liveLinkHead"><div style="float: left;">&nbsp;</div><div style="float: right;"><a href="javascript://" title="Close results" onclick="closeLiveLink()">[x]</a></div><br></div>'+liveSearchReq.responseText;//;
		newDiv.style.display = 'block';

		myRes=document.getElementById(theText+"-livelink");
		myRes.appendChild(newDiv);
		myBlank=document.createTextNode("");
		myRes.insertBefore(myBlank,newDiv);
	}
 }
	liveSearchReq.open("GET", "?ls=" + searchTerm);
	liveSearchLast = searchTerm;
	liveSearchReq.send(null);
	} else {
		closeLiveLink();
	}
}

function searchTrimAll(sString) {
	while (sString.substring(0,1) == ' ') {
		sString = sString.substring(1, sString.length);
	}
	while (sString.substring(sString.length-1, sString.length) == ' ') {
		sString = sString.substring(0,sString.length-1);
	}
	return sString;
}

function closeLiveLink() {
	liveSearchLast = "";
	highlight = document.getElementById("LSHighlight");
	if (highlight) {
		highlight.removeAttribute("id");
	}
	document.getElementById("liveLinkRes").style.display = "none";
}

function addLoadEvent(func) {
  var oldonload = window.onload;
  if (typeof window.onload != 'function') {
    window.onload = func;
  } else {
    window.onload = function() {
      oldonload();
      func();
    }
  }
}

addLoadEvent(function() {
  liveSearchInit();
});