<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['token']);
$user = $isLoggedIn ? $_SESSION['user'] : ['full_name' => 'Tamu / Guest', 'email' => ''];
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-[#131314]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Astra AI Workstation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #1e1e1f; }
        ::-webkit-scrollbar-thumb { background: #3c4043; border-radius: 10px; }
    </style>
</head>
<body class="h-full flex text-gray-200 font-sans antialiased overflow-hidden">

    <aside id="sidebar" class="w-68 bg-[#1e1e1f] flex flex-col justify-between border-r border-[#3c4043] p-4 hidden md:flex absolute md:relative z-50 h-full transition-all duration-300 w-64 md:w-68">
        <div class="flex flex-col gap-6">
            <div class="flex items-center justify-between pl-2">
                <div class="flex items-center gap-3">
                    <i onclick="toggleSidebar()" class="fa-solid fa-bars text-xl text-gray-400 cursor-pointer hover:text-white md:hidden"></i>
                    <i class="fa-solid fa-bars text-xl text-gray-400 cursor-pointer hover:text-white hidden md:block"></i>
                    <span class="font-semibold text-lg tracking-wide text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400">Astra AI</span>
                </div>
            </div>

            <button onclick="checkAuth(clearChat)" class="flex items-center gap-3 bg-[#131314] hover:bg-[#282a2c] text-sm font-medium py-3 px-4 rounded-full border border-[#3c4043] transition-all text-gray-300 shadow-md">
                <i class="fa-solid fa-plus text-blue-400"></i>
                <span>Percakapan Baru</span>
            </button>

            <div class="flex flex-col gap-2 flex-1">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider pl-2">Riwayat Obrolan</span>
                <div id="chatHistoryList" class="flex flex-col gap-1 max-h-[60vh] overflow-y-auto pr-1">
                    <?php if (!$isLoggedIn): ?>
                        <div class="text-[11px] text-gray-500 pl-2 py-2">Silakan login untuk melihat riwayat.</div>
                    <?php else: ?>
                        <div class="text-[11px] text-gray-500 pl-2 italic py-2">Memuat riwayat...</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="border-t border-[#3c4043] pt-4 flex flex-col gap-3">
            <div onclick="checkAuth(openProfileModal)" class="flex items-center justify-between p-2 rounded-xl bg-[#131314] cursor-pointer hover:bg-[#282a2c] transition-all border border-transparent hover:border-[#3c4043]">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div class="w-9 h-9 flex-shrink-0 rounded-full <?= $isLoggedIn ? 'bg-gradient-to-tr from-blue-500 to-purple-600' : 'bg-gray-700' ?> flex items-center justify-center font-bold text-white text-sm shadow-md">
                        <?= $isLoggedIn ? strtoupper(substr($user['full_name'], 0, 2)) : 'G' ?>
                    </div>
                    <div class="flex flex-col truncate">
                        <span class="text-sm font-medium text-white truncate w-24"><?= htmlspecialchars($user['full_name']) ?></span>
                        <span id="subBadge" class="text-[10px] font-bold px-1.5 py-0.5 rounded w-max mt-0.5 <?= $isLoggedIn ? 'bg-gray-600 text-gray-300' : 'bg-transparent text-gray-500' ?>">
                            <?= $isLoggedIn ? 'CHECKING...' : 'Belum Login' ?>
                        </span>
                    </div>
                </div>
                <i class="fa-solid fa-gear text-gray-500 text-xs mr-2"></i>
            </div>
            
            <?php if (!$isLoggedIn): ?>
                <a href="http://localhost/back-end-user-service/views/login.php" class="text-xs text-center font-bold text-blue-400 hover:text-blue-300 transition-all"><i class="fa-solid fa-right-to-bracket mr-1"></i> Login / Register</a>
            <?php endif; ?>
        </div>
    </aside>

    <main class="flex-1 flex flex-col justify-between bg-[#131314] relative h-full">
        <header class="md:hidden flex items-center justify-between p-4 bg-[#1e1e1f] border-b border-[#3c4043]">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="text-gray-400 hover:text-white transition-all"><i class="fa-solid fa-bars text-xl"></i></button>
                <span class="font-bold text-blue-400">Astra Workstation</span>
            </div>
            <button onclick="checkAuth(openProfileModal)" class="text-gray-400 hover:text-white"><i class="fa-solid fa-user text-lg"></i></button>
        </header>

        <div id="chatBox" class="flex-1 overflow-y-auto p-4 md:p-8 space-y-6 max-w-4xl w-full mx-auto pb-32">
            <div id="welcomeScreen" class="h-full flex flex-col justify-center items-start pt-12 gap-2">
                <h1 class="text-4xl md:text-5xl font-medium text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400">Halo, <?= htmlspecialchars($user['full_name']) ?></h1>
                <p class="text-2xl md:text-3xl font-medium text-[#444746]">Ada yang bisa saya bantu hari ini?</p>
            </div>
        </div>

        <div class="p-4 md:pb-8 max-w-4xl w-full mx-auto bg-[#131314] absolute bottom-0 left-0 right-0 md:relative md:bg-transparent">
            <div id="inputContainer" class="relative flex items-center bg-[#1e1e1f] rounded-full border border-[#3c4043] focus-within:border-[#4285f4] px-4 py-2 shadow-lg">
                <input type="text" id="userMessage" onfocus="checkAuth()" onclick="checkAuth()" onkeypress="handleEnter(event)" placeholder="Tulis pertanyaan di sini..." class="w-full bg-transparent text-sm focus:outline-none text-gray-200 py-2 pr-12 pl-2">
                <button onclick="checkAuth(sendMessage)" class="absolute right-3 bg-[#131314] hover:bg-[#282a2c] w-9 h-9 rounded-full flex items-center justify-center text-blue-400 hover:text-white transition-all">
                    <i class="fa-solid fa-paper-plane text-sm"></i>
                </button>
            </div>
            <p class="text-center text-[11px] text-gray-500 mt-2 hidden md:block">Astra PHP Workstation dapat memunculkan informasi yang tidak akurat, harap periksa kembali akurasi sistem.</p>
        </div>

        <div id="subBlocker" class="absolute inset-0 bg-[#131314]/90 backdrop-blur-md hidden flex flex-col items-center justify-center text-center p-6 z-40">
            <div class="bg-[#1e1e1f] border border-[#3c4043] p-8 rounded-3xl max-w-md shadow-2xl flex flex-col items-center gap-4">
                <div class="w-16 h-16 bg-amber-500/10 text-amber-400 rounded-full flex items-center justify-center text-3xl mb-2"><i class="fa-solid fa-lock"></i></div>
                <h2 class="text-2xl font-bold text-white">Fitur Premium Terkunci</h2>
                <button onclick="openUpgradeModal()" class="mt-4 w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white font-semibold py-3 px-6 rounded-full shadow-lg hover:opacity-90 transition-all">
                    Lihat Paket Berlangganan
                </button>
            </div>
        </div>
    </main>

    <div id="profileModal" class="fixed inset-0 bg-black/80 flex items-center justify-center hidden z-50 p-4 backdrop-blur-sm">
        <div class="bg-[#1e1e1f] border border-[#3c4043] rounded-3xl max-w-md w-full p-6 relative flex flex-col shadow-2xl">
            <button onclick="closeProfileModal()" class="absolute top-4 right-4 text-gray-400 hover:text-white"><i class="fa-solid fa-xmark text-xl"></i></button>
            <h3 class="text-xl font-bold text-white mb-6 border-b border-[#3c4043] pb-3">Pengaturan Profil</h3>
            
            <div class="flex flex-col gap-4 mb-6">
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">Nama Lengkap</label>
                    <input type="text" id="editFullName" value="<?= htmlspecialchars($user['full_name']) ?>" class="w-full bg-[#131314] border border-[#3c4043] rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">Alamat Email</label>
                    <input type="email" id="editEmail" value="<?= htmlspecialchars($user['email'] ?? '') ?>" class="w-full bg-[#131314] border border-[#3c4043] rounded-xl px-4 py-2.5 text-sm text-gray-500 cursor-not-allowed" disabled title="Email tidak dapat diubah">
                </div>
            </div>

            <div class="flex flex-col gap-3">
                <button onclick="updateProfile()" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-medium py-2.5 rounded-xl text-sm transition-all shadow-lg">Simpan Perubahan Profil</button>
                <button onclick="cancelSubscription()" class="w-full bg-[#282a2c] hover:bg-rose-500/20 hover:text-rose-400 text-gray-300 border border-[#3c4043] hover:border-rose-500/50 font-medium py-2.5 rounded-xl text-sm transition-all">Berhenti Berlangganan</button>
                <div class="border-t border-[#3c4043] my-2"></div>
                <button onclick="deleteAccount()" class="w-full bg-rose-600 hover:bg-rose-700 text-white font-medium py-2.5 rounded-xl text-sm transition-all shadow-lg">Hapus Akun Permanen</button>
                <a href="http://localhost/back-end-user-service/app/login/logout.php" class="text-center mt-2 text-sm text-gray-500 hover:text-white">Keluar Sesi (Logout)</a>
            </div>
        </div>
    </div>
    
    <div id="authModal" class="fixed inset-0 bg-black/70 flex items-center justify-center hidden z-50 p-4"><div class="bg-[#1e1e1f] border border-[#3c4043] rounded-3xl max-w-sm w-full p-8 relative flex flex-col items-center text-center"><button onclick="closeAuthModal()" class="absolute top-4 right-5 text-gray-400 hover:text-white"><i class="fa-solid fa-xmark text-xl"></i></button><h3 class="text-2xl font-bold text-white mb-2 mt-4">Akses Dibatasi</h3><p class="text-sm text-gray-400 mb-8">Silakan Login terlebih dahulu.</p><a href="http://localhost/back-end-user-service/views/login.php" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 px-4 rounded-xl">Masuk</a></div></div>
    
    <div id="upgradeModal" class="fixed inset-0 bg-black/70 flex items-center justify-center hidden z-50 p-4">
        <div class="bg-[#1e1e1f] border border-[#3c4043] rounded-3xl max-w-lg w-full p-6 relative">
            <button onclick="closeUpgradeModal()" class="absolute top-4 right-4 text-gray-400 hover:text-white"><i class="fa-solid fa-xmark text-xl"></i></button>
            <h3 class="text-xl font-bold text-white mb-1 text-center">Pilih Paket Berlangganan</h3>
            <p class="text-xs text-gray-400 text-center mb-6">Akses infrastruktur model machine learning tanpa batasan token</p>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                <div class="border border-[#3c4043] p-5 rounded-2xl flex flex-col justify-between bg-[#131314] hover:border-blue-500 transition-all">
                    <div>
                        <span class="text-xs font-bold text-blue-400 bg-blue-500/10 px-2 py-0.5 rounded">POPULER</span>
                        <h4 class="text-lg font-bold text-white mt-2">Pro Plan</h4>
                        <p class="text-[10px] text-gray-400 mt-1">Ideal untuk individu dan pelajar</p>
                    </div>
                    <div class="mt-6">
                        <span class="text-2xl font-bold text-white">Rp 49.000</span>
                        <button onclick="buyPlan('2cda222a-e624-4425-8c96-dcf5816394e4', 49000)" class="w-full mt-4 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 rounded-xl text-xs transition-all">Pilih Paket</button>
                    </div>
                </div>
                <div class="border border-[#3c4043] p-5 rounded-2xl flex flex-col justify-between bg-[#131314] hover:border-purple-500 transition-all">
                    <div>
                        <span class="text-xs font-bold text-purple-400 bg-purple-500/10 px-2 py-0.5 rounded">ADVANCED</span>
                        <h4 class="text-lg font-bold text-white mt-2">Plus Plan</h4>
                        <p class="text-[10px] text-gray-400 mt-1">Akses prioritas model & API</p>
                    </div>
                    <div class="mt-6">
                        <span class="text-2xl font-bold text-white">Rp 99.000</span>
                        <button onclick="buyPlan('66c40f09-a199-434a-9303-457baa9b8740', 99000)" class="w-full mt-4 bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 rounded-xl text-xs transition-all">Pilih Paket</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const API_BASE_URL = 'http://localhost/back-end-user-service/app/app.php?route=';
        const isLoggedIn = <?= $isLoggedIn ? 'true' : 'false' ?>;
        let globalChatHistory = [];

        document.addEventListener("DOMContentLoaded", function() {
            if (isLoggedIn) { checkSubscription(); loadChatHistory(); }
        });

        
        function escapeHTML(str) {
            if (typeof str !== 'string') return str;
            return str.replace(/&/g, '&amp;')
                      .replace(/</g, '&lt;')
                      .replace(/>/g, '&gt;')
                      .replace(/"/g, '&quot;')
                      .replace(/'/g, '&#039;');
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('hidden');
        }

        function handleEnter(event) { if (event.key === "Enter") checkAuth(sendMessage); }
        function checkAuth(actionCallback = null) {
            if (!isLoggedIn) { document.getElementById('authModal').style.display = 'flex'; return; }
            if (actionCallback) actionCallback();
        }

        function openProfileModal() { document.getElementById('profileModal').style.display = 'flex'; }
        function closeProfileModal() { document.getElementById('profileModal').style.display = 'none'; }
        function closeAuthModal() { document.getElementById('authModal').style.display = 'none'; }
        function closeUpgradeModal() { document.getElementById('upgradeModal').style.display = 'none'; }
        function openUpgradeModal() { document.getElementById('profileModal').style.display = 'none'; document.getElementById('upgradeModal').style.display = 'flex'; }

        function updateProfile() {
            const newName = document.getElementById('editFullName').value;
            fetch(API_BASE_URL + 'update-profile', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ full_name: newName })
            }).then(res => res.json()).then(data => {
                alert(data.message);
                if(data.status === 'success') location.reload();
            });
        }

        function cancelSubscription() {
            if(confirm("Yakin ingin membatalkan langganan premium? Sisa hari tidak dapat di-refund.")){
                fetch(API_BASE_URL + 'cancel-subscription', { method: 'POST' })
                .then(res => res.json()).then(data => {
                    alert(data.message); location.reload();
                });
            }
        }

        function deleteAccount() {
            if(confirm("PERINGATAN KERAS! Akun dan semua riwayat chat akan dihapus permanen. Lanjutkan?")){
                fetch(API_BASE_URL + 'delete-user', { method: 'POST' })
                .then(res => res.json()).then(data => {
                    alert(data.message); window.location.href = "http://localhost/back-end-user-service/app/login/logout.php";
                });
            }
        }

        function checkSubscription() {
            fetch(API_BASE_URL + 'my-subscription')
                .then(res => res.json())
                .then(res => {
                    const badge = document.getElementById('subBadge');
                    const blocker = document.getElementById('subBlocker');
                    if (res.status === 'success') {
                        badge.innerText = res.data.plan_name.toUpperCase();
                        badge.className = "text-[10px] font-bold px-1.5 py-0.5 rounded w-max mt-0.5 bg-emerald-500/20 text-emerald-400 border border-emerald-500/30";
                        blocker.style.display = 'none';
                    } else {
                        badge.innerText = "GRATISAN";
                        badge.className = "text-[10px] font-bold px-1.5 py-0.5 rounded w-max mt-0.5 bg-rose-500/20 text-rose-400 border border-rose-500/30";
                        blocker.style.display = 'flex';
                    }
                });
        }

       
        function loadChatHistory() {
            fetch(API_BASE_URL + 'chat-history')
                .then(res => res.json())
                .then(data => {
                    const listContainer = document.getElementById('chatHistoryList');
                    let messages = [];
                    if (Array.isArray(data)) messages = data;
                    else if (data && data.messages) messages = data.messages;
                    else if (data && data.data) messages = data.data;

                    if (messages.length > 0) {
                        listContainer.innerHTML = '';
                        globalChatHistory = messages;
                        
                        const item = document.createElement('div');
                        item.className = "flex items-center gap-2 p-2 rounded-lg bg-[#282a2c] border border-[#3c4043] text-xs cursor-pointer text-white truncate transition-all";
                        item.innerHTML = `<i class="fa-regular fa-comment text-blue-400"></i> <span class="truncate font-medium">Obrolan Tersimpan</span>`;
                        item.onclick = () => renderSavedChat(); 
                        listContainer.appendChild(item);
                    } else {
                        // Kunci Perbaikan: Hanya cetak "Kosong" JIKA di UI tidak ada obrolan berjalan
                        if (!document.getElementById('current-chat-session')) {
                            listContainer.innerHTML = '<div class="text-[11px] text-gray-500 pl-2 py-2">Belum ada riwayat obrolan.</div>';
                        }
                    }
                }).catch(err => {
                    if (!document.getElementById('current-chat-session')) {
                        document.getElementById('chatHistoryList').innerHTML = '<div class="text-[11px] text-gray-500 pl-2 py-2">Belum ada riwayat</div>';
                    }
                });
        }

        function renderSavedChat() {
            if (globalChatHistory.length === 0) return;
            const chatBox = document.getElementById('chatBox');
            chatBox.innerHTML = ''; 

            globalChatHistory.forEach(msg => {
                let sender = (msg.role === 'user' || msg.type === 'human' || msg.sender === 'user') ? 'user' : 'bot';
                let text = msg.content || msg.message || msg.text || '';
                if (text) appendMessage(text, sender, false, true); 
            });
            setTimeout(() => { chatBox.scrollTop = chatBox.scrollHeight; }, 100);
            if(window.innerWidth < 768) toggleSidebar(); 
        }

        function sendMessage() {
            const input = document.getElementById('userMessage');
            const message = input.value.trim();
            if (!message) return;

            const welcomeScreen = document.getElementById('welcomeScreen');
            if (welcomeScreen) welcomeScreen.remove();

            appendMessage(message, 'user');
            input.value = '';

            const listContainer = document.getElementById('chatHistoryList');
            if (listContainer.innerHTML.includes('Belum ada riwayat') || listContainer.innerHTML.includes('Memuat')) {
                listContainer.innerHTML = '';
            }
            if (!document.getElementById('current-chat-session')) {
                const item = document.createElement('div');
                item.id = 'current-chat-session';
                item.className = "flex items-center gap-2 p-2 rounded-lg bg-[#282a2c] text-xs cursor-pointer text-white truncate transition-all border border-[#3c4043]";
                item.innerHTML = `<i class="fa-regular fa-message text-blue-400"></i> <span class="truncate">${message.substring(0, 18)}...</span>`;
                listContainer.prepend(item);
            }

            const loadId = appendMessage('Sedang menganalisis...', 'bot', true);

            fetch(API_BASE_URL + 'chat', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message: message })
            })
            .then(res => res.json())
            .then(data => {
                document.getElementById(loadId).remove();
                if (data.reply || data.status === 'success') {
                    appendMessage(data.reply || data.message, 'bot');
                } else {
                    appendMessage('Error: Akses ditolak.', 'bot');
                }
                loadChatHistory(); 
            }).catch(err => {
                document.getElementById(loadId).remove();
                appendMessage('Terjadi kesalahan jaringan.', 'bot');
            });
        }

        function appendMessage(rawText, sender, isLoading = false, isHistoryLoad = false) {
            const chatBox = document.getElementById('chatBox');
            const msgDiv = document.createElement('div');
            const id = "msg-" + Date.now() + Math.random();
            msgDiv.id = id;
            
            // PENGGUNAAN PENETRALISIR (Anti-Potong Chat)
            let safeText = typeof rawText === 'object' ? JSON.stringify(rawText, null, 2) : String(rawText);
            let escapedText = escapeHTML(safeText); // HTML di-escape dulu
            let formattedText = escapedText.replace(/\n/g, '<br>'); // Baru diubah enter-nya
            
            if (sender === 'user') {
                msgDiv.className = "flex justify-end";
                msgDiv.innerHTML = `<div class="bg-[#282a2c] text-sm text-gray-200 px-4 py-3 rounded-2xl max-w-[85%] shadow-md whitespace-pre-wrap" style="word-break: break-word;">${formattedText}</div>`;
            } else {
                msgDiv.className = "flex gap-4 items-start bg-[#1a1a1c]/40 p-4 rounded-2xl border border-[#282a2c]";
                msgDiv.innerHTML = `
                    <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-cyan-400 to-blue-600 flex items-center justify-center text-xs font-bold text-white shadow flex-shrink-0"><i class="fa-solid fa-robot"></i></div>
                    <div class="flex-1 text-sm leading-relaxed ${isLoading ? 'animate-pulse text-gray-500' : 'text-gray-300'} whitespace-pre-wrap" style="word-break: break-word; overflow-x: auto;">${formattedText}</div>
                `;
            }
            chatBox.appendChild(msgDiv);
            
            if(!isHistoryLoad) {
                setTimeout(() => { chatBox.scrollTop = chatBox.scrollHeight; }, 50);
            }
            return id;
        }

        function buyPlan(planId, price) {
            fetch(API_BASE_URL + 'payment', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ plan_id: planId, amount: price, payment_status: 'success' })
            }).then(res => res.json()).then(data => {
                alert(data.message || 'Pembayaran diproses.');
                closeUpgradeModal(); checkSubscription(); 
            });
        }

        function clearChat() {
            document.getElementById('chatBox').innerHTML = `
            <div id="welcomeScreen" class="h-full flex flex-col justify-center items-start pt-12 gap-2">
                <h1 class="text-4xl md:text-5xl font-medium text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400">Percakapan Baru</h1>
                <p class="text-2xl md:text-3xl font-medium text-[#444746]">Silakan ketik pertanyaan Anda.</p>
            </div>`;
            if(window.innerWidth < 768) toggleSidebar();
        }
    </script>
</body>
</html>