// ══════════════════════════════════════════════════════════════════════
// Calendário JavaScript — Google Calendar-inspired
// ══════════════════════════════════════════════════════════════════════

// ─── STATE ────────────────────────────────────────────────────────────────────
let currentView   = 'month';   // 'day' | 'week' | 'month'
let currentDate   = new Date();
let allTasks      = [];
let editingTaskId = null;

const DIAS_SEMANA  = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
const MESES        = ['Janeiro','Fevereiro','Março','Abril','Maio','Junho',
                      'Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];
const HOURS        = Array.from({length: 24}, (_, i) => i); // 0–23

// ─── INIT ─────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    setupViewToggle();
    setupNavigation();
    setupModal();
    setupLogout();
    carregarTarefas();
    closePopupOnOutsideClick();
});

// ─── FETCH TASKS ──────────────────────────────────────────────────────────────
function carregarTarefas() {
    fetch('api/tarefas.php?acao=listar&filtro=todas')
        .then(r => r.json())
        .then(data => {
            if (data.sucesso) {
                allTasks = data.dados;
                render();
            }
        })
        .catch(console.error);
}

// ─── RENDER DISPATCHER ────────────────────────────────────────────────────────
function render() {
    updateTitle();
    const container = document.getElementById('calendarContainer');
    container.innerHTML = '';

    if (currentView === 'month') renderMonth(container);
    else if (currentView === 'week') renderWeek(container);
    else renderDay(container);
}

// ─── TITLE ────────────────────────────────────────────────────────────────────
function updateTitle() {
    const el = document.getElementById('calendarTitle');
    if (!el) return;

    if (currentView === 'month') {
        el.textContent = `${MESES[currentDate.getMonth()]} ${currentDate.getFullYear()}`;
    } else if (currentView === 'week') {
        const isMobile = window.innerWidth <= 768;
        if (isMobile) {
            // 3-day view: yesterday/today/tomorrow
            const prev = new Date(currentDate); prev.setDate(currentDate.getDate() - 1);
            const next = new Date(currentDate); next.setDate(currentDate.getDate() + 1);
            el.textContent = `${prev.getDate()}–${next.getDate()} ${MESES[currentDate.getMonth()].slice(0, 3)} ${currentDate.getFullYear()}`;
        } else {
            const { start, end } = getWeekRange(currentDate);
            if (start.getMonth() === end.getMonth()) {
                el.textContent = `${start.getDate()}–${end.getDate()} de ${MESES[start.getMonth()]} ${start.getFullYear()}`;
            } else {
                el.textContent = `${start.getDate()} ${MESES[start.getMonth()].slice(0,3)} – ${end.getDate()} ${MESES[end.getMonth()].slice(0,3)} ${end.getFullYear()}`;
            }
        }
    } else {
        const opts = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
        el.textContent = currentDate.toLocaleDateString('pt-BR', opts);
    }
}

// ═══════════════════════════════════════════════════════════════════════════════
// MONTH VIEW
// ═══════════════════════════════════════════════════════════════════════════════
function renderMonth(container) {
    const grid = document.createElement('div');
    grid.className = 'calendar-grid';

    // Weekday headers
    const wh = document.createElement('div');
    wh.className = 'calendar-weekdays';
    DIAS_SEMANA.forEach(d => {
        const cell = document.createElement('div');
        cell.className = 'calendar-weekday';
        cell.textContent = d;
        wh.appendChild(cell);
    });
    grid.appendChild(wh);

    // Days grid
    const daysGrid = document.createElement('div');
    daysGrid.className = 'calendar-days';

    const year  = currentDate.getFullYear();
    const month = currentDate.getMonth();
    const firstDay = new Date(year, month, 1).getDay(); // 0=Sun
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const daysInPrev  = new Date(year, month, 0).getDate();
    const today = new Date(); today.setHours(0,0,0,0);

    // Fill leading days from prev month
    for (let i = firstDay - 1; i >= 0; i--) {
        const d = new Date(year, month - 1, daysInPrev - i);
        daysGrid.appendChild(createDayCell(d, true));
    }

    // Current month days
    for (let d = 1; d <= daysInMonth; d++) {
        const date = new Date(year, month, d);
        daysGrid.appendChild(createDayCell(date, false));
    }

    // Fill trailing days to complete grid
    const total = firstDay + daysInMonth;
    const trailing = total % 7 === 0 ? 0 : 7 - (total % 7);
    for (let d = 1; d <= trailing; d++) {
        const date = new Date(year, month + 1, d);
        daysGrid.appendChild(createDayCell(date, true));
    }

    grid.appendChild(daysGrid);
    container.appendChild(grid);
}

