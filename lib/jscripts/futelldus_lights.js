function createXHRobjekt() {
	var XHRobjekt = null;
	
	try {
		ajaxRequest = new XMLHttpRequest(); // Firefox, Opera, ...
	} catch(err1) {
		try {
			ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP"); // Noen IE v.
		} catch(err2) {
			try {
					ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP"); // Noen IE v.
			} catch(err3) {
				ajaxRequest = false;
			}
		}
	}
	return ajaxRequest;
}



function lightControl(state, deviceID) {
	myXHRobjekt = createXHRobjekt(); 

	if (myXHRobjekt) {
		myXHRobjekt.onreadystatechange = function() { 
			if(ajaxRequest.readyState == 4){
				var ajaxDisplay = document.getElementById('ajax_loader_' + deviceID);
				ajaxDisplay.innerHTML = ajaxRequest.responseText;

				if (state == "on") {
					$('#btn_' + deviceID + "_on").addClass('btn-success active');
					$('#btn_' + deviceID + "_off").removeClass('btn-success active');
				} else {
					$('#btn_' + deviceID + "_on").removeClass('btn-success active');
					$('#btn_' + deviceID + "_off").addClass('btn-success active');
				}
			} else {
				document.getElementById('ajax_loader_' + deviceID).innerHTML = "<img style='height:15px; margin-right:8px;' src='images/ajax-loader2.gif' alt='ajax-loader' />";
			}
		}

		ajaxRequest.open("GET", "ajax_light_control.php?state=" + state + "&id=" + deviceID + "&&rand=" + Math.random()*9999, true);
		ajaxRequest.send(null); 
	}
}