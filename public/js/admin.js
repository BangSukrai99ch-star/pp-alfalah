/* ============================================================
   ADMIN PANEL JS - Sistem Informasi Pesantren
   ============================================================ */

document.addEventListener('DOMContentLoaded', function () {

  // Kompres gambar besar di browser agar batas 5 MB tidak memblokir upload.
  const maxImageBytes = 5 * 1024 * 1024;
  const compressImage = function (file) {
    return new Promise(function (resolve) {
      if (!file.type.startsWith('image/') || file.size <= maxImageBytes) {
        resolve(file);
        return;
      }

      const reader = new FileReader();
      reader.onload = function () {
        const image = new Image();
        image.onload = function () {
          const maxDimension = 2400;
          const scale = Math.min(1, maxDimension / Math.max(image.width, image.height));
          const canvas = document.createElement('canvas');
          canvas.width = Math.max(1, Math.round(image.width * scale));
          canvas.height = Math.max(1, Math.round(image.height * scale));
          canvas.getContext('2d').drawImage(image, 0, 0, canvas.width, canvas.height);

          const qualities = [0.86, 0.72, 0.58, 0.44];
          const encode = function (index) {
            canvas.toBlob(function (blob) {
              if (!blob || (blob.size > maxImageBytes && index < qualities.length - 1)) {
                encode(index + 1);
                return;
              }
              const extension = 'webp';
              resolve(new File([blob], file.name.replace(/\.[^.]+$/, '') + '.' + extension, {
                type: 'image/webp',
                lastModified: Date.now()
              }));
            }, 'image/webp', qualities[index]);
          };
          encode(0);
        };
        image.onerror = function () { resolve(file); };
        image.src = reader.result;
      };
      reader.onerror = function () { resolve(file); };
      reader.readAsDataURL(file);
    });
  };

  document.querySelectorAll('input[type="file"][data-compress-image]').forEach(function (input) {
    input.addEventListener('change', async function () {
      const files = Array.from(this.files || []);
      if (!files.some(file => file.type.startsWith('image/') && file.size > maxImageBytes)) return;
      const compressed = [];
      for (const file of files) compressed.push(await compressImage(file));
      const transfer = new DataTransfer();
      compressed.forEach(file => transfer.items.add(file));
      this.files = transfer.files;
      let note = this.parentElement.querySelector('.upload-compression-note');
      if (!note) {
        note = document.createElement('div');
        note.className = 'form-text upload-compression-note text-success';
        this.parentElement.appendChild(note);
      }
      note.textContent = 'Gambar besar sudah dikompresi otomatis sebelum dikirim.';
    });
  });

  // ===== SIDEBAR TOGGLE =====
  const sidebarToggle = document.getElementById('sidebarToggle');
  const sidebar = document.getElementById('adminSidebar');
  const main    = document.getElementById('adminMain');

  if (sidebarToggle && sidebar) {
    sidebarToggle.addEventListener('click', function () {
      if (window.innerWidth < 992) {
        sidebar.classList.toggle('show');
      } else {
        sidebar.classList.toggle('collapsed');
        main.classList.toggle('expanded');
      }
    });

    // Close sidebar on outside click (mobile)
    document.addEventListener('click', function (e) {
      if (window.innerWidth < 992 && sidebar.classList.contains('show')) {
        if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
          sidebar.classList.remove('show');
        }
      }
    });
  }

  // ===== AUTO-DISMISS ALERTS =====
  const alerts = document.querySelectorAll('.alert[data-auto-dismiss]');
  alerts.forEach(function (alert) {
    setTimeout(function () {
      const bsAlert = bootstrap.Alert.getInstance(alert);
      if (bsAlert) bsAlert.close();
      else alert.classList.remove('show');
    }, 4000);
  });

  // Auto-dismiss all success alerts after 4s
  document.querySelectorAll('.alert-success').forEach(function (el) {
    setTimeout(function () {
      el.style.transition = 'opacity 0.5s';
      el.style.opacity = '0';
      setTimeout(() => el.remove(), 500);
    }, 4000);
  });

  // ===== CONFIRM DELETE =====
  document.querySelectorAll('[data-confirm]').forEach(function (el) {
    el.addEventListener('click', function (e) {
      const msg = el.getAttribute('data-confirm') || 'Apakah Anda yakin?';
      if (!confirm(msg)) {
        e.preventDefault();
        return false;
      }
    });
  });

  // ===== CONFIRM FORM SUBMIT =====
  document.querySelectorAll('form[data-confirm]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      const msg = form.getAttribute('data-confirm') || 'Apakah Anda yakin?';
      if (!confirm(msg)) {
        e.preventDefault();
      }
    });
  });

  // ===== IMAGE PREVIEW =====
  document.querySelectorAll('input[type="file"][data-preview]').forEach(function (input) {
    input.addEventListener('change', function () {
      const previewId = this.getAttribute('data-preview');
      const preview   = document.getElementById(previewId);
      if (preview && this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
          preview.src = e.target.result;
          preview.style.display = 'block';
        };
        reader.readAsDataURL(this.files[0]);
      }
    });
  });

  // ===== SELECT ALL CHECKBOXES =====
  const selectAll = document.getElementById('selectAll');
  if (selectAll) {
    selectAll.addEventListener('change', function () {
      document.querySelectorAll('.row-checkbox').forEach(cb => {
        cb.checked = this.checked;
      });
    });
  }

  // ===== SEARCH DEBOUNCE =====
  const searchInput = document.getElementById('searchInput');
  if (searchInput) {
    let debounceTimer;
    searchInput.addEventListener('input', function () {
      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(function () {
        const form = searchInput.closest('form');
        if (form) form.submit();
      }, 500);
    });
  }

  // ===== TPQ DYNAMIC ROWS =====
  const addTpqRow = document.getElementById('addTpqRow');
  if (addTpqRow) {
    addTpqRow.addEventListener('click', function () {
      const container = document.getElementById('tpqRowsContainer');
      const idx       = container.querySelectorAll('.tpq-row').length;
      const row       = document.createElement('div');
      row.className   = 'tpq-row row g-2 mb-2 align-items-center';
      row.innerHTML   = `
        <input type="hidden" name="id[]" value="new">
        <div class="col-md-4">
          <input type="text" name="nama[]" class="form-control" placeholder="Nama Tingkatan" required>
        </div>
        <div class="col-md-2">
          <input type="number" name="urutan[]" class="form-control" placeholder="Urutan" value="${idx+1}">
        </div>
        <div class="col-md-4">
          <input type="text" name="keterangan[]" class="form-control" placeholder="Keterangan (opsional)">
        </div>
        <div class="col-md-1 text-center">
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="aktif[]" value="1" checked>
          </div>
        </div>
        <div class="col-md-1">
          <button type="button" class="btn btn-sm btn-outline-danger remove-tpq-row"><i class="bi bi-trash"></i></button>
        </div>
      `;
      container.appendChild(row);
      row.querySelector('.remove-tpq-row').addEventListener('click', function () {
        row.remove();
      });
    });

    // Remove existing row
    document.querySelectorAll('.remove-tpq-row').forEach(btn => {
      btn.addEventListener('click', function () {
        this.closest('.tpq-row').remove();
      });
    });
  }

  // ===== VALIDATION STATUS CONFIRM =====
  document.querySelectorAll('.btn-validasi').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      const status = this.getAttribute('data-status');
      const nama   = this.getAttribute('data-nama') || 'santri ini';
      const label  = status === 'diterima' ? 'MENERIMA' : 'MENOLAK';
      if (!confirm(`Apakah Anda yakin ingin ${label} pendaftaran ${nama}?`)) {
        e.preventDefault();
      }
    });
  });

});
