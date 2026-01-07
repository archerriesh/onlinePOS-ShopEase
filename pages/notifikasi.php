<?php
$pageCSS = '../css/notifikasi.css';
include '../includes/header-main.php';
?>

<div class="notif-wrapper">
    <aside class="col-md-1">
        <div class="sidebar">
            <a href="profile.php" class="icon active" title="Profile">
            <i class="bi bi-person"></i>
            </a>
            <a href="notifikasi.php" class="icon" title="Notifications">
            <i class="bi bi-bell"></i>
            </a>
            <a href="liat-review.php" class="icon" title="Reviews">
            <i class="bi bi-chat-left-text"></i>
            </a>
            <a href="sign-out.php" class="icon logout" id="btnLogout" data-bs-toggle="modal" data-bs-target="#logoutModal">
            <i class="bi bi-box-arrow-right"></i>
            </a>
        </div>
    </aside>
    <section class="notif-content">
        <h2>Notification</h2>

        <div class="notif-card">
            <img src="../assets/img/shoes.png" alt="">
            <div class="notif-text">
                <h4>Order Arrived at Destination</h4>
                <p>Check the completeness of the product for order 251228AJXC. Satisfied with your order ? Rate it !</p>
                <span>31-12-2025&nbsp;&nbsp;08:38</span>
            </div>
        </div>

        <div class="notif-card">
            <img src="../assets/img/bag.png" alt="">
            <div class="notif-text">
                <h4>Order Arrived at Destination</h4>
                <p>Check the completeness of the product for order 251228AJXC. Satisfied with your order ? Rate it !</p>
                <span>31-12-2025&nbsp;&nbsp;08:38</span>
            </div>
        </div>

        <div class="notif-card">
            <img src="../assets/img/shoes2.png" alt="">
            <div class="notif-text">
                <h4>Order Handed Over to Shipping Service</h4>
                <p>Check the details and track order 251358JCAX here.</p>
                <span>29-12-2025&nbsp;&nbsp;08:38</span>
            </div>
        </div>
    </section>
</div>

<?php include '../includes/footer.php'; ?>