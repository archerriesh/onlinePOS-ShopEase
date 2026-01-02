<?php
$pageCSS = 'css/nulis-review.css';
include 'includes/header-main.php';
?>

<div class="page-bg">
  <section class="review-modal" aria-label="Add a Review">
      <h2 class="title">Add a Review</h2>
      <p class="subtitle">
        We highly value your feedback! Kindly take a moment to rate your product and provide us with your valuable feedback
      </p>

      <div class="stars-row" aria-label="Rating">
        <span class="star" title="1">★</span>
        <span class="star" title="2">★</span>
        <span class="star" title="3">★</span>
        <span class="star" title="4">★</span>
        <span class="star" title="5">★</span>
      </div>

      <textarea class="comment" placeholder="Provide a detailed review"></textarea>

      <button class="send-btn" type="button" aria-label="Send Review">Send</button>
  </section>
</div>

<?php include 'includes/footer.php'; ?>