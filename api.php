<?php
/* ============================================================
   物业管理台账 PHP 版 · 后端单文件路由
   动作：login / logout / session / put / delete / list / get / clear
        kv_get / kv_set / bulk_put / clear_all / export / import
        change_password / user_create
   响应：{ok:true,data:...} / {ok:false,error:'...'}
   权限：所有写操作要求已登录；users 管理动作、export/import/clear_all 仅总管理员；
        kv 供「设置」功能页读写（登录用户可用），clear/delete 整表等管理动作仅管理员。
   密码：password_hash()/password_verify()（bcrypt）；
        兼容 HTML 版备份的 pbkdf2$/sha256js$ 旧格式，登录校验成功后自动升级为 bcrypt。
   ============================================================ */
require __DIR__ . '/config.php';
pl_session_start();
header('Content-Type: application/json; charset=utf-8');

function out($data = null){ echo json_encode(['ok'=>true,'data'=>$data], JSON_UNESCAPED_UNICODE); exit; }
function fail($msg){ echo json_encode(['ok'=>false,'error'=>$msg], JSON_UNESCAPED_UNICODE); exit; }

$action = isset($_GET['action']) ? (string)$_GET['action'] : '';
$raw = file_get_contents('php://input');
$in = json_decode($raw === false ? '' : $raw, true);
if (!is_array($in)) $in = [];

/* ---------- store 元数据：表名 + 列 + 类型（列名与 JS 对象属性一致） ---------- */
$META = [
  'properties' => ['table'=>'properties','cols'=>['name','type','address','managementContact','utilityContact','note','createdAt'],
    'int'=>['type'],'num'=>[],'bool'=>[]],
  'rooms' => ['table'=>'rooms','cols'=>['propertyId','name','area','defaultRent','note','isArchived','createdAt'],
    'int'=>['propertyId'],'num'=>['area','defaultRent'],'bool'=>['isArchived']],
  'tenants' => ['table'=>'tenants','cols'=>['name','phone','idCard','note','createdAt'],
    'int'=>[],'num'=>[],'bool'=>[]],
  'leases' => ['table'=>'leases','cols'=>['roomId','tenantId','startDate','endDate','rentAmount','houseDeposit','utilityDeposit','paymentDay','paymentCycle','escMode','escValue','note','status','createdAt'],
    'int'=>['roomId','tenantId','paymentDay','paymentCycle','escMode','status'],'num'=>['rentAmount','houseDeposit','utilityDeposit','escValue'],'bool'=>[]],
  'bills' => ['table'=>'bills','cols'=>['leaseId','roomId','tenantId','periodStart','periodEnd','dueDate','rentAmount','totalAmount','paidAmount','status','paidAt','note','isManual','createdAt'],
    'int'=>['leaseId','roomId','tenantId','status'],'num'=>['rentAmount','totalAmount','paidAmount'],'bool'=>['isManual']],
  'billItems' => ['table'=>'bill_items','cols'=>['billId','name','amount','remark'],
    'int'=>['billId'],'num'=>['amount'],'bool'=>[]],
  'payments' => ['table'=>'payments','cols'=>['billId','amount','paidAt','method','note','createdAt'],
    'int'=>['billId','method'],'num'=>['amount'],'bool'=>[]],
  'knowledge' => ['table'=>'knowledge','cols'=>['title','content','category','leaseId','createdAt','updatedAt'],
    'int'=>['category','leaseId'],'num'=>[],'bool'=>[]],
  'users' => ['table'=>'users','cols'=>['userName','displayName','isAdmin','isActive','passwordHash','features','lastLoginAt','createdAt'],
    'int'=>[],'num'=>[],'bool'=>['isAdmin','isActive']],
];

$ALL_FEATS = ['bills','reports','leases','rooms','tenants','properties','settings'];

