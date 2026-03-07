// ===============================
// main.js – Password manager JS
// ===============================


document.getElementById('search-input').focus();
const formFields = [
    document.querySelector('select[name="category"]'),
    document.querySelector('input[name="domain"]'),
    document.querySelector('input[name="username"]'),
    document.querySelector('#password-input'),
    document.querySelector('input[name="email"]'),
    document.querySelector('textarea[name="note"]'),
    document.querySelector('button[name="add_password"]')
];

formFields.forEach((field, i) => {
    if (!field) return;
    field.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const next = formFields[i + 1];
            if (next) next.focus();
            else field.click(); 
        }
    });
});


// ⏱ Idle timers
let idleTimer;
let countdownTimer;
let secondsLeft = 30;
const IDLE_TIME = 4.5 * 60 * 1000; // 4 minutes 30 seconds

// Start the idle timer
function startTimers() {
    clearTimeout(idleTimer);
    idleTimer = setTimeout(showTimeoutWarning, IDLE_TIME);
}

// Show logout warning modal
function showTimeoutWarning() {
    const modal = document.getElementById('timeout-modal');
    if (!modal) return;

    modal.classList.replace('hidden', 'block');
    secondsLeft = 30;
    document.getElementById('timer-seconds').innerText = secondsLeft;

    countdownTimer = setInterval(() => {
        secondsLeft--;
        document.getElementById('timer-seconds').innerText = secondsLeft;
        if (secondsLeft <= 0) {
            clearInterval(countdownTimer);
            window.location.href = 'logout.php';
        }
    }, 1000);
}

// Reset timers on user activity
['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'].forEach(evt => {
    document.addEventListener(evt, () => {
        const modal = document.getElementById('timeout-modal');
        if (modal && modal.classList.contains('hidden')) {
            clearTimeout(idleTimer);
            startTimers();
        }
    });
});

// Reset modal and timers manually
function resetTimers() {
    const modal = document.getElementById('timeout-modal');
    if (modal) modal.classList.replace('block', 'hidden');

    clearTimeout(idleTimer);
    clearInterval(countdownTimer);
    startTimers();
}

// ===============================
// Password strength checker
// ===============================
function checkStrength(password, barId, textId) {
    const bar = document.getElementById(barId);
    const text = document.getElementById(textId);
    if (!bar) return;

    let strength = 0;

    if (!password) {
        bar.style.width = '0%';
        bar.style.boxShadow = 'none';
        if (text) text.innerText = '';
        return;
    }

    if (password.length >= 8) strength += 25;
    if (password.match(/[A-Z]/)) strength += 25;
    if (password.match(/[0-9]/)) strength += 25;
    if (password.match(/[^A-Za-z0-9]/)) strength += 25;

    bar.style.width = strength + '%';

    // Set color and label based on strength
    if (strength <= 25) {
        bar.className = 'strength-bar bg-red-500';
        bar.style.boxShadow = '0 0 10px rgba(239,68,68,0.5)';
        if (text) {
            text.innerText = 'Weak';
            text.className = 'text-[9px] text-red-500 uppercase font-bold';
        }
    } else if (strength <= 50) {
        bar.className = 'strength-bar bg-orange-500';
        bar.style.boxShadow = '0 0 10px rgba(249,115,22,0.5)';
        if (text) {
            text.innerText = 'Fair';
            text.className = 'text-[9px] text-orange-500 uppercase font-bold';
        }
    } else if (strength <= 75) {
        bar.className = 'strength-bar bg-blue-500';
        bar.style.boxShadow = '0 0 10px rgba(59,130,246,0.5)';
        if (text) {
            text.innerText = 'Good';
            text.className = 'text-[9px] text-blue-500 uppercase font-bold';
        }
    } else {
        bar.className = 'strength-bar bg-emerald-500';
        bar.style.boxShadow = '0 0 15px rgba(16,185,129,0.7)';
        if (text) {
            text.innerText = 'Strong';
            text.className = 'text-[9px] text-emerald-500 uppercase font-bold';
        }
    }
}

// ===============================
// Search filter
// ===============================
document.getElementById('search-input').addEventListener('input', (e) => {
    const q = e.target.value.toLowerCase();
    document.querySelectorAll('.password-card').forEach(c => {
        c.style.display = c.dataset.domain.includes(q) ? 'block' : 'none';
    });
});

// ===============================
// Password generation
// ===============================
function generatePassword() {
    const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";
    let pass = "";
    for (let i = 0; i < 16; i++) pass += chars.charAt(Math.floor(Math.random() * chars.length));

    document.getElementById('password-input').value = pass;
    checkStrength(pass, 'strength-bar-add', 'strength-text-add');
}

function generatePasswordForModal(inputId) {
    const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";
    let pass = "";
    for (let i = 0; i < 16; i++) pass += chars.charAt(Math.floor(Math.random() * chars.length));

    const input = document.getElementById(inputId);
    input.value = pass;
    input.type = 'text';

    const idNum = inputId.split('-')[1];
    checkStrength(pass, 'strength-bar-' + idNum, 'strength-text-' + idNum);
}

// ===============================
// Keyboard shortcuts
// ===============================
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey && (e.key === 'z' || e.key === 'Z')) {
        e.preventDefault();
        window.location.href = 'logout.php';
    }
});

