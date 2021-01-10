$( document ).ready(function() {
    $('#checkAll').change(function (e) {


        var el = e.target || e.srcElement;
        var selectAll = el.form.getElementsByClassName('selectAll');
        for (var i =0; i<selectAll.length; i++)
        {
            if (el.checked)
            {
                selectAll[i].checked = true;
            } else {
                selectAll[i].checked = false;
            }
        }
    })
})











