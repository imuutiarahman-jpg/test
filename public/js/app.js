// assets/js/app.js — JS global: konfirmasi hapus, toggle FAQ, drawer sidebar Android.
document.addEventListener('click', (e) => {
  const del = e.target.closest('[data-confirm]');
  if (del && !confirm(del.dataset.confirm || 'Hapus data ini?')) e.preventDefault();
});

(function () {
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('overlay');
  const btnMenu = document.getElementById('btnMenu');
  const btnClose = document.getElementById('btnClose');
  if (!sidebar || !overlay || !btnMenu) return;
  function openMenu() {
    sidebar.classList.add('open');
    overlay.hidden = false;
    requestAnimationFrame(() => overlay.classList.add('show'));
    btnMenu.setAttribute('aria-expanded', 'true');
    document.body.style.overflow = 'hidden';
  }
  function closeMenu() {
    sidebar.classList.remove('open');
    overlay.classList.remove('show');
    btnMenu.setAttribute('aria-expanded', 'false');
    document.body.style.overflow = '';
    setTimeout(() => { if (!sidebar.classList.contains('open')) overlay.hidden = true; }, 200);
  }
  btnMenu.addEventListener('click', () => {
    sidebar.classList.contains('open') ? closeMenu() : openMenu();
  });
  if (btnClose) btnClose.addEventListener('click', closeMenu);
  overlay.addEventListener('click', closeMenu);
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeMenu(); });
  // Otomatis tutup drawer setelah pilih menu di HP
  sidebar.querySelectorAll('nav a').forEach((a) => a.addEventListener('click', () => {
    if (window.innerWidth <= 1023) closeMenu();
  }));
})();
function toggleFaq(btn) {
  const d = btn.nextElementSibling;
  if (d) d.style.display = d.style.display === 'none' ? 'block' : 'none';
}
