/* Script de modification d’annonce */
document.addEventListener('DOMContentLoaded', () => {
  // Compteurs
  ['title', 'description'].forEach(id => {
    const el = document.getElementById(id);
    const counter = document.getElementById(`count-${id === 'title' ? 'title' : 'desc'}`);
    if (!el || !counter) return;
    const max = id === 'title' ? 100 : 2000;
    const update = () => {
      counter.textContent = `${el.value.length}/${max}`;
    };
    el.addEventListener('input', update);
    update();
  });

  // Preview image édition
  const fileInput = document.getElementById('image');
  const preview   = document.getElementById('previewEdit');
  fileInput?.addEventListener('change', (e) => {
    const file = e.target.files[0];
    if (!file || !file.type.startsWith('image/')) return;
    const reader = new FileReader();
    reader.onload = (e) => { preview.src = e.target.result; preview.classList.remove('hidden'); };
    reader.readAsDataURL(file);
  });
});