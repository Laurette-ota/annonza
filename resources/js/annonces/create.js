/* Script de création d’annonce – 100 % Tailwind / Vite */
document.addEventListener('DOMContentLoaded', () => {
  // Compteurs de caractères
  ['title', 'description'].forEach(id => {
    const el = document.getElementById(id);
    const counter = document.getElementById(`count-${id === 'title' ? 'title' : 'desc'}`);
    if (!el || !counter) return;
    const max = id === 'title' ? 100 : 2000;
    const update = () => {
      const len = el.value.length;
      counter.textContent = `${len}/${max}`;
      counter.classList.toggle('text-red-400', len > max * 0.9);
      counter.classList.toggle('text-yellow-400', len > max * 0.7 && len <= max * 0.9);
    };
    el.addEventListener('input', update);
    update(); // init
  });

  // Upload d’image (simple preview)
  const fileInput = document.getElementById('image');
  const preview   = document.getElementById('preview');
  const dropZone  = document.getElementById('dropZone');

  const showPreview = (file) => {
    if (!file.type.startsWith('image/') || file.size > 5 * 1024 * 1024) {
      alert('Image ≤ 5Mo requise');
      return;
    }
    const reader = new FileReader();
    reader.onload = (e) => {
      preview.src = e.target.result;
      preview.classList.remove('hidden');
    };
    reader.readAsDataURL(file);
  };

  fileInput.addEventListener('change', (e) => showPreview(e.target.files[0]));

  ['dragover', 'dragenter'].forEach(evt =>
    dropZone.addEventListener(evt, (e) => e.preventDefault())
  );
  dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    const file = e.dataTransfer.files[0];
    if (file) { fileInput.files = e.dataTransfer.files; showPreview(file); }
  });
});