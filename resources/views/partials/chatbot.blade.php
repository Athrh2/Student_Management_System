{{-- Floating Chatbot Widget --}}
{{-- resources/views/partials/chatbot.blade.php --}}

<div id="chatbot-launcher" onclick="toggleChat()">
    <img src="{{ asset('images/robot.png') }}" alt="AI" style="width:30px;height:30px;object-fit:contain;">
    <span id="chatbot-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:.6rem;">1</span>
</div>

<div id="chatbot-window" class="d-none">
    <div id="chat-header">
        <div style="display:flex;align-items:center;gap:8px;">
            <img src="{{ asset('images/robot.png') }}" style="width:20px;height:20px;object-fit:contain;">
            <span style="font-weight:700;font-size:.9rem;">AI Assistant</span>
        </div>
        <span onclick="toggleChat()" style="cursor:pointer;font-size:1rem;opacity:.8;">✕</span>
    </div>

    <div id="chat-body">
        <div class="msg-bot">
            👋 Hello! Ask me about your students.<br>
            <small style="color:#999;">e.g. <em>"who will fail in Ekonomi?"</em></small>
        </div>
    </div>

    <div id="chat-chips">
        <span class="chip" onclick="useChip(this)">Who will fail in Ekonomi?</span>
        <span class="chip" onclick="useChip(this)">Top students in Fizik</span>
        <span class="chip" onclick="useChip(this)">List all Biologi students</span>
        <span class="chip" onclick="useChip(this)">Lowest attendance</span>
        <span class="chip" onclick="useChip(this)">How many in Kimia?</span>
    </div>

    <div id="chat-footer">
        <input type="text" id="chat-input" placeholder="Type your question..." onkeydown="if(event.key==='Enter')sendMessage()">
        <button onclick="sendMessage()">
            <i class="fas fa-paper-plane" style="font-size:.8rem;"> > </i>
        </button>
    </div>
</div>

<style>
/* Launcher */
#chatbot-launcher {
    position: fixed;
    bottom: 28px; right: 28px;
    width: 62px; height: 62px;
    background: linear-gradient(135deg,#cd67f6,#f395e5);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    z-index: 9999;
    box-shadow: 0 4px 16px rgba(160,60,220,.35);
    transition: transform .2s;
}
#chatbot-launcher:hover { transform: scale(1.08); }

/* Window */
#chatbot-window {
    position: fixed;
    bottom: 102px; right: 28px;
    width: 370px;
    height: 540px;
    border-radius: 16px;
    background: #f6f6f6;
    box-shadow: 0 8px 32px rgba(0,0,0,.18);
    z-index: 10000;
    display: flex;
    flex-direction: column;
    /* NO overflow:hidden here — that was clipping the table */
}

/* Header */
#chat-header {
    background: linear-gradient(135deg,#cd67f6,#f395e5);
    color: #fff;
    padding: 11px 14px;
    display: flex; align-items: center; justify-content: space-between;
    border-radius: 16px 16px 0 0;
    flex-shrink: 0;
}

/* Message area — KEY: min-height:0 + overflow-y:auto */
#chat-body {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    padding: 10px;
    display: flex;
    flex-direction: column;
    gap: 8px;
    background: #f6f6f6;
}

