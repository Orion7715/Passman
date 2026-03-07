<?php
require_once 'includes/session_config.php'; 
session_start();
require_once 'includes/protect.php';
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
require "includes/db.php";
require "includes/functions.php";


$user_id = $_SESSION['user_id'];
$master_key = $_SESSION['master_key'];


$stmt = $pdo->prepare("SELECT * FROM passwords WHERE user_id = ? ORDER BY id DESC");
$stmt->execute([$user_id]);
$passwords = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Passman</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="min-h-screen p-8 font-sans text-gray-200">

<div id="timeout-modal" class="fixed bottom-10 right-10 bg-red-900/90 border border-red-500 p-6 rounded-2xl shadow-2xl z-[200] hidden max-w-xs backdrop-blur-md">
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-red-600 flex items-center justify-center animate-pulse text-white">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </div>
        <div>
            <p class="text-sm font-bold text-white">Inactivity Timeout!</p>
            <p class="text-xs text-red-200">Logging out in <span id="timer-seconds" class="font-mono font-bold text-lg">30</span>s</p>
        </div>
    </div>
    <button onclick="resetTimers()" class="w-full mt-4 py-2 bg-white/10 hover:bg-white/20 text-white text-xs font-bold rounded-lg transition-all uppercase tracking-widest">Keep me logged in</button>
</div>

<div class="max-w-6xl mx-auto">
    
    <div class="flex justify-between items-center mb-10">
        <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-400 to-emerald-400 bg-clip-text text-transparent italic">Passman Dashboard</h1>
        <div class="flex items-center gap-4">
            <span class="text-xs text-gray-500 font-mono">User: <?= htmlspecialchars($_SESSION["username"]) ?></span>
<div class="flex items-center gap-4">

<a href="logout.php"
   class="group relative flex items-center justify-between gap-6 py-3 px-5 bg-red-900/10 border border-red-900/30 rounded-xl transition-all duration-500 overflow-hidden hover:bg-red-600 hover:border-red-500 hover:shadow-[0_0_20px_rgba(220,38,38,0.3)]">
    
    <div class="flex items-center gap-3 relative z-10">
        <svg class="w-4 h-4 text-red-500 group-hover:text-white transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
        </svg>
        <span class="text-xs font-bold uppercase tracking-widest text-red-400 group-hover:text-white transition-colors duration-300">Logout</span>
    </div>

    <div class="relative z-10 flex items-center gap-1.5 px-2 py-0.5 rounded border border-red-900/50 bg-black/20 group-hover:bg-white/20 group-hover:border-white/30 transition-colors">
        <span class="text-[9px] font-mono text-red-500/70 group-hover:text-white transition-colors">Ctrl</span>
        <span class="text-[9px] font-mono text-red-500/70 group-hover:text-white transition-colors">+</span>
        <span class="text-[9px] font-mono text-red-500/70 group-hover:text-white transition-colors">Z</span>
    </div>
</a>
</div>
        </div>
    </div>

