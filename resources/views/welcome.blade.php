<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Agenda Inteligente</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>

    <style>
        /* Estilos de tela cheia */
        html, body { height: 100%; margin: 0; overflow: hidden; }
        .app-container { height: 100vh; display: flex; flex-direction: column; }
        
        /* Personalização do Calendário */
        .fc { height: 100% !important; font-size: 0.9rem; }
        .fc-daygrid-day { cursor: pointer; transition: 0.2s; }
        .fc-daygrid-day:hover { background-color: #f1f5f9 !important; }
        
        /* Esconde explicitamente células de outros meses caso o script demore a carregar */
        .fc-day-other { visibility: hidden !important; pointer-events: none !important; }
        
        .priority-urgent { border-left: 5px solid #ef4444 !important; background: #fee2e2 !important; color: #b91c1c !important; }
        .priority-important { border-left: 5px solid #f59e0b !important; background: #fef3c7 !important; color: #b45309 !important; }
        .priority-optional { border-left: 5px solid #10b981 !important; background: #d1fae5 !important; color: #047857 !important; }
        
        .fc-event { border: none !important; margin: 2px 4px !important; padding: 2px 5px !important; font-weight: bold; font-size: 0.85em; border-radius: 6px !important; }

        @media (max-width: 640px) {
            .fc-toolbar { flex-direction: column; gap: 8px; }
            .fc-toolbar-title { font-size: 1.1rem !important; }
            #clock { display: none; }
            .app-header { padding: 0.75rem 1rem; }
        }

        #notification-container {
            position: fixed; bottom: 20px; right: 20px; z-index: 9999;
            display: flex; flex-direction: column; gap: 10px; max-width: 320px;
        }
        .notif-item { animation: slideIn 0.3s ease-out; }
        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        .dot { height: 8px; width: 8px; border-radius: 50%; display: inline-block; margin-right: 4px; }
        .dot-urgent { background-color: #ef4444; }
        .dot-important { background-color: #f59e0b; }
        .dot-optional { background-color: #10b981; }
    </style>
</head>
<body class="bg-slate-50">

    <div class="app-container">
        <header class="app-header p-6 bg-indigo-600 text-white flex justify-between items-center shadow-lg">
            <h1 class="text-xl md:text-2xl font-bold"><i class="fas fa-calendar-alt mr-2"></i> Minha Agenda</h1>
            <p id="clock" class="text-sm font-mono bg-indigo-500 px-3 py-1 rounded-lg"></p>
        </header>

        <main class="flex-grow bg-white overflow-hidden">
            <div id="calendar" class="h-full p-2 md:p-6"></div>
        </main>
    </div>

    <div id="notification-container"></div>

    <div id="actionModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-40 p-4">
        <div class="bg-white rounded-2xl w-full max-w-sm shadow-2xl p-6 text-center">
            <h2 id="actionDateLabel" class="text-xl font-bold mb-6 text-slate-800"></h2>
            <div class="grid grid-cols-1 gap-4">
                <button onclick="showDayTasks()" class="bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold py-4 rounded-xl flex items-center justify-center gap-2">
                    <i class="fas fa-list"></i> Ver Compromissos
                </button>
                <button onclick="openFormModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-xl flex items-center justify-center gap-2">
                    <i class="fas fa-plus"></i> Agendar Novo
                </button>
                <button onclick="closeModals()" class="text-slate-400 text-sm mt-2">Cancelar</button>
            </div>
        </div>
    </div>

    <div id="listModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl flex flex-col max-h-[80vh]">
            <div class="p-6 border-b flex justify-between items-center">
                <h2 class="text-xl font-bold">Compromissos do Dia</h2>
                <button onclick="closeModals()" class="text-slate-400 text-2xl">&times;</button>
            </div>
            <div id="dayTasksList" class="p-6 space-y-4 overflow-y-auto"></div>
        </div>
    </div>

    <div id="formModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-[60] p-4">
        <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl">
            <div class="p-6 border-b flex justify-between items-center">
                <h2 id="formTitle" class="text-lg font-bold">Novo Compromisso</h2>
                <button onclick="closeModals()" class="text-slate-400 text-2xl">&times;</button>
            </div>
            <form id="taskForm" class="p-6 space-y-4">
                <input type="hidden" id="taskId">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nome</label>
                    <input type="text" id="title" class="w-full border-2 p-3 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Descrição</label>
                    <textarea id="description" class="w-full border-2 p-3 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none" rows="3"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Data</label>
                    <input type="date" id="taskDate" class="w-full border-2 p-3 rounded-xl outline-none" required>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Prioridade</label>
                        <select id="priority" class="w-full border-2 p-3 rounded-xl bg-white outline-none">
                            <option value="urgent">🔴 Urgente</option>
                            <option value="important">🟡 Importante</option>
                            <option value="optional">🟢 Opcional</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Status</label>
                        <select id="category" class="w-full border-2 p-3 rounded-xl bg-white outline-none">
                            <option value="fechado">🔒 Fechado</option>
                            <option value="andamento">⏳ Em andamento</option>
                            <option value="finalizado">✅ Finalizado</option>
                        </select>
                    </div>
                </div>

                <div class="flex gap-2 pt-4">
                    <button type="button" id="btnDelete" onclick="deleteTask()" class="hidden flex-1 bg-rose-50 text-rose-600 font-bold py-3 rounded-xl border border-rose-200">Remover</button>
                    <button type="submit" class="flex-[2] bg-indigo-600 text-white font-bold py-3 rounded-xl hover:bg-indigo-700">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    let calendar;
    let selectedDay;
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    function showFlashcard(message, type = 'success') {
        const container = document.getElementById('notification-container');
        const flashcard = document.createElement('div');
        const bgColor = type === 'success' ? 'bg-emerald-600' : 'bg-rose-600';
        
        flashcard.className = `notif-item ${bgColor} text-white p-4 rounded-xl shadow-2xl flex justify-between items-center min-w-[280px]`;
        flashcard.innerHTML = `
            <div class="flex items-center gap-2">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-trash-alt'}"></i>
                <span class="font-bold text-sm">${message}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-white/80 hover:text-white ml-4 text-xl">&times;</button>
        `;

        container.appendChild(flashcard);
        setTimeout(() => { if (flashcard.parentElement) flashcard.remove(); }, 3000);
    }

    document.addEventListener('DOMContentLoaded', function() {
        setInterval(() => { 
            const clockEl = document.getElementById('clock');
            if(clockEl) clockEl.innerText = new Date().toLocaleTimeString('pt-BR'); 
        }, 1000);

        const calendarEl = document.getElementById('calendar');
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: window.innerWidth < 768 ? 'dayGridDay' : 'dayGridMonth',
            locale: 'pt-br',
            height: '100%',
            displayEventTime: false,
            
            // --- AJUSTES SOLICITADOS ---
            showNonCurrentDates: false, // Não mostra dias de outros meses
            fixedWeekCount: false,      // Ajusta a altura se o mês tiver 4, 5 ou 6 semanas
            // ---------------------------

            headerToolbar: { 
                left: 'prev,next today', 
                center: 'title', 
                right: 'dayGridMonth,dayGridWeek,dayGridDay' 
            },
            buttonText: { today: 'Hoje', month: 'Mês', week: 'Semana', day: 'Dia' },
            dayCellDidMount: function(info) {
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                if (info.date < today && info.isCurrent) {
                    info.el.style.backgroundColor = 'rgba(239, 68, 68, 0.08)';
                }
            },
            dateClick: (info) => {
                selectedDay = info.dateStr;
                document.getElementById('actionDateLabel').innerText = new Date(selectedDay + "T00:00:00").toLocaleDateString('pt-BR', {day:'numeric', month:'long'});
                document.getElementById('actionModal').classList.remove('hidden');
            },
            eventDataTransform: function(task) {
                return {
                    id: task.id,
                    title: task.title,
                    start: task.start_time,
                    end: task.end_time,
                    extendedProps: { 
                        priority: task.priority, 
                        description: task.description, 
                        category: task.category
                    }
                };
            },
            eventContent: function(arg) {
                const status = arg.event.extendedProps.category || 'fechado';
                const icon = status === 'finalizado' ? '✅' : (status === 'andamento' ? '⏳' : '🔒');
                let titleEl = document.createElement('div');
                titleEl.className = 'fc-event-title-container flex items-center gap-1 overflow-hidden text-xs';
                titleEl.innerHTML = `<span>${icon}</span> <span class="truncate">${arg.event.title}</span>`;
                return { domNodes: [titleEl] };
            },
            events: "{{ url('/tasks') }}",
            eventClassNames: (arg) => ['priority-' + arg.event.extendedProps.priority],
            windowResize: function(view) {
                if (window.innerWidth < 768) {
                    calendar.changeView('dayGridDay');
                } else {
                    calendar.changeView('dayGridMonth');
                }
            }
        });
        calendar.render();
        checkTodayNotifications();
    });

    async function showDayTasks() {
        const res = await fetch(`{{ url('/tasks') }}?date=${selectedDay}`, {
            headers: { 'Accept': 'application/json' }
        });
        const tasks = await res.json();
        const list = document.getElementById('dayTasksList');
        closeModals();
        document.getElementById('listModal').classList.remove('hidden');

        list.innerHTML = tasks.length ? '' : '<p class="text-center text-slate-400 py-10">Nenhum compromisso.</p>';
        tasks.forEach(task => {
            const dotClass = task.priority === 'urgent' ? 'dot-urgent' : (task.priority === 'important' ? 'dot-important' : 'dot-optional');
            const statusIcon = task.category === 'finalizado' ? '✅' : (task.category === 'andamento' ? '⏳' : '🔒');
            const statusLabel = task.category ? task.category.charAt(0).toUpperCase() + task.category.slice(1) : 'Fechado';
            
            list.innerHTML += `
                <div onclick='editTask(${JSON.stringify(task).replace(/'/g, "&apos;")})' class="p-4 border-2 border-slate-50 rounded-xl hover:border-indigo-200 cursor-pointer bg-white transition-all shadow-sm">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full bg-slate-100 flex items-center">
                            <span class="dot ${dotClass}"></span>${task.priority}
                        </span>
                        <span class="text-[10px] font-bold text-slate-400 uppercase italic flex items-center gap-1">
                            ${statusIcon} ${statusLabel}
                        </span>
                    </div>
                    <h4 class="font-bold text-slate-800">${task.title}</h4>
                    <p class="text-xs text-slate-500 line-clamp-2">${task.description || ''}</p>
                </div>`;
        });
    }

    function openFormModal() {
        document.getElementById('taskForm').reset();
        document.getElementById('taskId').value = "";
        document.getElementById('taskDate').value = selectedDay;
        document.getElementById('category').value = "fechado";
        document.getElementById('btnDelete').classList.add('hidden');
        closeModals();
        document.getElementById('formModal').classList.remove('hidden');
        document.getElementById('formTitle').innerText = "Novo Compromisso";
    }

    function editTask(task) {
        const dateOnly = task.start_time.split('T')[0] || task.start_time.split(' ')[0];
        document.getElementById('taskId').value = task.id;
        document.getElementById('title').value = task.title;
        document.getElementById('description').value = task.description || '';
        document.getElementById('taskDate').value = dateOnly;
        document.getElementById('priority').value = task.priority;
        document.getElementById('category').value = task.category || 'fechado';

        document.getElementById('btnDelete').classList.remove('hidden');
        closeModals();
        document.getElementById('formModal').classList.remove('hidden');
        document.getElementById('formTitle').innerText = "Editar Compromisso";
    }

    document.getElementById('taskForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.getElementById('taskId').value;
        const newDate = document.getElementById('taskDate').value;
        
        const payload = {
            title: document.getElementById('title').value,
            description: document.getElementById('description').value,
            priority: document.getElementById('priority').value,
            category: document.getElementById('category').value,
            start_time: `${newDate} 00:00:00`,
            end_time: `${newDate} 23:59:59`
        };

        const url = id ? `{{ url('/tasks') }}/${id}` : `{{ url('/tasks') }}`;
        const method = id ? 'PUT' : 'POST';
        
        try {
            const response = await fetch(url, {
                method: method, 
                headers: { 
                    'X-CSRF-TOKEN': csrf, 
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            });
            if (response.ok) {
                closeModals();
                calendar.refetchEvents();
                calendar.gotoDate(newDate);
                checkTodayNotifications();
                const msg = id ? "Tarefa editada com sucesso" : "Tarefa adicionada com sucesso";
                showFlashcard(msg, 'success');
            }
        } catch (error) { console.error('Erro na requisição:', error); }
    });

    async function deleteTask() {
        const id = document.getElementById('taskId').value;
        if (id && confirm('Excluir permanentemente este compromisso?')) {
            const response = await fetch(`{{ url('/tasks') }}/${id}`, { 
                method: 'DELETE', 
                headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' } 
            });
            if (response.ok) {
                closeModals();
                calendar.refetchEvents();
                checkTodayNotifications();
                showFlashcard('Mensagem excluída com sucesso', 'error');
            }
        }
    }

    async function checkTodayNotifications() {
        const today = new Date().toISOString().split('T')[0];
        const res = await fetch(`{{ url('/tasks') }}?date=${today}`, {
            headers: { 'Accept': 'application/json' }
        });
        if (res.ok) {
            const tasks = await res.json();
            const container = document.getElementById('notification-container');
            const items = container.querySelectorAll('.notif-item:not([class*="bg-emerald"]):not([class*="bg-rose"])');
            items.forEach(el => el.remove());
            tasks.forEach(task => {
                const dotClass = task.priority === 'urgent' ? 'dot-urgent' : (task.priority === 'important' ? 'dot-important' : 'dot-optional');
                const div = document.createElement('div');
                div.className = "notif-item bg-slate-900 text-white p-4 rounded-xl shadow-2xl border-l-4 border-indigo-500 flex justify-between items-start";
                div.innerHTML = `
                    <div>
                        <h4 class="font-bold text-sm flex items-center"><span class="dot ${dotClass}"></span>Hoje: ${task.title}</h4>
                        <p class="text-xs text-slate-400 mt-1">${task.description || 'Compromisso agendado'}</p>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-slate-500 hover:text-white ml-4 text-lg">&times;</button>
                `;
                container.appendChild(div);
            });
        }
    }

    function closeModals() {
        document.querySelectorAll('#actionModal, #listModal, #formModal').forEach(m => m.classList.add('hidden'));
    }
</script>
</body>
</html>