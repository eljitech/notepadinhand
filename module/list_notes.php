<?php
    // 03022021.2 : API stable with authentication

    // Tag: 150121.2

    include '../config/connection.php';

    if($_SERVER['REQUEST_METHOD'] == 'POST') {

        // Ketika membuat variable boleh bebas, sesuai ketentuan
        // Jangan spasi, diawali dengan angka, simbol atau yang lainnya
        $username = $_POST['username'];
        $password = $_POST['password'];
        $hash_access = $_POST['hash_useraccess'];
        $level_user = $_POST['level'];

        // Deklarasikan dulu Query dalam Variable
        // $variable = mysqli_query($_variable_authentikasi, query)
        $querysearch_user = mysqli_query($_AUTH, "SELECT * FROM tbl_user WHERE username = '$username' AND password = '$password' AND hash_useraccess = '$hash_access' AND level = '$level_user'");

        $response = array();

        // mysqli_num_rows = berfungsi untuk membaca total data yang diatmpilkan ketika query di execute
        $execute_querylogin = mysqli_num_rows($querysearch_user);

        if ($execute_querylogin > 0) {
            $categoryof_notes = $_POST['categoryof_notes'];

            // Deklarasi Query SQL
            $gettotal_notes = "SELECT COUNT(*) 'total' FROM tbl_notes JOIN tbl_user ON tbl_user.id_user=tbl_notes.id_user WHERE tbl_notes.categoryof_notes = '$categoryof_notes' AND tbl_user.username = '$username'  ORDER BY date_created ASC";
            // Eksekusi Query
            $execute_querytotalnotes = mysqli_query($_AUTH, $gettotal_notes);
            // Ambil Data yang sudah di request tadi oleh query yang barusan dijalankan
            $result_totaldatanotes = mysqli_fetch_assoc($execute_querytotalnotes);
            // mysqli_fetch_assoc ini berfungsi untuk menfetching / mengambil data secara satu persatu

            $query_listnotes = "SELECT tbl_notes.id_notes, tbl_notes.titleof_notes, tbl_notes.contentof_notes, tbl_notes.date_created, tbl_notes.id_user, tbl_user.username, tbl_notes.categoryof_notes FROM tbl_notes JOIN tbl_user ON tbl_user.id_user=tbl_notes.id_user WHERE categoryof_notes = '$categoryof_notes' AND tbl_user.username = '$username'  ORDER BY date_created ASC;";
            $execute_querylistnotes = mysqli_query($_AUTH, $query_listnotes);
            
            if ($result_totaldatanotes['total'] == 0) {
                // Informasi bagian apabila database notes tidak ditemukan

                $response["message"] = trim("Data $categoryof_notes tidak ditemukan didatabase / " . $result_totaldatanotes['total'] . " data");
                $response["code"] = 401; // Tidak ditemukan
                $response["status"] = false;
                $response["category"] = $categoryof_notes;
                $response["totalnotes"] = $result_totaldatanotes['total'];

                echo json_encode($response);
            } else {
                // Informasi list notes yang berhasil di temukan

                $response["message"] = trim("Data $categoryof_notes berhasil ditemukan didatabase / " . $result_totaldatanotes['total'] . " data");
                $response["code"] = 200; // Berhasil ditemukan
                $response["status"] = true;
                $response["category"] = $categoryof_notes;
                $response["totalnotes"] = $result_totaldatanotes['total']; 
                $response["listnotes"] = array(); 

                // Looping while untuk menampilkan list data notes
                while($row = mysqli_fetch_array($execute_querylistnotes)) {
                    $data = array();

                    $data['id_notes'] = $row['id_notes'];
                    $data['titleof_notes'] = $row['titleof_notes'];
                    $data['contentof_notes'] = $row['contentof_notes'];
                    $data['date_created'] = $row['date_created'];
                    $data['id_user'] = $row['id_user'];
                    $data['username'] = $row['username'];
                    $data['categoryof_notes'] = $row['categoryof_notes'];

                    array_push($response['listnotes'], $data);
                }

                echo json_encode($response);
            }

        } else {
            $response["message"] = trim("Autentikasi gagal, Cek kembali user credential anda.");
            $response["code"] = 401;
            $response["status"] = false;
            echo json_encode($response);
        }
    } else {
        $response["message"] = trim("Oops! Sory, Request API ini membutuhkan parameter!.");
        $response["code"] = 400;
        $response["status"] = false;

        echo json_encode($response);
    }
?>