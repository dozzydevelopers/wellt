import { useState } from "react";
import { NavBar, PageHero, Footer } from "./Layout";

const faqs = [
  {
    q: "What is Welthflow?",
    a: "Welthflow is a premier asset management and investment platform dedicated to helping investors around the world reach their desired investment goals. We offer a broad range of investment packages including Cryptocurrency, Real Estate, FOREX (PAMM/MAM), Agro, and Fixed Deposit products.",
  },
  {
    q: "How do I open an account?",
    a: "Opening an account is simple. Click the 'Create an Account' or 'Get Started' button, fill in your personal details, verify your email address, and you'll be ready to start investing within minutes. Our onboarding process is fully guided.",
  },
  {
    q: "What is the minimum investment?",
    a: "The minimum investment varies by package. Our Tiro Package starts at $200, making it accessible for new investors. Our Semi-Tiro Package starts at $5,000, and the Executive Package starts at $50,000. Long-term packages begin at $1,500.",
  },
  {
    q: "How are returns paid?",
    a: "Returns (ROI) are calculated on a daily basis and credited to your account. You can withdraw your returns at any time depending on your selected investment plan. Standard plans allow daily withdrawals, while Fixed Deposit plans have scheduled withdrawal dates.",
  },
  {
    q: "Is my investment safe?",
    a: "Your investment security is our top priority. Welthflow is fully regulated by the FCA and CySec. Additionally, all invested funds are covered by our comprehensive insurance policy, ensuring your capital is protected against unforeseen losses.",
  },
  {
    q: "What is PAMM/MAM Forex trading?",
    a: "PAMM (Percentage Allocation Management Module) and MAM (Multi-Account Manager) are Forex trading systems where our professional managers trade using the pooled capital of investors. Any profits and losses are shared proportionally among all participating accounts.",
  },
  {
    q: "Can I refer friends to Welthflow?",
    a: "Yes! Our referral/affiliate program rewards you for every investor you refer. Referral bonuses range from 1% to 2% depending on your active investment plan. There is no limit to how many people you can refer.",
  },
  {
    q: "How do I make a withdrawal?",
    a: "Withdrawals are processed through your account dashboard. Simply navigate to the 'Withdrawal' section, enter the amount, and select your preferred payment method. Standard withdrawals are processed within 24–72 business hours.",
  },
  {
    q: "What is the Fixed Funds Deposit plan?",
    a: "The Fixed Funds Deposit (FFD) is our VIP investment plan offering a 28% weekly interest rate. The minimum deposit is $9,999, and funds are held for the agreed period (monthly or yearly). Early withdrawal is permitted with a 5% penalty.",
  },
  {
    q: "Are there loan options available?",
    a: "Yes. Active investors with at least 10 referred accounts are eligible for our loan program. The Semi-Annual Offer provides $5,000–$49,999 with no interest for up to 6 months. The Annual Offer provides $50,000–$100,000 at 2% interest for up to 12 months.",
  },
  {
    q: "How do I contact support?",
    a: "Our support team is available 24/7. You can reach us via email at admin@welthflow.com, through our live chat widget on the website, or via WhatsApp at +447418611709. We aim to respond to all queries within 2 hours.",
  },
  {
    q: "Is Welthflow available in my country?",
    a: "Welthflow serves investors across 80+ countries worldwide. We currently manage assets on behalf of clients in North America, Europe, Asia, Africa, the Middle East, and Oceania. Check with our support team if you have a specific country inquiry.",
  },
];

export default function FAQ() {
  const [open, setOpen] = useState<number | null>(0);

  return (
    <div className="wf-root">
      <NavBar />
      <PageHero
        title="Frequently Asked Questions"
        subtitle="Find answers to the most common questions about investing with Welthflow."
        bg="https://images.unsplash.com/photo-1590283603385-17ffb3a7f29f?w=1600&q=70"
      />

      <section style={{ padding: "80px 0 100px" }}>
        <div className="container">
          <div className="row justify-content-center">
            <div className="col-lg-8">
              <div className="wf-section-badge mb-3">HELP CENTER</div>
              <h2 className="wf-h2 mb-2">Got Questions? We Have Answers.</h2>
              <p className="wf-body mb-5" style={{ color: "#718096" }}>
                Can't find what you're looking for? Contact us at <a href="mailto:admin@welthflow.com" style={{ color: "#062f6d" }}>admin@welthflow.com</a>
              </p>

              <div className="wf-accordion">
                {faqs.map((item, i) => (
                  <div key={i} className={`wf-accordion-item ${open === i ? "open" : ""}`}>
                    <button className="wf-accordion-btn" onClick={() => setOpen(open === i ? null : i)}>
                      <span>{item.q}</span>
                      <i className={`fas ${open === i ? "fa-minus" : "fa-plus"}`}></i>
                    </button>
                    {open === i && (
                      <div className="wf-accordion-body">
                        <p>{item.a}</p>
                      </div>
                    )}
                  </div>
                ))}
              </div>
            </div>

            <div className="col-lg-4 mt-5 mt-lg-0">
              <div className="wf-faq-cta-card">
                <div className="wf-faq-icon"><i className="fas fa-headset"></i></div>
                <h4>Still have questions?</h4>
                <p>Our investment advisors are available 24/7 to help you get started.</p>
                <a href="mailto:admin@welthflow.com" className="wf-btn-primary d-block text-center">Email Support</a>
                <a href="#" className="wf-btn-outline d-block text-center mt-3">Live Chat</a>
              </div>

              <div className="wf-faq-stat-card mt-4">
                <div className="wf-faq-stat"><span className="wf-faq-num">80M+</span><span>Active Investors</span></div>
                <div className="wf-faq-stat"><span className="wf-faq-num">10+</span><span>Years Experience</span></div>
                <div className="wf-faq-stat"><span className="wf-faq-num">$563Bn</span><span>Assets Managed</span></div>
                <div className="wf-faq-stat"><span className="wf-faq-num">80+</span><span>Countries Served</span></div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <Footer />
    </div>
  );
}
