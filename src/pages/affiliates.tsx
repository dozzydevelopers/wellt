import { NavBar, PageHero, Footer } from "./Layout";

const BRAND = "Welthflow";
const REGISTER_URL = "/welthflow/portal/register.php";

const tiers = [
  { plan: "Tiro Package", investRange: "$200 – $4,500", referralBonus: "1%", tier: "Bronze", color: "#cd7f32" },
  { plan: "Semi-Tiro Package", investRange: "$5,000 – $45,000", referralBonus: "1.3%", tier: "Silver", color: "#aaa9ad" },
  { plan: "Executive Package", investRange: "$50,000 – $100,000", referralBonus: "2%", tier: "Gold", color: "#ffd700" },
];

const steps = [
  { icon: "fa-user-plus", step: "01", title: "Sign Up & Invest", desc: "Create your account and make your first investment to activate your affiliate status." },
  { icon: "fa-share-nodes", step: "02", title: "Share Your Link", desc: "Get your unique referral link from your dashboard and share it with your network." },
  { icon: "fa-users", step: "03", title: "Friends Join & Invest", desc: "Your referrals sign up using your link and make their first investment." },
  { icon: "fa-coins", step: "04", title: "Earn Commissions", desc: "Receive your referral bonus automatically credited to your account." },
];

export default function Affiliates() {
  return (
    <div className="wf-root">
      <NavBar />
      <PageHero
        title="Affiliate Program"
        subtitle="Earn generous commissions by referring friends and colleagues to invest with Welthflow."
        bg="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=1600&q=70"
      />

      {/* Hero stats */}
      <div style={{ background: "#01123c", padding: "50px 0" }}>
        <div className="container">
          <div className="row g-4 text-center">
            {[["Up to 2%", "Commission per referral"], ["Unlimited", "Referrals you can make"], ["Instant", "Commission crediting"], ["$0", "Cost to join"]].map(([val, label], i) => (
              <div key={i} className="col-6 col-md-3">
                <div style={{ fontFamily: "'Lora', serif", fontSize: 36, fontWeight: 700, color: "#3eda99" }}>{val}</div>
                <div style={{ color: "rgba(255,255,255,0.65)", fontSize: 13, marginTop: 4 }}>{label}</div>
              </div>
            ))}
          </div>
        </div>
      </div>

      {/* How it Works */}
      <section style={{ padding: "80px 0" }}>
        <div className="container">
          <div className="text-center mb-5">
            <div className="wf-section-badge mb-2">HOW IT WORKS</div>
            <h2 className="wf-h2">Start Earning in 4 Simple Steps</h2>
          </div>
          <div className="row g-4">
            {steps.map((step, i) => (
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

      {/* Commission Table */}
      <section style={{ background: "#f8fafc", padding: "80px 0" }}>
        <div className="container">
          <div className="text-center mb-5">
            <div className="wf-section-badge mb-2">COMMISSIONS</div>
            <h2 className="wf-h2">Referral Bonus by Plan</h2>
            <p className="wf-body" style={{ color: "#718096", maxWidth: 560, margin: "0 auto" }}>
              The more your referral invests, the more you earn. All commissions are paid instantly upon successful investment.
            </p>
          </div>
          <div className="row g-4 justify-content-center">
            {tiers.map((t, i) => (
              <div key={i} className="col-md-4">
                <div className="wf-tier-card">
                  <div className="wf-tier-badge" style={{ background: t.color }}>{t.tier}</div>
                  <h4 className="wf-tier-plan">{t.plan}</h4>
                  <div className="wf-tier-range">{t.investRange}</div>
                  <div className="wf-tier-bonus">{t.referralBonus}<span>referral bonus</span></div>
                  <p style={{ fontSize: 13, color: "#718096", marginBottom: 0 }}>Per every referred investor who joins at this plan level.</p>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Benefits */}
      <section style={{ padding: "80px 0" }}>
        <div className="container">
          <div className="row align-items-center g-5">
            <div className="col-lg-6">
              <div className="wf-section-badge mb-3">AFFILIATE BENEFITS</div>
              <h2 className="wf-h2">Why Join the {BRAND} Affiliate Program?</h2>
              <p className="wf-body mb-4">Our affiliate program is one of the most rewarding in the investment industry. With no cap on earnings and instant commission payouts, it's a powerful way to generate passive income alongside your own investments.</p>
              <div className="wf-loan-features">
                {[
                  ["fa-infinity", "Unlimited referral potential"],
                  ["fa-bolt", "Instant commission crediting"],
                  ["fa-link", "Unique trackable referral link"],
                  ["fa-chart-bar", "Real-time referral dashboard"],
                  ["fa-wallet", "Multiple withdrawal options"],
                  ["fa-headset", "Dedicated affiliate support"],
                ].map(([icon, text], i) => (
                  <div key={i} className="wf-loan-feature">
                    <i className={`fas ${icon}`}></i>
                    <span>{text}</span>
                  </div>
                ))}
              </div>
            </div>
            <div className="col-lg-6">
              <div style={{ background: "#01123c", borderRadius: 16, padding: "40px 36px" }}>
                <h4 className="c-w mb-4">Loan Program Access</h4>
                <p className="c-w mb-3" style={{ fontSize: 15 }}>
                  Affiliates who refer 10 or more active investors also become eligible for {BRAND}'s exclusive Loan Program, giving you access to:
                </p>
                <ul style={{ listStyle: "none", padding: 0 }}>
                  {[
                    "$5,000 – $49,999 at 0% interest (Semi-Annual)",
                    "$50,000 – $100,000 at 2% interest (Annual)",
                    "Repayment periods of 6 or 12 months",
                    "2-week grace period included",
                  ].map((item, i) => (
                    <li key={i} style={{ color: "rgba(255,255,255,0.8)", fontSize: 14, padding: "8px 0", borderBottom: "1px solid rgba(255,255,255,0.08)", display: "flex", gap: 10, alignItems: "flex-start" }}>
                      <i className="fas fa-check-circle" style={{ color: "#3eda99", marginTop: 2 }}></i> {item}
                    </li>
                  ))}
                </ul>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* CTA */}
      <div style={{ background: "#062f6d", padding: "70px 0" }}>
        <div className="container text-center">
          <h2 style={{ fontFamily: "'Lora', serif", color: "#fff", fontSize: 28, marginBottom: 12 }}>Ready to Start Earning?</h2>
          <p style={{ color: "rgba(255,255,255,0.8)", marginBottom: 28, maxWidth: 520, margin: "0 auto 28px" }}>
            Join thousands of {BRAND} affiliates already earning generous commissions. Sign up today and start sharing your referral link.
          </p>
          <a href={REGISTER_URL} className="wf-btn-primary">Join the Affiliate Program</a>
        </div>
      </div>

      <Footer />
    </div>
  );
}
