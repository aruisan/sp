<!DOCTYPE html>

<html>

<head>
<link href="https://cdnjs.cloudflare.com/ajax/libs/handsontable/6.2.2/handsontable.full.css" rel="stylesheet" media="screen">
<title>Ejemplo del uso de formularios - aprenderaprogramar.com</title>

</head>

<body>
<button onclick="guardar()">guardar</button>
{{--
<form method="post" action="{{route('import.presupuesto-estadistica')}}" enctype="multipart/form-data">
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
    var data = [
    ["", "Ford", "Tesla", "Toyota", "Honda"],
    ["2017", 10, 11, 12, 13],
    ["2018", 20, 11, 14, 13],
    ["2019", 30, 15, 12, 13]
    ];

    var container = document.getElementById('example');
    var hot = new Handsontable(container, {
        data: Handsontable.helper.createSpreadsheetData(100, 252),
        rowHeaders: true,
        colHeaders: true,
        filters: true,
        dropdownMenu: true
    });

    const guardar = async () => {
        /*
        let column = hot.getData();
        let headers = column.shift();
        */
        let resp = await fetch('{{route('import.presupuesto-estadistica')}}', {
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