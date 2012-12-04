<!--
// please keep these lines on when you copy the source
// made by: Nicolas - http://www.javascript-page.com

function checkTime(i)
{
	if (i<10) 
  		{i="0" + i}
	return i
}

var clockID = 0;

function UpdateClock() {
   if(clockID) {
      clearTimeout(clockID);
      clockID  = 0;
   }

   var tDate = new Date();
   var h=tDate.getHours()
   var m=tDate.getMinutes()
   var s=tDate.getSeconds()
   
	// add a zero in front of numbers<10
	m=checkTime(m)
	s=checkTime(s)
	
   document.theClock.theTime.value = "" 
                                   + h + ":" 
                                   + m + ":" 
                                   + s;
   
   clockID = setTimeout("UpdateClock()", 1000);
}
function StartClock() {
   clockID = setTimeout("UpdateClock()", 500);
}

function KillClock() {
   if(clockID) {
      clearTimeout(clockID);
      clockID  = 0;
   }
}

//-->

