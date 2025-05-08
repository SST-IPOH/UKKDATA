<?php
require 'config.php'; // Mengambil $conn dan $database

// Semak jika sambungan berjaya dibuat dalam config.php
if (!isset($conn) || $conn->connect_error) {
     die("Sambungan pangkalan data gagal. Semak config.php.");
}

echo "Menggunakan pangkalan data: " . htmlspecialchars($database) . "<br>";

// --- Cipta Jadual Users ---
$sql_users = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,         -- Pastikan lajur adalah 'name'
    role VARCHAR(20) NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

if ($conn->query($sql_users) === TRUE) {
    echo "Jadual 'users' berjaya dicipta atau sudah wujud.<br>";
} else {
    echo "Ralat mencipta jadual 'users': " . htmlspecialchars($conn->error) . "<br>";
}

// --- Cipta Jadual Customers ---
$sql_customers = "CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tarikh DATE DEFAULT NULL,
    masa TIME DEFAULT NULL,
    namaPelanggan VARCHAR(100) NOT NULL,
    namaSyarikat VARCHAR(100),
    kaedah VARCHAR(50),
    telefon VARCHAR(20),
    emel VARCHAR(100),
    pegawai VARCHAR(100),
    perkara VARCHAR(100),
    isu VARCHAR(100),
    pertanyaan TEXT,
    jawapan TEXT,
    createdBy VARCHAR(50),
    createdByName VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP -- Lajur ini baik untuk ada
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

if ($conn->query($sql_customers) === TRUE) {
    echo "Jadual 'customers' berjaya dicipta atau sudah wujud.<br>";
} else {
    echo "Ralat mencipta jadual 'customers': " . htmlspecialchars($conn->error) . "<br>";
}

// --- Memasukkan/Memastikan Pengguna Admin Lalai wujud dengan Kata Laluan Hash ---
$admin_username = 'admin';
$admin_password_plain = 'admin30100'; // Kata laluan asal
$admin_name = 'Administrator';       // Nama untuk admin (selari dengan JS asal)
$admin_role = 'admin';

// 1. Semak jika admin sudah wujud
$stmt_check = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
if ($stmt_check === false) {
     echo "Ralat menyediakan penyata semak admin: " . htmlspecialchars($conn->error) . "<br>";
} else {
    $stmt_check->bind_param("s", $admin_username);
    if ($stmt_check->execute()) {
        $result_check = $stmt_check->get_result();
        if ($result_check->num_rows === 0) {
            // 2a. Jika tidak wujud, HASH kata laluan dan MASUKKAN
            $hashed_admin_password = password_hash($admin_password_plain, PASSWORD_DEFAULT); // Hashing!

            $stmt_insert = $conn->prepare("INSERT INTO users (username, password, name, role) VALUES (?, ?, ?, ?)");
            if($stmt_insert === false) {
                 echo "Ralat menyediakan penyata masukkan admin: " . htmlspecialchars($conn->error) . "<br>";
            } else {
                // Bind pembolehubah yang telah di-hash
                $stmt_insert->bind_param("ssss", $admin_username, $hashed_admin_password, $admin_name, $admin_role);
                if ($stmt_insert->execute()) {
                    echo "Pengguna admin lalai ('$admin_username') berjaya dimasukkan dengan kata laluan yang di-hash.<br>";
                } else {
                    echo "Ralat memasukkan pengguna admin lalai: " . htmlspecialchars($stmt_insert->error) . "<br>";
                }
                $stmt_insert->close();
            }
        } else {
            // 2b. Jika admin sudah wujud, SEMAK jika kata laluan perlu dikemas kini (jika ia masih plaintext)
            $existing_user = $result_check->fetch_assoc();
            $existing_hash = $existing_user['password'];
            // Cuba sahkan kata laluan plain text dengan hash sedia ada
            // password_needs_rehash() juga boleh digunakan jika hash lama digunakan
            if (!password_verify($admin_password_plain, $existing_hash) || password_needs_rehash($existing_hash, PASSWORD_DEFAULT)) {
                // Jika verify GAGAL atau hash perlu di rehash, kemas kini hash.
                 $log_msg = !password_verify($admin_password_plain, $existing_hash)
                    ? "Kata laluan admin sedia ada tidak sah atau mungkin teks biasa."
                    : "Hash kata laluan admin sedia ada perlu dikemaskini.";
                echo $log_msg . " Mengemas kini hash...<br>";

                $new_hashed_password = password_hash($admin_password_plain, PASSWORD_DEFAULT);
                $stmt_update = $conn->prepare("UPDATE users SET password = ?, name = ?, role = ? WHERE username = ?");
                 if($stmt_update === false) {
                    echo "Ralat menyediakan penyata kemas kini admin: " . htmlspecialchars($conn->error) . "<br>";
                 } else {
                    $stmt_update->bind_param("ssss", $new_hashed_password, $admin_name, $admin_role, $admin_username);
                    if ($stmt_update->execute()) {
                        echo "Kata laluan pengguna admin ('$admin_username') berjaya dikemas kini kepada hash baharu.<br>";
                    } else {
                        echo "Ralat mengemas kini kata laluan admin: " . htmlspecialchars($stmt_update->error) . "<br>";
                    }
                    $stmt_update->close();
                 }
            } else {
                echo "Pengguna admin lalai ('$admin_username') sudah wujud dengan kata laluan hash yang sah.<br>";
            }
        }
    } else {
         echo "Ralat execute semak admin: " . htmlspecialchars($stmt_check->error) . "<br>";
    }
    $stmt_check->close();
}

echo "Persediaan pangkalan data selesai.<br>";
$conn->close();
?>