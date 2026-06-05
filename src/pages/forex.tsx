import { NavBar, PageHero, Footer } from "./Layout";

const BRAND = "Welthflow";
const REGISTER_URL = "/welthflow/portal/register.php";

const ltPlans = [
  {
    name: "CRYPTO",
    roi: "6.5%",
    period: "Daily Profits",
    range: "$2,000 – $50,000",
    duration: "1 Year",
    popular: false,
    features: ["Daily profit payouts", "Crypto portfolio managed", "24/7 support", "Dashboard analytics", "Monthly statements"],
  },
  {
    name: "STANDARD PACKAGE",
    roi: "5%",
    period: "Daily Profits",
    range: "$1,500 – $50,000",
    duration: "1 Year",
    popular: true,
    features: ["Daily profit payouts", "PAMM/MAM managed", "24/7 priority support", "Dashboard analytics", "Monthly statements", "Portfolio diversification"],
  },
  {
    name: "REAL ESTATE",
    roi: "8%",
    period: "Daily Profits",
    range: "$2,500 – $200,000",
    duration: "1 Year",
    popular: false,
    features: ["Daily profit payouts", "Real estate portfolio", "Dedicated account manager", "Dashboard analytics", "Quarterly reports", "Tax documentation", "Full portfolio rebalancing"],
  },
];

export default function Forex() {
  return (
    <div className="wf-root">
      <NavBar />
      <PageHero
        title="FOREX (PAMM/MAM) Investment"
        subtitle="Professional Forex trading managed by our expert PAMM managers — invest and earn proportionally."
        bg="https://images.unsplash.com/photo-1642790106117-e829e14a795f?w=1600&q=70"
      />

      {/* What is PAMM */}
      <section style={{ padding: "80px 0" }}>
        <div className="container">
          <div className="row align-items-center g-5">
            <div className="col-lg-6">
              <div className="wf-section-badge mb-3">PAMM / MAM FOREX</div>
              <h2 className="wf-h2">What is PAMM Trading?</h2>
              <p className="wf-body mb-3">PAMM (Percentage Allocation Management Module) is a revolutionary Forex trading system where professional managers trade using the pooled investment capital of multiple investors. All profits and losses are shared proportionally among all participating accounts.</p>
              <p className="wf-body mb-3">{BRAND}'s PAMM Managers are rigorously vetted professionals with proven track records in global Forex markets. They trade using sophisticated algorithms, technical analysis, and market intelligence to consistently generate returns for our investors.</p>
              <p className="wf-body">MAM (Multi-Account Manager) allows a single manager to trade across multiple client accounts simultaneously, ensuring efficiency and consistency in execution across all investor portfolios.</p>
            </div>
            <div className="col-lg-6">
              <div className="row g-3">
                {[
                  { icon: "fa-chart-line", title: "How it Works", desc: "You deposit funds into your managed account. Our PAMM manager trades on your behalf. Profits and losses are distributed proportionally based on your investment size." },
                  { icon: "fa-shield-halved", title: "Risk Management", desc: "Our managers follow strict risk management protocols including stop-loss limits, position sizing rules, and diversification across multiple currency pairs." },
                  { icon: "fa-eye", title: "Full Transparency", desc: "Monitor your account performance in real time through your dashboard. Full trade history and P&L statements are available at any time." },
                  { icon: "fa-rotate", title: "Flexible Investment", desc: "You remain in control. You can add to your investment, withdraw returns, or change plans at any time according to your investment plan terms." },
                ].map((item, i) => (
                  <div key={i} className="col-md-6">
                    <div className="wf-forex-card">
                      <div className="wf-forex-icon"><i className={`fas ${item.icon}`}></i></div>
                      <h5 className="wf-forex-title">{item.title}</h5>
                      <p className="wf-forex-desc">{item.desc}</p>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Long-term plans */}
      <section style={{ background: "#01123c", padding: "80px 0" }}>
        <div className="container">
          <div className="text-center mb-5">
            <div className="wf-plan-eyebrow">LONG-TERM PACKAGES</div>
            <h2 className="c-w">Long Term Investment <span className="wf-highlight">Packages</span></h2>
            <p className="c-w" style={{ maxWidth: 560, margin: "0 auto" }}>Our long-term packages offer the highest daily profit rates for serious investors committed to sustained wealth growth.</p>
          </div>
          <div className="row g-4 justify-content-center">
            {ltPlans.map((p, i) => (
              <div key={i} className="col-lg-4">
                <div className={`wf-plan-card ${p.popular ? "popular" : ""}`} style={{ height: "100%" }}>
                  {p.popular && <div className="wf-plan-badge">Best Value</div>}
                  <h5 className="wf-plan-name">{p.name}</h5>
                  <div className="wf-plan-roi">{p.roi}</div>
                  <span className="wf-plan-period">/ {p.period}</span>
                  <hr />
                  <div style={{ color: "rgba(255,255,255,0.7)", fontSize: 13, marginBottom: 16 }}>
                    <div><b style={{ color: "#fff" }}>Range:</b> {p.range}</div>
                    <div><b style={{ color: "#fff" }}>Duration:</b> {p.duration}</div>
                  </div>
                  <ul className="wf-plan-features">
                    {p.features.map((f, j) => <li key={j}>{f}</li>)}
                  </ul>
                  <a href={REGISTER_URL} className="wf-btn-plan">Invest Now</a>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Why Forex */}
      <section style={{ background: "#f8fafc", padding: "80px 0" }}>
        <div className="container">
          <div className="text-center mb-5">
            <div className="wf-section-badge mb-2">ADVANTAGES</div>
            <h2 className="wf-h2">Why Trade Forex with {BRAND}?</h2>
          </div>
          <div className="row g-4">
            {[
              { icon: "fa-globe", title: "24/5 Global Market", desc: "The Forex market operates 24 hours a day, 5 days a week, providing continuous opportunities regardless of your time zone." },
              { icon: "fa-money-bill-trend-up", title: "High Liquidity", desc: "With over $6.6 trillion traded daily, Forex is the world's most liquid market — ensuring your funds can always be accessed." },
              { icon: "fa-users", title: "Expert Management", desc: "Our PAMM managers have proven track records with average annual returns that outperform traditional investment vehicles." },
              { icon: "fa-layer-group", title: "Diversified Exposure", desc: "Trade across major, minor, and exotic currency pairs plus commodities and indices for a balanced, diversified portfolio." },
            ].map((item, i) => (
              <div key={i} className="col-md-6 col-lg-3">
                <div className="wf-value-card">
                  <div className="wf-value-icon"><i className={`fas ${item.icon}`}></i></div>
                  <h4 className="wf-value-title">{item.title}</h4>
                  <p className="wf-value-desc">{item.desc}</p>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* CTA */}
      <div style={{ background: "#062f6d", padding: "70px 0" }}>
        <div className="container text-center">
          <h2 style={{ fontFamily: "'Lora', serif", color: "#fff", fontSize: 28, marginBottom: 12 }}>Start Forex Investing Today</h2>
          <p style={{ color: "rgba(255,255,255,0.8)", marginBottom: 28, maxWidth: 500, margin: "0 auto 28px" }}>Let our expert PAMM managers put your money to work in the world's largest financial market.</p>
          <a href={REGISTER_URL} className="wf-btn-primary">Open Your Account</a>
        </div>
      </div>

      <Footer />
    </div>
  );
}
