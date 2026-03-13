<?php
// 1. FORCE ERROR REPORTING
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. DIAGNOSTIC CHECK
try {
    if (!class_exists('\SW\Source\Modules\SimplySql\Pointer')) {
        throw new Exception("CRITICAL ERROR: The class \SW\Source\Modules\SimplySql\Pointer was not found. Do you need to include or require a bootstrap file?");
    }
    $Pointer = new \SW\Source\Modules\SimplySql\Pointer();
} catch (Exception $e) {
    echo "<div style='padding:40px; background:#fee2e2; color:#b91c1c; font-family:sans-serif; border:2px solid red;'>";
    echo "<h1>Connection / Class Error</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
    exit;
}

$selectedTable = $_GET['table'] ?? null;

// --- DATABASE ACTIONS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    \SW\Source\Server\Security\CSRF::Validate();
    $action = $_POST['action'];

    if ($action === 'create' && $selectedTable) {
        $data = $_POST;
        unset($data['action'], $data['CSRF_TOKEN'], $data['primary_key_name'], $data['primary_key_val']); 
        $Pointer->Insert($selectedTable, $data);
        \SW\Source\Server\Web::Refresh(); exit;
    }

    if ($action === 'update' && $selectedTable) {
        $pkName = $_POST['primary_key_name'];
        $pkVal = $_POST['primary_key_val'];
        $data = $_POST;
        unset($data['action'], $data['CSRF_TOKEN'], $data['primary_key_name'], $data['primary_key_val']);
        $Pointer->Update($selectedTable, $data, [$pkName => $pkVal]);
        \SW\Source\Server\Web::Refresh(); exit;
    }

    if ($action === 'delete' && $selectedTable) {
        $Pointer->Delete($selectedTable, [$_POST['key'] => $_POST['val']]);
        \SW\Source\Server\Web::Refresh(); exit;
    }
}

// --- DATA FETCHING ---
$tables = $Pointer->FetchTables();
$records = $selectedTable ? $Pointer->FetchAll($selectedTable) : [];
$columns = [];

