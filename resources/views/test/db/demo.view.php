<?php
    $DBPointer = new \SW\Source\Modules\SimplySql\Pointer();
    $tableName = "test_data";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        \SW\Source\Server\Security\CSRF::Validate();
        if (isset($_POST['action']) && $_POST['action'] === 'update') {
            $DBPointer->Update($tableName, ["test_field" => $_POST['test_input']], ["id" => (int)$_POST['row_id']]);
            \SW\Source\Server\Web::Refresh(); exit;
        }
        if (isset($_POST['action']) && $_POST['action'] === 'delete') {
            $DBPointer->Delete($tableName, ["id" => (int)$_POST['row_id']]);
            \SW\Source\Server\Web::Refresh(); exit;
        }
        if (isset($_POST['action']) && $_POST['action'] === 'create') {
            $DBPointer->Insert($tableName, ["test_field" => $_POST['new_value']]);
            \SW\Source\Server\Web::Refresh(); exit;
        }
    }

    $allRecords = $DBPointer->FetchAll($tableName); 
    ?>

    <style>
        :root { --primary: #4f46e5; --danger: #ef4444; --bg: #f3f4f6; --text: #1f2937; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); padding: 40px; display: flex; flex-direction: column; align-items: center; }
        .container { width: 100%; max-width: 700px; }
        .card { background: white; padding: 1.5rem; border-radius: 12px; border: 1px solid #e5e7eb; margin-bottom: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        
        /* Buttons & Inputs */
        input[type="text"] { padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem; }
        button { padding: 8px 16px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; transition: 0.2s; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-secondary { background: #e5e7eb; color: #374151; }
        .btn-danger { background: #fee2e2; color: var(--danger); }
        
        /* Table */
        .record-table { width: 100%; border-collapse: collapse; }
        .record-table td { padding: 15px 0; border-bottom: 1px solid #f3f4f6; }
        .badge { background: #f3f4f6; padding: 2px 6px; border-radius: 4px; font-size: 0.7rem; font-family: monospace; }

        /* MODAL STYLES */
        .modal-overlay { 
            display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
            background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000; 
        }
        .modal { background: white; padding: 2rem; border-radius: 12px; width: 90%; max-width: 400px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); }
    </style>

    <div class="container">
        <header style="margin-bottom: 24px;">
            <h2 style="margin: 0;">Database Manager</h2>
            <p style="color: #6b7280;">Manage your records with ease.</p>
        </header>

        <div class="card">
            <h3 style="margin: 0 0 15px 0; font-size: 1rem;">Add New Record</h3>
            <form method="POST" style="display: flex; gap: 10px;">
                <?php \SW\Source\Server\Security\CSRF::Insert() ?>
                <input type="hidden" name="action" value="create">
                <input type="text" name="new_value" placeholder="Enter content..." style="flex-grow: 1;" required>
                <button type="submit" class="btn-primary">Add Row</button>
            </form>
        </div>

        <div class="card">
            <?php if (!empty($allRecords)): ?>
                <table class="record-table">
                    <tbody>
                        <?php foreach ($allRecords as $row): ?>
                            <tr>
                                <td width="50"><span class="badge"><?php echo $row['id']; ?></span></td>
                                <td><strong id="val-<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['test_field']); ?></strong></td>
                                <td style="text-align: right;">
                                    <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                        <button class="btn-secondary" onclick="openEditModal(<?php echo $row['id']; ?>)">Edit</button>
                                        
                                        <form method="POST" style="margin:0;" onsubmit="return confirm('Delete this row?');">
                                            <?php \SW\Source\Server\Security\CSRF::Insert() ?>
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="row_id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" class="btn-danger">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="text-align: center; color: #9ca3af;">Empty table.</div>
            <?php endif; ?>
        </div>
    </div>

    <div id="editModal" class="modal-overlay">
        <div class="modal">
            <h3 style="margin-top: 0;">Edit Record</h3>
            <form method="POST">
                <?php \SW\Source\Server\Security\CSRF::Insert() ?>
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="row_id" id="modal_row_id">
                
                <div style="margin-bottom: 20px;">
                    <label style="display:block; font-size: 0.8rem; margin-bottom: 5px; color: #6b7280;">Content</label>
                    <input type="text" name="test_input" id="modal_test_input" style="width: 100%; box-sizing: border-box;" required>
                </div>

                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" class="btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('editModal');
        const modalIdInput = document.getElementById('modal_row_id');
        const modalTextInput = document.getElementById('modal_test_input');

        function openEditModal(id) {
            // Find the current text in the table using the ID
            const currentText = document.getElementById('val-' + id).innerText;
            
            // Fill the modal inputs
            modalIdInput.value = id;
            modalTextInput.value = currentText;
            
            // Show the modal
            modal.style.display = 'flex';
        }

        function closeModal() {
            modal.style.display = 'none';
        }

        // Close modal if user clicks outside the white box
        window.onclick = function(event) {
            if (event.target == modal) closeModal();
        }
    </script>