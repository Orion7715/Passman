1<?php
require_once 'includes/protect.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

$vault_dir = 'vault';
$notes_file = $vault_dir . '/secure_notes.bin';


if (!file_exists($vault_dir)) {
    mkdir($vault_dir, 0775, true);
    file_put_contents($vault_dir . '/.htaccess', "Deny from all");
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$master_key = $_SESSION['master_key'];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['flash_error'] = "Security validation failed.";
    } else {

        if (isset($_POST['note_title']) && !isset($_POST['note_index'])) {
            $data = json_encode([
                'title' => trim($_POST['note_title']),
                'content' => trim($_POST['note_content']),
                'user_id' => $_SESSION['user_id']
            ]);
            file_put_contents($notes_file, encrypt_with_master($data, $master_key) . PHP_EOL, FILE_APPEND | LOCK_EX);
            $_SESSION['flash_success'] = "Entry Secured in Vault.";
            header("Location: notes.php"); exit;
        }


        if (isset($_POST['edit_note_title']) && isset($_POST['note_index'])) { $index 
            = $_POST['note_index']; $lines = file($notes_file, FILE_IGNORE_NEW_LINES | 
            FILE_SKIP_EMPTY_LINES); if (isset($lines[$index])) {
                $data = json_encode([
                    'title' => $_POST['edit_note_title'],
                    'content' => $_POST['edit_note_content'],
                    'user_id' => $_SESSION['user_id']
                ]);
                $lines[$index] = encrypt_with_master($data, $master_key);
                file_put_contents($notes_file, implode(PHP_EOL, $lines) . PHP_EOL, LOCK_EX);
                $_SESSION['flash_success'] = "Intel Updated.";
                header("Location: notes.php"); exit;
            }
        }


        if (isset($_POST['delete_note_index'])) {
            $index = $_POST['delete_note_index'];
            $lines = file($notes_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if (isset($lines[$index])) {
                unset($lines[$index]);
                file_put_contents($notes_file, implode(PHP_EOL, $lines) . (empty($lines) ? "" : PHP_EOL), LOCK_EX);
                $_SESSION['flash_success'] = "Intel Wiped.";
                header("Location: notes.php"); exit;
            }
        }
    }
}