if ($selectedTable) {
    try {
        $conn = \SW\Source\Modules\SimplySql\Database::GetConnection();
        $q = $conn->query("DESCRIBE `$selectedTable` ");
        $columns = $q->fetchAll(\PDO::FETCH_COLUMN);
    } catch (\Exception $e) {
        $columns = !empty($records) ? array_keys($records[0]) : [];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>DB Manager</title>
    <style>
        :root { --primary: #4f46e5; --bg: #f9fafb; --border: #e5e7eb; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); margin: 0; display: flex; height: 100vh; color: #1f2937; }
        .sidebar { width: 260px; background: white; border-right: 1px solid var(--border); padding: 20px; overflow-y: auto; }
        .nav-item { display: block; padding: 10px; color: #374151; text-decoration: none; border-radius: 6px; margin-bottom: 5px; }
        .nav-item.active { background: var(--primary); color: white; }
        .main { flex-grow: 1; padding: 40px; overflow-y: auto; }
        .card { background: white; border-radius: 12px; border: 1px solid var(--border); padding: 25px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; background: #f8fafc; padding: 12px; border-bottom: 2px solid var(--border); }
        td { padding: 12px; border-bottom: 1px solid var(--border); }
        .btn { padding: 8px 16px; border-radius: 6px; border: none; cursor: pointer; font-weight: 600; }
        .btn-primary { background: var(--primary); color: white; }
        .modal-overlay { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:9999; align-items:center; justify-content:center; }
        .modal { background:white; padding:30px; border-radius:12px; width:450px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display:block; margin-bottom:5px; font-weight:600; font-size: 0.8rem; }
        .form-group input { width:100%; padding:8px; border:1px solid var(--border); border-radius:6px; box-sizing: border-box; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2 style="font-size: 1.1rem;">Tables</h2>
    <?php foreach ($tables as $t): ?>
        <a href="?table=<?php echo urlencode($t); ?>" class="nav-item <?php echo $selectedTable === $t ? 'active' : ''; ?>">
            <?php echo htmlspecialchars($t); ?>
        </a>
    <?php endforeach; ?>
</div>

<div class="main">
    <?php if ($selectedTable): ?>
        <div class="card">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                <h1 style="margin:0; font-size:1.4rem;">Table: <?php echo htmlspecialchars($selectedTable); ?></h1>
                <button class="btn btn-primary" onclick="openAddModal()">+ Add New Row</button>
            </div>

            <table>
                <thead>
                    <tr>
                        <?php foreach ($columns as $col): ?>
                            <th><?php echo htmlspecialchars($col); ?></th>
                        <?php endforeach; ?>
                        <th style="text-align:right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($records)): ?>
                        <?php foreach ($records as $row): ?>
                            <tr>
                                <?php foreach ($row as $val): ?>
                                    <td><?php echo htmlspecialchars($val ?? ''); ?></td>
                                <?php endforeach; ?>
                                <td style="text-align:right;">
                                    <button class="btn" style="background:#e0e7ff; color:#4338ca;" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8'); ?>)">Edit</button>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Delete?')">
                                        <?php \SW\Source\Server\Security\CSRF::Insert() ?>
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="key" value="<?php echo htmlspecialchars($columns[0]); ?>">
                                        <input type="hidden" name="val" value="<?php echo htmlspecialchars($row[$columns[0]]); ?>">
                                        <button type="submit" class="btn" style="background:#fee2e2; color:#b91c1c;">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="<?php echo count($columns)+1; ?>" style="text-align:center; padding:30px;">Empty Table</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div style="height:100%; display:flex; align-items:center; justify-content:center; color:#9ca3af;">
            <h2>Select a table to begin</h2>
        </div>
    <?php endif; ?>
</div>

<div id="dataModal" class="modal-overlay">
    <div class="modal">
        <h3 id="modalTitle" style="margin-top:0;">Edit Row</h3>
        <form method="POST">
            <?php \SW\Source\Server\Security\CSRF::Insert() ?>
            <input type="hidden" name="action" id="formAction" value="update">
            <input type="hidden" name="primary_key_name" value="<?php echo htmlspecialchars($columns[0] ?? ''); ?>">
            <input type="hidden" name="primary_key_val" id="primaryKeyVal">
            <div id="dynamicFields"></div>
            <div style="display:flex; gap:10px; margin-top:25px; justify-content:flex-end;">
                <button type="button" class="btn" style="background:#f3f4f6" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
    const modal = document.getElementById('dataModal');
    const fieldsDiv = document.getElementById('dynamicFields');
    const cols = <?php echo json_encode($columns); ?>;

    function openAddModal() {
        document.getElementById('modalTitle').innerText = "Add Record";
        document.getElementById('formAction').value = "create";
        generateFields({});
        modal.style.display = 'flex';
    }

    function openEditModal(rowData) {
        document.getElementById('modalTitle').innerText = "Edit Record";
        document.getElementById('formAction').value = "update";
        document.getElementById('primaryKeyVal').value = rowData[cols[0]];
        generateFields(rowData);
        modal.style.display = 'flex';
    }

    function generateFields(data) {
        fieldsDiv.innerHTML = '';
        const action = document.getElementById('formAction').value;
        cols.forEach((col, index) => {
            const val = data[col] !== undefined ? data[col] : '';
            const isReadonly = (index === 0 && action === "update");
            fieldsDiv.innerHTML += `
                <div class="form-group">
                    <label>${col}</label>
                    <input type="text" name="${col}" value="${val}" ${isReadonly ? 'readonly style="background:#f3f4f6"' : ''}>
                </div>`;
        });
    }

    function closeModal() { modal.style.display = 'none'; }
    window.onclick = (e) => { if(e.target == modal) closeModal(); }
</script>
</body>
</html>