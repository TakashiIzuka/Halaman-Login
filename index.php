<!DOCTYPE html>
<html>
<head>
    <title>Registrasi</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;  /* Ubah dari height:100vh ke min-height untuk fleksibilitas */
            background: #f4f4f4;
            margin: 0;
            padding: 20px;  /* Tambahan padding untuk spacing di edge layar */
            box-sizing: border-box;
        }

        /* Wrapper untuk form agar lebih centered di layar lebar */
        .form-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            max-width: 400px;  /* Batasi lebar agar tidak terlalu lebar di desktop */
        }

        fieldset {
            width: 320px;
            max-height: 480px;       
            overflow-y: auto;     
            background: #fff;
            padding: 14px;        
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin: 0 auto;  /* Tambahan: Margin auto untuk centering horizontal ekstra */
        }
        p {display:flex;align-items:center;margin-bottom:8px;}  
        label {width:130px;text-align:right;margin-right:8px;font-size:14px;}
        input[type="text"],input[type="email"],input[type="password"],input[type="date"],select,textarea {
            flex:1;
            padding:5px;
            font-size:14px;
            box-sizing:border-box;
        }
        textarea {height:50px;}
        legend {text-align:center;font-weight:bold;margin-bottom:6px;font-size:16px;}
        .button-row {display:flex;justify-content:space-between;margin-top:10px;}
        input[type="submit"],input[type="reset"] {
            padding:6px 12px;
            border:none;
            border-radius:4px;
            cursor:pointer;
            font-weight:bold;
            font-size:14px;
        }
        input[type="submit"] {background-color:#4CAF50;color:white;}
        input[type="reset"] {background-color:#f44336;color:white;}
        input[type="submit"]:hover {background-color:#45a049;}
        input[type="reset"]:hover {background-color:#e53935;}
        .checkbox-group {
            flex:1;
            display:flex;
            flex-direction:column;
            gap:3px;
        }
        .checkbox-group label {
            width:auto;
            text-align:left;
            margin:0;
            font-size:14px;
        }
        /* Pesan sukses/error */
        .message { 
            padding: 10px; 
            margin: 10px 0 5px 0; 
            border-radius: 4px; 
            text-align: center; 
            font-size: 14px;
            width: 100%;
            box-sizing: border-box;
        }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>

    <div class="form-wrapper">  <!-- Wrapper baru untuk centering tambahan -->
        <form id="regForm" action="register.php" method="POST" enctype="multipart/form-data">
            <fieldset>
            <legend>Registrasi</legend>

            <p><label>Nama:</label><input type="text" name="nama" placeholder="Masukkan Nama" required></p>
            <p><label>Username:</label><input type="text" id="username" name="username" placeholder="Masukkan Username" required /></p>
            <p><label>Email:</label><input type="email" name="email" placeholder="Masukkan Email" required /></p>
            <p><label>Password:</label><input type="password" id="password" name="password" placeholder="Masukkan Password" required /></p>
            <p>
                <label>Jenis kelamin:</label>
                <span>
                    <input type="radio" name="jenis_kelamin" value="laki-laki" required /> Laki-laki
                    <input type="radio" name="jenis_kelamin" value="perempuan" required /> Perempuan
                </span>
            </p>
            <p><label>Tanggal Lahir:</label><input type="date" name="bday" required></p>
            <p>
                <label>Agama:</label>
                <select name="agama" required>
                    <option value="">-- Pilih Agama --</option>
                    <option value="islam">Islam</option>
                    <option value="kristen">Kristen</option>
                    <option value="hindu">Hindu</option>
                    <option value="budha">Budha</option>
                </select>
            </p>
            <p><label>Biografi:</label><textarea name="biografi" placeholder="Masukkan Biografi"></textarea></p>
            <p><label>File koding anda:</label><input type="file" name="filecode" accept=".txt,.js,.py,.html,.css" required></p>
            <p><label>Foto anda:</label><input type="file" name="filephoto" accept="image/*" required></p>
            <p><label>Warna favorit:</label><input type="color" value="#ff0000" name="favcolor" required></p>

            <p>
                <label></label>
                <span class="checkbox-group">
                    <label><input type="checkbox" id="rememberMe"> Remember me</label>
                    <label><input type="checkbox" id="ageCheck" required> Saya harus berumur 18 tahun</label>
                </span>
            </p>

            <?php
            // Tampilkan pesan dari redirect PHP - Di bawah checkbox
            if (isset($_GET['status'])) {
                if ($_GET['status'] == 'success') {
                    echo '<div class="message success">Registrasi berhasil! Silakan login.</div>';
                } elseif ($_GET['status'] == 'error') {
                    $msg = isset($_GET['msg']) ? urldecode($_GET['msg']) : 'Registrasi gagal. Silakan coba lagi.';
                    echo '<div class="message error">' . htmlspecialchars($msg) . '</div>';
                }
            }
            ?>

            <div class="button-row">
                <input type="reset" value="Reset">
                <input type="submit" name="submit" value="Daftar">
            </div>

            </fieldset>
        </form>
    </div>  <!-- Akhir wrapper -->

    <script>
        const form = document.getElementById("regForm");
        const rememberMe = document.getElementById("rememberMe");
        const ageCheck = document.getElementById("ageCheck");
        const username = document.getElementById("username");
        const password = document.getElementById("password");

        // Muat data remember me
        window.onload = () => {
            if(localStorage.getItem("remember") === "true"){
                username.value = localStorage.getItem("username") || "";
                password.value = localStorage.getItem("password") || "";
                rememberMe.checked = true;
            }
        };

        // Validasi & simpan data
        form.addEventListener("submit", function(e){
            if(!ageCheck.checked){
                e.preventDefault();
                alert("kamu harus berumur 18+ untuk melanjutkan");
                return false;
            }

            if(rememberMe.checked){
                localStorage.setItem("remember", "true");
                localStorage.setItem("username", username.value);
                localStorage.setItem("password", password.value);
            } else {
                localStorage.removeItem("remember");
                localStorage.removeItem("username");
                localStorage.removeItem("password");
            }
        });
    </script>
</body>
</html>