<?php
    $flash_types = ['success' => 'emerald', 'error' => 'red', 'warning' => 'amber'];

    foreach ($flash_types as $type => $color) {
        if (isset($_SESSION['flash_' . $type])): ?>
            <div class="mb-6 p-4 border rounded-xl text-sm animate-pulse
                <?php if ($type == 'success') echo 'bg-emerald-900/20 border-emerald-800 text-emerald-400'; ?>
                <?php if ($type == 'error') echo 'bg-red-900/20 border-red-800 text-red-400'; ?>
                <?php if ($type == 'warning') echo 'bg-amber-900/20 border-amber-800 text-amber-400'; ?>">
                <?= $_SESSION['flash_' . $type]; unset($_SESSION['flash_' . $type]); ?>
            </div>
        <?php endif;
    } ?>


    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div class="relative">
            <input type="text" id="search-input" placeholder="Search your vault..." class="w-full bg-[#1a1a1a] border border-gray-800 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none pl-11 shadow-inner">
            <svg class="h-5 w-5 absolute left-4 top-1/2 -translate-y-1/2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
        </div>
		<div class="flex gap-4 w-full justify-end">

    <div class="flex gap-4 w-full max-w-sm justify-end">
        <form method="POST" action="actions/export_passwords.php" class="w-1/2">
            <button type="submit" class="relative w-full h-10 flex items-center justify-center gap-2 rounded-xl border border-amber-500/20 bg-amber-500/5 backdrop-blur-md transition-all duration-500 overflow-hidden group text-amber-100 hover:border-amber-500/50">
                <span class="absolute inset-0 bg-amber-500/80 transition-transform duration-700 ease-out transform translate-x-full group-hover:translate-x-0"></span>
                
                <svg class="w-4 h-4 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                <span class="text-[10px] font-bold uppercase tracking-widest relative z-10">Export</span>
            </button>
        </form>

        <form id="importForm" method="POST" action="actions/import_csv.php" enctype="multipart/form-data" class="w-1/2">
            <label class="cursor-pointer relative w-full h-10 flex items-center justify-center gap-2 rounded-xl border border-blue-500/20 bg-blue-500/5 backdrop-blur-md transition-all duration-500 overflow-hidden group text-blue-100 hover:border-blue-500/50">
		<input type="file" name="csv_file" class="hidden" onchange="this.form.submit()">
                <span class="absolute inset-0 bg-blue-600/80 transition-transform duration-700 ease-out transform translate-x-full group-hover:translate-x-0"></span>
                
                <svg class="w-4 h-4 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                <span class="text-[10px] font-bold uppercase tracking-widest relative z-10">Import</span>
            </label>
        </form>
    </div>
    
</div>
    </div>

    <div class="flex flex-wrap gap-2 mb-8 items-center">
        <span class="text-[10px] text-gray-600 uppercase tracking-widest font-bold mr-2">Filter:</span>
        <button onclick="filterCategory('all', this)" class="category-pill active px-4 py-1.5 text-[10px] uppercase font-bold bg-gray-800 border border-gray-700 text-gray-400 rounded-lg hover:border-blue-500">All</button>
        <?php 
        $cats = ['Work', 'Education', 'Social', 'Finance', 'Personal', 'Shopping', 'General'];
        foreach($cats as $c): ?>
            <button onclick="filterCategory('<?= $c ?>', this)" class="category-pill px-4 py-1.5 text-[10px] uppercase font-bold bg-gray-800 border border-gray-700 text-gray-400 rounded-lg hover:border-blue-500"><?= $c ?></button>
        <?php endforeach; ?>
    </div>

    <form method="POST" action="actions/add_password.php" class="bg-[#1a1a1a] border border-gray-800 p-6 rounded-2xl mb-12 shadow-xl">
        <h2 class="text-xl font-semibold mb-5 text-blue-400 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
            Add New Password
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            <div><label class="text-xs text-gray-500 ml-1 uppercase tracking-wider font-bold">Category</label>
                <select name="category" class="w-full bg-[#0f0f0f] border border-gray-800 px-4 py-2 rounded-xl focus:ring-1 focus:ring-blue-500 outline-none mt-1 text-sm">
                    <option value="General">General</option>
                    <option value="Work">Work</option>
                    <option value="Education">Education</option>
                    <option value="Social">Social Media</option>
                    <option value="Finance">Finance</option>
                    <option value="Shopping">Shopping</option>
                    <option value="Personal">Personal</option>
                </select>
            </div>
            <div><label class="text-xs text-gray-500 ml-1 uppercase tracking-wider font-bold">Domain</label><input type="text" name="domain" required placeholder="example.com" class="w-full bg-[#0f0f0f] border border-gray-800 px-4 py-2 rounded-xl focus:ring-1 focus:ring-blue-500 outline-none mt-1"></div>
            <div><label class="text-xs text-gray-500 ml-1 uppercase tracking-wider font-bold">Username</label><input type="text" name="username" placeholder="johndoe" class="w-full bg-[#0f0f0f] border border-gray-800 px-4 py-2 rounded-xl focus:ring-1 focus:ring-blue-500 outline-none mt-1"></div>
            
            <div>
                <div class="flex justify-between items-center">
                    <label class="text-xs text-gray-500 ml-1 uppercase tracking-wider font-bold">Password</label>
                    <span id="strength-text-add"></span>
                </div>
                <div class="relative mt-1">
                    <input type="text" name="password" id="password-input" required placeholder="••••••••" class="w-full bg-[#0f0f0f] border border-gray-800 px-4 py-2 rounded-xl focus:ring-1 focus:ring-blue-500 outline-none pr-10" oninput="checkStrength(this.value, 'strength-bar-add', 'strength-text-add')">
                    <button type="button" onclick="generatePassword()" class="absolute right-3 top-3 text-blue-500 hover:text-blue-400" title="Generate">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                    </button>
                </div>
                <div id="strength-bar-add" class="strength-bar"></div>
            </div>
            <div><label class="text-xs text-gray-500 ml-1 uppercase tracking-wider font-bold">Email</label><input type="email" name="email" placeholder="mail@example.com" class="w-full bg-[#0f0f0f] border border-gray-800 px-4 py-2 rounded-xl focus:ring-1 focus:ring-blue-500 outline-none mt-1"></div>
            <div class="lg:col-span-3"><label class="text-xs text-gray-500 ml-1 uppercase tracking-wider font-bold">Note</label><textarea name="note" rows="2" placeholder="Extra details..." class="w-full bg-[#0f0f0f] border border-gray-800 px-4 py-2 rounded-xl focus:ring-1 focus:ring-blue-500 outline-none mt-1 text-sm"></textarea></div>
        </div>
