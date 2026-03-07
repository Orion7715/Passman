<?php
session_start();

require "includes/db.php";
require "includes/functions.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm  = $_POST["confirm_password"] ?? "";

    if (empty($username) || empty($password) || empty($confirm)) {
        $_SESSION["error"] = "All fields are required.";
    } elseif ($password !== $confirm) {
        $_SESSION["error"] = "Passwords do not match.";
    } else {
        $passwordErrors = is_strong_password($password);
        if (!empty($passwordErrors)) {
            $_SESSION["error"] = implode("\n", $passwordErrors);
            header("Location: register.php");
            exit;
        }

        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $_SESSION["error"] = "Username already exists.";
            header("Location: register.php");
            exit;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, master_password) VALUES (?, ?)");
        $stmt->execute([$username, $hash]);

        $_SESSION["success"] = "Registration successful. You can now login.";
        header("Location: login.php");
        exit;
    }
    header("Location: register.php");
    exit;
}

$error = $_SESSION["error"] ?? "";
unset($_SESSION["error"]);
$success = $_SESSION["success"] ?? "";
unset($_SESSION["success"]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Passman</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root { --background: #0f0f0f; --card-dark: #1a1a1a; }
        body { background-color: var(--background); color: #e5e7eb; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-6 font-sans">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-400 to-emerald-400 bg-clip-text text-transparent">
                Passman
            </h1>
            <p class="text-gray-500 text-sm mt-2">Create your master account</p>
        </div>

        <form method="POST" class="bg-[#1a1a1a] border border-gray-800 p-8 rounded-2xl shadow-2xl">
            <h2 class="text-xl font-semibold mb-6 text-emerald-400">Join the Vault</h2>
            
            <?php if (!empty($error)): ?>
                <div class="bg-red-900/20 border border-red-800 text-red-400 px-4 py-3 rounded-xl mb-6 text-xs whitespace-pre-line">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="mb-5">
                <label class="text-xs text-gray-500 ml-1 uppercase tracking-wider font-bold">Username</label>
                <input type="text" name="username" required placeholder="Choose a username" 
                       class="w-full bg-[#0f0f0f] border border-gray-800 px-4 py-3 rounded-xl focus:ring-1 focus:ring-emerald-500 outline-none text-white mt-1 transition-all">
            </div>

            <div class="mb-5">
                <label class="text-xs text-gray-500 ml-1 uppercase tracking-wider font-bold">Master Password</label>
                <div class="relative mt-1">
                    <input type="password" name="password" id="reg-password" required placeholder="Strong & Memorable Password" 
                           class="w-full bg-[#0f0f0f] border border-gray-800 px-4 py-3 rounded-xl focus:ring-1 focus:ring-emerald-500 outline-none text-white transition-all">
                    <button type="button" onclick="togglePass()" class="absolute right-3 top-3.5 text-gray-600 hover:text-emerald-400">
                        <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2"/>
                            <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" stroke-width="2"/>
                        </svg>
                    </button>
                </div>
                
                <div class="mt-3 px-1">
                    <div class="h-1.5 w-full bg-gray-800 rounded-full overflow-hidden">
                        <div id="strength-bar" class="h-full w-0 transition-all duration-500"></div>
                    </div>
                    <div class="flex justify-between items-center mt-1">
                        <span id="strength-text" class="text-[9px] uppercase tracking-wider text-gray-500 font-bold">Strength: None</span>
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <label class="text-xs text-gray-500 ml-1 uppercase tracking-wider font-bold">Confirm Password</label>
                <input type="password" name="confirm_password" required placeholder="••••••••" 
                       class="w-full bg-[#0f0f0f] border border-gray-800 px-4 py-3 rounded-xl focus:ring-1 focus:ring-emerald-500 outline-none text-white mt-1 transition-all">
            </div>

<button type="submit" id="reg-btn" 
    class="relative w-full py-4 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-500 hover:to-emerald-600 text-white rounded-xl shadow-lg shadow-emerald-900/30 transition-all duration-300 font-bold uppercase text-xs tracking-widest overflow-hidden flex items-center justify-center gap-3 active:scale-[0.97] group">
    
    <span id="btn-icon" class="transition-transform duration-500">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
        </svg>
    </span>

    <span id="btn-text">Create Account</span>

    <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
</button>

            <div class="mt-8 pt-6 border-t border-gray-800 text-center">
                <p class="text-sm text-gray-500">
                    Already have an account? 
                    <a href="login.php" class="text-emerald-400 hover:text-emerald-300 font-medium ml-1">Login here</a>
                </p>
            </div>
        </form>
    </div>

    <script>
const usernameField = document.getElementsByName('username')[0]; // أول عنصر باسم username
if (usernameField) {
    usernameField.focus();
}
        function togglePass() {
            const p = document.getElementById('reg-password');
            const icon = document.getElementById('eye-icon');
            if (p.type === 'password') {
                p.type = 'text';
                icon.classList.add('text-emerald-400');
            } else {
                p.type = 'password';
                icon.classList.remove('text-emerald-400');
            }
        }

        document.getElementById('reg-password').addEventListener('input', function() {
            const password = this.value;
            const bar = document.getElementById('strength-bar');
            const text = document.getElementById('strength-text');
            let strength = 0;

            if (password.length >= 8) strength += 25;
            if (password.match(/[A-Z]/)) strength += 25;
            if (password.match(/[0-9]/)) strength += 25;
            if (password.match(/[^A-Za-z0-9]/)) strength += 25;

            bar.style.width = strength + '%';
            
            if (strength === 0) {
                bar.className = 'h-full bg-gray-800 transition-all duration-500';
                text.innerText = 'Strength: None';
                text.className = 'text-[9px] uppercase tracking-wider text-gray-500 font-bold';
            } else if (strength <= 25) {
                bar.className = 'h-full bg-red-500 shadow-[0_0_10px_rgba(239,68,68,0.5)] transition-all duration-500';
                text.innerText = 'Strength: Weak';
                text.className = 'text-[9px] uppercase tracking-wider text-red-500 font-bold';
            } else if (strength <= 50) {
                bar.className = 'h-full bg-orange-500 shadow-[0_0_10px_rgba(249,115,22,0.5)] transition-all duration-500';
                text.innerText = 'Strength: Fair';
                text.className = 'text-[9px] uppercase tracking-wider text-orange-500 font-bold';
            } else if (strength <= 75) {
                bar.className = 'h-full bg-blue-500 shadow-[0_0_10px_rgba(59,130,246,0.5)] transition-all duration-500';
                text.innerText = 'Strength: Good';
                text.className = 'text-[9px] uppercase tracking-wider text-blue-500 font-bold';
            } else {
                bar.className = 'h-full bg-emerald-500 shadow-[0_0_12px_rgba(16,185,129,0.6)] transition-all duration-500';
                text.innerText = 'Strength: Strong';
                text.className = 'text-[9px] uppercase tracking-wider text-emerald-500 font-bold';
            }
        });
document.querySelector('form').addEventListener('submit', function(e) {
    const btn = document.getElementById('reg-btn');
    const icon = document.getElementById('btn-icon');
    const text = document.getElementById('btn-text');
    

    btn.classList.add('cursor-wait');
    text.innerText = "Registering...";
    

    icon.innerHTML = `<svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                      </svg>`;
});
    </script>
</body>
</html>
