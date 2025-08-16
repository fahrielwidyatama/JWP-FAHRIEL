<?php
/**
 * To-Do List (PHP + Bootstrap 5)
 * Penyimpanan: SESSION (array of objects)
 * Fitur: tambah, toggle, edit (modal), hapus, filter, cari, sort, progress, bulk actions, toast
 */
session_start();

/** Escape HTML untuk output aman */
function e($str){ return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8'); }

/** Muat tasks dan pastikan setiap elemen memiliki key default (priority, due, status) */
function load_tasks(){
  if(!isset($_SESSION['tasks']) || !is_array($_SESSION['tasks'])){
    $_SESSION['tasks'] = [
      ["id"=>1,"title"=>"Belajar PHP","status"=>"belum","priority"=>"medium","due"=>""],
      ["id"=>2,"title"=>"Rancang wireframe UI","status"=>"selesai","priority"=>"high","due"=>date('Y-m-d', strtotime('+1 day'))],
    ];
  }
  // Healing untuk data lama yang belum punya key
  foreach($_SESSION['tasks'] as &$t){
    if(!isset($t['status'])  || ($t['status']!=='selesai' && $t['status']!=='belum')) $t['status'] = 'belum';
    if(!isset($t['priority'])|| !in_array($t['priority'],['low','medium','high'])) $t['priority'] = 'medium';
    if(!isset($t['due'])) $t['due'] = '';
    if(!isset($t['title'])) $t['title'] = '';
    if(!isset($t['id']))    $t['id'] = 0;
  }
  return $_SESSION['tasks'];
}
/** Simpan tasks */
function save_tasks($tasks){ $_SESSION['tasks']=$tasks; }
/** Ambil id berikutnya */
function next_id($tasks){ $m=0; foreach($tasks as $t){ if(($t['id']??0)>$m) $m=$t['id']; } return $m+1; }

/** Tambah */
function add_task($title,$priority,$due){
  $tasks = load_tasks();
  $title = trim($title);
  if($title==='') return false;
  $priority = in_array($priority,['low','medium','high']) ? $priority : 'medium';
  $due = trim($due);
  $tasks[] = ["id"=>next_id($tasks),"title"=>$title,"status"=>"belum","priority"=>$priority,"due"=>$due];
  save_tasks($tasks); return true;
}
/** Toggle status */
function toggle_task($id){
  $tasks = load_tasks();
  foreach($tasks as &$t){ if(($t['id']??0)==$id){ $t['status']=($t['status']==='selesai'?'belum':'selesai'); break; } }
  save_tasks($tasks);
}
/** Hapus */
function delete_task($id){
  $tasks = array_values(array_filter(load_tasks(), fn($t)=>($t['id']??0)!=$id));
  save_tasks($tasks);
}
/** Edit */
function edit_task($id,$title,$priority,$due,$status){
  $title = trim($title);
  if($title==='') return false;
  $priority = in_array($priority,['low','medium','high']) ? $priority : 'medium';
  $status = ($status==='selesai')?'selesai':'belum';
  $tasks = load_tasks();
  foreach($tasks as &$t){
    if(($t['id']??0)==$id){ $t['title']=$title; $t['priority']=$priority; $t['due']=$due; $t['status']=$status; break; }
  }
  save_tasks($tasks); return true;
}
/** Bulk selesai semua */
function bulk_complete_all(){
  $tasks = load_tasks();
  foreach($tasks as &$t){ $t['status']='selesai'; }
  save_tasks($tasks);
}
/** Bulk hapus selesai */
function bulk_clear_completed(){
  $tasks = array_values(array_filter(load_tasks(), fn($t)=>($t['status']??'belum')!=='selesai'));
  save_tasks($tasks);
}

/** -------- Controller -------- */
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if($method==='POST'){
  $action = $_POST['action'] ?? '';
  if($action==='add'){
    $ok = add_task($_POST['title']??'', $_POST['priority']??'medium', $_POST['due']??'');
    header('Location: index.php?msg='.urlencode($ok?'Tugas ditambahkan':'Judul tidak boleh kosong')); exit;
  }elseif($action==='toggle'){
    toggle_task((int)($_POST['id']??0));
    header('Location: index.php?msg='.urlencode('Status diperbarui')); exit;
  }elseif($action==='delete'){
    delete_task((int)($_POST['id']??0));
    header('Location: index.php?msg='.urlencode('Tugas dihapus')); exit;
  }elseif($action==='edit'){
    $ok = edit_task((int)($_POST['id']??0), $_POST['title']??'', $_POST['priority']??'medium', $_POST['due']??'', $_POST['status']??'belum');
    header('Location: index.php?msg='.urlencode($ok?'Tugas diperbarui':'Judul tidak boleh kosong')); exit;
  }elseif($action==='bulk_complete'){
    bulk_complete_all(); header('Location: index.php?msg='.urlencode('Semua tugas ditandai selesai')); exit;
  }elseif($action==='bulk_clear'){
    bulk_clear_completed(); header('Location: index.php?msg='.urlencode('Tugas selesai dihapus')); exit;
  }
}

$tasks = load_tasks();
$total = count($tasks);
$done  = count(array_filter($tasks, fn($t)=>($t['status']??'belum')==='selesai'));
$progress = $total? round($done*100/$total):0;
$msg = $_GET['msg']??'';
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>To-Do List • by Fahriel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="assets/style.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">

  <!-- Header & Progress -->
  <header class="mb-3 d-flex align-items-center justify-content-between">
    <div>
      <h1 class="h3 mb-1">To-Do List</h1>
      <div class="text-muted small">Total: <strong><?=$total?></strong> • Selesai: <strong><?=$done?></strong></div>
    </div>
    <div class="ms-3" style="min-width:260px;">
      <div class="d-flex justify-content-between small"><span>Progress</span><span><?=$progress?>%</span></div>
      <div class="progress" role="progressbar" aria-valuenow="<?=$progress?>" aria-valuemin="0" aria-valuemax="100">
        <div class="progress-bar" style="width: <?=$progress?>%"></div>
      </div>
    </div>
  </header>

  <!-- Toolbar (Cari, Filter, Sort, Bulk) -->
  <section class="card shadow-sm mb-3">
    <div class="card-body">
      <div class="row g-2 align-items-center">
        <div class="col-12 col-md-4">
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input id="search" type="text" class="form-control" placeholder="Cari judul… (tekan / untuk fokus)">
          </div>
        </div>
        <div class="col-12 col-md-4">
          <!-- <div class="btn-group w-100" role="group" aria-label="Filter">
            <button class="btn btn-outline-secondary active" data-filter="all">Semua</button>
            <button class="btn btn-outline-secondary" data-filter="active">Aktif</button>
            <button class="btn btn-outline-secondary" data-filter="done">Selesai</button>
          </div> -->
        </div>
        <div class="col-12 col-md-4 d-flex gap-2">
          <select id="sort" class="form-select">
            <option value="created_desc">Urut Terbaru</option>
            <option value="title_asc">Judul A → Z</option>
            <option value="title_desc">Judul Z → A</option>
            <option value="priority">Prioritas (Tinggi → Rendah)</option>
            <option value="due">Due Date (Terdekat)</option>
          </select>
          <form method="post">
            <input type="hidden" name="action" value="bulk_complete">
            <button class="btn btn-outline-primary" title="Tandai semua selesai"><i class="bi bi-check2-square"></i></button>
          </form>
          <form method="post" onsubmit="return confirm('Hapus semua tugas yang selesai?');">
            <input type="hidden" name="action" value="bulk_clear">
            <button class="btn btn-outline-danger" title="Hapus tugas selesai"><i class="bi bi-trash"></i></button>
          </form>
        </div>
      </div>
    </div>
  </section>

  <!-- Tambah Tugas -->
  <section class="card shadow-sm mb-4">
    <div class="card-body">
      <form method="post" class="row g-2 align-items-end">
        <input type="hidden" name="action" value="add">
        <div class="col-12 col-md-6">
          <label class="form-label">Judul Tugas</label>
          <input id="new-title" type="text" name="title" class="form-control form-control-lg" placeholder="Tulis tugas baru… (tekan n untuk fokus)" required>
        </div>
        <div class="col-6 col-md-3">
          <label class="form-label">Prioritas</label>
          <select name="priority" class="form-select">
            <option value="low">Rendah</option>
            <option value="medium" selected>Menengah</option>
            <option value="high">Tinggi</option>
          </select>
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label">Tenggat Waktu</label>
          <input type="date" name="due" class="form-control">
        </div>
        <div class="col-12 col-md-1 d-grid">
          <button class="btn btn-primary btn-lg"><i class="bi bi-plus-lg me-1"></i>Tambah</button>
        </div>
      </form>
    </div>
  </section>

  <!-- Daftar Tugas -->
  <section class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
      <strong>Daftar Tugas</strong>
      <span class="text-muted small">Klik checkbox untuk ubah status • Edit lewat ikon pensil</span>
    </div>
    <ul id="task-list" class="list-group list-group-flush">
      <?php if(empty($tasks)): ?>
        <li class="list-group-item text-center text-muted">Belum ada tugas.</li>
      <?php else: foreach($tasks as $idx=>$t):
        $priority = $t['priority'] ?? 'medium';
        $prioWeight = ['high'=>3,'medium'=>2,'low'=>1][$priority] ?? 2;
        $dueAttr = ($t['due'] ?? '') !== '' ? $t['due'] : '9999-12-31';
      ?>
      <li class="list-group-item d-flex align-items-center justify-content-between task-item"
          data-status="<?= ($t['status'] ?? 'belum') ?>"
          data-title="<?= e(mb_strtolower($t['title'] ?? '')) ?>"
          data-priority="<?= $prioWeight ?>"
          data-due="<?= e($dueAttr) ?>"
          data-created="<?= $idx ?>">

        <div class="d-flex align-items-center gap-3 flex-wrap">
          <!-- Toggle -->
          <form method="post" class="m-0">
            <input type="hidden" name="action" value="toggle">
            <input type="hidden" name="id" value="<?= (int)($t['id'] ?? 0) ?>">
            <input class="form-check-input" type="checkbox" <?= ($t['status'] ?? 'belum')==='selesai'?'checked':'' ?> onchange="this.form.submit()" aria-label="Tandai selesai">
          </form>

          <!-- Title & Meta -->
          <div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
              <span class="task-title <?= ($t['status'] ?? 'belum')==='selesai'?'done':'' ?>"><?= e($t['title'] ?? '') ?></span>
              <?php if($priority==='high'): ?>
                <span class="badge text-bg-danger">Tinggi</span>
              <?php elseif($priority==='medium'): ?>
                <span class="badge text-bg-warning text-dark">Menengah</span>
              <?php else: ?>
                <span class="badge text-bg-secondary">Rendah</span>
              <?php endif; ?>
              <?php if(($t['due'] ?? '')!==''): ?>
                <span class="badge rounded-pill text-bg-light border"><i class="bi bi-calendar-event me-1"></i><?= e($t['due']) ?></span>
              <?php endif; ?>
              <span class="badge rounded-pill <?= ($t['status'] ?? 'belum')==='selesai'?'text-bg-success':'text-bg-secondary' ?>"><?= ($t['status'] ?? 'belum') ?></span>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="d-flex align-items-center gap-2">
          <button class="btn btn-outline-secondary btn-sm"
                  data-bs-toggle="modal"
                  data-bs-target="#editModal"
                  title="Edit"
                  data-id="<?= (int)($t['id'] ?? 0) ?>"
                  data-title="<?= e($t['title'] ?? '') ?>"
                  data-priority="<?= e($priority) ?>"
                  data-due="<?= e($t['due'] ?? '') ?>"
                  data-status="<?= e($t['status'] ?? 'belum') ?>">
            <i class="bi bi-pencil-square"></i>
          </button>
          <form method="post" class="m-0" onsubmit="return confirm('Hapus tugas ini?')">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int)($t['id'] ?? 0) ?>">
            <button class="btn btn-outline-danger btn-sm" title="Hapus"><i class="bi bi-trash"></i></button>
          </form>
        </div>
      </li>
      <?php endforeach; endif; ?>
    </ul>
  </section>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="post">
      <input type="hidden" name="action" value="edit">
      <input type="hidden" name="id" id="edit-id">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="editLabel">Edit Tugas</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Judul</label>
          <input type="text" class="form-control" name="title" id="edit-title" required>
        </div>
        <div class="row g-2">
          <div class="col-6">
            <label class="form-label">Prioritas</label>
            <select class="form-select" name="priority" id="edit-priority">
              <option value="low">Rendah</option>
              <option value="medium">Menengah</option>
              <option value="high">Tinggi</option>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label">Tenggat Waktu</label>
            <input type="date" class="form-control" name="due" id="edit-due">
          </div>
          <div class="col-12">
            <label class="form-label">Status</label>
            <select class="form-select" name="status" id="edit-status">
              <option value="belum">Belum</option>
              <option value="selesai">Selesai</option>
            </select>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

