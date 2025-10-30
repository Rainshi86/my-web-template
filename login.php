    <?php
    include '../connect.php';
    session_start(); // เริ่มต้นเซสชัน

    $_SESSION['user_id'] = $row['user_id']; // เพิ่มโค้ดนี้ในส่วนของการล็อกอิน
    var_dump($_SESSION); // ดูข้อมูลในเซสชัน

    // ตั้งค่าการเชื่อมต่อฐานข้อมูล MySQL
    $servername = "localhost";
    $username = "root";  // ชื่อผู้ใช้ฐานข้อมูล
    $password = "";      // รหัสผ่านฐานข้อมูล
    $dbname = "rentroomdb";  // ชื่อฐานข้อมูล

    // สร้างการเชื่อมต่อ
    $conn = new mysqli($servername, $username, $password, $dbname);

    // ตรวจสอบการเชื่อมต่อ
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // ตรวจสอบว่ามีการส่งข้อมูลมาจากฟอร์มหรือไม่
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        // ตรวจสอบให้แน่ใจว่าค่าที่กรอกมาไม่ว่าง
        if (empty($username) || empty($password)) {
            echo "กรุณากรอกข้อมูลให้ครบถ้วน.";
        } else {
            // เตรียมคำสั่ง SQL สำหรับการค้นหาผู้ใช้ในฐานข้อมูล
            $sql = "SELECT * FROM users WHERE username = ?";
            $stmt = $conn->prepare($sql);

            // ผูกค่าพารามิเตอร์
            $stmt->bind_param("s", $username);

            // ดำเนินการและตรวจสอบผลลัพธ์
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // ดึงข้อมูลผู้ใช้
                $row = $result->fetch_assoc();

                // ตรวจสอบรหัสผ่าน (รหัสผ่านในฐานข้อมูลต้องเป็นแบบแฮช)
                if (password_verify($password, $row['password'])) {
                    // หากรหัสผ่านถูกต้อง
                    $_SESSION['user'] = $row['username'];
                    $_SESSION['role'] = $row['role'];
                    // หลังจากที่ผู้ใช้ล็อกอินสำเร็จ
                    $_SESSION['user_id'] = $row['user_id'];  // ตั้งค่า user_id ในเซสชัน
                    $_SESSION['user'] = $row['username'];
                    $_SESSION['role'] = $row['role'];

                    // เปลี่ยนเส้นทางตามบทบาท
                    if ($row['role'] === 'admin') {
                        header('Location: index.php');
                    } elseif ($row['role'] === 'employee') {
                        header('Location: index.php');
                    }elseif ($row['role'] === 'customer') {
                        header('Location: index.php');
                    } else {
                        header('Location: index.php');
                    }
                    exit();
                } else {
                    // หากรหัสผ่านไม่ถูกต้อง
                    echo "รหัสผ่านไม่ถูกต้อง.";
                }
            } else {
                // หากไม่พบผู้ใช้ในฐานข้อมูล
                echo "ไม่พบผู้ใช้นี้.";
            }

            // ปิด statement
            $stmt->close();
        }
    }

    // ปิดการเชื่อมต่อ
    $conn->close();
    ?>
