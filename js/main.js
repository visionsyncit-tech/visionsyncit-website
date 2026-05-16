/* ============================================================
   VisionSync IT — Main JavaScript
   ============================================================ */

document.addEventListener('DOMContentLoaded', () => {

  /* ── Navbar scroll effect ──────────────────────────────── */
  const navbar = document.querySelector('.navbar');
  if (navbar) {
    window.addEventListener('scroll', () => {
      navbar.classList.toggle('scrolled', window.scrollY > 40);
    }, { passive: true });
  }

  /* ── Active nav link ───────────────────────────────────── */
  const currentPage = window.location.pathname.split('/').pop() || 'index.html';
  document.querySelectorAll('.nav-link, .mobile-nav-link').forEach(link => {
    const href = link.getAttribute('href');
    if (href === currentPage || (currentPage === '' && href === 'index.html')) {
      link.classList.add('active');
    }
  });

  /* ── Mobile menu ───────────────────────────────────────── */
  const hamburger   = document.querySelector('.hamburger');
  const mobileMenu  = document.querySelector('.mobile-menu');
  const overlay     = document.querySelector('.mobile-menu-overlay');
  const closeBtn    = document.querySelector('.mobile-menu-close');

  function openMenu()  { mobileMenu?.classList.add('open'); overlay?.classList.add('open'); document.body.style.overflow = 'hidden'; }
  function closeMenu() { mobileMenu?.classList.remove('open'); overlay?.classList.remove('open'); document.body.style.overflow = ''; }

  hamburger?.addEventListener('click', openMenu);
  closeBtn?.addEventListener('click', closeMenu);
  overlay?.addEventListener('click', closeMenu);

  /* ── Scroll animations ─────────────────────────────────── */
  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry, i) => {
      if (entry.isIntersecting) {
        setTimeout(() => entry.target.classList.add('visible'), i * 80);
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

  document.querySelectorAll('.animate-on-scroll').forEach(el => observer.observe(el));

  /* ── Smooth count-up numbers ───────────────────────────── */
  function animateCount(el, from, to, duration, suffix = '') {
    const start = performance.now();
    const update = (time) => {
      const progress = Math.min((time - start) / duration, 1);
      const ease = 1 - Math.pow(1 - progress, 3);
      el.textContent = Math.round(from + (to - from) * ease) + suffix;
      if (progress < 1) requestAnimationFrame(update);
    };
    requestAnimationFrame(update);
  }

  const statsObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.querySelectorAll('[data-count]').forEach(el => {
          const to = parseInt(el.dataset.count);
          const suffix = el.dataset.suffix || '';
          animateCount(el, 0, to, 1600, suffix);
        });
        statsObserver.unobserve(entry.target);
      }
    });
  }, { threshold: 0.5 });

  document.querySelectorAll('.hero__stats').forEach(el => statsObserver.observe(el));

  /* ── Contact form submission ───────────────────────────── */
  const contactForm = document.getElementById('contactForm');
  if (contactForm) {
    contactForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const btn = contactForm.querySelector('.form-submit');
      const msg = document.getElementById('formMessage');
      const originalText = btn.innerHTML;

      btn.innerHTML = '<span class="spinner"></span> Sending...';
      btn.disabled = true;

      try {
        const res = await fetch('php/contact.php', {
          method: 'POST',
          body: new FormData(contactForm),
        });
        const data = await res.json();

        msg.className = 'form-message ' + (data.success ? 'success' : 'error');
        msg.textContent = data.message;

        if (data.success) contactForm.reset();
      } catch {
        msg.className = 'form-message error';
        msg.textContent = 'Something went wrong. Please try again.';
      }

      btn.innerHTML = originalText;
      btn.disabled = false;
    });
  }

  /* ── Typed headline effect (hero only) ────────────────── */
  const typedEl = document.getElementById('typed-text');
  if (typedEl) {
    const words = ['Software Solutions', 'IT Services', 'Digital Transformation', 'Tech Excellence'];
    let wordIdx = 0, charIdx = 0, deleting = false;
    let cursor;

    cursor = document.createElement('span');
    cursor.className = 'typed-cursor';
    cursor.textContent = '|';
    cursor.style.cssText = 'animation:pulse 1s infinite;color:var(--accent)';
    typedEl.after(cursor);

    function type() {
      const word = words[wordIdx];
      if (!deleting) {
        typedEl.textContent = word.slice(0, ++charIdx);
        if (charIdx === word.length) { deleting = true; setTimeout(type, 2200); return; }
      } else {
        typedEl.textContent = word.slice(0, --charIdx);
        if (charIdx === 0) { deleting = false; wordIdx = (wordIdx + 1) % words.length; }
      }
      setTimeout(type, deleting ? 60 : 110);
    }
    setTimeout(type, 1000);
  }

  /* ── Smooth parallax for hero orbs ────────────────────── */
  const orbs = document.querySelectorAll('.hero__orb');
  if (orbs.length) {
    window.addEventListener('mousemove', (e) => {
      const xFactor = (e.clientX / window.innerWidth - 0.5) * 30;
      const yFactor = (e.clientY / window.innerHeight - 0.5) * 20;
      orbs[0]?.style.setProperty('transform', `translate(${xFactor}px, ${yFactor}px)`);
      orbs[1]?.style.setProperty('transform', `translate(${-xFactor * 0.6}px, ${-yFactor * 0.6}px)`);
    }, { passive: true });
  }

  /* ── Tilt effect on service cards ─────────────────────── */
  document.querySelectorAll('.service-card, .team-card').forEach(card => {
    card.addEventListener('mousemove', (e) => {
      const rect = card.getBoundingClientRect();
      const x = ((e.clientX - rect.left) / rect.width - 0.5) * 8;
      const y = ((e.clientY - rect.top) / rect.height - 0.5) * -8;
      card.style.transform = `perspective(800px) rotateX(${y}deg) rotateY(${x}deg) translateZ(4px)`;
    });
    card.addEventListener('mouseleave', () => {
      card.style.transform = '';
    });
  });

});