/* ---------- 类型转换 ---------- */
function type_map($meta){
  $t = [];
  foreach ($meta['int']  as $c) $t[$c] = 'int';
  foreach ($meta['num']  as $c) $t[$c] = 'num';
  foreach ($meta['bool'] as $c) $t[$c] = 'bool';
  return $t;
}
function cast_write($v, $type){
  if ($v === null) return null;
  if ($type === 'int')  return is_array($v) ? null : (int)$v;
  if ($type === 'num')  return is_array($v) ? null : (float)$v;
  if ($type === 'bool') return $v ? 1 : 0;
  if (is_bool($v)) return $v ? 1 : 0;
  return is_scalar($v) ? (string)$v : json_encode($v, JSON_UNESCAPED_UNICODE);
}
function row_out($meta, $row, $stripHash = false){
  $t = type_map($meta);
  $o = ['id' => (int)$row['id']];
  foreach ($meta['cols'] as $c){
    if ($stripHash && $c === 'passwordHash') continue;
    $v = array_key_exists($c, $row) ? $row[$c] : null;
    if ($v === null){ $o[$c] = null; continue; }
    $ty = isset($t[$c]) ? $t[$c] : 'str';
    $o[$c] = $ty === 'int' ? (int)$v : ($ty === 'num' ? (float)$v : ($ty === 'bool' ? (bool)$v : $v));
  }
  return $o;
}

/* ---------- 密码校验（bcrypt + HTML 版旧 PBKDF2 格式兼容） ---------- */
function is_legacy_hash($stored){ return (bool)preg_match('/^(pbkdf2|sha256js)\$/', (string)$stored); }
function verify_password($password, $stored){
  $stored = (string)$stored;
  if (preg_match('/^(pbkdf2|sha256js)\$(\d+)\$([^$]+)\$([^$]+)$/', $stored, $m)){
    $salt = base64_decode($m[3], true);
    $want = base64_decode($m[4], true);
    if ($salt === false || $want === false || $want === '') return false;
    $iter = max(1, (int)$m[2]);
    $got = hash_pbkdf2('sha256', $password, $salt, $iter, 32, true);
    return hash_equals($want, $got);
  }
  return password_verify($password, $stored);
}

/* ---------- 会话 ---------- */
function norm_features_arr($csv){
  global $ALL_FEATS;
  $set = array_map('trim', explode(',', (string)$csv));
  return array_values(array_filter($ALL_FEATS, function($k) use ($set){ return in_array($k, $set, true); }));
}
function norm_features_csv($csv){ return implode(',', norm_features_arr($csv)); }
function session_obj($row){
  return [
    'userId' => (int)$row['id'], 'userName' => $row['userName'], 'displayName' => $row['displayName'],
    'isAdmin' => (bool)$row['isAdmin'], 'features' => norm_features_arr($row['features']),
  ];
}
function cur_user_row(){
  static $checked = false, $row = null;
  if ($checked) return $row;
  $checked = true; $row = null;
  $uid = isset($_SESSION['userId']) ? (int)$_SESSION['userId'] : 0;
  if ($uid > 0){
    $st = db()->prepare('SELECT * FROM users WHERE id = ? AND isActive <> 0 LIMIT 1');
    $st->execute([$uid]);
    $r = $st->fetch(PDO::FETCH_ASSOC);
    if ($r) $row = $r; else unset($_SESSION['userId']);
  }
  return $row;
}
function require_login(){ $u = cur_user_row(); if (!$u) fail('未登录或会话已过期，请重新登录。'); return $u; }
function require_admin(){ $u = require_login(); if (!(int)$u['isAdmin']) fail('仅总管理员可执行此操作。'); return $u; }
function store_meta($in){
  global $META;
  $store = isset($in['store']) ? (string)$in['store'] : '';
  if (!isset($META[$store])) fail('未知数据表：' . $store);
  return $META[$store];
}

