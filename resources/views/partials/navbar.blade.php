<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand">Gestor de Tareas</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('tasks.index') }}">Tareas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('persons.index') }}">Personas</a>
                </li>
            </ul>
        </div>
    </div>
</nav>