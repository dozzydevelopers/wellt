import { useState, useEffect } from "react";
import { Link } from "wouter";
import { NavBar, Footer } from "./Layout";
import WalletConnect from "./WalletConnect";
import Leadership from "./Leadership";

const BRAND = "Welthflow";
const DOMAIN = "welthflow.com";
const REGISTER_URL = "/welthflow/portal/register.php";
const LOGIN_URL = "/welthflow/portal/login.php";
const NAVY = "#062f6d";
const DARK_NAVY = "#01123c";
const MID_NAVY = "#1a396b";

const HERO_SLIDES = [
  {
    bg: "https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=1920&q=90",
    title: `${BRAND} Investments`,
    sub: `We pride ourselves in our guarantees, success and track record in the asset management and investments market. Take control with our all-in-one multiple investment packages such as Real estate, Cryptocurrency, Agro and more.`,
    cta: "Create an Account", ctaHref: REGISTER_URL, ctaIcon: "fa-user-plus",
  },
  {
    bg: "https://images.unsplash.com/photo-1642790106117-e829e14a795f?w=1920&q=90",
    title: "Invest and Earn With Us",
    sub: `Invest with confidence on World's leading asset management and investment platform. Your single point of access to professional asset investment and management solutions built for investors seeking stable returns and high liquidity.`,
    cta: "Login", ctaHref: LOGIN_URL, ctaIcon: "fa-user",
  },
  {
    bg: "https://images.unsplash.com/photo-1560520653-9e0e4c89eb11?w=1920&q=90",
    title: "Trading Expertise You Can Trust",
    sub: `Our goal is to enhance lives by providing a safe avenue for investing in the world's most profitable financial markets — improving our investors' financial situation and ultimately delivering the financial freedom they deserve.`,
    cta: "Get Started", ctaHref: REGISTER_URL, ctaIcon: "fa-play",
  },
  {
    bg: "https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=1920&q=90",
    title: "Over $563Bn Assets Managed",
    sub: `Trusted by 80 million investors across 80 countries. From Bitcoin to Real Estate to PAMM Forex — our expert portfolio managers work around the clock to grow your wealth with precision and security.`,
    cta: "View Investment Plans", ctaHref: "#plans-table", ctaIcon: "fa-chart-line",
  },
];

function HeroSlider() {
  const [current, setCurrent] = useState(0);
  useEffect(() => {
    const t = setInterval(() => setCurrent(c => (c + 1) % HERO_SLIDES.length), 6000);
    return () => clearInterval(t);
  }, []);
  const s = HERO_SLIDES[current];
  return (
    <div className="wf-hero">
      {HERO_SLIDES.map((slide, i) => (
        <div key={i} className={`wf-slide ${i === current ? "active" : ""}`}
          style={{ backgroundImage: `url(${slide.bg})` }}>
          <div className="wf-slide-overlay" />
          <div className="container wf-slide-content">
            <div className="wf-slide-badge">
              <span className="wf-slide-badge-dot" />
              SECURE GLOBAL INVESTMENT PLATFORM
            </div>
            <h1 className="wf-slide-title">{slide.title}</h1>
            <p className="wf-slide-sub">{slide.sub}</p>
            <div className="wf-slide-btns">
              <a href={slide.ctaHref} className="wf-btn-primary">
                <i className={`fas ${slide.ctaIcon}`}></i> {slide.cta}
              </a>
              <Link href="/about" className="wf-btn-glass">Learn More</Link>
            </div>
          </div>
        </div>
      ))}
      <div className="wf-slide-dots">
        {HERO_SLIDES.map((_, i) => (
          <button key={i} className={`wf-dot ${i === current ? "active" : ""}`} onClick={() => setCurrent(i)} />
        ))}
      </div>
    </div>
  );
}

/* ─── INVESTMENT PLANS (after hero) ──────────────────────── */
const QUICK_PLANS = [
  {
    name: "GOLD PLAN",
    roi: "5%",
    period: "24 Hours",
    range: "$100 – $999",
    duration: "24 Hours",
    referral: "3%",
    color: "#c9a227",
    glow: "rgba(201,162,39,0.25)",
    icon: "fa-star",
  },
  {
    name: "GROWTH PLAN",
    roi: "10%",
    period: "2 Days",
    range: "$1,000 – $9,999",
    duration: "2 Days",
    referral: "3%",
    color: "#3eda99",
    glow: "rgba(62,218,153,0.2)",
    icon: "fa-chart-line",
    popular: true,
  },
  {
    name: "PREMIUM PLAN",
    roi: "15%",
    period: "4 Days",
    range: "$10,000 – $39,999",
    duration: "4 Days",
    referral: "5%",
    color: "#1dbfc8",
    glow: "rgba(29,191,200,0.2)",
    icon: "fa-gem",
  },
  {
    name: "ULTIMATE PLAN",
    roi: "35%",
    period: "7 Days",
    range: "$40,000 – Unlimited",
    duration: "7 Days",
    referral: "5%",
    color: "#ff6b6b",
    glow: "rgba(255,107,107,0.2)",
    icon: "fa-crown",
  },
];

