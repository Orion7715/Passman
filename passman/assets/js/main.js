
let idleTimer;
let countdownTimer;
let secondsLeft = 30;
const IDLE_TIME = 4.5 * 60 * 1000; 

function startTimers() {
    idleTimer = setTimeout(showTimeoutWarning, IDLE_TIME);
}

function showTimeoutWarning() {
    document.getElementById('timeout-modal').classList.replace('hidden', 'block');
    secondsLeft = 30;
    document.getElementById('timer-seconds').innerText = secondsLeft;

    countdownTimer = setInterval(() => {
        secondsLeft--;
        document.getElementById('timer-seconds').innerText = secondsLeft;
        if (secondsLeft <= 0) {
            window.location.href = 'logout.php';
        }
    }, 1000);
}

function resetTimers() {
    document.getElementById('timeout-modal').classList.replace('block', 'hidden');
    clearTimeout(idleTimer);
    clearInterval(countdownTimer);
    startTimers();
}

['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'].forEach(evt => {
    document.addEventListener(evt, () => {
        if (document.getElementById('timeout-modal').classList.contains('hidden')) {
            clearTimeout(idleTimer);
            startTimers();
        }
    });
});
startTimers();


function checkStrength(password, barId, textId) {
    const bar = document.getElementById(barId);
    const text = document.getElementById(textId);
    if (!bar) return;

    let strength = 0;
    if (!password) { 
        bar.style.width = '0%'; 
        if(text) text.innerText = ''; 
        bar.style.boxShadow = 'none';
        return; 
    }

    if (password.length >= 8) strength += 25;
    if (password.match(/[A-Z]/)) strength += 25;
    if (password.match(/[0-9]/)) strength += 25;
    if (password.match(/[^A-Za-z0-9]/)) strength += 25;

    bar.style.width = strength + '%';

    if (strength <= 25) { 
        bar.className = 'strength-bar bg-red-500'; 
        bar.style.boxShadow = '0 0 10px rgba(239, 68, 68, 0.5)';
        if(text) { text.innerText = 'Weak'; text.className = 'text-[9px] text-red-500 uppercase font-bold'; }
    } 
    else if (strength <= 50) { 
        bar.className = 'strength-bar bg-orange-500'; 
        bar.style.boxShadow = '0 0 10px rgba(249, 115, 22, 0.5)';
        if(text) { text.innerText = 'Fair'; text.className = 'text-[9px] text-orange-500 uppercase font-bold'; }
    } 
    else if (strength <= 75) { 
        bar.className = 'strength-bar bg-blue-500'; 
        bar.style.boxShadow = '0 0 10px rgba(59, 130, 246, 0.5)';
        if(text) { text.innerText = 'Good'; text.className = 'text-[9px] text-blue-500 uppercase font-bold'; }
    } 
    else { 
        bar.className = 'strength-bar bg-emerald-500'; 
        bar.style.boxShadow = '0 0 15px rgba(16, 185, 129, 0.7)';
        if(text) { text.innerText = 'Strong'; text.className = 'text-[9px] text-emerald-500 uppercase font-bold'; }
    }
}

document.getElementById('search-input').addEventListener('input', (e) => {
    const q = e.target.value.toLowerCase();
    document.querySelectorAll('.password-card').forEach(c => {
        c.style.display = c.dataset.domain.includes(q) ? 'block' : 'none';
    });
});

function generatePassword() {
    const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";
    let pass = ""; for (let i = 0; i < 16; i++) pass += chars.charAt(Math.floor(Math.random() * chars.length));
    document.getElementById('password-input').value = pass;
    checkStrength(pass, 'strength-bar-add', 'strength-text-add');
}

function generatePasswordForModal(inputId) {
    const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";
    let pass = ""; for (let i = 0; i < 16; i++) pass += chars.charAt(Math.floor(Math.random() * chars.length));
    const input = document.getElementById(inputId); 
    input.value = pass; 
    input.type = 'text';
    const idNum = inputId.split('-')[1];
    checkStrength(pass, 'strength-bar-' + idNum, 'strength-text-' + idNum);
}


document.addEventListener('keydown', function(e) {
    
    if (e.ctrlKey && (e.key === 'z' || e.key === 'Z')) {

        e.preventDefault();
        

        window.location.href = 'logout.php';
    }
});

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

function copyToClipboard(id, btn) {
    const input = document.getElementById(id);
    navigator.clipboard.writeText(input.value).then(() => {
        const original = btn.innerHTML;
        btn.innerHTML = '<svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        setTimeout(() => { btn.innerHTML = original; }, 1500);
    });
}

function openModal(id) { 
    document.getElementById('modal-'+id).classList.replace('hidden', 'flex'); 
    let pass = document.getElementById('pass-'+id).value;
    checkStrength(pass, 'strength-bar-'+id, 'strength-text-'+id);
}
function closeModal(id) { 

    document.getElementById('modal-'+id).classList.replace('flex', 'hidden'); 
    

    const form = document.getElementById('edit-form-' + id);
    if (form) {
        form.reset();
        

        const passInput = document.getElementById('pass-' + id);
        if (passInput) passInput.type = 'password';
        

        checkStrength(passInput.value, 'strength-bar-' + id, 'strength-text-' + id);
    }
}
function confirmDelete(id) {
    document.getElementById('delete-id-input').value = id;
    document.getElementById('delete-modal').classList.replace('hidden', 'flex');
}
function closeDeleteModal() { document.getElementById('delete-modal').classList.replace('flex', 'hidden'); }
function validateAndUploadCSV(input) { if(input.files[0]) document.getElementById('importForm').submit(); }

function filterCategory(cat, btn) {
    document.querySelectorAll('.category-pill').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.password-card').forEach(card => {
        if (cat === 'all' || card.dataset.category === cat) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

let currentUpdateId = null;

function confirmUpdate(id) {
    currentUpdateId = id;
    document.getElementById('update-confirm-modal').classList.replace('hidden', 'flex');
}

function closeUpdateModal() {
    document.getElementById('update-confirm-modal').classList.replace('flex', 'hidden');
}


document.getElementById('final-update-btn').addEventListener('click', () => {
    if (currentUpdateId) {

        const form = document.getElementById('edit-form-' + currentUpdateId);
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'edit_password';
        hiddenInput.value = '1';
        form.appendChild(hiddenInput);
        form.submit();
    }
});
