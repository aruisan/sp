<!DOCTYPE html>

<html>

<head>
<link href="https://cdnjs.cloudflare.com/ajax/libs/handsontable/6.2.2/handsontable.full.css" rel="stylesheet" media="screen">
<title>empleados import</title>

</head>

<body>
<button onclick="guardar()">guardar</button>
{{--
<form method="post" action="{{route('import.bancos_saldos_iniciales')}}" enctype="multipart/form-data">
{{ csrf_field() }}

<input name="documento" type="file" />
<br>
<input type="submit" value="enviar">

</form>
--}}

<div id="example"></div>

</body>
<script src="https://unpkg.com/handsontable@6.2.2/dist/handsontable.js"></script>


<script>
    var container = document.getElementById('example');
    var hot = new Handsontable(container, {
        data: Handsontable.helper.createSpreadsheetData(2,5),
        rowHeaders: true,
        colHeaders: true,
        filters: true,
        dropdownMenu: true,
        contextMenu:true,
        formulas:true
    });

    const guardar = async () => {
        /*
        let column = hot.getData();
        let headers = column.shift();
        */
        let resp = await fetch('{{route('import.bancos_saldos_iniciales')}}', {
            headers:{
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            method:'POST',
            body: JSON.stringify({data:hot.getData()})
        }).then(res => res.json())
        .then(res => res)

        console.log('data', resp);
    }
</script>
</html>