<button type="submit" name="add_password" 
    class="relative group mt-6 py-3 px-10 bg-blue-900/10 border border-blue-500/20 text-white rounded-xl font-bold shadow-[0_0_15px_rgba(37,99,235,0.1)] hover:shadow-[0_0_25px_rgba(37,99,235,0.3)] transition-all duration-500 uppercase text-xs tracking-widest overflow-hidden">
    
    <span class="absolute inset-0 bg-blue-600 translate-y-full group-hover:translate-y-0 transition-transform duration-500 ease-out z-0"></span>
    
    <div class="relative z-10 flex items-center gap-3">
        <svg class="w-4 h-4 group-hover:-translate-y-0.5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
        </svg>
        <span>Save to Vault</span>
    </div>
</button>
    </form>

    <div id="passwords-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($passwords as $p): 
            $domain_dec = decrypt_with_master($p['domain'], $master_key);
            $pass_dec = decrypt_with_master($p['password'], $master_key);
            $email_dec = decrypt_with_master($p['email'], $master_key);
            $note_dec = decrypt_with_master($p['note'], $master_key);
            $clean_url = str_replace(['http://', 'https://', 'www.'], '', strtolower($domain_dec));
            $cat = $p['category'] ?? 'General';
        ?>
        <div class="password-card group relative bg-[#1a1a1a] hover:bg-[#222] p-5 rounded-2xl shadow-lg border border-gray-800 cursor-pointer transition-all hover:-translate-y-1" 
             data-domain="<?= htmlspecialchars(strtolower($domain_dec)) ?>" 
             data-category="<?= $cat ?>"
             onclick="openModal(<?= $p['id'] ?>)">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 flex-shrink-0 flex items-center justify-center rounded-xl bg-gray-800 border border-gray-700 group-hover:border-blue-500/50 transition-all">
                    <img src="https://www.google.com/s2/favicons?sz=128&domain=<?= $clean_url ?>" class="w-8 h-8 object-contain" onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($domain_dec) ?>&background=random&color=fff'">
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                         <h3 class="text-white font-bold truncate text-sm"><?= htmlspecialchars($domain_dec) ?></h3>
                         <span class="category-tag cat-<?= $cat ?>"><?= $cat ?></span>
                    </div>
                    <p class="text-gray-500 text-[11px] truncate mt-0.5"><?= htmlspecialchars($p['username'] ?: 'No username') ?></p>
                </div>
            </div>
        </div>

        <div id="modal-<?= $p['id'] ?>" class="fixed inset-0 bg-black/90 hidden items-center justify-center z-50 p-4 backdrop-blur-sm">
            <div class="bg-[#1a1a1a] border border-gray-800 rounded-2xl shadow-2xl max-w-lg w-full p-8 relative" onclick="event.stopPropagation()">
                <button onclick="closeModal(<?= $p['id'] ?>)" class="absolute top-4 right-4 text-gray-500 hover:text-white text-2xl">&times;</button>
                <div class="flex items-center mb-8 border-b border-gray-800 pb-4">
                    <img src="https://www.google.com/s2/favicons?sz=64&domain=<?= $clean_url ?>" class="w-10 h-10 mr-4 rounded-lg">
                    <h3 class="text-2xl font-bold text-white"><?= htmlspecialchars($domain_dec) ?></h3>
                </div>
                <form id="edit-form-<?= $p['id'] ?>" action="actions/edit_password.php" method="POST" class="space-y-4">
                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-1">
                            <label class="text-[10px] text-gray-500 font-bold uppercase ml-1">Category</label>
                            <select name="category" class="w-full bg-[#0f0f0f] border border-gray-800 px-4 py-2.5 rounded-xl text-white outline-none focus:border-blue-500">
                                <?php foreach($cats as $opt): ?>
                                    <option value="<?= $opt ?>" <?= ($cat == $opt) ? 'selected' : '' ?>><?= $opt ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-span-1"><label class="text-[10px] text-gray-500 font-bold uppercase ml-1">Domain</label><input type="text" name="domain" value="<?= htmlspecialchars($domain_dec) ?>" class="w-full bg-[#0f0f0f] border border-gray-800 px-4 py-2.5 rounded-xl text-white outline-none focus:border-blue-500"></div>
                        <div><label class="text-[10px] text-gray-500 font-bold uppercase ml-1">Username</label><input type="text" name="username" value="<?= htmlspecialchars($p['username']) ?>" class="w-full bg-[#0f0f0f] border border-gray-800 px-4 py-2.5 rounded-xl text-white outline-none"></div>
                        <div><label class="text-[10px] text-gray-500 font-bold uppercase ml-1">Email</label><input type="email" name="email" value="<?= htmlspecialchars($email_dec) ?>" class="w-full bg-[#0f0f0f] border border-gray-800 px-4 py-2.5 rounded-xl text-white outline-none"></div>
                        <div class="col-span-2">
                            <div class="flex justify-between items-center">
                                <label class="text-[10px] text-gray-500 font-bold uppercase ml-1">Password</label>
                                <span id="strength-text-<?= $p['id'] ?>"></span>
                            </div>
                            <div class="relative flex gap-2 mt-1">
                                <div class="relative flex-1">
                                    <input type="password" id="pass-<?= $p['id'] ?>" name="password" value="<?= htmlspecialchars($pass_dec) ?>" class="w-full bg-[#0f0f0f] border border-gray-800 px-4 py-2.5 rounded-xl text-white outline-none focus:border-blue-500 pr-10" oninput="checkStrength(this.value, 'strength-bar-<?= $p['id'] ?>', 'strength-text-<?= $p['id'] ?>')">
                                    <button type="button" onclick="generatePasswordForModal('pass-<?= $p['id'] ?>')" class="absolute right-3 top-3 text-blue-500 hover:text-blue-400">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                    </button>
                                </div>
				<button type="button" onclick="togglePasswordVisibility('pass-<?= $p['id'] ?>', this)" 
    class="p-2.5 bg-gray-800 rounded-xl transition-all text-gray-400 hover:bg-gray-700 hover:text-white">
    
    <span id="icon-container-<?= $p['id'] ?>">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="eye-open-<?= $p['id'] ?>">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
        </svg>
        
        <svg class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="eye-closed-<?= $p['id'] ?>">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.97 9.97 0 012.336-3.458M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"/>
        </svg>
    </span>
</button>
                                <button type="button" onclick="copyToClipboard('pass-<?= $p['id'] ?>', this)" class="p-2.5 bg-gray-800 hover:bg-gray-700 rounded-xl transition-all text-gray-400 hover:text-white">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3" /></svg>
                                </button>
                            </div>
                            <div id="strength-bar-<?= $p['id'] ?>" class="strength-bar"></div>
                        </div>
                        <div class="col-span-2"><label class="text-[10px] text-gray-500 font-bold uppercase ml-1">Note</label><textarea name="note" rows="2" class="w-full bg-[#0f0f0f] border border-gray-800 px-4 py-2.5 rounded-xl text-white outline-none focus:border-blue-500"><?= htmlspecialchars($note_dec) ?></textarea></div>
                    </div>
                    <div class="flex gap-3 mt-6">
				<button type="button" onclick="confirmUpdate(<?= $p['id'] ?>)" 
    class="flex-1 relative py-3 px-6 bg-gradient-to-r from-blue-600 to-blue-500 rounded-xl font-bold shadow-lg shadow-blue-500/20 hover:shadow-blue-500/40 transition-all duration-300 uppercase text-[10px] tracking-widest text-white border border-blue-400/20 group overflow-hidden">
    
    <span class="absolute inset-0 rounded-xl bg-white opacity-0 group-hover:opacity-10 transition-opacity duration-300"></span>
    <span class="absolute inset-0 border border-white/20 rounded-xl animate-ping opacity-0 group-hover:opacity-100 duration-1000"></span>

    <div class="flex items-center justify-center gap-2 relative z-10">
        <svg class="w-4 h-4 group-hover:rotate-180 transition-transform duration-700 ease-in-out" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
        </svg>
        <span>Update</span>
    </div>
</button>
			<button type="button" onclick="confirmDelete(<?= $p['id'] ?>)" 
    class="flex items-center justify-center gap-2 px-6 py-3 bg-red-900/20 border border-red-800/50 text-red-500 rounded-xl hover:bg-red-600 hover:text-white transition-all duration-300 active:scale-[0.97]">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
    </svg>
    <span class="text-[10px] font-bold uppercase tracking-widest">Delete</span>
</button>
                    </div>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<div id="delete-modal" class="fixed inset-0 bg-black/90 hidden items-center justify-center z-[100] p-4 backdrop-blur-md">
    <div class="bg-[#1a1a1a] border border-gray-800 rounded-2xl p-8 max-w-sm w-full text-center shadow-2xl">
        <div class="w-16 h-16 bg-red-900/20 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4 border border-red-800/30">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
        </div>
        <h3 class="text-xl font-bold text-white mb-2">Are you sure?</h3>
        <p class="text-gray-400 text-sm mb-8">This action cannot be undone.</p>
        <div class="flex gap-3">
            <button onclick="closeDeleteModal()" class="flex-1 py-3 bg-gray-800 text-gray-300 rounded-xl hover:bg-gray-700 transition font-bold text-xs uppercase tracking-widest">Cancel</button>
            <form method="POST" action="actions/delete_password.php" class="flex-1">
                <input type="hidden" name="delete_id" id="delete-id-input">
                <button type="submit" name="confirm_delete" class="w-full py-3 bg-red-600 text-white rounded-xl hover:bg-red-700 transition font-bold text-xs uppercase tracking-widest">Delete</button>
            </form>
        </div>
    </div>
</div>
<div id="update-confirm-modal" class="fixed inset-0 bg-black/90 hidden items-center justify-center z-[110] p-4 backdrop-blur-md">
    <div class="bg-[#1a1a1a] border border-gray-800 rounded-2xl p-8 max-w-sm w-full text-center shadow-2xl">
        <div class="w-16 h-16 bg-blue-900/20 text-blue-500 rounded-full flex items-center justify-center mx-auto mb-4 border border-blue-800/30">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
        </div>
        <h3 class="text-xl font-bold text-white mb-2">Confirm Update?</h3>
        <p class="text-gray-400 text-sm mb-8">Are you sure you want to save these changes to your credential?</p>
        <div class="flex gap-3">
            <button onclick="closeUpdateModal()" class="flex-1 py-3 bg-gray-800 text-gray-300 rounded-xl hover:bg-gray-700 transition font-bold text-xs uppercase tracking-widest">Cancel</button>
            <button id="final-update-btn" class="flex-1 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition font-bold text-xs uppercase tracking-widest">Yes, Update</button>
        </div>
    </div>
</div>
<script src="assets/js/main.js"></script>
</body>
</html>
