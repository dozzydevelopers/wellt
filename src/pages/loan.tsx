import { NavBar, PageHero, Footer } from "./Layout";

const BRAND = "Welthflow";
const REGISTER_URL = "/welthflow/portal/register.php";

export default function Loan() {
  return (
    <div className="wf-root">
      <NavBar />
      <PageHero
        title="Loan & Pension Funds"
        subtitle="Access flexible loan options and retirement investment packages designed for long-term financial security."
        bg="https://images.unsplash.com/photo-1559526324-4b87b5e36e44?w=1600&q=70"
      />

      {/* Pension Funds */}
      <section style={{ padding: "80px 0" }}>
        <div className="container">
          <div className="row align-items-center g-5">
            <div className="col-lg-6">
              <div className="wf-section-badge mb-3">RETIREMENT</div>
              <h2 className="wf-h2">Retirement & Pension Fund Investment</h2>
              <p className="wf-body mb-3">After retirement, there needs to be a regular source of income which is possible only when you make the right investments. Investing in the right plans helps you get a regular income and also helps you deal with the rising cost of living.</p>
              <p className="wf-body mb-4">{BRAND}'s Pension Fund investment package is specifically designed for investors planning for their long-term future. Our professional managers allocate your capital across a diversified portfolio of assets optimised for steady, compounding returns.</p>
              <div className="wf-loan-features">
                {[
                  ["fa-piggy-bank", "Tax-efficient retirement savings"],
                  ["fa-chart-line", "Compounding monthly returns"],
                  ["fa-shield-halved", "Capital protected investment"],
                  ["fa-rotate", "Flexible contribution schedule"],
                ].map(([icon, text], i) => (
                  <div key={i} className="wf-loan-feature">
                    <i className={`fas ${icon}`}></i>
                    <span>{text}</span>
                  </div>
                ))}
              </div>
            </div>
            <div className="col-lg-6">
              <img src="https://images.unsplash.com/photo-1559526324-4b87b5e36e44?w=700&q=70" alt="pension" className="img-fluid rounded-3 shadow" />
            </div>
          </div>
        </div>
      </section>

      {/* Loan Program */}
      <section style={{ background: "#f8fafc", padding: "80px 0" }}>
        <div className="container">
          <div className="text-center mb-5">
            <div className="wf-section-badge mb-2">LOAN PROGRAM</div>
            <h2 className="wf-h2">Loan Offer with {BRAND} Funds Company</h2>
            <p className="wf-body" style={{ color: "#718096", maxWidth: 620, margin: "0 auto" }}>
              {BRAND} grants loan offers exclusively to active investors. There are two loan tiers available depending on your investment profile.
            </p>
          </div>

          <div className="row g-4 mb-5">
            <div className="col-lg-6">
              <div className="wf-loan-card">
                <div className="wf-loan-card-header">
                  <i className="fas fa-calendar-half-stroke"></i>
                  <h3>Semi-Annual Offer</h3>
                </div>
                <div className="wf-loan-amount">$5,000 – $49,999</div>
                <ul className="wf-loan-list">
                  <li><i className="fas fa-check"></i> Zero interest rate</li>
                  <li><i className="fas fa-check"></i> Repayment period: 6 months</li>
                  <li><i className="fas fa-check"></i> Grace period: 2 weeks</li>
                  <li><i className="fas fa-check"></i> Full lump-sum repayment required</li>
                  <li><i className="fas fa-check"></i> Requires 10+ active referrals</li>
                </ul>
                <a href={REGISTER_URL} className="wf-btn-primary d-block text-center">Apply Now</a>
              </div>
            </div>
            <div className="col-lg-6">
              <div className="wf-loan-card featured">
                <div className="wf-loan-badge">Higher Limit</div>
                <div className="wf-loan-card-header">
                  <i className="fas fa-calendar-days"></i>
                  <h3>Annual Offer</h3>
                </div>
                <div className="wf-loan-amount">$50,000 – $100,000</div>
                <ul className="wf-loan-list">
                  <li><i className="fas fa-check"></i> 2% interest on loan amount</li>
                  <li><i className="fas fa-check"></i> Repayment period: 12 months</li>
                  <li><i className="fas fa-check"></i> Grace period: 2 weeks</li>
                  <li><i className="fas fa-check"></i> Full lump-sum repayment required</li>
                  <li><i className="fas fa-check"></i> Requires 10+ active referrals</li>
                </ul>
                <a href={REGISTER_URL} className="wf-btn-primary d-block text-center">Apply Now</a>
              </div>
            </div>
          </div>

          {/* Eligibility */}
          <div className="wf-eligibility-box">
            <h4><i className="fas fa-info-circle"></i> Loan Eligibility Requirements</h4>
            <div className="row g-3 mt-2">
              {[
                "Must be an active {BRAND} investor",
                "Minimum 10 active referrals who have invested",
                "Account in good standing (no outstanding issues)",
                "All referred investors must have made at least one deposit",
                "Full repayment — no installment payments permitted",
                "Loan applied for through official account dashboard",
              ].map((item, i) => (
                <div key={i} className="col-md-6">
                  <div className="wf-eligibility-item">
                    <i className="fas fa-check-circle"></i>
                    <span>{item.replace("{BRAND}", BRAND)}</span>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      </section>

      {/* Fixed Deposit */}
      <section style={{ background: "#01123c", padding: "80px 0" }}>
        <div className="container">
          <div className="row align-items-center g-5">
            <div className="col-lg-6">
              <div className="wf-section-badge mb-3">VIP PLAN</div>
              <h2 className="wf-h2 c-w">Fixed Funds Deposit</h2>
              <p className="c-w mb-3">The Fixed Funds Deposit (FFD) is {BRAND}'s premium VIP investment plan, offering up to 28% weekly interest. This plan is ideal for serious investors seeking maximum compounding returns over a defined period.</p>
              <div className="row g-3 mb-4">
                {[["$9,999", "Minimum Deposit"], ["28%", "Weekly Interest"], ["5%", "Early Exit Penalty"], ["$200", "Account Activation Fee"]].map(([val, label], i) => (
                  <div key={i} className="col-6">
                    <div style={{ border: "1px solid rgba(255,255,255,0.2)", borderRadius: 10, padding: "16px", textAlign: "center" }}>
                      <div style={{ fontFamily: "'Lora', serif", fontSize: 28, fontWeight: 700, color: "#3eda99" }}>{val}</div>
                      <div style={{ color: "rgba(255,255,255,0.65)", fontSize: 12, marginTop: 4 }}>{label}</div>
                    </div>
                  </div>
                ))}
              </div>
              <a href={REGISTER_URL} className="wf-btn-primary">Start Fixed Deposit</a>
            </div>
            <div className="col-lg-6">
              <div className="wf-ffd-benefits">
                <h4 className="c-w mb-4">Benefits of Fixed Funds Deposit</h4>
                {[
                  { icon: "fa-coins", title: "Accumulated Wealth", desc: "Earn massively at the end of the investment period — far more than standard packages." },
                  { icon: "fa-shield-halved", title: "Risk-Free Returns", desc: "Your investment is protected by PAMM and CySec Policy, guaranteeing fixed returns." },
                  { icon: "fa-rotate", title: "Flexible Migration", desc: "Migrate from any ordinary account to Fixed Deposit with no separate account required." },
                  { icon: "fa-money-bill-wave", title: "Emergency Access", desc: "Premature withdrawal is available for emergencies — subject to a 5% penalty." },
                ].map((b, i) => (
                  <div key={i} className="wf-ffd-benefit">
                    <div className="wf-ffd-benefit-icon"><i className={`fas ${b.icon}`}></i></div>
                    <div>
                      <h6 className="c-w mb-1">{b.title}</h6>
                      <p style={{ color: "rgba(255,255,255,0.7)", fontSize: 14, marginBottom: 0 }}>{b.desc}</p>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </div>
        </div>
      </section>

      <Footer />
    </div>
  );
}
