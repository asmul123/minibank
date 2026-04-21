<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<style>
    .backup-container {
        background: #fff;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-top: 20px;
    }
    .backup-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 10px;
    }
    .backup-btn {
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.3s ease;
    }
    .backup-btn-primary {
        background-color: #2ecc71;
        color: white;
    }
    .backup-btn-primary:hover {
        background-color: #27ae60;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    .backup-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    .backup-table th {
        background-color: #34495e;
        color: white;
        padding: 12px;
        text-align: left;
        font-weight: 600;
    }
    .backup-table td {
        padding: 12px;
        border-bottom: 1px solid #ddd;
    }
    .backup-table tr:hover {
        background-color: #f5f5f5;
    }
    .backup-table tbody tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    .action-btn {
        padding: 6px 12px;
        margin: 2px;
        border: none;
        border-radius: 3px;
        cursor: pointer;
        font-size: 12px;
        transition: all 0.3s ease;
    }
    .action-btn-download {
        background-color: #3498db;
        color: white;
    }
    .action-btn-download:hover {
        background-color: #2980b9;
    }
    .action-btn-restore {
        background-color: #f39c12;
        color: white;
    }
    .action-btn-restore:hover {
        background-color: #e67e22;
    }
    .action-btn-delete {
        background-color: #e74c3c;
        color: white;
    }
    .action-btn-delete:hover {
        background-color: #c0392b;
    }
    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    .alert-error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    .alert-info {
        background-color: #d1ecf1;
        color: #0c5460;
        border: 1px solid #bee5eb;
    }
    .stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }
    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        border-radius: 5px;
        text-align: center;
    }
    .stat-card.databases {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    .stat-card.backups {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    .stat-value {
        font-size: 32px;
        font-weight: bold;
        margin-bottom: 5px;
    }
    .stat-label {
        font-size: 14px;
        opacity: 0.9;
    }
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #7f8c8d;
    }
    .empty-state-icon {
        font-size: 48px;
        margin-bottom: 15px;
        opacity: 0.5;
    }
    .size-info {
        font-size: 12px;
        color: #7f8c8d;
    }
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
    }
    .modal-content {
        background-color: #fff;
        margin: 15% auto;
        padding: 30px;
        border: 1px solid #ddd;
        border-radius: 5px;
        width: 80%;
        max-width: 400px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    }
    .modal-header {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 15px;
        color: #34495e;
    }
    .modal-body {
        margin-bottom: 20px;
        color: #555;
    }
    .modal-buttons {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
    }
    .modal-buttons button {
        padding: 8px 16px;
        border: none;
        border-radius: 3px;
        cursor: pointer;
        font-size: 14px;
    }
    .modal-btn-cancel {
        background-color: #bdc3c7;
        color: white;
    }
    .modal-btn-confirm {
        background-color: #e74c3c;
        color: white;
    }
</style>

