import { useState, useEffect } from "react";
import { Link, useLocation } from "wouter";
import "../welthflow.css";

const BRAND = "Welthflow";
const DOMAIN = "welthflow.com";
const REGISTER_URL = "/welthflow/portal/register.php";
const LOGIN_URL = "/welthflow/portal/login.php";

export function Logo({ light = false }: { light?: boolean }) {
  return (
    <svg width="220" height="44" viewBox="0 0 220 44" fill="none" xmlns="http://www.w3.org/2000/svg">
      <defs>
        <linearGradient id="wf-grad2" x1="0" y1="0" x2="44" y2="44" gradientUnits="userSpaceOnUse">
          <stop offset="0%" stopColor="#3eda99" />
          <stop offset="100%" stopColor="#1dbfc8" />
        </linearGradient>
      </defs>
      <rect x="0" y="4" width="40" height="36" rx="8" fill="url(#wf-grad2)" opacity="0.15" />
      <path d="M8 32 L14 16 L20 27 L26 18 L32 32" stroke="url(#wf-grad2)" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round" fill="none" />
      <circle cx="26" cy="11" r="5" fill="url(#wf-grad2)" opacity="0.9" />
      <path d="M26 6 L27.8 9.5 L32 10.2 L29 13.1 L29.6 17.4 L26 15.5 L22.4 17.4 L23 13.1 L20 10.2 L24.2 9.5 Z" fill="url(#wf-grad2)" />
      <text x="50" y="31" fontFamily="'Lora', serif" fontWeight="700" fontSize="22" fill="#ffffff">Welth</text>
      <text x="103" y="31" fontFamily="'Lora', serif" fontWeight="400" fontSize="22" fill="#3eda99">flow</text>
      <text x="50" y="42" fontFamily="'Raleway', sans-serif" fontWeight="400" fontSize="9.5" letterSpacing="3" fill="rgba(255,255,255,0.55)">INVESTMENTS</text>
    </svg>
  );
}

export function NavBar() {
  const [scrolled, setScrolled] = useState(false);
  const [menuOpen, setMenuOpen] = useState(false);
  const [location] = useLocation();
  const isHome = location === "/";

  useEffect(() => {
    const onScroll = () => setScrolled(window.scrollY > 60);
    window.addEventListener("scroll", onScroll);
    return () => window.removeEventListener("scroll", onScroll);
  }, []);

  const navBg = (!isHome || scrolled) ? "#01123c" : "transparent";

  return (
    <nav className="wf-navbar" style={{ background: navBg }}>
      <div className="container">
        <div className="wf-nav-inner">
          <Link href="/" className="wf-logo-link"><Logo light /></Link>
          <button className="wf-hamburger" onClick={() => setMenuOpen(!menuOpen)}>
            <i className={`fas ${menuOpen ? "fa-times" : "fa-bars"}`}></i>
          </button>
          <ul className={`wf-nav-links ${menuOpen ? "open" : ""}`}>
            <li><Link href="/">Home</Link></li>
            <li><Link href="/faq">FAQ</Link></li>
            <li><div id="google_translate_element" /></li>
            <li className="wf-dropdown">
              <a href="#">Our Company <i className="fas fa-chevron-down" style={{ fontSize: 10 }}></i></a>
              <ul className="wf-submenu">
                <li><Link href="/about">About Us</Link></li>
                <li><Link href="/terms">Legal and Terms</Link></li>
              </ul>
            </li>
            <li className="wf-dropdown">
              <a href="#">Investment Packages <i className="fas fa-chevron-down" style={{ fontSize: 10 }}></i></a>
              <ul className="wf-submenu">
                <li><Link href="/investment">Cryptocurrency</Link></li>
                <li><Link href="/loan">Loan</Link></li>
                <li><Link href="/forex">FOREX (PAMM/MAM)</Link></li>
              </ul>
            </li>
            <li><Link href="/affiliates">Affiliate</Link></li>
            <li className="wf-dropdown">
              <a href="#">My Account <i className="fas fa-chevron-down" style={{ fontSize: 10 }}></i></a>
              <ul className="wf-submenu">
                <li><a href={REGISTER_URL}>Open Account</a></li>
                <li><a href={LOGIN_URL}>Account Login</a></li>
              </ul>
            </li>
            <li>
              <a href={REGISTER_URL} className="wf-btn-nav">
                <i className="fas fa-user-plus"></i> Get Started
              </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  );
}

export function PageHero({ title, subtitle, bg }: { title: string; subtitle?: string; bg?: string }) {
  return (
    <div className="wf-page-hero" style={{ backgroundImage: `url(${bg || "https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=1600&q=70"})` }}>
      <div className="wf-page-hero-overlay"></div>
      <div className="container wf-page-hero-content">
        <div className="wf-breadcrumb">
          <Link href="/">Home</Link>
          <i className="fas fa-chevron-right"></i>
          <span>{title}</span>
        </div>
        <h1 className="wf-page-title">{title}</h1>
        {subtitle && <p className="wf-page-subtitle">{subtitle}</p>}
      </div>
    </div>
  );
}