/* ---------- 通用写：put（有 id → UPDATE；无 id → INSERT 返回新 id） ---------- */
function put_values($meta, $rec, $forUpdate){
  $t = type_map($meta);
  $cols = $meta['cols'];
  /* users 更新永不触碰 passwordHash（改密走专用接口），防止前端整对象回写清空哈希 */
  if ($meta['table'] === 'users' && $forUpdate) $cols = array_values(array_diff($cols, ['passwordHash']));
  $vals = [];
  foreach ($cols as $c){
    $v = array_key_exists($c, $rec) ? $rec[$c] : null;
    $vals[$c] = cast_write($v, isset($t[$c]) ? $t[$c] : 'str');
  }
  return [$cols, $vals];
}
function put_record($meta, $rec){
  $pdo = db();
  $id = isset($rec['id']) && (int)$rec['id'] > 0 ? (int)$rec['id'] : 0;
  list($cols, $vals) = put_values($meta, $rec, $id > 0);
  if ($id > 0){
    $set = implode(', ', array_map(function($c){ return "`$c` = ?"; }, $cols));
    $args = array_values($vals); $args[] = $id;
    $pdo->prepare("UPDATE `{$meta['table']}` SET $set WHERE id = ?")->execute($args);
    return $id;
  }
  $colSql = '`' . implode('`,`', $cols) . '`';
  $ph = rtrim(str_repeat('?,', count($cols)), ',');
  $pdo->prepare("INSERT INTO `{$meta['table']}` ($colSql) VALUES ($ph)")->execute(array_values($vals));
  return (int)$pdo->lastInsertId();
}
/* 导入用：保留原 id 的 INSERT */
function insert_with_id($meta, $rec){
  $pdo = db();
  list($cols, $vals) = put_values($meta, $rec, false);
  $id = isset($rec['id']) && (int)$rec['id'] > 0 ? (int)$rec['id'] : null;
  if ($id !== null){
    $colSql = '`id`,`' . implode('`,`', $cols) . '`';
    $ph = rtrim(str_repeat('?,', count($cols) + 1), ',');
    $args = array_merge([$id], array_values($vals));
  } else {
    $colSql = '`' . implode('`,`', $cols) . '`';
    $ph = rtrim(str_repeat('?,', count($cols)), ',');
    $args = array_values($vals);
  }
  $pdo->prepare("INSERT INTO `{$meta['table']}` ($colSql) VALUES ($ph)")->execute($args);
  return $id !== null ? $id : (int)$pdo->lastInsertId();
}

/* ---------- kv ---------- */
function kv_read($key){
  $st = db()->prepare('SELECT `value` FROM kv WHERE `key` = ? LIMIT 1');
  $st->execute([$key]);
  $r = $st->fetch(PDO::FETCH_ASSOC);
  if (!$r) return ['found'=>false,'value'=>null];
  $v = json_decode((string)$r['value'], true);
  if ($v === null && trim((string)$r['value']) !== 'null') $v = $r['value']; /* 非 JSON 时原样返回 */
  return ['found'=>true,'value'=>$v];
}
function kv_write($key, $value){
  $st = db()->prepare('INSERT INTO kv (`key`,`value`) VALUES (?,?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)');
  $st->execute([(string)$key, json_encode($value, JSON_UNESCAPED_UNICODE)]);
}

