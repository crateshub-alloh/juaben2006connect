/* ============================================================
   NJOSA Alumni Portal – Dashboard JS
   ============================================================ */

(function () {
  'use strict';

  // ── Sidebar toggle (mobile) ─────────────────────────────────
  const sidebar  = document.querySelector('.sidebar');
  const sideToggle = document.querySelector('.sidebar-toggle');
  const overlay    = document.querySelector('.sidebar-overlay');

  if (sideToggle && sidebar) {
    sideToggle.addEventListener('click', () => {
      sidebar.classList.toggle('open');
      overlay && overlay.classList.toggle('show');
    });
    overlay && overlay.addEventListener('click', () => {
      sidebar.classList.remove('open');
      overlay.classList.remove('show');
    });
  }

  // ── Dark mode ───────────────────────────────────────────────
  const saved = localStorage.getItem('theme') || 'light';
  document.documentElement.dataset.theme = saved;
  const dmBtn = document.getElementById('darkModeToggle');
  if (dmBtn) {
    dmBtn.addEventListener('click', () => {
      const next = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
      document.documentElement.dataset.theme = next;
      localStorage.setItem('theme', next);
    });
  }

  // ── Notification dropdown ───────────────────────────────────
  const notifBtn  = document.getElementById('notifBtn');
  const notifDrop = document.getElementById('notifDrop');
  if (notifBtn && notifDrop) {
    notifBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      notifDrop.classList.toggle('open');
    });
    document.addEventListener('click', () => notifDrop.classList.remove('open'));
  }

  // ── Flash auto-dismiss ──────────────────────────────────────
  document.querySelectorAll('.alert').forEach(alert => {
    setTimeout(() => {
      alert.style.transition = 'opacity .5s';
      alert.style.opacity    = '0';
      setTimeout(() => alert.remove(), 500);
    }, 5000);
  });

  // ── Mini Chart renderer (Chart.js via CDN) ──────────────────
  window.renderLineChart = function (canvasId, labels, data, label = '', color = '#1a3c6e') {
    const canvas = document.getElementById(canvasId);
    if (!canvas || typeof Chart === 'undefined') return;
    new Chart(canvas, {
      type: 'line',
      data: {
        labels,
        datasets: [{
          label,
          data,
          borderColor: color,
          backgroundColor: color + '22',
          fill: true,
          tension: 0.4,
          pointRadius: 4,
          pointHoverRadius: 6,
        }],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          x: { grid: { display: false } },
          y: { beginAtZero: true, grid: { color: '#f0f0f0' } },
        },
      },
    });
  };

  window.renderBarChart = function (canvasId, labels, data, label = '', color = '#c9a227') {
    const canvas = document.getElementById(canvasId);
    if (!canvas || typeof Chart === 'undefined') return;
    new Chart(canvas, {
      type: 'bar',
      data: {
        labels,
        datasets: [{
          label,
          data,
          backgroundColor: color + 'cc',
          borderColor: color,
          borderWidth: 2,
          borderRadius: 6,
        }],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          x: { grid: { display: false } },
          y: { beginAtZero: true, grid: { color: '#f0f0f0' } },
        },
      },
    });
  };

  window.renderDoughnut = function (canvasId, labels, data, colors) {
    const canvas = document.getElementById(canvasId);
    if (!canvas || typeof Chart === 'undefined') return;
    new Chart(canvas, {
      type: 'doughnut',
      data: {
        labels,
        datasets: [{ data, backgroundColor: colors, borderWidth: 0 }],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '70%',
        plugins: { legend: { position: 'bottom' } },
      },
    });
  };

  // ── Confirm delete ──────────────────────────────────────────
  document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', (e) => {
      const msg = el.dataset.confirm || 'Are you sure?';
      if (!confirm(msg)) e.preventDefault();
    });
  });

  // ── Table row search ────────────────────────────────────────
  const tableSearch = document.getElementById('tableSearch');
  if (tableSearch) {
    tableSearch.addEventListener('input', () => {
      const q = tableSearch.value.toLowerCase();
      document.querySelectorAll('[data-searchable] tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
      });
    });
  }

  // ── Avatar preview ──────────────────────────────────────────
  const avatarInput = document.getElementById('avatarInput');
  const avatarPreview = document.getElementById('avatarPreview');
  if (avatarInput && avatarPreview) {
    avatarInput.addEventListener('change', () => {
      const file = avatarInput.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = e => { avatarPreview.src = e.target.result; };
        reader.readAsDataURL(file);
      }
    });
  }

  // ── Progress bar animation ──────────────────────────────────
  const progressBars = document.querySelectorAll('.progress__bar[data-width]');
  progressBars.forEach(bar => {
    setTimeout(() => { bar.style.width = bar.dataset.width + '%'; }, 200);
  });

})();