<!-- Toast -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1080">
  <div id="liveToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body">
        <?= e($msg ?: 'Siap!') ?>
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Modal Edit: isi field dari data tombol
const editModal = document.getElementById('editModal');
editModal.addEventListener('show.bs.modal', (event)=>{
  const btn = event.relatedTarget;
  document.getElementById('edit-id').value       = btn.getAttribute('data-id') || '';
  document.getElementById('edit-title').value    = btn.getAttribute('data-title') || '';
  document.getElementById('edit-priority').value = btn.getAttribute('data-priority') || 'medium';
  document.getElementById('edit-due').value      = btn.getAttribute('data-due') || '';
  document.getElementById('edit-status').value   = btn.getAttribute('data-status') || 'belum';
});

// Toast sukses saat ada ?msg=
<?php if($msg): ?>
new bootstrap.Toast(document.getElementById('liveToast')).show();
<?php endif; ?>

// Filter, Search, Sort (client-side)
const list = document.getElementById('task-list');
const items = Array.from(list.querySelectorAll('.task-item'));
let currentFilter = 'all';

document.querySelectorAll('[data-filter]').forEach(btn=>{
  btn.addEventListener('click', ()=>{
    document.querySelectorAll('[data-filter]').forEach(b=>b.classList.remove('active'));
    btn.classList.add('active');
    currentFilter = btn.getAttribute('data-filter');
    applyFilters();
  });
});
document.getElementById('search').addEventListener('input', applyFilters);
document.getElementById('sort').addEventListener('change', applyFilters);

