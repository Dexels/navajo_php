/* 
Derived from a script by Alejandro Gervasio. 
Modified to take class names by Andy Miller
*/
matchDivs=function(divclass){ 

     var divs,contDivs,maxHeight,divHeight,d; 
     // get all <div> elements in the document 
     divs=document.getElementsByTagName('div'); 
     contDivs=[]; 
     // initialize maximum height value 
     maxHeight=0; 
     // iterate over all <div> elements in the document 
     for(var i=0;i<divs.length;i++){ 
          // make collection with <div> elements with class attribute 'container' 
	      var reg = new RegExp("\\b" + divclass + "\\b");
          if(reg.test(divs[i].className)){ 
                d=divs[i]; 
                contDivs[contDivs.length]=d; 
                // determine height for <div> element 
                if(d.offsetHeight){ 
                     divHeight=d.offsetHeight; 					
                } 
                else if(d.style.pixelHeight){ 
                     divHeight=d.style.pixelHeight;					 
                } 
                // calculate maximum height 
                maxHeight=Math.max(maxHeight,divHeight); 
          } 
     } 
     // assign maximum height value to all of container <div> elements 
     for(var i=0;i<contDivs.length;i++){ 
          contDivs[i].style.height=maxHeight + "px"; 
     } 
} 

//non invasive way to add function to onload()
function addLoadEvent(func) {
  var oldonload = window.onload;
  if (typeof window.onload != 'function') {
    window.onload = func;
  } else {
    window.onload = function() {
      if (oldonload) {
        oldonload();
      }
      func();
    }
  }
}

addLoadEvent(function(){ 
     if(document.getElementsByTagName){ 
        matchDivs("midmodules");
				matchDivs("botmodules");
     }
});