function createDayCell(date, otherMonth) {
    const today = new Date(); today.setHours(0,0,0,0);
    const isToday = date.getTime() === today.getTime();

    const cell = document.createElement('div');
    cell.className = 'calendar-day' +
        (otherMonth ? ' other-month' : '') +
        (isToday ? ' today' : '');

    const num = document.createElement('div');
    num.className = 'day-number';
    num.textContent = date.getDate();
    cell.appendChild(num);

    // Click on empty area → open create modal
    cell.addEventListener('click', (e) => {
        if (e.target === cell || e.target === num) {
            abrirModalCriar(date);
        }
    });

    // Events for this day
    const dayEvents = getTasksForDate(date);
    const eventsWrapper = document.createElement('div');
    eventsWrapper.className = 'day-events';

    const MAX_VISIBLE = 3;
    dayEvents.slice(0, MAX_VISIBLE).forEach(task => {
        const ev = document.createElement('div');
        ev.className = `day-event ${task.prioridade}${task.concluida ? ' concluida' : ''}`;
        ev.textContent = task.titulo;
        ev.addEventListener('click', (e) => { e.stopPropagation(); showEventPopup(task, e); });
        eventsWrapper.appendChild(ev);
    });

    if (dayEvents.length > MAX_VISIBLE) {
        const more = document.createElement('div');
        more.className = 'day-more';
        more.textContent = `+${dayEvents.length - MAX_VISIBLE} mais`;
        more.addEventListener('click', (e) => {
            e.stopPropagation();
            // Switch to day view on that date
            currentDate = new Date(date);
            currentView = 'day';
            document.querySelectorAll('.view-toggle button').forEach(b => {
                b.classList.toggle('ativo', b.dataset.view === 'day');
            });
            render();
        });
        eventsWrapper.appendChild(more);
    }

    cell.appendChild(eventsWrapper);
    return cell;
}

