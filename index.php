<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="theme-color" content="#0A0908">
<title>物业管理台账</title>
<style>
/* ============================================================
   物业管理台账 · PHP + MySQL 版（移植自单文件 HTML 版）
   鎏金风格（Obsidian Gold）· 与个人网站 index 一致 · 兼容 PC / 手机 / 平板
   ============================================================ */
:root{
  --text:#F5EDDD; --muted:#B7A488; --faint:#7E7060;
  --glass:rgba(217,181,125,.055); --glass-strong:rgba(24,20,14,.92);
  --line:rgba(217,181,125,.16); --line2:rgba(217,181,125,.30);
  --danger:#f87171; --ok:#34d399; --warn:#fbbf24;
  --r-lg:20px; --r-md:14px; --r-sm:10px;
  --shadow:0 14px 44px -14px rgba(0,0,0,.6);
  --shadow-sm:0 8px 26px -12px rgba(0,0,0,.55);
  --sb-w:236px;
  --gold:#D9B57D; --gold-deep:#B98E48; --gold-bright:#E8CF96; --ink-gold:#241B0C;
}
*{box-sizing:border-box;margin:0;padding:0;-webkit-tap-highlight-color:transparent}
html{-webkit-text-size-adjust:100%}
body{
  font-family:-apple-system,BlinkMacSystemFont,"Segoe UI","PingFang SC","Hiragino Sans GB","Microsoft YaHei",sans-serif;
  color:var(--text); background:#0A0908; min-height:100vh; font-size:15px; line-height:1.55;
}
/* 背景装饰：曜石金晕（呼吸光斑） */
.bg-deco{position:fixed;inset:0;z-index:-1;overflow:hidden;pointer-events:none}
.bg-deco i{position:absolute;border-radius:50%;filter:blur(90px);opacity:.8;
  background:radial-gradient(circle,rgba(217,181,125,.15),transparent 65%);animation:breathe 9s ease-in-out infinite}
.bg-deco i:nth-child(1){width:55vmax;height:55vmax;left:8vmax;top:-24vmax}
.bg-deco i:nth-child(2){width:40vmax;height:40vmax;right:-12vmax;top:30vh;background:radial-gradient(circle,rgba(180,130,60,.13),transparent 65%);animation-duration:11s;animation-delay:.8s}
.bg-deco i:nth-child(3){width:44vmax;height:44vmax;left:16vw;bottom:-20vmax;animation-duration:10s;animation-delay:1.6s}
@keyframes breathe{0%,100%{opacity:.5;transform:scale(1)}50%{opacity:.95;transform:scale(1.1)}}
/* 细颗粒金线底纹 */
.bg-deco::after{content:'';position:absolute;inset:0;opacity:.5;
  background-image:repeating-linear-gradient(0deg,transparent 0 79px,rgba(217,181,125,.04) 79px 80px),
                   repeating-linear-gradient(90deg,transparent 0 79px,rgba(217,181,125,.04) 79px 80px)}
