// var miga_currentMonth = new Date().getMonth() + 1;
var miga_currentYear = new Date().getFullYear();
var miga_currentCal = 0;
var miga_targetCal = "";

function miga_calendar_loadNext(obj) {
	miga_currentCal = obj.parentNode.parentNode.parentNode.parentNode.getAttribute("data-calendar");
	miga_targetCal = obj.parentNode.parentNode;

	let miga_currentMonth = parseInt(obj.parentNode.parentNode.parentNode.parentNode.getAttribute("data-month"));
	miga_currentMonth++;
	let miga_currentYear = parseInt(obj.parentNode.parentNode.parentNode.parentNode.getAttribute("data-year"));
	if (miga_currentMonth > 12) {
		miga_currentMonth = 1;
		miga_currentYear++;
	}
	obj.parentNode.parentNode.parentNode.parentNode.setAttribute("data-month", miga_currentMonth);
	obj.parentNode.parentNode.parentNode.parentNode.setAttribute("data-year", miga_currentYear);

	miga_calendar_request(miga_currentMonth, miga_currentYear)
}

function miga_calendar_request(miga_currentMonth, miga_currentYear) {
	miga_targetCal.parentNode.querySelector(".loading_spinner").classList.add("show");
	var showButton = miga_targetCal.parentNode.parentNode.getAttribute("data-showbutton");
	var showTitle = miga_targetCal.parentNode.parentNode.getAttribute("data-showtitle");
	jQuery.post({
		url: miga_calendar.wp_url,
		data: {
			action: 'miga_custom_post_filter_cal',
			miga_nonce: miga_calendar.miga_nonce,
			m: miga_currentMonth,
			y: miga_currentYear,
			c: miga_currentCal,
			sb: showButton,
			st: showTitle
		},
		complete: function(data) {},
		success: function(data) {
			miga_targetCal.parentNode.querySelector(".loading_spinner").classList.remove("show");
			var div = document.createElement('div');
			div.innerHTML = data;
			miga_targetCal.replaceWith(div.children[0]);
		},
		error: function(data) {
			miga_targetCal.parentNode.querySelector(".loading_spinner").classList.remove("show");
			console.log("Error", data);
		}
	});
}

function miga_calendar_loadPrev(obj) {
	miga_currentCal = obj.parentNode.parentNode.parentNode.parentNode.getAttribute("data-calendar");
	miga_targetCal = obj.parentNode.parentNode;

	let miga_currentMonth = parseInt(obj.parentNode.parentNode.parentNode.parentNode.getAttribute("data-month"));
	miga_currentMonth--;
	let miga_currentYear = parseInt(obj.parentNode.parentNode.parentNode.parentNode.getAttribute("data-year"));

	if (miga_currentMonth <= 0) {
		miga_currentMonth = 12;
		miga_currentYear--;
	}

	obj.parentNode.parentNode.parentNode.parentNode.setAttribute("data-month", miga_currentMonth);
	obj.parentNode.parentNode.parentNode.parentNode.setAttribute("data-year", miga_currentYear);

	miga_calendar_request(miga_currentMonth, miga_currentYear)
}

function miga_calendar_loadToday(obj) {
	miga_currentCal = obj.parentNode.parentNode.parentNode.getAttribute("data-calendar");
	miga_targetCal = obj.parentNode;

	let miga_currentMonth = parseInt(obj.parentNode.parentNode.parentNode.parentNode.getAttribute("data-month"));
	let miga_currentYear = parseInt(obj.parentNode.parentNode.parentNode.parentNode.getAttribute("data-year"));

	var d = new Date();
	miga_currentMonth = d.getMonth() + 1;
	miga_currentYear = d.getFullYear();
	obj.parentNode.parentNode.parentNode.setAttribute("data-month", miga_currentMonth);
	obj.parentNode.parentNode.parentNode.setAttribute("data-year", miga_currentYear);
	miga_calendar_request(miga_currentMonth, miga_currentYear)
}
