<?php
session_start();
if (isset($_SESSION['token'])) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Akun - Gemini Workstation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#131314] min-h-screen flex items-center justify-center font-sans p-4">
    
    <div class="bg-[#1e1e1f] p-8 rounded-3xl border border-[#3c4043] w-full max-w-sm shadow-2xl">
        
        <?php
        if (isset($_SESSION['error'])) {
            echo '<div class="bg-rose-500/10 border border-rose-500/30 text-rose-400 px-4 py-3 rounded-xl text-sm mb-6 text-center">' . $_SESSION['error'] . '</div>';
            unset($_SESSION['error']);
        }
        ?>

        <div id="js-alert-success" style="display: none;" class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-4 py-3 rounded-xl text-sm mb-6 text-center"></div>
        <div id="js-alert-error" style="display: none;" class="bg-rose-500/10 border border-rose-500/30 text-rose-400 px-4 py-3 rounded-xl text-sm mb-6 text-center"></div>

        <div id="login-container" style="display: block;">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-400">Welcome Back</h2>
                <p class="text-sm text-gray-400 mt-2">Sign in to access AI Workstation</p>
            </div>

            <form action="http://localhost/back-end-user-service/app/login/process_login.php" method="POST" class="flex flex-col gap-5">
                <div>
                    <label class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-2 block">Email</label>
                    <input type="email" name="email" required placeholder="user@example.com" class="w-full bg-[#131314] text-white border border-[#3c4043] rounded-xl px-4 py-3 focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-2 block">Password</label>
                    <input type="password" name="password" required placeholder="••••••••" class="w-full bg-[#131314] text-white border border-[#3c4043] rounded-xl px-4 py-3 focus:outline-none focus:border-blue-500">
                </div>
                <button type="submit" class="mt-2 w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 px-4 rounded-xl shadow-lg shadow-blue-500/20 transition-all">
                    Masuk ke Dashboard
                </button>
            </form>
            
            <p class="text-center text-sm text-gray-400 mt-6">
                Belum punya akun? 
                <button type="button" onclick="openRegister()" class="text-blue-400 hover:text-blue-300 font-bold ml-1">Daftar di sini</button>
            </p>
        </div>

        <div id="register-container" style="display: none;">
            <div class="text-center mb-6">
                <h2 class="text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-pink-400">Buat Akun</h2>
                <p class="text-sm text-gray-400 mt-2">Daftar untuk mengakses fitur AI</p>
            </div>

            <form id="form-register" onsubmit="processRegister(event)" class="flex flex-col gap-4">
                <div>
                    <label class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1 block">Nama Lengkap</label>
                    <input type="text" id="reg-name" required placeholder="Nama Anda" class="w-full bg-[#131314] text-white border border-[#3c4043] rounded-xl px-4 py-3 focus:outline-none focus:border-purple-500">
                </div>
                <div>
                    <label class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1 block">Email</label>
                    <input type="email" id="reg-email" required placeholder="user@example.com" class="w-full bg-[#131314] text-white border border-[#3c4043] rounded-xl px-4 py-3 focus:outline-none focus:border-purple-500">
                </div>
                <div>
                    <label class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1 block">Password</label>
                    <input type="password" id="reg-password" required placeholder="••••••••" class="w-full bg-[#131314] text-white border border-[#3c4043] rounded-xl px-4 py-3 focus:outline-none focus:border-purple-500">
                </div>
                <button type="submit" id="btn-register" class="mt-2 w-full bg-purple-600 hover:bg-purple-500 text-white font-bold py-3 px-4 rounded-xl shadow-lg shadow-purple-500/20 transition-all">
                    Daftar Sekarang
                </button>
            </form>
            
            <p class="text-center text-sm text-gray-400 mt-6">
                Sudah punya akun? 
                <button type="button" onclick="openLogin()" class="text-purple-400 hover:text-purple-300 font-bold ml-1">Masuk kembali</button>
            </p>
        </div>

    </div>

    <script>
        // FUNGSI GANTI LAYAR
        function openRegister() {
            document.getElementById('login-container').style.display = 'none';
            document.getElementById('register-container').style.display = 'block';
            hideAlerts();
        }

        function openLogin() {
            document.getElementById('register-container').style.display = 'none';
            document.getElementById('login-container').style.display = 'block';
            hideAlerts();
        }

        function hideAlerts() {
            document.getElementById('js-alert-success').style.display = 'none';
            document.getElementById('js-alert-error').style.display = 'none';
        }

        
        async function processRegister(event) {
            event.preventDefault();

            const btn = document.getElementById('btn-register');
            const errAlert = document.getElementById('js-alert-error');
            const succAlert = document.getElementById('js-alert-success');
            
            const name = document.getElementById('reg-name').value;
            const email = document.getElementById('reg-email').value;
            const password = document.getElementById('reg-password').value;

            btn.innerHTML = 'Memproses...';
            btn.disabled = true;
            hideAlerts();

            
            const targetUrl = 'http://localhost/back-end-user-service/app/app.php?route=register';

            try {
                const response = await fetch(targetUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ full_name: name, email: email, password: password })
                });

                const rawText = await response.text(); 
                
                try {
                    const data = JSON.parse(rawText);

                    if (response.status === 201 || data.status === 'success') {
                        document.getElementById('form-register').reset();
                        succAlert.innerHTML = 'Registrasi berhasil! Silakan login.';
                        succAlert.style.display = 'block';
                        openLogin(); 
                    } else {
                        errAlert.innerHTML = data.message || 'Pendaftaran gagal.';
                        errAlert.style.display = 'block';
                    }
                } catch (parseError) {
                    // JIKA 404, TAMPILKAN ALAMAT LENGKAP YANG DITEMBAK JS!
                    const fullUrl = new URL(targetUrl, document.baseURI).href;
                    errAlert.innerHTML = `
                        <b>Salah Sasaran (404)!</b><br>
                        JS mencoba menembak ke alamat ini:<br>
                        <span class="text-blue-300 text-xs">${fullUrl}</span><br><br>
                        Pesan asli:<br>
                        <textarea class="w-full h-24 mt-1 text-xs p-2 text-black rounded bg-gray-200" readonly>${rawText}</textarea>
                    `;
                    errAlert.style.display = 'block';
                }

            } catch (error) {
                errAlert.innerHTML = 'Gagal melakukan fetch (Server down).';
                errAlert.style.display = 'block';
            } finally {
                btn.innerHTML = 'Daftar Sekarang';
                btn.disabled = false;
            }
        }
    </script>
</body>
</html>