// ===============================
// Password visibility toggle
// ===============================
function togglePasswordVisibility(inputId, buttonElement) {
    const passwordInput = document.getElementById(inputId);
    const eyeOpen = buttonElement.querySelector(`#eye-open-${inputId.replace('pass-', '')}`);
    const eyeClosed = buttonElement.querySelector(`#eye-closed-${inputId.replace('pass-', '')}`);

    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        eyeOpen.classList.add('hidden');
        eyeClosed.classList.remove('hidden');
    } else {
        passwordInput.type = "password";
        eyeOpen.classList.remove('hidden');
        eyeClosed.classList.add('hidden');
    }
}

// ===============================
// Copy password to clipboard
// ===============================
function copyToClipboard(id, btn) {
    const input = document.getElementById(id);
    navigator.clipboard.writeText(input.value).then(() => {
        const original = btn.innerHTML;
        btn.innerHTML = '<svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        setTimeout(() => { btn.innerHTML = original; }, 1500);
    });
}

// ===============================
// Modal management
// ===============================
function openModal(id) {
    document.getElementById('modal-' + id).classList.replace('hidden', 'flex');
    let pass = document.getElementById('pass-' + id).value;
    checkStrength(pass, 'strength-bar-' + id, 'strength-text-' + id);
    syncPasswordIcon('pass-' + id);
}

function closeModal(id) {
    const modal = document.getElementById('modal-' + id);
    if (!modal) return;
    modal.classList.replace('flex', 'hidden');

    const form = document.getElementById('edit-form-' + id);
    if (form) form.reset();

    const passInput = document.getElementById('pass-' + id);
    if (passInput) passInput.type = 'password';

    checkStrength(passInput.value, 'strength-bar-' + id, 'strength-text-' + id);
}

// ===============================
// Delete confirmation modal
// ===============================
function confirmDelete(id) {
    document.getElementById('delete-id-input').value = id;
    document.getElementById('delete-modal').classList.replace('hidden', 'flex');
}

function closeDeleteModal() {
    document.getElementById('delete-modal').classList.replace('flex', 'hidden');
}

// ===============================
// CSV import/export
// ===============================
function validateAndUploadCSV(input) {
    if (input.files[0]) document.getElementById('importForm').submit();
}

// ===============================
// Category filter
// ===============================
function filterCategory(cat, btn) {
    document.querySelectorAll('.category-pill').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    document.querySelectorAll('.password-card').forEach(card => {
        card.style.display = (cat === 'all' || card.dataset.category === cat) ? 'block' : 'none';
    });
}

// ===============================
// Confirm update
// ===============================
let currentUpdateId = null;
function confirmUpdate(id) {
    currentUpdateId = id;
    document.getElementById('update-confirm-modal').classList.replace('hidden', 'flex');
}

document.getElementById('final-update-btn').addEventListener('click', function () {
    if (currentUpdateId !== null) {
        document.getElementById('edit-form-' + currentUpdateId).submit();
    }
});

function closeUpdateModal() {
    document.getElementById('update-confirm-modal').classList.replace('flex', 'hidden');
}

// ===============================
// Terminate account modal
// ===============================
function openTerminateModal() {
    const modal = document.getElementById('terminate-modal');
    const content = document.getElementById('terminate-content');
    
    modal.classList.replace('hidden', 'flex');
    setTimeout(() => {
        content.classList.replace('scale-95', 'scale-100');
        content.classList.replace('opacity-0', 'opacity-100');
    }, 10);
}

function closeTerminateModal() {
    const modal = document.getElementById('terminate-modal');
    const content = document.getElementById('terminate-content');
    const input = document.getElementById('username-confirm-input');

    content.classList.replace('scale-100', 'scale-95');
    content.classList.replace('opacity-100', 'opacity-0');
    
    setTimeout(() => {
        modal.classList.replace('flex', 'hidden');
        input.value = '';
        toggleTerminateBtn(false);
    }, 200);
}

function toggleTerminateBtn(isActive) {
    const btn = document.getElementById('final-terminate-btn');
    if (isActive) {
        btn.disabled = false;
        btn.className = "flex-1 py-3 bg-red-600 text-white rounded-xl font-bold uppercase text-[10px] tracking-widest shadow-lg shadow-red-900/40 cursor-pointer transition-all";
    } else {
        btn.disabled = true;
        btn.className = "flex-1 py-3 bg-red-600/10 text-red-500/30 rounded-xl font-bold uppercase text-[10px] tracking-widest cursor-not-allowed transition-all shadow-none";
    }
}

// Enable terminate button when username matches
document.getElementById('username-confirm-input').addEventListener('input', function(e) {
    toggleTerminateBtn(e.target.value === window.currentUsername);
});

// ===============================
// Change master password modal
// ===============================
function openChangeMasterModal() {
    document.getElementById('change-master-modal').classList.replace('hidden', 'flex');
}

function closeChangeMasterModal() {
    document.getElementById('change-master-modal').classList.replace('flex', 'hidden');
}

// Start idle timer
startTimers();


function syncPasswordIcon(inputId) {
    const input = document.getElementById(inputId);
    const idNum = inputId.replace('pass-', '');
    const eyeOpen = document.getElementById(`eye-open-${idNum}`);
    const eyeClosed = document.getElementById(`eye-closed-${idNum}`);

    if (input.type === "password") {
        eyeOpen.classList.remove('hidden');
        eyeClosed.classList.add('hidden');
    } else {
        eyeOpen.classList.add('hidden');
        eyeClosed.classList.remove('hidden');
    }
}
