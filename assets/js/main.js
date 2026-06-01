/* ============================================================
   NJOSA Alumni Portal – Public JS
   ============================================================ */

(function () {
  'use strict';

  // ── Navbar scroll shadow ────────────────────────────────────
  const navbar = document.querySelector('.navbar');
  if (navbar) {
    const onScroll = () => navbar.classList.toggle('scrolled', window.scrollY > 20);
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  // ── Mobile hamburger ────────────────────────────────────────
  const hamburger = document.querySelector('.hamburger');
  const navLinks  = document.querySelector('.nav-links');
  if (hamburger && navLinks) {
    hamburger.addEventListener('click', () => {
      navLinks.classList.toggle('open');
      hamburger.setAttribute('aria-expanded', navLinks.classList.contains('open'));
    });
    document.addEventListener('click', (e) => {
      if (!navbar.contains(e.target)) navLinks.classList.remove('open');
    });
  }

  // ── Animated counters ───────────────────────────────────────
  function animateCount(el) {
    const target   = parseInt(el.dataset.target, 10);
    const suffix   = el.dataset.suffix || '';
    const duration = 1800;
    const step     = 16;
    const steps    = duration / step;
    const increment = target / steps;
    let   current  = 0;

    const timer = setInterval(() => {
      current += increment;
      if (current >= target) {
        el.textContent = target.toLocaleString() + suffix;
        clearInterval(timer);
      } else {
        el.textContent = Math.floor(current).toLocaleString() + suffix;
      }
    }, step);
  }

  const counters = document.querySelectorAll('[data-counter]');
  if (counters.length) {
    const io = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          animateCount(entry.target);
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.4 });
    counters.forEach(c => io.observe(c));
  }

  // ── Scroll fade-in ──────────────────────────────────────────
  const fadeEls = document.querySelectorAll('.fade-in');
  if (fadeEls.length) {
    const fadeIO = new IntersectionObserver((entries) => {
      entries.forEach(e => e.isIntersecting && e.target.classList.add('visible'));
    }, { threshold: 0.15 });
    fadeEls.forEach(el => fadeIO.observe(el));
  }

  // ── Testimonials slider ─────────────────────────────────────
  const track  = document.querySelector('.testimonials-track');
  const slides = track ? Array.from(track.children) : [];
  let   current = 0;

  if (track && slides.length > 1) {
    const dots   = document.querySelectorAll('.slider-dot');
    const prevBtn = document.querySelector('.slider-prev');
    const nextBtn = document.querySelector('.slider-next');

    const goTo = (idx) => {
      current = (idx + slides.length) % slides.length;
      track.style.transform = `translateX(-${current * 100}%)`;
      dots.forEach((d, i) => d.classList.toggle('active', i === current));
    };

    prevBtn && prevBtn.addEventListener('click', () => goTo(current - 1));
    nextBtn && nextBtn.addEventListener('click', () => goTo(current + 1));
    dots.forEach((d, i) => d.addEventListener('click', () => goTo(i)));

    // Auto-advance
    let autoPlay = setInterval(() => goTo(current + 1), 5000);
    track.addEventListener('mouseenter', () => clearInterval(autoPlay));
    track.addEventListener('mouseleave', () => { autoPlay = setInterval(() => goTo(current + 1), 5000); });

    // Touch swipe
    let startX = 0;
    track.addEventListener('touchstart', e => { startX = e.touches[0].clientX; });
    track.addEventListener('touchend',   e => {
      const diff = startX - e.changedTouches[0].clientX;
      if (Math.abs(diff) > 50) goTo(diff > 0 ? current + 1 : current - 1);
    });
  }

  // ── Gallery Lightbox ────────────────────────────────────────
  const lightbox   = document.getElementById('lightbox');
  const lbImg      = lightbox  && lightbox.querySelector('img');
  const lbClose    = lightbox  && lightbox.querySelector('.lightbox-close');
  const lbPrev     = lightbox  && lightbox.querySelector('.lightbox-prev');
  const lbNext     = lightbox  && lightbox.querySelector('.lightbox-next');
  const galleryItems = Array.from(document.querySelectorAll('.gallery-item'));
  let   lbCurrent  = 0;

  if (lightbox && galleryItems.length) {
    const open = (idx) => {
      lbCurrent = idx;
      const img = galleryItems[idx].querySelector('img');
      lbImg.src = img.dataset.full || img.src;
      lightbox.classList.add('open');
      document.body.style.overflow = 'hidden';
    };
    const close = () => {
      lightbox.classList.remove('open');
      document.body.style.overflow = '';
    };
    const nav = (dir) => open((lbCurrent + dir + galleryItems.length) % galleryItems.length);

    galleryItems.forEach((el, i) => el.addEventListener('click', () => open(i)));
    lbClose.addEventListener('click', close);
    lbPrev.addEventListener('click',  () => nav(-1));
    lbNext.addEventListener('click',  () => nav(1));
    lightbox.addEventListener('click', e => { if (e.target === lightbox) close(); });
    document.addEventListener('keydown', e => {
      if (!lightbox.classList.contains('open')) return;
      if (e.key === 'Escape')      close();
      if (e.key === 'ArrowLeft')   nav(-1);
      if (e.key === 'ArrowRight')  nav(1);
    });
  }

  // ── Dark mode toggle ────────────────────────────────────────
  const dmToggle = document.getElementById('darkModeToggle');
  const saved    = localStorage.getItem('theme') || 'light';
  document.documentElement.dataset.theme = saved;
  if (dmToggle) {
    dmToggle.addEventListener('click', () => {
      const next = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
      document.documentElement.dataset.theme = next;
      localStorage.setItem('theme', next);
    });
  }

  // ── Smooth donate scroll ────────────────────────────────────
  document.querySelectorAll('a[href="#donate"]').forEach(a => {
    a.addEventListener('click', e => {
      e.preventDefault();
      document.querySelector('#donate')?.scrollIntoView({ behavior: 'smooth' });
    });
  });

  // ── Flash auto-dismiss ──────────────────────────────────────
  document.querySelectorAll('.alert').forEach(alert => {
    setTimeout(() => {
      alert.style.transition = 'opacity .5s';
      alert.style.opacity    = '0';
      setTimeout(() => alert.remove(), 500);
    }, 5000);
  });

})();
