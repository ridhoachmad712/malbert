document.querySelector('.menu-toggle')?.addEventListener('click', function () {
  const expanded = this.getAttribute('aria-expanded') === 'true';
  this.setAttribute('aria-expanded', String(!expanded));
  document.querySelector('#main-nav').classList.toggle('open', !expanded);
});
document.querySelectorAll('[data-confirm]').forEach(form => form.addEventListener('submit', event => {
  if (!confirm(form.dataset.confirm)) event.preventDefault();
}));
let dirty = false;
const editor = document.querySelector('[data-dirty-form]');
editor?.addEventListener('input', () => { dirty = true; });
editor?.addEventListener('submit', event => {
  const bytes = [...editor.querySelectorAll('input[type=file]')].flatMap(input => [...input.files]).reduce((total, file) => total + file.size, 0);
  if (bytes > 30 * 1024 * 1024) { event.preventDefault(); alert('Total gambar maksimal 30 MB. Unggah sisanya setelah draf disimpan.'); return; }
  dirty = false;
});
window.addEventListener('beforeunload', event => { if (dirty) { event.preventDefault(); event.returnValue = ''; } });
document.querySelectorAll('form[data-submit-label]').forEach(form => {
  form.addEventListener('submit', event => {
    if (event.defaultPrevented) return;
    const button = event.submitter || form.querySelector('button[type=submit],button:not([type])');
    if (!button) return;
    button.dataset.originalLabel = button.textContent;
    button.textContent = form.dataset.submitLabel;
    button.disabled = true;
    form.setAttribute('aria-busy', 'true');
  });
});
window.addEventListener('pageshow', () => {
  document.querySelectorAll('button[data-original-label]').forEach(button => {
    button.textContent = button.dataset.originalLabel;
    button.disabled = false;
    button.closest('form').removeAttribute('aria-busy');
    delete button.dataset.originalLabel;
  });
});
let rowIndex = document.querySelectorAll('.drive-row').length;
document.querySelector('#add-drive')?.addEventListener('click', () => {
  const rows = document.querySelector('#drive-rows');
  if (rows.children.length >= 30) { alert('Maksimal 30 tautan per cerita.'); return; }
  rows.insertAdjacentHTML('beforeend', document.querySelector('#drive-template').innerHTML.replaceAll('__INDEX__', rowIndex++));
  rows.lastElementChild.querySelector('input').focus();
  dirty = true;
});
function driveEmbed(value) {
  const url = new URL(value);
  if (url.origin !== 'https://drive.google.com') throw new Error('Gunakan tautan file Google Drive.');
  const id = url.pathname.match(/^\/file\/d\/([\w-]+)/)?.[1] || (['/open', '/uc'].includes(url.pathname) ? url.searchParams.get('id') : '');
  if (!/^[\w-]{10,200}$/.test(id || '')) throw new Error('Gunakan tautan satu file, bukan folder.');
  const result = new URL(`https://drive.google.com/file/d/${id}/preview`);
  const key = url.searchParams.get('resourcekey');
  if (key && /^[\w-]{1,200}$/.test(key)) result.searchParams.set('resourcekey', key);
  return result.href;
}
function frame(src, title) {
  const iframe = document.createElement('iframe');
  iframe.src = src; iframe.title = title; iframe.allow = 'fullscreen'; iframe.allowFullscreen = true;
  iframe.referrerPolicy = 'strict-origin-when-cross-origin';
  return iframe;
}
document.addEventListener('click', event => {
  const button = event.target.closest('button');
  if (!button) return;
  const row = button.closest('.drive-row,.local-row');
  if (button.matches('.remove-row')) { row.remove(); dirty = true; }
  if (button.matches('.move-up') && row.previousElementSibling?.matches('.drive-row,.local-row')) { row.previousElementSibling.before(row); dirty = true; }
  if (button.matches('.move-down') && row.nextElementSibling?.matches('.drive-row,.local-row')) { row.nextElementSibling.after(row); dirty = true; }
  if (button.matches('.preview-drive')) {
    try {
      const src = driveEmbed(row.querySelector('input[type=url]').value);
      const dialog = document.querySelector('#media-dialog');
      dialog.querySelector('.dialog-media').replaceChildren(frame(src, 'Pratinjau Google Drive'));
      dialog.showModal();
    } catch (error) { alert(error.message); }
  }
  if (button.matches('.close-dialog')) {
    const dialog = button.closest('dialog'); dialog.close(); dialog.querySelector('.dialog-media').replaceChildren();
  }
  if (button.matches('.load-drive')) button.parentElement.replaceChildren(frame(button.dataset.src, button.dataset.title));
});
document.querySelector('#media-dialog')?.addEventListener('close', function () { this.querySelector('.dialog-media').replaceChildren(); });
document.querySelector('#cover-input')?.addEventListener('change', function () {
  const file = this.files[0]; if (!file) return;
  if (file.size > 10 * 1024 * 1024) { alert('Ukuran gambar maksimal 10 MB.'); this.value = ''; return; }
  const img = new Image(); const url = URL.createObjectURL(file);
  img.onload = () => URL.revokeObjectURL(url); img.src = url; img.alt = 'Pratinjau sampul pilihan';
  document.querySelector('#cover-preview').replaceChildren(img);
});

document.querySelectorAll('[data-album]').forEach(album => {
  const slides = [...album.querySelectorAll('[data-album-slide]')];
  const controls = album.querySelector('[data-album-controls]');
  let current = 0;
  function show(index) {
    current = (index + slides.length) % slides.length;
    slides.forEach((slide, position) => {
      slide.hidden = position !== current;
      const host = slide.querySelector('[data-album-frame]');
      host.querySelector('iframe')?.remove();
      const fallback = host.querySelector('a');
      if (fallback) fallback.hidden = position === current;
      if (position === current) host.append(frame(host.dataset.src, host.dataset.title || 'Media album'));
    });
    album.querySelector('[data-album-count]').textContent = (current + 1) + ' / ' + slides.length;
  }
  controls.hidden = false;
  album.querySelectorAll('[data-album-prev],[data-album-next]').forEach(button => { button.disabled = slides.length < 2; });
  album.querySelector('[data-album-prev]').addEventListener('click', () => show(current - 1));
  album.querySelector('[data-album-next]').addEventListener('click', () => show(current + 1));
  album.addEventListener('keydown', event => {
    if (!['ArrowLeft', 'ArrowRight'].includes(event.key) || event.target.closest('input,textarea,select')) return;
    event.preventDefault();
    show(current + (event.key === 'ArrowRight' ? 1 : -1));
  });
  show(0);
});