/* Bubbles */
.msg-bot {
    background: #fff;
    border-left: 3px solid #cd67f6;
    border-radius: 0 10px 10px 0;
    padding: 8px 11px;
    font-size: .82rem;
    line-height: 1.5;
    max-width: 92%;
    box-shadow: 0 1px 3px rgba(0,0,0,.06);
    word-break: break-word;
}
.msg-user {
    background: linear-gradient(135deg,#cd67f6,#f395e5);
    color: #fff;
    border-radius: 10px 0 0 10px;
    padding: 8px 11px;
    font-size: .82rem;
    max-width: 85%;
    align-self: flex-end;
    word-break: break-word;
}

/* Table wrapper — inside msg-bot so it inherits the bubble scroll */
.tbl-wrap {
    margin-top: 6px;
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid #eee;
    width: 100%;
}
.tbl-label {
    background: linear-gradient(135deg,#cd67f6,#f395e5);
    color: #fff;
    padding: 4px 8px;
    font-size: .74rem;
    font-weight: 700;
}
.tbl-scroll {
    max-height: 180px;
    overflow-y: auto;
    overflow-x: auto;   /* allow horizontal scroll */
    -webkit-overflow-scrolling: touch;
}
.tbl-wrap table {
    border-collapse: collapse;
    font-size: .75rem;
    table-layout: auto;  /* let columns size to content */
    min-width: 320px;    /* ensures all columns visible, triggers scroll */
}
.tbl-wrap thead tr { background: #f3e8ff; }
.tbl-wrap th {
    padding: 4px 10px;
    color: #7c3aed;
    font-size: .7rem;
    font-weight: 700;
    text-transform: uppercase;
    text-align: left;
    white-space: nowrap; /* headers never wrap */
}
.tbl-wrap td {
    padding: 4px 10px;
    border-top: 1px solid #f3e8ff;
    white-space: nowrap; /* keep rows on one line, scroll instead */
}
.tbl-wrap tbody tr:hover td { background: #fdf4ff; }

.badge-risk { padding:2px 6px; border-radius:20px; font-size:.68rem; font-weight:600; display:inline-block; }
.badge-risk.high   { background:#fee2e2; color:#dc2626; }
.badge-risk.medium { background:#fef9c3; color:#ca8a04; }
.badge-risk.low    { background:#dcfce7; color:#16a34a; }

/* Chips */
#chat-chips {
    display: flex; flex-wrap: wrap; gap: 5px;
    padding: 6px 10px;
    background: #fff;
    border-top: 1px solid #f0e6ff;
    flex-shrink: 0;
}
.chip {
    background: #fdf4ff; border: 1px solid #e2b8fd; color: #9333ea;
    border-radius: 20px; padding: 3px 10px; font-size: .71rem;
    cursor: pointer; transition: all .15s; white-space: nowrap;
}
.chip:hover { background: #cd67f6; color: #fff; border-color: #cd67f6; }

/* Footer */
#chat-footer {
    display: flex; gap: 6px; padding: 8px 10px;
    background: #fff;
    border-top: 1px solid #f0e6ff;
    border-radius: 0 0 16px 16px;
    flex-shrink: 0;
}
#chat-input {
    flex: 1;
    border: 1.5px solid #e2b8fd; border-radius: 20px;
    padding: 6px 14px; font-size: .84rem;
    outline: none; background: #fdf4ff; color: #333;
    transition: border-color .2s;
}
#chat-input:focus { border-color: #cd67f6; background: #fff; }
#chat-footer button {
    width: 36px; height: 36px; border-radius: 50%; border: none;
    background: linear-gradient(135deg,#cd67f6,#f395e5); color: #fff;
    cursor: pointer; display: flex; align-items: center; justify-content: center;
    transition: transform .15s; flex-shrink: 0;
}
#chat-footer button:hover { transform: scale(1.08); }

/* Typing */
.typing-dots { display:flex; gap:4px; padding:2px 0; align-items:center; }
.typing-dots span { width:7px; height:7px; border-radius:50%; background:#cd67f6; animation:blink .9s infinite; }
.typing-dots span:nth-child(2){ animation-delay:.2s; }
.typing-dots span:nth-child(3){ animation-delay:.4s; }
@keyframes blink { 0%,80%,100%{opacity:.2} 40%{opacity:1} }
</style>

<script>
function toggleChat() {
    document.getElementById('chatbot-window').classList.toggle('d-none');
    document.getElementById('chatbot-badge').classList.add('d-none');
}

function useChip(el) {
    document.getElementById('chat-input').value = el.textContent.trim();
    sendMessage();
}

async function sendMessage() {
    const input    = document.getElementById('chat-input');
    const chatBody = document.getElementById('chat-body');
    const text     = input.value.trim();
    if (!text) return;

    addMsg(chatBody, text, 'user');
    input.value = '';
    input.focus();

    const typingEl = addTyping(chatBody);

    try {
        const res  = await fetch('{{ route("chatbot.message") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ message: text }),
        });
        const data = await res.json();
        typingEl.remove();

        // Create ONE bot bubble that contains BOTH the reply text AND the table
        const bubble = document.createElement('div');
        bubble.className = 'msg-bot';

        // Reply text
        const textDiv = document.createElement('div');
        textDiv.innerHTML = fmtReply(data.reply);
        bubble.appendChild(textDiv);

        // Table inside same bubble — always visible, no separate flex child
        if (Array.isArray(data.students) && data.students.length > 0) {
            bubble.appendChild(buildTable(data.students, data.label, data.intent));
        }

        chatBody.appendChild(bubble);
        scrollEnd(chatBody);

    } catch (err) {
        typingEl.remove();
        addMsg(chatBody, '⚠️ Something went wrong. Please try again.', 'bot');
    }
}

function addMsg(container, text, who) {
    const div = document.createElement('div');
    div.className = who === 'bot' ? 'msg-bot' : 'msg-user';
    div.textContent = text;
    container.appendChild(div);
    scrollEnd(container);
    return div;
}

function addTyping(container) {
    const div = document.createElement('div');
    div.className = 'msg-bot';
    div.innerHTML = '<div class="typing-dots"><span></span><span></span><span></span></div>';
    container.appendChild(div);
    scrollEnd(container);
    return div;
}

function buildTable(students, label, intent) {

    // Column definitions per intent
    function getCols(intent) {
        switch(intent) {
            case 'top_assignment':
            case 'low_assignment':
                return [
                    { label: 'Name',        val: s => x(s.name) },
                    { label: 'Course',      val: s => x(s.course) },
                    { label: 'Assignment',  val: s => x(s.assignment_score) + '%' },
                    { label: 'Test',        val: s => x(s.test_score) + '%' },
                    { label: 'Risk',        val: s => badge(s.risk_level) },
                ];
            case 'top_test':
            case 'low_test':
                return [
                    { label: 'Name',   val: s => x(s.name) },
                    { label: 'Course', val: s => x(s.course) },
                    { label: 'Test',   val: s => x(s.test_score) + '%' },
                    { label: 'Assign.',val: s => x(s.assignment_score) + '%' },
                    { label: 'Risk',   val: s => badge(s.risk_level) },
                ];
            case 'low_attendance':
            case 'high_attendance':
                return [
                    { label: 'Name',       val: s => x(s.name) },
                    { label: 'Course',     val: s => x(s.course) },
                    { label: 'Attendance', val: s => x(s.attendance_rate) + '%' },
                    { label: 'Progress',   val: s => x(s.current_progress ?? 0) + '%' },
                    { label: 'Risk',       val: s => badge(s.risk_level) },
                ];
            case 'likely_fail':
            case 'at_risk':
                return [
                    { label: 'Name',       val: s => x(s.name) },
                    { label: 'Course',     val: s => x(s.course) },
                    { label: 'Attendance', val: s => x(s.attendance_rate) + '%' },
                    { label: 'Progress',   val: s => x(s.current_progress ?? 0) + '%' },
                    { label: 'Risk',       val: s => badge(s.risk_level) },
                ];
            case 'top_students':
            case 'low_students':
                return [
                    { label: 'Name',     val: s => x(s.name) },
                    { label: 'Course',   val: s => x(s.course) },
                    { label: 'Year',     val: s => 'Year ' + x(s.year) },
                    { label: 'Progress', val: s => x(s.current_progress ?? 0) + '%' },
                    { label: 'Test',     val: s => x(s.test_score) + '%' },
                    { label: 'Assign.',  val: s => x(s.assignment_score) + '%' },
                    { label: 'Risk',     val: s => badge(s.risk_level) },
                ];
            case 'count':
            case 'course_list':
            default:
                return [
                    { label: 'Name',     val: s => x(s.name) },
                    { label: 'Course',   val: s => x(s.course) },
                    { label: 'Year',     val: s => 'Year ' + x(s.year) },
                    { label: 'Progress', val: s => x(s.current_progress ?? 0) + '%' },
                    { label: 'Risk',     val: s => badge(s.risk_level) },
                ];
        }
    }

    function badge(risk) {
        const r = String(risk ?? 'low').toLowerCase();
        return `<span class="badge-risk ${r}">${cap(r)}</span>`;
    }

    const cols = getCols(intent);

    // Build table
    const wrap   = document.createElement('div');
    wrap.className = 'tbl-wrap';

    const lbl = document.createElement('div');
    lbl.className = 'tbl-label';
    lbl.textContent = `📋 ${label ?? 'Results'} (${students.length})`;
    wrap.appendChild(lbl);

    const scroll = document.createElement('div');
    scroll.className = 'tbl-scroll';

    const tbl  = document.createElement('table');
    const thead = document.createElement('thead');
    thead.innerHTML = '<tr>' + cols.map(c => `<th>${c.label}</th>`).join('') + '</tr>';

    const tbody = document.createElement('tbody');
    students.forEach(s => {
        const tr = document.createElement('tr');
        tr.innerHTML = cols.map(c => `<td>${c.val(s)}</td>`).join('');
        tbody.appendChild(tr);
    });

    tbl.appendChild(thead);
    tbl.appendChild(tbody);
    scroll.appendChild(tbl);
    wrap.appendChild(scroll);
    return wrap;
}

function fmtReply(text) {
    if (!text) return '';
    return x(text).replace(/\*\*(.+?)\*\*/g,'<strong>$1</strong>').replace(/\n/g,'<br>');
}

// HTML escape
function x(v) {
    return String(v ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function cap(s) { return String(s).charAt(0).toUpperCase() + String(s).slice(1); }

function scrollEnd(el) { setTimeout(() => el.scrollTop = el.scrollHeight, 50); }
</script>