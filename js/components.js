/* ============================================================
   VisionSync IT — Shared Components (Nav + Footer)
   ============================================================ */

const NAV_HTML = `
<div class="grid-bg"></div>

<nav class="navbar">
  <div class="navbar__inner">
    <a href="index.html" class="logo">
      <div class="logo__icon">⚡</div>
      VisionSync IT
    </a>
    <div class="nav-links">
      <a href="index.html"    class="nav-link">Home</a>
      <a href="services.html" class="nav-link">Services</a>
      <a href="about.html"    class="nav-link">About</a>
      <a href="contact.html"  class="nav-link">Contact</a>
    </div>
    <a href="contact.html" class="btn btn-primary btn-sm nav-cta">Get Started →</a>
    <button class="hamburger" aria-label="Open menu">
      <span></span><span></span><span></span>
    </button>
  </div>
</nav>

<!-- Mobile Menu -->
<div class="mobile-menu-overlay"></div>
<div class="mobile-menu">
  <button class="mobile-menu-close" aria-label="Close">✕</button>
  <nav class="mobile-nav-links">
    <a href="index.html"    class="mobile-nav-link">Home</a>
    <a href="services.html" class="mobile-nav-link">Services</a>
    <a href="about.html"    class="mobile-nav-link">About</a>
    <a href="contact.html"  class="mobile-nav-link">Contact</a>
  </nav>
  <a href="contact.html" class="btn btn-primary" style="margin-top:32px;width:100%;justify-content:center">Get Started →</a>
</div>
`;

const FOOTER_HTML = `
<footer class="footer relative">
  <div class="container">
    <div class="footer__grid">
      <div>
        <a href="index.html" class="logo">
          <div class="logo__icon">⚡</div>
          VisionSync IT
        </a>
        <p class="footer__brand-desc">
          Empowering businesses with cutting-edge software solutions and IT services. 
          Your vision, synchronized with technology.
        </p>
        <div class="social-links" style="margin-top:24px">
          <a href="#" class="social-link" aria-label="LinkedIn">in</a>
          <a href="#" class="social-link" aria-label="Twitter">𝕏</a>
          <a href="#" class="social-link" aria-label="GitHub">⌥</a>
          <a href="#" class="social-link" aria-label="Instagram">◎</a>
        </div>
      </div>
      <div>
        <p class="footer__heading">Services</p>
        <ul class="footer__links">
          <li><a href="services.html" class="footer__link">Web Development</a></li>
          <li><a href="services.html" class="footer__link">Mobile Apps</a></li>
          <li><a href="services.html" class="footer__link">Cloud Solutions</a></li>
          <li><a href="services.html" class="footer__link">IT Consulting</a></li>
          <li><a href="services.html" class="footer__link">Cybersecurity</a></li>
        </ul>
      </div>
      <div>
        <p class="footer__heading">Company</p>
        <ul class="footer__links">
          <li><a href="about.html" class="footer__link">About Us</a></li>
          <li><a href="about.html#team" class="footer__link">Our Team</a></li>
          <li><a href="about.html#values" class="footer__link">Our Values</a></li>
          <li><a href="contact.html" class="footer__link">Contact</a></li>
        </ul>
      </div>
      <div>
        <p class="footer__heading">Contact</p>
        <ul class="footer__links">
          <li><a href="mailto:info@visionsyncit.com" class="footer__link">info@visionsyncit.com</a></li>
          <li><a href="mailto:support@visionsyncit.com" class="footer__link">support@visionsyncit.com</a></li>
          <li><a href="tel:+962790000000" class="footer__link">+962 79 000 0000</a></li>
          <li class="footer__link" style="cursor:default">Amman, Jordan 🇯🇴</li>
        </ul>
      </div>
    </div>
    <div class="footer__bottom">
      <p class="footer__copy">© 2025 VisionSync IT. All rights reserved.</p>
      <p class="footer__copy">Built with ♥ in Jordan</p>
    </div>
  </div>
</footer>
`;

// Inject into page
document.getElementById('nav-placeholder')?.insertAdjacentHTML('afterend', NAV_HTML);
document.getElementById('footer-placeholder')?.insertAdjacentHTML('beforeend', FOOTER_HTML);