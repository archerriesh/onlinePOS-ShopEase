<?php
$pageCSS = '../css/nulis-review.css';
include '../includes/header-main.php';
?>

<div class="review-page">
  <section class="review-card" aria-label="Add a Review">

      <h2 class="review-title">Add a Review</h2>

      <p class="review-subtitle">
        We highly value your feedback! Kindly take a moment to rate your product and provide us with your valuable feedback
      </p>

      <div class="review-stars" aria-label="Rating">
        <span class="review-star" title="1">★</span>
        <span class="review-star" title="2">★</span>
        <span class="review-star" title="3">★</span>
        <span class="review-star" title="4">★</span>
        <span class="review-star" title="5">★</span>
      </div>

      <textarea class="review-comment" placeholder="Provide a detailed review"></textarea>

      <button class="review-submit" type="button" aria-label="Send Review">
        Send
      </button>

  </section>
</div>

<?php include '../includes/footer.php'; ?>