function QuickPlansSection() {
  return (
    <section className="wf-quick-plans">
      <div className="container">
        <div className="wf-quick-plans-header">
          <div className="wf-section-badge" style={{ background: "rgba(201,162,39,0.15)", color: "#c9a227", borderColor: "rgba(201,162,39,0.3)" }}>
            INVESTMENT PACKAGES
          </div>
          <h2 className="wf-quick-plans-title">
            Choose Your <span style={{ color: "#c9a227" }}>Investment Plan</span>
          </h2>
          <p className="wf-quick-plans-sub">
            High-yield plans managed by our expert portfolio team. Daily, bi-daily, and weekly returns — all with 24/7 support.
          </p>
        </div>
        <div className="wf-qp-grid">
          {QUICK_PLANS.map((plan, i) => (
            <div
              key={i}
              className={`wf-qp-card ${plan.popular ? "wf-qp-popular" : ""}`}
              style={{ "--plan-color": plan.color, "--plan-glow": plan.glow } as any}
            >
              {plan.popular && <div className="wf-qp-badge">Most Popular</div>}
              <div className="wf-qp-icon-wrap" style={{ background: plan.glow }}>
                <i className={`fas ${plan.icon}`} style={{ color: plan.color }}></i>
              </div>
              <div className="wf-qp-name">{plan.name}</div>
              <div className="wf-qp-roi" style={{ color: plan.color }}>{plan.roi}</div>
              <div className="wf-qp-period">per {plan.period}</div>
              <div className="wf-qp-divider" style={{ background: plan.color }} />
              <ul className="wf-qp-features">
                <li><span>Investment</span><b>{plan.range}</b></li>
                <li><span>Duration</span><b>{plan.duration}</b></li>
                <li><span>Referral Bonus</span><b style={{ color: "#3eda99" }}>{plan.referral}</b></li>
                <li><span>Support</span><b style={{ color: "#3eda99" }}>24/7 ✓</b></li>
              </ul>
              <a href={REGISTER_URL} className="wf-qp-btn" style={{ background: plan.color }}>
                Invest Now
              </a>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}

function CryptoTicker() {
  const coins = [
    { symbol: "BTC", price: 68420.50, change: 2.34 }, { symbol: "ETH", price: 3812.20, change: 1.87 },
    { symbol: "XRP", price: 0.6124, change: -0.52 }, { symbol: "LTC", price: 84.35, change: 0.91 },
    { symbol: "EOS", price: 0.9142, change: -1.12 }, { symbol: "ADA", price: 0.4521, change: 3.22 },
    { symbol: "SOL", price: 178.45, change: 4.51 }, { symbol: "BNB", price: 607.88, change: 1.03 },
    { symbol: "DOGE", price: 0.1421, change: 6.12 }, { symbol: "MATIC", price: 0.8812, change: -2.1 },
  ];
  return (
    <div className="wf-ticker">
      <div className="wf-ticker-track">
        {[...coins, ...coins].map((c, i) => (
          <span key={i} className="wf-ticker-item">
            <strong>{c.symbol}</strong> ${c.price.toLocaleString()}
            <span style={{ color: c.change >= 0 ? "#3eda99" : "#ff6b6b", marginLeft: 4 }}>
              {c.change >= 0 ? "▲" : "▼"} {Math.abs(c.change)}%
            </span>
          </span>
        ))}
      </div>
    </div>
  );
}

function GrowSection() {
  return (
    <section className="wf-section-dark" style={{ background: MID_NAVY }}>
      <div className="container text-center">
        <div className="wf-medal-icon"><i className="fas fa-medal"></i></div>
        <p className="wf-section-eyebrow">{DOMAIN}</p>
        <h2 className="wf-h2 c-w">{BRAND} helps you grow your money</h2>
        <p className="wf-lead c-w mx-auto" style={{ maxWidth: 920 }}>
          {BRAND}, a pioneer of commission-free investing, gives you access to investing and more ways to make your money work harder for you. Whether you're new to investing or a seasoned pro, it's time to partner with a company that believes you could and should be getting more from your money.
        </p>
        <div className="wf-trust-row">
          {[["fas fa-shield-halved", "FCA Regulated"], ["fas fa-lock", "256-bit Encrypted"], ["fas fa-globe", "80+ Countries"], ["fas fa-clock", "24/7 Support"]].map(([icon, label], i) => (
            <div key={i} className="wf-trust-chip">
              <i className={icon}></i> {label}
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}

function OpportunitySection() {
  const mediaLogos = [
    { name: "Bloomberg", url: "https://upload.wikimedia.org/wikipedia/commons/thumb/7/75/Bloomberg_Logo.svg/320px-Bloomberg_Logo.svg.png" },
    { name: "Reuters", url: "https://upload.wikimedia.org/wikipedia/commons/thumb/f/f0/Reuters_2024.svg/320px-Reuters_2024.svg.png" },
    { name: "CNBC", url: "https://upload.wikimedia.org/wikipedia/commons/thumb/1/1e/CNBC_logo.svg/320px-CNBC_logo.svg.png" },
    { name: "Forbes", url: "https://upload.wikimedia.org/wikipedia/commons/thumb/3/3a/Forbes_logo.svg/320px-Forbes_logo.svg.png" },
    { name: "FT", url: "https://upload.wikimedia.org/wikipedia/commons/thumb/1/11/Financial_Times_corporate_logo_(2013).svg/320px-Financial_Times_corporate_logo_(2013).svg.png" },
  ];
  return (
    <section className="wf-section-light" style={{ paddingTop: 70, paddingBottom: 50 }}>
      <div className="container">
        <div className="row align-items-center g-5">
          <div className="col-lg-6">
            <div className="wf-section-badge">INVESTMENT OPPORTUNITY</div>
            <h4 className="wf-h4 mt-2">Lucrative Investment opportunity at your fingertips.</h4>
            <p className="wf-body">
              {BRAND} is dedicated to helping investors around the world reach their desired investment goals and broaden their financial horizons.<br /><br />
              {BRAND} was founded on a simple but revolutionary idea that an investment company should be run for the sole benefit of its investors.
            </p>
            <div className="row g-3 mt-2">
              {[["$563Bn+", "Assets Managed"], ["80M+", "Active Investors"], ["10+", "Years Experience"], ["80+", "Countries"]].map(([val, label], i) => (
                <div key={i} className="col-6">
                  <div className="wf-mini-stat">
                    <div className="wf-mini-val">{val}</div>
                    <div className="wf-mini-label">{label}</div>
                  </div>
                </div>
              ))}
            </div>
          </div>
          <div className="col-lg-6">
            <div className="wf-local-video-wrap">
              <video
                className="wf-local-video"
                src="/welthflow/intro.mp4"
                controls
                autoPlay
                muted
                loop
                playsInline
                poster="https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=800&q=70"
              />
            </div>
          </div>
        </div>
        <div className="wf-seen-on mt-5">
          <span className="wf-seen-label">As seen on</span>
          <div className="wf-logos-row">
            {mediaLogos.map((l, i) => (
              <div key={i} className="wf-media-logo">
                <img src={l.url} alt={l.name} style={{ height: 26, objectFit: "contain", filter: "grayscale(1) opacity(0.5)" }} onError={e => (e.currentTarget.style.display = "none")} />
              </div>
            ))}
          </div>
        </div>
      </div>
    </section>
  );
}

function InvestmentProducts() {
  const products = [
    { icon: "fa-bitcoin-sign", label: "Cryptocurrency", href: "/investment" },
    { icon: "fa-building", label: "Real Estate", href: "/investment" },
    { icon: "fa-seedling", label: "Agro", href: "/investment" },
    { icon: "fa-money-bill-trend-up", label: "Forex", href: "/forex" },
    { icon: "fa-piggy-bank", label: "Pension Funds", href: "/loan" },
    { icon: "fa-shield-halved", label: "Fixed Deposit", href: "/loan" },
  ];
  return (
    <div className="wf-section-dark" style={{ background: DARK_NAVY, paddingTop: 60, paddingBottom: 50 }}>
      <div className="container">
        <div className="row align-items-center g-4">
          <div className="col-lg-4">
            <h2 className="wf-h2 c-w">Investment products</h2>
            <p className="c-w" style={{ opacity: 0.8 }}>Choose from our array of 6 different investment packages and get started investing.</p>
            <a href={REGISTER_URL} className="wf-btn-primary mt-3 d-inline-block">Start Investing</a>
          </div>
          <div className="col-lg-8">
            <div className="row g-3">
              {products.map((p, i) => (
                <div key={i} className="col-4 col-md-2 text-center">
                  <Link href={p.href} className="wf-product-link">
                    <div className="wf-product-icon"><i className={`fas ${p.icon}`}></i></div>
                    <p className="c-w fw-bold mt-2" style={{ fontSize: 12 }}>{p.label}</p>
                  </Link>
                </div>
              ))}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

function WalletSection({ onOpenWallet }: { onOpenWallet: () => void }) {
  return (
    <section style={{ background: "#010c1f", padding: "70px 0" }}>
      <div className="container">
        <div className="row align-items-center g-5">
          <div className="col-lg-6">
            <div className="wf-section-badge mb-3" style={{ background: "rgba(62,218,153,0.1)", color: "#3eda99", borderColor: "rgba(62,218,153,0.3)" }}>WALLET INTEGRATION</div>
            <h2 className="wf-h2 c-w">Connect Your Crypto Wallet</h2>
            <p className="c-w mb-4" style={{ opacity: 0.75 }}>
              Seamlessly connect your existing crypto wallet to fund your investment account instantly. We support all major wallet providers including MetaMask, Trust Wallet, Ledger, Coinbase Wallet, and more.
            </p>
            <div className="wf-wallet-features mb-4">
              {[["fa-bolt", "Instant deposits from your wallet"], ["fa-shield-halved", "Military-grade encrypted connection"], ["fa-rotate", "Real-time portfolio sync"], ["fa-key", "Non-custodial — you keep control"]].map(([icon, text], i) => (
                <div key={i} className="wf-wallet-feat">
                  <i className={`fas ${icon}`}></i> {text}
                </div>
              ))}
            </div>
            <button onClick={onOpenWallet} className="wf-btn-wallet">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" style={{ marginRight: 8 }}>
                <rect x="2" y="7" width="20" height="14" rx="2" stroke="currentColor" strokeWidth="2" />
                <path d="M16 12h2" stroke="currentColor" strokeWidth="2" strokeLinecap="round" />
                <path d="M2 10V7a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v3" stroke="currentColor" strokeWidth="2" />
              </svg>
              Connect Wallet
            </button>
          </div>
          <div className="col-lg-6">
            <div className="wf-wallet-grid">
              {[
                { name: "MetaMask", icon: "🦊" }, { name: "Trust Wallet", icon: "💎" },
                { name: "Coinbase", icon: "🔵" }, { name: "WalletConnect", icon: "🔗" },
                { name: "Ledger", icon: "🔒" }, { name: "Phantom", icon: "👻" },
                { name: "Rainbow", icon: "🌈" }, { name: "Exodus", icon: "⚡" },
              ].map((w, i) => (
                <button key={i} className="wf-wallet-chip" onClick={onOpenWallet}>
                  <span className="wf-wallet-chip-icon">{w.icon}</span>
                  <span className="wf-wallet-chip-name">{w.name}</span>
                </button>
              ))}
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}

function AccountCards() {
  const cards = [
    { img: "https://images.unsplash.com/photo-1559526324-4b87b5e36e44?w=700&q=80", cat: "Retirement & Pension Funds", title: "Retirement & Pension Funds INVESTMENT Package", desc: "After retirement, there needs to be a regular source of income which is possible only when you make the right investments.", href: "/loan" },
    { img: "https://images.unsplash.com/photo-1518546305927-5a555bb7020d?w=700&q=80", cat: "Cryptocurrency", title: "CRYPTOCURRENCY INVESTMENT Package", desc: "We offer our investors access to high-growth investment opportunities in the Bitcoin markets. Our professional team takes charge of trading.", href: "/investment" },
    { img: "https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=700&q=80", cat: "PAMM & MAM Forex", title: "PAMM AND MAM FOREX INVESTMENT Package", desc: `${BRAND} PAMM Managers trade using invested capital of investors. Any profits and losses generated will be shared proportionally.`, href: "/forex" },
  ];
  return (
    <div className="wf-section-dark" style={{ background: DARK_NAVY, paddingTop: 60, paddingBottom: 60 }}>
      <div className="container">
        <div className="text-center mb-5">
          <div className="wf-section-badge mb-3">INVESTMENT ACCOUNTS</div>
          <h2 className="wf-h2 c-w">An account for everyone</h2>
          <p className="c-w" style={{ maxWidth: 700, margin: "0 auto", opacity: 0.8 }}>Our goal is to make investing in financial markets more affordable, more intuitive, and more fun, no matter how much experience you have.</p>
        </div>
        <div className="row g-4">
          {cards.map((c, i) => (
            <div key={i} className="col-lg-4">
              <div className="wf-card">
                <div className="wf-card-img">
                  <img src={c.img} alt={c.title} />
                  <span className="wf-card-cat">{c.cat}</span>
                </div>
                <div className="wf-card-body">
                  <h3 className="wf-card-title">{c.title}</h3>
                  <p className="wf-card-desc">{c.desc}</p>
                  <Link href={c.href} className="wf-card-link">Learn More <i className="fas fa-chevron-right"></i></Link>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}

function StatsSection() {
  return (
    <div className="wf-stats" style={{ background: DARK_NAVY }}>
      <div className="container">
        <h2 className="c-w mb-3" style={{ fontSize: 18, textTransform: "uppercase", letterSpacing: 1 }}>We are committed to offering high-performing investment packages to our investors.</h2>
        <p className="c-w mb-5" style={{ fontSize: 15, maxWidth: 900, opacity: 0.8 }}>Our primary focus has been on emerging and rapid growth investment markets with an emphasis on Forex (PAMM/MAM), Cryptocurrencies and other top performing investment solutions.</p>
        <div className="row g-4">
          {[["$563Bn+", "In Active Investments"], ["80M+", "Active Investment Accounts"], ["$394Bn+", "Total Gross Interest Earned"], ["10+", "Years of Experience"]].map(([v, l], i) => (
            <div key={i} className="col-6 col-md-3">
              <div className="wf-stat-item">
                <h1 className="wf-stat-value">{v}</h1>
                <p className="wf-stat-label">{l}</p>
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}

function FixedFundSection() {
  return (
    <section className="wf-section-dark" style={{ background: MID_NAVY }}>
      <div className="container">
        <h2 className="c-w text-center mb-4" style={{ fontSize: 22, textTransform: "uppercase" }}>
          Note: {BRAND} also deals with a VIP plan — <span style={{ color: "#c9a227" }}>"FIXED FUNDS DEPOSIT"</span>
        </h2>
        <div className="row g-5">
          <div className="col-lg-6">
            <h3 className="c-w mb-3" style={{ fontSize: 20, textTransform: "uppercase" }}>About Fixed Funds Deposit</h3>
            <p className="c-w" style={{ opacity: 0.85 }}>Fixed Deposit (FD) is an organized investment package that allows you as an investor to manage your Bitcoin account with a higher interest rate. In this package, investors are opportune to get a 28% weekly interest. The minimum deposit is $9,999.</p>
            <ul className="c-w mt-3" style={{ opacity: 0.85 }}>
              <li>Allows you to accumulate more funds while investing</li>
              <li>Risk-free investment plan guaranteed by the PAMM and CySec Policy</li>
              <li>Earned massively at the end of the investment period</li>
            </ul>
            <Link href="/loan" className="wf-btn-primary d-inline-block mt-4">Learn More</Link>
          </div>
          <div className="col-lg-6">
            <h3 className="c-w mb-3" style={{ fontSize: 20, textTransform: "uppercase" }}>Loan Offer with {BRAND}</h3>
            <p className="c-w" style={{ opacity: 0.85 }}>{BRAND} grants loan offers in two ways: "The Semi-Annual Offer" and "The Annual Offer". Eligibility requires at least 10 active referrals under your name who have invested.</p>
            <h4 className="c-w mt-3" style={{ fontSize: 16, color: "#c9a227" }}>SEMI-ANNUAL OFFER</h4>
            <p className="c-w" style={{ opacity: 0.8 }}>$5,000 – $49,999 | No interest | 6 months | 2-week grace period</p>
            <h4 className="c-w mt-3" style={{ fontSize: 16, color: "#c9a227" }}>ANNUAL OFFER</h4>
            <p className="c-w" style={{ opacity: 0.8 }}>$50,000 – $100,000 | 2% interest | 12 months | 2-week grace period</p>
            <Link href="/loan" className="wf-btn-primary d-inline-block mt-4">Apply for Loan</Link>
          </div>
        </div>
      </div>
    </section>
  );
}

function AwardsSection() {
  const awards = [
    { icon: "fa-trophy", label: "Best Investment Platform 2024", body: "Global Finance Awards" },
    { icon: "fa-medal", label: "Top Asset Manager", body: "CFA Institute · 2023" },
    { icon: "fa-certificate", label: "FCA Full Authorization", body: "Financial Conduct Authority" },
    { icon: "fa-shield-halved", label: "ISO 27001 Certified", body: "Information Security" },
    { icon: "fa-globe", label: "#1 Crypto Fund Manager", body: "Forbes Finance 2023" },
    { icon: "fa-star", label: "5-Star Investor Rating", body: "Trustpilot · 80M+ Reviews" },
  ];
  return (
    <section style={{ background: "#010c1f", padding: "70px 0 60px", borderTop: "1px solid rgba(201,162,39,0.12)", borderBottom: "1px solid rgba(201,162,39,0.12)" }}>
      <div className="container">
        <div className="text-center mb-5">
          <div className="wf-section-badge mb-3" style={{ background: "rgba(201,162,39,0.1)", color: "#c9a227", borderColor: "rgba(201,162,39,0.25)" }}>AWARDS & RECOGNITION</div>
          <h2 className="wf-h2 c-w">Globally Recognised & Trusted</h2>
          <p className="c-w" style={{ opacity: 0.55, maxWidth: 560, margin: "8px auto", fontSize: 14 }}>We hold ourselves to the highest standards of excellence, security and regulatory compliance recognised by leading global bodies.</p>
        </div>
        <div className="wf-awards-grid">
          {awards.map((a, i) => (
            <div key={i} className="wf-award-card">
              <div className="wf-award-icon"><i className={`fas ${a.icon}`}></i></div>
              <div className="wf-award-label">{a.label}</div>
              <div className="wf-award-body">{a.body}</div>
            </div>
          ))}
        </div>
        <div className="wf-cert-row mt-5">
          <div className="wf-cert-item">
            <img src="https://upload.wikimedia.org/wikipedia/en/thumb/6/65/FCA_logo.svg/200px-FCA_logo.svg.png" alt="FCA" style={{ height: 36, filter: "brightness(10)", opacity: 0.5 }} onError={e => (e.currentTarget.style.display = "none")} />
          </div>
          <a href="/welthflow/certificate.pdf" target="_blank" className="wf-cert-btn">
            <i className="fas fa-file-pdf"></i> View Our License Certificate
          </a>
          <div className="wf-cert-item" style={{ textAlign: "right" }}>
            <div style={{ color: "rgba(255,255,255,0.3)", fontSize: 11, textTransform: "uppercase", letterSpacing: 2 }}>Reg. No. FCA-778542</div>
            <div style={{ color: "rgba(255,255,255,0.25)", fontSize: 11 }}>CySec License · No. 187/12</div>
          </div>
        </div>
      </div>
    </section>
  );
}

function RevolutionSection() {
  const imgs = [
    "https://images.unsplash.com/photo-1642790106117-e829e14a795f?w=800&q=80",
    "https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=800&q=80",
    "https://images.unsplash.com/photo-1590283603385-17ffb3a7f29f?w=800&q=80",
    "https://images.unsplash.com/photo-1560520653-9e0e4c89eb11?w=800&q=80",
  ];
  const [imgIdx, setImgIdx] = useState(0);
  useEffect(() => {
    const t = setInterval(() => setImgIdx(x => (x + 1) % imgs.length), 4000);
    return () => clearInterval(t);
  }, []);
  return (
    <section className="wf-section-dark" style={{ background: MID_NAVY }}>
      <div className="container">
        <h2 className="c-w text-center mb-5" style={{ fontSize: 22, textTransform: "uppercase" }}>The Revolution In Asset Investments Management</h2>
        <div className="row g-5 align-items-center">
          <div className="col-lg-6">
            <div className="wf-img-rotator">
              {imgs.map((img, i) => <img key={i} src={img} alt="" className={i === imgIdx ? "active" : ""} />)}
            </div>
          </div>
          <div className="col-lg-6">
            <p className="c-w" style={{ opacity: 0.85 }}>We offer our investors access to high-growth investment opportunities in the financial markets through the utility of state-of-the-art technical facilities and the implementation of industry standard cryptocurrency trading strategies.</p>
            <p className="c-w mt-3" style={{ opacity: 0.85 }}>We're proud to be an asset management company whose culture is driven by strong values and a long-term vision. At {BRAND} Investments, our vision, mission and core values serve as the catalyst in our relations with our clients.</p>
            <a href={REGISTER_URL} className="wf-btn-primary mt-4 d-inline-block">Register Now</a>
          </div>
        </div>
      </div>
    </section>
  );
}

function PlatformSection() {
  return (
    <section className="wf-section-light" style={{ paddingTop: 70, paddingBottom: 60 }}>
      <div className="container">
        <div className="row g-5 align-items-center">
          <div className="col-lg-4">
            <h2 className="wf-h4">Invest in Forex and other high-performing investment packages on our intuitive platform</h2>
            <p className="wf-body">Due to the professionalism of our employees and the introduction of cutting-edge trading facilities, we manage to provide top-quality investment services at minimal costs. We have 80 million+ investors globally.</p>
            <a href={REGISTER_URL} className="wf-btn-primary">Open an Account Now</a>
          </div>
          <div className="col-lg-4 text-center">
            <div className="wf-min-invest">
              <h3 className="text-uppercase mb-0">Investment</h3>
              <div className="wf-invest-amount"><span className="wf-currency">$</span><span className="wf-amount">100</span></div>
              <p className="mb-0">Minimum investment rate</p>
            </div>
          </div>
          <div className="col-lg-4 d-none d-lg-block">
            <img src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=500&q=70" alt="platform" className="img-fluid rounded-3 shadow" style={{ maxHeight: 280, objectFit: "cover", width: "100%" }} />
          </div>
        </div>
      </div>
    </section>
  );
}

function VideoSection() {
  return (
    <section className="wf-video-bg-section">
      <div className="wf-video-bg-video">
        <video autoPlay muted loop playsInline src="/welthflow/intro.mp4" style={{ width: "100%", height: "100%", objectFit: "cover" }} />
      </div>
      <div className="wf-video-overlay" />
      <div className="container wf-video-content text-center" style={{ position: "relative", zIndex: 2 }}>
        <div className="wf-video-badge">
          <i className="fas fa-shield-halved" style={{ color: "#3eda99" }}></i>
          FCA Regulated · CySec Licensed
        </div>
        <h1 className="c-w" style={{ fontSize: 32, textTransform: "uppercase", letterSpacing: 2, fontFamily: "'Lora', serif", fontWeight: 700 }}>{BRAND.toUpperCase()} INVESTMENTS</h1>
        <p className="c-w mt-3" style={{ fontSize: 20, fontWeight: "bold", textTransform: "uppercase", color: "#c9a227" }}>The Revolution In Asset Investments Management</p>
        <p className="c-w" style={{ maxWidth: 700, margin: "16px auto", opacity: 0.85, fontSize: 15, lineHeight: 1.7 }}>We offer our investors access to high-growth investment opportunities in the financial markets through the utility of state-of-the-art technical facilities and the implementation of industry standard investment strategies.</p>
        <div className="wf-video-stats">
          {[["$563Bn+", "Managed Assets"], ["80M+", "Investors"], ["10+ yrs", "Experience"], ["99.9%", "Uptime"]].map(([val, lbl], i) => (
            <div key={i} className="wf-video-stat">
              <div className="wf-video-stat-val">{val}</div>
              <div className="wf-video-stat-lbl">{lbl}</div>
            </div>
          ))}
        </div>
        <div className="d-flex gap-3 justify-content-center flex-wrap mt-4">
          <a href={REGISTER_URL} className="wf-btn-primary">Start Investing</a>
          <a href="/welthflow/certificate.pdf" target="_blank" className="wf-btn-glass-gold">
            <i className="fas fa-certificate" style={{ marginRight: 7 }}></i>View Certificate
          </a>
        </div>
      </div>
    </section>
  );
}

function TaxFreeSection() {
  return (
    <section>
      <div style={{ background: "#1a5fab", padding: "60px 0" }}>
        <div className="container text-center">
          <div className="wf-percent-icon"><i className="fas fa-percent"></i></div>
          <h2 className="c-w mt-3">Earn more — invest tax-free</h2>
          <p className="c-w" style={{ maxWidth: 860, margin: "12px auto 0", fontSize: 16, opacity: 0.9 }}>
            The Government lets you invest up to £20,000 each year tax-free in an ISA. You can 'wrap' any {BRAND} investment in an ISA, so that you don't pay tax on the interest you earn.
            <strong> For investors in New Zealand only.</strong>
          </p>
        </div>
      </div>
    </section>
  );
}

function TestimonialsSection() {
  const testimonials = [
    { img: "https://randomuser.me/api/portraits/men/32.jpg", name: "Ron DiCicco", location: "Florida, United States", stars: 5, text: `I've had incredible customer service since I started investing here. I've been investing with ${BRAND} for nearly 3 years now and I've loved every bit of the experience so far.` },
    { img: "https://randomuser.me/api/portraits/women/44.jpg", name: "Diane Podmanik", location: "Budapest, Hungary", stars: 5, text: `Transparent, profitable, and reliable bitcoin investment company that will make you real money. Thanks to all of you at ${BRAND} for the excellent service.` },
    { img: "https://randomuser.me/api/portraits/men/68.jpg", name: "Joe Tantillo", location: "Prague, Czech Republic", stars: 5, text: `I have always been searching for an opportunity to earn on bitcoin and finally I found ${BRAND} and they have proven to be very reliable and consistent.` },
    { img: "https://randomuser.me/api/portraits/women/22.jpg", name: "Sarah Mitchell", location: "London, United Kingdom", stars: 5, text: `${BRAND} changed my financial life. The ROI is real and consistent. I've recommended them to 8 of my friends and we're all profiting together.` },
  ];
  const [idx, setIdx] = useState(0);
  useEffect(() => {
    const t = setInterval(() => setIdx(x => (x + 1) % testimonials.length), 5000);
    return () => clearInterval(t);
  }, []);
  const t = testimonials[idx];
  return (
    <section style={{ padding: "80px 0 60px" }}>
      <div className="container">
        <div className="row g-5">
          <div className="col-lg-5">
            <div className="wf-section-badge mb-3">TESTIMONIALS</div>
            <h2 className="wf-h2">What our investors are saying about us</h2>
            <p style={{ fontSize: 15, color: "#666" }}>We use the reviews of our investors as the yardstick to measure how well we are doing in the dispensation of our services globally.</p>
            <div className="wf-testimonial-nav mt-3">
              {testimonials.map((_, i) => (
                <button key={i} className={`wf-dot dark ${i === idx ? "active" : ""}`} onClick={() => setIdx(i)} />
              ))}
            </div>
          </div>
          <div className="col-lg-7">
            <div className="wf-testimonial">
              <div className="wf-stars">{[...Array(t.stars)].map((_, i) => <i key={i} className="fas fa-star"></i>)}</div>
              <p className="wf-testimonial-text">"{t.text}"</p>
              <div className="wf-testimonial-author">
                <img src={t.img} alt={t.name} className="wf-testimonial-img" />
                <div>
                  <strong className="d-block">{t.name}</strong>
                  <span style={{ fontSize: 13, color: "#666" }}>{t.location}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}

function SecuritySection() {
  return (
    <section className="wf-security" style={{ padding: "80px 0", background: "#f8fafc" }}>
      <div className="container">
        <div className="row justify-content-center">
          <div className="col-lg-10">
            <div className="text-center mb-5">
              <div className="wf-section-badge mb-2">SAFETY FIRST</div>
              <h4 style={{ color: "#1a5fab", marginBottom: 8 }}>Invest with confidence</h4>
              <h2 className="wf-h2">New level of capital <span className="wf-highlight">security</span></h2>
            </div>
            <div className="row g-5">
              <div className="col-md-6">
                <div className="wf-security-card">
                  <div className="wf-security-icon-wrap"><i className="fas fa-lock"></i></div>
                  <h3 style={{ fontSize: 20, textTransform: "uppercase", marginTop: 16 }}>{BRAND} is Secured</h3>
                  <p>Guaranteed protection. We have built one of the world's most sophisticated security systems. Members records are kept confidential from third parties.</p>
                </div>
              </div>
              <div className="col-md-6">
                <div className="wf-security-card">
                  <div className="wf-security-icon-wrap"><i className="fas fa-handshake"></i></div>
                  <h3 style={{ fontSize: 20, textTransform: "uppercase", marginTop: 16 }}>Fully Regulated & Insured</h3>
                  <p>Our company is fully regulated by the FCA and CySec. Your funds are additionally covered by our insurance policy so you don't have to worry about losing your invested capital.</p>
                </div>
              </div>
              <div className="col-md-6">
                <div className="wf-security-card">
                  <div className="wf-security-icon-wrap"><i className="fas fa-file-certificate"></i></div>
                  <h3 style={{ fontSize: 20, textTransform: "uppercase", marginTop: 16 }}>Licensed & Certified</h3>
                  <p>Welthflow holds valid financial service licenses from major regulatory bodies across 12 jurisdictions, ensuring full legal compliance in every market we operate.</p>
                </div>
              </div>
              <div className="col-md-6">
                <div className="wf-security-card">
                  <div className="wf-security-icon-wrap"><i className="fas fa-server"></i></div>
                  <h3 style={{ fontSize: 20, textTransform: "uppercase", marginTop: 16 }}>Redundant Infrastructure</h3>
                  <p>Our platform is hosted across multiple geo-redundant data centres with 99.99% uptime SLA, real-time monitoring, and automated failover systems.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}

function PricingSection() {
  const cryptoPlans = [
    { name: "TIRO PACKAGE", roi: "1.5%", period: "Daily ROIs", range: "$200 – $4,500", duration: "3 months", referral: "1%", popular: false },
    { name: "SEMI-TIRO PACKAGE", roi: "2%", period: "Daily ROIs", range: "$5,000 – $45,000", duration: "6 months", referral: "1.3%", popular: true },
    { name: "EXECUTIVE PACKAGE", roi: "3.5%", period: "Daily ROIs", range: "$50,000 – $100,000", duration: "1 year", referral: "2%", popular: false },
  ];
  const ltPlans = [
    { name: "CRYPTO", roi: "6.5%", period: "Daily Profits", range: "$2,000 – $50,000", duration: "1 year", popular: false },
    { name: "STANDARD PACKAGE", roi: "5%", period: "Daily Profits", range: "$1,500 – $50,000", duration: "1 year", popular: true },
    { name: "REAL ESTATE", roi: "8%", period: "Daily Profits", range: "$2,500 – $200,000", duration: "1 year", popular: false },
  ];
  function PlanCard({ plan, isLT }: { plan: any; isLT?: boolean }) {
    return (
      <div className="col-lg-4 mb-4">
        <div className={`wf-plan-card ${plan.popular ? "popular" : ""}`}>
          {plan.popular && <div className="wf-plan-badge">Most Popular</div>}
          <h5 className="wf-plan-name">{plan.name}</h5>
          <div className="wf-plan-roi">{plan.roi}<span className="wf-plan-period">/ {plan.period}</span></div>
          <hr />
          <ul className="wf-plan-features">
            <li><b>Investment:</b> {plan.range}</li>
            <li><b>R.O.I:</b> {plan.roi}</li>
            <li><b>Duration:</b> {plan.duration}</li>
            {!isLT && <li><b>Referral Bonus:</b> {plan.referral}</li>}
            <li><b>24/7 Support:</b> YES</li>
          </ul>
          <a href={REGISTER_URL} className="wf-btn-plan">Invest Now</a>
        </div>
      </div>
    );
  }
  return (
    <section className="wf-pricing" id="plans-table">
      <div className="container text-center mb-5">
        <div className="wf-plan-eyebrow">ALL INVESTMENT PLANS</div>
        <h2 className="c-w">Cryptocurrency Investment <span className="wf-highlight">Plans</span></h2>
        <p className="c-w" style={{ opacity: 0.8 }}>Choose from the options below the investment plan which best suits you.</p>
      </div>
      <div className="container"><div className="row">{cryptoPlans.map((p, i) => <PlanCard key={i} plan={p} />)}</div></div>
      <div className="container text-center mb-4 mt-4">
        <h2 className="c-w">Long Term Investment <span className="wf-highlight">Packages</span></h2>
      </div>
      <div className="container"><div className="row">{ltPlans.map((p, i) => <PlanCard key={i} plan={p} isLT />)}</div></div>
    </section>
  );
}

function BuiltForYouSection() {
  return (
    <section style={{ padding: "40px 0 60px" }}>
      <div className="container">
        <div className="wf-built-card">
          <h2 className="c-w">Built for you</h2>
          <p className="c-w" style={{ maxWidth: 700, opacity: 0.9 }}>Our platform is designed for everybody. You can manually pick your own investment package and let our qualified professionals and automated systems manage your investments. And if you need help, there are real people to talk to via our live chat.</p>
          <a href={REGISTER_URL} className="wf-btn-primary">Open an Account Now</a>
        </div>
      </div>
    </section>
  );
}

function InvestorsSection() {
  const cryptoLogos = [
    { name: "Bitcoin", url: "https://cryptologos.cc/logos/bitcoin-btc-logo.svg?v=035" },
    { name: "Ethereum", url: "https://cryptologos.cc/logos/ethereum-eth-logo.svg?v=035" },
    { name: "BNB", url: "https://cryptologos.cc/logos/binance-coin-bnb-logo.svg?v=035" },
    { name: "XRP", url: "https://cryptologos.cc/logos/xrp-xrp-logo.svg?v=035" },
    { name: "Litecoin", url: "https://cryptologos.cc/logos/litecoin-ltc-logo.svg?v=035" },
    { name: "Cardano", url: "https://cryptologos.cc/logos/cardano-ada-logo.svg?v=035" },
  ];
  return (
    <section style={{ padding: "60px 0" }}>
      <div className="container">
        <div className="row g-4 align-items-center">
          <div className="col-lg-4">
            <div className="wf-section-badge mb-3">OUR INVESTORS</div>
            <h4 style={{ fontSize: 18 }}>$562.9bn+ under management on behalf of our world leading investors</h4>
            <p style={{ fontSize: 15, color: "#666" }}>Today, we manage assets on behalf of governments, pension funds, insurers, companies, charities, foundations and individuals across 80 countries.</p>
          </div>
          <div className="col-lg-8">
            <div className="wf-crypto-logos">
              {cryptoLogos.map((l, i) => (
                <div key={i} className="wf-crypto-logo-item">
                  <img src={l.url} alt={l.name} style={{ height: 44, objectFit: "contain" }} onError={e => (e.currentTarget.style.display = "none")} />
                </div>
              ))}
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}

function CTABannerSection() {
  return (
    <section style={{ padding: "0 0 60px" }}>
      <div className="container">
        <div className="wf-cta-banner">
          <div><h3 className="mb-0">Lucrative Investment opportunity at your fingertips.</h3></div>
          <a href={REGISTER_URL} className="wf-btn-primary">Open an Account</a>
        </div>
      </div>
    </section>
  );
}

function FinalHeroSection() {
  return (
    <section className="wf-final-hero" style={{ backgroundImage: "url(https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=1600&q=60)" }}>
      <div className="wf-slide-overlay" />
      <div className="container wf-final-hero-content">
        <div className="col-lg-7">
          <h2 className="c-w" style={{ fontSize: 28, lineHeight: 1.3 }}>Combining high-quality, focused investment management with a rich service experience.</h2>
          <p className="c-w mt-3" style={{ opacity: 0.85 }}>Bringing together the specialist areas of expert investment management and holistic financial advice to provide you with a robust and personalised strategy for achieving your financial objectives.</p>
          <a href={REGISTER_URL} className="wf-btn-primary mt-4 d-inline-block">Get Started Today</a>
        </div>
      </div>
    </section>
  );
}

function LiveChatWidget() {
  // Smartsupp is injected via index.html — this component is a no-op placeholder
  return null;
}

function NotificationBanner() {
  const names = ["Dirk", "Johnny", "Watkin", "Alejandro", "Vina", "Tony", "Ahmed", "Jackson", "Noah", "Aiden", "Isabella", "Greyson", "Peter", "William", "Lucas", "Amelia", "Mason", "Zara"];
  const locations = ["USA", "UAE", "ITALY", "FLORIDA", "MEXICO", "INDIA", "CHINA", "UK", "GERMANY", "AUSTRALIA", "SWEDEN", "PAKISTAN", "SAUDI ARABIA", "CHILE", "SOUTH AFRICA", "SINGAPORE"];
  const amounts = [10000, 2500, 5000, 7500, 3000, 12000, 8000, 4500, 15000, 25000, 50000];
  const [visible, setVisible] = useState(false);
  const [notif, setNotif] = useState({ name: "", location: "", amount: 0 });
  useEffect(() => {
    const show = () => {
      setNotif({ name: names[Math.floor(Math.random() * names.length)], location: locations[Math.floor(Math.random() * locations.length)], amount: amounts[Math.floor(Math.random() * amounts.length)] });
      setVisible(true);
      setTimeout(() => setVisible(false), 4500);
    };
    const t = setInterval(show, 8000);
    setTimeout(show, 3000);
    return () => clearInterval(t);
  }, []);
  return (
    <div className={`wf-notif ${visible ? "show" : ""}`}>
      <div className="wf-notif-icon"><i className="fas fa-dollar-sign"></i></div>
      <div>
        <div className="wf-notif-title">Earning</div>
        <div className="wf-notif-text"><b>{notif.name}</b> from {notif.location} just Earned <b>${notif.amount.toLocaleString()}</b></div>
      </div>
    </div>
  );
}

export default function Home() {
  const [showWallet, setShowWallet] = useState(false);

  return (
    <div className="wf-root">
      <NavBar />
      <HeroSlider />
      <QuickPlansSection />
      <CryptoTicker />
      <GrowSection />
      <OpportunitySection />
      <AwardsSection />
      <InvestmentProducts />
      <WalletSection onOpenWallet={() => setShowWallet(true)} />
      <AccountCards />
      <StatsSection />
      <FixedFundSection />
      <RevolutionSection />
      <PlatformSection />
      <VideoSection />
      <TaxFreeSection />
      <TestimonialsSection />
      <SecuritySection />
      <Leadership />
      <PricingSection />
      <BuiltForYouSection />
      <InvestorsSection />
      <CTABannerSection />
      <FinalHeroSection />
      <Footer />
      <NotificationBanner />
      <LiveChatWidget />
      {showWallet && <WalletConnect onClose={() => setShowWallet(false)} />}
    </div>
  );
}