// ═══════════════════════════════════════════════════════════════════════════════
// WEEK VIEW
// ═══════════════════════════════════════════════════════════════════════════════
function renderWeek(container) {
    const isMobile = window.innerWidth <= 768;
    const { start } = getWeekRange(currentDate);
    let weekDays = Array.from({length: 7}, (_, i) => {
        const d = new Date(start);
        d.setDate(start.getDate() + i);
        return d;
    });

    // On mobile: show only 3 days centered on currentDate
    if (isMobile) {
        const center = new Date(currentDate); center.setHours(0,0,0,0);
        weekDays = [
            new Date(center.getFullYear(), center.getMonth(), center.getDate() - 1),
            center,
            new Date(center.getFullYear(), center.getMonth(), center.getDate() + 1)
        ];
    }

    const today = new Date(); today.setHours(0,0,0,0);

    // Scroll wrapper (enables horizontal scroll on mobile)
    const scrollWrap = document.createElement('div');
    scrollWrap.className = 'calendar-week-scroll';

    const cal = document.createElement('div');
    cal.className = 'calendar-week';
    if (isMobile) {
        cal.style.minWidth = '0';
    }

    // ── Header ──
    const numDays = weekDays.length;
    const header = document.createElement('div');
    header.className = 'week-header';
    header.style.gridTemplateColumns = `40px repeat(${numDays}, 1fr)`;
    if (isMobile) header.style.minWidth = '0';

    const spacer = document.createElement('div');
    spacer.className = 'week-header-spacer week-all-day-label';
    spacer.textContent = 'Todo dia';
    header.appendChild(spacer);

    weekDays.forEach(date => {
        const col = document.createElement('div');
        col.className = 'week-header-day' + (date.getTime() === today.getTime() ? ' today' : '');
        col.innerHTML = `
            <div class="week-day-name">${isMobile ? DIAS_SEMANA[date.getDay()] : DIAS_SEMANA[date.getDay()]}</div>
            <div class="week-day-number">${date.getDate()}</div>
        `;
        header.appendChild(col);
    });
    cal.appendChild(header);

    // ── All-day tasks row ──
    const allDayRow = document.createElement('div');
    allDayRow.className = 'week-header';
    allDayRow.style.gridTemplateColumns = `40px repeat(${numDays}, 1fr)`;
    allDayRow.style.borderBottom = '2px solid var(--border-primary)';
    if (isMobile) allDayRow.style.minWidth = '0';

    const allDayLabel = document.createElement('div');
    allDayLabel.className = 'week-header-spacer';
    allDayLabel.style.cssText = 'font-size:10px;color:var(--text-tertiary);display:flex;align-items:center;justify-content:flex-end;padding-right:var(--space-2);border-right:1px solid var(--border-secondary);';
    allDayLabel.textContent = 'Sem hora';
    allDayRow.appendChild(allDayLabel);

    weekDays.forEach(date => {
        const cell = document.createElement('div');
        cell.className = 'week-all-day';
        cell.style.borderRight = '1px solid var(--border-secondary)';

        const noTimeTasks = getTasksForDate(date).filter(t => !t.data_atual || !t.data_atual.includes(' ') || t.data_atual.endsWith(' 00:00:00'));
        noTimeTasks.slice(0, 2).forEach(task => {
            const ev = document.createElement('div');
            ev.className = `day-event ${task.prioridade}${task.concluida ? ' concluida' : ''}`;
            ev.textContent = task.titulo;
            ev.style.marginBottom = '2px';
            ev.addEventListener('click', (e) => { e.stopPropagation(); showEventPopup(task, e); });
            cell.appendChild(ev);
        });

        cell.addEventListener('click', (e) => {
            if (e.target === cell) abrirModalCriar(date);
        });
        allDayRow.appendChild(cell);
    });
    cal.appendChild(allDayRow);

    // ── Time Grid ──
    const body = document.createElement('div');
    body.className = 'week-body';
    body.style.gridTemplateColumns = `40px repeat(${numDays}, 1fr)`;
    if (isMobile) body.style.minWidth = '0';

    // Time column
    const timeCol = document.createElement('div');
    timeCol.className = 'week-time-col';
    HOURS.forEach(h => {
        const slot = document.createElement('div');
        slot.className = 'week-time-slot';
        slot.textContent = h > 0 ? `${String(h).padStart(2, '0')}:00` : '';
        timeCol.appendChild(slot);
    });
    body.appendChild(timeCol);

    // Day columns
    weekDays.forEach(date => {
        const col = document.createElement('div');
        col.className = 'week-day-col';

        HOURS.forEach(h => {
            const slot = document.createElement('div');
            slot.className = 'week-day-slot';

            // Tasks at this hour
            const hourTasks = getTasksForDate(date).filter(t => {
                if (!t.data_atual) return false;
                const parts = t.data_atual.split(' ');
                if (parts.length < 2) return false;
                const hour = parseInt(parts[1].split(':')[0]);
                return hour === h;
            });

            hourTasks.forEach(task => {
                const ev = document.createElement('div');
                ev.className = `day-view-event ${task.prioridade}`;
                ev.style.cssText = 'margin:2px 0;padding:3px 8px;';
                ev.innerHTML = `
                    <div class="day-view-event-title">${task.titulo}</div>
                    <div class="day-view-event-time">${formatHour(task.data_atual)}</div>
                `;
                ev.addEventListener('click', (e) => { e.stopPropagation(); showEventPopup(task, e); });
                slot.appendChild(ev);
            });

            slot.addEventListener('click', (e) => {
                if (e.target === slot) {
                    const d = new Date(date);
                    d.setHours(h, 0, 0, 0);
                    abrirModalCriar(d, h);
                }
            });
            col.appendChild(slot);
        });
        body.appendChild(col);
    });

    cal.appendChild(body);
    scrollWrap.appendChild(cal);
    container.appendChild(scrollWrap);

    // Scroll to 8am
    setTimeout(() => {
        const slots = body.querySelectorAll('.week-time-slot');
        if (slots[8]) slots[8].scrollIntoView({ block: 'start' });
    }, 50);
}

