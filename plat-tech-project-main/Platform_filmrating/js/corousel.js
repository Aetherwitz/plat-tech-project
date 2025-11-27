const hero = document.querySelector('.hero');
const items = document.querySelectorAll('.carousel-item');
const dots = document.querySelectorAll('.dot');

// Movie info elements
const title = document.getElementById('movie-title');
const fullTitle = document.getElementById('movie-full-title');
const genre = document.getElementById('movie-genre');
const wiki = document.getElementById('wiki-link');
const releaseDate = document.getElementById('movie-date');
const description = document.getElementById('movie-description');

let current = 0;

function updateCarousel(index) {
  // Toggle active state for items and dots
  items.forEach((item, i) => {
    item.classList.toggle('active', i === index);
    dots[i].classList.toggle('active', i === index);
  });

  const activeItem = items[index];

  // Get movie data
  const bgImage = activeItem.getAttribute('data-bg');
  const movieTitle = activeItem.getAttribute('data-title');
  const movieFullTitle = activeItem.getAttribute('data-fulltitle');
  const movieGenre = activeItem.getAttribute('data-genre');
  const movieWiki = activeItem.getAttribute('data-wiki');
  const movieDate = activeItem.getAttribute('data-date');
  const movieDescription = activeItem.getAttribute('data-description');

  // Update hero background
  hero.style.backgroundImage = `url('${bgImage}')`;

  // Update movie info
  title.textContent = movieTitle;
  fullTitle.textContent = movieFullTitle;
  genre.textContent = movieGenre;

  // Fix wiki text so it displays properly
  wiki.textContent = movieWiki.replace("https://", "").replace("www.", "");
  wiki.href = movieWiki;

  releaseDate.textContent = movieDate;
  description.textContent = movieDescription;

  current = index;

  // Compute an identifier for this slide (use data-id if present, otherwise slugify title)
  function slugify(text) {
    return text.toString().toLowerCase().trim()
      .replace(/\s+/g, '_')           // Replace spaces with _
      .replace(/[^a-z0-9_\-]/g, '')   // Remove invalid chars
      .replace(/_+/g, '_');            // Collapse multiple underscores
  }

  const movieId = activeItem.getAttribute('data-id') || `movie_${slugify(movieTitle)}`;

  // If there's a .movie element on the page, update its dataset so other scripts can read current movie
  const movieEl = document.querySelector('.movie');
  if (movieEl) {
    movieEl.dataset.id = movieId;
    movieEl.dataset.title = movieTitle;
    movieEl.dataset.genre = movieGenre;
  }

  // Dispatch a global event so other modules (e.g., rating) can react to movie change
  window.dispatchEvent(new CustomEvent('movieChange', { detail: {
    id: movieId,
    title: movieTitle,
    genre: movieGenre,
    index: index
  }}));
}

// Click listeners
items.forEach((item, index) => {
  item.addEventListener('click', () => updateCarousel(index));
});

dots.forEach((dot, index) => {
  dot.addEventListener('click', () => updateCarousel(index));
});

// Auto-rotate every 4 seconds
setInterval(() => {
  current = (current + 1) % items.length;
  updateCarousel(current);
}, 4000);

// Initialize first slide
updateCarousel(0);
