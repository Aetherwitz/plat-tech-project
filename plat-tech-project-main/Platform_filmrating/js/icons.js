document.addEventListener('DOMContentLoaded', function() {
  let selectedRating = 0; // This will store the rating value when a star is clicked

  // Handle hover effect over stars
  const stars = document.querySelectorAll('.star');
  
  stars.forEach(star => {
    star.addEventListener('mouseover', function() {
      const ratingValue = parseInt(star.getAttribute('data-value'));
      highlightStars(ratingValue);
    });

    star.addEventListener('mouseout', function() {
      highlightStars(selectedRating); // Revert to the current selected rating
    });

    star.addEventListener('click', function() {
      selectedRating = parseInt(star.getAttribute('data-value'));
      updateStars();
    });
  });

  // Function to highlight stars up to the current rating value
  function highlightStars(ratingValue) {
    stars.forEach(star => {
      const starValue = parseInt(star.getAttribute('data-value'));
      if (starValue <= ratingValue) {
        star.classList.add('hover');
      } else {
        star.classList.remove('hover');
      }
    });
  }

  // Function to update stars based on the clicked rating
  function updateStars() {
    stars.forEach(star => {
      const starValue = parseInt(star.getAttribute('data-value'));
      if (starValue <= selectedRating) {
        star.classList.add('selected');
      } else {
        star.classList.remove('selected');
      }
    });
  }

  // Handle watchlist pin click
  const watchlistPin = document.getElementById('watchlist');
  
  watchlistPin.addEventListener('click', function() {
    watchlistPin.classList.toggle('active'); // Toggle the pin state (active or not)
    const isAdded = watchlistPin.classList.contains('active');
    if (isAdded) {
      alert('Added to Watchlist');
    } else {
      alert('Removed from Watchlist');
    }
  });
});
