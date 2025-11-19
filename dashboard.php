<?php 
require 'config.php';
redirecionarSeNaoLogado();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Calendário de Entrevistas</title>
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; background: #f8f9fa; }
        #calendar { max-width: 1100px; margin: 40px auto; background: white; border-radius: 10px; padding: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .fc-event { cursor: pointer; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded shadow-sm">
        <h2 class="mb-0">Olá, <strong><?= htmlspecialchars($_SESSION['usuario_nome']) ?></strong>!</h2>
        <a href="logout.php" class="btn btn-outline-danger">Sair</a>
    </div>

    <div id="calendar"></div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalEvento" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formEvento">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalTitulo">Nova Entrevista</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id">
                    <div class="mb-3">
                        <label class="form-label">Assunto da Entrevista</label>
                        <input type="text" class="form-control" id="titulo" required placeholder="Ex: Entrevista com João">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Data</label>
                        <input type="date" class="form-control" id="data" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Horário</label>
                        <input type="time" class="form-control" id="hora" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="btnExcluir" style="display:none;">Excluir</button>
                    <button type="submit" class="btn btn-success">Salvar Entrevista</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    const modal = new bootstrap.Modal(document.getElementById('modalEvento'));
    const form = document.getElementById('formEvento');
    const btnExcluir = document.getElementById('btnExcluir');

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'pt-br',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: 'eventos.php',
        eventColor: '#3788d8',
        dateClick: function(info) {
            form.reset();
            document.getElementById('id').value = '';
            btnExcluir.style.display = 'none';
            document.getElementById('modalTitulo').textContent = 'Nova Entrevista';
            document.getElementById('data').value = info.dateStr;
            modal.show();
        },
        eventClick: function(info) {
            document.getElementById('id').value = info.event.id;
            document.getElementById('titulo').value = info.event.title.split(' - ')[0];
            document.getElementById('data').value = info.event.start.toISOString().slice(0,10);
            document.getElementById('hora').value = info.event.start.toTimeString().slice(0,5);
            document.getElementById('modalTitulo').textContent = 'Editar Entrevista';
            btnExcluir.style.display = 'block';
            modal.show();
        }
    });
    calendar.render();

    // === VERSÃO FINAL QUE NUNCA FALHA ===
    form.onsubmit = function(e) {
        e.preventDefault();

        const id     = document.getElementById('id').value.trim();
        const titulo = document.getElementById('titulo').value.trim();
        const data   = document.getElementById('data').value;
        const hora   = document.getElementById('hora').value;

        if (!titulo || !data || !hora) {
            alert('Preencha todos os campos!');
            return;
        }

        const url = id ? 'editar.php' : 'agendar.php';
        const formData = new FormData();
        formData.append('titulo', titulo);
        formData.append('data',   data);
        formData.append('hora',   hora);
        if (id) formData.append('id', id);

        fetch(url, {
            method: 'POST',
            body: formData
        })
        .then(r => r.text())
        .then(resp => {
            if (resp.trim() === 'OK') {
                modal.hide();
                calendar.refetchEvents();
            } else {
                alert('Erro ao salvar: ' + resp);
            }
        })
        .catch(() => alert('Erro de conexão com o servidor'));
    };

    // EXCLUIR
    btnExcluir.onclick = function() {
        if (confirm('Tem certeza que quer excluir esta entrevista?')) {
            fetch('excluir.php?id=' + document.getElementById('id').value)
                .then(() => {
                    modal.hide();
                    calendar.refetchEvents();
                });
        }
    };
});
</script>
</body>
</html>