<div class="content">
    <div class="container-fluid">
        <div class="page-header">
            <div class="page-title">
                <h1><i class="fa fa-database"></i> Backup Database</h1>
            </div>
        </div>

        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success">
                <i class="fa fa-check-circle"></i>
                <?php echo $this->session->flashdata('success'); ?>
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-error">
                <i class="fa fa-times-circle"></i>
                <?php echo $this->session->flashdata('error'); ?>
            </div>
        <?php endif; ?>

        <div class="backup-container">
            <div class="stats">
                <div class="stat-card">
                    <div class="stat-value">
                        <i class="fa fa-database"></i>
                    </div>
                    <div class="stat-label">Database: db_bms</div>
                </div>
                <div class="stat-card backups">
                    <div class="stat-value"><?php echo count($backup_files); ?></div>
                    <div class="stat-label">File Backup</div>
                </div>
            </div>

            <div class="backup-header">
                <h3>Manajemen Backup Database</h3>
                <form method="post" action="<?php echo site_url('backup/create_backup'); ?>" style="margin: 0;">
                    <button type="submit" class="backup-btn backup-btn-primary" onclick="return confirm('Buat backup database sekarang?')">
                        <i class="fa fa-download"></i> Buat Backup Baru
                    </button>
                </form>
            </div>

            <?php if (empty($backup_files)): ?>
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fa fa-inbox"></i>
                    </div>
                    <p>Tidak ada file backup</p>
                    <p style="font-size: 12px;">Klik tombol "Buat Backup Baru" untuk membuat backup database</p>
                </div>
            <?php else: ?>
                <table class="backup-table">
                    <thead>
                        <tr>
                            <th style="width: 40%;">Nama File</th>
                            <th style="width: 20%;">Ukuran</th>
                            <th style="width: 25%;">Tanggal</th>
                            <th style="width: 15%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($backup_files as $file): ?>
                            <tr>
                                <td>
                                    <strong><?php echo $file['name']; ?></strong>
                                </td>
                                <td>
                                    <span class="size-info">
                                        <?php 
                                        $size = $file['size'];
                                        if ($size >= 1073741824) {
                                            echo round($size / 1073741824, 2) . ' GB';
                                        } elseif ($size >= 1048576) {
                                            echo round($size / 1048576, 2) . ' MB';
                                        } elseif ($size >= 1024) {
                                            echo round($size / 1024, 2) . ' KB';
                                        } else {
                                            echo $size . ' B';
                                        }
                                        ?>
                                    </span>
                                </td>
                                <td><?php echo $file['date_formatted']; ?></td>
                                <td>
                                    <a href="<?php echo site_url('backup/download/' . urlencode($file['name'])); ?>" class="action-btn action-btn-download" title="Download">
                                        <i class="fa fa-download"></i> Download
                                    </a>
                                    <button class="action-btn action-btn-restore" onclick="confirmRestore('<?php echo addslashes($file['name']); ?>')" title="Restore">
                                        <i class="fa fa-refresh"></i> Restore
                                    </button>
                                    <button class="action-btn action-btn-delete" onclick="confirmDelete('<?php echo addslashes($file['name']); ?>')" title="Hapus">
                                        <i class="fa fa-trash"></i> Hapus
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Delete -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">Konfirmasi Penghapusan</div>
        <div class="modal-body">
            <p>Apakah Anda yakin ingin menghapus file backup ini?</p>
            <p style="color: #e74c3c; font-weight: bold;" id="deleteFileName"></p>
            <p style="font-size: 12px; color: #7f8c8d;">Tindakan ini tidak dapat dibatalkan.</p>
        </div>
        <div class="modal-buttons">
            <button onclick="closeDeleteModal()" class="modal-btn-cancel">Batal</button>
            <button onclick="confirmDeleteAction()" class="modal-btn-confirm">Hapus</button>
        </div>
    </div>
</div>

<!-- Modal Restore -->
<div id="restoreModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">Konfirmasi Restore Database</div>
        <div class="modal-body">
            <p><strong style="color: #e74c3c;">PERINGATAN!</strong></p>
            <p>Restore akan mengganti semua data database dengan data dari file backup ini.</p>
            <p style="color: #e74c3c; font-weight: bold;" id="restoreFileName"></p>
            <p style="font-size: 12px; color: #7f8c8d;">Pastikan Anda telah membuat backup terbaru sebelum melanjutkan.</p>
        </div>
        <div class="modal-buttons">
            <button onclick="closeRestoreModal()" class="modal-btn-cancel">Batal</button>
            <button onclick="confirmRestoreAction()" class="modal-btn-confirm">Lanjutkan Restore</button>
        </div>
    </div>
</div>

<script>
    let deleteFileName = '';
    let restoreFileName = '';

    function confirmDelete(filename) {
        deleteFileName = filename;
        document.getElementById('deleteFileName').textContent = filename;
        document.getElementById('deleteModal').style.display = 'block';
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').style.display = 'none';
        deleteFileName = '';
    }

    function confirmDeleteAction() {
        if (deleteFileName) {
            window.location.href = '<?php echo site_url("backup/delete/"); ?>' + encodeURIComponent(deleteFileName);
        }
    }

    function confirmRestore(filename) {
        restoreFileName = filename;
        document.getElementById('restoreFileName').textContent = filename;
        document.getElementById('restoreModal').style.display = 'block';
    }

    function closeRestoreModal() {
        document.getElementById('restoreModal').style.display = 'none';
        restoreFileName = '';
    }

    function confirmRestoreAction() {
        if (restoreFileName) {
            window.location.href = '<?php echo site_url("backup/restore/"); ?>' + encodeURIComponent(restoreFileName);
        }
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        let deleteModal = document.getElementById('deleteModal');
        let restoreModal = document.getElementById('restoreModal');
        
        if (event.target === deleteModal) {
            deleteModal.style.display = 'none';
        }
        if (event.target === restoreModal) {
            restoreModal.style.display = 'none';
        }
    }
</script>
