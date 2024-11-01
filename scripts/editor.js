var miga_currentEditorMonth = new Date().getMonth() + 1;
var miga_currentEditorYear = new Date().getFullYear();

function miga_calendar_loadEditorNext() {
	miga_currentEditorMonth++;
	if (miga_currentEditorMonth > 12) {
		miga_currentEditorMonth = 1;
		miga_currentEditorYear++;
	}
	miga_calendar_editorRequest()
}

function miga_calendar_editorRequest() {
	jQuery.post({
		url: miga_calendar.wp_url,
		data: {
			action: 'miga_editor_cal',
			miga_nonce: miga_calendar.miga_nonce,
			m: miga_currentEditorMonth,
			y: miga_currentEditorYear,
			c: document.getElementById("currentCalendar").value
		},
		complete: function(data) {},
		success: function(data) {
			console.log(data);
			document.querySelector("#miga_calendar_half_cal").innerHTML = data;
		},
		error: function(data) {
			console.log("Error", data);
		}
	});
}

function miga_calendar_loadEditorPrev() {
	miga_currentEditorMonth--;
	if (miga_currentEditorMonth == 0) {
		miga_currentEditorMonth = 12;
		miga_currentEditorYear--;
	}
	miga_calendar_editorRequest()
}

function miga_calendar_loadEditorToday() {
	var d = new Date();
	miga_currentEditorMonth = d.getMonth() + 1;
	miga_currentEditorYear = d.getFullYear();
	miga_calendar_editorRequest()
}


function miga_calendar_deleteItem(obj) {
	var statusValue = obj.getAttribute("data-value");
	if (confirm("Delete item?")) {
		jQuery.post({
			url: miga_calendar.wp_url,
			data: {
				action: 'miga_editor_cal_delete',
				s: statusValue
			},
			complete: function(data) {},
			success: function(data) {
				window.location.reload()
			},
			error: function(data) {
				console.log("Error", data);
			}
		});
	}
	return false;
}

function miga_calendar_updateItem(obj) {
	var statusValue = obj.getAttribute("data-value");
	var value_n = obj.parentNode.parentNode.querySelector(".status_status").value;
	var value_c = (obj.parentNode.parentNode.querySelector(".status_class").tagName == "DIV") ? obj.parentNode.parentNode.querySelector(".status_class").textContent : obj.parentNode.parentNode.querySelector(".status_class").value;
	var value_v = obj.parentNode.parentNode.querySelector(".status_visible").checked ? 1 : 0;

	jQuery.post({
		url: miga_calendar.wp_url,
		data: {
			action: 'miga_editor_cal_update',
			s: statusValue,
			n: value_n,
			c: value_c,
			v: value_v
		},
		complete: function(data) {},
		success: function(data) {
			window.location.reload()
		},
		error: function(data) {
			console.log("Error", data);
		}
	});
	return false;
}


function miga_calendar_onchangeCal() {
	var cal = document.getElementById("miga_calendar_select").value;
	var queryparams = location.search.split('?')[1];
	var url = location.href.split('?')[0];
	var params = queryparams.split('&');
	var pair = null;
	var data = [];
	var changeVal = true;
	var outString = "";
	params.forEach(function(d) {
		pair = d.split('=');

		if (pair[0] == "cal") {
			pair[1] = cal;
			changeVal = false;
		}
		outString += pair[0] + "=" + pair[1] + "&";
	});
	if (changeVal) {
		outString += "cal=" + cal;
	}
	location.href = url + "?" + outString;
}