// ═══════════════════════════════════════════════════════════════════════════════
// DAY VIEW
// ═══════════════════════════════════════════════════════════════════════════════
function renderDay(container) {
    const today = new Date(); today.setHours(0,0,0,0);
    const isToday = currentDate.getTime() === today.getTime();
    const opts = { weekday: 'long', day: 'numeric', month: 'long' };

    const cal = document.createElement('div');
    cal.className = 'calendar-day-view';

    const header = document.createElement('div');
    header.className = 'day-view-header';
    header.innerHTML = `
        <div class="day-view-date" style="${isToday ? 'color:var(--primary-400)' : ''}">
            ${currentDate.toLocaleDateString('pt-BR', opts)}
            ${isToday ? ' · <span style="font-size:var(--font-sm);font-weight:var(--weight-normal);color:var(--primary-400)">Hoje</span>' : ''}
        </div>
    `;
    cal.appendChild(header);

    const body = document.createElement('div');
    body.className = 'day-view-body';

    // Time column
    const timesCol = document.createElement('div');
    timesCol.className = 'day-view-times';
    HOURS.forEach(h => {
        const slot = document.createElement('div');
        slot.className = 'day-view-time-slot';
        slot.textContent = h > 0 ? `${String(h).padStart(2, '0')}:00` : '';
        timesCol.appendChild(slot);
    });
    body.appendChild(timesCol);

    // Events column
    const eventsCol = document.createElement('div');
    eventsCol.className = 'day-view-events';

    // No-time tasks at top
    const noTimeTasks = getTasksForDate(currentDate).filter(t => !t.data_atual || !t.data_atual.includes(' ') || t.data_atual.endsWith(' 00:00:00'));
    if (noTimeTasks.length > 0) {
        const noTimeWrap = document.createElement('div');
        noTimeWrap.style.cssText = 'padding:var(--space-2);background:var(--bg-surface);border-bottom:1px solid var(--border-secondary);display:flex;flex-wrap:wrap;gap:4px;';
        noTimeTasks.forEach(task => {
            const ev = document.createElement('div');
            ev.className = `day-event ${task.prioridade}${task.concluida ? ' concluida' : ''}`;
            ev.textContent = task.titulo;
            ev.addEventListener('click', (e) => { e.stopPropagation(); showEventPopup(task, e); });
            noTimeWrap.appendChild(ev);
        });
        eventsCol.appendChild(noTimeWrap);
    }

    HOURS.forEach(h => {
        const slot = document.createElement('div');
        slot.className = 'day-view-slot';

        const hourTasks = getTasksForDate(currentDate).filter(t => {
            if (!t.data_atual) return false;
            const parts = t.data_atual.split(' ');
            if (parts.length < 2) return false;
            const hour = parseInt(parts[1].split(':')[0]);
            return hour === h;
        });

        hourTasks.forEach(task => {
            const ev = document.createElement('div');
            ev.className = `day-view-event ${task.prioridade}${task.concluida ? ' concluida' : ''}`;
            ev.innerHTML = `
                <div class="day-view-event-title">${task.titulo}</div>
                <div class="day-view-event-time">${formatHour(task.data_atual)}${task.descricao ? ' · ' + task.descricao : ''}</div>
            `;
            ev.addEventListener('click', (e) => { e.stopPropagation(); showEventPopup(task, e); });
            slot.appendChild(ev);
        });

        slot.addEventListener('click', (e) => {
            if (e.target === slot) {
                const d = new Date(currentDate);
                d.setHours(h, 0, 0, 0);
                abrirModalCriar(d, h);
            }
        });
        eventsCol.appendChild(slot);
    });

    body.appendChild(eventsCol);
    cal.appendChild(body);
    container.appendChild(cal);

    // Scroll to 7am
    setTimeout(() => {
        const slots = eventsCol.querySelectorAll('.day-view-slot');
        if (slots[7]) slots[7].scrollIntoView({ behavior: 'smooth', block: 'start' });
    }, 50);
}

