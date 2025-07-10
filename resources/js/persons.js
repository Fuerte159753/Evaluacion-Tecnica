$(document).ready(function() {
    loadPersons();
    $('#personForm').submit(function(e) {
        e.preventDefault();
        savePerson();
    });
    $('#avatarInput').change(function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#avatarPreview').attr('src', e.target.result).show();
            }
            reader.readAsDataURL(file);
        }
    });
});

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
function loadPersons() {
    $.ajax({
        url: '/api/persons',
        type: 'GET',
        success: function(response) {
            if(response.success) {
                renderPersons(response.data);
            } else {
                showAlert('Error al cargar personas', 'danger');
            }
        },
        error: function() {
            showAlert('Error al conectar con el servidor', 'danger');
        }
    });
}
function renderPersons(persons) {
    const tableBody = $('#personsTableBody');
    tableBody.empty();

    persons.forEach(person => {
        const avatarSrc = person.avatar 
            ? `data:image/png;base64,${person.avatar}`
            : 'https://via.placeholder.com/50';

        const row = `
            <tr>
                <td><img src="${avatarSrc}" class="avatar" alt="${person.name}"></td>
                <td>${person.name}</td>
                <td>
                    <button class="btn btn-sm btn-outline-danger delete-btn" data-id="${person.id}">
                        <i class="bi bi-trash"></i> Eliminar
                    </button>
                </td>
            </tr>
        `;
        tableBody.append(row);
    });
}
function savePerson() {
    const personId = $('#personId').val();
    const url = personId ? `/api/persons/${personId}` : '/api/persons';
    const method = personId ? 'PUT' : 'POST';

    const avatarFile = $('#avatarInput')[0].files[0];
    if (!avatarFile && !personId) {
        showAlert('Debes seleccionar un avatar', 'warning');
        return;
    }

    const formData = new FormData();
    formData.append('name', $('#name').val());
    if (avatarFile) {
        formData.append('avatar', avatarFile);
    }

    $.ajax({
        url: url,
        type: method,
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if(response.success) {
                showAlert(`Persona ${personId ? 'actualizada' : 'creada'} correctamente`, 'success');
                loadPersons();
                $('#personModal').modal('hide');
                $('#personForm')[0].reset();
                $('#avatarPreview').hide();
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
$(document).on('click', '.delete-btn', function() {
    const personId = $(this).data('id');
    if(confirm('¿Estás seguro de eliminar esta persona?')) {
        $.ajax({
            url: `/api/persons/${personId}`,
            type: 'DELETE',
            success: function(response) {
                if(response.success) {
                    showAlert('Persona eliminada correctamente', 'success');
                    loadPersons();
                } else {
                    showAlert(response.message || 'Error al eliminar', 'danger');
                }
            },
            error: function(xhr) {
                if(xhr.status === 404) {
                    showAlert('La persona no existe o ya fue eliminada', 'warning');
                    loadPersons();
                } else {
                    const errorMsg = xhr.responseJSON?.message || 'Error en la solicitud';
                    showAlert(errorMsg, 'danger');
                }
            }
        });
    }
});