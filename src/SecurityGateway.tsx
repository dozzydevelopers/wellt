import { useState, useEffect, useRef, useCallback } from "react";

interface SecurityGatewayProps {
  onVerified: () => void;
}

function generateCode() {
  return String(Math.floor(1000 + Math.random() * 9000));
}

export default function SecurityGateway({ onVerified }: SecurityGatewayProps) {
  const [code, setCode] = useState(generateCode);
  const [inputs, setInputs] = useState(["", "", "", ""]);
  const [attempts, setAttempts] = useState(3);
  const [timeLeft, setTimeLeft] = useState(60);
  const [status, setStatus] = useState<"idle" | "success" | "error">("idle");
  const [fading, setFading] = useState(false);
  const [entered, setEntered] = useState(false);
  const inputRefs = [
    useRef<HTMLInputElement>(null),
    useRef<HTMLInputElement>(null),
    useRef<HTMLInputElement>(null),
    useRef<HTMLInputElement>(null),
  ];

  useEffect(() => {
    document.body.style.overflow = "hidden";
    setTimeout(() => inputRefs[0].current?.focus(), 400);
    return () => { document.body.style.overflow = ""; };
  }, []);

  useEffect(() => {
    if (timeLeft <= 0) {
      newCode();
      return;
    }
    const t = setTimeout(() => setTimeLeft(t => t - 1), 1000);
    return () => clearTimeout(t);
  }, [timeLeft]);

  const newCode = useCallback(() => {
    setCode(generateCode());
    setInputs(["", "", "", ""]);
    setTimeLeft(60);
    setStatus("idle");
    setTimeout(() => inputRefs[0].current?.focus(), 100);
  }, []);

  const handleInput = (i: number, val: string) => {
    if (!/^\d?$/.test(val)) return;
    const next = [...inputs];
    next[i] = val;
    setInputs(next);
    setStatus("idle");
    if (val && i < 3) inputRefs[i + 1].current?.focus();
  };

  const handleKeyDown = (i: number, e: React.KeyboardEvent) => {
    if (e.key === "Backspace" && !inputs[i] && i > 0) {
      inputRefs[i - 1].current?.focus();
    }
  };

  const verify = () => {
    const entered = inputs.join("");
    if (entered.length < 4) { setStatus("error"); return; }
    if (entered === code) {
      setStatus("success");
      setEntered(true);
      setTimeout(() => {
        setFading(true);
        setTimeout(() => {
          document.body.style.overflow = "";
          onVerified();
        }, 700);
      }, 900);
    } else {
      setStatus("error");
      const rem = attempts - 1;
      setAttempts(rem);
      if (rem <= 0) {
        setTimeout(() => newCode(), 1500);
        setAttempts(3);
      }
      setTimeout(() => { setInputs(["", "", "", ""]); inputRefs[0].current?.focus(); }, 800);
    }
  };

  const handlePaste = (e: React.ClipboardEvent) => {
    const pasted = e.clipboardData.getData("text").replace(/\D/g, "").slice(0, 4);
    if (pasted.length === 4) {
      setInputs(pasted.split(""));
      inputRefs[3].current?.focus();
      e.preventDefault();
    }
  };

  const progress = (timeLeft / 60) * 100;

  return (
    <div
      className="sgw-root"
      style={{
        opacity: fading ? 0 : 1,
        transition: "opacity 0.7s ease",
        pointerEvents: fading ? "none" : "all",
      }}
    >
      {/* Animated bg blobs */}
      <div className="sgw-blob sgw-blob1" />
      <div className="sgw-blob sgw-blob2" />
      <div className="sgw-blob sgw-blob3" />

      <div className="sgw-card">
        {/* Gold shimmer top border */}
        <div className="sgw-top-border" />

        {/* Lock icon */}
        <div className={`sgw-icon-wrap ${status === "success" ? "success" : status === "error" ? "error" : ""}`}>
          {status === "success" ? (
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none">
              <path d="M20 6L9 17l-5-5" stroke="#3eda99" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round"/>
            </svg>
          ) : (
            <svg width="34" height="34" viewBox="0 0 24 24" fill="none">
              <rect x="3" y="11" width="18" height="11" rx="2" stroke={status === "error" ? "#ff4d6d" : "#c9a227"} strokeWidth="2"/>
              <path d="M7 11V7a5 5 0 0 1 10 0v4" stroke={status === "error" ? "#ff4d6d" : "#c9a227"} strokeWidth="2" strokeLinecap="round"/>
              <circle cx="12" cy="16" r="1.5" fill={status === "error" ? "#ff4d6d" : "#c9a227"}/>
            </svg>
          )}
        </div>

        {/* Brand badge */}
        <div className="sgw-brand">
          <span className="sgw-brand-dot" />
          WELTHFLOW SECURE ACCESS
        </div>

        <h2 className="sgw-title">Security Verification</h2>
        <p className="sgw-subtitle">Enter the 4-digit code below to access the platform</p>

        {/* Code display */}
        <div className="sgw-code-box">
          <div className="sgw-code-label">ACCESS CODE</div>
          <div className="sgw-code-digits">
            {code.split("").map((d, i) => (
              <span key={i} className="sgw-code-digit">{d}</span>
            ))}
          </div>
          <div className="sgw-code-refresh">
            <span className="sgw-pulse" /> AUTO-REFRESHES IN {timeLeft}s
          </div>
        </div>

        {/* Progress bar */}
        <div className="sgw-progress-track">
          <div
            className="sgw-progress-bar"
            style={{
              width: `${progress}%`,
              background: timeLeft > 20
                ? "linear-gradient(90deg, #c9a227, #f0d060)"
                : "linear-gradient(90deg, #ff4d6d, #ff8a65)",
              transition: "width 1s linear, background 0.5s",
            }}
          />
        </div>

        {/* Input row */}
        <div className="sgw-inputs-row" onPaste={handlePaste}>
          {inputs.map((val, i) => (
            <input
              key={i}
              ref={inputRefs[i]}
              type="tel"
              inputMode="numeric"
              maxLength={1}
              value={val}
              onChange={e => handleInput(i, e.target.value)}
              onKeyDown={e => handleKeyDown(i, e)}
              className={`sgw-input ${status === "error" ? "err" : ""} ${status === "success" ? "ok" : ""} ${val ? "filled" : ""}`}
              disabled={entered}
            />
          ))}
        </div>

        {/* Status message */}
        <div className={`sgw-msg ${status === "success" ? "sgw-msg-ok" : status === "error" ? "sgw-msg-err" : "sgw-msg-hint"}`}>
          {status === "success" && <><span className="sgw-msg-icon">✓</span> Verification successful — loading your dashboard...</>}
          {status === "error" && inputs.join("").length === 4 && <><span className="sgw-msg-icon">✗</span> Invalid code. Please try again.</>}
          {status === "error" && inputs.join("").length < 4 && <><span className="sgw-msg-icon">!</span> Please enter all 4 digits.</>}
          {status === "idle" && <><span className="sgw-msg-icon sgw-green-dot" />Enter the code shown above to proceed</>}
        </div>

        {/* Buttons */}
        <div className="sgw-btn-row">
          <button className="sgw-btn-secondary" onClick={newCode} disabled={entered}>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" style={{ marginRight: 7 }}>
              <path d="M1 4v6h6M23 20v-6h-6" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
              <path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4-4.64 4.36A9 9 0 0 1 3.51 15" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
            </svg>
            New Code
          </button>
          <button className="sgw-btn-primary" onClick={verify} disabled={entered}>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" style={{ marginRight: 7 }}>
              <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
            </svg>
            Verify Access
          </button>
        </div>

        {/* Footer */}
        <div className="sgw-footer-row">
          <div className="sgw-attempts">
            <span className="sgw-attempts-dots">
              {[0,1,2].map(i => (
                <span key={i} className={`sgw-dot-attempt ${i < attempts ? "active" : ""}`} />
              ))}
            </span>
            Attempts remaining: <b>{attempts}</b>
          </div>
          <div className="sgw-security-badges">
            <span className="sgw-badge"><span style={{ color: "#3eda99" }}>✓</span> SSL</span>
            <span className="sgw-badge"><span style={{ color: "#3eda99" }}>✓</span> 256-bit</span>
          </div>
        </div>
      </div>
    </div>
  );
}