// ═══════════════════════════════════════════════════════════════════════════════
// EVENT POPUP
// ═══════════════════════════════════════════════════════════════════════════════
function showEventPopup(task, event) {
    closePopup();

    const popup = document.getElementById('eventPopup');

    const prioridadeLabel = { alta: 'Alta', media: 'Média', baixa: 'Baixa' }[task.prioridade] || task.prioridade;
    const dataLabel = task.data_atual ? formatarDataHora(task.data_atual) : 'Sem data/hora';
    const recLabel  = task.recorrencia && task.recorrencia !== 'nenhuma'
        ? `<div class="event-popup-meta-item"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m17 2 4 4-4 4"/><path d="M3 11v-1a4 4 0 0 1 4-4h14"/><path d="m7 22-4-4 4-4"/><path d="M21 13v1a4 4 0 0 1-4 4H3"/></svg>${formatarRecorrencia(task.recorrencia)}</div>`
        : '';

    popup.innerHTML = `
        <div class="event-popup-header">
            <div class="event-popup-title">${task.titulo}</div>
            <button class="event-popup-close" onclick="closePopup()">&times;</button>
        </div>
        <div class="event-popup-meta">
            <div class="event-popup-meta-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                ${dataLabel}
            </div>
            <div class="event-popup-meta-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                Prioridade: ${prioridadeLabel}
            </div>
            ${task.descricao ? `<div class="event-popup-meta-item"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>${task.descricao}</div>` : ''}
            ${recLabel}
        </div>
        <div class="event-popup-actions">
            <button class="btn btn-secondary btn-small" onclick="closePopup(); abrirModalEditar(${task.id})">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                Editar
            </button>
            <button class="btn btn-danger btn-small" onclick="closePopup(); deletarTarefaCal(${task.id})">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                Deletar
            </button>
        </div>
    `;

    // Position popup near the clicked element
    const rect = event.currentTarget.getBoundingClientRect();
    const popupW = 320;
    const popupH = 250;
    const vpW    = window.innerWidth;
    const vpH    = window.innerHeight;

    let left = rect.right + 8;
    let top  = rect.top;

    if (left + popupW > vpW - 16) left = rect.left - popupW - 8;
    if (left < 16) left = 16;
    if (top + popupH > vpH - 16) top = vpH - popupH - 16;
    if (top < 16) top = 16;

    popup.style.left = `${left}px`;
    popup.style.top  = `${top}px`;
    popup.style.display = 'block';

    event.stopPropagation();
}

function closePopup() {
    const popup = document.getElementById('eventPopup');
    if (popup) popup.style.display = 'none';
}

function closePopupOnOutsideClick() {
    document.addEventListener('click', (e) => {
        const popup = document.getElementById('eventPopup');
        if (popup && popup.style.display !== 'none' && !popup.contains(e.target)) {
            closePopup();
        }
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closePopup();
    });
}

// ═══════════════════════════════════════════════════════════════════════════════
// MODAL CRUD
// ═══════════════════════════════════════════════════════════════════════════════
function setupModal() {
    const modalOverlay = document.getElementById('modalNovaTarefaCalOverlay');
    const btnNova = document.getElementById('btnNovaTarefaCal');
    const btnClose = modalOverlay.querySelector('.modal-close');
    const form = document.getElementById('formTarefaCal');
    const selectRec = document.getElementById('cal_recorrencia');
    const grpLimite = document.getElementById('cal_group_data_limite');

    btnNova.addEventListener('click', () => abrirModalCriar());
    btnClose.addEventListener('click', () => fecharModal());
    modalOverlay.addEventListener('click', (e) => {
        if (e.target === modalOverlay) fecharModal();
    });

    if (selectRec && grpLimite) {
        selectRec.addEventListener('change', () => {
            grpLimite.style.display = selectRec.value !== 'nenhuma' ? '' : 'none';
        });
    }

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        submitTarefaCal();
    });
}

function abrirModalCriar(date = null, hour = null) {
    editingTaskId = null;
    document.getElementById('modalCalTitle').textContent = 'Nova Tarefa';
    document.getElementById('btnSubmitCal').textContent = 'Criar Tarefa';
    document.getElementById('cal_tarefa_id').value = '';
    document.getElementById('formTarefaCal').reset();
    document.getElementById('mensagemErroCal').classList.remove('ativo');
    document.getElementById('cal_group_data_limite').style.display = 'none';

    // Pre-fill date/time if provided
    if (date) {
        const d = new Date(date);
        const h = hour !== null ? hour : d.getHours();
        d.setHours(h, 0, 0, 0);
        const offset = d.getTimezoneOffset() * 60000;
        document.getElementById('cal_data').value = new Date(d - offset).toISOString().substring(0, 16);
    }

    document.getElementById('modalNovaTarefaCalOverlay').classList.add('ativo');
    setTimeout(() => document.getElementById('cal_titulo').focus(), 100);
}