function applyFilters(){
  const q = (document.getElementById('search').value || '').trim().toLowerCase();
  items.forEach(li=>{
    const status = li.dataset.status || 'belum';
    const title  = li.dataset.title || '';
    let show = true;
    if(currentFilter==='active' && status!=='belum') show=false;
    if(currentFilter==='done' && status!=='selesai') show=false;
    if(q && !title.includes(q)) show=false;
    li.style.display = show ? '' : 'none';
  });

  const visible = items.filter(li=>li.style.display!=='none');
  const mode = document.getElementById('sort').value;
  visible.sort((a,b)=>{
    if(mode==='title_asc')  return (a.dataset.title||'').localeCompare(b.dataset.title||'');
    if(mode==='title_desc') return (b.dataset.title||'').localeCompare(a.dataset.title||'');
    if(mode==='priority')   return (parseInt(b.dataset.priority||'0') - parseInt(a.dataset.priority||'0'));
    if(mode==='due')        return (a.dataset.due||'').localeCompare(b.dataset.due||'');
    return parseInt(b.dataset.created||'0') - parseInt(a.dataset.created||'0');
  });
  visible.forEach(el=>list.appendChild(el));
}
applyFilters();

// Keyboard shortcuts
document.addEventListener('keydown', (e)=>{
  const tag = e.target.tagName.toLowerCase();
  if(tag==='input' || tag==='textarea') return;
  if(e.key === '/'){ e.preventDefault(); document.getElementById('search').focus(); }
  if(e.key.toLowerCase() === 'n'){ e.preventDefault(); document.getElementById('new-title').focus(); }
});
</script>
</body>
</html>