/* ---------- 快照（导出/导入共用结构，与 HTML 版备份 JSON 互通） ---------- */
function build_snapshot(){
  global $META;
  $snap = ['app'=>'property-ledger-html','version'=>'1.4','exportedAt'=>date('c'),'data'=>[]];
  foreach ($META as $store => $m){
    $rows = db()->query("SELECT * FROM `{$m['table']}` ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
    $snap['data'][$store] = array_map(function($r) use ($m){ return row_out($m, $r, false); }, $rows);
  }
  $settings = [];
  foreach (db()->query('SELECT `key`,`value` FROM kv')->fetchAll(PDO::FETCH_ASSOC) as $r){
    $settings[$r['key']] = kv_decode_raw($r['value']);
  }
  $snap['data']['settings'] = $settings;
  return $snap;
}
function kv_decode_raw($raw){
  $v = json_decode((string)$raw, true);
  if ($v === null && trim((string)$raw) !== 'null') return $raw;
  return $v;
}
function do_import($snap){
  $d = is_array($snap) && isset($snap['data']) ? $snap['data'] : null;
  if (!$d || !isset($d['users']) || !is_array($d['users']) || count($d['users']) === 0
      || !isset($d['properties']) || !is_array($d['properties'])) fail('这不是有效的台账备份文件。');
  global $META;
  $pdo = db();
  $pdo->beginTransaction();
  try {
    foreach ($META as $m) $pdo->exec("DELETE FROM `{$m['table']}`");
    $pdo->exec('DELETE FROM kv');
    $counts = [];
    foreach ($META as $store => $m){
      $rows = isset($d[$store]) && is_array($d[$store]) ? $d[$store] : [];
      foreach ($rows as $rec){
        if (!is_array($rec)) continue;
        if ($store === 'users' && (!isset($rec['passwordHash']) || $rec['passwordHash'] === '')){
          $rec['passwordHash'] = password_hash('admin123', PASSWORD_DEFAULT); /* 防止导入后无法登录 */
        }
        insert_with_id($m, $rec);
      }
      $counts[$store] = count($rows);
    }
    if (isset($d['settings']) && is_array($d['settings'])){
      foreach ($d['settings'] as $k => $v) kv_write($k, $v);
    }
    $pdo->commit();
    return $counts;
  } catch (Exception $e){ $pdo->rollBack(); throw $e; }
}

/* ============================ 路由 ============================ */
try {
  switch ($action) {

    case 'login': {
      $u = trim((string)($in['userName'] ?? '')); $p = (string)($in['password'] ?? '');
      if ($u === '' || $p === '') fail('请输入登录名和密码。');
      $st = db()->prepare('SELECT * FROM users WHERE LOWER(userName) = LOWER(?) LIMIT 1');
      $st->execute([$u]);
      $row = $st->fetch(PDO::FETCH_ASSOC);
      if (!$row || !(int)$row['isActive']) fail('账号不存在或已停用。');
      if (!verify_password($p, $row['passwordHash'])) fail('密码错误，请重试。');
      if (is_legacy_hash($row['passwordHash'])){   /* 旧 PBKDF2 哈希自动升级为 bcrypt */
        db()->prepare('UPDATE users SET passwordHash = ? WHERE id = ?')
            ->execute([password_hash($p, PASSWORD_DEFAULT), (int)$row['id']]);
      }
      session_regenerate_id(true);
      $_SESSION['userId'] = (int)$row['id'];
      db()->prepare('UPDATE users SET lastLoginAt = ? WHERE id = ?')
          ->execute([date('Y-m-d H:i'), (int)$row['id']]);
      out(session_obj($row));
    }

    case 'logout': {
      $_SESSION = [];
      if (ini_get('session.use_cookies')){
        $ck = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $ck['path'], $ck['domain'], $ck['secure'], $ck['httponly']);
      }
      session_destroy();
      out(null);
    }

    case 'session': {
      $u = cur_user_row();
      out($u ? session_obj($u) : null);
    }

    case 'list': {
      require_login();
      $store = (string)($in['store'] ?? '');
      if ($store === 'kv'){
        $rows = db()->query('SELECT `key`,`value` FROM kv')->fetchAll(PDO::FETCH_ASSOC);
        $out = array_map(function($r){ return ['key'=>$r['key'], 'value'=>kv_decode_raw($r['value'])]; }, $rows);
        out($out);
      }
      $m = store_meta($in);
      $rows = db()->query("SELECT * FROM `{$m['table']}` ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
      $strip = ($m['table'] === 'users');
      out(array_map(function($r) use ($m, $strip){ return row_out($m, $r, $strip); }, $rows));
    }

    case 'get': {
      require_login();
      $m = store_meta($in);
      $id = (int)($in['id'] ?? 0);
      $st = db()->prepare("SELECT * FROM `{$m['table']}` WHERE id = ? LIMIT 1");
      $st->execute([$id]);
      $row = $st->fetch(PDO::FETCH_ASSOC);
      out($row ? row_out($m, $row, $m['table'] === 'users') : null);
    }

    case 'put': {
      $store = (string)($in['store'] ?? '');
      if ($store === 'users') require_admin(); else require_login();
      if ($store === 'kv'){
        $rec = $in['record'] ?? null;
        if (!is_array($rec) || !isset($rec['key'])) fail('kv 记录须包含 key。');
        kv_write($rec['key'], array_key_exists('value', $rec) ? $rec['value'] : null);
        out($rec['key']);
      }
      $m = store_meta(['store'=>$store]);
      $rec = $in['record'] ?? null;
      if (!is_array($rec)) fail('缺少记录数据。');
      out(put_record($m, $rec));
    }

    case 'delete': {
      $store = (string)($in['store'] ?? '');
      if ($store === 'users') require_admin(); else require_login();
      if ($store === 'kv'){
        db()->prepare('DELETE FROM kv WHERE `key` = ?')->execute([(string)($in['id'] ?? '')]);
        out(null);
      }
      $m = store_meta(['store'=>$store]);
      db()->prepare("DELETE FROM `{$m['table']}` WHERE id = ?")->execute([(int)($in['id'] ?? 0)]);
      out(null);
    }

    case 'clear': {
      $store = (string)($in['store'] ?? '');
      if ($store === 'users' || $store === 'kv') require_admin(); else require_login();
      if ($store === 'kv'){ db()->exec('DELETE FROM kv'); out(null); }
      $m = store_meta(['store'=>$store]);
      db()->exec("DELETE FROM `{$m['table']}`");
      out(null);
    }

    case 'kv_get': {
      require_login();
      out(kv_read((string)($in['key'] ?? '')));
    }

    case 'kv_set': {
      require_login();
      kv_write((string)($in['key'] ?? ''), array_key_exists('value', $in) ? $in['value'] : null);
      out(null);
    }

    case 'bulk_put': {
      $store = (string)($in['store'] ?? '');
      if ($store === 'users') require_admin(); else require_login();
      $recs = isset($in['records']) && is_array($in['records']) ? $in['records'] : [];
      $n = 0;
      if ($store === 'kv'){
        foreach ($recs as $r){ if (is_array($r) && isset($r['key'])){ kv_write($r['key'], $r['value'] ?? null); $n++; } }
        out($n);
      }
      $m = store_meta(['store'=>$store]);
      foreach ($recs as $r){ if (is_array($r)){ put_record($m, $r); $n++; } }
      out($n);
    }

    case 'clear_all': {
      require_admin();
      global $META;
      foreach ($META as $m) db()->exec("DELETE FROM `{$m['table']}`");
      db()->exec('DELETE FROM kv');
      out(null);
    }

    case 'export': {
      require_admin();
      out(build_snapshot());
    }

    case 'import': {
      require_admin();
      $counts = do_import($in['snapshot'] ?? null);
      out($counts);
    }

    case 'change_password': {
      $me = require_login();
      $old = (string)($in['oldPassword'] ?? ''); $new = (string)($in['newPassword'] ?? '');
      if ($old === '' || $new === '') fail('请填写原密码与新密码。');
      if (strlen($new) < 6) fail('新密码至少 6 位。');
      if (!verify_password($old, $me['passwordHash'])) fail('原密码不正确。');
      db()->prepare('UPDATE users SET passwordHash = ? WHERE id = ?')
          ->execute([password_hash($new, PASSWORD_DEFAULT), (int)$me['id']]);
      out(null);
    }

    case 'user_create': {
      require_admin();
      $u = trim((string)($in['userName'] ?? ''));
      $disp = trim((string)($in['displayName'] ?? '')); if ($disp === '') $disp = $u;
      $pass = (string)($in['password'] ?? '');
      $featCsv = norm_features_csv((string)($in['features'] ?? ''));
      if ($u === '') fail('请填写登录名。');
      if (preg_match('/\s/u', $u)) fail('登录名不能包含空格。');
      if (strlen($pass) < 6) fail('初始密码至少 6 位。');
      $st = db()->prepare('SELECT id FROM users WHERE LOWER(userName) = LOWER(?) LIMIT 1');
      $st->execute([$u]);
      if ($st->fetch()) fail('登录名已存在。');
      db()->prepare('INSERT INTO users (userName,displayName,isAdmin,isActive,passwordHash,features,createdAt) VALUES (?,?,0,1,?,?,?)')
         ->execute([$u, $disp, password_hash($pass, PASSWORD_DEFAULT), $featCsv, date('Y-m-d H:i')]);
      out((int)db()->lastInsertId());
    }

    default:
      fail('未知操作：' . $action);
  }
} catch (PDOException $e){
  fail('数据库错误：' . $e->getMessage());
} catch (Exception $e){
  fail('服务器错误：' . $e->getMessage());
}
