<?php
// Mulakan sesi jika belum dimulakan
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DATA PELANGGAN - UNIT KONSULTASI DAN KIOSK CDN IPOH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Gaya CSS anda di sini (tidak berubah dari versi sebelumnya) */
        .header-bg { background-color: #1e40af; }
        .loading-spinner { display: inline-block; width: 1rem; height: 1rem; border: 2px solid rgba(255,255,255,.3); border-radius: 50%; border-top-color: #fff; animation: spin 1s ease-in-out infinite; margin-left: 8px; vertical-align: middle; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .nav-tab { transition: all 0.2s ease; padding: 1rem 0.25rem; margin-bottom: -1px; border-bottom: 3px solid transparent; cursor: pointer; }
        .nav-tab:hover { border-bottom-color: #ddd; color: #374151; }
        .nav-tab.active { border-bottom-color: #1e40af; font-weight: 600; color: #1e40af;}
        .login-container { max-width: 400px; margin: 5rem auto 0 auto; padding: 2rem; background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); }
        #loginScreen { display: none; /* Disembunyikan secara lalai, JS akan tunjukkan jika perlu */ }
        #appContainer { display: none; /* Disembunyikan secara lalai */ }
        /* Logik paparan dikawal oleh JavaScript checkLoginStatus */
        .modal-content { max-height: 70vh; overflow-y: auto; } /* Untuk modal scrollable */
        /* Sembunyikan scrollbar tapi kekalkan fungsi scroll */
        .modal-content::-webkit-scrollbar { width: 8px; }
        .modal-content::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px;}
        .modal-content::-webkit-scrollbar-thumb { background: #888; border-radius: 10px;}
        .modal-content::-webkit-scrollbar-thumb:hover { background: #555; }

    </style>
</head>
<body class="bg-gray-100">

    <div id="appContainer" class="min-h-screen hidden">
        <div class="header-bg text-white p-4 shadow-md">
            <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center">
                <div class="mb-2 sm:mb-0 text-center sm:text-left">
                    <h1 class="text-xl md:text-2xl font-bold">DATA PELANGGAN</h1>
                    <p class="text-xs md:text-sm">UNIT KONSULTASI DAN KIOSK CDN IPOH</p>
                </div>
                <div class="flex items-center space-x-3 md:space-x-4">
                    <span id="userDisplay" class="text-xs sm:text-sm bg-white text-blue-800 px-2 py-1 rounded">Memuatkan...</span>
                    <button type="button" onclick="logout()" class="text-xs sm:text-sm bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition duration-150 ease-in-out">
                        Log Keluar
                    </button>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-sm sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <nav class="flex space-x-6 sm:space-x-8 -mb-px" id="navigationTabs">
                    <button type="button" id="tabDataEntry" class="nav-tab whitespace-nowrap">Entri Data</button>
                    <button type="button" id="tabRecords" class="nav-tab whitespace-nowrap">Rekod Pelanggan</button>
                    <button type="button" id="tabUserManagement" class="nav-tab hidden whitespace-nowrap">Pengurusan Pengguna</button>
                </nav>
            </div>
        </div>

        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div id="dataEntryContent" class="hidden">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold mb-4">Entri Data Pelanggan Baru</h2>
                        <form id="dataForm" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="currentDate" class="block text-sm font-medium text-gray-700 mb-1">Tarikh *</label>
                                <input type="date" name="tarikh" id="currentDate" class="p-2 border rounded w-full focus:ring-indigo-500 focus:border-indigo-500" required>
                            </div>
                            <div>
                                <label for="currentTime" class="block text-sm font-medium text-gray-700 mb-1">Masa *</label>
                                <input type="time" name="masa" id="currentTime" class="p-2 border rounded w-full focus:ring-indigo-500 focus:border-indigo-500" required>
                            </div>
                            <div>
                                <label for="namaPelanggan" class="block text-sm font-medium text-gray-700 mb-1">Nama Pelanggan *</label>
                                <input type="text" name="namaPelanggan" id="namaPelanggan" placeholder="Nama Penuh Pelanggan" class="p-2 border rounded w-full focus:ring-indigo-500 focus:border-indigo-500" required>
                            </div>
                           <div>
                                <label for="namaSyarikat" class="block text-sm font-medium text-gray-700 mb-1">Nama Syarikat</label>
                                <input type="text" name="namaSyarikat" id="namaSyarikat" placeholder="Nama Syarikat (jika ada)" class="p-2 border rounded w-full focus:ring-indigo-500 focus:border-indigo-500">
                           </div>
                            <div>
                                <label for="kaedah" class="block text-sm font-medium text-gray-700 mb-1">Kaedah</label>
                                <select name="kaedah" id="kaedah" class="p-2 border rounded w-full focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Pilih Kaedah</option>
                                    <option value="Bersemuka">Bersemuka</option>
                                    <option value="Telefon">Telefon</option>
                                    <option value="Emel">Emel</option>
                                    <option value="Surat">Surat</option>
                                </select>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="telefon" class="block text-sm font-medium text-gray-700 mb-1">No. Telefon</label>
                                    <input type="tel" name="telefon" id="telefon" placeholder="No. Telefon (cth: 0123456789)" pattern="[0-9+\- ]+" title="Sila masukkan nombor telefon yang sah" class="p-2 border rounded w-full focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div>
                                    <label for="emel" class="block text-sm font-medium text-gray-700 mb-1">Emel</label>
                                    <input type="email" name="emel" id="emel" placeholder="Emel (cth: pengguna@domain.com)" class="p-2 border rounded w-full focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                            </div>
                            <div>
                                <label for="pegawai" class="block text-sm font-medium text-gray-700 mb-1">Pegawai Bertugas</label>
                                <select name="pegawai" id="pegawai" class="p-2 border rounded w-full focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Pilih Pegawai</option>
                                    <option value="Santhanavel Ramesh A/L Subramaniam">Santhanavel Ramesh A/L Subramaniam</option>
                                    <option value="Mohamed Inmran bin Sahul Hamid">Mohamed Inmran bin Sahul Hamid</option>
                                    <option value="Haziq bin Zakaria">Haziq bin Zakaria</option>
                                    <option value="Mohd Nazri bin Mohd Noh">Mohd Nazri bin Mohd Noh</option>
                                    <option value="Siti Bahayah binti Baharom">Siti Bahayah binti Baharom</option>
                                    <option value="Nur Hawari binti Mohd Johar @ Mohd Johan">Nur Hawari binti Mohd Johar @ Mohd Johan</option>
                                    <option value="Noor Umaira binti Md Yusop">Noor Umaira binti Md Yusop</option>
                                    <option value="Siti Zulaikha bint Eroi">Siti Zulaikha bint Eroi</option>
                                    <option value="Umum">Umum</option>
                                </select>
                            </div>
                            <div class="col-span-1 md:col-span-2 space-y-4">
                                <div>
                                    <label for="perkara" class="block text-sm font-medium text-gray-700 mb-1">Perkara</label>
                                    <select name="perkara" id="perkara" class="p-2 border rounded w-full focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">Pilih Perkara</option>
                                        <option value="Cukai Jualan">Cukai Jualan</option>
                                        <option value="Cukai Perkhidmatan">Cukai Perkhidmatan</option>
                                        <option value="GST">GST</option>
                                        <option value="Cukai Pelancongan">Cukai Pelancongan</option>
                                        <option value="Duti Import">Duti Import</option>
                                        <option value="LMW">LMW</option>
                                        <option value="FIZ">FIZ</option>
                                        <option value="ABT">ABT</option>
                                        <option value="Lain-Lain">Lain-Lain</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="isu" class="block text-sm font-medium text-gray-700 mb-1">Isu</label>
                                    <select name="isu" id="isu" class="p-2 border rounded w-full focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">Pilih Isu</option>
                                        <option value="Aduan/Lain-Lain">Aduan/Lain-Lain</option>
                                        <option value="Audit">Audit</option>
                                        <option value="B2B Exemption">B2B Exemption</option>
                                        <option value="BOD">BOD</option>
                                        <option value="Pembatalan">Pembatalan</option>
                                        <option value="Pembatalan Pengecualian">Pembatalan Pengecualian</option>
                                        <option value="Penalti">Penalti</option>
                                        <option value="Pendaftaran">Pendaftaran</option>
                                        <option value="Pengecualian Cukai">Pengecualian Cukai</option>
                                        <option value="Penukaran Alamat">Penukaran Alamat</option>
                                        <option value="Penukaran Emel">Penukaran Emel</option>
                                        <option value="Penyata">Penyata</option>
                                        <option value="Penyata SST-02 Pembayaran">Penyata SST-02 Pembayaran</option>
                                        <option value="Penyata SST-02 Penghantaran">Penyata SST-02 Penghantaran</option>
                                        <option value="Pertanyaan Nilai Cukai">Pertanyaan Nilai Cukai</option>
                                        <option value="Refund / Drawback">Refund / Drawback</option>
                                        <option value="Remisi">Remisi</option>
                                        <option value="Reset Password">Reset Password</option>
                                        <option value="Senarai hitam Imigresen">Senarai hitam Imigresen</option>
                                        <option value="Invoice">Invoice</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="pertanyaan" class="block text-sm font-medium text-gray-700 mb-1">Pertanyaan</label>
                                    <textarea name="pertanyaan" id="pertanyaan" placeholder="Masukkan butiran pertanyaan pelanggan" class="p-2 border rounded w-full h-24 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                                </div>
                                <div>
                                    <label for="jawapan" class="block text-sm font-medium text-gray-700 mb-1">Jawapan/Tindakan</label>
                                    <textarea name="jawapan" id="jawapan" placeholder="Masukkan jawapan atau tindakan yang diberikan" class="p-2 border rounded w-full h-24 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                                </div>
                            </div>

                            <button type="submit" class="btn-submit bg-blue-600 text-white p-2 rounded col-span-1 md:col-span-2 hover:bg-blue-700 transition duration-150 ease-in-out flex items-center justify-center">
                                <span id="submit-text">Hantar Data</span>
                                <span id="spinner" class="loading-spinner" style="display:none;"></span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div id="recordsContent" class="hidden">
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="p-6">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                            <h2 class="text-xl font-semibold">Rekod Pelanggan</h2>
                            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                                <input type="text" id="searchInput" placeholder="Cari rekod..." class="p-2 border rounded w-full sm:w-64 focus:ring-indigo-500 focus:border-indigo-500">
                                <div class="flex gap-2">
                                    <button type="button" onclick="downloadExcel()" class="bg-green-600 text-white px-3 py-2 rounded hover:bg-green-700 transition duration-150 ease-in-out whitespace-nowrap text-sm">
                                        Muat Turun Excel
                                    </button>
                                    <button type="button" id="clearAllButton" onclick="clearAllData()" class="bg-red-500 text-white px-3 py-2 rounded hover:bg-red-600 transition duration-150 ease-in-out whitespace-nowrap text-sm hidden">
                                        Kosongkan Semua
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="overflow-x-auto border rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bil</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Pelanggan</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Syarikat</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kaedah</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Isu</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarikh</th>
                                        <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tindakan</th>
                                    </tr>
                                </thead>
                                <tbody id="recordsTableBody" class="bg-white divide-y divide-gray-200">
                                    </tbody>
                            </table>
                        </div>

                        <div class="mt-4 flex flex-col sm:flex-row justify-between items-center gap-3">
                            <div class="text-sm text-gray-500">
                                Menunjukkan <span id="showingFrom" class="font-medium">0</span> hingga <span id="showingTo" class="font-medium">0</span> daripada <span id="totalRecords" class="font-medium">0</span> rekod
                            </div>
                            <div class="flex space-x-2">
                                <button type="button" id="prevPage" class="px-3 py-1 border rounded text-sm disabled:opacity-50 bg-white hover:bg-gray-50 disabled:cursor-not-allowed" disabled>Sebelum</button>
                                <button type="button" id="nextPage" class="px-3 py-1 border rounded text-sm disabled:opacity-50 bg-white hover:bg-gray-50 disabled:cursor-not-allowed" disabled>Seterusnya</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="userManagementContent" class="hidden">
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="p-6">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                            <h2 class="text-xl font-semibold">Pengurusan Pengguna</h2>
                            <button type="button" onclick="showAddUserModal()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition duration-150 ease-in-out whitespace-nowrap text-sm">
                                Tambah Pengguna Baru
                            </button>
                        </div>

                        <div class="overflow-x-auto border rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Pengguna</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Penuh</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Akaun</th>
                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tindakan</th>
                                    </tr>
                                </thead>
                                <tbody id="usersTableBody" class="bg-white divide-y divide-gray-200">
                                    </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div> </div> <div id="loginScreen" class="login-container">
        <div id="loginFormContainer" class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <h2 class="text-2xl font-bold text-center mb-6">Log Masuk Sistem</h2>
                <form id="loginForm" class="space-y-4">
                    <div>
                        <label for="loginUsername" class="block text-sm font-medium text-gray-700 mb-1">Nama Pengguna</label>
                        <input type="text" id="loginUsername" name="username" class="p-2 border rounded w-full focus:ring-indigo-500 focus:border-indigo-500" required>
                    </div>
                    <div>
                        <label for="loginPassword" class="block text-sm font-medium text-gray-700 mb-1">Kata Laluan</label>
                        <input type="password" id="loginPassword" name="password" class="p-2 border rounded w-full focus:ring-indigo-500 focus:border-indigo-500" required>
                    </div>
                    <button type="submit" id="loginButton" class="w-full bg-blue-600 text-white p-2 rounded hover:bg-blue-700 transition duration-150 ease-in-out flex justify-center items-center">
                        <span id="loginButtonText">Log Masuk</span>
                        <span id="loginSpinner" class="loading-spinner" style="display:none;"></span>
                    </button>
                    <div id="loginError" class="text-red-600 text-sm mt-2 text-center h-4"></div>
                </form>
            </div>
        </div>
    </div>


    <div id="addUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center p-4 z-50 hidden transition-opacity duration-300" aria-labelledby="modal-title-add-user" role="dialog" aria-modal="true">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md transform transition-all duration-300 ease-out">
            <form id="addUserForm"> <div class="p-6"> <div class="flex justify-between items-center mb-4"> <h3 class="text-lg font-semibold text-gray-900" id="modal-title-add-user">Tambah Pengguna Baru</h3> <button type="button" onclick="hideAddUserModal()" class="text-gray-400 hover:text-gray-600"> <span class="sr-only">Tutup</span> <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /> </svg> </button> </div> <div class="space-y-4"> <div> <label for="addFullName" class="block text-sm font-medium text-gray-700 mb-1">Nama Penuh *</label> <input type="text" id="addFullName" class="p-2 border rounded w-full focus:ring-indigo-500 focus:border-indigo-500" required> </div> <div> <label for="addUsername" class="block text-sm font-medium text-gray-700 mb-1">Nama Pengguna *</label> <input type="text" id="addUsername" class="p-2 border rounded w-full focus:ring-indigo-500 focus:border-indigo-500" required> </div> <div> <label for="addPassword" class="block text-sm font-medium text-gray-700 mb-1">Kata Laluan *</label> <input type="password" id="addPassword" class="p-2 border rounded w-full focus:ring-indigo-500 focus:border-indigo-500" required minlength="6"> <p class="text-xs text-gray-500 mt-1">Minimum 6 aksara.</p> </div> <div> <label for="addRole" class="block text-sm font-medium text-gray-700 mb-1">Jenis Akaun *</label> <select id="addRole" class="p-2 border rounded w-full focus:ring-indigo-500 focus:border-indigo-500" required> <option value="user">Pengguna Biasa</option> <option value="admin">Administrator</option> </select> </div> <div id="addUserError" class="text-red-600 text-sm mt-2 text-center h-4"></div> </div> <div class="flex justify-end space-x-3 pt-5 border-t mt-4"> <button type="button" onclick="hideAddUserModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition duration-150 ease-in-out text-sm"> Batal </button> <button type="submit" id="addUserSubmitButton" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition duration-150 ease-in-out text-sm flex justify-center items-center"> <span id="addUserSubmitText">Simpan</span> <span id="addUserSpinner" class="loading-spinner" style="display:none;"></span> </button> </div> </div> </form>
        </div>
    </div>

    <div id="recordModalContainer"></div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <script>
    // =========================================================================
    // JAVASCRIPT (Versi Disesuaikan - Menggunakan API Backend)
    // =========================================================================
    let currentPage = 1;
    const recordsPerPage = 10;
    let allRecords = [];        // Semua rekod pelanggan dari server
    let filteredRecords = [];   // Rekod pelanggan selepas ditapis carian
    let currentUser = null;     // Data pengguna yang log masuk (dari sesi server)

    // Semak status log masuk semasa halaman dimuatkan
    document.addEventListener('DOMContentLoaded', function() {
      checkLoginStatus();

      // Sediakan borang log masuk
      const loginForm = document.getElementById('loginForm');
      if (loginForm) {
        loginForm.addEventListener('submit', handleLogin);
      }

      // Sediakan borang tambah pengguna (jika wujud)
      const addUserFormEl = document.getElementById('addUserForm');
      if(addUserFormEl) {
        addUserFormEl.addEventListener('submit', handleAddUserSubmit);
      }
    });

    // Fungsi untuk menyemak sesi aktif di server
    async function checkLoginStatus() {
      const loginScreen = document.getElementById('loginScreen');
      const appContainer = document.getElementById('appContainer');
      try {
        const response = await fetch('api/check_session.php'); // Panggil API sesi
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        const data = await response.json();

        if (data.loggedIn && data.user) {
          initializeApp(data.user); // Jika log masuk, mulakan aplikasi
        } else {
          showLoginScreen(); // Jika tidak, paparkan skrin log masuk
        }
      } catch (error) {
        console.error('Error checking login status:', error);
        alert(`Tidak dapat mengesahkan status log masuk: ${error.message}. Sila cuba muat semula.`);
        showLoginScreen(); // Paparkan skrin log masuk jika ralat
      }
    }

    // Fungsi untuk mengendalikan penghantaran borang log masuk
    async function handleLogin(event) {
      event.preventDefault();
      const usernameInput = document.getElementById('loginUsername');
      const passwordInput = document.getElementById('loginPassword');
      const loginButton = document.getElementById('loginButton');
      const loginButtonText = document.getElementById('loginButtonText');
      const loginSpinner = document.getElementById('loginSpinner');
      const loginErrorDiv = document.getElementById('loginError');

      loginErrorDiv.textContent = '';
      loginButton.disabled = true;
      loginButtonText.textContent = 'Memproses...';
      loginSpinner.style.display = 'inline-block';

      try {
        const response = await fetch('api/auth.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            username: usernameInput.value,
            password: passwordInput.value
          })
        });

        if (!response.ok) { // Tangani ralat HTTP
            const errorText = await response.text(); // Cuba dapatkan teks ralat
            throw new Error(`HTTP error! status: ${response.status}, Body: ${errorText}`);
        }

        const result = await response.json();

        if (result.success && result.user) {
          initializeApp(result.user); // Mulakan aplikasi dengan data pengguna
        } else {
          loginErrorDiv.textContent = result.error || 'Nama pengguna atau kata laluan tidak sah.';
        }
      } catch (error) {
        console.error('Login error:', error);
        loginErrorDiv.textContent = 'Ralat semasa cuba log masuk. Sila semak konsol untuk butiran.';
      } finally {
        loginButton.disabled = false;
        loginButtonText.textContent = 'Log Masuk';
        loginSpinner.style.display = 'none';
      }
    }

    // Memulakan aplikasi utama selepas log masuk berjaya
    function initializeApp(userData) {
      currentUser = userData; // Simpan data pengguna semasa

      document.getElementById('loginScreen').style.display = 'none';
      document.getElementById('appContainer').classList.remove('hidden');

      // Tetapkan tarikh & masa semasa dalam borang entri
      const now = new Date();
      const dateInput = document.getElementById('currentDate');
      const timeInput = document.getElementById('currentTime');
      if (dateInput) dateInput.value = now.toISOString().split('T')[0];
      if (timeInput) timeInput.value = now.toTimeString().substring(0, 5);

      // Paparkan nama & peranan pengguna
      const userDisplay = document.getElementById('userDisplay');
      if(userDisplay) {
          userDisplay.textContent = `${currentUser.name || currentUser.username} (${currentUser.role === 'admin' ? 'ADMIN' : 'PENGGUNA'})`;
          userDisplay.className = currentUser.role === 'admin'
            ? 'text-sm bg-white text-blue-800 px-2 py-1 rounded font-bold'
            : 'text-sm bg-white text-gray-800 px-2 py-1 rounded';
       }

      // Sediakan fungsi UI lain
      setupTabs();      // PENTING: Panggil setupTabs SELEPAS appContainer dipaparkan
      setupForm();
      setupSearch();
      setupPagination();

      // Muatkan data pelanggan awal SELEPAS UI disedia
      loadData();

      // Kawal ciri admin
      const tabUserManagement = document.getElementById('tabUserManagement');
      const clearAllRecordsButton = document.getElementById('clearAllButton');
      if (currentUser.role === 'admin') {
        if (tabUserManagement) tabUserManagement.classList.remove('hidden');
        if (clearAllRecordsButton) clearAllRecordsButton.classList.remove('hidden');
        loadUsers(); // Muatkan senarai pengguna untuk admin
      } else {
        if (tabUserManagement) tabUserManagement.classList.add('hidden');
        if (clearAllRecordsButton) clearAllRecordsButton.classList.add('hidden');
      }
    }

    // Fungsi log keluar
    async function logout() {
      if (confirm('Adakah anda pasti ingin log keluar?')) {
        try {
          const response = await fetch('api/auth.php?action=logout'); // Panggil API log keluar
          if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
          const result = await response.json();

          if (result.success) {
            currentUser = null;
            showLoginScreen(); // Kembali ke skrin log masuk
          } else {
            alert('Gagal log keluar: ' + (result.error || 'Ralat tidak diketahui'));
          }
        } catch (error) {
          console.error('Logout error:', error);
          alert('Ralat semasa log keluar.');
        }
      }
    }

     // Fungsi untuk memaparkan skrin log masuk dan menyembunyikan aplikasi utama
    function showLoginScreen() {
        currentUser = null; // Pastikan tiada pengguna semasa
        const appContainer = document.getElementById('appContainer');
        const loginScreen = document.getElementById('loginScreen');
        const loginForm = document.getElementById('loginForm');
        const loginError = document.getElementById('loginError');

        if (appContainer) appContainer.classList.add('hidden');
        if (loginScreen) loginScreen.style.display = 'block';
        if (loginForm) loginForm.reset(); // Reset borang log masuk
        if (loginError) loginError.textContent = ''; // Kosongkan mesej ralat log masuk
    }


    // Sediakan fungsi penukaran tab
    function setupTabs() {
      const tabs = {
        tabDataEntry: 'dataEntryContent',
        tabRecords: 'recordsContent',
        tabUserManagement: 'userManagementContent'
        // tabMigrate: 'migrateContent' // Jika anda tambah semula migrasi
      };
      const navContainer = document.getElementById('navigationTabs');
      if (!navContainer) { console.error("Navigation container not found"); return; }

      // Tetapkan event listener pada container navigasi (event delegation)
      navContainer.addEventListener('click', function(event) {
        const targetButton = event.target.closest('button.nav-tab');
        if (!targetButton) return; // Abaikan jika klik bukan pada butang tab

        const tabId = targetButton.id;
        const contentId = tabs[tabId];

        if (contentId && document.getElementById(contentId)) { // Pastikan content ada
            // Kemas kini kelas aktif untuk tab
            navContainer.querySelectorAll('.nav-tab').forEach(tab => tab.classList.remove('active'));
            targetButton.classList.add('active');

            // Sembunyikan semua bahagian kandungan utama
            document.getElementById('dataEntryContent')?.classList.add('hidden');
            document.getElementById('recordsContent')?.classList.add('hidden');
            document.getElementById('userManagementContent')?.classList.add('hidden');
            // document.getElementById('migrateContent')?.classList.add('hidden'); // Jika ada

            // Paparkan kandungan yang dipilih
            document.getElementById(contentId).classList.remove('hidden');

            // Muatkan semula data jika perlu
            if (contentId === 'recordsContent') {
                loadData(); // Muat semula data pelanggan
            } else if (contentId === 'userManagementContent' && currentUser?.role === 'admin') {
                loadUsers(); // Muat semula senarai pengguna
            }
        } else {
            console.error(`Content ID "${contentId}" for tab "${tabId}" not found or invalid.`);
        }
      });

        // Aktifkan kandungan tab lalai semasa permulaan
        // Cari tab yang mempunyai kelas 'active' dalam HTML atau fallback ke data entry
        let defaultActiveTab = navContainer.querySelector('.nav-tab.active');
        if (!defaultActiveTab) {
            defaultActiveTab = document.getElementById('tabDataEntry');
             if (defaultActiveTab) defaultActiveTab.classList.add('active');
        }

        let activeContentFound = false;
        if (defaultActiveTab && tabs[defaultActiveTab.id]) {
            const defaultContentId = tabs[defaultActiveTab.id];
            const defaultContent = document.getElementById(defaultContentId);
            if (defaultContent) {
                // Sembunyikan semua dahulu
                 document.getElementById('dataEntryContent')?.classList.add('hidden');
                 document.getElementById('recordsContent')?.classList.add('hidden');
                 document.getElementById('userManagementContent')?.classList.add('hidden');
                // Paparkan yang default
                defaultContent.classList.remove('hidden');
                activeContentFound = true;
            }
        }
         // Fallback jika tiada tab aktif atau kandungan tidak dijumpai
        if (!activeContentFound) {
             const dataEntryContent = document.getElementById('dataEntryContent');
             if(dataEntryContent) dataEntryContent.classList.remove('hidden');
        }
    }

    // Muat data rekod pelanggan dari server
    async function loadData() {
      if (!currentUser) return;
      const recordsTableBody = document.getElementById('recordsTableBody');
      if (!recordsTableBody) return;
      recordsTableBody.innerHTML = `<tr><td colspan="7" class="px-6 py-10 text-center text-gray-500 animate-pulse">Memuatkan rekod pelanggan...</td></tr>`;

      try {
        const response = await fetch('api/get_data.php');
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        const result = await response.json();

        if (result.success && Array.isArray(result.records)) {
          allRecords = result.records;
          // Pastikan tarikh diset semula ke 1 SEBELUM menapis
          // currentPage = 1; // Reset page number only when loading fresh data? Or maybe just when searching?
          // Untuk sekarang, tapis dahulu, render akan guna currentPage sedia ada
          filterAndRenderRecords(); // Panggil fungsi baru untuk tapis dan render
        } else {
          throw new Error(result.error || 'Gagal memuatkan data pelanggan.');
        }
      } catch (error) {
        console.error('Error loading customer data:', error);
        recordsTableBody.innerHTML = `
          <tr>
            <td colspan="7" class="px-6 py-10 text-center text-red-500">Ralat memuatkan data: ${error.message}</td>
          </tr>`;
        allRecords = [];
        filteredRecords = [];
        renderRecordsTable(); // Paparkan jadual kosong
      }
    }

    // Fungsi baru untuk menapis dan memaparkan rekod
    function filterAndRenderRecords() {
        const searchInput = document.getElementById('searchInput');
        const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';

        if (searchTerm === '') {
          filteredRecords = [...allRecords];
        } else {
          filteredRecords = allRecords.filter(record => {
            return Object.values(record).some(value =>
                String(value).toLowerCase().includes(searchTerm)
            );
          });
        }
        // currentPage = 1; // Reset ke halaman 1 setiap kali carian/filter berubah
        renderRecordsTable(); // Paparkan hasil tapisan
    }


    // Sediakan borang entri data pelanggan
    function setupForm() {
      const form = document.getElementById('dataForm');
      const submitButton = form?.querySelector('button[type="submit"]');
      const submitText = document.getElementById('submit-text');
      const spinner = document.getElementById('spinner');
      if (!form || !submitButton || !submitText || !spinner) return;

      form.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (!currentUser) {
            alert('Sila log masuk untuk menghantar data.');
            return;
        }
        submitButton.disabled = true;
        submitText.style.display = 'none';
        spinner.style.display = 'inline-block';

        try {
          const formData = new FormData(form);
          const entry = {};
          formData.forEach((value, key) => {
            // Ambil nilai tidak kosong sahaja dan trim
            const trimmedValue = typeof value === 'string' ? value.trim() : value;
            if (trimmedValue !== '' && trimmedValue !== null && trimmedValue !== undefined) {
                 entry[key] = trimmedValue;
            }
          });

          // Pastikan medan mandatori ada selepas trim
          if (!entry.tarikh || !entry.masa || !entry.namaPelanggan) {
               throw new Error("Tarikh, Masa, dan Nama Pelanggan adalah mandatori.");
          }


          // Medan createdBy akan ditetapkan oleh server berdasarkan sesi
          const response = await fetch('api/save_data.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(entry)
          });
          if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
          const data = await response.json();

          if (!data.success) throw new Error(data.error || 'Gagal menyimpan data.');

          form.reset(); // Reset borang
          // Tetapkan semula tarikh/masa kepada semasa
          const now = new Date();
          const dateInput = document.getElementById('currentDate');
          const timeInput = document.getElementById('currentTime');
          if (dateInput) dateInput.value = now.toISOString().split('T')[0];
          if (timeInput) timeInput.value = now.toTimeString().substring(0, 5);

          alert(data.message || 'Data berjaya disimpan!');
          // Tak perlu loadData() di sini jika kita tukar tab
          document.getElementById('tabRecords').click(); // Tukar ke tab rekod (ini akan trigger loadData)

        } catch (error) {
          alert('Ralat: ' + error.message);
        } finally {
          submitButton.disabled = false;
          submitText.style.display = 'inline';
          spinner.style.display = 'none';
        }
      });
    }

    // Sediakan fungsi carian
    function setupSearch() {
      const searchInput = document.getElementById('searchInput');
      if (!searchInput) return;

      let searchTimeout = null;
      searchInput.addEventListener('input', () => {
          clearTimeout(searchTimeout);
          searchTimeout = setTimeout(() => {
              currentPage = 1; // Reset ke halaman pertama apabila carian berubah
              filterAndRenderRecords(); // Panggil fungsi tapis dan render
          }, 300);
      });
    }

    // Sediakan butang paginasi
    function setupPagination() {
      const prevButton = document.getElementById('prevPage');
      const nextButton = document.getElementById('nextPage');
      if (!prevButton || !nextButton) return;

      prevButton.addEventListener('click', () => {
        if (currentPage > 1) {
          currentPage--;
          renderRecordsTable(); // Hanya render semula, tak perlu tapis lagi
        }
      });

      nextButton.addEventListener('click', () => {
        const totalPages = Math.ceil(filteredRecords.length / recordsPerPage);
        if (currentPage < totalPages) {
          currentPage++;
          renderRecordsTable(); // Hanya render semula, tak perlu tapis lagi
        }
      });
    }

    // Paparkan jadual rekod pelanggan
    function renderRecordsTable() {
      const tableBody = document.getElementById('recordsTableBody');
      if (!tableBody) return;
      tableBody.innerHTML = ''; // Kosongkan jadual

      const startIndex = (currentPage - 1) * recordsPerPage;
      const endIndex = startIndex + recordsPerPage;
      const paginatedRecords = filteredRecords.slice(startIndex, endIndex);

      if (paginatedRecords.length === 0) {
        tableBody.innerHTML = `
          <tr>
            <td colspan="7" class="px-6 py-10 text-center text-gray-500">
              ${filteredRecords.length !== allRecords.length ? 'Tiada rekod sepadan ditemui.' : 'Tiada rekod pelanggan lagi.'}
            </td>
          </tr>`;
      } else {
          paginatedRecords.forEach((record, indexInPage) => {
            const absoluteIndex = startIndex + indexInPage + 1;
            // Cari indeks asal dalam allRecords berdasarkan ID unik untuk tindakan
            const originalRecordIndex = allRecords.findIndex(r => r.id === record.id);

            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50 transition duration-150 ease-in-out';

            // Tentukan jika pengguna semasa boleh memadam rekod ini (backend akan sahkan)
            const canDelete = currentUser && (currentUser.role === 'admin' ||
                               (record.createdBy && record.createdBy === currentUser.username));

            row.innerHTML = `
              <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">${absoluteIndex}</td>
              <td class="px-4 py-3 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">${record.namaPelanggan || '-'}</div>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <div class="text-sm text-gray-500">${record.namaSyarikat || '-'}</div>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <div class="text-sm text-gray-500">${record.kaedah || '-'}</div>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <div class="text-sm text-gray-500">${record.isu || '-'}</div>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <div class="text-sm text-gray-500">${record.tarikh || ''} ${record.masa || ''}</div>
              </td>
              <td class="px-4 py-3 whitespace-nowrap text-center text-sm font-medium">
                <button type="button" onclick="viewRecordDetail(${originalRecordIndex})" class="text-indigo-600 hover:text-indigo-900 transition duration-150 ease-in-out mr-3" title="Lihat Butiran">Lihat</button>
                ${canDelete ? `<button type="button" onclick="deleteRecord(${originalRecordIndex})" class="text-red-600 hover:text-red-900 transition duration-150 ease-in-out" title="Padam Rekod">Padam</button>` : ''}
              </td>
            `;
            tableBody.appendChild(row);
          });
      }
      updatePaginationInfo(); // Kemas kini maklumat paginasi
    }

    // Kemas kini maklumat paginasi (bilangan rekod, butang)
    function updatePaginationInfo() {
      const totalRecordsDisplay = filteredRecords.length; // Papar jumlah hasil tapisan
      const totalPages = Math.ceil(totalRecordsDisplay / recordsPerPage);
      const startRecord = totalRecordsDisplay > 0 ? (currentPage - 1) * recordsPerPage + 1 : 0;
      const endRecord = Math.min(currentPage * recordsPerPage, totalRecordsDisplay);

      const showingFromEl = document.getElementById('showingFrom');
      const showingToEl = document.getElementById('showingTo');
      const totalRecordsEl = document.getElementById('totalRecords');
      if (showingFromEl) showingFromEl.textContent = startRecord;
      if (showingToEl) showingToEl.textContent = endRecord;
      if (totalRecordsEl) totalRecordsEl.textContent = totalRecordsDisplay;

      const prevButton = document.getElementById('prevPage');
      const nextButton = document.getElementById('nextPage');
      if (!prevButton || !nextButton) return;

      prevButton.disabled = currentPage <= 1;
      nextButton.disabled = currentPage >= totalPages || totalPages === 0;
    }

    // Paparkan modal butiran rekod
    function viewRecordDetail(originalIndex) {
        if (originalIndex === -1 || originalIndex >= allRecords.length || !allRecords[originalIndex]) {
            alert('Rekod tidak dijumpai atau indeks tidak sah.');
            return;
        }
        const record = allRecords[originalIndex];
        const modalContainer = document.getElementById('recordModalContainer');
        if (!modalContainer) return;

        // Struktur HTML untuk modal
        let detailHTML = `
        <div id="recordDetailModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center p-4 z-50 transition-opacity duration-300" aria-labelledby="modal-title-detail" role="dialog" aria-modal="true">
            <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-3xl transform transition-all duration-300 ease-out" onclick="event.stopPropagation();">
                <div class="flex justify-between items-center mb-4 pb-3 border-b">
                    <h3 class="text-lg font-semibold text-gray-900" id="modal-title-detail">Butiran Rekod #${record.id || 'N/A'}</h3>
                     <button type="button" onclick="closeModal('recordDetailModal')" class="text-gray-400 hover:text-gray-600">
                         <span class="sr-only">Tutup</span>
                         <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                     </button>
                 </div>
                 <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 modal-content pr-2">`; // Kelas modal-content ditambah di sini

        // Susunan dan label medan yang lebih baik
        const keyOrder = ['id','tarikh', 'masa', 'namaPelanggan', 'namaSyarikat', 'kaedah', 'telefon', 'emel', 'pegawai', 'perkara', 'isu', 'pertanyaan', 'jawapan', 'createdBy', 'createdByName', 'created_at'];
        const displayLabels = {
            id: 'ID Rekod', tarikh: 'Tarikh', masa: 'Masa', namaPelanggan: 'Nama Pelanggan', namaSyarikat: 'Nama Syarikat', kaedah: 'Kaedah',
            telefon: 'No. Telefon', emel: 'Emel', pegawai: 'Pegawai Bertugas', perkara: 'Perkara', isu: 'Isu',
            pertanyaan: 'Pertanyaan', jawapan: 'Jawapan/Tindakan', createdBy: 'Dicipta Oleh (ID Pengguna)',
            createdByName: 'Dicipta Oleh (Nama)', created_at: 'Masa Dicipta'
        };

        // Fungsi untuk memaparkan medan
        const renderField = (key, value) => {
            const displayKey = displayLabels[key] || key.replace(/([A-Z])/g, ' $1').replace(/^./, str => str.toUpperCase());
            // Guna pre-wrap untuk textarea atau medan panjang, yang lain biasa
            const textStyle = (key === 'pertanyaan' || key === 'jawapan') ? 'white-space: pre-wrap;' : '';
            return `
                <div class="mb-1 break-words">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">${displayKey}</p>
                    <p class="mt-1 text-sm text-gray-900" style="${textStyle}">${value}</p>
                </div>`;
        };

        // Paparkan medan mengikut susunan
        keyOrder.forEach(key => {
            if (record.hasOwnProperty(key) && (record[key] !== null && record[key] !== undefined && String(record[key]).trim() !== '')) {
                detailHTML += renderField(key, record[key]);
            }
        });

        // Paparkan medan lain jika ada
        Object.keys(record).forEach(key => {
            if (!keyOrder.includes(key) && record.hasOwnProperty(key) && (record[key] !== null && record[key] !== undefined && String(record[key]).trim() !== '')) {
                detailHTML += renderField(key, record[key]);
            }
        });


        detailHTML += `
                </div> <div class="mt-6 flex justify-end border-t pt-4">
                    <button type="button" onclick="closeModal('recordDetailModal')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition duration-150 ease-in-out text-sm">
                    Tutup
                    </button>
                </div>
            </div> </div> `;

        modalContainer.innerHTML = detailHTML; // Letak modal dalam container
        const modalElement = document.getElementById('recordDetailModal');
        if (modalElement) {
             // Tambah event listener untuk tutup modal jika klik di luar kandungan
             modalElement.addEventListener('click', function(e) {
                if (e.target === this) closeModal('recordDetailModal');
             });
        }
        document.body.style.overflow = 'hidden'; // Elak background scroll
    }

    // Tutup modal
    function closeModal(modalId) {
      const modal = document.getElementById(modalId);
      if (modal) {
          modal.remove(); // Buang modal dari DOM
          // Pulihkan scroll hanya jika tiada modal lain yang masih terbuka
          if (!document.querySelector('[id$="Modal"]:not(.hidden)')) {
                document.body.style.overflow = 'auto';
          }
      }
       // Kosongkan container jika ia khusus untuk satu modal
      if (modalId === 'recordDetailModal') {
           const modalContainer = document.getElementById('recordModalContainer');
           if(modalContainer) modalContainer.innerHTML = '';
      }
    }


    // Padam rekod pelanggan
    async function deleteRecord(originalIndex) {
      if (!currentUser) { alert('Sila log masuk.'); return; }
       if (originalIndex === -1 || originalIndex >= allRecords.length || !allRecords[originalIndex]) {
            alert('Rekod tidak dijumpai untuk dipadam atau indeks tidak sah.');
            return;
      }
      const recordToDelete = allRecords[originalIndex];

       // Semak kebenaran di frontend (backend akan sahkan lagi)
       const canDelete = currentUser.role === 'admin' || (recordToDelete.createdBy && recordToDelete.createdBy === currentUser.username);
       if (!canDelete) {
            alert('Anda tidak mempunyai kebenaran untuk memadam rekod ini.');
            return;
       }

      if (!confirm(`Adakah anda pasti ingin memadam rekod #${recordToDelete.id} (${recordToDelete.namaPelanggan})? Tindakan ini tidak boleh dibatalkan.`)) return;

      try {
        const response = await fetch('api/delete_data.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id: recordToDelete.id }) // Hantar ID rekod
        });
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        const data = await response.json();
        if (!data.success) throw new Error(data.error || 'Gagal memadam rekod.');

        alert(data.message || 'Rekod berjaya dipadam.');
        // Muat semula data untuk mencerminkan perubahan
        await loadData(); // Memuat semula akan memaparkan semula jadual dan paginasi

      } catch (error) {
        alert('Ralat semasa memadam: ' + error.message);
      }
    }

    // Kosongkan semua data pelanggan (admin sahaja)
    async function clearAllData() {
      if (!currentUser || currentUser.role !== 'admin') {
        alert('Anda tidak mempunyai kebenaran untuk melakukan tindakan ini.');
        return;
      }
      if (!confirm('AMARAN: Adakah anda pasti ingin mengosongkan SEMUA data pelanggan? Tindakan ini TIDAK BOLEH DIBATALKAN.')) return;

      try {
        const response = await fetch('api/clear_data.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ confirm: true })
        });
         if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        const data = await response.json();
        if (!data.success) throw new Error(data.error || 'Gagal mengosongkan data.');

        alert(data.message || 'Semua data pelanggan telah dikosongkan.');
        currentPage = 1; // Reset ke halaman pertama
        loadData(); // Muat semula untuk tunjuk jadual kosong

      } catch (error) {
        alert('Ralat semasa mengosongkan data: ' + error.message);
      }
    }

    // Muat turun data sebagai fail Excel
    function downloadExcel() {
        if (allRecords.length === 0) {
            alert('Tiada data untuk dimuat turun.');
            return;
        }
        // Eksport rekod yang ditapis jika ada tapisan aktif, jika tidak eksport semua
        const dataToExport = filteredRecords.length > 0 && filteredRecords.length < allRecords.length ? filteredRecords : allRecords;
        if (dataToExport.length === 0) {
            alert('Tiada data (selepas penapisan) untuk dimuat turun.');
            return;
        }
        console.log(`Mengeksport ${dataToExport.length} rekod...`);

        // Peta tajuk lajur untuk Excel
        const excelHeaderMap = {
            id: 'ID', tarikh: 'Tarikh', masa: 'Masa', namaPelanggan: 'Nama Pelanggan',
            namaSyarikat: 'Nama Syarikat', kaedah: 'Kaedah', telefon: 'No. Telefon', emel: 'Emel',
            pegawai: 'Pegawai Bertugas', perkara: 'Perkara', isu: 'Isu', pertanyaan: 'Pertanyaan',
            jawapan: 'Jawapan/Tindakan', createdBy: 'Dicipta Oleh (ID)', createdByName: 'Dicipta Oleh (Nama)',
            created_at: 'Tarikh Cipta Rekod' // Label untuk created_at
        };
        // Susunan lajur yang diingini dalam Excel
        const desiredOrder = ['id', 'tarikh', 'masa', 'namaPelanggan', 'namaSyarikat', 'kaedah', 'telefon', 'emel', 'pegawai', 'perkara', 'isu', 'pertanyaan', 'jawapan', 'createdBy', 'createdByName', 'created_at'];

        // Format data: pilih lajur mengikut susunan dan guna tajuk dari peta
        const formattedData = dataToExport.map(entry => {
            const formattedEntry = {};
            for (const key of desiredOrder) {
                formattedEntry[excelHeaderMap[key] || key] = entry[key] ?? ""; // Guna ?? "" untuk nilai null/undefined
            }
            return formattedEntry;
        });

        try {
            // Cipta header untuk SheetJS berdasarkan susunan dan peta
            const excelHeaders = desiredOrder.map(key => excelHeaderMap[key] || key);

            // Cipta worksheet dan workbook
            const worksheet = XLSX.utils.json_to_sheet(formattedData, { header: excelHeaders });

            // (Pilihan) Cuba laraskan lebar lajur secara automatik (anggaran)
            const columnWidths = excelHeaders.map(header => ({ wch: Math.max(header.length, 10) })); // Min width 10
            formattedData.forEach(row => {
                excelHeaders.forEach((header, index) => {
                    const cellLength = String(row[header]).length;
                    if (columnWidths[index].wch < cellLength) {
                        columnWidths[index].wch = cellLength;
                    }
                });
            });
             columnWidths.forEach(w => { if(w.wch > 50) w.wch = 50; }); // Lebar maksimum
            worksheet['!cols'] = columnWidths;

            const workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, 'Data Pelanggan');

            // Jana dan muat turun fail Excel
            const dateStr = new Date().toISOString().split('T')[0];
            XLSX.writeFile(workbook, `Data_Pelanggan_${dateStr}.xlsx`);
        } catch (err) {
            console.error("Error generating Excel file:", err);
            alert("Gagal menjana fail Excel.");
        }
    }


    // --- Fungsi Pengurusan Pengguna (Menggunakan API Backend) ---
    async function loadUsers() {
      if (!currentUser || currentUser.role !== 'admin') return;

      const tableBody = document.getElementById('usersTableBody');
      if (!tableBody) return;
      tableBody.innerHTML = `<tr><td colspan="4" class="px-6 py-10 text-center text-gray-500 animate-pulse">Memuatkan senarai pengguna...</td></tr>`;

      try {
        const response = await fetch('api/users_crud.php?action=get_all'); // Guna users_crud.php
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        const result = await response.json();

        if (result.success && Array.isArray(result.users)) {
          tableBody.innerHTML = '';
          if (result.users.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="4" class="px-6 py-10 text-center text-gray-500">Tiada pengguna ditemui.</td></tr>`;
            return;
          }

          result.users.forEach(userData => {
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50 transition duration-150 ease-in-out';
            // Admin tidak boleh padam diri sendiri atau pengguna 'admin' lalai.
            const canDeleteUser = currentUser.id !== userData.id && userData.username !== 'admin';

            row.innerHTML = `
              <td class="px-6 py-3 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">${userData.username}</div>
              </td>
              <td class="px-6 py-3 whitespace-nowrap">
                <div class="text-sm text-gray-500">${userData.name || '-'}</div>
              </td>
              <td class="px-6 py-3 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                  ${userData.role === 'admin' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'}">
                  ${userData.role === 'admin' ? 'Administrator' : 'Pengguna Biasa'}
                </span>
              </td>
              <td class="px-6 py-3 whitespace-nowrap text-center text-sm font-medium">
                ${canDeleteUser ? `<button type="button" onclick="deleteUser(${userData.id}, '${userData.username}')" class="text-red-600 hover:text-red-900 transition duration-150 ease-in-out" title="Padam Pengguna">Padam</button>` : ''}
              </td>
            `;
            tableBody.appendChild(row);
          });
        } else {
          throw new Error(result.error || 'Gagal memuatkan senarai pengguna.');
        }
      } catch (error) {
        console.error('Error loading users:', error);
        tableBody.innerHTML = `<tr><td colspan="4" class="px-6 py-10 text-center text-red-500">Ralat memuatkan pengguna: ${error.message}</td></tr>`;
      }
    }

    // Paparkan modal tambah pengguna
    function showAddUserModal() {
      if (!currentUser || currentUser.role !== 'admin') return;
      const addUserFormEl = document.getElementById('addUserForm');
      const addUserErrorEl = document.getElementById('addUserError');
      if (addUserFormEl) addUserFormEl.reset();
      if (addUserErrorEl) addUserErrorEl.textContent = ''; // Kosongkan ralat lama
      const addUserModalEl = document.getElementById('addUserModal');
      if (addUserModalEl) addUserModalEl.classList.remove('hidden');
    }

    // Sembunyikan modal tambah pengguna
    function hideAddUserModal() {
      const addUserModalEl = document.getElementById('addUserModal');
      if(addUserModalEl) addUserModalEl.classList.add('hidden');
    }

    // Kendalikan penghantaran borang tambah pengguna baru
    async function handleAddUserSubmit(event) {
        event.preventDefault(); // Halang penghantaran borang biasa
        if (!currentUser || currentUser.role !== 'admin') return;

        const fullNameInput = document.getElementById('addFullName');
        const usernameInput = document.getElementById('addUsername');
        const passwordInput = document.getElementById('addPassword');
        const roleInput = document.getElementById('addRole');
        const addUserErrorDiv = document.getElementById('addUserError');
        const submitButton = document.getElementById('addUserSubmitButton');
        const submitText = document.getElementById('addUserSubmitText');
        const spinner = document.getElementById('addUserSpinner');

        addUserErrorDiv.textContent = ''; // Kosongkan ralat lama

        // Validasi Frontend
        if (!fullNameInput.value.trim() || !usernameInput.value.trim() || !passwordInput.value || !roleInput.value) {
            addUserErrorDiv.textContent = 'Sila lengkapkan semua medan bertanda *.';
            return;
        }
        if (passwordInput.value.length < 6) {
            addUserErrorDiv.textContent = 'Kata laluan mesti sekurang-kurangnya 6 aksara.';
            return;
        }

        submitButton.disabled = true;
        submitText.style.display = 'none';
        spinner.style.display = 'inline-block';

        try {
            const userData = {
                name: fullNameInput.value.trim(),
                username: usernameInput.value.trim(),
                password: passwordInput.value, // Hantar kata laluan asal
                role: roleInput.value
            };

            const response = await fetch('api/users_crud.php?action=add', { // Panggil API
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(userData)
            });
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            const result = await response.json();

            if (result.success) {
                alert(result.message || 'Pengguna baru berjaya ditambah!');
                hideAddUserModal();
                loadUsers(); // Muat semula senarai pengguna
            } else {
                throw new Error(result.error || 'Gagal menambah pengguna baru.');
            }
        } catch (error) {
            console.error('Error adding new user:', error);
            addUserErrorDiv.textContent = 'Ralat: ' + error.message;
        } finally {
             submitButton.disabled = false;
             submitText.style.display = 'inline';
             spinner.style.display = 'none';
        }
    }


    // Padam pengguna (menggunakan API)
    async function deleteUser(userId, usernameToDelete) {
      if (!currentUser || currentUser.role !== 'admin') {
          alert('Anda tidak mempunyai kebenaran.'); return;
      }
      // Perbandingan ID untuk self-delete
      if (String(userId) === String(currentUser.id)) {
        alert('Anda tidak boleh memadam akaun sendiri.');
        return;
      }
      // Pencegahan tambahan untuk username 'admin'
      if (usernameToDelete === 'admin') {
        alert("Akaun 'admin' utama tidak boleh dipadam.");
        return;
      }

      if (confirm(`Adakah anda pasti ingin memadam pengguna '${usernameToDelete}' (ID: ${userId})? Tindakan ini tidak boleh dibatalkan.`)) {
        try {
          const response = await fetch('api/users_crud.php', {
            method: 'POST', // Atau DELETE, pastikan API menyokongnya
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'delete', id: userId }) // Hantar ID pengguna
          });
           if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
          const result = await response.json();

          if (result.success) {
            alert(result.message || `Pengguna ${usernameToDelete} berjaya dipadam.`);
            loadUsers(); // Muat semula senarai pengguna
          } else {
            throw new Error(result.error || `Gagal memadam pengguna ${usernameToDelete}.`);
          }
        } catch (error) {
          console.error('Error deleting user:', error);
          alert('Ralat: ' + error.message);
        }
      }
    }

    </script>
</body>
</html>