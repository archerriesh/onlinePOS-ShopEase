<?php
$pageCSS = '../css/liat-review.css';
include '../includes/header-main.php';
?>


<div class="review-container">

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

    <h1>Product Review</h1>

    <div class="rating-summary">

        <div class="left-rating">
            <div class="score">5.0</div>
            <div class="stars">★★★★★</div>
        </div>

        <form class="rating-filter">

            <input type="radio" id="star1" name="rating">
            <label for="star1">
                <span class="star">★</span> 1 star
            </label>

            <input type="radio" id="star2" name="rating">
            <label for="star2">
                <span class="star">★★</span> 2 star
            </label>

            <input type="radio" id="star3" name="rating">
            <label for="star3">
                <span class="star">★★★</span> 3 star
            </label>

            <input type="radio" id="star4" name="rating">
            <label for="star4" class="center">
                <span class="star">★★★★</span> 4 star
            </label>

            <input type="radio" id="star5" name="rating">
            <label for="star5" class="center">
                <span class="star">★★★★★</span> 5 star
            </label>

        </form>
    </div>

    <div class="review-list">

        <div class="review-card">
            <div class="avatar"></div>
            <div class="review-content">
                <div class="review-stars">★★★★★</div>
                <p>
                    Lorem Ipsum is simply dummy text of the printing and typesetting industry.
                    Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.
                </p>
            </div>
        </div>

        <div class="review-card">
            <div class="avatar"></div>
            <div class="review-content">
                <div class="review-stars">★★★★★</div>
                <p>
                    Lorem Ipsum is simply dummy text of the printing and typesetting industry.
                    Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.
                </p>
            </div>
        </div>

        <div class="review-card">
            <div class="avatar"></div>
            <div class="review-content">
                <div class="review-stars">★★★★★</div>
                <p>
                    Lorem Ipsum is simply dummy text of the printing and typesetting industry.
                    Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.
                </p>
            </div>
        </div>

        <div class="review-card">
            <div class="avatar"></div>
            <div class="review-content">
                <div class="review-stars">★★★★★</div>
                <p>
                    Lorem Ipsum is simply dummy text of the printing and typesetting industry.
                    Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.
                </p>
            </div>
        </div>

    </div>

</div>

<?php include '../includes/footer.php'; ?>