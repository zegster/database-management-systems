$(document).ready(function()
{
    /* Hide/Unhide base on when "as defined" is selected in column default */
    $("#as-defined-value").hide();
    $("#as-defined").click(function()
    {
        if($(this).val() === "as defined")
        {
            $("#as-defined-value").show();
        }
        else
        {
            $("#as-defined-value").hide();
        }
    });
});