function abrirModalEditar(id) {
    const task = allTasks.find(t => t.id == id);
    if (!task) return;

    editingTaskId = id;
    document.getElementById('modalCalTitle').textContent = 'Editar Tarefa';
    document.getElementById('btnSubmitCal').textContent = 'Salvar Alterações';
    document.getElementById('cal_tarefa_id').value = task.id;
    document.getElementById('cal_titulo').value = task.titulo;
    document.getElementById('cal_descricao').value = task.descricao || '';
    document.getElementById('cal_prioridade').value = task.prioridade;
    document.getElementById('cal_recorrencia').value = task.recorrencia || 'nenhuma';

    const grpLimite = document.getElementById('cal_group_data_limite');
    const temRec = task.recorrencia && task.recorrencia !== 'nenhuma';
    grpLimite.style.display = temRec ? '' : 'none';

    if (task.data_atual) {
        document.getElementById('cal_data').value = task.data_atual.replace(' ', 'T').substring(0, 16);
    } else {
        document.getElementById('cal_data').value = '';
    }
    if (task.data_vencimento && temRec) {
        document.getElementById('cal_dataVencimento').value = task.data_vencimento.replace(' ', 'T').substring(0, 16);
    } else {
        document.getElementById('cal_dataVencimento').value = '';
    }

    document.getElementById('mensagemErroCal').classList.remove('ativo');
    document.getElementById('modalNovaTarefaCalOverlay').classList.add('ativo');
    setTimeout(() => document.getElementById('cal_titulo').focus(), 100);
}

function fecharModal() {
    document.getElementById('modalNovaTarefaCalOverlay').classList.remove('ativo');
    editingTaskId = null;
}

function submitTarefaCal() {
    const id         = document.getElementById('cal_tarefa_id').value;
    const titulo     = document.getElementById('cal_titulo').value.trim();
    const descricao  = document.getElementById('cal_descricao').value.trim();
    const prioridade = document.getElementById('cal_prioridade').value;
    const dataAtual  = document.getElementById('cal_data').value;
    const dataVenc   = document.getElementById('cal_dataVencimento').value;
    const recorrencia= document.getElementById('cal_recorrencia').value;
    const msgErro    = document.getElementById('mensagemErroCal');
    const botao      = document.getElementById('btnSubmitCal');

    msgErro.classList.remove('ativo');

    if (!titulo) {
        msgErro.textContent = 'O título é obrigatório';
        msgErro.classList.add('ativo');
        return;
    }
    if (recorrencia !== 'nenhuma' && !dataVenc) {
        msgErro.textContent = 'Informe a data limite para tarefas recorrentes';
        msgErro.classList.add('ativo');
        return;
    }

    botao.disabled = true;
    botao.innerHTML = '<span class="btn-spinner"></span> Salvando...';

    const fd = new FormData();
    if (id) fd.append('id', id);
    fd.append('titulo', titulo);
    fd.append('descricao', descricao);
    fd.append('prioridade', prioridade);
    fd.append('data_atual', dataAtual);
    fd.append('data_vencimento', dataVenc);
    fd.append('recorrencia', recorrencia);

    const acao = id ? 'editar' : 'adicionar';

    fetch(`api/tarefas.php?acao=${acao}`, { method: 'POST', body: fd })
    .then(r => r.json())
    .then(data => {
        if (data.sucesso) {
            fecharModal();
            carregarTarefas();
        } else {
            msgErro.textContent = data.mensagem || 'Erro ao salvar';
            msgErro.classList.add('ativo');
        }
    })
    .catch(() => {
        msgErro.textContent = 'Erro de comunicação com o servidor';
        msgErro.classList.add('ativo');
    })
    .finally(() => {
        botao.disabled = false;
        botao.textContent = editingTaskId ? 'Salvar Alterações' : 'Criar Tarefa';
    });
}