$display_notes = [];
if (file_exists($notes_file)) {
    $lines = file($notes_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $idx => $line) {
        $decrypted = decrypt_with_master($line, $master_key);
        if ($decrypted) {
            $decoded = json_decode($decrypted, true);
            if ($decoded && $decoded['user_id'] == $_SESSION['user_id']) {
                $decoded['index'] = $idx;
                $display_notes[] = $decoded;
            }
        }
    }
}
$display_notes = array_reverse($display_notes);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notes - Passman Vault</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-color: #050505;
            background-image: radial-gradient(circle at top left, #1e1b4b, transparent), 
                              radial-gradient(circle at bottom right, #2e1065, transparent);
            font-family: system-ui, -apple-system, sans-serif;
        }
        .glass-card {
            background: rgba(15, 15, 15, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(139, 92, 246, 0.1);
        }
        .glass-input {
            background: rgba(0, 0, 0, 0.4) !important;
            border: 1px solid rgba(139, 92, 246, 0.1) !important;
            color: #fff !important;
        }
        .glass-input:focus {
            border-color: rgba(139, 92, 246, 0.5) !important;
            box-shadow: 0 0 15px rgba(139, 92, 246, 0.1);
        }
        .note-item { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .line-clamp-1 { display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; }
    </style>
</head>
<body class="min-h-screen p-4 md:p-8 text-slate-200">

    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-10 gap-4">
            <div>
                <h1 class="text-3xl font-black tracking-tighter bg-gradient-to-r from-purple-400 to-fuchsia-400 bg-clip-text text-transparent uppercase">
                    VAULT.NOTES
                </h1>
                <p class="text-slate-500 text-[10px] uppercase tracking-[0.3em]">Encrypted Intelligence Database</p>
            </div>
            <a href="dashboard.php" class="px-5 py-2 rounded-xl border border-slate-800 hover:border-purple-500/50 text-xs text-slate-400 transition-all uppercase tracking-widest font-bold">
                Back to Terminal
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- Sidebar: Add Note -->
            <div class="lg:col-span-4">
                <form method="POST" class="glass-card p-6 rounded-3xl sticky top-8 border-purple-500/10">
                    <h2 class="text-sm font-bold mb-6 text-white flex items-center gap-2 uppercase tracking-wider">
                        <span class="w-1.5 h-4 bg-purple-500 rounded-full"></span>
                        New Record
                    </h2>
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    
                    <div class="space-y-4">
                        <input type="text" name="note_title" required placeholder="Entry Title" 
                               class="w-full glass-input px-4 py-3 rounded-xl outline-none text-sm transition-all font-bold">
                        
                        <textarea name="note_content" required placeholder="Secure content..." rows="5" 
                                  class="w-full glass-input px-4 py-3 rounded-xl outline-none text-sm transition-all resize-none"></textarea>
                        
                        <button type="submit" class="w-full py-3 bg-purple-600 hover:bg-purple-500 text-white rounded-xl text-xs font-bold uppercase tracking-widest transition-all active:scale-95 shadow-lg shadow-purple-900/20">
                            Lock Information
                        </button>
                    </div>
                </form>
            </div>

            <!-- Main Section -->
            <div class="lg:col-span-8">
                <!-- Search Box (Live) -->
                <div class="relative mb-8 group">
                    <div class="absolute inset-y-0 right-4 flex items-center text-slate-500 group-focus-within:text-purple-400 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" id="searchInput" placeholder="Search decrypted intelligence..." 
                           class="w-full glass-input pr-12 pl-4 py-4 rounded-2xl outline-none text-sm transition-all focus:ring-1 ring-purple-500/20">
                </div>

                <!-- Status Messages -->
                <?php if (isset($_SESSION['flash_success'])): ?>
                    <div id="flash-message" class="mb-6 p-4 bg-purple-500/10 border border-purple-500/20 text-purple-400 text-xs font-bold rounded-xl flex items-center gap-3">
                        <span class="w-2 h-2 bg-purple-400 rounded-full animate-ping"></span>
                        <?= $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?>
                    </div>
                <?php endif; ?>

                <!-- Notes Container -->
                <div id="notesContainer" class="grid gap-4">
                    <?php if (empty($display_notes)): ?>
                        <div id="emptyMsg" class="text-center py-20 glass-card rounded-3xl border-dashed border-slate-800">
                            <p class="text-slate-600 text-xs uppercase tracking-widest font-bold">No Records Found</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($display_notes as $note): ?>
                            <div onclick="openEditModal(<?= $note['index'] ?>, `<?= addslashes($note['title']) ?>`, `<?= addslashes($note['content']) ?>`)" 
                                 class="note-item glass-card p-6 rounded-2xl cursor-pointer hover:border-purple-500/30 transition-all hover:translate-x-1 group"
                                 data-title="<?= htmlspecialchars(strtolower($note['title'])) ?>"
                                 data-content="<?= htmlspecialchars(strtolower($note['content'])) ?>">
                                <div class="flex justify-between items-start">
                                    <h3 class="text-white font-bold group-hover:text-purple-400 transition-colors"><?= htmlspecialchars($note['title']) ?></h3>
                                    <span class="text-[9px] font-mono text-slate-600">OFFSET: 0x<?= dechex($note['index']) ?></span>
                                </div>
                                <p class="text-slate-500 text-xs mt-2 line-clamp-1 italic"><?= htmlspecialchars($note['content']) ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="fixed inset-0 flex items-center justify-center z-50 opacity-0 pointer-events-none transition-all duration-300 backdrop-blur-md px-4">
        <div class="absolute inset-0 bg-black/60" onclick="closeModal()"></div>
        <div class="glass-card w-full max-w-2xl p-8 rounded-[2.5rem] relative z-10 shadow-2xl scale-95 transition-all" id="modalContainer">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-purple-500 to-transparent opacity-50"></div>
            
            <form action="notes.php" method="POST" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="note_index" id="modalIndex">
                
                <div>
                    <label class="text-[10px] text-purple-400 font-bold uppercase tracking-widest mb-2 block">Subject Header</label>
                    <input type="text" name="edit_note_title" id="modalTitle" required 
                           class="w-full glass-input px-0 py-2 text-2xl font-black outline-none border-0 border-b border-slate-800 focus:border-purple-500 transition-all bg-transparent !border-t-0 !border-x-0">
                </div>

                <div>
                    <label class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mb-2 block">Payload Content</label>
                    <textarea name="edit_note_content" id="modalContent" required rows="8" 
                              class="w-full glass-input px-4 py-4 rounded-2xl text-slate-300 text-sm outline-none resize-none"></textarea>
                </div>

                <div class="flex flex-col md:flex-row gap-3">
                    <button type="submit" class="flex-1 py-4 bg-purple-600 hover:bg-purple-500 text-white rounded-xl text-xs font-bold uppercase tracking-widest transition-all active:scale-95">
                        Commit Changes
                    </button>
                </form>
                
                <form action="notes.php" method="POST" onsubmit="return confirm('Wipe this intelligence permanently?');" class="md:w-1/4">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <input type="hidden" name="delete_note_index" id="modalDeleteIndex">
                    <button type="submit" class="w-full py-4 bg-red-500/10 border border-red-500/20 text-red-500 rounded-xl hover:bg-red-500 hover:text-white transition-all text-xs font-bold uppercase tracking-widest">
                        Wipe
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('searchInput');
            const noteItems = document.querySelectorAll('.note-item');
            const notesContainer = document.getElementById('notesContainer');
            const flash = document.getElementById('flash-message');


            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                let hasResults = false;

                noteItems.forEach(card => {
                    const title = card.getAttribute('data-title');
                    const content = card.getAttribute('data-content');

                    if (title.includes(query) || content.includes(query)) {
                        card.style.display = 'block';
                        hasResults = true;
                    } else {
                        card.style.display = 'none';
                    }
                });


                let noResultsMsg = document.getElementById('dynamicNoResults');
                if (!hasResults) {
                    if (!noResultsMsg) {
                        noResultsMsg = document.createElement('div');
                        noResultsMsg.id = 'dynamicNoResults';
                        noResultsMsg.className = 'text-center py-20 glass-card rounded-3xl border-dashed border-slate-800';
                        noResultsMsg.innerHTML = '<p class="text-slate-600 text-xs uppercase tracking-widest font-bold">No Intel Matches Your Query</p>';
                        notesContainer.appendChild(noResultsMsg);
                    }
                } else if (noResultsMsg) {
                    noResultsMsg.remove();
                }
            });


            if (flash) {
                setTimeout(() => {
                    flash.style.opacity = '0';
                    flash.style.transform = 'translateY(-10px)';
                    flash.style.transition = 'all 0.6s ease';
                    setTimeout(() => flash.remove(), 600);
                }, 6000);
            }


            setTimeout(() => searchInput.focus(), 200);
        });


        function openEditModal(index, title, content) {
            document.getElementById('modalIndex').value = index;
            document.getElementById('modalDeleteIndex').value = index;
            document.getElementById('modalTitle').value = title;
            document.getElementById('modalContent').value = content;
            
            const modal = document.getElementById('editModal');
            const container = document.getElementById('modalContainer');
            modal.classList.remove('opacity-0', 'pointer-events-none');
            container.classList.remove('scale-95');
        }

        function closeModal() {
            const modal = document.getElementById('editModal');
            const container = document.getElementById('modalContainer');
            modal.classList.add('opacity-0', 'pointer-events-none');
            container.classList.add('scale-95');
        }
    </script>
</body>
</html>
