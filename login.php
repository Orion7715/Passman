<?php
require_once "includes/protect.php";
require "includes/db.php";

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    header("Location: dashboard.php");
    exit;
}

$error = "";
$number_map = array(
    1 => "One",
    2 => "Two",
    3 => "Three",
    4 => "Four",
    5 => "Five",
    6 => "Six",
    7 => "Seven",
    8 => "Eight",
    9 => "Nine",
    10 => "Ten"
);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
 $username = $_POST["username"] ?? "";
 $password = $_POST["password"] ?? "";
 $user_captcha = $_POST["captcha_answer"] ?? "";
 if (!isset($_SESSION['captcha_result']) || intval($user_captcha) !== $_SESSION['captcha_result']) {
 $_SESSION["error"] = "Human verification failed. Please solve the challenge.";
 header("Location: login.php");
 exit;
 }


 $stmt = $pdo->prepare("SELECT id, master_password FROM users WHERE username = ?");
 $stmt->execute([$username]);
 $user = $stmt->fetch();

 if ($user && password_verify($password, $user["master_password"])) {
 unset($_SESSION['captcha_result']);
 session_regenerate_id(true);
 $_SESSION["logged_in"] = true;
	$_SESSION["user_id"] = $user["id"];
 $_SESSION["username"] = $username;
 $_SESSION['master_key'] = hash('sha256', $password, true);
 header("Location: dashboard.php");
 exit;
 } else {
 sleep(2);
 $_SESSION["error"] = "Invalid credentials";
 header("Location: login.php");
 exit;
 }
}


$n1 = rand(1, 10);
$n2 = rand(1, 10);
$operations = ['plus', 'minus'];
$op = $operations[rand(0, 1)];

if ($op === 'plus') {
 $_SESSION['captcha_result'] = $n1 + $n2;
 $challenge_text = $number_map[$n1] . " + " . $n2;
} else {
 $_SESSION['captcha_result'] = $n1 - $n2;
 $challenge_text = $n1 . " minus " . $number_map[$n2];
}

$error = $_SESSION["error"] ?? "";
unset($_SESSION["error"]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
 <meta charset="UTF-8">
 <title>Login - Passman</title>
 <script src="https://cdn.tailwindcss.com"></script>
 <link rel="stylesheet" href="assets/css/login_style.css"></link>
</head>
<body class="flex items-center justify-center min-h-screen p-6">
 <div class="w-full max-w-md">
 <div class="text-center mb-8">
 <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-400 to-emerald-400 bg-clip-text text-transparent">
 Passman
 </h1>
 <p class="text-gray-500 text-sm mt-2">Secure Your Digital Vault</p>
 </div>

 <form method="POST" class="bg-[#1a1a1a] border border-gray-800 p-8 rounded-2xl shadow-2xl">
 <h2 class="text-xl font-semibold mb-6 text-blue-400">Master Login</h2>
 
 <?php if (!empty($error)): ?>
 <div class="bg-red-900/20 border border-red-800 text-red-400 px-4 py-3 rounded-xl mb-6 text-sm">
 <?= htmlspecialchars($error) ?>
 </div>
 <?php endif; ?>
 
 <div class="mb-5">
 <label class="text-xs text-gray-500 ml-1 uppercase tracking-wider font-bold">Username</label>
 <div class="relative mt-1">
 <input type="text" name="username" required placeholder="Enter Username" 
 class="w-full bg-[#0f0f0f] border border-gray-800 px-4 py-3 rounded-xl focus:ring-1 focus:ring-blue-500 outline-none text-white transition-all">
 </div>
 </div>

 <div class="mb-8">
 <label class="text-xs text-gray-500 ml-1 uppercase tracking-wider font-bold">Master Password</label>
 <div class="relative mt-1">
 <input type="password" name="password" required placeholder="••••••••" 
 class="w-full bg-[#0f0f0f] border border-gray-800 px-4 py-3 rounded-xl focus:ring-1 focus:ring-blue-500 outline-none text-white transition-all">
 </div>
 </div>
<div class="mb-8 p-4 bg-[#0f0f0f]/50 border border-gray-800 rounded-xl">
 <label class="text-[10px] text-gray-500 uppercase tracking-[0.2em] font-bold block mb-3">
 Security Challenge
 </label>
 <div class="flex items-center justify-between gap-4">
 <div class="flex-1 py-3 px-4 bg-[#1a1a1a] border border-gray-800 rounded-lg text-center select-none">
 <span class="text-emerald-400 font-mono font-bold tracking-widest italic">
 <?= $challenge_text ?> = ?
 </span>
 </div>
 <div class="w-24">
 <input type="number" name="captcha_answer" required autocomplete="off"
 placeholder="Ans"
 class="w-full bg-[#0f0f0f] border border-gray-800 px-3 py-3 rounded-lg focus:ring-1 focus:ring-emerald-500 outline-none text-white text-center transition-all">
 </div>
 </div>
 <p class="text-[11px] text-gray-600 mt-2 italic text-center">Solve the equation above to prove you're not a bot.</p>
</div>
<button type="submit" id="login-btn" 
    class="relative w-full py-4 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-500 hover:to-blue-600 text-white rounded-xl shadow-lg shadow-blue-900/30 transition-all duration-300 font-bold uppercase text-xs tracking-widest overflow-hidden flex items-center justify-center gap-3 active:scale-[0.97] group">
    
    <span id="btn-icon" class="transition-transform duration-500">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
        </svg>
    </span>

    <span id="btn-text">Unlock Vault</span>

    <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
</button>

 <div class="mt-8 pt-6 border-t border-gray-800 text-center">
 <p class="text-sm text-gray-500">
 Don't have an account? 
 <a href="register.php" class="text-blue-400 hover:text-blue-300 font-medium ml-1">Register here</a>
 </p>
 </div>
 </form>
 
 <p class="text-center text-gray-600 text-[10px] mt-8 uppercase tracking-[0.2em]">
 End-to-End Encrypted Storage
 </p>
 </div>
</body>
<script>
const usernameField = document.getElementsByName('username')[0]; // أول عنصر باسم username
if (usernameField) {
    usernameField.focus();
}

document.querySelector('form').addEventListener('submit', function(e) {
    const btn = document.getElementById('login-btn');
    const icon = document.getElementById('btn-icon');
    const text = document.getElementById('btn-text');
    btn.classList.add('cursor-wait');
    text.innerText = "Authenticating...";
    icon.innerHTML = `<svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
     captcha_result                   <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                      </svg>`;
});
</script>
</html>
 
