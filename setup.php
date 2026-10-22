<?php
/* ============================================================
   物业管理台账 PHP 版 · 网页安装向导（setup.php）
   用法：把整个目录上传到网站根目录后，浏览器访问 /setup.php
   流程：填写 MySQL 信息 → 测试连接 → 自动生成 config.php →
        执行 schema.sql 建表 → 创建管理员账号
   安装成功后本文件自动锁定（防重装清库），可手动删除。
   ============================================================ */
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
header('Content-Type: text/html; charset=utf-8');
session_start();

$CONFIG_FILE = __DIR__ . '/config.php';
$SCHEMA_FILE = __DIR__ . '/schema.sql';
$LOCKED = file_exists($CONFIG_FILE) && @filesize($CONFIG_FILE) > 50;

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function page($title, $body, $ok = true){
  echo '<!DOCTYPE html><html lang="zh-CN"><head><meta charset="UTF-8">'
   . '<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">'
   . '<title>' . h($title) . '</title><style>'
   . '*{box-sizing:border-box;margin:0;padding:0}'
   . 'body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI","PingFang SC","Microsoft YaHei",sans-serif;'
   . 'background:radial-gradient(1200px 600px at 70% -10%,rgba(217,181,125,.12),transparent),#0A0908;'
   . 'color:#F5EDDD;min-height:100vh;display:grid;place-items:center;padding:24px}'
   . '.box{max-width:600px;width:100%;background:rgba(24,20,14,.94);border:1px solid rgba(217,181,125,.28);'
   . 'border-radius:20px;padding:30px 32px;box-shadow:0 14px 44px -14px rgba(0,0,0,.6)}'
   . 'h1{font-size:21px;color:#E8CF96;margin-bottom:6px}.sub{color:#B7A488;font-size:13px;margin-bottom:22px;line-height:1.7}'
   . '.label{display:block;font-size:13px;color:#D9B57D;margin:14px 0 6px}'
   . '.input{width:100%;font-size:15px;padding:11px 13px;border-radius:11px;border:1px solid rgba(217,181,125,.3);'
   . 'background:rgba(255,250,235,.06);color:#F5EDDD;outline:none}'
   . '.input:focus{border-color:#D9B57D}.hint{font-size:12px;color:#7E7060;margin-top:5px;line-height:1.6}'
   . '.row{display:flex;gap:12px}.row>div{flex:1}'
   . '.btn{width:100%;margin-top:24px;font-size:16px;padding:13px;border-radius:12px;border:none;cursor:pointer;font-weight:700;'
   . 'background:linear-gradient(135deg,#C9A05E,#E8CF96);color:#241B0C}'
   . '.err{background:rgba(248,113,113,.1);border:1px solid rgba(248,113,113,.35);color:#fca5a5;border-radius:12px;'
   . 'padding:13px 15px;font-size:13.5px;line-height:1.7;margin:16px 0;word-break:break-all}'
   . '.ok{background:rgba(52,211,153,.1);border:1px solid rgba(52,211,153,.35);color:#6ee7b7;border-radius:12px;'
   . 'padding:13px 15px;font-size:13.5px;line-height:1.8;margin:16px 0}'
   . '.code{background:rgba(217,181,125,.12);padding:2px 7px;border-radius:6px;font-size:12.5px}'
   . 'ol{margin:10px 0 0 20px;line-height:2;font-size:14px;color:#E8D9B8}'
   . 'a{color:#D9B57D}</style></head><body><div class="box"><h1>' . h($title) . '</h1>' . $body . '</div></body></html>';
  exit;
}

/* ---------- 已安装锁定：硬拒绝，无任何输出 ---------- */
/* 安全策略：config.php 存在即视为已安装，直接 403 拒绝，不回显任何 HTML。
   这是比"提示锁定"更严格的防线——即使 .htaccess 被绕过也无法利用。
   真要重装：先用 FTP 删 config.php + setup.php 两个文件，再上传新版 setup.php。 */
if ($LOCKED){
  http_response_code(403);
  header('Content-Type: text/plain');
  header('X-Robots-Tag: noindex,nofollow');
  header('Cache-Control: no-store');
  echo 'Installation already completed.';
  exit;
}

