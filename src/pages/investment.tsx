import { NavBar, PageHero, Footer } from "./Layout";

const BRAND = "Welthflow";
const REGISTER_URL = "/welthflow/portal/register.php";

const plans = [
  {
    name: "TIRO PACKAGE",
    roi: "1.5%",
    period: "Daily ROIs",
    range: "$200 – $4,500",
    duration: "3 Months",
    referral: "1%",
    popular: false,
    features: ["Daily profit payouts", "Referral bonus: 1%", "24/7 support", "Online dashboard access", "Crypto trading included"],
  },
  {
    name: "SEMI-TIRO PACKAGE",
    roi: "2%",
    period: "Daily ROIs",
    range: "$5,000 – $45,000",
    duration: "6 Months",
    referral: "1.3%",
    popular: true,
    features: ["Daily profit payouts", "Referral bonus: 1.3%", "24/7 priority support", "Online dashboard access", "Crypto + DeFi trading", "Portfolio rebalancing"],
  },
  {
    name: "EXECUTIVE PACKAGE",
    roi: "3.5%",
    period: "Daily ROIs",
    range: "$50,000 – $100,000",
    duration: "1 Year",
    referral: "2%",
    popular: false,
    features: ["Daily profit payouts", "Referral bonus: 2%", "Dedicated account manager", "Online dashboard access", "Full crypto suite", "Portfolio rebalancing", "Tax reporting assistance"],
  },
];

const howItWorks = [
  { icon: "fa-user-plus", step: "01", title: "Create Your Account", desc: "Sign up in minutes with your basic details. Identity verification is quick and secure." },
  { icon: "fa-wallet", step: "02", title: "Fund Your Account", desc: "Deposit cryptocurrency or fiat currency using any of our supported payment methods." },
  { icon: "fa-sliders", step: "03", title: "Choose a Plan", desc: "Select the investment package that matches your goals and risk tolerance." },
  { icon: "fa-chart-line", step: "04", title: "Earn Daily Returns", desc: "Watch your investment grow with daily ROI payouts credited directly to your account." },
];

export default function Investment() {
  return (
    <div className="wf-root">
      <NavBar />
      <PageHero
        title="Cryptocurrency Investment"
        subtitle="Access high-growth opportunities in Bitcoin and crypto markets — managed by our expert team."
        bg="https://images.unsplash.com/photo-1518546305927-5a555bb7020d?w=1600&q=70"
      />

      {/* Intro */}
      <section style={{ padding: "80px 0" }}>
        <div className="container">
          <div className="row align-items-center g-5">
            <div className="col-lg-6">
              <div className="wf-section-badge mb-3">CRYPTO INVESTING</div>
              <h2 className="wf-h2">Why Invest in Cryptocurrency with {BRAND}?</h2>
              <p className="wf-body mb-3">You don't need to be a cryptocurrency expert to profit from this sector. Our professional team takes charge of all trading and mining management, so your investment return is secured while you sit back and earn.</p>
              <p className="wf-body mb-4">We offer investors access to high-growth opportunities in the Bitcoin markets through cutting-edge technical facilities and industry-standard cryptocurrency trading strategies.</p>
              <div className="row g-3">
                {[["fa-shield-halved", "Secured Returns"], ["fa-clock", "Daily Payouts"], ["fa-headset", "24/7 Support"], ["fa-chart-line", "Expert Management"]].map(([icon, label], i) => (
                  <div key={i} className="col-6">
                    <div className="wf-feature-chip">
                      <i className={`fas ${icon}`}></i> {label}
                    </div>
                  </div>
                ))}
              </div>
            </div>
            <div className="col-lg-6">
              <img src="https://images.unsplash.com/photo-1642790106117-e829e14a795f?w=700&q=70" alt="crypto trading" className="img-fluid rounded-3 shadow" />
            </div>
          </div>
        </div>
      </section>

      {/* How it Works */}
      <section style={{ background: "#f8fafc", padding: "80px 0" }}>
        <div className="container">
          <div className="text-center mb-5">
            <div className="wf-section-badge mb-2">PROCESS</div>
            <h2 className="wf-h2">How It Works</h2>
          </div>
          <div className="row g-4">
            {howItWorks.map((step, i) => (
              <div key={i} className="col-md-6 col-lg-3">
                <div className="wf-step-card">
                  <div className="wf-step-num">{step.step}</div>
                  <div className="wf-step-icon"><i className={`fas ${step.icon}`}></i></div>
                  <h4 className="wf-step-title">{step.title}</h4>
                  <p className="wf-step-desc">{step.desc}</p>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Plans */}
      <section style={{ background: "#01123c", padding: "80px 0" }}>
        <div className="container">
          <div className="text-center mb-5">
            <div className="wf-plan-eyebrow">INVESTMENT PLANS</div>
            <h2 className="c-w">Cryptocurrency Investment <span className="wf-highlight">Plans</span></h2>
            <p className="c-w" style={{ maxWidth: 560, margin: "0 auto" }}>Choose the plan that best suits your financial goals. All plans include daily ROI payouts and 24/7 support.</p>
          </div>
          <div className="row g-4 justify-content-center">
            {plans.map((p, i) => (
              <div key={i} className="col-lg-4">
                <div className={`wf-plan-card ${p.popular ? "popular" : ""}`} style={{ height: "100%" }}>
                  {p.popular && <div className="wf-plan-badge">Most Popular</div>}
                  <h5 className="wf-plan-name">{p.name}</h5>
                  <div className="wf-plan-roi">{p.roi}</div>
                  <span className="wf-plan-period">/ {p.period}</span>
                  <hr />
                  <div style={{ color: "rgba(255,255,255,0.7)", fontSize: 13, marginBottom: 16 }}>
                    <div><b style={{ color: "#fff" }}>Range:</b> {p.range}</div>
                    <div><b style={{ color: "#fff" }}>Duration:</b> {p.duration}</div>
                    <div><b style={{ color: "#fff" }}>Referral:</b> {p.referral}</div>
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

      {/* CTA */}
      <div style={{ background: "#062f6d", padding: "70px 0" }}>
        <div className="container text-center">
          <h2 style={{ fontFamily: "'Lora', serif", color: "#fff", fontSize: 28, marginBottom: 12 }}>Start Earning Today</h2>
          <p style={{ color: "rgba(255,255,255,0.8)", marginBottom: 28, maxWidth: 500, margin: "0 auto 28px" }}>Open your account in minutes and join millions of investors already growing their wealth with {BRAND}.</p>
          <a href={REGISTER_URL} className="wf-btn-primary">Create Free Account</a>
        </div>
      </div>

      <Footer />
    </div>
  );
}