export function Footer() {
  const EMAIL = "admin@welthflow.com";
  const REGISTER_URL = "/welthflow/register";
  const LOGIN_URL = "/welthflow/login";

  const europeBuyCrypto = [
    { label: "Coin Mama", url: "https://coinmama.com/" },
    { label: "PayBis", url: "https://paybis.com/" },
    { label: "Coin Base", url: "https://coinbase.com/" },
    { label: "Luno", url: "https://luno.com/" },
    { label: "Kraken", url: "https://kraken.com/" },
    { label: "Binance", url: "https://binance.com/" },
    { label: "Bit2me", url: "https://bit2me.com/" },
  ];
  const americaBuyCrypto = [
    { label: "Coin Mama", url: "https://coinmama.com/" },
    { label: "PayBis", url: "https://paybis.com/" },
    { label: "Coin Base", url: "https://coinbase.com/" },
    { label: "Local Bitcoins", url: "https://localbitcoins.com/" },
    { label: "Cex.io", url: "https://cex.io/" },
    { label: "Gemini", url: "https://gemini.com/" },
  ];
  const othersBuyCrypto = [
    { label: "Indodax", url: "https://indodax.com/" },
    { label: "Coinhako", url: "https://coinhako.com/" },
    { label: "Wazirx", url: "https://wazirx.com/" },
    { label: "Zebpay", url: "https://zebpay.com/" },
    { label: "Nobitex", url: "https://nobitex.ir/" },
    { label: "Wallex", url: "https://wallex.ir/" },
  ];

  return (
    <footer className="wf-footer">
      <div className="wf-footer-body">
        <div className="container">
          <div className="row mb-4">
            {([["Quick Links To Buy Bitcoin in EUROPE", europeBuyCrypto], ["Quick Links To Buy Bitcoin in AMERICA", americaBuyCrypto], ["Quick Links To Buy Bitcoin in OTHERS", othersBuyCrypto]] as [string, { label: string; url: string }[]][]).map(([title, links], i) => (
              <div key={i} className="col-md-4 mb-4">
                <h4 className="wf-footer-heading">{title}</h4>
                <ul className="wf-footer-list">
                  {links.map((l, j) => (
                    <li key={j}><a href={l.url} target="_blank" rel="noreferrer">{l.label}</a></li>
                  ))}
                </ul>
              </div>
            ))}
          </div>
          <hr style={{ borderColor: "rgba(255,255,255,0.15)" }} />
          <div className="row mt-4">
            <div className="col-lg-4 mb-4">
              <h4 className="wf-footer-heading">Our Contacts</h4>
              <ul className="wf-footer-contact">
                <li><i className="fas fa-map-marker-alt"></i> 8 Fitzroy Pl, Finnieston, Glasgow G3 7RH, United Kingdom.</li>
                <li><i className="fas fa-envelope"></i> <a href={`mailto:${EMAIL}`}>{EMAIL}</a></li>
                <li><i className="fab fa-whatsapp"></i> <a href="#">+447418611709</a></li>
              </ul>
            </div>
            <div className="col-lg-3 mb-4">
              <h4 className="wf-footer-heading">Quick Links</h4>
              <ul className="wf-footer-list">
                <li><Link href="/">Home</Link></li>
                <li><Link href="/about">About Us</Link></li>
                <li><Link href="/affiliates">Affiliate</Link></li>
                <li><Link href="/terms">Terms and Conditions</Link></li>
                <li><a href={REGISTER_URL}>Create Account</a></li>
                <li><a href={LOGIN_URL}>Account Login</a></li>
              </ul>
            </div>
            <div className="col-lg-5 mb-4">
              <div className="wf-footer-logo-wrap mb-3"><Logo /></div>
              <p style={{ color: "rgba(255,255,255,0.7)", fontSize: 14 }}>
                {BRAND} is dedicated to helping investors around the world reach their desired investment goals and broaden their financial horizons.
              </p>
            </div>
          </div>
        </div>
      </div>
      <div className="wf-footer-copy">
        <div className="container">
          <div className="row align-items-center">
            <div className="col-lg-6">
              <div className="wf-social-icons">
                {([["fab fa-facebook-f", "#"], ["fab fa-twitter", "#"], ["fab fa-instagram", "#"], ["fab fa-linkedin-in", "#"], ["fab fa-youtube", "#"]] as [string, string][]).map(([icon, href], i) => (
                  <a key={i} href={href} className="wf-social-icon"><i className={icon}></i></a>
                ))}
              </div>
            </div>
            <div className="col-lg-6 text-lg-end">
              <p className="mb-0" style={{ color: "rgba(255,255,255,0.6)", fontSize: 13 }}>© 2024 {BRAND} ({DOMAIN}). All Rights Reserved.</p>
            </div>
          </div>
        </div>
      </div>
    </footer>
  );
}
