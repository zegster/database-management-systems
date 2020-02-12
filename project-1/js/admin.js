$(document).ready(function()
{
    /* Hide/Unhide base on the creating/deleting structure is selected */
    $("#form-structure-create").hide();
    $("#form-structure-delete").hide();
    $("#form-structure-toggler").click(function()
    {
        switch($(this).val())
        {
            case "create_column":
                $("#form-structure-create").show();
                $("#form-structure-delete").hide();
                break;

            case "delete_column":
                $("#form-structure-create").hide();
                $("#form-structure-delete").show();
                break;    
        }
    });

    /* Hide/Unhide base on when table column (for creating structure) is selected */
    $("[id^=structure-current-]").hide();
    $("#database-table-add").click(function()
    {
        $("[id^=structure-current-]").hide();
        $("#structure-current-" + $(this).val()).show();
    });

    /* Hide/Unhide base on the column type is selected */
    $("#column-type-length").hide();
    $("#column-type").click(function()
    {
        if($(this).val() === "int" || $(this).val() === "varchar")
        {
            $("#column-type-length").show();
        }
        else
        {
            $("#column-type-length").hide();
        }
    });

    /* Hide/Unhide base on when "as defined" is selected in column default */
    $("#column-default-value").hide();
    $("#column-default").click(function()
    {
        if($(this).val() === "as defined")
        {
            $("#column-default-value").show();
        }
        else
        {
            $("#column-default-value").hide();
        }
    });

    /* Hide/Unhide base on when table column (for deleting structure) is selected */
    $("[id^=structure-delete-]").hide();
    $("#database-table-drop").click(function()
    {
        $("[id^=structure-delete-]").hide();
        $("#structure-delete-" + $(this).val()).show();
    });
});
