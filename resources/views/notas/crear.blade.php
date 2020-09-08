<div>
<form action="{{route('notas.store')}}" method="post">
@csrf
    <h2>Crear una nota</h2>
    <input type="text" label="titulo" name="titulo">
    <input type="textarea" placeholder="inserte nota aqui" name="texto">
    <button type="submit">Postear</button>
</form>


</div>