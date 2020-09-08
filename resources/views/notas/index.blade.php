<a href="{{route('notas.create')}}">Nueva nota</a>

<h1>Listado de notas</h1>
@foreach($notas as $nota)
<div class="card" style="width: 18rem;">
  <div class="card-body">
    <h5 class="card-title">{{$nota->titulo}}</h5>
    <p class="card-text">{{$nota->texto}}</p>
    <a href="#" class="btn btn-primary">Go somewhere</a>
    <form action="{{route('notas.destroy', ['nota'=>$nota])}}" method="POST">
        @method('DELETE')
        @csrf
        <button type="submit">Eliminar</button>
    </form>
  </div>
</div>
@endforeach
