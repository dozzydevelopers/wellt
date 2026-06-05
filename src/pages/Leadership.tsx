export default function Leadership() {
  const team = [
    {
      name: "Richard Calloway",
      title: "Chief Executive Officer",
      bio: "Richard brings over 22 years of experience in global asset management and investment banking. Formerly at Goldman Sachs and Barclays Capital, he founded Welthflow with the vision of democratizing high-yield investments for global retail investors. Under his leadership, the firm has grown to manage over $563 billion in assets across 80 countries.",
      img: "https://images.unsplash.com/photo-1560250097-0b93528c311a?w=400&q=80",
      linkedin: "#",
      credentials: ["MBA Harvard Business School", "CFA Charterholder", "FCA Licensed"],
      tag: "CEO",
    },
    {
      name: "Sophia Hartmann",
      title: "Chief Investment Officer",
      bio: "Sophia has spent 18 years managing multi-asset portfolios for sovereign wealth funds and institutional investors in Frankfurt and Singapore. She leads Welthflow's investment committee and oversees all algorithmic trading strategies, cryptocurrency portfolio management, and real estate fund allocations.",
      img: "https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=400&q=80",
      linkedin: "#",
      credentials: ["MSc Finance, LSE", "CMT Certified", "CAIA Member"],
      tag: "CIO",
    },
    {
      name: "Marcus O. Brennan",
      title: "Chief Technology Officer",
      bio: "Marcus architected Welthflow's enterprise-grade trading infrastructure, capable of processing over 2 million transactions per second. With a background at Microsoft Research and two fintech unicorns, he leads a 200-person engineering team building the next generation of investment platform technology.",
      img: "https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&q=80",
      linkedin: "#",
      credentials: ["MSc Computer Science, MIT", "AWS Certified Architect", "Blockchain Expert"],
      tag: "CTO",
    },
    {
      name: "Amara Osei-Bonsu",
      title: "Head of Global Operations",
      bio: "Amara manages Welthflow's operations across 4 continents with expertise in regulatory compliance, KYC/AML frameworks, and cross-border capital flows. She previously served as Head of Compliance at JPMorgan's EMEA division and holds dual licenses from the FCA and SEC.",
      img: "https://images.unsplash.com/photo-1551836022-deb4988cc6c0?w=400&q=80",
      linkedin: "#",
      credentials: ["LLM International Finance", "FCA/SEC Licensed", "CAMS Certified"],
      tag: "COO",
    },
    {
      name: "James Whitfield",
      title: "Head of Quantitative Research",
      bio: "James leads Welthflow's quant team, developing proprietary AI-driven models that underpin our PAMM/MAM forex strategies. A former D.E. Shaw researcher, he holds a PhD in Financial Mathematics from Oxford and has published over 30 papers on algorithmic asset allocation.",
      img: "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&q=80",
      linkedin: "#",
      credentials: ["PhD Financial Mathematics, Oxford", "FRM Certified", "IEEE Member"],
      tag: "Research",
    },
    {
      name: "Elena Vasquez",
      title: "Director of Client Relations",
      bio: "Elena oversees Welthflow's global client services, ensuring every investor receives a premium, personalised experience. With 14 years in private wealth management at UBS and Credit Suisse, she has guided high-net-worth clients through complex multi-asset strategies across emerging and developed markets.",
      img: "https://images.unsplash.com/photo-1580489944761-15a19d654956?w=400&q=80",
      linkedin: "#",
      credentials: ["BA Economics, Wharton", "CISI Diploma", "IAQ Certified"],
      tag: "Client",
    },
  ];

  return (
    <section className="wf-leadership" id="leadership">
      <div className="wf-leadership-bg-deco" />
      <div className="container">
        <div className="wf-leadership-header">
          <div className="wf-section-badge" style={{ background: "rgba(201,162,39,0.12)", color: "#c9a227", borderColor: "rgba(201,162,39,0.25)" }}>
            LEADERSHIP TEAM
          </div>
          <h2 className="wf-h2 c-w mt-3">
            Meet the People Behind <span style={{ color: "#c9a227" }}>Welthflow</span>
          </h2>
          <p className="c-w mx-auto" style={{ opacity: 0.65, maxWidth: 640, fontSize: 15, marginTop: 10, marginBottom: 50 }}>
            Our leadership team combines decades of institutional finance, technology, and regulatory expertise to deliver world-class investment results for our clients.
          </p>
        </div>
        <div className="wf-team-grid">
          {team.map((member, i) => (
            <div key={i} className="wf-team-card">
              <div className="wf-team-img-wrap">
                <img src={member.img} alt={member.name} className="wf-team-img" />
                <div className="wf-team-tag">{member.tag}</div>
              </div>
              <div className="wf-team-body">
                <h3 className="wf-team-name">{member.name}</h3>
                <div className="wf-team-title">{member.title}</div>
                <p className="wf-team-bio">{member.bio}</p>
                <div className="wf-team-credentials">
                  {member.credentials.map((c, j) => (
                    <span key={j} className="wf-cred-badge">{c}</span>
                  ))}
                </div>
                <a href={member.linkedin} className="wf-team-linkedin">
                  <i className="fab fa-linkedin-in" style={{ marginRight: 6 }}></i> LinkedIn
                </a>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}
