function highlightField(fieldId) {
	$('#' + fieldId).css('color', 'red');
}

function displayMsg(title, msg, color) {
	$('#msg').html('<p class=\"bold\">' + title + '</p><p>' + msg + '</p>').css({'display': 'block', 'border-color': color});
}