/* ---------- 安装执行 ---------- */
$err = '';
$f = [
  'host' => trim($_POST['db_host'] ?? 'sql210.infinityfree.com'),
  'name' => trim($_POST['db_name'] ?? ''),
  'user' => trim($_POST['db_user'] ?? 'if0_43184524'),
  'pass' => (string)($_POST['db_pass'] ?? ''),
  'admin_user' => trim($_POST['admin_user'] ?? 'admin'),
  'admin_pass' => (string)($_POST['admin_pass'] ?? ''),
];

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
  if ($f['host']==='' || $f['name']==='' || $f['user']==='' || $f['pass']==='')
    $err = '数据库主机、数据库名、用户名、密码均为必填项。';
  elseif ($f['admin_user']==='' || strlen($f['admin_pass']) < 6)
    $err = '管理员用户名不能为空，管理员密码至少 6 位。';
  elseif (!is_writable(__DIR__))
    $err = '网站目录不可写，无法生成 config.php。请在 FTP 里把站点目录权限设为 755，或联系主机商。';

  if ($err === ''){
    /* 1) 测试数据库连接 */
    try {
      $dsn = 'mysql:host=' . $f['host'] . ';dbname=' . $f['name'] . ';charset=utf8mb4';
      $pdo = new PDO($dsn, $f['user'], $f['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_TIMEOUT => 12,
      ]);
      $pdo->exec("SET NAMES utf8mb4");
    } catch (Throwable $e){
      $msg = $e->getMessage();
      $hint = '';
      if (preg_match('/1045|Access denied/i', $msg))
        $hint = '<br><b>常见原因</b>：MySQL 密码与控制面板密码不是同一个；数据库需先在 cPanel → MySQL Databases 里创建，'
              . '库名形如 <span class="code">if0_43184524_xxx</span>，建库时设置的密码才是数据库密码。';
      if (preg_match('/1049|Unknown database/i', $msg))
        $hint = '<br><b>数据库不存在</b>：请先到 cPanel → MySQL Databases 创建该数据库。';
      if (preg_match('/2002|2003|getaddrinfo|Unknown host/i', $msg))
        $hint = '<br><b>主机名无法连接</b>：请核对 cPanel 上显示的 MySQL Hostname（可能不是 sql210.infinityfree.com）。';
      page('数据库连接失败',
        '<div class="err">' . h($msg) . $hint . '</div>'
        . '<p class="sub">请修正后重试，已填写的其它信息会保留。</p>'
        . form_html($f));
    }

    /* 2) 生成 config.php（密码只写入服务器本地文件） */
    $installKey = bin2hex(random_bytes(16));
    $cfg  = "<?php\n";
    $cfg .= "/* 物业管理台账 PHP 版 · 数据库与会话配置（由 setup.php 于 " . date('Y-m-d H:i') . " 自动生成） */\n";
    $cfg .= "define('DB_HOST', " . var_export($f['host'], true) . ");\n";
    $cfg .= "define('DB_USER', " . var_export($f['user'], true) . ");\n";
    $cfg .= "define('DB_PASS', " . var_export($f['pass'], true) . ");\n";
    $cfg .= "define('DB_NAME', " . var_export($f['name'], true) . ");\n";
    $cfg .= "define('INSTALL_KEY', " . var_export($installKey, true) . ");\n\n";
    $cfg .= "function db(){\n  static \$pdo = null;\n  if (\$pdo === null){\n";
    $cfg .= "    \$dsn = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4';\n";
    $cfg .= "    \$pdo = new PDO(\$dsn, DB_USER, DB_PASS, [\n";
    $cfg .= "      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,\n";
    $cfg .= "      PDO::ATTR_EMULATE_PREPARES => false,\n";
    $cfg .= "      PDO::ATTR_STRINGIFY_FETCHES => false,\n";
    $cfg .= "    ]);\n  }\n  return \$pdo;\n}\n\n";
    $cfg .= "function pl_session_start(){\n";
    $cfg .= "  if (session_status() === PHP_SESSION_ACTIVE) return;\n";
    $cfg .= "  session_name('PLSESSID');\n";
    $cfg .= "  ini_set('session.use_strict_mode', '1');\n";
    $cfg .= "  ini_set('session.use_only_cookies', '1');\n";
    $cfg .= "  \$secure = !empty(\$_SERVER['HTTPS']) && \$_SERVER['HTTPS'] !== 'off';\n";
    $cfg .= "  session_set_cookie_params(['lifetime'=>0,'path'=>'/','domain'=>'','secure'=>\$secure,'httponly'=>true,'samesite'=>'Lax']);\n";
    $cfg .= "  session_start();\n}\n";

    if (@file_put_contents($CONFIG_FILE, $cfg) === false){
      page('写入配置失败',
        '<div class="err">无法在服务器生成 <span class="code">config.php</span>，请检查站点目录写入权限（FTP 设为 755）。</div>'
        . form_html($f));
    }

    /* 3) 执行建表。
       注意：只能按 CR/LF 切行，不能用 \R —— \R 会把 UTF-8 汉字里
       的 0x85 字节（如「仅/免/入」）误判为 NEL 换行，截断注释行。 */
    $schema = @file_get_contents($SCHEMA_FILE);
    if ($schema === false)
      page('缺少 schema.sql', '<div class="err">找不到 schema.sql，请确认已随安装包一起上传。</div>' . form_html($f));
    $stmts = [];
    foreach (explode(';', $schema) as $chunk){
      $lines = [];
      foreach (preg_split('/\r\n|\r|\n/', $chunk) as $line){
        if (preg_match('/^\s*--/', $line) || trim($line) === '') continue;
        $lines[] = $line;
      }
      $sql = trim(implode("\n", $lines));
      if ($sql !== '') $stmts[] = $sql;
    }
    $created = 0;
    try {
      foreach ($stmts as $sql){ $pdo->exec($sql); $created++; }
    } catch (Throwable $e){
      @unlink($CONFIG_FILE);
      page('建表失败', '<div class="err">' . h($e->getMessage()) . '</div>'
        . '<p class="sub">已回滚：config.php 已删除，修正后可重新安装。已执行 ' . $created . ' / ' . count($stmts) . ' 条。</p>'
        . form_html($f));
    }

    /* 4) 创建管理员（users 为空时） */
    $seeded = false;
    try {
      $cnt = (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
      if ($cnt === 0){
        $st = $pdo->prepare('INSERT INTO users (userName,displayName,isAdmin,isActive,passwordHash,features,createdAt) VALUES (?,?,1,1,?,?,?)');
        $st->execute([$f['admin_user'], '管理员', password_hash($f['admin_pass'], PASSWORD_DEFAULT), '', date('Y-m-d H:i')]);
        $seeded = true;
      }
    } catch (Throwable $e){
      page('管理员账号创建失败', '<div class="err">' . h($e->getMessage()) . '</div>'
        . '<p class="sub">表已建好，可在数据库中手动补建账号后访问。</p>');
    }

    /* 5) 成功页 */
    page('安装成功',
      '<div class="ok">✅ 数据库连接成功，已执行 <b>' . count($stmts) . '</b> 条建表语句。<br>'
      . ($seeded
          ? '管理员账号已创建：<b>' . h($f['admin_user']) . '</b>（密码为你刚设置的）'
          : 'users 表已有账号，未重复创建。')
      . '</div>'
      . '<p class="sub">配置文件 <span class="code">config.php</span> 已生成，安装向导已自动锁定。</p>'
      . '<ol>'
      . '<li>建议用 FTP 删除 <span class="code">setup.php</span>（不删也无法再次安装）</li>'
      . '<li>点击下方按钮进入登录页，建议登录后在「设置」里修改密码</li>'
      . '</ol>'
      . '<a class="btn" style="display:flex;align-items:center;justify-content:center;text-decoration:none" href="index.php">进入物业管理台账</a>');
  }

  if ($err !== '') page('信息有误', '<div class="err">' . h($err) . '</div>' . form_html($f));
}

