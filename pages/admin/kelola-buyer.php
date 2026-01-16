<?php
$pageCSS = '../../css/admin/kelola-buyer.css';

include __DIR__ . '/../../includes/header-admin.php';
include __DIR__ . '/../../includes/dbOnlinePOS.php';
?>

<div class="toko-container">

    <div class="toko-header">
        <h1 class="section-title">Daftar Buyer</h1>
    </div>

    <div class="buyer-grid">

        <div class="buyer-card">

            <div class="menu-container">
                <button class="menu-dots" onclick="toggleDropdown(event, 'buyer-1')">⋮</button>
                <div id="buyer-1" class="dropdown-content">
                    <a href="#" onclick="return confirm('Yakin ingin menonaktifkan buyer ini?')">
                        Nonaktifkan
                    </a>
                </div>
            </div>

            <div class="buyer-avatar">
                <img src="../../foto/default-user.jpg" alt="Buyer">
            </div>

            <p class="buyer-name">Maria Mayang</p>

            <div class="buyer-stats">
                <span>🛒 12</span>
                <span class="stat-divider">|</span>
                <span>📦 48</span>
            </div>

        </div>
        <div class="buyer-card">

            <div class="menu-container">
                <button class="menu-dots" onclick="toggleDropdown(event, 'buyer-2')">⋮</button>
                <div id="buyer-2" class="dropdown-content">
                    <a href="#" onclick="return confirm('Yakin ingin menonaktifkan buyer ini?')">
                        Nonaktifkan
                    </a>
                </div>
            </div>

            <div class="buyer-avatar">
                <img src="../../foto/default-user.jpg" alt="Buyer">
            </div>

            <p class="buyer-name">Jonathan</p>

            <div class="buyer-stats">
                <span>🛒 5</span>
                <span class="stat-divider">|</span>
                <span>📦 19</span>
            </div>

        </div>

    </div>

</div>

<script>
function toggleDropdown(event, menuId) {
    event.preventDefault();
    event.stopPropagation();

    document.querySelectorAll('.dropdown-content').forEach(d => {
        if (d.id !== menuId) d.classList.remove('show');
    });

    document.getElementById(menuId).classList.toggle('show');
}

window.onclick = function(event) {
    if (!event.target.matches('.menu-dots')) {
        document.querySelectorAll('.dropdown-content')
            .forEach(d => d.classList.remove('show'));
    }
}
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
