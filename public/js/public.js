/* ============================================================
   PUBLIC WEBSITE JS - Sistem Informasi Pesantren
   ============================================================ */

document.addEventListener('DOMContentLoaded', function () {

  // ===== PSB FORM: Tampilkan pilihan program jika dicentang =====
  const daftarDiniyyahCheck = document.getElementById('daftarDiniyyahCheck');
  const diniyyahSection     = document.getElementById('diniyyahClassSection');
  if (daftarDiniyyahCheck && diniyyahSection) {
    diniyyahSection.style.display = daftarDiniyyahCheck.checked ? 'block' : 'none';
    daftarDiniyyahCheck.addEventListener('change', function () {
      diniyyahSection.style.display = this.checked ? 'block' : 'none';
      if (!this.checked) {
        diniyyahSection.querySelectorAll('input[type="radio"]').forEach(radio => radio.checked = false);
      }
    });
  }

  const daftarTpqCheck = document.getElementById('daftarTpqCheck');
  const tpqSection     = document.getElementById('tpqLevelSection');
  if (daftarTpqCheck && tpqSection) {
    tpqSection.style.display = daftarTpqCheck.checked ? 'block' : 'none';
    daftarTpqCheck.addEventListener('change', function () {
      tpqSection.style.display = this.checked ? 'block' : 'none';
      if (!this.checked) {
        tpqSection.querySelectorAll('input[type="radio"]').forEach(r => r.checked = false);
      }
    });
  }

  // ===== PSB FORM VALIDATION =====
  const psbForm = document.getElementById('psbForm');
  if (psbForm) {
    psbForm.addEventListener('submit', function (e) {
      const checkedJenjang = psbForm.querySelectorAll(
        '#daftarDiniyyahCheck:checked, #daftarTpqCheck:checked'
      );
      if (checkedJenjang.length === 0) {
        e.preventDefault();
        const alert = document.getElementById('jenjangAlert');
        if (alert) {
          alert.classList.remove('d-none');
          alert.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        return;
      }

      if (daftarDiniyyahCheck && daftarDiniyyahCheck.checked) {
        const diniyyahClass = psbForm.querySelector('input[name="kelas_diniyyah_id"]:checked');
        if (!diniyyahClass) {
          e.preventDefault();
          const alert = document.getElementById('jenjangAlert');
          if (alert) {
            alert.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i>Pilih jenjang dan kelas Diniyyah yang diinginkan.';
            alert.classList.remove('d-none');
            alert.scrollIntoView({ behavior: 'smooth', block: 'center' });
          }
          return;
        }
      }

      // Jika TPQ dipilih tapi tidak memilih tingkatan
      if (daftarTpqCheck && daftarTpqCheck.checked) {
        const tpqLevel = psbForm.querySelector('input[name="tingkat_tpq"]:checked');
        if (!tpqLevel) {
          e.preventDefault();
          const alert = document.getElementById('jenjangAlert');
          if (alert) {
            alert.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i>Pilih tingkatan TPQ yang diinginkan.';
            alert.classList.remove('d-none');
            alert.scrollIntoView({ behavior: 'smooth', block: 'center' });
          }
          return;
        }
      }

      // Disable tombol submit untuk mencegah double submit
      const submitBtn = psbForm.querySelector('button[type="submit"]');
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
      }
    });

    // Hide jenjang alert saat user memilih sesuatu
    psbForm.querySelectorAll('input[type="checkbox"], input[type="radio"]').forEach(function (inp) {
      inp.addEventListener('change', function () {
        const alert = document.getElementById('jenjangAlert');
        if (alert) alert.classList.add('d-none');
      });
    });
  }

  // ===== SMOOTH SCROLL =====
  document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
    anchor.addEventListener('click', function (e) {
      const href = this.getAttribute('href');
      if (!href || href === '#') return;
      const target = document.querySelector(href);
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });

  // ===== COUNTER ANIMATION =====
  const counters = document.querySelectorAll('[data-counter]');
  const observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting && !entry.target.classList.contains('counted')) {
        entry.target.classList.add('counted');
        const target = parseInt(entry.target.getAttribute('data-counter'));
        let count = 0;
        const step = Math.ceil(target / 60);
        const timer = setInterval(function () {
          count += step;
          if (count >= target) {
            count = target;
            clearInterval(timer);
          }
          entry.target.textContent = count.toLocaleString('id-ID') + '+';
        }, 16);
      }
    });
  }, { threshold: 0.5 });
  counters.forEach(c => observer.observe(c));

  // ===== NAVBAR ACTIVE STATE =====
  const currentPath = window.location.pathname;
  document.querySelectorAll('.navbar-nav .nav-link').forEach(function (link) {
    if (link.getAttribute('href') === currentPath) {
      link.classList.add('active');
    }
  });

  // ===== GALERI LIGHTBOX (simple) =====
  const galeriItems = document.querySelectorAll('.galeri-item[data-src]');
  if (galeriItems.length) {
    // Create modal
    const modal = document.createElement('div');
    modal.style.cssText = 'display:none;position:fixed;inset:0;background:rgba(0,0,0,0.92);z-index:9999;align-items:center;justify-content:center;cursor:pointer;';
    modal.innerHTML = '<img id="lightboxImg" style="max-width:90vw;max-height:90vh;border-radius:8px;box-shadow:0 8px 32px rgba(0,0,0,0.5);" src=""><div style="position:absolute;top:20px;right:20px;color:#fff;font-size:2rem;cursor:pointer;" id="lightboxClose">&times;</div>';
    document.body.appendChild(modal);

    galeriItems.forEach(function (item) {
      item.addEventListener('click', function () {
        const src = this.getAttribute('data-src');
        modal.querySelector('#lightboxImg').src = src;
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
      });
    });
    modal.addEventListener('click', function (e) {
      if (e.target === modal || e.target.id === 'lightboxClose') {
        modal.style.display = 'none';
        document.body.style.overflow = '';
      }
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') { modal.style.display = 'none'; document.body.style.overflow = ''; }
    });
  }

  // ===== SCROLL TO TOP BUTTON =====
  const scrollTop = document.createElement('button');
  scrollTop.className = 'btn btn-primary';
  scrollTop.innerHTML = '<i class="bi bi-chevron-up"></i>';
  scrollTop.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:999;width:42px;height:42px;border-radius:50%;display:none;padding:0;box-shadow:0 4px 12px rgba(0,0,0,0.2);';
  scrollTop.id = 'scrollTopBtn';
  document.body.appendChild(scrollTop);

  window.addEventListener('scroll', function () {
    scrollTop.style.display = window.scrollY > 400 ? 'flex' : 'none';
    scrollTop.style.alignItems = 'center';
    scrollTop.style.justifyContent = 'center';
  });
  scrollTop.addEventListener('click', function () {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });

});
