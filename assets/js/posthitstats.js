(function() {
	//alert("hello");
	jQuery( "#from" ).datepicker({
		//defaultDate: "+1w",
		dateFormat:"yy-mm-dd",
		changeMonth: true,
		numberOfMonths: 1,
		onSelect: function( selectedDate ) {
			jQuery( "#to" ).datepicker( "option", "minDate", selectedDate );
		}
	});
	jQuery( "#to" ).datepicker({
		//defaultDate: "+1w",
		dateFormat:"yy-mm-dd",
		changeMonth: true,
		numberOfMonths: 1,
		onSelect: function( selectedDate ) {
			jQuery( "#from" ).datepicker( "option", "maxDate", selectedDate );
		}
	});
	
	
}( jQuery ) );