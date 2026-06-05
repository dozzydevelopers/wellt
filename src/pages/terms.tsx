import { NavBar, PageHero, Footer } from "./Layout";

const BRAND = "Welthflow";
const DOMAIN = "welthflow.com";
const EMAIL = "admin@welthflow.com";

const sections = [
  {
    title: "1. Acceptance of Terms",
    content: `By accessing or using the ${BRAND} website and investment services, you agree to be bound by these Terms and Conditions, our Privacy Policy, and all applicable laws and regulations. If you do not agree to these terms, you may not use our services. ${BRAND} reserves the right to update these terms at any time. Continued use of the platform constitutes acceptance of any changes.`,
  },
  {
    title: "2. Eligibility",
    content: `To use ${BRAND} services, you must: (a) be at least 18 years of age; (b) have the legal capacity to enter into binding contracts in your jurisdiction; (c) not be a resident of any jurisdiction where the provision of such services is prohibited by law. ${BRAND} reserves the right to verify your identity and eligibility at any time.`,
  },
  {
    title: "3. Investment Risks",
    content: `All investments carry inherent risk, including the possible loss of principal. Past performance is not indicative of future results. ${BRAND} does not guarantee any specific investment return. Cryptocurrency, Forex, and other financial markets are highly volatile and can result in significant losses. You should only invest capital that you can afford to lose. We strongly recommend consulting with an independent financial advisor before making any investment decisions.`,
  },
  {
    title: "4. Account Registration",
    content: `You are responsible for maintaining the confidentiality of your account credentials. All activities conducted through your account are your responsibility. You must provide accurate, current, and complete information during registration. Providing false information may result in immediate account termination and forfeiture of funds. You must notify us immediately at ${EMAIL} if you suspect unauthorized access to your account.`,
  },
  {
    title: "5. Deposits and Withdrawals",
    content: `Minimum deposit amounts vary by investment plan. All deposits must be made using approved payment methods as listed on the platform. Withdrawal requests are processed within 24–72 business hours, subject to verification. ${BRAND} reserves the right to require additional verification before processing any withdrawal. Withdrawal fees, if applicable, will be disclosed at the time of the request.`,
  },
  {
    title: "6. Fixed Funds Deposit Terms",
    content: `The Fixed Funds Deposit (FFD) plan requires a minimum deposit of $9,999. Funds are locked for the agreed investment period. Early withdrawal attracts a penalty of 5% of the total invested amount. Failure to maintain the required weekly deposit schedule will result in account freezing. Account reactivation requires a deposit of the standard minimum investment amount ($200).`,
  },
  {
    title: "7. Loan Program Terms",
    content: `Eligibility for the loan program requires a minimum of 10 active referrals who have made investments. Installment payments are not accepted — loans must be repaid in full by the agreed deadline. The Semi-Annual Offer provides loans from $5,000 to $49,999 with no interest, repayable within 6 months. The Annual Offer provides $50,000 to $100,000 at 2% interest, repayable within 12 months. Both offers include a 2-week grace period.`,
  },
  {
    title: "8. Referral and Affiliate Program",
    content: `Referral bonuses are credited to your account upon successful registration and investment by referred parties. Referral bonus rates range from 1% to 2% depending on your active plan. Fraudulent referrals, including self-referrals or use of multiple accounts, will result in account termination and forfeiture of all bonuses. ${BRAND} reserves the right to modify or discontinue the referral program at any time.`,
  },
  {
    title: "9. Prohibited Activities",
    content: `You may not: use the platform for money laundering or financing illegal activities; create multiple accounts; manipulate the platform in any way; engage in any activity that disrupts the service; violate any applicable local, national, or international laws. Violation of these prohibitions may result in immediate account suspension and legal action.`,
  },
  {
    title: "10. Privacy and Data Protection",
    content: `${BRAND} collects and processes personal data in accordance with applicable data protection laws, including GDPR where applicable. Your data is used solely for the purposes of providing our investment services, complying with legal obligations, and improving our platform. We do not sell your personal data to third parties. For full details, please refer to our Privacy Policy.`,
  },
  {
    title: "11. Regulatory Compliance",
    content: `${BRAND} operates in compliance with regulations set by the Financial Conduct Authority (FCA) and the Cyprus Securities and Exchange Commission (CySec). We are committed to maintaining the highest standards of regulatory compliance and Anti-Money Laundering (AML) procedures. We may be required by law to report certain transactions to regulatory authorities.`,
  },
  {
    title: "12. Limitation of Liability",
    content: `To the maximum extent permitted by law, ${BRAND} shall not be liable for any indirect, incidental, special, consequential, or punitive damages arising from your use of the platform or investment services. Our total liability shall not exceed the amount you have invested with us in the 12 months preceding any claim.`,
  },
  {
    title: "13. Governing Law",
    content: `These Terms shall be governed by and construed in accordance with the laws of England and Wales. Any disputes arising from these Terms shall be subject to the exclusive jurisdiction of the courts of England and Wales. For international investors, local laws may also apply.`,
  },
  {
    title: "14. Contact Us",
    content: `For any questions regarding these Terms and Conditions, please contact our legal team at: Email: ${EMAIL} | Address: 8 Fitzroy Pl, Finnieston, Glasgow G3 7RH, United Kingdom | WhatsApp: +447418611709`,
  },
];

export default function Terms() {
  return (
    <div className="wf-root">
      <NavBar />
      <PageHero
        title="Legal & Terms of Service"
        subtitle={`Last updated: January 1, 2024 — Please read these terms carefully before using ${DOMAIN}.`}
        bg="https://images.unsplash.com/photo-1450101499163-c8848c66ca85?w=1600&q=70"
      />

      <section style={{ padding: "80px 0 100px" }}>
        <div className="container">
          <div className="row">
            {/* Sidebar */}
            <div className="col-lg-3 d-none d-lg-block">
              <div className="wf-terms-sidebar">
                <h6 className="wf-terms-sidebar-title">Contents</h6>
                {sections.map((s, i) => (
                  <a key={i} href={`#section-${i}`} className="wf-terms-sidebar-link">{s.title}</a>
                ))}
              </div>
            </div>

            {/* Content */}
            <div className="col-lg-9">
              <div className="wf-terms-intro">
                <i className="fas fa-balance-scale wf-terms-intro-icon"></i>
                <p>
                  These Terms and Conditions govern your use of {BRAND} ({DOMAIN}) and all associated investment services. By using our platform, you acknowledge that you have read, understood, and agree to be bound by these terms. If you have any questions, please contact us at <a href={`mailto:${EMAIL}`}>{EMAIL}</a>.
                </p>
              </div>

              {sections.map((s, i) => (
                <div key={i} id={`section-${i}`} className="wf-terms-section">
                  <h3 className="wf-terms-heading">{s.title}</h3>
                  <p className="wf-body">{s.content}</p>
                </div>
              ))}
            </div>
          </div>
        </div>
      </section>

      <Footer />
    </div>
  );
}