function deletarTarefaCal(tarefaId) {
    confirmarAcao(
        'Deletar Tarefa',
        'Deseja deletar esta tarefa permanentemente? Esta ação não pode ser desfeita.',
        function() {
            const fd = new FormData();
            fd.append('id', tarefaId);
            fetch('api/tarefas.php?acao=deletar', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(data => { if (data.sucesso) carregarTarefas(); });
        }
    );
}

// ═══════════════════════════════════════════════════════════════════════════════
// NAVIGATION
// ═══════════════════════════════════════════════════════════════════════════════
function setupNavigation() {
    document.getElementById('btnPrev').addEventListener('click', () => {
        navigate(-1);
    });
    document.getElementById('btnNext').addEventListener('click', () => {
        navigate(1);
    });
    document.getElementById('btnToday').addEventListener('click', () => {
        currentDate = new Date();
        render();
    });
}

function navigate(dir) {
    if (currentView === 'month') {
        currentDate = new Date(currentDate.getFullYear(), currentDate.getMonth() + dir, 1);
    } else if (currentView === 'week') {
        const isMobile = window.innerWidth <= 768;
        currentDate = new Date(currentDate);
        currentDate.setDate(currentDate.getDate() + dir * (isMobile ? 3 : 7));
    } else {
        currentDate = new Date(currentDate);
        currentDate.setDate(currentDate.getDate() + dir);
    }
    render();
}

// ═══════════════════════════════════════════════════════════════════════════════
// VIEW TOGGLE
// ═══════════════════════════════════════════════════════════════════════════════
function setupViewToggle() {
    document.querySelectorAll('.view-toggle button').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.view-toggle button').forEach(b => b.classList.remove('ativo'));
            this.classList.add('ativo');
            currentView = this.dataset.view;
            render();
        });
    });
}

// ═══════════════════════════════════════════════════════════════════════════════
// HELPERS
// ═══════════════════════════════════════════════════════════════════════════════
function getTasksForDate(date) {
    const target = new Date(date); target.setHours(0, 0, 0, 0);

    return allTasks.filter(task => {
        if (!task.data_atual) return false;

        const taskDateStr = task.data_atual.split(' ')[0];
        const taskDate = new Date(taskDateStr + 'T00:00:00');

        // Direct match
        if (taskDate.getTime() === target.getTime()) return true;

        // Recurrence logic
        if (task.recorrencia && task.recorrencia !== 'nenhuma') {
            const limiteStr = task.data_vencimento ? task.data_vencimento.split(' ')[0] : null;
            const limite = limiteStr ? new Date(limiteStr + 'T00:00:00') : null;

            if (target < taskDate) return false;
            if (limite && target > limite) return false;

            switch (task.recorrencia) {
                case 'diaria':
                    return true;
                case 'semanal':
                    return target.getDay() === taskDate.getDay();
                case 'dias_semana': {
                    const dow = target.getDay();
                    return dow >= 1 && dow <= 5;
                }
                case 'anual':
                    return target.getMonth() === taskDate.getMonth() &&
                           target.getDate()  === taskDate.getDate();
                default:
                    return false;
            }
        }
        return false;
    });
}

function getWeekRange(date) {
    const d = new Date(date);
    const dow = d.getDay(); // 0=Sun
    const start = new Date(d);
    start.setDate(d.getDate() - dow);
    start.setHours(0, 0, 0, 0);
    const end = new Date(start);
    end.setDate(start.getDate() + 6);
    end.setHours(23, 59, 59, 999);
    return { start, end };
}

function formatHour(dataStr) {
    if (!dataStr) return '';
    const parts = dataStr.split(' ');
    if (parts.length < 2) return '';
    return parts[1].substring(0, 5);
}

function formatarDataHora(dataStr) {
    if (!dataStr) return 'Sem data';
    const [datePart, timePart] = dataStr.split(' ');
    const [ano, mes, dia] = datePart.split('-');
    const result = `${dia}/${mes}/${ano}`;
    if (timePart && timePart !== '00:00:00') return `${result} às ${timePart.substring(0, 5)}`;
    return result;
}

function formatarRecorrencia(rec) {
    const map = { diaria: 'Diária', semanal: 'Semanal', anual: 'Anual', dias_semana: 'Dias de semana (Seg–Sex)' };
    return map[rec] || rec;
}

// ─── LOGOUT ───────────────────────────────────────────────────────────────────
function setupLogout() {
    const btn = document.getElementById('btnLogout');
    if (!btn) return;
    btn.addEventListener('click', () => {
        confirmarAcao(
            'Sair da conta',
            'Deseja sair da sua conta?',
            () => {
                fetch('api/auth.php?acao=logout', { method: 'POST' })
                .then(() => { window.location.href = 'login.php'; });
            },
            '👋'
        );
    });
}
