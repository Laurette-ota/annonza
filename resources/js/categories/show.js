/* Script page catégorie : toggle favoris, filtre auto-submit */
document.addEventListener('DOMContentLoaded', () => {
  // Soumission auto du filtre quand on change "Tri"
  const sortSelect = document.querySelector('select[name="sort"]');
  sortSelect?.addEventListener('change', () => document.getElementById('filterForm').submit());

  // Toggle favoris
  window.toggleFavorite = async (id) => {
    try {
      const res = await fetch(`/favoris/${id}/toggle`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } });
      const data = await res.json();
      const btn = document.getElementById(`favorite-btn-${id}`);
      const svg = btn.querySelector('svg');
      svg.setAttribute('fill', data.is_favorite ? 'currentColor' : 'none');
      svg.classList.toggle('text-red-500', data.is_favorite);
    } catch (e) {
      console.error(e);
    }
  };

  // Initial check des favoris
  document.querySelectorAll('[id^="favorite-btn-"]').forEach(async (btn) => {
    const id = btn.id.split('-')[2];
    try {
      const res = await fetch(`/favoris/${id}/check`);
      const data = await res.json();
      const svg = btn.querySelector('svg');
      svg.setAttribute('fill', data.is_favorite ? 'currentColor' : 'none');
      svg.classList.toggle('text-red-500', data.is_favorite);
    } catch (e) {
      console.error(e);
    }
  });
});