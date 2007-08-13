function createSelect(form, row_name, array, num)
{
    var select = document.createElement("select");
    select.setAttribute("name", row_name);

    for (var i = 0; i < num; i++)
        select.options[i] = new Option(array[i+1], i+1, false);
    
    var p = document.createElement("p");
    form = document.getElementById(form);
    form.appendChild(p);
    form.appendChild(select);
}
