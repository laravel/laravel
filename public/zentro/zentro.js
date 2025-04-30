function sleep(ms) {
	return new Promise(resolve => setTimeout(resolve, ms));
}

function summarizeString(text, maxLength, half) {
	if (text.length <= maxLength)
	  return text;
	

	if (half){
		var mitadLongitud = Math.floor(maxLength / 2);
		var primeraMitad = text.substr(0, mitadLongitud);
		var segundaMitad = text.substr(text.length - mitadLongitud);
	  
		return primeraMitad + "..." + segundaMitad;
	}

	return text.substring(0, maxLength) + "...";
}

function removeTrailingZeros(number) {
	// Convertir el número a una cadena
	var numberString = number.toString();
  
	// Buscar la posición del último dígito distinto de cero
	var lastNonZeroIndex = numberString.lastIndexOf(/[1-9]/);
  
	// Si se encontró un dígito distinto de cero
	if (lastNonZeroIndex !== -1) {
	  // Recortar la cadena hasta el último dígito distinto de cero
	  numberString = numberString.substring(0, lastNonZeroIndex + 1);
	}
  
	// Retornar el número sin los ceros decimales a la derecha
	return numberString;
  }
  

  
function showToastrMessage(type, title, message, timeOut, progressBar, closeButton, positionClass, preventDuplicates, onHidden){
	if(!timeOut)
		timeOut = "5000";
	if(!progressBar)
		progressBar = true;
	if(!closeButton)
		closeButton = true;
	if(!positionClass)
		positionClass = "toast-top-right";
	if(!preventDuplicates)
		preventDuplicates = true;
	
	toastr.options = {
		"closeButton": closeButton,
		//"newestOnTop": false,
		"progressBar": progressBar,
		"positionClass": positionClass,
		"preventDuplicates": preventDuplicates,
		"timeOut": timeOut,
	};
	if(onHidden)
		toastr.options.onHidden = onHidden;

	toastr[type](message, title);
}

function showSwalMessage(title, message, buttons, callback, type, timeOut, closeOnEsc){
	swal({
		title: title,
		text: message,
		icon: type,
		closeOnClickOutside: false,
		closeOnEsc: false,
		buttons: buttons,
		dangerMode: buttons!=undefined,
		timer: timeOut,
	}).then((value) => {
		return callback(value);
	});
	  
}

function showSwalPreloader(){
	swal({
		className: 'swal-height-zero',
		content: {
			element: "div",
			attributes: {
				id: "preloader",
				style: "background:transparent !important;"
			},
		},
		closeOnClickOutside: false,
		closeOnEsc: false,
		buttons: false,
	});
}
        
function copyToClipboard(id) {
	var copyText = document.getElementById(id);
	var textArea = document.createElement("textarea");
	textArea.value = copyText.textContent;
	document.body.appendChild(textArea);
	textArea.select();
	document.execCommand("copy");
	document.body.removeChild(textArea);

	showToastrMessage('warning', 'Copied successfully', 'Copied to clipboard', 10000);
  }


function formatDate(date, year="numeric", month="2-digit", day="2-digit", hour="2-digit", minute="2-digit", second="2-digit", weekday = false, locale="en-US") {
	var specifications = new Object;

	if (weekday) // long, short
		specifications.weekday = weekday;

	if (year) // numeric, 2-digit
		specifications.year = year;
	if (month) // numeric, 2-digit
		specifications.month = month;
	if (day) // numeric, 2-digit
		specifications.day = day;

	if (hour) // numeric, 2-digit
		specifications.hour = hour;
	if (minute) // numeric, 2-digit
		specifications.minute = minute;
	if (second) // numeric, 2-digit
		specifications.second = second;

	return date.toLocaleDateString(locale, specifications);
}

function treatAsUTC(date) {
    var result = new Date(date);
    result.setMinutes(result.getMinutes() - result.getTimezoneOffset());
    return result;
}

function daysBetween(startDate, endDate) {
    var millisecondsPerDay = 24 * 60 * 60 * 1000;
    return (treatAsUTC(endDate) - treatAsUTC(startDate)) / millisecondsPerDay;
}

function formatNumber(number, decimals = 2, separator = ' ', decimalSeparator = '.') {
	const parts = parseFloat(number).toFixed(decimals).toString().split('.');
	parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, separator);
	return parts.join(decimalSeparator);
}

  

/*
const testCases = [
new Date().toLocaleDateString(), // 8/19/2020
new Date().toLocaleDateString('en-US', {year: 'numeric', month: '2-digit', day: '2-digit'}), // 08/19/2020 (month and day with two digits)
new Date().toLocaleDateString('en-ZA'), // 2020/08/19 (year/month/day) notice the different locale
new Date().toLocaleDateString('en-CA'), // 2020-08-19 (year-month-day) notice the different locale
new Date().toLocaleString("en-US", {timeZone: "America/New_York"}), // 8/19/2020, 9:29:51 AM. (date and time in a specific timezone)
new Date().toLocaleString("en-US", {hour: '2-digit', hour12: false, timeZone: "America/New_York"}),  // 09 (just the hour)
]
*/