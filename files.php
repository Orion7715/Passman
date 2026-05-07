<?php
require_once 'includes/protect.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

$master_key = $_SESSION['master_key'];
$upload_dir = 'vault/files/';


if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0775, true);
    file_put_contents($upload_dir . '.htaccess', "Deny from all");
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file_to_upload'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Security Breach: CSRF Token Invalid");
    }

    $file = $_FILES['file_to_upload'];
    if ($file['error'] === 0) {
        $original_name = basename($file['name']);
        $file_content = file_get_contents($file['tmp_name']);
        
        $encrypted_data = encrypt_with_master($file_content, $master_key);
        
        $packet = json_encode([
            'original_name' => $original_name,
            'user_id' => $_SESSION['user_id'],
            'data' => $encrypted_data,
            'timestamp' => time()
        ]);

        $safe_filename = bin2hex(random_bytes(16)) . ".enc";
        file_put_contents($upload_dir . $safe_filename, $packet);
        
        $_SESSION['flash_success'] = "File encrypted and vaulted.";
        header("Location: files.php"); exit;
    }
}


if (isset($_GET['download'])) {
    $target = basename($_GET['download']);
    $path = $upload_dir . $target;

    if (file_exists($path)) {
        $packet = json_decode(file_get_contents($path), true);
        
        if ($packet['user_id'] == $_SESSION['user_id']) {
            $decrypted_data = decrypt_with_master($packet['data'], $master_key);
            
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $packet['original_name'] . '"');
            header('Content-Length: ' . strlen($decrypted_data));
            echo $decrypted_data;
            exit;
        }
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_file'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Security Breach");
    }

    $target = basename($_POST['delete_file']);
    $path = $upload_dir . $target;

    if (file_exists($path)) {
        $packet = json_decode(file_get_contents($path), true);

        if ($packet['user_id'] == $_SESSION['user_id']) {
            unlink($path);
            $_SESSION['flash_success'] = "File permanently wiped.";
        }
    }
    header("Location: files.php"); exit;
}


$user_files = [];
if (file_exists($upload_dir)) {
    $all_files = array_diff(scandir($upload_dir), array('.', '..', '.htaccess'));
    foreach ($all_files as $f) {
        $data = json_decode(file_get_contents($upload_dir . $f), true);
        if ($data && $data['user_id'] == $_SESSION['user_id']) {
            $user_files[] = [
                'safe_name' => $f,
                'original_name' => $data['original_name'],
                'time' => $data['timestamp']
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Files - Passman Vault</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-color: #050505;
            background-image: radial-gradient(circle at top left, #1e1b4b, transparent), 
                              radial-gradient(circle at bottom right, #2e1065, transparent);
        }
        .glass-card {
            background: rgba(15, 15, 15, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(139, 92, 246, 0.1);
        }
    </style>
</head>
<body class="min-h-screen p-4 md:p-8 text-slate-200">

    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-10 gap-4">
            <div>
                <h1 class="text-3xl font-black tracking-tighter bg-gradient-to-r from-purple-400 to-fuchsia-400 bg-clip-text text-transparent uppercase">
                    FILE.VAULT
                </h1>
                <p class="text-slate-500 text-[10px] uppercase tracking-[0.3em]">Encrypted Blob Storage</p>
            </div>
            <a href="dashboard.php" class="px-5 py-2 rounded-xl border border-slate-800 hover:border-purple-500/50 text-xs text-slate-400 transition-all uppercase tracking-widest font-bold">
                Back to Terminal
            </a>
        </div>

        <!-- Upload Zone -->
        <form method="POST" enctype="multipart/form-data" class="glass-card p-6 rounded-3xl mb-10 border-purple-500/10">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <div class="flex flex-col md:flex-row gap-4 items-center">
                <label class="flex-1 w-full p-4 border-2 border-dashed border-slate-800 rounded-2xl hover:border-purple-500/40 transition-all cursor-pointer group text-center">
                    <input type="file" name="file_to_upload" class="hidden" onchange="this.nextElementSibling.innerText = this.files[0].name">
                    <span class="text-slate-500 text-sm group-hover:text-purple-400">Click to select classified file...</span>
                </label>
                <button type="submit" class="w-full md:w-auto px-8 py-4 bg-purple-600 hover:bg-purple-500 text-white rounded-2xl font-bold text-xs uppercase tracking-widest transition-all active:scale-95">
                    Encrypt & Vault
                </button>
            </div>
        </form>

        <?php if (isset($_SESSION['flash_success'])): ?>
            <div class="mb-6 p-4 bg-purple-500/10 border border-purple-500/20 text-purple-400 text-xs font-bold rounded-xl flex items-center gap-3">
                <span class="w-2 h-2 bg-purple-500 rounded-full animate-pulse"></span>
                <?= $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?>
            </div>
        <?php endif; ?>

        <!-- Files List -->
        <div class="grid gap-4">
            <?php if (empty($user_files)): ?>
                <div class="text-center py-20 glass-card rounded-3xl border-dashed border-slate-800">
                    <p class="text-slate-600 text-xs uppercase tracking-widest font-bold">No Encrypted Files Found</p>
                </div>
            <?php else: ?>
                <?php foreach ($user_files as $file): ?>
                    <div class="glass-card p-4 md:p-6 rounded-2xl flex items-center justify-between hover:border-purple-500/30 transition-all group">
                        <div class="flex items-center gap-4">
                            <div class="p-3 rounded-xl bg-purple-500/10 text-purple-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                            </div>
                            <div>
                                <h3 class="text-white font-bold text-sm"><?= htmlspecialchars($file['original_name']) ?></h3>
                                <p class="text-[9px] text-slate-500 uppercase tracking-widest mt-1">
                                    AES-256 • <?= date('Y-m-d H:i', $file['time']) ?>
                                </p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <!-- Download Button -->
                            <a href="?download=<?= urlencode($file['safe_name']) ?>" class="p-3 rounded-xl hover:bg-purple-500/20 text-purple-400 transition-colors" title="Decrypt & Download">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                            </a>
                            
                            <!-- Delete Button (Form for CSRF protection) -->
                            <form method="POST" onsubmit="return confirm('Permanently wipe this file from vault?');">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                <input type="hidden" name="delete_file" value="<?= htmlspecialchars($file['safe_name']) ?>">
                                <button type="submit" class="p-3 rounded-xl hover:bg-red-500/20 text-red-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
