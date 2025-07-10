<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor de Personas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
        #avatarPreview {
            max-width: 200px;
            max-height: 200px;
            display: none;
        }
    </style>
    @vite(['resources/js/app.js', 'resources/js/persons.js'])
</head>
<body>
    @include('partials.navbar')
    
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Lista de Personas</h1>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#personModal">
                <i class="bi bi-plus"></i> Nueva Persona
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Avatar</th>
                        <th>Nombre</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="personsTableBody">
                    <!-- Las personas se cargarán aquí via AJAX -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal para crear/editar persona -->
    <div class="modal fade" id="personModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="personModalTitle">Nueva Persona</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="personForm">
                    <div class="modal-body">
                        <input type="hidden" id="personId">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="avatarInput" class="form-label">Avatar</label>
                            <input type="file" class="form-control" id="avatarInput" accept="image/*">
                            <img id="avatarPreview" class="img-thumbnail mt-2">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>