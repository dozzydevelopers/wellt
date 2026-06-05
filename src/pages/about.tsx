import { NavBar, PageHero, Footer } from "./Layout";

const BRAND = "Welthflow";

const team = [
  { name: "James Harrington", role: "Chief Executive Officer", img: "https://randomuser.me/api/portraits/men/52.jpg", bio: "20+ years in global asset management across Europe and the Americas." },
  { name: "Sophie Chen", role: "Chief Investment Officer", img: "https://randomuser.me/api/portraits/women/62.jpg", bio: "Former hedge fund manager with expertise in cryptocurrency and emerging markets." },
  { name: "Marcus Webb", role: "Head of Forex Trading", img: "https://randomuser.me/api/portraits/men/44.jpg", bio: "PAMM/MAM specialist with a decade of institutional trading experience." },
  { name: "Amara Osei", role: "Head of Client Relations", img: "https://randomuser.me/api/portraits/women/33.jpg", bio: "Dedicated to ensuring every investor receives world-class service and support." },
];

const values = [
  { icon: "fa-shield-halved", title: "Integrity", desc: "We operate with full transparency and hold ourselves to the highest ethical standards in every transaction." },
  { icon: "fa-chart-line", title: "Performance", desc: "We are relentlessly focused on delivering superior, consistent returns for our investors across all market conditions." },
  { icon: "fa-globe", title: "Global Reach", desc: "With a presence in 40+ locations, we bring local market knowledge combined with coordinated global oversight." },
  { icon: "fa-users", title: "Investor First", desc: "Our entire structure is built around you. Every decision we make is guided by the best interests of our investors." },
];

export default function About() {
  return (
    <div className="wf-root">
      <NavBar />
      <PageHero
        title="About Welthflow"
        subtitle="A pioneer of commission-free investing — built for the sole benefit of our investors."
        bg="https://images.unsplash.com/photo-1504384308090-c894fdcc538d?w=1600&q=70"
      />

      {/* Mission */}
      <section style={{ padding: "80px 0" }}>
        <div className="container">
          <div className="row align-items-center g-5">
            <div className="col-lg-6">
              <div className="wf-section-badge mb-3">OUR STORY</div>
              <h2 className="wf-h2">Who We Are</h2>
              <p className="wf-body mb-3">
                {BRAND} was founded on a simple but revolutionary idea — that an investment company should be run for the sole benefit of its investors. We removed outside owners and outside interests from the equation, creating a structure where our success can only be measured by your success.
              </p>
              <p className="wf-body mb-3">
                Today, we manage over $562.9 billion in assets on behalf of governments, pension funds, insurers, companies, charities, foundations and individual investors across 80 countries. With employees in more than 40 locations worldwide, our operations extend across global financial capitals and important regional centres.
              </p>
              <p className="wf-body">
                We combine deep knowledge of local markets with the power of coordinated global oversight to drive better investment outcomes for every single client.
              </p>
            </div>
            <div className="col-lg-6">
              <div className="wf-about-img-grid">
                <img src="https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=600&q=70" alt="trading" className="wf-about-img wf-about-img-lg" />
                <img src="https://images.unsplash.com/photo-1590283603385-17ffb3a7f29f?w=400&q=70" alt="charts" className="wf-about-img wf-about-img-sm" />
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Stats */}
      <div style={{ background: "#01123c", padding: "60px 0" }}>
        <div className="container">
          <div className="row g-4 text-center">
            {[["$563Bn+", "Assets Under Management"], ["80M+", "Active Investors Worldwide"], ["80+", "Countries Served"], ["40+", "Global Office Locations"]].map(([val, label], i) => (
              <div key={i} className="col-6 col-md-3">
                <h2 style={{ fontFamily: "'Lora', serif", fontSize: 42, fontWeight: 700, color: "#3eda99", marginBottom: 8 }}>{val}</h2>
                <p style={{ color: "rgba(255,255,255,0.7)", fontSize: 14, textTransform: "uppercase", letterSpacing: 1 }}>{label}</p>
              </div>
            ))}
          </div>
        </div>
      </div>

      {/* Values */}
      <section style={{ padding: "80px 0", background: "#f8fafc" }}>
        <div className="container">
          <div className="text-center mb-5">
            <div className="wf-section-badge mb-2">WHAT DRIVES US</div>
            <h2 className="wf-h2">Our Core Values</h2>
          </div>
          <div className="row g-4">
            {values.map((v, i) => (
              <div key={i} className="col-md-6 col-lg-3">
                <div className="wf-value-card">
                  <div className="wf-value-icon"><i className={`fas ${v.icon}`}></i></div>
                  <h4 className="wf-value-title">{v.title}</h4>
                  <p className="wf-value-desc">{v.desc}</p>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Team */}
      <section style={{ padding: "80px 0" }}>
        <div className="container">
          <div className="text-center mb-5">
            <div className="wf-section-badge mb-2">LEADERSHIP</div>
            <h2 className="wf-h2">Meet Our Team</h2>
            <p className="wf-body" style={{ color: "#718096", maxWidth: 560, margin: "0 auto" }}>
              Our team of seasoned professionals brings decades of combined experience across global financial markets.
            </p>
          </div>
          <div className="row g-4 justify-content-center">
            {team.map((m, i) => (
              <div key={i} className="col-md-6 col-lg-3">
                <div className="wf-team-card">
                  <img src={m.img} alt={m.name} className="wf-team-img" />
                  <h4 className="wf-team-name">{m.name}</h4>
                  <p className="wf-team-role">{m.role}</p>
                  <p className="wf-team-bio">{m.bio}</p>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* CTA */}
      <div style={{ background: "#062f6d", padding: "70px 0" }}>
        <div className="container text-center">
          <h2 style={{ fontFamily: "'Lora', serif", color: "#fff", fontSize: 30, marginBottom: 16 }}>Ready to grow your wealth?</h2>
          <p style={{ color: "rgba(255,255,255,0.8)", maxWidth: 520, margin: "0 auto 32px", fontSize: 15 }}>
            Join over 80 million investors who trust Welthflow with their financial future.
          </p>
          <a href="/welthflow/portal/register.php" className="wf-btn-primary">Open Your Account Today</a>
        </div>
      </div>

      <Footer />
    </div>
  );
}