a{color:var(--gold);text-decoration:none}
button{font-family:inherit}
input,select,textarea{font-family:inherit;font-size:15px;color:var(--text)}
::placeholder{color:#7E7060}
::selection{background:var(--gold);color:#1A1408}
::-webkit-scrollbar{width:10px}::-webkit-scrollbar-thumb{background:rgba(217,181,125,.3);border-radius:8px}
.hidden{display:none!important}

/* ---------- 鎏金玻璃卡片 ---------- */
.glass{
  position:relative;
  background:linear-gradient(160deg,rgba(255,240,210,.07),rgba(217,181,125,.03));
  backdrop-filter:blur(20px) saturate(140%); -webkit-backdrop-filter:blur(20px) saturate(140%);
  border:1px solid rgba(217,181,125,.18); border-radius:var(--r-lg);
  box-shadow:var(--shadow);
}
.glass::after{content:'';position:absolute;top:0;left:12%;right:12%;height:1px;pointer-events:none;
  background:linear-gradient(90deg,transparent,rgba(255,240,200,.55),transparent)}
.card{padding:18px 20px}

/* ---------- 鎏金图标块（金系统一色系） ---------- */
.ico{width:38px;height:38px;min-width:38px;border-radius:12px;display:inline-grid;place-items:center;
  color:var(--ink-gold);font-size:19px;line-height:1;
  background:linear-gradient(135deg,var(--g1,#E3C383),var(--g2,#B98E48));
  box-shadow:0 4px 12px rgba(217,181,125,.22)}
.ico.sm{width:30px;height:30px;min-width:30px;border-radius:9px;font-size:15px}
.ico.lg{width:52px;height:52px;min-width:52px;border-radius:16px;font-size:26px}
.g-blue{--g1:#E3C383;--g2:#B98E48;box-shadow:0 4px 12px rgba(217,181,125,.25)}
.g-purple{--g1:#C9A05E;--g2:#8a6a35;box-shadow:0 4px 12px rgba(185,142,72,.25)}
.g-green{--g1:#F0DCA8;--g2:#C9A05E;box-shadow:0 4px 12px rgba(240,220,168,.22)}
.g-pink{--g1:#D9B57D;--g2:#9a7437;box-shadow:0 4px 12px rgba(217,181,125,.25)}
.g-amber{--g1:#F5E0B4;--g2:#D9B57D;box-shadow:0 4px 12px rgba(245,224,180,.25)}
.g-cyan{--g1:#E8CF96;--g2:#A87F3E;box-shadow:0 4px 12px rgba(232,207,150,.22)}
.g-teal{--g1:#C9A05E;--g2:#7a5c28;box-shadow:0 4px 12px rgba(185,142,72,.28)}
.g-slate{--g1:#B7A488;--g2:#7E7060;box-shadow:0 4px 12px rgba(183,164,136,.22)}
.g-red{--g1:#E0A96D;--g2:#A0522D;box-shadow:0 4px 12px rgba(160,82,45,.28)}
.g-indigo{--g1:#D9B57D;--g2:#8a6a35;box-shadow:0 4px 12px rgba(217,181,125,.25)}

/* ---------- 登录页 ---------- */
#loginView{min-height:100vh;display:grid;place-items:center;padding:20px}
.login-box{width:min(400px,94vw);padding:36px 30px;text-align:center}
.login-box .ico{margin:0 auto 14px}
.login-box h1{font-size:22px;letter-spacing:1px;background:linear-gradient(115deg,#F5E0B4,#D9B57D 45%,#F0DCA8 70%,#A87F3E);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent}
.login-box .sub{color:var(--muted);font-size:13px;margin:4px 0 22px}
.login-box .field{text-align:left;margin-bottom:14px}
.login-box .field label{display:block;font-size:13px;color:var(--muted);margin-bottom:6px;font-weight:600}
.login-tip{margin-top:16px;font-size:12.5px;color:var(--muted);background:rgba(217,181,125,.06);
  border:1px dashed var(--line2);border-radius:var(--r-sm);padding:8px 10px}

/* ---------- 应用骨架 ---------- */
#appView{display:flex;min-height:100vh}
#sidebar{width:var(--sb-w);min-width:var(--sb-w);position:sticky;top:0;height:100vh;
  display:flex;flex-direction:column;padding:18px 12px;z-index:30;
  background:rgba(12,10,7,.6);backdrop-filter:blur(22px) saturate(140%);-webkit-backdrop-filter:blur(22px) saturate(140%);
  border-right:1px solid rgba(217,181,125,.14)}
.brand{display:flex;align-items:center;gap:10px;padding:6px 8px 16px}
.brand b{font-size:16.5px;letter-spacing:.5px;color:var(--gold-bright)}
.brand small{display:block;font-size:11px;color:var(--faint);font-weight:400}
#sideNav{flex:1;overflow-y:auto;display:flex;flex-direction:column;gap:3px}
.nav-item{display:flex;align-items:center;gap:11px;padding:9px 10px;border-radius:13px;cursor:pointer;
  color:var(--text);font-weight:500;font-size:14.5px;border:none;background:none;width:100%;text-align:left;
  transition:background .15s}
.nav-item:hover{background:rgba(217,181,125,.08)}
.nav-item.active{background:linear-gradient(135deg,rgba(217,181,125,.16),rgba(217,181,125,.07));
  box-shadow:inset 0 0 0 1px rgba(217,181,125,.3);color:var(--gold-bright)}
.nav-item .ico{width:30px;height:30px;min-width:30px;font-size:15px;border-radius:9px}
.side-foot{border-top:1px solid var(--line);padding-top:12px;margin-top:8px}
.user-chip{display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:13px;background:rgba(217,181,125,.07)}
.user-chip .avatar{width:34px;height:34px;border-radius:50%;display:grid;place-items:center;color:var(--ink-gold);
  font-weight:700;font-size:14px;background:linear-gradient(135deg,#E8CF96,#B98E48)}
.user-chip b{font-size:13.5px;display:block;line-height:1.2}
.user-chip small{color:var(--muted);font-size:11.5px}
.side-foot .btn{margin-top:10px;width:100%}

.main-area{flex:1;min-width:0;display:flex;flex-direction:column}
#topbar{position:sticky;top:0;z-index:25;display:none;align-items:center;gap:10px;
  padding:12px 16px;margin:10px 12px 0;
  background:rgba(12,10,7,.55);backdrop-filter:blur(22px) saturate(140%);-webkit-backdrop-filter:blur(22px) saturate(140%);
  border:1px solid rgba(217,181,125,.16);border-radius:16px;box-shadow:var(--shadow-sm)}
#topbar h2{font-size:16.5px;flex:1;display:flex;align-items:center;gap:8px}
#pageWrap{flex:1;padding:18px 22px 28px;max-width:1280px;width:100%;margin:0 auto}
.page-head{display:flex;align-items:center;gap:12px;margin-bottom:16px;flex-wrap:wrap}
.page-head .ico{width:42px;height:42px;min-width:42px;font-size:21px;border-radius:13px}
.page-head h2{font-size:21px;background:linear-gradient(115deg,#F5E0B4,#D9B57D 45%,#F0DCA8 70%,#A87F3E);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent}
.page-head .sub{color:var(--muted);font-size:13px}
.page-head .spacer{flex:1}

/* ---------- 顶栏/底部导航（移动） ---------- */
#tabbar{display:none;position:fixed;left:12px;right:12px;bottom:calc(10px + env(safe-area-inset-bottom));
  z-index:40;padding:8px;border-radius:22px;justify-content:space-around;
  background:rgba(16,14,10,.78);backdrop-filter:blur(28px) saturate(150%);-webkit-backdrop-filter:blur(28px) saturate(150%);
  border:1px solid rgba(217,181,125,.22);box-shadow:0 10px 34px rgba(0,0,0,.5)}
.tab{display:flex;flex-direction:column;align-items:center;gap:2px;border:none;background:none;cursor:pointer;
  color:var(--muted);font-size:10.5px;padding:4px 10px;border-radius:12px;min-width:56px}
.tab .ti{font-size:19px;line-height:1.2;filter:grayscale(1);opacity:.6;transition:.15s}
.tab.active{color:var(--gold-bright);font-weight:600}
.tab.active .ti{filter:none;opacity:1;transform:translateY(-1px)}

/* ---------- 统计卡 ---------- */
.stat-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:14px;margin-bottom:18px}
.stat{display:flex;align-items:center;gap:13px;padding:16px 18px;cursor:default}
.stat .num{font-size:21px;font-weight:700;line-height:1.25;font-variant-numeric:tabular-nums}
.stat .num small{font-size:12.5px;font-weight:600;color:var(--muted)}
.stat .label{font-size:12.5px;color:var(--muted);font-weight:600}
.stat.warn .num{color:var(--warn)}.stat.danger .num{color:var(--danger)}.stat.ok .num{color:var(--ok)}

/* ---------- 通用区块 ---------- */
.section{margin-bottom:18px}
.sec-head{display:flex;align-items:center;gap:10px;margin-bottom:12px}
.sec-head h3{font-size:16px;display:flex;align-items:center;gap:8px}
.sec-head .hint{font-size:12.5px;color:var(--faint)}
.sec-head .spacer{flex:1}

/* ---------- 表格 ---------- */
.tbl-wrap{overflow-x:auto;border-radius:var(--r-lg)}
table.tbl{width:100%;border-collapse:collapse;font-size:14px;min-width:560px}
table.tbl th{font-size:11.5px;text-transform:uppercase;letter-spacing:.6px;color:var(--faint);
  text-align:left;padding:10px 12px;border-bottom:1px solid var(--line);white-space:nowrap;font-weight:700}
table.tbl td{padding:11px 12px;border-bottom:1px solid var(--line);vertical-align:middle}
table.tbl tr:last-child td{border-bottom:none}
table.tbl tbody tr{transition:background .12s}
table.tbl tbody tr:hover{background:rgba(217,181,125,.06)}
table.tbl .num{text-align:right;font-variant-numeric:tabular-nums;white-space:nowrap}
table.tbl tr.total-row td{font-weight:700;background:rgba(217,181,125,.08);border-top:1.5px solid var(--line2)}
.tbl-empty{padding:34px 16px;text-align:center;color:var(--faint);font-size:14px}
.t-primary{font-weight:600}
.t-dim{color:var(--faint);font-size:12.5px}
.t-danger{color:var(--danger)}
.clickable{cursor:pointer}

/* ---------- 徽章 ---------- */
.badge{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:999px;
  font-size:12px;font-weight:600;white-space:nowrap}
.badge i{width:6px;height:6px;border-radius:50%;background:currentColor}
.st-unpaid{background:rgba(248,113,113,.14);color:#fca5a5}
.st-partial{background:rgba(251,191,36,.14);color:#fcd34d}
.st-paid{background:rgba(52,211,153,.15);color:#6ee7b7}
.st-overdue{background:rgba(248,113,113,.2);color:#fda4af}
.st-gray{background:rgba(126,112,96,.22);color:#B7A488}
.st-active{background:rgba(52,211,153,.15);color:#6ee7b7}
.st-ended{background:rgba(126,112,96,.22);color:#8f8073}
.st-info{background:rgba(217,181,125,.14);color:#E8CF96}

/* ---------- 按钮 ---------- */
.btn{display:inline-flex;align-items:center;justify-content:center;gap:7px;border:none;cursor:pointer;
  padding:9px 18px;border-radius:13px;font-size:14px;font-weight:600;color:var(--ink-gold);
  background:linear-gradient(135deg,#C9A05E,#E8CF96 50%,#B98E48);box-shadow:0 4px 14px rgba(217,181,125,.28);
  transition:transform .12s,box-shadow .12s,opacity .12s;white-space:nowrap}
.btn:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(217,181,125,.45)}
.btn:active{transform:translateY(0) scale(.98)}
.btn:disabled{opacity:.55;cursor:not-allowed;transform:none}
.btn.sm{padding:6px 12px;font-size:12.5px;border-radius:10px}
.btn.ghost{background:rgba(217,181,125,.07);color:var(--text);box-shadow:inset 0 0 0 1px var(--line2)}
.btn.ghost:hover{background:rgba(217,181,125,.14)}
.btn.danger{background:linear-gradient(135deg,#fb7185,#ef4444);color:#fff;box-shadow:0 4px 14px rgba(239,68,68,.32)}
.btn.ok{background:linear-gradient(135deg,#D4AC66,#F0DA9E 50%,#C09550);color:var(--ink-gold);box-shadow:0 4px 18px rgba(217,181,125,.42)}
.btn.icon-only{padding:7px 9px}
.link-btn{border:none;background:none;color:var(--gold);cursor:pointer;font-size:13px;font-weight:600;padding:2px 4px}
.link-btn.danger{color:var(--danger)}

/* ---------- 表单 ---------- */
.field{margin-bottom:14px}
.field label{display:block;font-size:13px;color:var(--muted);font-weight:600;margin-bottom:6px}
.field label .req{color:var(--danger)}
.input,.select,.textarea{width:100%;padding:9px 13px;border-radius:12px;border:1px solid var(--line2);
  background:rgba(255,250,235,.05);outline:none;transition:border .15s,box-shadow .15s}
.input:focus,.select:focus,.textarea:focus{border-color:var(--gold);box-shadow:0 0 0 3px rgba(217,181,125,.15)}
/* 下拉选项：深底金字，避免系统默认白底配浅色文字看不清 */
select option{background:#1B1611;color:var(--text)}
.textarea{min-height:76px;resize:vertical}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:0 16px}
.form-grid .full{grid-column:1/-1}
@media(max-width:560px){.form-grid{grid-template-columns:1fr}}
.check-row{display:flex;flex-wrap:wrap;gap:8px 16px}
.check-item{display:inline-flex;align-items:center;gap:7px;font-size:14px;cursor:pointer;
  background:rgba(217,181,125,.05);border:1px solid var(--line2);padding:7px 12px;border-radius:11px}
.check-item input{accent-color:#C9A05E;width:15px;height:15px}
.input-hint{font-size:12px;color:var(--faint);margin-top:4px}

/* ---------- 弹窗 ---------- */
.modal-mask{position:fixed;inset:0;z-index:100;display:grid;place-items:center;padding:18px;
  background:rgba(0,0,0,.55);backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);
  animation:fadeIn .16s}
@keyframes fadeIn{from{opacity:0}to{opacity:1}}
@keyframes popIn{from{opacity:0;transform:translateY(14px) scale(.98)}to{opacity:1;transform:none}}
.modal{width:min(640px,96vw);max-height:88vh;overflow-y:auto;padding:22px;border-radius:24px;
  background:rgba(22,19,14,.94);backdrop-filter:blur(28px) saturate(140%);-webkit-backdrop-filter:blur(28px) saturate(140%);
  border:1px solid rgba(217,181,125,.22);box-shadow:0 24px 64px rgba(0,0,0,.6);animation:popIn .18s}
.modal.lg{width:min(860px,96vw)}
.modal.sm{width:min(420px,96vw)}
.modal-head{display:flex;align-items:center;gap:11px;margin-bottom:16px}
.modal-head h3{font-size:17.5px;flex:1}
.modal-x{border:none;background:rgba(217,181,125,.09);width:32px;height:32px;border-radius:50%;cursor:pointer;
  font-size:15px;color:var(--muted);display:grid;place-items:center}
.modal-x:hover{background:rgba(217,181,125,.2)}
.modal-foot{display:flex;justify-content:flex-end;gap:10px;margin-top:18px;flex-wrap:wrap}
.confirm-msg{font-size:14.5px;color:var(--text);padding:4px 2px 6px;line-height:1.7}
.confirm-msg b{color:var(--danger)}

/* ---------- 轻提示 ---------- */
#toastBox{position:fixed;top:18px;left:50%;transform:translateX(-50%);z-index:200;display:flex;
  flex-direction:column;gap:8px;align-items:center;pointer-events:none;width:max-content;max-width:92vw}
.toast{padding:11px 20px;border-radius:14px;font-size:14px;font-weight:500;color:#F5EDDD;
  background:rgba(20,17,12,.92);backdrop-filter:blur(12px);border:1px solid rgba(217,181,125,.25);box-shadow:var(--shadow);
  animation:toastIn .22s;max-width:92vw}
.toast.ok{background:linear-gradient(135deg,rgba(5,150,105,.94),rgba(16,185,129,.94))}
.toast.err{background:linear-gradient(135deg,rgba(220,38,38,.95),rgba(239,68,68,.95))}
@keyframes toastIn{from{opacity:0;transform:translateY(-10px)}to{opacity:1}}

/* ---------- 明细面板 ---------- */
.detail-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:10px;margin-bottom:14px}
.d-item{background:rgba(217,181,125,.05);border:1px solid var(--line);border-radius:12px;padding:9px 12px}
.d-item .k{font-size:11.5px;color:var(--faint);font-weight:600;margin-bottom:2px}
.d-item .v{font-size:14px;font-weight:600;word-break:break-all}
.sub-table-h{font-size:13px;font-weight:700;color:var(--muted);margin:14px 0 8px;display:flex;align-items:center;gap:8px}
.sub-table-h .spacer{flex:1}
.note-box{background:rgba(217,181,125,.04);border:1px dashed var(--line2);border-radius:12px;
  padding:10px 14px;font-size:13.5px;color:var(--muted);margin-top:10px;white-space:pre-wrap;word-break:break-word}

/* ---------- 列表卡（物业/房间/租客） ---------- */
.tile-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:14px}
.tile{padding:16px 17px;display:flex;flex-direction:column;gap:9px;transition:transform .14s}
.tile:hover{transform:translateY(-2px)}
.tile .t-head{display:flex;align-items:center;gap:11px}
.tile .t-head b{font-size:15.5px}
.tile .t-head small{display:block;color:var(--muted);font-size:12px}
.tile .t-meta{font-size:12.5px;color:var(--muted);display:flex;flex-wrap:wrap;gap:4px 12px}
.tile .t-foot{display:flex;gap:8px;margin-top:2px;flex-wrap:wrap}

/* ---------- 搜索栏 ---------- */
.toolbar{display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin-bottom:14px}
.toolbar .input,.toolbar .select{width:auto;min-width:150px}
.toolbar .input.grow{flex:1;min-width:170px}

/* ---------- 空状态 ---------- */
.empty{padding:44px 20px;text-align:center;color:var(--faint)}
.empty .e-ico{font-size:40px;margin-bottom:10px;opacity:.6}
.empty p{font-size:14px}

/* ---------- 响应式 ---------- */
@media(max-width:920px){
  #sidebar{display:none}
  #topbar{display:flex}
  #tabbar{display:flex}
  #pageWrap{padding:14px 14px 108px}
  .page-head h2{font-size:18.5px}
  .modal{padding:18px 16px;border-radius:20px}
  .stat-grid{grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:10px}
  .stat{padding:13px 14px}.stat .num{font-size:17px}
  /* 输入控件 ≥16px，避免 iOS Safari 聚焦时自动放大页面 */
  input,select,textarea{font-size:16px}
}
@media(max-width:760px){
  /* 修复：卡片式行被 table.tbl 的 min-width:560px 撑出屏幕 */
  table.rsp,table.rsp tbody{display:block;width:100%;min-width:0}
  table.rsp thead{display:none}
  table.rsp tr{display:block;padding:12px 14px;border-bottom:1px solid var(--line);border-radius:14px;margin-bottom:10px}
  table.rsp tr:hover{background:rgba(217,181,125,.06)}
  table.rsp td{display:flex;justify-content:space-between;align-items:center;gap:14px;
    border:none;padding:5px 0;text-align:right;min-width:0}
  table.rsp td::before{content:attr(data-label);color:var(--muted);font-size:12.5px;font-weight:700;text-align:left;flex-shrink:0}
  table.rsp td.act-cell{justify-content:flex-end;flex-wrap:wrap}
  table.rsp td.act-cell::before{content:none}
  table.rsp tr.total-row td{justify-content:space-between}
}
@media(max-width:560px){
  .form-grid{grid-template-columns:1fr}
  .toolbar .input,.toolbar .select{flex:1 1 46%;min-width:0}
  .toolbar .input.grow{flex:1 1 100%}
  /* 长提示语按钮允许换行，避免横向溢出 */
  .check-row .btn{white-space:normal;text-align:left}
}
@media print{ #sidebar,#topbar,#tabbar,.page-head .spacer .btn{display:none!important} }

/* ============================================================
   浅色主题（一键切换；暗色为默认，本段仅做覆盖）
   ============================================================ */
html[data-theme=light]{ color-scheme:light;
  --text:#2C2419; --muted:#6B5D45; --faint:#9A8C72;
  --glass:rgba(255,255,255,.82); --glass-strong:rgba(253,250,243,.96);
  --line:rgba(138,106,53,.18); --line2:rgba(138,106,53,.35);
  --danger:#C23B30; --ok:#1F7A3D; --warn:#B25C09;
  --shadow:0 14px 44px -16px rgba(120,92,45,.28);
  --shadow-sm:0 8px 26px -14px rgba(120,92,45,.22);
  --gold:#9A7635; --gold-deep:#8A6A35; --gold-bright:#7A5E2C;
}
html[data-theme=light] body{background:#F4EEE2;color:var(--text)}
html[data-theme=light] .bg-deco i{opacity:.5;filter:blur(100px)}
html[data-theme=light] .bg-deco::after{opacity:.9}
html[data-theme=light] .glass{
  background:linear-gradient(160deg,rgba(255,255,255,.95),rgba(250,243,228,.9));
  border-color:var(--line);
}
html[data-theme=light] .glass::after{
  background:linear-gradient(90deg,transparent,rgba(154,118,53,.25),transparent);
}
html[data-theme=light] #sidebar{background:rgba(255,252,245,.86);border-right-color:var(--line)}
html[data-theme=light] #topbar{background:rgba(255,252,245,.92);border-color:var(--line)}
html[data-theme=light] #tabbar{
  background:rgba(255,252,245,.94);border-color:var(--line);
  box-shadow:0 10px 34px rgba(120,92,45,.22);
}
html[data-theme=light] .input,
html[data-theme=light] .select,
html[data-theme=light] .textarea{background:#FFFDF8;color:var(--text)}
html[data-theme=light] input[type=date]{color-scheme:light}
html[data-theme=light] select option{background:#fff;color:#2C2419}
html[data-theme=light] .modal{
  background:rgba(253,250,243,.97);border-color:var(--line2);
  box-shadow:0 24px 64px rgba(120,92,45,.32);
}
/* 徽章：浅底深字 */
html[data-theme=light] .st-unpaid{background:rgba(194,59,48,.1);color:#C23B30}
html[data-theme=light] .st-partial{background:rgba(178,92,9,.12);color:#B25C09}
html[data-theme=light] .st-paid,
html[data-theme=light] .st-active{background:rgba(31,122,61,.12);color:#1F7A3D}
html[data-theme=light] .st-overdue{background:rgba(194,59,48,.12);color:#B93226}
html[data-theme=light] .st-gray,
html[data-theme=light] .st-ended{background:rgba(107,93,69,.12);color:#6B5D45}
html[data-theme=light] .st-info{background:rgba(154,118,53,.12);color:#8A6A35}
/* 主题切换钮 */
.theme-btn{
  display:inline-flex;align-items:center;justify-content:center;
  width:38px;height:34px;font-size:17px;line-height:1;
  border-radius:11px;border:1px solid var(--line2);
  background:transparent;cursor:pointer;flex:0 0 auto;
}
.theme-btn:hover{background:rgba(217,181,125,.14)}
#tbTheme{width:34px;height:34px;padding:0}
#loginTheme{position:fixed;top:14px;right:14px;z-index:20;background:var(--glass-strong)}
</style>
</head>
<body>
<script>
/* 首屏前应用主题偏好，避免白闪（暗色默认；与切换逻辑共用 pl_theme 键） */
(function(){try{var t=localStorage.getItem('pl_theme')==='light'?'light':'dark';document.documentElement.setAttribute('data-theme',t);}catch(e){document.documentElement.setAttribute('data-theme','dark');}})();
</script>
<div class="bg-deco"><i></i><i></i><i></i></div>

<!-- 登录视图 -->
<div id="loginView">
  <button class="theme-btn" id="loginTheme" type="button" title="切换浅色 / 暗色">☀️</button>
  <div class="glass login-box">
    <div class="ico lg g-indigo">🏠</div>
    <h1>物业管理台账</h1>
    <p class="sub">PHP + MySQL 版 · 数据保存在服务器</p>
    <div class="field">
      <label>登录名</label>
      <input class="input" id="loginUser" autocomplete="username" placeholder="请输入登录名">
    </div>
    <div class="field">
      <label>密码</label>
      <input class="input" id="loginPass" type="password" autocomplete="current-password" placeholder="请输入密码">
    </div>
    <button class="btn" id="loginBtn" style="width:100%;padding:11px">登 录</button>
    <div class="login-tip">默认总管理员：<b>admin</b> / <b>admin123</b>，登录后请在「设置」中修改密码</div>
  </div>
</div>

<!-- 应用视图 -->
<div id="appView" class="hidden">
  <aside id="sidebar" class="hidden">
    <div class="brand">
      <div class="ico g-indigo">🏠</div>
      <div><b>物业管理台账</b><small>PHP + MySQL 版</small></div>
    </div>
    <nav id="sideNav"></nav>
    <div class="side-foot">
      <div class="user-chip">
        <div class="avatar" id="sbAvatar">A</div>
        <div style="min-width:0"><b id="sbName">—</b><small id="sbRole">—</small></div>
      </div>
      <button class="btn ghost" id="logoutBtn">退出登录</button>
      <button class="btn ghost" id="sbTheme" type="button" style="margin-top:8px">☀️ 浅色 / 🌙 暗色</button>
      <div class="t-dim" style="text-align:center;margin-top:10px" id="sbVer"></div>
    </div>
  </aside>

  <div class="main-area">
    <header id="topbar" class="hidden">
      <div class="ico sm g-indigo" id="tbIco">📊</div>
      <h2 id="tbTitle">仪表盘</h2>
      <span class="spacer" style="flex:1"></span>
      <button class="theme-btn" id="tbTheme" type="button" title="切换浅色 / 暗色">☀️</button>
      <span class="t-dim" id="tbUser"></span>
    </header>
    <div id="pageWrap"><div id="pageRoot"></div></div>
  </div>

  <nav id="tabbar"></nav>
</div>

<div id="modalRoot"></div>
<div id="toastBox"></div>

<script>
'use strict';
/* ============================================================
   工具函数
   ============================================================ */
const APP_VERSION = '1.4';
const $  = (s, el) => (el || document).querySelector(s);
const $$ = (s, el) => Array.from((el || document).querySelectorAll(s));
function esc(s){ return String(s == null ? '' : s)
  .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
  .replace(/"/g,'&quot;').replace(/'/g,'&#39;'); }

/* 金额：保留两位、千分位 */
function r2(x){ return Math.round((Number(x) || 0) * 100) / 100; }
function fmt(x){ return r2(x).toLocaleString('zh-CN', { minimumFractionDigits:2, maximumFractionDigits:2 }); }
function money(x){ return '¥' + fmt(x); }

/* 日期：统一存 'YYYY-MM-DD' */
function pad2(n){ return (n < 10 ? '0' : '') + n; }
function toStr(d){ return d.getFullYear() + '-' + pad2(d.getMonth()+1) + '-' + pad2(d.getDate()); }
function todayStr(){ return toStr(new Date()); }
function parseDate(s){
  if (!s) return null;
  const m = /^(\d{4})-(\d{1,2})-(\d{1,2})/.exec(String(s));
  if (!m) return null;
  return new Date(+m[1], +m[2]-1, +m[3]);
}
function fmtDate(s){ return s ? String(s).slice(0,10) : '—'; }
function daysInMonth(y, m){ return new Date(y, m, 0).getDate(); }      // m: 1-12
function monthIdx(y, m){ return y * 12 + (m - 1); }
function dueDateOf(y, m, day){ return y + '-' + pad2(m) + '-' + pad2(Math.min(Math.max(day,1), daysInMonth(y,m))); }
function daysBetween(a, b){ return Math.round((parseDate(b) - parseDate(a)) / 86400000); }
function nowIso(){ const d = new Date(); return toStr(d) + ' ' + pad2(d.getHours()) + ':' + pad2(d.getMinutes()); }
function fileDate(){ const d = new Date(); return '' + d.getFullYear() + pad2(d.getMonth()+1) + pad2(d.getDate()); }

/* 下载 */
function download(name, content, type){
  const blob = content instanceof Blob ? content : new Blob([content], { type: type || 'application/octet-stream' });
  const a = document.createElement('a');
  a.href = URL.createObjectURL(blob);
  a.download = name;
  document.body.appendChild(a); a.click();
  setTimeout(() => { URL.revokeObjectURL(a.href); a.remove(); }, 400);
}

/* ============================================================
   服务器数据层（fetch REST 适配器，PHP + MySQL 版）
   接口与 HTML 版 IndexedDB 封装完全一致：
   open/get/all/put/del/clear/kvGet/kvSet/bulkPut/clearAll
   数据全部存放于服务器 MySQL，经 api.php 读写；浏览器不再本地存储业务数据。
   put：对象带 id → UPDATE；不带 id → INSERT 并返回新 id（与 autoIncrement 语义一致）。
   布尔/数值/类型还原由服务器统一处理（TINYINT→true/false，DOUBLE→number）。
   ============================================================ */
async function apiCall(action, body){
  const opt = { method:'POST', credentials:'same-origin', headers:{'Content-Type':'application/json'} };
  if (body !== undefined) opt.body = JSON.stringify(body);
  let res;
  try { res = await fetch('api.php?action=' + encodeURIComponent(action), opt); }
  catch(e){ throw new Error('无法连接服务器，请检查网络后重试。'); }
  let j = null;
  try { j = await res.json(); } catch(e){}
  if (!j || typeof j.ok !== 'boolean') throw new Error('服务器响应异常（HTTP ' + res.status + '），请确认数据库已完成安装。');
  if (!j.ok) throw new Error(j.error || '请求失败');
  return j.data;
}
const DB = (function(){
  return {
    open: async () => true,
    get:  (s,k) => apiCall('get', { store:s, id:k }),
    all:  (s)   => apiCall('list', { store:s }),
    put:  (s,v) => apiCall('put', { store:s, record:v }),
    del:  (s,k) => apiCall('delete', { store:s, id:k }),
    clear:(s)   => apiCall('clear', { store:s }),
    kvGet: async (k, d) => { const r = await apiCall('kv_get', { key:k }); return (r && r.found) ? r.value : (d === undefined ? null : d); },
    kvSet: (k, v) => apiCall('kv_set', { key:k, value:v }),
    bulkPut: (s, arr) => apiCall('bulk_put', { store:s, records:arr }),
    clearAll: () => apiCall('clear_all', {}),
  };
})();

/* 服务器专属接口：登录 / 会话 / 退出 / 改密 / 新建用户 / 导出 / 导入 */
const API = {
  login:   (userName, password) => apiCall('login', { userName, password }),
  logout:  () => apiCall('logout', {}).catch(() => {}),
  session: () => apiCall('session', {}),
  changePassword: (oldPassword, newPassword) => apiCall('change_password', { oldPassword, newPassword }),
  createUser: (rec) => apiCall('user_create', rec),
  exportSnapshot: () => apiCall('export', {}),
  importSnapshot: (snap) => apiCall('import', { snapshot:snap }),
};

const ALL_FEATURES = [
  ['bills','账单记账'], ['reports','报表'], ['leases','租约'], ['rooms','房间'],
  ['tenants','租客'], ['properties','物业'], ['settings','设置'],
];
const DEFAULT_FEATURES = 'bills';
function normFeatures(csv){
  const set = String(csv || '').split(',').map(s => s.trim()).filter(k => ALL_FEATURES.some(a => a[0] === k));
  return ALL_FEATURES.filter(a => set.indexOf(a[0]) >= 0).map(a => a[0]).join(',');
}
function userHasFeature(u, key){
  if (!u) return false;
  if (u.isAdmin) return true;
  return normFeatures(u.features).split(',').indexOf(key) >= 0;
}

let session = null;   /* {userId, userName, displayName, isAdmin, features:[]} */

/* 登录 / 会话：认证在服务器完成（PHP session + bcrypt），前端只持有会话对象 */
async function doLogin(userName, password){
  try { session = await API.login(userName, password); return null; }
  catch(e){ return e.message || '登录失败，请重试。'; }
}
async function loadSession(){
  try { const s = await API.session(); return (s && s.userId) ? s : null; }
  catch(e){ return null; }
}
function doLogout(){ API.logout(); session = null; showLogin(); }

/* ============================================================
   弹窗 / 确认框（默认按钮：取消）/ 轻提示
   ============================================================ */
function openModal(html, opts){
  opts = opts || {};
  const mask = document.createElement('div');
  mask.className = 'modal-mask';
  mask.innerHTML = '<div class="modal ' + (opts.size || '') + '">' + html + '</div>';
  $('#modalRoot').appendChild(mask);
  mask.addEventListener('mousedown', e => { if (e.target === mask && !opts.stay) closeModal(mask); });
  return mask;
}
function closeModal(el){
  const mask = el && el.classList && el.classList.contains('modal-mask') ? el : $('#modalRoot').lastElementChild;
  if (mask) mask.remove();
}
function closeAllModals(){ $('#modalRoot').innerHTML = ''; }

function toast(msg, type){
  const t = document.createElement('div');
  t.className = 'toast' + (type ? ' ' + type : '');
  t.textContent = msg;
  $('#toastBox').appendChild(t);
  setTimeout(() => { t.style.transition = 'opacity .3s'; t.style.opacity = '0'; setTimeout(() => t.remove(), 320); }, 2400);
}

/* 确认框：取消为默认按钮（Enter/焦点均落在取消上），okDanger 时确定按钮为红色 */
function confirmBox(msg, opts){
  opts = opts || {};
  return new Promise(res => {
    const mask = openModal(
      '<div class="modal-head"><div class="ico sm ' + (opts.okDanger ? 'g-red' : 'g-amber') + '">⚠️</div><h3>' + esc(opts.title || '请确认') + '</h3></div>' +
      '<div class="confirm-msg">' + msg + '</div>' +
      '<div class="modal-foot">' +
        '<button class="btn ghost" data-act="cancel">取消</button>' +
        '<button class="btn ' + (opts.okDanger ? 'danger' : '') + '" data-act="ok">' + esc(opts.okText || '确定') + '</button>' +
      '</div>', { size:'sm' });
    $('[data-act="cancel"]', mask).focus();
    mask.addEventListener('click', e => {
      const b = e.target.closest('[data-act]');
      if (!b) return;
      closeModal(mask); res(b.dataset.act === 'ok');
    });
    mask.addEventListener('keydown', e => {
      if (e.key === 'Escape'){ closeModal(mask); res(false); }
      if (e.key === 'Enter'){ e.preventDefault(); closeModal(mask); res(false); }  /* Enter=默认(取消) */
    });
  });
}

/* 通用表单弹窗：fields 为 HTML 字符串，onSave 返回错误文案或 null */
function formModal(opts){
  const mask = openModal(
    '<div class="modal-head"><div class="ico sm ' + (opts.iconCls || 'g-indigo') + '">' + (opts.icon || '✏️') + '</div>' +
      '<h3>' + esc(opts.title) + '</h3>' +
      '<button class="modal-x" data-act="x">✕</button></div>' +
    '<form id="fmForm" novalidate>' + opts.fields +
      '<div class="form-err t-danger" style="font-size:13px;margin-top:4px;min-height:1em"></div>' +
      '<div class="modal-foot">' +
        '<button type="button" class="btn ghost" data-act="cancel">取消</button>' +
        '<button type="submit" class="btn">' + esc(opts.okText || '保存') + '</button>' +
      '</div></form>', opts);
  const form = $('#fmForm', mask);
  const err = $('.form-err', mask);
  mask.addEventListener('click', e => {
    if (e.target.closest('[data-act="x"]') || e.target.closest('[data-act="cancel"]')) closeModal(mask);
  });
  form.addEventListener('submit', async e => {
    e.preventDefault();
    err.textContent = '';
    const msg = await opts.onSave(mask);
    if (msg){ err.textContent = msg; return; }
    closeModal(mask);
  });
  return mask;
}
function fv(mask, sel){ const el = $(sel, mask); return el ? el.value.trim() : ''; }
function fn(mask, sel){ const v = fv(mask, sel); const n = parseFloat(v); return isNaN(n) ? null : n; }

/* ============================================================
   导航 / 路由
   ============================================================ */
const NAV = [
  { key:'dashboard',  name:'仪表盘', icon:'📊', cls:'g-blue',   feat:null },
  { key:'properties', name:'物业',   icon:'🏢', cls:'g-amber',  feat:'properties' },
  { key:'rooms',      name:'房间',   icon:'🚪', cls:'g-green',  feat:'rooms' },
  { key:'tenants',    name:'租客',   icon:'👥', cls:'g-pink',   feat:'tenants' },
  { key:'leases',     name:'租约',   icon:'📄', cls:'g-purple', feat:'leases' },
  { key:'bills',      name:'账单',   icon:'🧾', cls:'g-amber',  feat:'bills' },
  { key:'knowledge',  name:'经验库', icon:'💡', cls:'g-cyan',   feat:'leases' },
  { key:'reports',    name:'报表',   icon:'📈', cls:'g-teal',   feat:'reports' },
  { key:'users',      name:'用户',   icon:'🛡️', cls:'g-slate',  feat:'admin' },
  { key:'settings',   name:'设置',   icon:'⚙️', cls:'g-slate',  feat:'settings' },
];
const TABS = ['dashboard','bills','leases','reports','more'];
let currentPage = 'dashboard';

function navAllowed(key){
  const n = NAV.find(x => x.key === key);
  if (!n) return false;
  if (n.feat === 'admin') return !!(session && session.isAdmin);
  if (!n.feat) return true;
  return userHasFeature(session, n.feat);
}
function renderNav(){
  $('#sideNav').innerHTML = NAV.filter(n => navAllowed(n.key)).map(n =>
    '<button class="nav-item' + (currentPage === n.key ? ' active' : '') + '" data-nav="' + n.key + '">' +
      '<span class="ico ' + n.cls + '">' + n.icon + '</span>' + n.name + '</button>').join('');
  const tbNav = NAV.filter(n => navAllowed(n.key));
  const tabDefs = TABS.map(k => k === 'more' ? { key:'more', name:'更多', icon:'⋯' } : NAV.find(n => n.key === k)).filter(Boolean)
                      .filter(t => t.key === 'more' || navAllowed(t.key));
  $('#tabbar').innerHTML = tabDefs.map(t =>
    '<button class="tab' + (currentPage === t.key ? ' active' : '') + '" data-nav="' + t.key + '">' +
      '<span class="ti">' + t.icon + '</span>' + t.name + '</button>').join('');
}
function go(key){
  if (key === 'more') { showSheet(); return; }
  if (!navAllowed(key)) key = 'dashboard';
  currentPage = key;
  renderNav();
  const n = NAV.find(x => x.key === key);
  $('#tbTitle').textContent = n.name;
  $('#tbIco').textContent = n.icon;
  $('#tbIco').className = 'ico sm ' + n.cls;
  window.scrollTo(0, 0);
  RENDER[key]().catch(err => { console.error(err); toast('页面加载失败：' + err.message, 'err'); });
}
function showSheet(){
  const items = NAV.filter(n => navAllowed(n.key)).map(n =>
    '<button class="btn ghost" data-nav="' + n.key + '" style="justify-content:flex-start">' +
      '<span class="ico sm ' + n.cls + '">' + n.icon + '</span>' + n.name + '</button>').join('');
  const mask = openModal(
    '<div class="modal-head"><div class="ico sm g-indigo">🧭</div><h3>全部功能</h3>' +
      '<button class="modal-x" data-act="x">✕</button></div>' +
    '<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:10px">' + items + '</div>', { size:'sm' });
  mask.addEventListener('click', e => {
    if (e.target.closest('[data-act="x"]')) return closeModal(mask);
    const b = e.target.closest('[data-nav]');
    if (b){ closeModal(mask); go(b.dataset.nav); }
  });
}

/* ============================================================
   数据缓存：渲染前整体加载，写操作后刷新
   ============================================================ */
const data = { properties:[], rooms:[], tenants:[], leases:[], bills:[], billItems:[], payments:[], knowledge:[], users:[], settings:{} };
async function loadAll(){
  const [props, rooms, tenants, leases, bills, items, pays, know, users, kv] = await Promise.all([
    DB.all('properties'), DB.all('rooms'), DB.all('tenants'), DB.all('leases'),
    DB.all('bills'), DB.all('billItems'), DB.all('payments'), DB.all('knowledge'), DB.all('users'), DB.all('kv'),
  ]);
  data.properties = props; data.rooms = rooms; data.tenants = tenants; data.leases = leases;
  data.bills = bills; data.billItems = items; data.payments = pays; data.knowledge = know; data.users = users;
  data.settings = {};
  kv.forEach(r => { if (r.key !== 'pl_session') data.settings[r.key] = r.value; });
  /* 排序：业务表按 id 升序，便于稳定展示 */
  ['properties','rooms','tenants','leases','bills','billItems','payments','knowledge','users']
    .forEach(k => data[k].sort((a,b) => a.id - b.id));
}
function refreshAfterSave(){ return loadAll(); }

/* 常用取值 */
function propOf(room){ return room ? data.properties.find(p => p.id === room.propertyId) : null; }
function roomName(id){ const r = data.rooms.find(x => x.id === id); if (!r) return '—'; const p = propOf(r); return (p ? p.name + ' · ' : '') + r.name; }
function roomOf(id){ return data.rooms.find(x => x.id === id) || null; }
function tenantName(id){ const t = data.tenants.find(x => x.id === id); return t ? t.name : '—'; }
function leaseLabel(l){
  if (!l) return '—';
  return roomName(l.roomId) + ' · ' + tenantName(l.tenantId);
}
function itemsOf(billId){ return data.billItems.filter(i => i.billId === billId); }
function paymentsOf(billId){ return data.payments.filter(p => p.billId === billId); }
function paidOf(billId){ return r2(paymentsOf(billId).reduce((s,p) => s + p.amount, 0)); }
function extraOf(billId){ return r2(itemsOf(billId).reduce((s,i) => s + i.amount, 0)); }
function totalOf(bill){ return r2(bill.rentAmount + extraOf(bill.id)); }
function billStatus(bill){
  const paid = paidOf(bill.id), total = totalOf(bill);
  if (paid <= 0.001) return 'Unpaid';
  if (paid < total - 0.001) return 'Partial';
  return 'Paid';
}
function isOverdue(bill){ return bill.dueDate < todayStr() && billStatus(bill) !== 'Paid'; }
function statusBadge(bill){
  const st = billStatus(bill);
  if (st === 'Paid') return '<span class="badge st-paid"><i></i>已付清</span>';
  if (st === 'Partial') return '<span class="badge st-partial"><i></i>部分已付</span>';
  if (isOverdue(bill)) return '<span class="badge st-overdue"><i></i>已逾期 ' + Math.max(0, daysBetween(bill.dueDate, todayStr())) + ' 天</span>';
  return '<span class="badge st-unpaid"><i></i>未付</span>';
}
function settingVal(key, dflt){ const v = data.settings[key]; return (v == null || v === '') ? dflt : v; }
const SET = { LANDLORD:'landlord_name', NOTICE:'payment_notice', QR:'pay_qrcode' };
const DEFAULT_NOTICE = '请在到期日前支付，转账后请告知，谢谢~';
const NOTICE_PRESETS = [
  '请在到期日前支付，转账后请告知，谢谢~',
  '房租请按时支付，如有疑问随时联系我，谢谢~',
  '转账后请截图告知，方便核对登记，谢谢~',
  '请按账单金额支付，付款后我们会第一时间确认，谢谢~',
  '温馨提示：请按时交租，逾期会影响下期续租，感谢配合~',
];

/* 通用空状态 */
function emptyBox(icon, text){ return '<div class="empty"><div class="e-ico">' + icon + '</div><p>' + text + '</p></div>'; }

/* ============================================================
   页面注册表
   ============================================================ */
const RENDER = {};

/* ============================================================
   仪表盘
   ============================================================ */
RENDER.dashboard = async function(){
  await ensureBillsThrough(todayStr());
  await loadAll();
  const ym = todayStr().slice(0,7);
  const monthBills = data.bills.filter(b => String(b.periodStart).slice(0,7) === ym);
  const monthDue = r2(monthBills.reduce((s,b) => s + totalOf(b), 0));
  const monthPaid = r2(data.payments.filter(p => String(p.paidAt).slice(0,7) === ym).reduce((s,p) => s + p.amount, 0));
  const overdue = data.bills.filter(isOverdue);
  const dueTotal = r2(data.bills.reduce((s,b) => { const st = billStatus(b); return st === 'Paid' ? s : s + (totalOf(b) - paidOf(b.id)); }, 0));

  const soon = data.leases.filter(l => l.status === 'Active' && l.endDate)
    .map(l => ({ l, days: daysBetween(todayStr(), l.endDate) }))
    .filter(x => x.days <= 60).sort((a,b) => a.days - b.days);
  const recentPays = data.payments.slice().sort((a,b) => String(b.paidAt).localeCompare(String(a.paidAt)) || b.id - a.id).slice(0, 8);
  const activeLeases = data.leases.filter(l => l.status === 'Active').length;

  $('#pageRoot').innerHTML =
  '<div class="page-head"><div class="ico g-blue">📊</div>' +
    '<div><h2>仪表盘</h2><div class="sub">' + todayStr() + ' · 欢迎回来，' + esc(session.displayName) + '</div></div></div>' +
  '<div class="stat-grid">' +
    statCard('g-blue','💰','本月应收', money(monthDue)) +
    statCard('g-green','✅','本月实收', money(monthPaid)) +
    statCard('g-red','⏰','逾期账单', overdue.length + ' 张' + '<small> · ' + money(r2(overdue.reduce((s,b) => s + totalOf(b) - paidOf(b.id),0))) + '</small>') +
    statCard('g-amber','🧾','待收合计', money(dueTotal)) +
  '</div>' +
  '<div class="stat-grid">' +
    statCard('g-amber','🏢','物业 / 房间', data.properties.length + ' / ' + data.rooms.length + '') +
    statCard('g-purple','📄','生效中租约', activeLeases + ' 份') +
    statCard('g-pink','👥','租客', data.tenants.length + ' 位') +
    statCard('g-cyan','💡','经验沉淀', data.knowledge.length + ' 条') +
  '</div>' +
  '<div class="section glass card">' +
    '<div class="sec-head"><h3>🔔 租约到期提醒</h3><span class="hint">未来 60 天内到期的生效租约</span></div>' +
    (soon.length ? '<div class="tbl-wrap"><table class="tbl rsp"><thead><tr><th>物业 · 房间</th><th>租客</th><th>到期日</th><th>状态</th><th class="num">操作</th></tr></thead><tbody>' +
      soon.map(x => '<tr><td data-label="房间" class="t-primary">' + esc(roomName(x.l.roomId)) + '</td>' +
        '<td data-label="租客">' + esc(tenantName(x.l.tenantId)) + '</td>' +
        '<td data-label="到期日">' + fmtDate(x.l.endDate) + '</td>' +
        '<td data-label="状态">' + (x.days < 0 ? '<span class="badge st-overdue"><i></i>已到期 ' + (-x.days) + ' 天</span>' : '<span class="badge st-info"><i></i>还有 ' + x.days + ' 天</span>') + '</td>' +
        '<td class="num act-cell"><button class="link-btn" data-lease="' + x.l.id + '">查看租约</button></td></tr>').join('') +
      '</tbody></table></div>' : emptyBox('🎉', '近期没有即将到期的租约')) +
  '</div>' +
  '<div class="section glass card">' +
    '<div class="sec-head"><h3>🧾 最近收款</h3><span class="hint">最近 8 笔</span></div>' +
    (recentPays.length ? '<div class="tbl-wrap"><table class="tbl rsp"><thead><tr><th>日期</th><th>账单</th><th>方式</th><th class="num">金额</th><th>备注</th></tr></thead><tbody>' +
      recentPays.map(p => {
        const b = data.bills.find(x => x.id === p.billId);
        return '<tr><td data-label="日期">' + fmtDate(p.paidAt) + '</td>' +
          '<td data-label="账单" class="t-primary">' + (b ? esc(roomName(b.roomId) + ' · ' + tenantName(b.tenantId)) : '—') + '</td>' +
          '<td data-label="方式">' + methodLabel(p.method) + '</td>' +
          '<td data-label="金额" class="num t-primary">' + money(p.amount) + '</td>' +
          '<td data-label="备注" class="t-dim">' + esc(p.note || '—') + '</td></tr>';
      }).join('') + '</tbody></table></div>' : emptyBox('💤', '还没有收款记录')) +
  '</div>';

  $$('#pageRoot [data-lease]').forEach(b => b.addEventListener('click', () => openLeaseDetail(+b.dataset.lease)));
};
function statCard(cls, icon, label, num){
  return '<div class="glass stat"><div class="ico ' + cls + '">' + icon + '</div>' +
    '<div><div class="num">' + num + '</div><div class="label">' + label + '</div></div></div>';
}

/* ============================================================
   物业
   ============================================================ */
RENDER.properties = async function(){
  await loadAll();
  const tiles = data.properties.map(p => {
    const rooms = data.rooms.filter(r => r.propertyId === p.id && !r.isArchived);
    const typeNames = { 0:'房屋', 1:'商铺', 2:'公寓' };
    return '<div class="glass tile" data-id="' + p.id + '">' +
      '<div class="t-head"><div class="ico g-amber">🏢</div>' +
        '<div><b>' + esc(p.name) + '</b><small>' + (typeNames[p.type] || '房屋') + (p.address ? ' · ' + esc(p.address) : '') + '</small></div></div>' +
      '<div class="t-meta"><span>🚪 在用房间 ' + rooms.length + '</span>' +
        (p.managementContact ? '<span>📞 ' + esc(p.managementContact) + '</span>' : '') +
        (p.utilityContact ? '<span>💡 ' + esc(p.utilityContact) + '</span>' : '') + '</div>' +
      (p.note ? '<div class="t-dim">' + esc(stripHtml(p.note)).slice(0, 60) + '</div>' : '') +
      '<div class="t-foot"><button class="btn sm ghost" data-edit="' + p.id + '">编辑</button>' +
        '<button class="btn sm ghost" data-rooms="' + p.id + '">查看房间</button>' +
        '<button class="btn sm danger" data-del="' + p.id + '">删除</button></div></div>';
  }).join('');
  $('#pageRoot').innerHTML =
    '<div class="page-head"><div class="ico g-amber">🏢</div><div><h2>物业</h2><div class="sub">楼盘 / 楼栋 / 院落</div></div>' +
      '<div class="spacer"></div><button class="btn" id="addProp">＋ 新建物业</button></div>' +
    '<div class="tile-grid">' + (tiles || '') + '</div>' +
    (data.properties.length ? '' : '<div class="glass card">' + emptyBox('🏢', '还没有物业，点击右上角「新建物业」开始登记') + '</div>');

  $('#addProp').addEventListener('click', () => propForm(null));
  $$('#pageRoot [data-edit]').forEach(b => b.addEventListener('click', () => propForm(data.properties.find(p => p.id === +b.dataset.edit))));
  $$('#pageRoot [data-del]').forEach(b => b.addEventListener('click', async () => {
    const p = data.properties.find(x => x.id === +b.dataset.del);
    const rooms = data.rooms.filter(r => r.propertyId === p.id);
    if (rooms.length) return toast('该物业下已有 ' + rooms.length + ' 个房间，请先删除或转移房间。', 'err');
    if (!(await confirmBox('确定删除物业 <b>' + esc(p.name) + '</b> 吗？此操作不可恢复。', { okDanger:true, okText:'删除', title:'删除物业' }))) return;
    await DB.del('properties', p.id); await refreshAfterSave(); toast('已删除', 'ok'); RENDER.properties();
  }));
  $$('#pageRoot [data-rooms]').forEach(b => b.addEventListener('click', () => { go('rooms'); setTimeout(() => { const f = $('#propFilter'); if (f) f.value = b.dataset.rooms, f.dispatchEvent(new Event('change')); }, 60); }));
};
function stripHtml(s){ const d = document.createElement('div'); d.innerHTML = String(s || ''); return d.textContent || ''; }

function propForm(p){
  const isNew = !p;
  p = p || { name:'', type:0, address:'', managementContact:'', utilityContact:'', note:'' };
  formModal({
    title: isNew ? '新建物业' : '编辑物业', icon:'🏢', iconCls:'g-amber',
    fields: '<div class="form-grid">' +
      '<div class="field"><label>物业名称 <span class="req">*</span></label><input class="input" id="f_name" value="' + esc(p.name) + '" placeholder="如：阳光花园"></div>' +
      '<div class="field"><label>物业类型</label><select class="select" id="f_type">' +
        '<option value="0"' + (p.type === 0 ? ' selected' : '') + '>房屋</option>' +
        '<option value="1"' + (p.type === 1 ? ' selected' : '') + '>商铺</option>' +
        '<option value="2"' + (p.type === 2 ? ' selected' : '') + '>公寓</option></select></div>' +
      '<div class="field full"><label>详细地址</label><input class="input" id="f_addr" value="' + esc(p.address || '') + '"></div>' +
      '<div class="field"><label>管理处联系方式</label><input class="input" id="f_mgmt" value="' + esc(p.managementContact || '') + '"></div>' +
      '<div class="field"><label>水电燃气联系方式</label><input class="input" id="f_util" value="' + esc(p.utilityContact || '') + '"></div>' +
      '<div class="field full"><label>备注</label><textarea class="textarea" id="f_note">' + esc(p.note || '') + '</textarea></div>' +
    '</div>',
    onSave: async mask => {
      const name = fv(mask, '#f_name');
      if (!name) return '请填写物业名称。';
      const rec = { name, type:+fv(mask,'#f_type'), address:fv(mask,'#f_addr') || null,
        managementContact:fv(mask,'#f_mgmt') || null, utilityContact:fv(mask,'#f_util') || null, note:fv(mask,'#f_note') || null };
      if (isNew) await DB.put('properties', Object.assign({ createdAt: nowIso() }, rec));
      else await DB.put('properties', Object.assign({}, p, rec));
      await refreshAfterSave(); toast('已保存', 'ok'); RENDER.properties(); return null;
    },
  });
}

/* ============================================================
   房间
   ============================================================ */
RENDER.rooms = async function(){
  await loadAll();
  let filterProp = Number(window.__roomPropFilter || 0);
  if (typeof window.__roomPropFilter === 'undefined') filterProp = 0;
  const list = data.rooms.filter(r => !filterProp || r.propertyId === filterProp);
  const rows = list.map(r => {
    const prop = propOf(r);
    const active = data.leases.find(l => l.roomId === r.id && l.status === 'Active');
    const leasesCount = data.leases.filter(l => l.roomId === r.id).length;
    return '<tr' + (r.isArchived ? ' style="opacity:.55"' : '') + '>' +
      '<td data-label="物业">' + esc(prop ? prop.name : '—') + '</td>' +
      '<td data-label="房号" class="t-primary">' + esc(r.name) + '</td>' +
      '<td data-label="面积" class="num">' + (r.area ? r.area + ' ㎡' : '—') + '</td>' +
      '<td data-label="默认月租" class="num">' + money(r.defaultRent) + '</td>' +
      '<td data-label="状态">' + (r.isArchived ? '<span class="badge st-gray"><i></i>已归档</span>' :
        active ? '<span class="badge st-active"><i></i>在租 · ' + esc(tenantName(active.tenantId)) + '</span>' :
                 '<span class="badge st-info"><i></i>空闲</span>') + '</td>' +
      '<td data-label="租约数" class="num">' + leasesCount + '</td>' +
      '<td class="act-cell" data-label="操作">' +
        '<button class="link-btn" data-edit="' + r.id + '">编辑</button>' +
        '<button class="link-btn" data-arch="' + r.id + '">' + (r.isArchived ? '取消归档' : '归档') + '</button>' +
        '<button class="link-btn danger" data-del="' + r.id + '">删除</button></td></tr>';
  }).join('');
  $('#pageRoot').innerHTML =
    '<div class="page-head"><div class="ico g-green">🚪</div><div><h2>房间</h2><div class="sub">可出租单元，退租后历史保留</div></div>' +
      '<div class="spacer"></div><button class="btn" id="addRoom">＋ 新建房间</button></div>' +
    '<div class="toolbar"><select class="select" id="propFilter"><option value="0">全部物业</option>' +
      data.properties.map(p => '<option value="' + p.id + '"' + (filterProp === p.id ? ' selected' : '') + '>' + esc(p.name) + '</option>').join('') +
    '</select><span class="t-dim">共 ' + list.length + ' 个房间</span></div>' +
    '<div class="glass tbl-wrap"><table class="tbl rsp"><thead><tr><th>物业</th><th>房号</th><th class="num">面积</th><th class="num">默认月租</th><th>状态</th><th class="num">租约数</th><th>操作</th></tr></thead>' +
    '<tbody>' + (rows || '') + '</tbody></table>' + (list.length ? '' : emptyBox('🚪','暂无房间，点击「新建房间」添加')) + '</div>';

  const f = $('#propFilter');
  f.addEventListener('change', () => { window.__roomPropFilter = f.value; RENDER.rooms(); });
  $('#addRoom').addEventListener('click', () => roomForm(null));
  $$('#pageRoot [data-edit]').forEach(b => b.addEventListener('click', () => roomForm(data.rooms.find(r => r.id === +b.dataset.edit))));
  $$('#pageRoot [data-arch]').forEach(b => b.addEventListener('click', async () => {
    const r = data.rooms.find(x => x.id === +b.dataset.arch);
    if (!r.isArchived && data.leases.some(l => l.roomId === r.id && l.status === 'Active'))
      return toast('该房间有生效中的租约，不能归档。', 'err');
    r.isArchived = !r.isArchived;
    await DB.put('rooms', r); await refreshAfterSave(); toast(r.isArchived ? '已归档' : '已取消归档', 'ok'); RENDER.rooms();
  }));
  $$('#pageRoot [data-del]').forEach(b => b.addEventListener('click', async () => {
    const r = data.rooms.find(x => x.id === +b.dataset.del);
    const ls = data.leases.filter(l => l.roomId === r.id);
    if (ls.length) return toast('该房间下已有 ' + ls.length + ' 份租约，请先删除相关租约。', 'err');
    if (!(await confirmBox('确定删除房间 <b>' + esc(r.name) + '</b> 吗？', { okDanger:true, okText:'删除', title:'删除房间' }))) return;
    await DB.del('rooms', r.id); await refreshAfterSave(); toast('已删除', 'ok'); RENDER.rooms();
  }));
};
function roomForm(r){
  const isNew = !r;
  r = r || { propertyId:0, name:'', area:null, defaultRent:0, note:'', isArchived:false };
  const props = data.properties;
  if (!props.length) return toast('请先在「物业」中新建物业。', 'err');
  formModal({
    title: isNew ? '新建房间' : '编辑房间', icon:'🚪', iconCls:'g-green',
    fields: '<div class="form-grid">' +
      '<div class="field"><label>所属物业 <span class="req">*</span></label><select class="select" id="f_prop">' +
        props.map(p => '<option value="' + p.id + '"' + (p.id === (r.propertyId || props[0].id) ? ' selected' : '') + '>' + esc(p.name) + '</option>').join('') + '</select></div>' +
      '<div class="field"><label>房号 / 房间名 <span class="req">*</span></label><input class="input" id="f_name" value="' + esc(r.name) + '" placeholder="如：3栋502"></div>' +
      '<div class="field"><label>建筑面积（㎡，选填）</label><input class="input" id="f_area" type="number" step="0.01" min="0" value="' + (r.area != null ? r.area : '') + '"></div>' +
      '<div class="field"><label>默认月租金（元）</label><input class="input" id="f_rent" type="number" step="0.01" min="0" value="' + r2(r.defaultRent) + '"></div>' +
      '<div class="field full"><label>备注</label><textarea class="textarea" id="f_note">' + esc(r.note || '') + '</textarea></div>' +
    '</div>',
    onSave: async mask => {
      const name = fv(mask, '#f_name');
      if (!name) return '请填写房号。';
      const rent = fn(mask, '#f_rent');
      const rec = { propertyId:+fv(mask,'#f_prop'), name, area:fn(mask,'#f_area'),
        defaultRent: rent == null ? 0 : r2(rent), note:fv(mask,'#f_note') || null };
      if (isNew) await DB.put('rooms', Object.assign({ isArchived:false, createdAt: nowIso() }, rec));
      else await DB.put('rooms', Object.assign({}, r, rec));
      await refreshAfterSave(); toast('已保存', 'ok'); RENDER.rooms(); return null;
    },
  });
}

/* ============================================================
   租客
   ============================================================ */
RENDER.tenants = async function(){
  await loadAll();
  const tiles = data.tenants.map(t => {
    const active = data.leases.find(l => l.tenantId === t.id && l.status === 'Active');
    const leasesCount = data.leases.filter(l => l.tenantId === t.id).length;
    return '<div class="glass tile" data-id="' + t.id + '">' +
      '<div class="t-head"><div class="ico g-pink">👤</div>' +
        '<div><b>' + esc(t.name) + '</b><small>' + (active ? '在租 · ' + esc(roomName(active.roomId)) : (leasesCount ? '历史租客' : '未租')) + '</small></div></div>' +
      '<div class="t-meta">' + (t.phone ? '<span>📞 ' + esc(t.phone) + '</span>' : '') +
        (t.idCard ? '<span>🪪 ' + esc(maskIdCard(t.idCard)) + '</span>' : '') + '</div>' +
      (t.note ? '<div class="t-dim">' + esc(t.note).slice(0, 60) + '</div>' : '') +
      '<div class="t-foot"><button class="btn sm ghost" data-edit="' + t.id + '">编辑</button>' +
        '<button class="btn sm ghost" data-leases="' + t.id + '">租约(' + leasesCount + ')</button>' +
        '<button class="btn sm danger" data-del="' + t.id + '">删除</button></div></div>';
  }).join('');
  $('#pageRoot').innerHTML =
    '<div class="page-head"><div class="ico g-pink">👥</div><div><h2>租客</h2><div class="sub">租客档案可复用，退租后保留</div></div>' +
      '<div class="spacer"></div><button class="btn" id="addTenant">＋ 新建租客</button></div>' +
    '<div class="tile-grid">' + (tiles || '') + '</div>' +
    (data.tenants.length ? '' : '<div class="glass card">' + emptyBox('👥','还没有租客档案，点击「新建租客」添加') + '</div>');

  $('#addTenant').addEventListener('click', () => tenantForm(null));
  $$('#pageRoot [data-edit]').forEach(b => b.addEventListener('click', () => tenantForm(data.tenants.find(t => t.id === +b.dataset.edit))));
  $$('#pageRoot [data-leases]').forEach(b => b.addEventListener('click', () => { go('leases'); }));
  $$('#pageRoot [data-del]').forEach(b => b.addEventListener('click', async () => {
    const t = data.tenants.find(x => x.id === +b.dataset.del);
    const ls = data.leases.filter(l => l.tenantId === t.id);
    if (ls.length) return toast('该租客名下已有 ' + ls.length + ' 份租约，请先删除相关租约。', 'err');
    if (!(await confirmBox('确定删除租客 <b>' + esc(t.name) + '</b> 吗？', { okDanger:true, okText:'删除', title:'删除租客' }))) return;
    await DB.del('tenants', t.id); await refreshAfterSave(); toast('已删除', 'ok'); RENDER.tenants();
  }));
};
function maskIdCard(s){ s = String(s); return s.length > 8 ? s.slice(0, 4) + '**********' + s.slice(-4) : s; }
function tenantForm(t){
  const isNew = !t;
  t = t || { name:'', phone:'', idCard:'', note:'' };
  formModal({
    title: isNew ? '新建租客' : '编辑租客', icon:'👥', iconCls:'g-pink',
    fields: '<div class="form-grid">' +
      '<div class="field"><label>姓名 <span class="req">*</span></label><input class="input" id="f_name" value="' + esc(t.name) + '"></div>' +
      '<div class="field"><label>手机号</label><input class="input" id="f_phone" type="tel" value="' + esc(t.phone || '') + '"></div>' +
      '<div class="field full"><label>身份证号（选填）</label><input class="input" id="f_id" value="' + esc(t.idCard || '') + '"></div>' +
      '<div class="field full"><label>备注</label><textarea class="textarea" id="f_note">' + esc(t.note || '') + '</textarea></div>' +
    '</div>',
    onSave: async mask => {
      const name = fv(mask, '#f_name');
      if (!name) return '请填写姓名。';
      const rec = { name, phone:fv(mask,'#f_phone') || null, idCard:fv(mask,'#f_id') || null, note:fv(mask,'#f_note') || null };
      if (isNew) await DB.put('tenants', Object.assign({ createdAt: nowIso() }, rec));
      else await DB.put('tenants', Object.assign({}, t, rec));
      await refreshAfterSave(); toast('已保存', 'ok'); RENDER.tenants(); return null;
    },
  });
}

/* ============================================================
   租约
   ============================================================ */
const CYCLE_NAMES = { 0:'月付', 1:'季付' };
RENDER.leases = async function(){
  await loadAll();
  let statusFilter = window.__leaseStatusFilter || 'all';
  const list = data.leases.filter(l => statusFilter === 'all' || l.status === Number(statusFilter === 'active' ? 0 : 1))
                          .slice().sort((a,b) => (b.status - a.status) || b.id - a.id);
  const rows = list.map(l => {
    const active = l.status === 0;
    const bills = data.bills.filter(b => b.leaseId === l.id);
    const due = r2(bills.reduce((s,b) => { const st = billStatus(b); return st === 'Paid' ? s : s + (totalOf(b) - paidOf(b.id)); }, 0));
    return '<tr>' +
      '<td data-label="房间" class="t-primary clickable" data-open="' + l.id + '">' + esc(roomName(l.roomId)) + '</td>' +
      '<td data-label="租客">' + esc(tenantName(l.tenantId)) + '</td>' +
      '<td data-label="租期">' + fmtDate(l.startDate) + ' ~ ' + (l.endDate ? fmtDate(l.endDate) : '长期') + '</td>' +
      '<td data-label="月租金" class="num">' + money(l.rentAmount) + '</td>' +
      '<td data-label="押金" class="num">' + money(r2((l.houseDeposit||0) + (l.utilityDeposit||0))) + '</td>' +
      '<td data-label="付租">' + CYCLE_NAMES[l.paymentCycle] + ' · 每月 ' + l.paymentDay + ' 号</td>' +
      '<td data-label="账单" class="num">' + bills.length + ' 张' + (due > 0 ? '<div class="t-dim">待收 ' + money(due) + '</div>' : '') + '</td>' +
      '<td data-label="状态">' + (active ? '<span class="badge st-active"><i></i>生效中</span>' : '<span class="badge st-ended"><i></i>已终止</span>') + '</td>' +
      '<td class="act-cell" data-label="操作">' +
        '<button class="link-btn" data-open="' + l.id + '">详情</button>' +
        '<button class="link-btn" data-edit="' + l.id + '">编辑</button>' +
        (active ? '<button class="link-btn" data-end="' + l.id + '">终止</button>' : '<button class="link-btn" data-end="' + l.id + '">恢复</button>') +
        '<button class="link-btn danger" data-del="' + l.id + '">删除</button></td></tr>';
  }).join('');
  $('#pageRoot').innerHTML =
    '<div class="page-head"><div class="ico g-purple">📄</div><div><h2>租约</h2><div class="sub">签订租约后按周期自动出账</div></div>' +
      '<div class="spacer"></div><button class="btn" id="addLease">＋ 新建租约</button></div>' +
    '<div class="toolbar"><select class="select" id="leaseStatus">' +
      '<option value="all"' + (statusFilter==='all'?' selected':'') + '>全部状态</option>' +
      '<option value="active"' + (statusFilter==='active'?' selected':'') + '>生效中</option>' +
      '<option value="ended"' + (statusFilter==='ended'?' selected':'') + '>已终止</option></select>' +
      '<span class="t-dim">共 ' + list.length + ' 份租约</span></div>' +
    '<div class="glass tbl-wrap"><table class="tbl rsp"><thead><tr><th>房间</th><th>租客</th><th>租期</th><th class="num">月租金</th><th class="num">押金</th><th>付租</th><th class="num">账单</th><th>状态</th><th>操作</th></tr></thead>' +
    '<tbody>' + (rows || '') + '</tbody></table>' + (list.length ? '' : emptyBox('📄','暂无租约，点击「新建租约」签订')) + '</div>';

  $('#leaseStatus').addEventListener('change', e => { window.__leaseStatusFilter = e.target.value; RENDER.leases(); });
  $('#addLease').addEventListener('click', () => leaseForm(null));
  $$('#pageRoot [data-open]').forEach(b => b.addEventListener('click', () => openLeaseDetail(+b.dataset.open)));
  $$('#pageRoot [data-edit]').forEach(b => b.addEventListener('click', () => leaseForm(data.leases.find(l => l.id === +b.dataset.edit))));
  $$('#pageRoot [data-end]').forEach(b => b.addEventListener('click', async () => {
    const l = data.leases.find(x => x.id === +b.dataset.end);
    if (l.status === 0){
      if (!(await confirmBox('确定终止 <b>' + esc(leaseLabel(l)) + '</b> 的租约吗？终止后不再自动出账。', { okDanger:true, okText:'终止租约', title:'终止租约' }))) return;
      l.status = 1;
    } else {
      if (data.leases.some(o => o.id !== l.id && o.roomId === l.roomId && o.status === 0))
        return toast('该房间已有其他生效中的租约，无法恢复。', 'err');
      l.status = 0;
    }
    await DB.put('leases', l); await refreshAfterSave(); toast('已保存', 'ok'); RENDER.leases();
  }));
  $$('#pageRoot [data-del]').forEach(b => b.addEventListener('click', async () => {
    const l = data.leases.find(x => x.id === +b.dataset.del);
    const bills = data.bills.filter(x => x.leaseId === l.id);
    if (bills.length) return toast('该租约下已有 ' + bills.length + ' 张账单，请先删除相关账单后再删除租约。', 'err');
    if (!(await confirmBox('确定删除 <b>' + esc(leaseLabel(l)) + '</b> 的租约吗？', { okDanger:true, okText:'删除', title:'删除租约' }))) return;
    await DB.del('leases', l.id);
    for (const k of data.knowledge.filter(k => k.leaseId === l.id)){ k.leaseId = null; await DB.put('knowledge', k); }
    await refreshAfterSave(); toast('已删除', 'ok'); RENDER.leases();
  }));
};

/* 租期年数 / 租金递增辅助 */
function endFromYears(startStr, n){          /* 起租 + n 年 - 1 天 */
  const d = new Date(startStr + 'T00:00:00');
  d.setFullYear(d.getFullYear() + n);
  d.setDate(d.getDate() - 1);
  return d.getFullYear() + '-' + pad2(d.getMonth() + 1) + '-' + pad2(d.getDate());
}
function leaseTermYears(startStr, endStr){  /* 到期日恰为 起租+N年-1天 时返回 N（1-20），否则 0 */
  for (let i = 1; i <= 20; i++) if (endFromYears(startStr, i) === endStr) return i;
  return 0;
}
function leaseYearOptions(l){
  const opts = [{ v:'lt', t:'长期（无固定期限）' }];
  for (let i = 1; i <= 20; i++) opts.push({ v:String(i), t:i + ' 年' });
  opts.push({ v:'custom', t:'自定义日期' });
  let sel = 'lt';
  if (l && l.id){
    if (!l.endDate) sel = 'lt';
    else { const n = leaseTermYears(l.startDate, l.endDate); sel = n ? String(n) : 'custom'; }
  }
  return opts.map(o => '<option value="' + o.v + '"' + (o.v === sel ? ' selected' : '') + '>' + o.t + '</option>').join('');
}
function escHint(mode){
  if (mode === 1) return { lbl:'每年递增比例（%）', hint:'如填 3：第二年起每月租金 = 上年 × 1.03，逐年复利' };
  if (mode === 2) return { lbl:'每年递增金额（元/月）', hint:'如填 100：第二年起每月租金 = 上年 + 100 元' };
  return { lbl:'每年递增', hint:'选择递增方式后填写' };
}
/* 某自然月的月租（含逐年递增；未配置递增时=租金原值） */
function escalatedMonthlyRent(lease, y, m){
  const base = lease.rentAmount || 0;
  const mode = lease.escMode || 0, val = lease.escValue || 0;
  if (!mode || !val) return r2(base);
  let diff = monthIdx(y, m) - monthIdx(+lease.startDate.slice(0,4), +lease.startDate.slice(5,7));
  if (diff < 0) diff = 0;
  const yi = Math.floor(diff / 12);
  return r2(mode === 1 ? base * Math.pow(1 + val / 100, yi) : base + val * yi);
}

function leaseForm(l){
  const isNew = !l;
  l = l || { roomId:0, tenantId:0, startDate:todayStr(), endDate:'', rentAmount:0, houseDeposit:0, utilityDeposit:0, paymentDay:1, paymentCycle:0, note:'' };
  const rooms = data.rooms.filter(r => !r.isArchived);
  if (!rooms.length) return toast('请先在「房间」中新建房间。', 'err');
  if (!data.tenants.length) return toast('请先在「租客」中新建租客。', 'err');
  formModal({
    title: isNew ? '新建租约' : '编辑租约', icon:'📄', iconCls:'g-purple',
    fields: '<div class="form-grid">' +
      '<div class="field"><label>房间 <span class="req">*</span></label><select class="select" id="f_room">' +
        rooms.map(r => '<option value="' + r.id + '"' + (r.id === (l.roomId || rooms[0].id) ? ' selected' : '') + '>' + esc(roomName(r.id)) + '</option>').join('') + '</select></div>' +
      '<div class="field"><label>租客 <span class="req">*</span></label><select class="select" id="f_tenant">' +
        data.tenants.map(t => '<option value="' + t.id + '"' + (t.id === (l.tenantId || data.tenants[0].id) ? ' selected' : '') + '>' + esc(t.name) + '</option>').join('') + '</select></div>' +
      '<div class="field"><label>起租日期 <span class="req">*</span></label><input class="input" id="f_start" type="date" value="' + fmtDate(l.startDate) + '"></div>' +
      '<div class="field"><label>租期年数</label><select class="select" id="f_years">' + leaseYearOptions(l) + '</select><div class="input-hint">按年选择，自动算出到期日</div></div>' +
      '<div class="field"><label>到期日期</label><input class="input" id="f_end" type="date" value="' + (l.endDate ? fmtDate(l.endDate) : '') + '"><div class="input-hint" id="f_endHint"></div></div>' +
      '<div class="field"><label>每月租金（元） <span class="req">*</span></label><input class="input" id="f_rent" type="number" step="0.01" min="0.01" value="' + r2(l.rentAmount) + '"><div class="input-hint">必填，须大于 0；保存修改后未收款的账单会按新租金重算</div></div>' +
      '<div class="field"><label>租金递增</label><select class="select" id="f_esc">' +
        '<option value="0"' + (!(l.escMode > 0) ? ' selected' : '') + '>不递增</option>' +
        '<option value="1"' + (l.escMode === 1 ? ' selected' : '') + '>按比例（每年 +x%）</option>' +
        '<option value="2"' + (l.escMode === 2 ? ' selected' : '') + '>按金额（每年 +x 元）</option></select></div>' +
      '<div class="field"><label id="f_escLbl">每年递增</label><input class="input" id="f_escval" type="number" step="0.01" min="0" value="' + (l.escMode > 0 ? (l.escValue || 0) : '') + '"' + (!(l.escMode > 0) ? ' disabled' : '') + '><div class="input-hint" id="f_escHint"></div></div>' +
      '<div class="field"><label>付租周期</label><select class="select" id="f_cycle">' +
        '<option value="0"' + (l.paymentCycle === 0 ? ' selected' : '') + '>月付（每月一张账单）</option>' +
        '<option value="1"' + (l.paymentCycle === 1 ? ' selected' : '') + '>季付（三个月一张账单）</option></select></div>' +
      '<div class="field"><label>房屋押金（元）</label><input class="input" id="f_hdep" type="number" step="0.01" min="0" value="' + r2(l.houseDeposit||0) + '"></div>' +
      '<div class="field"><label>水电押金（元）</label><input class="input" id="f_udep" type="number" step="0.01" min="0" value="' + r2(l.utilityDeposit||0) + '"></div>' +
      '<div class="field"><label>每月付租日（1-31）</label><input class="input" id="f_day" type="number" min="1" max="31" value="' + (l.paymentDay || 1) + '"><div class="input-hint">短月自动落到当月最后一天（如 2 月 31 日 → 2 月 28/29 日）</div></div>' +
      '<div class="field"><label>押金合计</label><input class="input" id="f_depot" readonly value="—"></div>' +
      '<div class="field full"><label>备注（如：含宽带 / 押一付三）</label><textarea class="textarea" id="f_note">' + esc(l.note || '') + '</textarea></div>' +
    '</div>',
    onSave: async mask => {
      const start = fv(mask, '#f_start'), end = fv(mask, '#f_end');
      const rent = fn(mask, '#f_rent');
      const day = Math.min(31, Math.max(1, Math.round(fn(mask, '#f_day') || 1)));
      const escMode = +fv(mask, '#f_esc'), escValue = r2(fn(mask, '#f_escval') || 0);
      if (!start) return '请选择起租日期。';
      if (end && end < start) return '到期日期不能早于起租日期。';
      if (!(rent > 0)) return '月租金为必填项，请填写大于 0 的金额。';
      if (escMode && escValue <= 0) return '请填写有效的每年递增' + (escMode === 1 ? '比例' : '金额') + '。';
      const roomId = +fv(mask, '#f_room'), tenantId = +fv(mask, '#f_tenant');
      /* 同一房间同时只允许一份生效租约 */
      const activeOnRoom = data.leases.find(o => o.roomId === roomId && o.status === 0 && (isNew || o.id !== l.id));
      if (activeOnRoom) return '该房间已有生效中的租约（' + esc(leaseLabel(activeOnRoom)) + '），请先终止原租约。';
      const rec = { roomId, tenantId, startDate:start, endDate:end || null, rentAmount:r2(rent),
        houseDeposit:r2(fn(mask,'#f_hdep') || 0), utilityDeposit:r2(fn(mask,'#f_udep') || 0),
        paymentDay:day, paymentCycle:+fv(mask,'#f_cycle'),
        escMode:escMode || 0, escValue:escMode ? escValue : 0, note:fv(mask,'#f_note') || null };
      if (isNew){
        const created = Object.assign({ status:0, createdAt: nowIso() }, rec);
        created.id = await DB.put('leases', created);
        await refreshAfterSave();
        /* 新租约立即补齐到当月的账单 */
        await applyLeaseRecalc(created.id, todayStr().slice(0,7));
        toast('租约已创建，账单已自动生成到当月', 'ok');
        RENDER.leases();
        return null;
      }
      const newLease = Object.assign({}, l, rec);
      /* 编辑：金额/账期关键字段变化时，先预览影响并确认（与 Docker 版规则一致） */
      const thisYM = todayStr().slice(0,7);
      if (leaseBillKeyChanged(l, newLease)){
        const pv = buildLeaseRecalcPreview(newLease, thisYM);
        if (pv.blocked.length) return blockedText(pv.blocked.map(x => x.bill));
        if (pv.hasChanges){
          const ok = await confirmBox(recalcConfirmText(pv).replace(/\n/g, '<br>'),
            { title:'账单将随租约同步', okText:'保存并同步' });
          if (!ok) return null;
        }
      }
      await DB.put('leases', newLease);
      const rr = await applyLeaseRecalc(newLease.id, thisYM);
      if (rr.error) return rr.error;
      if (rr.recalculated + rr.deleted + rr.created > 0){
        let msg = '租约已保存，账单已同步：重算 ' + rr.recalculated + ' 张、删除 ' + rr.deleted + ' 张、补建 ' + rr.created + ' 张';
        msg += rr.frozen > 0 ? '；' + rr.frozen + ' 张已有收款或补录账单保持不变（历史金额锁定）。' : '。';
        await refreshAfterSave(); toast(msg, 'ok');
      } else {
        await refreshAfterSave();
        toast(rr.frozen > 0 ? '租约已保存；' + rr.frozen + ' 张已有收款或补录账单保持不变。' : '已保存', 'ok');
      }
      RENDER.leases(); return null;
    },
  });
  /* 押金合计联动 */
  const upd = () => {
    const h = parseFloat($('#f_hdep', mask0).value) || 0, u = parseFloat($('#f_udep', mask0).value) || 0;
    $('#f_depot', mask0).value = money(r2(h + u));
  };
  const mask0 = $('#fmForm').closest('.modal-mask');
  ['#f_hdep','#f_udep'].forEach(s => $(s, mask0).addEventListener('input', upd)); upd();
  /* 租期年数 → 到期日联动 */
  const syncEnd = () => {
    const mode = $('#f_years', mask0).value, start = $('#f_start', mask0).value;
    const fEnd = $('#f_end', mask0), hint = $('#f_endHint', mask0);
    if (mode === 'custom'){ fEnd.disabled = false; hint.textContent = '可手动选择任意到期日'; return; }
    if (mode === 'lt'){ fEnd.disabled = true; fEnd.value = ''; hint.textContent = '长期租约，无固定到期日'; return; }
    const n = +mode;
    fEnd.disabled = true;
    fEnd.value = start ? endFromYears(start, n) : '';
    hint.textContent = start ? ('自动计算：起租 + ' + n + ' 年 − 1 天') : '请先选择起租日期';
  };
  /* 递增方式 → 递增值联动 */
  const syncEsc = () => {
    const mode = +$('#f_esc', mask0).value, info = escHint(mode);
    $('#f_escLbl', mask0).textContent = info.lbl;
    $('#f_escHint', mask0).textContent = info.hint;
    const v = $('#f_escval', mask0); v.disabled = !mode; if (!mode) v.value = '';
  };
  $('#f_years', mask0).addEventListener('change', syncEnd);
  $('#f_start', mask0).addEventListener('change', syncEnd);
  $('#f_esc', mask0).addEventListener('change', syncEsc);
  syncEnd(); syncEsc();
}

/* 租约详情 */
function openLeaseDetail(id){
  const l = data.leases.find(x => x.id === id);
  if (!l) return;
  const bills = data.bills.filter(b => b.leaseId === l.id).sort((a,b) => String(b.periodStart).localeCompare(String(a.periodStart)));
  const mask = openModal(
    '<div class="modal-head"><div class="ico sm g-purple">📄</div><h3>' + esc(leaseLabel(l)) + '</h3>' +
      '<button class="modal-x" data-act="x">✕</button></div>' +
    '<div class="detail-grid">' +
      dItem('状态', l.status === 0 ? '生效中' : '已终止') +
      dItem('租期', fmtDate(l.startDate) + ' ~ ' + (l.endDate ? fmtDate(l.endDate) : '长期')) +
      dItem('每月租金', money(l.rentAmount)) +
      dItem('租金递增', l.escMode === 1 ? ('每年 +' + (l.escValue || 0) + '%（按比例复利）') : l.escMode === 2 ? ('每年 +' + (l.escValue || 0) + ' 元/月') : '不递增') +
      dItem('押金合计', money(r2((l.houseDeposit||0)+(l.utilityDeposit||0))) + '（房屋 ' + money(l.houseDeposit||0) + ' / 水电 ' + money(l.utilityDeposit||0) + '）') +
      dItem('付租方式', CYCLE_NAMES[l.paymentCycle] + ' · 每月 ' + l.paymentDay + ' 号') +
      dItem('租客联系方式', esc((data.tenants.find(t => t.id === l.tenantId) || {}).phone || '—')) +
    '</div>' +
    (l.note ? '<div class="note-box">' + esc(l.note) + '</div>' : '') +
    '<div class="sub-table-h">🧾 账单（' + bills.length + ' 张）<span class="spacer"></span>' +
      '<button class="btn sm ghost" id="ldBackfill">＋ 补录账单</button></div>' +
    (bills.length ? '<div class="tbl-wrap" style="max-height:300px;overflow-y:auto"><table class="tbl rsp"><thead><tr><th>账期</th><th>到期日</th><th class="num">应收</th><th class="num">已收</th><th>状态</th><th></th></tr></thead><tbody>' +
      bills.map(b => '<tr><td data-label="账期">' + fmtDate(b.periodStart) + ' ~ ' + fmtDate(b.periodEnd) + '</td>' +
        '<td data-label="到期日">' + fmtDate(b.dueDate) + '</td>' +
        '<td data-label="应收" class="num">' + money(totalOf(b)) + '</td>' +
        '<td data-label="已收" class="num">' + money(paidOf(b.id)) + '</td>' +
        '<td data-label="状态">' + statusBadge(b) + '</td>' +
        '<td class="act-cell"><button class="link-btn" data-bill="' + b.id + '">查看</button></td></tr>').join('') +
      '</tbody></table></div>' : emptyBox('🧾','该租约暂无账单')) +
    '<div class="modal-foot"><button class="btn ghost" data-act="close">关闭</button></div>', { size:'lg' });
  mask.addEventListener('click', async e => {
    if (e.target.closest('[data-act="x"]') || e.target.closest('[data-act="close"]')) return closeModal(mask);
    const bb = e.target.closest('[data-bill]');
    if (bb){ closeModal(mask); openBillDetail(+bb.dataset.bill); return; }
    if (e.target.closest('#ldBackfill')){ closeModal(mask); backfillForm(l.id); }
  });
}
function dItem(k, v){ return '<div class="d-item"><div class="k">' + k + '</div><div class="v">' + v + '</div></div>'; }

/* ============================================================
   出账引擎（与后端语义一致）
   ============================================================ */
/* 判断租约在指定自然月是否应出账；返回 {periodStart, periodEnd, dueDate, rentAmount} 或 null */
function tryGetPeriod(lease, y, m){
  const monthStart = y + '-' + pad2(m) + '-01';
  const monthEnd = y + '-' + pad2(m) + '-' + pad2(daysInMonth(y, m));
  const sd = lease.startDate, ed = lease.endDate;
  if (lease.paymentCycle === 0){                       /* 月付 */
    if (monthEnd < sd) return null;
    if (ed && monthStart > ed) return null;
    return { periodStart:monthStart, periodEnd:monthEnd, dueDate:dueDateOf(y, m, lease.paymentDay), rentAmount:escalatedMonthlyRent(lease, y, m) };
  }
  const delta = monthIdx(y, m) - monthIdx(+sd.slice(0,4), +sd.slice(5,7));   /* 季付 */
  if (delta < 0 || delta % 3 !== 0) return null;
  const eIdx = monthIdx(y, m) + 2, eY = Math.floor(eIdx / 12), eM = (eIdx % 12) + 1;
  const periodEnd = eY + '-' + pad2(eM) + '-' + pad2(daysInMonth(eY, eM));
  if (periodEnd < sd) return null;
  if (ed && monthStart > ed) return null;
  let q = 0;                                           /* 季付金额 = 跨入各月月租之和（含逐年递增） */
  for (let k = 0; k < 3; k++){ let mm = m + k, yy = y; if (mm > 12){ mm -= 12; yy++; } q += escalatedMonthlyRent(lease, yy, mm); }
  return { periodStart:monthStart, periodEnd:periodEnd, dueDate:dueDateOf(y, m, lease.paymentDay), rentAmount:r2(q) };
}

/* 为所有生效租约补齐从起租月到指定日的全部账单（幂等），返回新建数 */
async function ensureBillsThrough(throughStr){
  await loadAll();
  const throughYm = throughStr.slice(0,7);
  let created = 0;
  for (const lease of data.leases.filter(l => l.status === 0)){
    const existing = new Set(data.bills.filter(b => b.leaseId === lease.id).map(b => String(b.periodStart).slice(0,7)));
    let y = +lease.startDate.slice(0,4), m = +lease.startDate.slice(5,7);
    while (y + '-' + pad2(m) <= throughYm){
      const plan = tryGetPeriod(lease, y, m);
      if (plan && !existing.has(plan.periodStart.slice(0,7))){
        await DB.put('bills', {
          leaseId:lease.id, roomId:lease.roomId, tenantId:lease.tenantId,
          periodStart:plan.periodStart, periodEnd:plan.periodEnd, dueDate:plan.dueDate,
          rentAmount:plan.rentAmount, totalAmount:plan.rentAmount, paidAmount:0,
          status:0, paidAt:null, note:null, isManual:false, createdAt:nowIso(),
        });
        created++;
      }
      m++; if (m > 12){ m = 1; y++; }
    }
  }
  if (created) await loadAll();
  return created;
}

/* 补录过往账单（含单月）：与后端 CreateManualBillsRangeAsync 语义一致 */
async function backfillBills(leaseId, startYM, endYM, amountPerBill, note){
  const lease = data.leases.find(l => l.id === leaseId);
  if (!lease) return { error:'租约不存在。' };
  if (amountPerBill != null && amountPerBill <= 0) return { error:'补录金额必须大于 0。' };
  if (startYM > endYM) return { error:'开始月份不能晚于结束月份。' };
  const existing = new Set(data.bills.filter(b => b.leaseId === leaseId &&
    String(b.periodStart).slice(0,7) >= startYM && String(b.periodStart).slice(0,7) <= endYM).map(b => String(b.periodStart).slice(0,7)));
  let created = 0, skipped = 0, outOfRange = 0;
  let [y, m] = startYM.split('-').map(Number);
  const [ey, em] = endYM.split('-').map(Number);
  const todayYm = todayStr().slice(0,7);
  while (y * 12 + m <= ey * 12 + em){
    const ym = y + '-' + pad2(m);
    const plan = tryGetPeriod(lease, y, m);
    if (!plan){ outOfRange++; }
    else if (existing.has(ym)){ skipped++; }
    else if (ym > todayYm){ outOfRange++; }         /* 不允许补录未来月份 */
    else {
      const rent = amountPerBill != null ? r2(amountPerBill) : plan.rentAmount;
      await DB.put('bills', {
        leaseId:lease.id, roomId:lease.roomId, tenantId:lease.tenantId,
        periodStart:plan.periodStart, periodEnd:plan.periodEnd, dueDate:plan.dueDate,
        rentAmount:rent, totalAmount:rent, paidAmount:0,
        status:0, paidAt:null, note:note || null, isManual:true, createdAt:nowIso(),
      });
      created++;
    }
    m++; if (m > 12){ m = 1; y++; }
  }
  if (created) await loadAll();
  return { created, skipped, outOfRange };
}

/* ============================================================
   租约修改后重算账单（与后端 BillingService.ApplyLeaseRecalc 语义一致）
   规则：
   - 未收款的自动账单：新条款下仍有账期 → 重算（杂费保留）；无账期且无杂费 → 删除；挂杂费 → 阻止
   - 已有收款（含部分收款）或人工补录账单：冻结，金额/账期不动，仅同步房间/租客冗余
   - 缺失账期：从新起租月补建到当月（重算/冻结账单已覆盖的月份不重复建）
   ============================================================ */
function leaseBillKeyChanged(o, n){
  return (o.startDate || '') !== (n.startDate || '')
    || (o.endDate || '') !== (n.endDate || '')
    || +o.rentAmount !== +n.rentAmount
    || +(o.escMode || 0) !== +(n.escMode || 0)
    || +(o.escValue || 0) !== +(n.escValue || 0)
    || +o.paymentDay !== +n.paymentDay
    || +o.paymentCycle !== +n.paymentCycle;
}

/* 收集一张账单覆盖的全部月份（季账单跨 3 月），写入 set */
function coverMonths(set, periodStart, periodEnd){
  let y = +periodStart.slice(0,4), m = +periodStart.slice(5,7);
  const ey = +periodEnd.slice(0,4), em = +periodEnd.slice(5,7);
  while (y * 12 + m <= ey * 12 + em){
    set.add(y + '-' + pad2(m));
    m++; if (m > 12){ m = 1; y++; }
  }
}

function buildLeaseRecalcPreview(lease, throughYM){
  const bills = data.bills.filter(b => b.leaseId === lease.id);
  const recalc = [], del = [], frozen = [], blocked = [], create = [];
  for (const b of bills){
    const hasPay = paidOf(b.id) > 0.001;
    const hasItems = itemsOf(b.id).length > 0;
    const plan = tryGetPeriod(lease, +b.periodStart.slice(0,4), +b.periodStart.slice(5,7));
    if (hasPay || b.isManual) frozen.push({ bill:b });
    else if (plan) recalc.push({ bill:b, plan });
    else if (hasItems) blocked.push({ bill:b });
    else del.push({ bill:b });
  }
  /* 重算后/冻结账单覆盖的月份不再重复补建 */
  const covered = new Set();
  frozen.forEach(x => coverMonths(covered, x.bill.periodStart, x.bill.periodEnd));
  recalc.forEach(x => coverMonths(covered, x.plan.periodStart, x.plan.periodEnd));
  let y = +lease.startDate.slice(0,4), m = +lease.startDate.slice(5,7);
  const [ty, tm] = throughYM.split('-').map(Number);
  while (y * 12 + m <= ty * 12 + tm){
    const plan = tryGetPeriod(lease, y, m);
    if (plan){
      const pm = new Set();
      coverMonths(pm, plan.periodStart, plan.periodEnd);
      let hit = false;
      for (const k of pm) if (covered.has(k)){ hit = true; break; }
      if (!hit) create.push(plan);
    }
    m++; if (m > 12){ m = 1; y++; }
  }
  return { recalc, del, frozen, blocked, create,
    hasChanges: recalc.length + del.length + create.length > 0 };
}

function billPeriodLabel(b){
  const a = String(b.periodStart).slice(0,7), e = String(b.periodEnd).slice(0,7);
  return a + (e !== a ? ' 至 ' + e : '');
}
function recalcConfirmText(pv){
  const lines = ['修改租金 / 租期 / 付租方式后，系统将按新条款同步该租约的账单：', ''];
  if (pv.recalc.length) lines.push('• ' + pv.recalc.length + ' 张未付账单将重新计算金额 / 到期日（已登记的杂费保留）');
  if (pv.del.length) lines.push('• ' + pv.del.length + ' 张账单已不在新租期 / 付租周期内，将删除：'
    + pv.del.map(x => String(x.bill.periodStart).slice(0,7)).join('、'));
  if (pv.create.length) lines.push('• ' + pv.create.length + ' 张缺失账单将补建：'
    + pv.create.map(p => p.periodStart.slice(0,7)).join('、'));
  lines.push('• ' + pv.frozen.length + ' 张已有收款（含部分收款）或补录账单保持不变，历史金额锁定。');
  lines.push('', '确定保存并同步账单吗？');
  return lines.join('\n');
}
function blockedText(list){
  return '以下账期的账单含杂费明细，调整租期 / 付租周期后将不再成立：'
    + list.map(billPeriodLabel).join('、')
    + '。请先到账单页移除或改挂这些杂费，再修改租约。';
}

/* 应用重算（调用前租约已落库）。返回统计或 { error } */
async function applyLeaseRecalc(leaseId, throughYM){
  await loadAll();
  const lease = data.leases.find(x => x.id === leaseId);
  if (!lease) return { error:'租约不存在。' };
  const pv = buildLeaseRecalcPreview(lease, throughYM);
  if (pv.blocked.length) return { error:blockedText(pv.blocked.map(x => x.bill)) };
  /* 冻结账单：仅同步房间 / 租客冗余 */
  for (const x of pv.frozen){
    const b = await DB.get('bills', x.bill.id);
    if (!b) continue;
    b.roomId = lease.roomId; b.tenantId = lease.tenantId;
    await DB.put('bills', b);
  }
  /* 重算：更新账期/到期日/租金，杂费保留，再重算总额与状态 */
  for (const x of pv.recalc){
    const b = await DB.get('bills', x.bill.id);
    if (!b) continue;
    b.roomId = lease.roomId; b.tenantId = lease.tenantId;
    b.periodStart = x.plan.periodStart;
    b.periodEnd = x.plan.periodEnd;
    b.dueDate = x.plan.dueDate;
    b.rentAmount = x.plan.rentAmount;
    await DB.put('bills', b);
    await loadAll();
    await recalcBill(b.id);
  }
  /* 删除：连同杂费一起删（无收款） */
  for (const x of pv.del){
    for (const it of itemsOf(x.bill.id)) await DB.del('billItems', it.id);
    await DB.del('bills', x.bill.id);
  }
  /* 补建 */
  for (const plan of pv.create){
    await DB.put('bills', {
      leaseId:lease.id, roomId:lease.roomId, tenantId:lease.tenantId,
      periodStart:plan.periodStart, periodEnd:plan.periodEnd, dueDate:plan.dueDate,
      rentAmount:plan.rentAmount, totalAmount:plan.rentAmount, paidAmount:0,
      status:0, paidAt:null, note:null, isManual:false, createdAt:nowIso(),
    });
  }
  await loadAll();
  return { recalculated:pv.recalc.length, deleted:pv.del.length,
    created:pv.create.length, frozen:pv.frozen.length };
}

function backfillForm(leaseId){
  if (!data.leases.length) return toast('请先创建租约。', 'err');
  const nowY = new Date().getFullYear();
  const yearOpts = n => { let s = ''; for (let i = nowY - 6; i <= nowY + 1; i++) s += '<option value="' + i + '"' + (i === n ? ' selected' : '') + '>' + i + ' 年</option>'; return s; };
  const monthOpts = n => { let s = ''; for (let i = 1; i <= 12; i++) s += '<option value="' + i + '"' + (i === n ? ' selected' : '') + '>' + i + ' 月</option>'; return s; };
  const today = new Date();
  formModal({
    title:'补录账单（记录历史欠款）', icon:'🧾', iconCls:'g-amber',
    fields: '<div class="form-grid">' +
      '<div class="field"><label>租约 <span class="req">*</span></label><select class="select" id="f_lease">' +
        data.leases.map(l => '<option value="' + l.id + '"' + (l.id === leaseId ? ' selected' : '') + '>' + esc(leaseLabel(l)) + '（' + CYCLE_NAMES[l.paymentCycle] + '）</option>').join('') + '</select></div>' +
      '<div class="field"></div>' +
      '<div class="field"><label>开始月份 <span class="req">*</span></label><div style="display:flex;gap:8px"><select class="select" id="f_sy">' + yearOpts(nowY) + '</select><select class="select" id="f_sm">' + monthOpts(today.getMonth()+1) + '</select></div></div>' +
      '<div class="field"><label>结束月份 <span class="req">*</span></label><div style="display:flex;gap:8px"><select class="select" id="f_ey">' + yearOpts(nowY) + '</select><select class="select" id="f_em">' + monthOpts(today.getMonth()+1) + '</select></div></div>' +
      '<div class="field"><label>每张账单金额（留空=按租约标准自动计算）</label><input class="input" id="f_amt" type="number" step="0.01" min="0" placeholder="如：1500"></div>' +
      '<div class="field full"><label>备注</label><input class="input" id="f_note" placeholder="如：历史欠款"></div>' +
    '</div><div class="input-hint">逐月判定：与租期无交集的月份跳过；已有账单的月份自动跳过（不会重复）；季付租约只在账期首月出账。</div>',
    onSave: async mask => {
      const leaseIdV = +fv(mask, '#f_lease');
      const startYM = fv(mask,'#f_sy') + '-' + pad2(+fv(mask,'#f_sm'));
      const endYM = fv(mask,'#f_ey') + '-' + pad2(+fv(mask,'#f_em'));
      const amt = fn(mask, '#f_amt');
      const r = await backfillBills(leaseIdV, startYM, endYM, amt, fv(mask, '#f_note') || null);
      if (r.error) return r.error;
      const parts = [];
      if (r.created) parts.push('新建 ' + r.created + ' 张');
      if (r.skipped) parts.push('跳过已存在 ' + r.skipped + ' 个月');
      if (r.outOfRange) parts.push('租期外 ' + r.outOfRange + ' 个月');
      toast(parts.length ? '补录完成：' + parts.join('，') : '没有需要补录的月份', r.created ? 'ok' : 'err');
      if (currentPage === 'bills') RENDER.bills();
      return null;
    },
  });
}

/* ============================================================
   账单
   ============================================================ */
const METHOD_NAMES = { 0:'微信', 1:'支付宝', 2:'银行转账', 3:'现金', 4:'其他' };
function methodLabel(m){ return METHOD_NAMES[m] || '其他'; }

RENDER.bills = async function(){
  await ensureBillsThrough(todayStr());
  await loadAll();
  const stFilter = window.__billStFilter || 'all';
  const leaseFilter = +(window.__billLeaseFilter || 0);
  const kw = (window.__billKw || '').toLowerCase();
  let list = data.bills.slice();
  if (stFilter === 'overdue') list = list.filter(isOverdue);
  else if (stFilter !== 'all') list = list.filter(b => billStatus(b) === stFilter);
  if (leaseFilter) list = list.filter(b => b.leaseId === leaseFilter);
  if (kw) list = list.filter(b => (roomName(b.roomId) + tenantName(b.tenantId)).toLowerCase().indexOf(kw) >= 0);
  list.sort((a,b) => String(b.periodStart).localeCompare(String(a.periodStart)) || b.id - a.id);

  const sel = window.__billSel = window.__billSel || new Set();
  const rows = list.map(b => {
    const total = totalOf(b), paid = paidOf(b.id), remain = r2(Math.max(0, total - paid));
    return '<tr>' +
      '<td data-label="选择" class="act-cell"><input type="checkbox" data-sel="' + b.id + '"' + (sel.has(b.id) ? ' checked' : '') + ' style="accent-color:#C9A05E;width:16px;height:16px"></td>' +
      '<td data-label="房间" class="t-primary clickable" data-open="' + b.id + '">' + esc(roomName(b.roomId)) +
        '<div class="t-dim">' + esc(tenantName(b.tenantId)) + '</div></td>' +
      '<td data-label="账期">' + fmtDate(b.periodStart).slice(0,7) + (b.paymentCycleHint || '') +
        '<div class="t-dim">' + fmtDate(b.periodStart) + ' ~ ' + fmtDate(b.periodEnd) + '</div></td>' +
      '<td data-label="到期日">' + fmtDate(b.dueDate) + '</td>' +
      '<td data-label="租金" class="num">' + money(b.rentAmount) + '</td>' +
      '<td data-label="杂费" class="num">' + money(extraOf(b.id)) + '</td>' +
      '<td data-label="应收" class="num t-primary">' + money(total) + '</td>' +
      '<td data-label="已收" class="num">' + money(paid) + (remain > 0 ? '<div class="t-dim">余 ' + money(remain) + '</div>' : '') + '</td>' +
      '<td data-label="状态">' + statusBadge(b) + '</td>' +
      '<td class="act-cell" data-label="操作"><button class="link-btn" data-open="' + b.id + '">详情</button></td></tr>';
  }).join('');

  const selCount = Array.from(sel).filter(id => data.bills.some(b => b.id === id && r2(totalOf(b) - paidOf(b.id)) > 0.001)).length;
  $('#pageRoot').innerHTML =
    '<div class="page-head"><div class="ico g-amber">🧾</div><div><h2>账单</h2><div class="sub">按租约周期自动出账，支持补录与批量结清</div></div>' +
      '<div class="spacer"></div><button class="btn ghost" id="backfillBtn">📥 补录账单</button>' +
      '<button class="btn" id="settleBtn"' + (selCount ? '' : ' disabled') + '>✅ 结清所选(' + selCount + ')</button></div>' +
    '<div class="toolbar">' +
      '<select class="select" id="stFilter">' +
        '<option value="all"' + (stFilter==='all'?' selected':'') + '>全部状态</option>' +
        '<option value="Unpaid"' + (stFilter==='Unpaid'?' selected':'') + '>未付</option>' +
        '<option value="Partial"' + (stFilter==='Partial'?' selected':'') + '>部分已付</option>' +
        '<option value="Paid"' + (stFilter==='Paid'?' selected':'') + '>已付清</option>' +
        '<option value="overdue"' + (stFilter==='overdue'?' selected':'') + '>已逾期</option></select>' +
      '<select class="select" id="leaseFilter"><option value="0">全部租约</option>' +
        data.leases.map(l => '<option value="' + l.id + '"' + (leaseFilter === l.id ? ' selected' : '') + '>' + esc(leaseLabel(l)) + '</option>').join('') + '</select>' +
      '<input class="input grow" id="kw" placeholder="搜索房间 / 租客…" value="' + esc(window.__billKw || '') + '">' +
      '<span class="t-dim">共 ' + list.length + ' 张</span></div>' +
    '<div class="glass tbl-wrap"><table class="tbl rsp"><thead><tr><th style="width:34px"></th><th>房间 / 租客</th><th>账期</th><th>到期日</th><th class="num">租金</th><th class="num">杂费</th><th class="num">应收</th><th class="num">已收</th><th>状态</th><th>操作</th></tr></thead>' +
    '<tbody>' + (rows || '') + '</tbody></table>' + (list.length ? '' : emptyBox('🧾','没有符合条件的账单')) + '</div>';

  $('#stFilter').addEventListener('change', e => { window.__billStFilter = e.target.value; RENDER.bills(); });
  $('#leaseFilter').addEventListener('change', e => { window.__billLeaseFilter = e.target.value; RENDER.bills(); });
  $('#kw').addEventListener('input', e => { window.__billKw = e.target.value; clearTimeout(window.__kwT); window.__kwT = setTimeout(RENDER.bills, 350); });
  $('#backfillBtn').addEventListener('click', () => backfillForm(leaseFilter || null));
  $('#settleBtn').addEventListener('click', batchSettleDialog);
  $$('#pageRoot [data-sel]').forEach(cb => cb.addEventListener('change', () => { cb.checked ? sel.add(+cb.dataset.sel) : sel.delete(+cb.dataset.sel); RENDER.bills(); }));
  $$('#pageRoot [data-open]').forEach(b => b.addEventListener('click', () => openBillDetail(+b.dataset.open)));
};

/* 批量结清 */
function batchSettleDialog(){
  const sel = window.__billSel || new Set();
  const ids = Array.from(sel).filter(id => { const b = data.bills.find(x => x.id === id); return b && r2(totalOf(b) - paidOf(b.id)) > 0.001; });
  if (!ids.length) return toast('请先勾选未付清的账单。', 'err');
  formModal({
    title:'批量结清 ' + ids.length + ' 张账单', icon:'✅', iconCls:'g-green',
    fields: '<div class="form-grid">' +
      '<div class="field"><label>收款方式</label><select class="select" id="f_method">' +
        Object.keys(METHOD_NAMES).map(k => '<option value="' + k + '">' + METHOD_NAMES[k] + '</option>').join('') + '</select></div>' +
      '<div class="field"><label>收款日期模式</label><select class="select" id="f_dmode">' +
        '<option value="0">统一日期（全部同一天）</option>' +
        '<option value="1">账单当月（按各账单到期日）</option></select></div>' +
      '<div class="field"><label>收款日期</label><input class="input" id="f_date" type="date" value="' + todayStr() + '"></div>' +
    '</div><div class="input-hint" id="bsHint">将按每张账单的剩余应收逐张登记收款，全部使用同一收款日期（备注「批量结清」）。</div>',
    okText:'立即结清',
    onSave: async mask => {
      const method = +fv(mask, '#f_method'), dmode = +fv(mask, '#f_dmode'), date = fv(mask, '#f_date');
      if (dmode === 0 && !date) return '请选择收款日期。';
      let settled = 0, total = 0;
      for (const id of ids){
        const b = data.bills.find(x => x.id === id);
        if (!b) continue;
        const remain = r2(totalOf(b) - paidOf(b.id));
        if (remain <= 0.001) continue;
        const paidAt = dmode === 1 ? (b.dueDate || date || todayStr()) : date;   /* 账单当月=各账单到期日 */
        await DB.put('payments', { billId:b.id, amount:remain, paidAt, method, note:'批量结清', createdAt:nowIso() });
        await recalcBill(b.id);
        settled++; total = r2(total + remain);
      }
      sel.clear();
      await refreshAfterSave();
      toast('已结清 ' + settled + ' 张账单，共收款 ' + money(total), 'ok');
      RENDER.bills(); return null;
    },
  });
  /* 收款日期模式联动：账单当月时禁用手动日期 */
  const m0 = $('#fmForm').closest('.modal-mask');
  $('#f_dmode', m0).addEventListener('change', e => {
    const one = +e.target.value === 1;
    $('#f_date', m0).disabled = one;
    $('#bsHint', m0).textContent = one
      ? '将按每张账单各自的到期日（即账单当月）登记收款日期。'
      : '将按每张账单的剩余应收逐张登记收款，全部使用同一收款日期（备注「批量结清」）。';
  });
}

/* 账单统一重算（金额收口）：总额=租金+杂费；已收=Σ收款；状态迁移；超收拒绝 */
async function recalcBill(billId){
  const bill = data.bills.find(b => b.id === billId);
  if (!bill) return;
  const total = r2(bill.rentAmount + extraOf(bill.id));
  const pays = paymentsOf(billId);
  const paid = r2(pays.reduce((s,p) => s + p.amount, 0));
  if (paid > total + 0.001) throw new Error('已收金额超过应收总额，请先撤销多余的收款记录。');
  bill.totalAmount = total; bill.paidAmount = paid;
  if (paid <= 0.001){ bill.status = 0; bill.paidAt = null; }
  else if (paid < total - 0.001){ bill.status = 1; bill.paidAt = null; }
  else {
    bill.status = 2;
    if (!bill.paidAt) bill.paidAt = pays.length ? pays.map(p => p.paidAt).sort().pop() : todayStr();
  }
  await DB.put('bills', bill);
}

/* 账单详情 */
function openBillDetail(id){
  const b = data.bills.find(x => x.id === id);
  if (!b) return;
  const items = itemsOf(b.id), pays = paymentsOf(b.id).slice().sort((x,y) => String(y.paidAt).localeCompare(String(x.paidAt)) || y.id - x.id);
  const total = totalOf(b), paid = paidOf(b.id), remain = r2(Math.max(0, total - paid));
  const mask = openModal(
    '<div class="modal-head"><div class="ico sm g-amber">🧾</div>' +
      '<h3>' + esc(roomName(b.roomId)) + ' · ' + esc(tenantName(b.tenantId)) + '</h3>' + statusBadge(b) +
      '<button class="modal-x" data-act="x">✕</button></div>' +
    '<div class="detail-grid">' +
      dItem('账期', fmtDate(b.periodStart) + ' ~ ' + fmtDate(b.periodEnd)) +
      dItem('到期日', fmtDate(b.dueDate)) +
      dItem('租金', money(b.rentAmount) + (b.isManual ? ' <span class="badge st-gray">补录</span>' : '')) +
      dItem('杂费合计', money(extraOf(b.id))) +
      dItem('应收总额', '<span class="t-primary">' + money(total) + '</span>') +
      dItem('已收 / 剩余', money(paid) + ' / ' + money(remain)) +
    '</div>' +
    (b.note ? '<div class="note-box">' + esc(b.note) + '</div>' : '') +
    '<div class="modal-foot" style="justify-content:flex-start">' +
      '<button class="btn sm" data-act="pay">💰 登记收款</button>' +
      '<button class="btn sm ghost" data-act="edit">✏️ 编辑租金/备注</button>' +
      '<button class="btn sm ghost" data-act="img">🖼️ 账单图</button>' +
      '<button class="btn sm danger" data-act="del">🗑 删除账单</button></div>' +
    '<div class="sub-table-h">📋 杂费明细<span class="spacer"></span><button class="btn sm ghost" id="addItemBtn">＋ 加杂费</button></div>' +
    '<div class="tbl-wrap"><table class="tbl rsp"><thead><tr><th>费用名称</th><th class="num">金额</th><th>备注</th><th></th></tr></thead><tbody>' +
      (items.length ? items.map(i => '<tr><td data-label="名称" class="t-primary">' + esc(i.name) + '</td>' +
        '<td data-label="金额" class="num">' + money(i.amount) + '</td>' +
        '<td data-label="备注" class="t-dim">' + esc(i.remark || '—') + '</td>' +
        '<td class="act-cell"><button class="link-btn danger" data-delitem="' + i.id + '">删除</button></td></tr>').join('')
        : '<tr><td colspan="4" class="t-dim" style="text-align:center;padding:14px">暂无杂费，可在下方添加水电费等</td></tr>') +
    '</tbody></table></div>' +
    '<div class="sub-table-h">💰 收款记录（' + pays.length + '）</div>' +
    '<div class="tbl-wrap"><table class="tbl rsp"><thead><tr><th>日期</th><th class="num">金额</th><th>方式</th><th>备注</th><th></th></tr></thead><tbody>' +
      (pays.length ? pays.map(p => '<tr><td data-label="日期">' + fmtDate(p.paidAt) + '</td>' +
        '<td data-label="金额" class="num t-primary">' + money(p.amount) + '</td>' +
        '<td data-label="方式">' + methodLabel(p.method) + '</td>' +
        '<td data-label="备注" class="t-dim">' + esc(p.note || '—') + '</td>' +
        '<td class="act-cell"><button class="link-btn danger" data-delpay="' + p.id + '">撤销</button></td></tr>').join('')
        : '<tr><td colspan="5" class="t-dim" style="text-align:center;padding:14px">暂无收款</td></tr>') +
    '</tbody></table></div>', { size:'lg' });

  const refresh = async () => { await refreshAfterSave(); closeModal(mask); openBillDetail(id); if (currentPage === 'bills') RENDER.bills(); };
  mask.addEventListener('click', async e => {
    if (e.target.closest('[data-act="x"]')) return closeModal(mask);
    const act = (e.target.closest('[data-act]') || {}).dataset;
    if (act && act.act === 'pay'){ paymentForm(b.id); return; }
    if (act && act.act === 'edit'){ editBillForm(b.id); return; }
    if (act && act.act === 'img'){ showBillImage(b.id); return; }
    if (act && act.act === 'del'){
      if (pays.length) return toast('该账单已有收款记录，请先撤销全部收款后再删除。', 'err');
      if (!(await confirmBox('确定删除该账单吗？杂费明细将一并删除。若为自动出账范围内的账单，下次补账时会按租约标准重新生成（如需当月免租，可把租金改为 0）。', { okDanger:true, okText:'删除', title:'删除账单' }))){
        return;
      }
      for (const i of items) await DB.del('billItems', i.id);
      await DB.del('bills', b.id);
      await refreshAfterSave(); closeModal(mask); toast('账单已删除', 'ok'); if (currentPage === 'bills') RENDER.bills();
      return;
    }
    if (e.target.closest('#addItemBtn')) return itemForm(b.id);
    const di = e.target.closest('[data-delitem]');
    if (di){
      await DB.del('billItems', +di.dataset.delitem);
      try { await recalcBill(b.id); } catch(err){ await refreshAfterSave(); return toast(err.message, 'err'); }
      await refreshAfterSave(); closeModal(mask); openBillDetail(id); if (currentPage === 'bills') RENDER.bills();
      return;
    }
    const dp = e.target.closest('[data-delpay]');
    if (dp){
      if (!(await confirmBox('确定撤销这笔收款吗？账单状态会自动回迁。', { okDanger:true, okText:'撤销收款', title:'撤销收款' }))) return;
      await DB.del('payments', +dp.dataset.delpay);
      await recalcBill(b.id);
      await refreshAfterSave(); closeModal(mask); openBillDetail(id); if (currentPage === 'bills') RENDER.bills();
      return;
    }
  });
}

/* 登记收款 */
function paymentForm(billId){
  const b = data.bills.find(x => x.id === billId);
  const remain = r2(totalOf(b) - paidOf(b.id));
  formModal({
    title:'登记收款 · ' + roomName(b.roomId) + ' ' + fmtDate(b.periodStart).slice(0,7), icon:'💰', iconCls:'g-green',
    fields: '<div class="form-grid">' +
      '<div class="field"><label>收款金额（元） <span class="req">*</span></label><input class="input" id="f_amt" type="number" step="0.01" min="0" value="' + remain + '"><div class="input-hint">剩余应收 ' + money(remain) + '，支持部分收款</div></div>' +
      '<div class="field"><label>收款日期 <span class="req">*</span></label><input class="input" id="f_date" type="date" value="' + todayStr() + '"></div>' +
      '<div class="field"><label>收款方式</label><select class="select" id="f_method">' +
        Object.keys(METHOD_NAMES).map(k => '<option value="' + k + '"' + (k === '0' ? ' selected' : '') + '>' + METHOD_NAMES[k] + '</option>').join('') + '</select></div>' +
      '<div class="field"><label>备注</label><input class="input" id="f_note"></div>' +
    '</div>',
    okText:'登记收款',
    onSave: async mask => {
      const amt = fn(mask, '#f_amt'), date = fv(mask, '#f_date');
      if (amt == null || amt <= 0) return '收款金额必须大于 0。';
      if (amt > remain + 0.001) return '收款金额超出剩余应收（剩余 ' + money(remain) + '）。';
      if (!date) return '请选择收款日期。';
      await DB.put('payments', { billId, amount:r2(amt), paidAt:date, method:+fv(mask,'#f_method'), note:fv(mask,'#f_note') || null, createdAt:nowIso() });
      await recalcBill(billId);
      await refreshAfterSave(); toast('已登记收款 ' + money(amt), 'ok');
      closeModal(mask); openBillDetail(billId); if (currentPage === 'bills') RENDER.bills();
      return null;
    },
  });
}

/* 编辑租金 / 备注（账期不可改） */
function editBillForm(billId){
  const b = data.bills.find(x => x.id === billId);
  formModal({
    title:'编辑账单 · ' + fmtDate(b.periodStart).slice(0,7), icon:'✏️', iconCls:'g-amber',
    fields: '<div class="detail-grid">' +
      dItem('账期（不可修改）', fmtDate(b.periodStart) + ' ~ ' + fmtDate(b.periodEnd)) +
      dItem('到期日（不可修改）', fmtDate(b.dueDate)) +
      dItem('当前杂费合计', money(extraOf(billId))) + '</div>' +
      '<div class="form-grid">' +
      '<div class="field"><label>租金金额（元） <span class="req">*</span></label><input class="input" id="f_rent" type="number" step="0.01" min="0" value="' + r2(b.rentAmount) + '"><div class="input-hint">如需当月免租，可改为 0</div></div>' +
      '<div class="field"><label>备注</label><input class="input" id="f_note" value="' + esc(b.note || '') + '"></div>' +
    '</div>',
    okText:'保存',
    onSave: async mask => {
      const rent = fn(mask, '#f_rent');
      if (rent == null || rent < 0) return '租金金额不能为负数。';
      const rec = Object.assign({}, b, { rentAmount:r2(rent), note:fv(mask,'#f_note') || null });
      const total = r2(rent + extraOf(billId));
      if (paidOf(billId) > total + 0.001) return '已收金额超过应收总额，请先撤销多余的收款记录。';
      await DB.put('bills', rec);
      await recalcBill(billId);
      await refreshAfterSave(); toast('已保存', 'ok');
      closeModal(mask); openBillDetail(billId); if (currentPage === 'bills') RENDER.bills();
      return null;
    },
  });
}

/* 添加杂费 */
function itemForm(billId){
  formModal({
    title:'添加杂费明细', icon:'📋', iconCls:'g-cyan',
    fields: '<div class="form-grid">' +
      '<div class="field"><label>费用名称 <span class="req">*</span></label><input class="input" id="f_name" placeholder="如：电费 / 物业费"></div>' +
      '<div class="field"><label>金额（元） <span class="req">*</span></label><input class="input" id="f_amt" type="number" step="0.01" min="0"></div>' +
      '<div class="field full"><label>备注（如水电表读数）</label><input class="input" id="f_remark"></div>' +
    '</div>',
    okText:'添加',
    onSave: async mask => {
      const name = fv(mask, '#f_name'), amt = fn(mask, '#f_amt');
      if (!name) return '请填写费用名称。';
      if (amt == null || amt < 0) return '请填写有效金额。';
      const b = data.bills.find(x => x.id === billId);
      if (r2(paidOf(billId)) > r2(b.rentAmount + amt) + 0.001)
        return '已收金额超过应收总额，请先撤销多余的收款记录。';
      await DB.put('billItems', { billId, name, amount:r2(amt), remark:fv(mask,'#f_remark') || null });
      await recalcBill(billId);
      await refreshAfterSave(); toast('已添加杂费', 'ok');
      closeModal(mask); openBillDetail(billId); if (currentPage === 'bills') RENDER.bills();
      return null;
    },
  });
}

/* ============================================================
   账单图（Canvas 绘制，可下载 PNG）
   ============================================================ */
function wrapText(ctx, text, maxW){
  const lines = []; let cur = '';
  for (const ch of String(text || '')){
    if (ctx.measureText(cur + ch).width > maxW && cur){ lines.push(cur); cur = ch; } else cur += ch;
  }
  if (cur) lines.push(cur);
  return lines;
}
async function showBillImage(billId){
  const b = data.bills.find(x => x.id === billId);
  const items = itemsOf(billId);
  const pays = paymentsOf(billId);
  const total = totalOf(b), paid = r2(pays.reduce((s,p) => s + p.amount, 0));
  const landlord = settingVal(SET.LANDLORD, '房东');
  const notice = settingVal(SET.NOTICE, DEFAULT_NOTICE);
  const qrSrc = settingVal(SET.QR, null);
  const room = roomOf(b.roomId), prop = room ? propOf(room) : null;

  const W = 750, H = 1180;
  const mask = openModal(
    '<div class="modal-head"><div class="ico sm g-amber">🖼️</div><h3>账单图预览</h3>' +
      '<button class="modal-x" data-act="x">✕</button></div>' +
    '<div style="text-align:center"><canvas id="billCanvas" style="max-width:100%;border-radius:14px;box-shadow:var(--shadow)"></canvas></div>' +
    '<div class="modal-foot"><button class="btn ghost" data-act="x">关闭</button>' +
      '<button class="btn" id="dlPng">⬇️ 下载 PNG</button></div>', { size:'lg' });
  mask.addEventListener('click', e => { if (e.target.closest('[data-act="x"]')) closeModal(mask); });

  const canvas = $('#billCanvas', mask);
  const dpr = Math.min(2, window.devicePixelRatio || 1);
  canvas.width = W * dpr; canvas.height = H * dpr;
  canvas.style.width = Math.min(560, W) + 'px';
  const ctx = canvas.getContext('2d');
  ctx.scale(dpr, dpr);

  /* 底色 */
  ctx.fillStyle = '#ffffff'; ctx.fillRect(0, 0, W, H);
  ctx.fillStyle = '#f6f8fd'; ctx.fillRect(0, 0, W, 8);

  const qrImg = await new Promise(res => { if (!qrSrc){ res(null); return; } const im = new Image(); im.onload = () => res(im); im.onerror = () => res(null); im.src = qrSrc; });

  let y = 66;
  ctx.textAlign = 'center'; ctx.fillStyle = '#0f172a';
  ctx.font = '700 30px -apple-system,"PingFang SC","Microsoft YaHei",sans-serif';
  ctx.fillText(landlord, W/2, y);
  y += 34;
  ctx.font = '600 22px -apple-system,"PingFang SC","Microsoft YaHei",sans-serif';
  ctx.fillStyle = '#475569';
  ctx.fillText('租 金 账 单', W/2, y);
  y += 14;
  ctx.font = '400 15px -apple-system,sans-serif'; ctx.fillStyle = '#94a3b8';
  ctx.fillText('账单编号 #' + b.id + ' · ' + todayStr(), W/2, y);
  y += 24;
  ctx.strokeStyle = '#e2e8f0'; ctx.lineWidth = 1;
  ctx.beginPath(); ctx.moveTo(48, y); ctx.lineTo(W-48, y); ctx.stroke();

  const rowH = 40; y += 26;
  const infoRow = (k, v) => {
    ctx.textAlign = 'left'; ctx.font = '600 16px -apple-system,"PingFang SC",sans-serif';
    ctx.fillStyle = '#64748b'; ctx.fillText(k, 52, y);
    ctx.textAlign = 'right'; ctx.font = '600 17px -apple-system,"PingFang SC",sans-serif';
    ctx.fillStyle = '#0f172a'; ctx.fillText(String(v).slice(0, 30), W-52, y);
    y += rowH;
  };
  infoRow('物业 / 房间', (prop ? prop.name + ' · ' : '') + (room ? room.name : '—'));
  infoRow('租客', tenantName(b.tenantId) + ((data.tenants.find(t => t.id === b.tenantId) || {}).phone ? '（' + (data.tenants.find(t => t.id === b.tenantId) || {}).phone + '）' : ''));
  infoRow('账期', fmtDate(b.periodStart) + ' 至 ' + fmtDate(b.periodEnd));
  infoRow('应付截止', fmtDate(b.dueDate));
  y += 6;
  ctx.beginPath(); ctx.moveTo(48, y); ctx.lineTo(W-48, y); ctx.stroke();

  y += 30;
  ctx.textAlign = 'left'; ctx.font = '700 17px -apple-system,"PingFang SC",sans-serif'; ctx.fillStyle = '#334155';
  ctx.fillText('费用明细', 52, y); y += 14;
  const drawLine = (name, amount, strong) => {
    y += 32;
    ctx.font = (strong ? '700 ' : '400 ') + '16px -apple-system,"PingFang SC",sans-serif';
    ctx.fillStyle = strong ? '#0f172a' : '#334155';
    ctx.textAlign = 'left'; ctx.fillText(name.slice(0, 20), 52, y);
    ctx.textAlign = 'right'; ctx.fillText(money(amount), W-52, y);
  };
  drawLine('租金' + (b.isManual ? '（补录）' : ''), b.rentAmount, false);
  items.forEach(i => drawLine(i.name + (i.remark ? '（' + i.remark + '）' : ''), i.amount, false));
  y += 12; ctx.strokeStyle = '#e2e8f0'; ctx.beginPath(); ctx.moveTo(48, y); ctx.lineTo(W-48, y); ctx.stroke();
  drawLine('应收合计', total, true);
  drawLine('已收金额', paid, false);
  drawLine('剩余待收', r2(Math.max(0, total - paid)), true);

  y += 34;
  ctx.textAlign = 'left'; ctx.font = '400 15px -apple-system,"PingFang SC",sans-serif'; ctx.fillStyle = '#64748b';
  wrapText(ctx, notice, W - 104).forEach(line => { ctx.fillText(line, 52, y); y += 24; });

  if (qrImg){
    const qs = 190, qx = W - 52 - qs, qy = H - 52 - qs;
    ctx.fillStyle = '#fff'; ctx.strokeStyle = '#e2e8f0';
    ctx.fillRect(qx - 8, qy - 8, qs + 16, qs + 16); ctx.strokeRect(qx - 8, qy - 8, qs + 16, qs + 16);
    ctx.drawImage(qrImg, qx, qy, qs, qs);
    ctx.textAlign = 'center'; ctx.font = '600 14px -apple-system,"PingFang SC",sans-serif'; ctx.fillStyle = '#64748b';
    ctx.fillText('扫码支付', qx + qs/2, qy - 18);
  }
  ctx.textAlign = 'left'; ctx.font = '400 13px -apple-system,sans-serif'; ctx.fillStyle = '#b6c0d0';
  ctx.fillText('由「物业管理台账」生成 · ' + nowIso(), 52, H - 34);

  $('#dlPng', mask).addEventListener('click', () => {
    const a = document.createElement('a');
    a.href = canvas.toDataURL('image/png');
    a.download = '账单-' + (room ? room.name : '') + '-' + tenantName(b.tenantId) + '-' + b.periodStart.slice(0,7) + '.png';
    a.click();
  });
}

/* ============================================================
   经验库
   ============================================================ */
const KNOW_NAMES = { 0:'出租经验', 1:'退租经验', 2:'其他' };
RENDER.knowledge = async function(){
  await loadAll();
  const cat = window.__knowCat || 'all';
  const list = data.knowledge.filter(k => cat === 'all' || String(k.category) === cat)
                             .slice().sort((a,b) => String(b.updatedAt).localeCompare(String(a.updatedAt)) || b.id - a.id);
  const cards = list.map(k => {
    const lease = k.leaseId ? data.leases.find(l => l.id === k.leaseId) : null;
    return '<div class="glass tile" style="grid-column:span 1">' +
      '<div class="t-head"><div class="ico g-cyan">💡</div>' +
        '<div style="min-width:0"><b style="word-break:break-all">' + esc(k.title) + '</b>' +
        '<small>' + (k.updatedAt || '').slice(0,10) + ' · <span class="badge st-info">' + (KNOW_NAMES[k.category] || '其他') + '</span></small></div></div>' +
      (lease ? '<div class="t-dim">📎 关联租约：' + esc(leaseLabel(lease)) + '</div>' : '') +
      (k.content ? '<div class="t-dim" style="white-space:pre-wrap;word-break:break-word">' + esc(String(k.content)).slice(0, 120) + (String(k.content).length > 120 ? '…' : '') + '</div>' : '') +
      '<div class="t-foot"><button class="btn sm ghost" data-view="' + k.id + '">查看</button>' +
        '<button class="btn sm ghost" data-edit="' + k.id + '">编辑</button>' +
        '<button class="btn sm danger" data-del="' + k.id + '">删除</button></div></div>';
  }).join('');
  $('#pageRoot').innerHTML =
    '<div class="page-head"><div class="ico g-cyan">💡</div><div><h2>经验库</h2><div class="sub">签约 / 退租经验与注意事项沉淀</div></div>' +
      '<div class="spacer"></div><button class="btn" id="addKnow">＋ 记一条经验</button></div>' +
    '<div class="toolbar"><select class="select" id="knowCat">' +
      '<option value="all"' + (cat==='all'?' selected':'') + '>全部类别</option>' +
      '<option value="0"' + (cat==='0'?' selected':'') + '>出租经验</option>' +
      '<option value="1"' + (cat==='1'?' selected':'') + '>退租经验</option>' +
      '<option value="2"' + (cat==='2'?' selected':'') + '>其他</option></select>' +
      '<span class="t-dim">共 ' + list.length + ' 条</span></div>' +
    '<div class="tile-grid">' + (cards || '') + '</div>' +
    (list.length ? '' : '<div class="glass card">' + emptyBox('💡','还没有经验记录，点击「记一条经验」开始沉淀') + '</div>');

  $('#knowCat').addEventListener('change', e => { window.__knowCat = e.target.value; RENDER.knowledge(); });
  $('#addKnow').addEventListener('click', () => knowForm(null));
  $$('#pageRoot [data-view]').forEach(b => b.addEventListener('click', () => {
    const k = data.knowledge.find(x => x.id === +b.dataset.view);
    const lease = k.leaseId ? data.leases.find(l => l.id === k.leaseId) : null;
    const mask = openModal(
      '<div class="modal-head"><div class="ico sm g-cyan">💡</div><h3>' + esc(k.title) + '</h3>' +
        '<button class="modal-x" data-act="x">✕</button></div>' +
      '<div class="t-dim" style="margin-bottom:10px">' + (k.updatedAt || '').slice(0,10) + ' · ' + (KNOW_NAMES[k.category] || '其他') +
        (lease ? ' · 📎 ' + esc(leaseLabel(lease)) : '') + '</div>' +
      '<div style="white-space:pre-wrap;word-break:break-word;font-size:14.5px;line-height:1.8">' + esc(k.content || '（无内容）') + '</div>' +
      '<div class="modal-foot"><button class="btn ghost" data-act="x">关闭</button></div>', { size:'lg' });
    mask.addEventListener('click', e => { if (e.target.closest('[data-act="x"]')) closeModal(mask); });
  }));
  $$('#pageRoot [data-edit]').forEach(b => b.addEventListener('click', () => knowForm(data.knowledge.find(x => x.id === +b.dataset.edit))));
  $$('#pageRoot [data-del]').forEach(b => b.addEventListener('click', async () => {
    const k = data.knowledge.find(x => x.id === +b.dataset.del);
    if (!(await confirmBox('确定删除经验 <b>' + esc(k.title) + '</b> 吗？', { okDanger:true, okText:'删除', title:'删除经验' }))) return;
    await DB.del('knowledge', k.id); await refreshAfterSave(); toast('已删除', 'ok'); RENDER.knowledge();
  }));
};
function knowForm(k){
  const isNew = !k;
  k = k || { title:'', content:'', category:0, leaseId:null };
  formModal({
    title: isNew ? '记一条经验' : '编辑经验', icon:'💡', iconCls:'g-cyan',
    fields: '<div class="form-grid">' +
      '<div class="field full"><label>标题（≤100 字） <span class="req">*</span></label><input class="input" id="f_title" maxlength="100" value="' + esc(k.title) + '"></div>' +
      '<div class="field"><label>类别</label><select class="select" id="f_cat">' +
        '<option value="0"' + (k.category === 0 ? ' selected' : '') + '>出租经验</option>' +
        '<option value="1"' + (k.category === 1 ? ' selected' : '') + '>退租经验</option>' +
        '<option value="2"' + (k.category === 2 ? ' selected' : '') + '>其他</option></select></div>' +
      '<div class="field"><label>关联租约（可选）</label><select class="select" id="f_lease"><option value="0">不关联</option>' +
        data.leases.map(l => '<option value="' + l.id + '"' + (k.leaseId === l.id ? ' selected' : '') + '>' + esc(leaseLabel(l)) + '</option>').join('') + '</select></div>' +
      '<div class="field full"><label>内容</label><textarea class="textarea" id="f_content" style="min-height:140px">' + esc(k.content || '') + '</textarea></div>' +
    '</div>',
    onSave: async mask => {
      const title = fv(mask, '#f_title');
      if (!title) return '请填写标题。';
      if (title.length > 100) return '标题不能超过 100 字。';
      const leaseId = +fv(mask, '#f_lease');
      const rec = { title, content:fv(mask, '#f_content') || null, category:+fv(mask,'#f_cat'), leaseId: leaseId || null };
      if (isNew) await DB.put('knowledge', Object.assign({ createdAt: nowIso(), updatedAt: nowIso() }, rec));
      else await DB.put('knowledge', Object.assign({}, k, rec, { updatedAt: nowIso() }));
      await refreshAfterSave(); toast('已保存', 'ok'); RENDER.knowledge(); return null;
    },
  });
}

/* ============================================================
   报表
   ============================================================ */
var rptMonthPage = 0;   /* 按月份汇总翻页：0 = 最近 12 个月 */

RENDER.reports = async function(){
  await loadAll();
  const today = todayStr();
  const bills = data.bills;
  const sumTotal = r2(bills.reduce((s,b) => s + totalOf(b), 0));
  const sumPaid = r2(bills.reduce((s,b) => s + Math.min(paidOf(b.id), totalOf(b)), 0));
  const sumRemain = r2(Math.max(0, sumTotal - sumPaid));
  const manual = bills.filter(b => b.isManual);
  const manualTotal = r2(manual.reduce((s,b) => s + totalOf(b), 0));

  /* 按月汇总 */
  const byMonth = {};
  bills.forEach(b => {
    const ym = String(b.periodStart).slice(0,7);
    byMonth[ym] = byMonth[ym] || { count:0, total:0, paid:0, remain:0 };
    const t = totalOf(b), p = Math.min(paidOf(b.id), t);
    byMonth[ym].count++; byMonth[ym].total = r2(byMonth[ym].total + t);
    byMonth[ym].paid = r2(byMonth[ym].paid + p); byMonth[ym].remain = r2(byMonth[ym].total - byMonth[ym].paid);
  });
  const monthsDesc = Object.keys(byMonth).sort().reverse();
  const monthTotal = monthsDesc.reduce((a, ym) => {
    const m = byMonth[ym];
    return { count:a.count + m.count, total:r2(a.total + m.total), paid:r2(a.paid + m.paid), remain:r2(a.remain + m.remain) };
  }, { count:0, total:0, paid:0, remain:0 });
  /* 只显示最近 12 个月，可翻页（由近到远） */
  const M_PAGE = 12;
  const mPages = Math.max(1, Math.ceil(monthsDesc.length / M_PAGE));
  if (rptMonthPage >= mPages) rptMonthPage = mPages - 1;
  if (rptMonthPage < 0) rptMonthPage = 0;
  const monthRows = monthsDesc.slice(rptMonthPage * M_PAGE, rptMonthPage * M_PAGE + M_PAGE).map(ym => {
    const m = byMonth[ym];
    const rate = m.total > 0 ? Math.round(m.paid / m.total * 100) : 0;
    return '<tr><td data-label="月份" class="t-primary">' + ym + '</td><td data-label="账单数" class="num">' + m.count + '</td>' +
      '<td data-label="应收" class="num">' + money(m.total) + '</td><td data-label="已收" class="num">' + money(m.paid) + '</td>' +
      '<td data-label="未收" class="num">' + money(m.remain) + '</td><td data-label="收款率" class="num">' + rate + '%</td></tr>';
  }).join('');
  const monthRate = monthTotal.total > 0 ? Math.round(monthTotal.paid / monthTotal.total * 100) : 0;

  /* 按物业汇总 */
  const byProp = {};
  bills.forEach(b => {
    const room = roomOf(b.roomId); const p = room ? propOf(room) : null;
    const key = p ? p.name : '（未关联物业）';
    byProp[key] = byProp[key] || { count:0, total:0, paid:0, remain:0 };
    const t = totalOf(b), pd = Math.min(paidOf(b.id), t);
    byProp[key].count++; byProp[key].total = r2(byProp[key].total + t);
    byProp[key].paid = r2(byProp[key].paid + pd); byProp[key].remain = r2(byProp[key].total - byProp[key].paid);
  });
  const propRows = Object.keys(byProp).sort().map(k => {
    const m = byProp[k];
    const rate = m.total > 0 ? Math.round(m.paid / m.total * 100) : 0;
    return '<tr><td data-label="物业" class="t-primary">' + esc(k) + '</td><td data-label="账单数" class="num">' + m.count + '</td>' +
      '<td data-label="应收" class="num">' + money(m.total) + '</td><td data-label="已收" class="num">' + money(m.paid) + '</td>' +
      '<td data-label="未收" class="num">' + money(m.remain) + '</td><td data-label="收款率" class="num">' + rate + '%</td></tr>';
  }).join('');

  const overdueList = bills.filter(isOverdue).sort((a,b) => a.dueDate.localeCompare(b.dueDate));
  const overdueRows = overdueList.map(b => {
    const t = totalOf(b), p = paidOf(b.id);
    return '<tr><td data-label="物业 · 房间" class="t-primary">' + esc(roomName(b.roomId)) + '</td>' +
      '<td data-label="租客">' + esc(tenantName(b.tenantId)) + '</td>' +
      '<td data-label="账期">' + fmtDate(b.periodStart) + ' ~ ' + fmtDate(b.periodEnd) + '</td>' +
      '<td data-label="到期日">' + fmtDate(b.dueDate) + '</td>' +
      '<td data-label="应收" class="num">' + money(t) + '</td><td data-label="已收" class="num">' + money(p) + '</td>' +
      '<td data-label="未收" class="num">' + money(r2(Math.max(0, t - p))) + '</td>' +
      '<td data-label="逾期" class="num t-danger">' + daysBetween(b.dueDate, today) + ' 天</td></tr>';
  }).join('');

  $('#pageRoot').innerHTML =
    '<div class="page-head"><div class="ico g-teal">📈</div><div><h2>报表</h2><div class="sub">' + today + ' · 应收 / 已收 / 未收汇总</div></div></div>' +
    '<div class="stat-grid">' +
      statCard('g-blue','💰','应收合计', money(sumTotal)) +
      statCard('g-green','✅','已收合计', money(sumPaid)) +
      statCard('g-amber','🧾','未收合计', money(sumRemain)) +
      statCard('g-red','⏰','逾期 ' + overdueList.length + ' 张', money(r2(overdueList.reduce((s,b) => s + Math.max(0, totalOf(b) - paidOf(b.id)), 0)))) +
    '</div>' +
    (manual.length ? '<div class="section glass card"><div class="sec-head"><h3>📥 人工补录</h3><span class="hint">历史欠款，已计入上方合计</span></div>' +
      '<div class="tbl-wrap"><table class="tbl rsp"><thead><tr><th>物业 · 房间</th><th>租客</th><th>账期</th><th class="num">应收</th><th>备注</th></tr></thead><tbody>' +
      manual.map(b => '<tr><td data-label="房间" class="t-primary">' + esc(roomName(b.roomId)) + '</td>' +
        '<td data-label="租客">' + esc(tenantName(b.tenantId)) + '</td>' +
        '<td data-label="账期">' + fmtDate(b.periodStart) + ' ~ ' + fmtDate(b.periodEnd) + '</td>' +
        '<td data-label="应收" class="num">' + money(totalOf(b)) + '</td>' +
        '<td data-label="备注" class="t-dim">' + esc(b.note || '—') + '</td></tr>').join('') +
      '<tr class="total-row"><td data-label="合计" colspan="3">人工补录合计（' + manual.length + ' 张）</td>' +
      '<td data-label="应收" class="num">' + money(manualTotal) + '</td><td></td></tr></tbody></table></div></div>' : '') +
    '<div class="section glass card"><div class="sec-head"><h3>🏢 按物业汇总</h3></div>' +
      '<div class="tbl-wrap"><table class="tbl rsp"><thead><tr><th>物业</th><th class="num">账单数</th><th class="num">应收</th><th class="num">已收</th><th class="num">未收</th><th class="num">收款率</th></tr></thead><tbody>' +
      (propRows || '<tr><td colspan="6" class="t-dim" style="text-align:center;padding:14px">暂无数据</td></tr>') + '</tbody></table></div></div>' +
    '<div class="section glass card"><div class="sec-head"><h3>📅 按月份汇总</h3><span class="hint">每页 12 个月 · 由近到远</span></div>' +
      '<div class="tbl-wrap"><table class="tbl rsp"><thead><tr><th>月份</th><th class="num">账单数</th><th class="num">应收</th><th class="num">已收</th><th class="num">未收</th><th class="num">收款率</th></tr></thead><tbody>' +
      monthRows + '<tr class="total-row"><td data-label="合计">合计（全部 ' + monthsDesc.length + ' 个月）</td><td data-label="账单数" class="num">' + monthTotal.count + '</td>' +
      '<td data-label="应收" class="num">' + money(monthTotal.total) + '</td><td data-label="已收" class="num">' + money(monthTotal.paid) + '</td>' +
      '<td data-label="未收" class="num">' + money(monthTotal.remain) + '</td><td data-label="收款率" class="num">' + monthRate + '%</td></tr>' +
      '</tbody></table></div>' +
      (monthsDesc.length
        ? '<div style="display:flex;align-items:center;justify-content:flex-end;gap:10px;padding:10px 4px 0">' +
          '<span class="hint">第 ' + (rptMonthPage + 1) + ' / ' + mPages + ' 页</span>' +
          '<button class="btn sm ghost" id="rptPrev"' + (rptMonthPage <= 0 ? ' disabled' : '') + '>‹ 上页</button>' +
          '<button class="btn sm ghost" id="rptNext"' + (rptMonthPage >= mPages - 1 ? ' disabled' : '') + '>下页 ›</button></div>'
        : emptyBox('📊','还没有账单数据')) + '</div>' +
    '<div class="section glass card"><div class="sec-head"><h3>⏰ 逾期明细</h3><span class="hint">截止日已过且未付清</span></div>' +
      '<div class="tbl-wrap"><table class="tbl rsp"><thead><tr><th>物业 · 房间</th><th>租客</th><th>账期</th><th>到期日</th><th class="num">应收</th><th class="num">已收</th><th class="num">未收</th><th class="num">逾期</th></tr></thead><tbody>' +
      (overdueRows || '<tr><td colspan="8" class="t-dim" style="text-align:center;padding:14px">没有逾期账单 🎉</td></tr>') + '</tbody></table></div></div>';

  $$('#pageRoot tr.clickable, #pageRoot tbody tr').forEach(() => {});
  /* 月份汇总翻页 */
  const rp = $('#rptPrev'), rn = $('#rptNext');
  if (rp) rp.addEventListener('click', () => { if (rptMonthPage > 0){ rptMonthPage--; RENDER.reports(); } });
  if (rn) rn.addEventListener('click', () => { if (rptMonthPage < mPages - 1){ rptMonthPage++; RENDER.reports(); } });
  /* 逾期行点击查看账单 */
  $$('#pageRoot .section:last-child tbody tr').forEach((tr, idx) => {
    if (!overdueList[idx]) return;
    tr.classList.add('clickable');
    tr.addEventListener('click', () => openBillDetail(overdueList[idx].id));
  });
};

/* ============================================================
   用户管理（仅总管理员）
   ============================================================ */
RENDER.users = async function(){
  await loadAll();
  if (!session.isAdmin) return go('dashboard');
  const rows = data.users.map(u => {
    const self = u.id === session.userId;
    const feats = u.isAdmin ? '<span class="badge st-active"><i></i>全部功能</span>' :
      (normFeatures(u.features).split(',').filter(Boolean).map(k => '<span class="badge st-gray">' + (ALL_FEATURES.find(a => a[0] === k) || [])[1] + '</span>').join(' ') || '<span class="t-dim">无</span>');
    return '<tr>' +
      '<td data-label="登录名" class="t-primary">' + esc(u.userName) + (self ? ' <span class="badge st-info">当前登录</span>' : '') + '</td>' +
      '<td data-label="显示名">' + esc(u.displayName) + '</td>' +
      '<td data-label="角色">' + (u.isAdmin ? '<span class="badge st-active"><i></i>总管理员</span>' : '<span class="badge st-gray"><i></i>普通用户</span>') + '</td>' +
      '<td data-label="可用功能">' + feats + '</td>' +
      '<td data-label="最近登录" class="t-dim">' + (u.lastLoginAt || '从未登录') + '</td>' +
      '<td class="act-cell" data-label="操作">' +
        (u.isAdmin ? '<span class="t-dim">不可修改</span>' :
          '<button class="link-btn" data-edit="' + u.id + '">改名/权限</button>' +
          '<button class="link-btn danger" data-del="' + u.id + '">删除</button>') + '</td></tr>';
  }).join('');
  $('#pageRoot').innerHTML =
    '<div class="page-head"><div class="ico g-slate">🛡️</div><div><h2>用户管理</h2><div class="sub">普通用户可分配功能模块；总管理员拥有全部功能</div></div>' +
      '<div class="spacer"></div><button class="btn" id="addUser">＋ 新建用户</button></div>' +
    '<div class="glass tbl-wrap"><table class="tbl rsp"><thead><tr><th>登录名</th><th>显示名</th><th>角色</th><th>可用功能</th><th>最近登录</th><th>操作</th></tr></thead><tbody>' +
    rows + '</tbody></table></div>' +
    '<div class="note-box" style="margin-top:14px">· 新建普通用户默认仅开放「账单记账」，可随时调整。<br>· 修改登录名后，该用户需使用新登录名重新登录。<br>· 总管理员账号（admin）不可在此改名或删除，如需修改密码请前往「设置」。</div>';

  $('#addUser').addEventListener('click', () => userForm(null));
  $$('#pageRoot [data-edit]').forEach(b => b.addEventListener('click', () => userForm(data.users.find(u => u.id === +b.dataset.edit))));
  $$('#pageRoot [data-del]').forEach(b => b.addEventListener('click', async () => {
    const u = data.users.find(x => x.id === +b.dataset.del);
    if (u.isAdmin || u.id === session.userId) return toast('不能删除总管理员账号。', 'err');
    if (!(await confirmBox('确定删除用户 <b>' + esc(u.userName) + '</b>（' + esc(u.displayName) + '）吗？', { okDanger:true, okText:'删除', title:'删除用户' }))) return;
    await DB.del('users', u.id); await refreshAfterSave(); toast('已删除', 'ok'); RENDER.users();
  }));
};
function userForm(u){
  const isNew = !u;
  u = u || { userName:'', displayName:'', features:DEFAULT_FEATURES };
  const feats = normFeatures(u.features).split(',').filter(Boolean);
  formModal({
    title: isNew ? '新建用户' : '编辑用户', icon:'🛡️', iconCls:'g-slate',
    fields: '<div class="form-grid">' +
      '<div class="field"><label>登录名 <span class="req">*</span></label><input class="input" id="f_user" value="' + esc(u.userName) + '" autocomplete="off"><div class="input-hint">登录名唯一，不可与其他账号重复</div></div>' +
      '<div class="field"><label>显示名</label><input class="input" id="f_disp" value="' + esc(u.displayName) + '"></div>' +
      (isNew ? '<div class="field"><label>初始密码 <span class="req">*</span></label><input class="input" id="f_pass" type="text" value="" placeholder="至少 6 位"></div>' : '') +
      '<div class="field full"><label>可用功能（总管理员始终拥有全部功能）</label><div class="check-row" id="f_feats">' +
        ALL_FEATURES.map(a => '<label class="check-item"><input type="checkbox" value="' + a[0] + '"' + (feats.indexOf(a[0]) >= 0 ? ' checked' : '') + '>' + a[1] + '</label>').join('') +
      '</div></div></div>',
    onSave: async mask => {
      const userName = fv(mask, '#f_user'), displayName = fv(mask, '#f_disp') || userName;
      if (!userName) return '请填写登录名。';
      if (/\s/.test(userName)) return '登录名不能包含空格。';
      const dup = data.users.find(x => x.userName.toLowerCase() === userName.toLowerCase() && (isNew || x.id !== u.id));
      if (dup) return '登录名已存在。';
      const featsSel = $$('#f_feats input:checked', mask).map(i => i.value);
      const features = normFeatures(featsSel.join(','));
      if (isNew){
        const pass = fv(mask, '#f_pass');
        if (pass.length < 6) return '初始密码至少 6 位。';
        try {
          await API.createUser({ userName, displayName, features, password:pass });
        } catch(e){ return e.message || '创建用户失败。'; }
      } else {
        const selfRenamed = u.id === session.userId && u.userName !== userName;
        const rec = Object.assign({}, u, { userName, displayName, features });
        await DB.put('users', rec);
        if (selfRenamed){
          await refreshAfterSave(); toast('登录名已修改，请使用新登录名重新登录。', 'ok');
          doLogout(); return null;
        }
        if (u.id === session.userId) session.displayName = displayName;
      }
      await refreshAfterSave(); toast('已保存', 'ok'); RENDER.users(); return null;
    },
  });
}

/* ============================================================
   设置（基础信息 / 收款码 / 修改密码 / 备份恢复）
   ============================================================ */
RENDER.settings = async function(){
  await loadAll();
  const landlord = settingVal(SET.LANDLORD, '');
  const notice = settingVal(SET.NOTICE, DEFAULT_NOTICE);
  const qr = settingVal(SET.QR, null);
  $('#pageRoot').innerHTML =
    '<div class="page-head"><div class="ico g-slate">⚙️</div><div><h2>设置</h2><div class="sub">基础信息 · 收款码 · 密码 · 数据备份</div></div></div>' +
    '<div class="section glass card">' +
      '<div class="sec-head"><h3>🏷️ 账单基础信息</h3><span class="hint">显示在账单图片上</span></div>' +
      '<div class="form-grid">' +
        '<div class="field"><label>房东称呼 / 落款</label><input class="input" id="s_landlord" value="' + esc(landlord) + '" placeholder="如：张房东"></div>' +
        '<div class="field"><label>收款提示语</label><input class="input" id="s_notice" value="' + esc(notice) + '"></div>' +
      '</div>' +
      '<div class="check-row" style="margin-bottom:12px">' + NOTICE_PRESETS.map(p => '<button class="btn sm ghost preset-btn">' + esc(p) + '</button>').join('') + '</div>' +
      '<button class="btn" id="saveBase">保存基础信息</button>' +
    '</div>' +
    '<div class="section glass card">' +
      '<div class="sec-head"><h3>📱 收款码</h3><span class="hint">显示在账单图片右下角，租客扫码付款</span></div>' +
      '<div style="display:flex;gap:16px;align-items:flex-start;flex-wrap:wrap">' +
        (qr ? '<img src="' + qr + '" alt="收款码" style="width:170px;height:170px;object-fit:contain;border:1px solid var(--line);border-radius:14px;background:#fff">' :
              '<div style="width:170px;height:170px;border:1.5px dashed var(--line2);border-radius:14px;display:grid;place-items:center;color:var(--faint)">未上传</div>') +
        '<div style="flex:1;min-width:220px">' +
          '<p class="t-dim" style="margin-bottom:10px">上传「微信收款码」或「支付宝收款码」图片，建议微信收款码（与发微信账单场景一致）。<br>微信：服务 → 收付款 → 二维码收款 → 保存收款码；<br>支付宝：收付款 → 收钱 → 保存图片。</p>' +
          '<input type="file" id="qrFile" accept="image/*" class="hidden">' +
          '<button class="btn sm" id="qrUpload">上传 / 更换收款码</button> ' +
          (qr ? '<button class="btn sm danger" id="qrRemove">移除</button>' : '') +
        '</div></div>' +
    '</div>' +
    '<div class="section glass card">' +
      '<div class="sec-head"><h3>🔑 修改密码</h3><span class="hint">当前账号：' + esc(session.userName) + '</span></div>' +
      '<div class="form-grid">' +
        '<div class="field"><label>原密码</label><input class="input" id="p_old" type="password" autocomplete="current-password"></div>' +
        '<div class="field"></div>' +
        '<div class="field"><label>新密码（至少 6 位）</label><input class="input" id="p_new" type="password" autocomplete="new-password"></div>' +
        '<div class="field"><label>确认新密码</label><input class="input" id="p_new2" type="password" autocomplete="new-password"></div>' +
      '</div><button class="btn" id="savePass">修改密码</button>' +
    '</div>' +
    '<div class="section glass card">' +
      '<div class="sec-head"><h3>💾 数据备份与恢复</h3><span class="hint">数据保存在服务器 MySQL，建议定期导出备份</span></div>' +
      '<div style="font-size:12.5px;line-height:1.75;color:var(--muted);background:rgba(217,181,125,.05);border:1px dashed var(--line2);border-radius:10px;padding:10px 12px;margin-bottom:12px">' +
        '<div style="color:var(--text);font-weight:600;margin-bottom:4px">📖 备份说明（两种方式，推荐第一种）</div>' +
        '<b style="color:var(--text)">① 页面导出 JSON（推荐，最简单）</b>：点「一键备份 / 导出 JSON」，服务器把 MySQL 里的物业、房间、租客、租约、账单、杂费、收款、经验、账号和全部设置打包，下载一个「物业管理台账备份-日期.json」。纯业务数据、不含数据库密码；该格式与 HTML 单文件版<b>完全互通</b>，两边可互相导入。<br>' +
        '<b style="color:var(--text)">② 整库灾备（可选）</b>：也可在主机控制面板（如 cPanel → phpMyAdmin）把整个 MySQL 库导出为 .sql，或用 mysqldump 命令备份，适合服务器故障 / 误删整库时恢复。<br>' +
        '<b style="color:var(--text)">🔁 换主机 / 迁移流程</b>：旧站点「导出 JSON」→ 新主机上传本程序 5 个文件并跑完网页安装向导 → 登录新站点「从备份恢复」选择该 JSON，数据、账号、收款码全部还原，<b>全程不用手动搬数据库</b>。<br>' +
        '<b style="color:var(--text)">⚠️ 恢复注意</b>：导入会先清空再整体覆盖当前数据（数据库事务保护，中途失败自动回滚，不会出现半份数据）；备份 JSON 含登录密码哈希，请像钥匙一样妥善保管，建议存到网盘或 U 盘。' +
      '</div>' +
      '<div id="qbStats" style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:14px;font-size:12px"></div>' +
      '<div id="qbStatus" style="font-size:13px;margin-bottom:12px"></div>' +
      '<button class="btn ok" id="quickBackup" style="width:100%;padding:14px;font-size:16px;font-weight:600">🚀 一键备份</button> ' +
      '<div style="display:flex;gap:8px;margin-top:8px">' +
      '<button class="btn sm ghost" id="exportBtn" style="flex:1">⬇️ 导出 JSON</button>' +
      '<button class="btn sm ghost" id="importBtn" style="flex:1">⬆️ 从备份恢复</button>' +
      '</div>' +
      '<input type="file" id="importFile" accept=".json,application/json" class="hidden">' +
    '</div>' +
    '<div class="t-dim" style="text-align:center;padding:6px 0 2px">物业管理台账 · PHP + MySQL 版 v' + APP_VERSION + ' · 数据存储于服务器</div>';

  $('#saveBase').addEventListener('click', async () => {
    await DB.kvSet(SET.LANDLORD, $('#s_landlord').value.trim() || null);
    await DB.kvSet(SET.NOTICE, $('#s_notice').value.trim() || DEFAULT_NOTICE);
    await refreshAfterSave(); toast('基础信息已保存', 'ok');
  });
  $$('#pageRoot .preset-btn').forEach(b => b.addEventListener('click', () => { $('#s_notice').value = b.textContent; }));
  $('#qrUpload').addEventListener('click', () => $('#qrFile').click());
  $('#qrFile').addEventListener('change', e => {
    const file = e.target.files[0];
    if (!file) return;
    if (file.size > 3 * 1024 * 1024) return toast('图片过大（超过 3MB），请压缩后再上传。', 'err');
    const rd = new FileReader();
    rd.onload = async () => {
      await DB.kvSet(SET.QR, rd.result);
      await refreshAfterSave(); toast('收款码已保存，之后生成的账单图会带上该二维码', 'ok'); RENDER.settings();
    };
    rd.readAsDataURL(file);
  });
  const rm = $('#qrRemove');
  if (rm) rm.addEventListener('click', async () => {
    if (!(await confirmBox('确定移除收款码吗？', { okDanger:true, okText:'移除', title:'移除收款码' }))) return;
    await DB.kvSet(SET.QR, null); await refreshAfterSave(); toast('收款码已移除', 'ok'); RENDER.settings();
  });
  $('#savePass').addEventListener('click', async () => {
    const oldP = $('#p_old').value, newP = $('#p_new').value, newP2 = $('#p_new2').value;
    if (!oldP || !newP) return toast('请填写原密码与新密码。', 'err');
    if (newP.length < 6) return toast('新密码至少 6 位。', 'err');
    if (newP !== newP2) return toast('两次输入的新密码不一致。', 'err');
    try { await API.changePassword(oldP, newP); }
    catch(e){ return toast(e.message || '密码修改失败。', 'err'); }
    $('#p_old').value = $('#p_new').value = $('#p_new2').value = '';
    toast('密码已修改，下次登录请使用新密码', 'ok');
  });
  /* 一键备份区域：数据量统计 + 最近备份时间 */
  const lastBak = await DB.kvGet('lastBackupAt', null);
  const cnt = (k) => (data[k] || []).length;
  $('#qbStats').innerHTML = [
    ['物业', cnt('properties')], ['房间', cnt('rooms')], ['租客', cnt('tenants')],
    ['租约', cnt('leases')], ['账单', cnt('bills')], ['杂费', cnt('billItems')],
    ['收款', cnt('payments')], ['经验', cnt('knowledge')], ['账号', cnt('users')],
  ].map(([n,v]) => '<span class="chip" style="padding:2px 8px">' + v + ' ' + n + '</span>').join('');
  $('#qbStatus').innerHTML = lastBak
    ? '<span style="color:var(--ok,#059669)">✅ 最近备份：' + esc(lastBak.replace('T',' ').slice(0,16)) + '</span>'
    : '<span style="color:var(--warn,#d97706)">⚠️ 尚未备份过，建议定期「一键备份」导出 JSON 文件保存</span>';
  $('#quickBackup').addEventListener('click', quickBackup);
  $('#exportBtn').addEventListener('click', exportData);
  $('#importBtn').addEventListener('click', () => $('#importFile').click());
  $('#importFile').addEventListener('change', e => {
    const file = e.target.files[0];
    if (file) importData(file);
    e.target.value = '';
  });
};

/* 导出 / 导入（走服务器接口；备份 JSON 含账号密码哈希，与 HTML 版备份互通） */
async function quickBackup(){
  await loadAll();
  let snap;
  try { snap = await API.exportSnapshot(); } catch(e){ return toast('备份导出失败：' + e.message, 'err'); }
  const now = new Date();
  download('物业管理台账备份-' + fileDate() + '.json', JSON.stringify(snap, null, 1), 'application/json');
  await DB.kvSet('lastBackupAt', now.toISOString());
  const cnt = (k) => (data[k] || []).length;
  toast('✅ 备份完成：' + cnt('properties') + ' 物业 / ' + cnt('bills') + ' 账单 / ' + cnt('payments') + ' 收款 / ' + cnt('knowledge') + ' 经验', 'ok');
  RENDER.settings();
}
async function exportData(){
  let snap;
  try { snap = await API.exportSnapshot(); } catch(e){ return toast('导出失败：' + e.message, 'err'); }
  download('物业管理台账备份-' + fileDate() + '.json', JSON.stringify(snap, null, 1), 'application/json');
  toast('备份文件已导出，请妥善保管', 'ok');
}
async function importData(file){
  let snap;
  try { snap = JSON.parse(await file.text()); }
  catch(e){ return toast('备份文件格式不正确（无法解析）。', 'err'); }
  const d = snap && snap.data;
  if (!d || !Array.isArray(d.users) || !Array.isArray(d.properties))
    return toast('这不是有效的台账备份文件。', 'err');
  const counts = ['properties','rooms','tenants','leases','bills','billItems','payments','knowledge','users']
    .map(k => (d[k] || []).length).join(' / ');
  const ok = await confirmBox(
    '导入将 <b>清空并覆盖</b> 服务器上当前全部数据（含账号与密码），操作不可恢复。<br><br>' +
    '备份时间：' + esc((snap.exportedAt || '').replace('T',' ').slice(0,16) || '未知') + '<br>' +
    '数据规模（物业/房间/租客/租约/账单/杂费/收款/经验/用户）：<b>' + counts + '</b><br><br>确定继续导入吗？',
    { okDanger:true, okText:'覆盖导入', title:'从备份恢复' });
  if (!ok) return;
  try {
    await API.importSnapshot(snap);
    toast('数据已恢复，请使用备份中的账号重新登录', 'ok');
    doLogout();
  } catch(err){ toast('导入失败：' + err.message, 'err'); }
}

/* ============================================================
   启动 / 登录
   ============================================================ */
function showLogin(){
  $('#loginView').classList.remove('hidden');
  $('#appView').classList.add('hidden');
  setTimeout(() => { $('#loginUser').focus(); }, 60);
}
function showApp(){
  $('#loginView').classList.add('hidden');
  $('#appView').classList.remove('hidden');
  $('#sidebar').classList.remove('hidden');
  $('#topbar').classList.remove('hidden');
  $('#sbName').textContent = session.displayName;
  $('#sbRole').textContent = session.isAdmin ? '总管理员' : '普通用户';
  $('#sbAvatar').textContent = (session.displayName || 'U').slice(0,1).toUpperCase();
  $('#tbUser').textContent = session.displayName + (session.isAdmin ? '（管理员）' : '');
  $('#sbVer').textContent = 'v' + APP_VERSION + ' · 服务器数据';
  renderNav();
  go('dashboard');
}
async function init(){
  session = await loadSession();
  if (session) showApp(); else showLogin();

  /* 主题切换：浅色 / 暗色，偏好存 localStorage（首屏由 head 后的内联脚本预设） */
  const syncThemeBtns = () => {
    const light = document.documentElement.getAttribute('data-theme') === 'light';
    $('#loginTheme').textContent = light ? '🌙' : '☀️';
    $('#tbTheme').textContent = light ? '🌙' : '☀️';
    $('#sbTheme').innerHTML = light ? '🌙 切换暗色主题' : '☀️ 切换浅色主题';
  };
  window.plSetTheme = (t) => {
    if (t !== 'light' && t !== 'dark') t = 'dark';
    document.documentElement.setAttribute('data-theme', t);
    try { localStorage.setItem('pl_theme', t); } catch(e){}
    syncThemeBtns();
  };
  const toggleTheme = () => window.plSetTheme(
    document.documentElement.getAttribute('data-theme') === 'light' ? 'dark' : 'light');
  ['#loginTheme', '#tbTheme', '#sbTheme'].forEach(s => $(s).addEventListener('click', toggleTheme));
  syncThemeBtns();

  const tryLogin = async () => {
    const u = $('#loginUser').value, p = $('#loginPass').value;
    if (!u || !p) return toast('请输入登录名和密码。', 'err');
    $('#loginBtn').disabled = true;
    const err = await doLogin(u, p);
    $('#loginBtn').disabled = false;
    if (err) return toast(err, 'err');
    showApp();
  };
  $('#loginBtn').addEventListener('click', tryLogin);
  $('#loginPass').addEventListener('keydown', e => { if (e.key === 'Enter') tryLogin(); });
  $('#loginUser').addEventListener('keydown', e => { if (e.key === 'Enter') $('#loginPass').focus(); });
  $('#logoutBtn').addEventListener('click', async () => {
    if (await confirmBox('确定退出登录吗？', { title:'退出登录', okText:'退出' })) doLogout();
  });
  /* 导航点击（侧边栏 + 底部 tab） */
  document.addEventListener('click', e => {
    const n = e.target.closest('#sideNav [data-nav], #tabbar [data-nav]');
    if (n) go(n.dataset.nav);
  });
  /* 手机上键盘弹出时把弹窗内的输入框滚到可视区，避免被键盘遮挡 */
  document.addEventListener('focusin', e => {
    const t = e.target;
    if (!t || !t.closest || !t.closest('.modal')) return;
    if (!t.matches('input,select,textarea')) return;
    setTimeout(() => { try { t.scrollIntoView({ block:'center', behavior:'smooth' }); } catch(err){} }, 300);
  });
}
init();
</script>
</body>
</html>
