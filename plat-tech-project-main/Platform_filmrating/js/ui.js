/*
  Refactored UI script
  - Menu toggling
  - Watchlist helper
  - Star rating module: per-slide ratings with carousel integration
  - Toast notifications (non-blocking)

  Goals: clearer structure, robust null checks, and easier maintainability.
*/

(function () {
  'use strict';

  // ---- Utilities ----
  const q = (sel, ctx = document) => ctx.querySelector(sel);
  const qAll = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));

  function readJSON(key, fallback) {
    try {
      return JSON.parse(localStorage.getItem(key)) || fallback;
    } catch (e) {
      return fallback;
    }
  }

  function writeJSON(key, val) {
    localStorage.setItem(key, JSON.stringify(val));
  }

  // Simple toast implementation (non-blocking)
  function showToast(message, timeout = 2200) {
    let node = document.createElement('div');
    node.className = 'cr-toast';
    node.textContent = message;
    Object.assign(node.style, {
      position: 'fixed',
      right: '16px',
      bottom: '22px',
      background: 'rgba(20,20,20,0.9)',
      color: '#fff',
      padding: '8px 12px',
      borderRadius: '6px',
      zIndex: 9999,
      fontSize: '13px',
      boxShadow: '0 6px 18px rgba(0,0,0,0.45)'
    });
    document.body.appendChild(node);
    setTimeout(() => node.classList.add('cr-toast-show'), 20);
    setTimeout(() => {
      node.classList.remove('cr-toast-show');
      setTimeout(() => node.remove(), 250);
    }, timeout);
  }

  // ---- Menu Toggle ----
  function initMenu() {
    const menuBtn = q('.menu-btn');
    const dropdown = q('.menu-dropdown');
    if (!menuBtn || !dropdown) return;

    menuBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      const open = dropdown.style.display === 'block';
      dropdown.style.display = open ? 'none' : 'block';
      menuBtn.setAttribute('aria-expanded', String(!open));
    });

    document.addEventListener('click', (e) => {
      if (!menuBtn.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.style.display = 'none';
        menuBtn.setAttribute('aria-expanded', 'false');
      }
    });
  }

  // ---- Watchlist ----
  function initWatchlist() {
    const watchlistBtn = q('#watchlistBtn');
    const movieEl = q('.movie');
    if (!watchlistBtn || !movieEl) return;

    function getWatchlist() { return readJSON('watchlist', []); }
    function saveWatchlist(list) { writeJSON('watchlist', list); }

    watchlistBtn.addEventListener('click', () => {
      const id = movieEl.dataset.id;
      const title = movieEl.dataset.title || id;
      if (!id) {
        showToast('No movie selected');
        return;
      }
      const list = getWatchlist();
      if (!list.includes(id)) {
        list.push(id);
        saveWatchlist(list);
        showToast(`${title} added to Watchlist`);
      } else {
        showToast(`${title} is already in your Watchlist`);
      }
    });
  }

  // ---- Rating Module ----
  function initRating() {
    const ratingForm = q('.rating-form');
    if (!ratingForm) return;

    const inputs = qAll('input[name="rating"]', ratingForm);
    const labels = qAll('label[for^="star"]', ratingForm);
    const rateBtn = q('.rate-btn', ratingForm);

    function storageKey(id) { return `rating_${id}`; }

    // get active slide context (carousel) or fallback to .movie
    function currentMovieContext() {
      const active = q('.carousel-item.active');
      const movieBlock = q('.movie');
      const id = (active && active.dataset.id) || (movieBlock && movieBlock.dataset.id) || null;
      const title = (active && active.dataset.title) || (movieBlock && movieBlock.dataset.title) || id || '';
      return { id, title };
    }

    function highlightStars(value) {
      // labels are ordered star5..star1 left-to-right. Highlight labels with val >= value
      labels.forEach(label => {
        const val = parseInt(label.htmlFor.replace('star',''), 10);
        if (!isNaN(val) && value > 0 && val >= value) label.classList.add('selected');
        else label.classList.remove('selected');
      });
    }

    function refreshForMovie(id) {
      // clear checks
      inputs.forEach(i => i.checked = false);
      if (!id) { highlightStars(0); return; }
      const saved = parseInt(localStorage.getItem(storageKey(id)), 10) || 0;
      if (saved > 0) {
        const node = q(`#star${saved}`);
        if (node) node.checked = true;
        highlightStars(saved);
      } else {
        highlightStars(0);
      }
    }

    // input change
    inputs.forEach(input => {
      input.addEventListener('change', (e) => {
        const { id, title } = currentMovieContext();
        const v = parseInt(e.target.value, 10);
        if (!id) return showToast('No movie selected');
        if (!isNaN(v)) {
          localStorage.setItem(storageKey(id), String(v));
          highlightStars(v);
        }
      });
    });

    // hover preview
    labels.forEach(label => {
      label.addEventListener('mouseenter', () => {
        const v = parseInt(label.htmlFor.replace('star',''), 10);
        if (!isNaN(v)) highlightStars(v);
      });
      label.addEventListener('mouseleave', () => {
        const { id } = currentMovieContext();
        const saved = parseInt(localStorage.getItem(storageKey(id)), 10) || 0;
        highlightStars(saved);
      });
    });

    // rate button
    if (rateBtn) {
      rateBtn.addEventListener('click', () => {
        const checked = ratingForm.querySelector('input[name="rating"]:checked');
        const { id, title } = currentMovieContext();
        if (!id) return showToast('No movie selected');
        if (!checked) return showToast('Please select a rating first');
        const v = parseInt(checked.value, 10);
        if (isNaN(v)) return;
        localStorage.setItem(storageKey(id), String(v));
        highlightStars(v);
        showToast(`Thanks — you rated ${title} ${v}/5`);
        // TODO: send to server with fetch if needed
      });
    }

    // respond to carousel movieChange events
    window.addEventListener('movieChange', (e) => {
      const d = e && e.detail ? e.detail : null;
      if (!d || !d.id) return;
      refreshForMovie(d.id);
    });

    // initialize from current slide or movie block
    const ctx = currentMovieContext();
    if (ctx && ctx.id) refreshForMovie(ctx.id);
  }

  // ---- Initialization ----
  function initAll() {
    initMenu();
    initWatchlist();
    initRating();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAll);
  } else {
    // DOM already ready (scripts loaded at end of body) — initialize immediately
    initAll();
  }

})();

