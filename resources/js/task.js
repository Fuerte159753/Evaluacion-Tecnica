        $(document).ready(function() {
            loadTasks();
            loadPersonsForSelect();
            
            $('#taskForm').submit(function(e) {
                e.preventDefault();
                saveTask();
            });
                $('#taskModal').on('hidden.bs.modal', function() {
                    resetTaskForm();
                });
            
            $('#assignPersonBtn').click(function() {
                assignPersonToTask();
            });
            
            $(document).on('click', '.edit-task-btn', function() {
                const taskId = $(this).data('id');
                editTask(taskId);
            });
            
            $(document).on('click', '.delete-task-btn', function() {
                const taskId = $(this).data('id');
                deleteTask(taskId);
            });
            
            $(document).on('click', '.complete-task-btn', function() {
                const taskId = $(this).data('id');
                completeTask(taskId);
            });
            
            $(document).on('click', '.assign-person-btn', function() {
                const taskId = $(this).data('id');
                openAssignModal(taskId);
            });
            
            $(document).on('click', '.unassign-person-btn', function() {
                const taskId = $(this).data('task-id');
                const personId = $(this).data('person-id');
                unassignPerson(taskId, personId);
            });
        });

        function loadTasks() {
            $.ajax({
                url: '/api/tasks',
                type: 'GET',
                success: function(response) {
                    if(response.success) {
                        renderTasks(response.data);
                    } else {
                        showAlert('Error al cargar tareas', 'danger');
                    }
                },
                error: function() {
                    showAlert('Error al conectar con el servidor', 'danger');
                }
            });
        }

        function renderTasks(tasks) {
            const tableBody = $('#tasksTableBody');
            tableBody.empty();

            tasks.forEach(task => {
                let statusText = '';
                switch(task.status) {
                    case "0": statusText = '<span class="badge bg-secondary">No asignado</span>'; break;
                    case "1": statusText = '<span class="badge bg-warning text-dark">Pendiente</span>'; break;
                    case "2": statusText = '<span class="badge bg-success">Completado</span>'; break;
                }

                let assignedPersons = '';
                if(task.persons && task.persons.length > 0) {
                    task.persons.forEach(person => {
                        assignedPersons += `
                            <div class="d-flex align-items-center mb-1">
                                <img src="data:image/png;base64,${person.avatar}" class="avatar-sm me-2">
                                <span>${person.name}</span>
                                <button class="btn btn-sm btn-outline-danger ms-2 unassign-person-btn" 
                                        data-task-id="${task.id}" data-person-id="${person.id}">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                        `;
                    });
                } else {
                    assignedPersons = `
                        <button class="btn btn-sm btn-outline-primary assign-person-btn" data-id="${task.id}">
                            <i class="bi bi-plus"></i> Asignar persona
                        </button>
                    `;
                }

                const row = `
                    <tr>
                        <td>${task.title}</td>
                        <td>${task.description || '-'}</td>
                        <td>${statusText}</td>
                        <td>${assignedPersons}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary me-2 edit-task-btn" data-id="${task.id}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-task-btn" data-id="${task.id}">
                                <i class="bi bi-trash"></i>
                            </button>
                            ${task.status === 1 ? `
                            <button class="btn btn-sm btn-outline-success ms-2 complete-task-btn" data-id="${task.id}">
                                <i class="bi bi-check"></i> Completar
                            </button>` : ''}
                        </td>
                    </tr>
                `;
                tableBody.append(row);
            });
        }

        function saveTask() {
            const taskId = $('#taskId').val();
            const url = taskId ? `/api/tasks/${taskId}` : '/api/tasks';
            const method = taskId ? 'PUT' : 'POST';

            const data = {
                title: $('#title').val(),
                description: $('#description').val(),
                status: $('#status').val()
            };

            $.ajax({
                url: url,
                type: method,
                data: JSON.stringify(data),
                contentType: 'application/json',
                success: function(response) {
                    if(response.success) {
                        showAlert(`Tarea ${taskId ? 'actualizada' : 'creada'} correctamente`, 'success');
                        loadTasks();
                        $('#taskModal').modal('hide');
                    } else {
                        showAlert(response.message || 'Error al guardar', 'danger');
                    }
                },
                error: function(xhr) {
                    const errorMsg = xhr.responseJSON?.message || 'Error en la solicitud';
                    showAlert(errorMsg, 'danger');
                }
            });
        }

        function editTask(taskId) {
            $.ajax({
                url: `/api/tasks/tasks/${taskId}`,
                type: 'GET',
                success: function(response) {
                    if(response.success) {
                        const task = response.data;
                        $('#taskId').val(task.id);
                        $('#title').val(task.title);
                        $('#description').val(task.description);
                        $('#status').val(task.status);
                        $('#modalTitle').text('Editar Tarea');
                        $('#taskModal').modal('show');
                    } else {
                        showAlert('Error al cargar tarea', 'danger');
                    }
                },
                error: function() {
                    showAlert('Error al conectar con el servidor', 'danger');
                }
            });
        }

        function deleteTask(taskId) {
            if(confirm('¿Estás seguro de eliminar esta tarea?')) {
                $.ajax({
                    url: `/api/tasks/${taskId}`,
                    type: 'DELETE',
                    success: function(response) {
                        if(response.success) {
                            showAlert('Tarea eliminada correctamente', 'success');
                            loadTasks();
                        } else {
                            showAlert(response.message || 'Error al eliminar', 'danger');
                        }
                    },
                    error: function(xhr) {
                        const errorMsg = xhr.responseJSON?.message || 'Error en la solicitud';
                        showAlert(errorMsg, 'danger');
                    }
                });
            }
        }

        function loadPersonsForSelect() {
            $.ajax({
                url: '/api/persons',
                type: 'GET',
                success: function(response) {
                    if(response.success) {
                        const select = $('#personSelect');
                        select.empty();
                        select.append('<option value="">Seleccionar persona...</option>');
                        response.data.forEach(person => {
                            select.append(`<option value="${person.id}">${person.name}</option>`);
                        });
                    }
                }
            });
        }

        function openAssignModal(taskId) {
            $('#currentTaskId').val(taskId);
            $('#assignPersonModal').modal('show');
        }

        function assignPersonToTask() {
            const taskId = $('#currentTaskId').val();
            const personId = $('#personSelect').val();

            if(!personId) {
                showAlert('Debes seleccionar una persona', 'warning');
                return;
            }

            $.ajax({
                url: `/api/tasks/${taskId}/assign`,
                type: 'POST',
                data: JSON.stringify({ person_id: personId }),
                contentType: 'application/json',
                success: function(response) {
                    if(response.success) {
                        showAlert('Persona asignada correctamente', 'success');
                        loadTasks();
                        $('#assignPersonModal').modal('hide');
                    } else {
                        showAlert(response.message || 'Error al asignar', 'danger');
                    }
                },
                error: function(xhr) {
                    const errorMsg = xhr.responseJSON?.message || 'Error en la solicitud';
                    showAlert(errorMsg, 'danger');
                }
            });
        }

        function unassignPerson(taskId, personId) {
            if(confirm('¿Quitar esta persona de la tarea?')) {
                $.ajax({
                    url: `/api/tasks/${taskId}/unassign`,
                    type: 'POST',
                    data: JSON.stringify({ person_id: personId }),
                    contentType: 'application/json',
                    success: function(response) {
                        if(response.success) {
                            showAlert('Persona desasignada correctamente', 'success');
                            loadTasks();
                        } else {
                            showAlert(response.message || 'Error al desasignar', 'danger');
                        }
                    },
                    error: function(xhr) {
                        const errorMsg = xhr.responseJSON?.message || 'Error en la solicitud';
                        showAlert(errorMsg, 'danger');
                    }
                });
            }
        }

        function showAlert(message, type) {
            const alert = $(`
                <div class="alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `);
            $('body').append(alert);
            setTimeout(() => alert.alert('close'), 5000);
        }
        function resetTaskForm() {
            $('#taskId').val('');
            $('#title').val('');
            $('#description').val('');
            $('#status').val('0');
            $('#taskForm')[0].reset();
        }