<?php
/* ============================================================
   物业管理台账 PHP 版 · 集中配置
   部署前必改：DB_PASS（数据库密码）、INSTALL_KEY（安装口令）
   本文件已被 .htaccess 禁止直接访问，请勿泄露到公开仓库。
   ============================================================ */

/* ---- MySQL（infinityfree 免费主机） ---- */
define('DB_HOST', 'sql210.infinityfree.com');
define('DB_USER', 'if0_43184524');
define('DB_PASS', '__DB_PASS__');        /* ← 占位符：部署时替换为真实密码，代码库中不得存真实密码 */
define('DB_NAME', 'if0_43184524');       /* infinityfree 默认库名 = 账号名，若不对请按控制台修改 */

/* ---- 安装口令：访问 install.php 时须带 ?key=此值；部署前改成自己的随机串 ---- */
define('INSTALL_KEY', 'pl7f3a9c2e5b8d4k1');

/* ---- PDO 连接（utf8mb4；关闭模拟预处理，数字列按原生类型返回） ---- */
function db(){
  static $pdo = null;
  if ($pdo === null){
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
      PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_EMULATE_PREPARES   => false,
      PDO::ATTR_STRINGIFY_FETCHES  => false,
    ]);
  }
  return $pdo;
}

/* ---- 会话安全参数 ---- */
function pl_session_start(){
  if (session_status() === PHP_SESSION_ACTIVE) return;
  session_name('PLSESSID');
  ini_set('session.use_strict_mode', '1');
  ini_set('session.use_only_cookies', '1');
  $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
  session_set_cookie_params([
    'lifetime' => 0, 'path' => '/', 'domain' => '',
    'secure' => $secure, 'httponly' => true, 'samesite' => 'Lax',
  ]);
  session_start();
}
