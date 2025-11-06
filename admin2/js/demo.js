$(function()
{

	if (!window['console'])
	{
		window.console = {};
		window.console.log = function(){};
	}
		
	/*
	define a new language named "custom"
	*/

	
	$('#date-range0').dateRangePicker({
	}).bind('datepicker-first-date-selected', function(event, obj)
	{
		/* This event will be triggered when first date is selected */
		console.log('first-date-selected',obj);


		// obj will be something like this:
		// {
		// 		date1: (Date object of the earlier date)
		// }
	})
	.bind('datepicker-change',function(event,obj)
	{
		/* This event will be triggered when second date is selected */
		console.log('change',obj);

		// obj will be something like this:
		// {
		// 		date1: (Date object of the earlier date),
		// 		date2: (Date object of the later date),
		//	 	value: "2013-06-05 to 2013-06-07"
		// }
	})
	.bind('datepicker-apply',function(event,obj)
	{
		/* This event will be triggered when user clicks on the apply button */
		console.log('apply',obj);
	})
	.bind('datepicker-close',function()
	{
		/* This event will be triggered before date range picker close animation */
		console.log('before close');
	})
	.bind('datepicker-closed',function()
	{
		/* This event will be triggered after date range picker close animation */
		console.log('after close');
	})
	.bind('datepicker-open',function()
	{
		/* This event will be triggered before date range picker open animation */
		console.log('before open');
	})
	.bind('datepicker-opened',function()
	{
		/* This event will be triggered after date range picker open animation */
		console.log('after open');
	});

	$('#date-range1').dateRangePicker(
	{
		separator : ' to ',
		getValue: function()
		{
			if ($('#date-range200').val() && $('#date-range201').val() )
				return $('#date-range200').val() + ' to ' + $('#date-range201').val();
			else
				return '';
		},
		setValue: function(s,s1,s2)
		{

			$('#date-range1').val(s1);
			$('#date-range0').val(s2);
		}
	});

	$('#date-range2').dateRangePicker(
	{
		separator : ' to ',
		getValue: function()
		{
			if ($('#date-range200').val() && $('#date-range201').val() )
				return $('#date-range200').val() + ' to ' + $('#date-range201').val();
			else
				return '';
		},
		setValue: function(s,s1,s2)
		{
			$('#date-range2').val(s1);
			$('#date-range3').val(s2);
		}
	});

	

	

	

});
