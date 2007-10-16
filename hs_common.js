function createOptions(select, array, num)
{
    var select_idx = 0;
    var array_idx = 1;

    while (select_idx < num)
    {
        if (array[array_idx])
        {
            select.options[select_idx] = new Option(array[array_idx],
                                                    array_idx, false);
            select_idx++;
        }
        array_idx++;
    }
}


function createSelect(form, row_name, array, num)
{
    var select = document.createElement("select");
    select.setAttribute("name", row_name);
    select.setAttribute("id", row_name);

    createOptions(select, array, num);

    form = document.getElementById(form);
    form.appendChild(select);
    return select;
}
