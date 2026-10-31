<?php
require_once __DIR__ . '/../includes/init.php';


if (isset($_SESSION['admin'])) {
    header('Location: index.php');
    exit();
}

$old_username = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $old_username = htmlspecialchars($username, ENT_QUOTES);
    if ($username !== '' && $password !== '') {
        $user = DB::selectOne('SELECT * FROM quan_tri_vien WHERE username = :u', ['u' => $username]);
        if ($user) {
            $stored = $user['password'] ?? '';
            $ok = false;

            if ($stored !== '' && password_verify($password, $stored)) {
                $ok = true;
                if (password_needs_rehash($stored, PASSWORD_DEFAULT)) {
                    $newHash = password_hash($password, PASSWORD_DEFAULT);
                    DB::update('quan_tri_vien', ['password' => $newHash], 'username = :u', ['u' => $username]);
                }
            }

            if ($ok) {
                $_SESSION['user_role'] = 'admin';
                $_SESSION['admin'] = $username;
                $_SESSION['fullname'] = $user['hoten'];
                unset($_SESSION['err_admin']);
                // update last login time
                DB::execute('UPDATE quan_tri_vien SET last_login = NOW() WHERE username = :u', ['u' => $username]);
                header('Location: ' . INDEX_URL . 'admin');
                exit();
            } else {
                $_SESSION['err_admin'] = 'Tên đăng nhập hoặc mật khẩu không đúng!';
            }
        } else {
            $_SESSION['err_admin'] = 'Tên đăng nhập hoặc mật khẩu không đúng!';
        }
    } else {
        $_SESSION['err_admin'] = 'Vui lòng nhập tên đăng nhập và mật khẩu.';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập Quản trị viên | Hệ thống quản lý sinh viên</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>⚙️</text></svg>">
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="bg-white shadow-2xl rounded-2xl w-full max-w-md p-8 space-y-6">

        <div class="text-center">
            <div class="flex justify-center mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0a9.003 9.003 0 005.196 5.196c.921.3.921 1.603 0 1.902a9.003 9.003 0 00-5.196 5.196c-.3.921-1.603.921-1.902 0a9.003 9.003 0 00-5.196-5.196c-.921-.3-.921-1.603 0-1.902a9.003 9.003 0 005.196-5.196z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <h1 class="text-2xl font-semibold text-blue-700">Đăng nhập Quản trị viên</h1>
            <p class="text-gray-500 text-sm mt-1">Hệ thống quản lý sinh viên</p>
        </div>
        <form action="login.php" method="POST" class="space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700">Tên đăng nhập</label>
                <input type="text" name="username" required
                    class="mt-1 w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    placeholder="Nhập tên đăng nhập" value="<?php echo $old_username; ?>">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Mật khẩu</label>
                <input type="password" name="password" required
                    class="mt-1 w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    placeholder="Nhập mật khẩu">
            </div>

            <?php if (isset($_SESSION['err_admin'])): ?>
            <div class="text-sm text-center text-red-600">
                <?php echo htmlspecialchars($_SESSION['err_admin']); unset($_SESSION['err_admin']); ?>
            </div>
            <?php endif; ?>

            <button type="submit"
                class="w-full bg-blue-700 hover:bg-blue-800 text-white py-2 rounded-lg font-semibold transition">
                Đăng nhập
            </button>
        </form>

        <div class="text-center text-sm text-gray-600">
            <p>Quên mật khẩu? <a href="#" class="text-blue-600 hover:underline">Liên hệ quản trị hệ thống</a></p>
        </div>


        <div class="text-center text-gray-400 text-xs mt-4">
            © 2025 - Hệ thống quản lý sinh viên | Trang quản trị
        </div>

    </div>

</body>

</html>