$(document).ready(function()
{
	/* Deactivate "JavaScript is not enable" error message */
	$('#javascript-warning').ready(function()
	{
		// $('#javascript-warning').dimmer('toggle');
		$('#javascript-warning').fadeOut(0);
	});

	/* Close alert message when clicked */
	$('.message .close').click(function() 
	{
		$(this).closest('.message').fadeOut();
	});
	
	/* Hide database editor when input is empty and vice versa */
	$("#editor-form").ready(function()
	{
		isFormEmpty = true;
		$('#editor-form input[type="text"]').each(function()
		{
			if($(this).val() !== "")
			{
				isFormEmpty = false;
				return false;
			}
		});
		
		if(isFormEmpty)
		{
			$("#editor").hide(0);
		}
		else
		{
			$("#editor-toggler").hide(0);
		}
	});
	
	/* Show database editor when clicked */
	$("#editor-toggler-button").click(function()
	{
		$("#editor-toggler").hide(0);
		$("#editor").show(500);
	});
});


/* Sort by name column (descending or ascending) */
function sortCurrentField(n, u, v) 
{
	if(n < 0)
	{
		document.location.href = "index.php?mn=" +u + "&cn=" + v + "&desc";
	}
	else
	{
		document.location.href = "index.php?mn=" +u + "&cn=" + v;
	}
}