/* ---------- 首次访问：安装表单 ---------- */
page('物业管理台账 · 安装向导',
  '<p class="sub">请填写 MySQL 数据库连接信息（在 cPanel 控制面板 → '
  . '<b>MySQL Databases</b> 页面查看；若还没有数据库，请先在该页面创建）。</p>'
  . form_html($f));

function form_html($f){
  return '<form method="post" action="setup.php">'
  . '<label class="label">MySQL 主机名（MySQL Hostname）</label>'
  . '<input class="input" name="db_host" value="' . h($f['host'] ?: 'sql210.infinityfree.com') . '" placeholder="sql210.infinityfree.com">'
  . '<div class="hint">以 cPanel → MySQL Databases 页面顶部显示的主机名为准。</div>'
  . '<div class="row"><div>'
  . '<label class="label">数据库名（Database Name）</label>'
  . '<input class="input" name="db_name" value="' . h($f['name']) . '" placeholder="if0_43184524_xxx">'
  . '</div><div>'
  . '<label class="label">数据库用户名</label>'
  . '<input class="input" name="db_user" value="' . h($f['user'] ?: 'if0_43184524') . '" placeholder="if0_43184524">'
  . '</div></div>'
  . '<label class="label">数据库密码（建库时设置的密码）</label>'
  . '<input class="input" type="password" name="db_pass" value="' . h($f['pass']) . '" placeholder="创建数据库时设置的密码">'
  . '<div class="hint">注意：它可能与控制面板/FTP 登录密码不同。</div>'
  . '<div class="row"><div>'
  . '<label class="label">管理员用户名</label>'
  . '<input class="input" name="admin_user" value="' . h($f['admin_user'] ?: 'admin') . '">'
  . '</div><div>'
  . '<label class="label">管理员登录密码（至少 6 位）</label>'
  . '<input class="input" type="text" name="admin_pass" value="' . h($f['admin_pass']) . '" placeholder="自定义，务必记住">'
  . '</div></div>'
  . '<button class="btn" type="submit">测试连接并开始安装</button>'
  . '</form>';
}
