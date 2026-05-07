<?php
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
   <script src="assets/js/export.js" defer></script>
   <script src="assets/js/import.js" defer></script>

</head>
<body class="min-h-screen p-8 font-sans text-gray-200">

<nav class="fixed top-0 left-0 w-full z-50 py-3 px-6 transition-all duration-500 bg-[#12100b]/70 backdrop-blur-xl border-b border-[#926a2d]/30 shadow-[0_4px_30px_rgba(146,106,45,0.1)]">
    
    
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-[50%] left-1/2 -translate-x-1/2 w-[60%] h-[100%] bg-[#926a2d]/10 blur-[80px] rounded-full"></div>
    </div>

    <div class="relative w-full flex justify-between items-center">
        
        
        <div class="flex items-center gap-3 group cursor-pointer">
            <div class="p-2 bg-gradient-to-br from-[#926a2d] to-[#5e441d] rounded-xl shadow-[0_0_15px_rgba(146,106,45,0.4)] group-hover:shadow-[0_0_25px_rgba(212,175,55,0.6)] transition-all duration-500">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <h1 class="text-2xl font-black tracking-tighter italic bg-gradient-to-b from-[#d4af37] via-[#926a2d] to-[#5e441d] bg-clip-text text-transparent drop-shadow-sm">
                Passman
            </h1>
        </div>

        
        <div class="flex-1 flex justify-center px-8">
            <div class="group relative flex items-center">
                <div class="absolute left-4 z-10 text-[#926a2d] group-focus-within:text-[#d4af37] transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                
                <input type="text" id="search-input" placeholder="Search Vault..." 
                    class="w-14 group-hover:w-72 focus:w-96 h-11 pl-12 pr-4 bg-black/40 border border-[#926a2d]/20 group-hover:border-[#926a2d]/50 focus:border-[#d4af37] rounded-2xl text-sm text-[#d4af37] placeholder-transparent group-hover:placeholder-[#926a2d]/60 transition-all duration-700 ease-in-out outline-none shadow-[inset_0_2px_10px_rgba(0,0,0,0.5)]">
            </div>
        </div>

        
        <div class="flex items-center gap-5">
            
            <div class="hidden md:flex items-center gap-3 px-4 py-2 bg-[#926a2d]/5 border border-[#926a2d]/20 rounded-xl">
                <div class="w-2 h-2 rounded-full bg-[#d4af37] shadow-[0_0_8px_#d4af37]"></div>
                <span class="text-xs font-bold font-mono text-[#d4af37]/80 uppercase tracking-widest">
                    <?= htmlspecialchars($_SESSION["username"]) ?>
                </span>
            </div>

            
            <a href="logout.php" 
               class="group flex items-center gap-3 py-2 px-5 bg-gradient-to-r from-red-950/40 to-black/40 border border-red-900/30 rounded-xl transition-all duration-300 hover:from-red-600 hover:to-red-700 hover:border-red-500 hover:shadow-[0_0_20px_rgba(220,38,38,0.4)]">
                <span class="text-[11px] font-black uppercase tracking-tighter text-red-500 group-hover:text-white transition-colors">
                    Logout
                </span>
                <svg class="w-4 h-4 text-red-500 group-hover:text-white group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
            </a>
        </div>
    </div>
</nav>

<div class="pt-24"></div>

<div id="timeout-modal" class="fixed bottom-10 right-10 bg-red-900/90 border border-red-500 p-6 rounded-2xl shadow-2xl z-[200] 
    hidden max-w-xs backdrop-blur-md"> <div class="flex items-center gap-4">
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
<div id="ajax-message-container"></div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
    </div>

<div class="flex flex-wrap gap-3 mb-10 items-center">
    <!-- Label -->
    <span class="text-xs text-[#d4af37] uppercase tracking-[0.2em] font-black mr-2 opacity-80">
        Filter Intelligence:
    </span>

    <!-- All Button -->
    <button onclick="filterCategory('all', this)" 
        class="category-pill active px-6 py-2 bg-white/5 border border-white/10 text-white/60 rounded-xl font-black text-xs uppercase tracking-widest transition-all duration-300 hover:border-[#926a2d]/50 hover:bg-white/10 active:scale-95 shadow-sm">
        All Units
    </button>

    <?php 
    $cats = ['Work', 'Education', 'Social', 'Finance', 'Personal', 'Shopping', 'General'];
    foreach($cats as $c): ?>
        <button onclick="filterCategory('<?= $c ?>', this)" 
            class="category-pill px-6 py-2 bg-white/5 border border-white/10 text-white/60 rounded-xl font-black text-xs uppercase tracking-widest transition-all duration-300 hover:border-[#d4af37]/50 hover:bg-white/10 active:scale-95 shadow-sm">
            <?= $c ?>
        </button>
    <?php endforeach; ?>
</div>


<style>
.category-pill.active {
    background: linear-gradient(to right, #926a2d, #d4af37) !important;
    color: black !important;
    border-color: transparent !important;
    box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
}
</style>

<form method="POST" action="actions/add_password.php" class="relative bg-[#0a0a0a]/60 backdrop-blur-2xl border border-[#926a2d]/30 p-6 rounded-2xl mb-12 shadow-2xl overflow-hidden">
    
    <div class="absolute -top-10 -left-10 w-40 h-40 bg-[#d4af37]/5 blur-[60px] rounded-full pointer-events-none"></div>

    <h2 class="text-xl font-black mb-6 text-[#d4af37] flex items-center gap-2 uppercase tracking-tight italic">
        <svg class="w-5 h-5 shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
        New Intelligence Entry
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 relative z-10">
        
        <!-- Category -->
        <div class="group">
            <label class="text-xs font-black text-[#d4af37]/80 uppercase tracking-[0.1em] block ml-1 mb-2">Classification</label>
            <div class="relative">
                <select name="category" class="w-full bg-white/5 border border-white/10 group-hover:border-[#926a2d]/50 px-4 py-3 rounded-xl focus:bg-white/10 focus:border-[#d4af37] outline-none text-white font-bold text-sm transition-all appearance-none cursor-pointer">
                    <option value="General" class="bg-[#1a1a1a]">General</option>
                    <option value="Work" class="bg-[#1a1a1a]">Work</option>
                    <option value="Education" class="bg-[#1a1a1a]">Education</option>
                    <option value="Social" class="bg-[#1a1a1a]">Social Media</option>
                    <option value="Finance" class="bg-[#1a1a1a]">Finance</option>
                    <option value="Shopping" class="bg-[#1a1a1a]">Shopping</option>
                    <option value="Personal" class="bg-[#1a1a1a]">Personal</option>
                </select>
                <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-[#926a2d]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
            </div>
        </div>

        <!-- Domain -->
        <div class="group">
            <label class="text-xs font-black text-[#d4af37]/80 uppercase tracking-[0.1em] block ml-1 mb-2">Target Domain</label>
            <input type="text" name="domain" required placeholder="example.com" 
                class="w-full bg-white/5 border border-white/10 group-hover:border-[#926a2d]/50 px-4 py-3 rounded-xl focus:bg-white/10 focus:border-[#d4af37] outline-none text-white font-bold text-sm transition-all placeholder:text-gray-600 shadow-inner">
        </div>

        <!-- Username -->
        <div class="group">
            <label class="text-xs font-black text-[#d4af37]/80 uppercase tracking-[0.1em] block ml-1 mb-2">Identity / User</label>
            <input type="text" name="username" placeholder="johndoe" 
                class="w-full bg-white/5 border border-white/10 group-hover:border-[#926a2d]/50 px-4 py-3 rounded-xl focus:bg-white/10 focus:border-[#d4af37] outline-none text-white font-bold text-sm transition-all placeholder:text-gray-600 shadow-inner">
        </div>

        <!-- Password Field -->
        <div class="group">
            <div class="flex justify-between items-center mb-2">
                <label class="text-xs font-black text-[#d4af37]/80 uppercase tracking-[0.1em] ml-1">Access Key</label>
                <span id="strength-text-add" class="text-[9px] font-black uppercase text-[#926a2d]"></span>
            </div>
            <div class="relative">
                <input type="text" name="password" id="password-input" required placeholder="••••••••" 
                    class="w-full bg-white/5 border border-white/10 group-hover:border-[#926a2d]/50 px-4 py-3 rounded-xl focus:bg-white/10 focus:border-[#d4af37] outline-none text-white font-bold text-sm transition-all pr-12 shadow-inner">
                
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                <button type="button" onclick="generatePassword()" class="absolute right-3 top-1/2 -translate-y-1/2 p-1.5 text-[#926a2d] hover:text-[#d4af37] transition-colors focus:outline-none">
                    <svg class="h-5 w-5 transform transition-all duration-700 hover:rotate-[180deg]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>
            </div>
            <div id="strength-bar-add" class="mt-2 h-1 rounded-full bg-white/5 overflow-hidden border border-white/5">
                <div class="h-full transition-all duration-500 bg-[#926a2d]" style="width: 0%"></div>
            </div>
        </div>

        <!-- Email -->
        <div class="group">
            <label class="text-xs font-black text-[#d4af37]/80 uppercase tracking-[0.1em] block ml-1 mb-2">Recovery Email</label>
            <input type="email" name="email" required placeholder="mail@example.com" 
                class="w-full bg-white/5 border border-white/10 group-hover:border-[#926a2d]/50 px-4 py-3 rounded-xl focus:bg-white/10 focus:border-[#d4af37] outline-none text-white font-bold text-sm transition-all placeholder:text-gray-600 shadow-inner">
        </div>

        <!-- Note (Multine with Glass Style) -->
        <div class="lg:col-span-3 group">
            <label class="text-xs font-black text-[#d4af37]/80 uppercase tracking-[0.1em] block ml-1 mb-2">Additional Intelligence (Notes)</label>
            <textarea name="note" rows="3" placeholder="Press Enter for new line..." 
                class="w-full bg-white/5 border border-white/10 group-hover:border-[#926a2d]/50 px-4 py-3 rounded-xl focus:bg-white/10 focus:border-[#d4af37] outline-none text-white font-bold text-sm transition-all placeholder:text-gray-600 resize-none whitespace-pre-wrap shadow-inner"></textarea>
        </div>
    </div>

    <!-- Submit Button (Golden Gradient) -->
    <button type="submit" name="add_password" 
        class="relative group mt-8 py-4 px-12 bg-transparent border-2 border-[#926a2d]/40 text-[#d4af37] rounded-xl font-black shadow-lg hover:shadow-[#926a2d]/20 transition-all duration-500 uppercase text-xs tracking-widest overflow-hidden active:scale-95">
        
        <span class="absolute inset-0 bg-gradient-to-r from-[#926a2d] to-[#d4af37] translate-y-full group-hover:translate-y-0 transition-transform duration-500 ease-out"></span>
        
        <div class="relative z-10 flex items-center justify-center gap-3 group-hover:text-black transition-colors duration-500">
            <svg class="w-5 h-5 group-hover:-translate-y-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
            </svg>
            <span>Deploy to Vault</span>
        </div>
    </button>
</form>

<div id="passwords-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($passwords as $p): 
        $domain_dec = decrypt_with_master($p['domain'], $master_key) ?: '[decrypt error]';
        $email_dec  = decrypt_with_master($p['email'], $master_key) ?: '[decrypt error]';
        $pass_dec   = decrypt_with_master($p['password'], $master_key) ?: '';
        $clean_url  = str_replace(['http://', 'https://', 'www.'], '', strtolower($domain_dec));
        $cat        = $p['category'] ?? 'General';
    ?>
    <div class="password-card group relative bg-white/[0.03] hover:bg-white/[0.08] p-5 rounded-2xl border border-white/10 transition-all"
         data-domain="<?= htmlspecialchars(strtolower($domain_dec)) ?>" 
         data-category="<?= $cat ?>"
         onclick="openModal(<?= (int)$p['id'] ?>)">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 flex-shrink-0 flex items-center justify-center rounded-xl bg-black/40 border border-white/5">
                <img src="https://www.google.com/s2/favicons?sz=128&domain=<?= $clean_url ?>" class="w-7 h-7">
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                     <h3 class="text-white font-black truncate text-base tracking-tight"><?= htmlspecialchars($domain_dec) ?></h3>
                     <span class="category-tag cat-<?= $cat ?> text-[9px] font-black uppercase tracking-widest border border-[#926a2d]/30 px-2 py-0.5 rounded"><?= $cat ?></span>
                </div>
                <p class="text-gray-400 font-black text-xs truncate mt-0.5"><?= htmlspecialchars($p['username'] ?: "N/A") ?></p>
            </div>
        </div>
    </div>


<div id="modal-<?= $p['id'] ?>" class="fixed inset-0 bg-black/95 hidden items-center justify-center z-50 p-4 backdrop-blur-md transition-all duration-500">
    
    <!-- Password Container -->
    <div class="bg-[#0c0c0c] border border-[#926a2d]/30 rounded-3xl shadow-[0_0_50px_rgba(0,0,0,1)] max-w-lg w-full p-8 relative overflow-hidden" onclick="event.stopPropagation()">
        
        
        <div class="absolute -top-24 -right-24 w-48 h-48 bg-[#926a2d]/10 blur-[60px] rounded-full pointer-events-none"></div>

        
        <button onclick="closeModal(<?= (int)$p['id'] ?>)" class="absolute top-5 right-5 text-[#926a2d] hover:text-[#d4af37] transition-colors text-2xl z-20 focus:outline-none">&times;</button>
        
        
        <div class="flex items-center mb-8 border-b border-[#926a2d]/10 pb-6 relative z-10">
            <div class="p-2 bg-[#926a2d]/10 rounded-xl border border-[#926a2d]/20 mr-4">
                <img src="https://www.google.com/s2/favicons?sz=64&domain=<?= $clean_url ?>" class="w-10 h-10 rounded-lg shadow-lg" alt="icon">
            </div>
            <div>
                <h3 class="text-2xl font-black text-white tracking-tight italic"><?= htmlspecialchars($domain_dec) ?></h3>
                <p class="text-[10px] text-[#926a2d] uppercase tracking-[0.2em] font-bold">Edit Vault Item</p>
            </div>
        </div>

        <!--
         Edit Password Form 
         -->

        <form id="edit-form-<?= $p['id'] ?>" action="actions/edit_password.php" method="POST" class="space-y-5 relative z-10">
            <input type="hidden" name="edit_password" value="1">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">

            <div class="grid grid-cols-2 gap-5">
                
                <div class="col-span-1">
                    <label class="text-[10px] text-[#926a2d] font-black uppercase ml-1 tracking-widest">Category</label>
                    <div class="relative">
                        <select name="category" class="w-full bg-black/50 border border-[#926a2d]/20 px-4 py-3 rounded-xl text-gray-300 outline-none focus:border-[#d4af37] focus:ring-1 focus:ring-[#d4af37]/30 transition-all cursor-pointer appearance-none">
                            <?php foreach($cats as $opt): ?>
                                <option value="<?= htmlspecialchars($opt) ?>" <?= ($cat == $opt) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($opt) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-[#926a2d]">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                    </div>
                </div>

                
                <div class="col-span-1">
                    <label class="text-[10px] text-[#926a2d] font-black uppercase ml-1 tracking-widest">Domain</label>
                    <input type="text" name="domain" value="<?= htmlspecialchars($domain_dec) ?>" 
                           class="w-full bg-black/50 border border-[#926a2d]/20 px-4 py-3 rounded-xl text-white outline-none focus:border-[#d4af37] transition-all">
                </div>

                
                <div class="col-span-1">
                    <label class="text-[10px] text-[#926a2d] font-black uppercase ml-1 tracking-widest">Username</label>
                    <input type="text" name="username" value="<?= htmlspecialchars($p['username']) ?>" 
                           class="w-full bg-black/50 border border-[#926a2d]/20 px-4 py-3 rounded-xl text-white outline-none focus:border-[#d4af37] transition-all">
                </div>

                
                <div class="col-span-1">
                    <label class="text-[10px] text-[#926a2d] font-black uppercase ml-1 tracking-widest">Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($email_dec) ?>" 
                           class="w-full bg-black/50 border border-[#926a2d]/20 px-4 py-3 rounded-xl text-white outline-none focus:border-[#d4af37] transition-all">
                </div>

                
                <div class="col-span-2">
                    <div class="flex justify-between items-center mb-1">
                        <label class="text-[10px] text-[#926a2d] font-black uppercase ml-1 tracking-widest">Password</label>
                        <span id="strength-text-<?= $p['id'] ?>" class="text-[9px] font-bold uppercase"></span>
                    </div>
                    
                    <div class="flex gap-2">
                        <div class="relative flex-1 group">
                            <input type="password" id="pass-<?= $p['id'] ?>" name="password" value="<?= htmlspecialchars($pass_dec) ?>" 
                                class="w-full bg-black border border-[#926a2d]/30 px-4 py-3 rounded-xl text-white outline-none focus:border-[#d4af37] shadow-inner transition-all pr-12">
                            
                            
                            <button type="button" onclick="generatePasswordForModal('pass-<?= $p['id'] ?>')" 
                                class="absolute right-3 top-3 text-[#926a2d] hover:text-[#d4af37] transition-all duration-700 group-hover:rotate-[360deg] focus:outline-none">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                            </button>
                        </div>

                        
                        <button type="button" onclick="togglePasswordVisibility('pass-<?= $p['id'] ?>', this)" 
                            class="p-3 bg-[#926a2d]/10 border border-[#926a2d]/20 rounded-xl text-[#926a2d] hover:bg-[#926a2d]/20 hover:text-[#d4af37] transition-all active:scale-90">
                            <span id="icon-container-<?= $p['id'] ?>">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </span>
                        </button>

                        
                        <button type="button" onclick="copyToClipboard('pass-<?= $p['id'] ?>', this)" 
                            class="p-3 bg-[#926a2d]/10 border border-[#926a2d]/20 rounded-xl text-[#926a2d] hover:bg-[#926a2d]/20 hover:text-[#d4af37] transition-all active:scale-90">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3" />
                            </svg>
                        </button>
                    </div>
                    
                    <div id="strength-bar-<?= $p['id'] ?>" class="h-1 mt-2 rounded-full bg-gray-900 overflow-hidden">
                        <div class="h-full w-0 transition-all duration-500"></div>
                    </div>
                </div>

                
                <div class="col-span-2">
                    <label class="text-[10px] text-[#926a2d] font-black uppercase ml-1 tracking-widest">Secure Note</label>
                    <textarea name="note" rows="2" class="w-full bg-black/50 border border-[#926a2d]/20 px-4 py-3 rounded-xl text-gray-300 outline-none focus:border-[#d4af37] transition-all resize-none italic"><?= htmlspecialchars($p['note']) ?></textarea>
                </div>
            </div>

            
            <div class="flex gap-4 mt-8">
                
                <button type="button" onclick="confirmUpdate(<?= (int)$p['id'] ?>)" 
                    class="group flex-1 relative py-4 px-6 bg-gradient-to-r from-[#926a2d] to-[#5e441d] rounded-2xl font-black text-white shadow-xl hover:shadow-[0_0_20px_rgba(146,106,45,0.4)] transition-all duration-300 active:scale-95 overflow-hidden">
                    <div class="flex items-center justify-center gap-3 relative z-10">
                        <svg class="w-5 h-5 group-hover:rotate-180 transition-transform duration-700 ease-in-out" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span class="uppercase tracking-[0.2em] text-[11px]">Update Vault</span>
                    </div>
                </button>

                
                <button type="button" onclick="confirmDelete(<?= (int)$p['id'] ?>)" 
                    class="flex items-center justify-center px-6 py-4 bg-red-950/20 border border-red-900/30 text-red-500 rounded-2xl hover:bg-red-600 hover:text-white transition-all duration-300 active:scale-95">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        </form>
    </div>
</div>

<?php endforeach; ?>

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
		<input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
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
</div>

<div class="mt-12 w-full max-w-[1400px] mx-auto px-4 space-y-8">
    
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-4 bg-[#0a0a0a]/60 backdrop-blur-2xl border border-[#926a2d]/20 rounded-3xl shadow-2xl">
        
        <a href="notes.php" class="group flex flex-col items-center justify-center gap-3 px-4 py-8 rounded-2xl border border-emerald-500/10 bg-emerald-500/5 transition-all duration-500 hover:border-emerald-500/40 hover:bg-emerald-500/10 shadow-lg">
            <svg class="w-7 h-7 text-emerald-500 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span class="text-xs font-black uppercase tracking-[0.2em] text-emerald-500">Notes</span>
        </a>

        <a href="files.php" class="group flex flex-col items-center justify-center gap-3 px-4 py-8 rounded-2xl border border-sky-500/10 bg-sky-500/5 transition-all duration-500 hover:border-sky-500/40 hover:bg-sky-500/10 shadow-lg">
            <svg class="w-7 h-7 text-sky-500 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            <span class="text-xs font-black uppercase tracking-[0.2em] text-sky-500">Files</span>
        </a>

        
<button type="button" onclick="toggleExportModal()" class="group w-full flex flex-col items-center justify-center gap-3 px-4 py-8 rounded-2xl border border-amber-500/10 bg-amber-500/5 transition-all duration-500 hover:border-amber-500/40 hover:bg-amber-500/10 shadow-lg">
    <svg class="w-7 h-7 text-amber-500 group-hover:-translate-y-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
    </svg>
    <span class="text-xs font-black uppercase tracking-[0.2em] text-amber-500">Export Data</span>
</button>


<div id="exportModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm px-4">
    <div class="bg-[#1a1a1a] border border-amber-500/20 w-full max-w-md rounded-3xl p-8 shadow-2xl transform transition-all">
        <div class="text-center mb-8">
            <h3 class="text-xl font-bold text-amber-500 mb-2">Export As</h3>
            <p class="text-gray-400 text-sm">Please select your preferred export file type</p>
        </div>

        <form method="POST" action="actions/export_passwords.php" class="grid grid-cols-1 gap-4">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            
            
            <button type="submit" name="export_type" value="encrypted" class="flex items-center justify-between p-4 rounded-xl border border-amber-500/10 bg-amber-500/5 hover:bg-amber-500/10 hover:border-amber-500/40 transition-all group">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <span class="text-white font-medium">Export Encrypted</span>
                </div>
                <span class="text-[10px] text-amber-500/50 uppercase font-bold">Safe</span>
            </button>

            
            <button type="submit" name="export_type" value="plain" class="flex items-center justify-between p-4 rounded-xl border border-white/5 bg-white/5 hover:bg-red-500/5 hover:border-red-500/30 transition-all group">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="text-white font-medium group-hover:text-red-500">Export Plain Text</span>
                </div>
                <span class="text-[10px] text-red-500/50 uppercase font-bold">Unsafe</span>
            </button>

            
            <button type="button" onclick="toggleExportModal()" class="mt-4 text-gray-500 hover:text-white text-sm font-medium transition-colors">
                Canel Process
            </button>
        </form>
    </div>
</div>

        
<div class="w-full">
    <button type="button" onclick="toggleImportModal()" class="group w-full flex flex-col items-center justify-center gap-3 px-4 py-8 rounded-2xl border border-indigo-500/10 bg-indigo-500/5 transition-all duration-500 hover:border-indigo-500/40 hover:bg-indigo-500/10 shadow-lg">
        <svg class="w-7 h-7 text-indigo-500 group-hover:translate-y-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
        </svg>
        <span class="text-xs font-black uppercase tracking-[0.2em] text-indigo-500">Import</span>
    </button>
</div>


<div id="importModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm px-4">
    <div class="bg-[#1a1a1a] border border-indigo-500/20 w-full max-w-md rounded-3xl p-8 shadow-2xl">
        <div class="text-center mb-8">
            <h3 class="text-xl font-bold text-indigo-500 mb-2">Import Options</h3>
            <p class="text-gray-400 text-sm">How should we process your CSV file?</p>
        </div>

        
        <form id="importForm" method="POST" action="actions/import_passwords.php" enctype="multipart/form-data" class="grid grid-cols-1 gap-4">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            
            <input type="file" id="csvFileInput" name="csv_file" class="hidden" accept=".csv" onchange="submitImport()">
            <input type="hidden" id="importType" name="import_type" value="">

            
            <button type="button" onclick="triggerFileSelect('encrypted')" class="flex items-center justify-between p-4 rounded-xl border border-indigo-500/10 bg-indigo-500/5 hover:bg-indigo-500/10 hover:border-indigo-500/40 transition-all group">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <span class="text-white font-medium text-left">
                        Encrypted CSV
                        <span class="block text-[10px] text-gray-500 font-normal italic">File was previously exported as encrypted</span>
                    </span>
                </div>
            </button>

            
            <button type="button" onclick="triggerFileSelect('plain')" class="flex items-center justify-between p-4 rounded-xl border border-white/5 bg-white/5 hover:bg-indigo-500/10 hover:border-indigo-500/40 transition-all group">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="text-white font-medium text-left">
                        Plain Text CSV
                        <span class="block text-[10px] text-gray-500 font-normal italic">Standard readable CSV (will be encrypted on upload)</span>
                    </span>
                </div>
            </button>

            <button type="button" onclick="toggleImportModal()" class="mt-4 text-gray-500 hover:text-white text-sm font-medium transition-colors">
                Cancel
            </button>
        </form>
    </div>
</div>
</div>




<!-- Main Trigger Button -->

<!-- Terminate Button Container -->
<div class="w-full mt-6">
    <button type="button" onclick="openTerminateModal()"
        class="group w-full py-5 bg-black/40 border border-purple-900/30 text-purple-600 rounded-xl transition-all flex items-center justify-center gap-2">
        
        <!-- SVG (Same size as the gold one) -->
        <svg class="w-5 h-5 text-purple-600 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
        </svg>

        <!-- Text -->
        <span class="font-bold">Terminate Account & Vault</span>
    </button>
</div>

<!-- Terminate Modal Container -->
<div id="terminate-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 backdrop-blur-sm p-4 text-left">
    <div id="terminate-content" 
         class="bg-[#0f0f0f] border border-purple-900/40 w-full max-w-md rounded-2xl overflow-hidden shadow-2xl scale-95 opacity-0 transition-all duration-300">
        
        <!-- Header -->
        <div class="p-6 border-b border-purple-900/20 bg-purple-950/10">
            <h3 class="text-xl font-bold text-purple-500 uppercase tracking-tighter">Extreme Danger Zone</h3>
            <p class="text-purple-400/60 text-sm mt-1">This action is permanent and cannot be undone.</p>
        </div>

        
        <form action="actions/delete_account.php" method="POST" class="p-6 space-y-4">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            
            <p class="text-gray-400 text-sm">
                To confirm deletion, please type your username <span class="text-purple-400 font-bold"><?= $_SESSION['username'] ?></span> below:
            </p>

            
            <input type="text" id="username-confirm-input" autocomplete="off" placeholder="Type username here..."
                class="w-full px-4 py-3 bg-black/60 border border-purple-900/30 rounded-xl text-white focus:outline-none focus:border-purple-600 transition-all">

            <!-- Action Buttons -->
            <div class="flex gap-3 pt-2">
                <button type="submit" id="final-terminate-btn" disabled
                    class="flex-1 py-3 bg-purple-600/10 text-purple-500/30 rounded-xl font-bold uppercase text-[10px] tracking-widest cursor-not-allowed transition-all">
                    TERMINATE EVERYTHING
                </button>
                <button type="button" onclick="closeTerminateModal()"
                    class="flex-1 py-3 bg-transparent border border-gray-800 text-gray-500 rounded-xl font-bold uppercase text-[10px] tracking-widest hover:bg-gray-800 transition-all">
                    CANCEL
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    window.currentUsername = "<?= $_SESSION['username'] ?>";
</script>

<!-- Change Master Key Modal -->
<div id="change-master-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 backdrop-blur-sm p-4">
    <div class="bg-[#121212] border border-[#926a2d]/40 w-full max-w-md rounded-2xl overflow-hidden shadow-2xl">
        
        <!-- Header -->
        <div class="p-6 border-b border-[#926a2d]/20 flex justify-between items-center">
            <h3 class="text-xl font-bold text-[#d4af37]">Change Master Security Key</h3>
            <button onclick="closeChangeMasterModal()" class="text-[#926a2d] hover:text-[#d4af37] transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Body -->
        <form id="masterKeyForm" method="POST" action="actions/change_master.php" class="p-6 space-y-4 text-left">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <!-- Old Password -->
            <div>
                <label class="block text-[#926a2d] text-sm mb-2 ml-1">Old Security Key</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-3 flex items-center text-[#926a2d]/60">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </span>
                    <input type="password" id="oldPass" name="old_master" required
                        class="form-input w-full pl-10 py-3 bg-black/40 border border-[#926a2d]/30 rounded-xl text-white focus:outline-none focus:border-[#d4af37] transition-all">
                </div>
            </div>

            <!-- New Password -->
            <div>
                <label class="block text-[#926a2d] text-sm mb-2 ml-1">New Key</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-3 flex items-center text-[#926a2d]/60">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                    </span>
                    <input type="password" id="newPass" name="new_master" required
                        class="form-input w-full pl-10 py-3 bg-black/40 border border-[#926a2d]/30 rounded-xl text-white focus:outline-none focus:border-[#d4af37] transition-all">
                </div>
            </div>

            <!-- Confirm Password -->
            <div>
                <label class="block text-[#926a2d] text-sm mb-2 ml-1">Confirm New Key</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-3 flex items-center text-[#926a2d]/60">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                    <input type="password" name="confirm_master" id="confirmPass" required
                        class="form-input w-full pl-10 py-3 bg-black/40 border border-[#926a2d]/30 rounded-xl text-white focus:outline-none focus:border-[#d4af37] transition-all">
                </div>
                <!-- Validation Message -->
                <p id="matchMessage" class="text-xs mt-2 hidden font-medium"></p>
            </div>

            <!-- Buttons -->
            <div class="flex gap-3 pt-4">
                <button type="submit" id="submitBtn" name="update_master_btn" disabled
                    class="flex-1 py-3 bg-[#d4af37] text-black font-bold rounded-xl opacity-50 cursor-not-allowed hover:shadow-[0_0_15px_rgba(212,175,55,0.3)] transition-all">
                    Update Key
                </button>
                <button type="button" onclick="closeChangeMasterModal()"
                    class="flex-1 py-3 bg-transparent border border-[#926a2d]/50 text-[#926a2d] rounded-xl hover:bg-[#926a2d]/10 transition-all">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>

document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('change-master-modal');
    const oldPass = document.getElementById('oldPass');
    const newPass = document.getElementById('newPass');
    const confirmPass = document.getElementById('confirmPass');
    const matchMessage = document.getElementById('matchMessage');
    const submitBtn = document.getElementById('submitBtn');
    const formInputs = document.querySelectorAll('.form-input');
    const masterForm = document.getElementById('masterKeyForm');

    // 1. Open Modal with Force Focus
    window.openChangeMasterModal = function() {
        modal.classList.replace('hidden', 'flex');
        
        
        setTimeout(() => {
            oldPass.focus();
        }, 200);
    };

    // 2. Close Modal & Reset
    window.closeChangeMasterModal = function() {
        modal.classList.replace('flex', 'hidden');
        masterForm.reset();
        matchMessage.classList.add('hidden');
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
    };

    // 3. Password Match Logic
    function validatePasswords() {
        const p1 = newPass.value;
        const p2 = confirmPass.value;

        if (p2.length === 0) {
            matchMessage.classList.add('hidden');
            return;
        }

        matchMessage.classList.remove('hidden');

        if (p1 === p2 && p1 !== "") {
            matchMessage.textContent = '✓ Passwords match';
            matchMessage.className = 'text-xs mt-2 text-green-500';
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            matchMessage.textContent = '× Passwords do not match';
            matchMessage.className = 'text-xs mt-2 text-red-500';
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        }
    }

    // 4. Enter Key Navigation
    formInputs.forEach((input, index) => {
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault(); 
                const nextInput = formInputs[index + 1];
                if (nextInput) {
                    nextInput.focus();
                } else if (!submitBtn.disabled) {
                    
                    masterForm.dispatchEvent(new Event('submit'));
                }
            }
        });
    });

    // Event Listeners for inputs
    newPass.addEventListener('input', validatePasswords);
    confirmPass.addEventListener('input', validatePasswords);

    
});


</script>
<script src="assets/js/main.js"></script>

</body>
</html>
