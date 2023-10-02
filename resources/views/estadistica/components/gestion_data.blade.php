<script>
    let array_items_name = [];
        let data = [];
        $(document).ready(function(){
            load_data();            
        })

        const change_index = cadena => {
            let name = "";
            let name_array =  cadena.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "").split(' ');
            name_array.forEach(e => {
                name = name == '' ? e : `${name}_${e}`;
            }); 
            return name;
        }

        const load_data = async () => {
            let values = [];
            for(let index = 0; index < headers.length; index++) {
                values.push(0);
            }
            array_items.forEach(e => {
                data[change_index(e)] = values
            });
            data = await fetch('{{route('colecciones.data')}}', {
                    method: 'POST', // or 'PUT'
                    body: JSON.stringify({
                    "_token": "{{ csrf_token() }}",
                    "coleccion":coleccion,
                    "data":Object.entries(data)
                }),
                headers:{
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(res => res)
            console.log('gf', typeof data);
            load_table();
        }

        const load_table = () =>{
            array_items.forEach(e => {
                console.log('f', data)
                let name = change_index(e);
                let count = 0;
                let inputs = '';
                headers.forEach((h,i) => {
                    console.log('i', i);
                    let value = data[name][i];
                    inputs = `${inputs}<td><input type="number" min="0" class="form-control" name="${name}[]" onchange="data_save(${count}, '${name}')" value="${value}"></td>`
                    count+= 1;
                });
                
                console.log('data_name', data);
                $('#tbody').append(`<tr>
                    <td>${e}</td>
                    ${inputs}
                </tr>`);
            });
        }

        const data_save = async (index, name) => {
            let inputs = document.querySelectorAll(`[name="${name}[]"`);
            let value = inputs[index].value;
            console.log('name', name)
            console.log('index', inputs[index].value);
            
            data[name][index] =  value;
            console.log('data', Object.entries(data));

            let resp = await fetch('{{route('colecciones.store')}}', {
                method: 'POST', // or 'PUT'
                body: JSON.stringify({
                "_token": "{{ csrf_token() }}",
                "coleccion":coleccion,
                "data":Object.entries(data) 
            }), // data can be `string` or {object}!
                headers:{
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(res => res)

            console.log('res', resp)
            
        }
</script>