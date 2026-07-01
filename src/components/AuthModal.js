"use client";

import React, { useState } from "react";

export default function AuthModal({ isOpen, onClose, currentUser, cash, riskProfile, onLogin, onRegister, onLogout }) {
  const [activeTab, setActiveTab] = useState("login"); // "login" | "register"
  const [username, setUsername] = useState("");
  const [password, setPassword] = useState("");
  const [startCash, setStartCash] = useState("1000000");

  if (!isOpen) return null;

  const isGuest = currentUser === "guest";

  const handleSubmit = (e) => {
    e.preventDefault();
    if (!username.trim() || !password.trim()) return;

    if (activeTab === "login") {
      onLogin(username.trim(), password.trim());
    } else {
      onRegister(username.trim(), password.trim(), parseFloat(startCash));
    }
  };

  const riskMapping = {
    conservative: "ความเสี่ยงต่ำ (Conservative)",
    moderate: "ความเสี่ยงปานกลาง (Moderate)",
    aggressive: "ความเสี่ยงสูง (Aggressive)",
  };

  const handleClose = () => {
    // Reset forms on close
    setUsername("");
    setPassword("");
    onClose();
  };

  return (
    <div className="modal-overlay active" id="auth-modal">
      <div className="modal-content" style={{ maxWidth: "460px", height: "auto", maxHeight: "85vh" }}>
        {/* Modal Header */}
        <div className="modal-header" style={{ padding: "20px 24px" }}>
          <h3 className="modal-stock-ticker" id="auth-modal-title" style={{ fontSize: "20px" }}>
            {isGuest ? "จัดการบัญชีผู้ใช้งาน" : "โปรไฟล์ผู้ใช้งาน"}
          </h3>
          <button className="modal-close-btn" id="auth-modal-close" onClick={handleClose}>
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </button>
        </div>

        {/* Modal Body */}
        <div className="modal-body" style={{ padding: "24px", overflowY: "auto" }}>
          {isGuest ? (
            /* Guest Section: Login/Register Forms */
            <div id="auth-guest-section">
              <div className="trade-tabs" style={{ marginBottom: "20px" }} id="auth-tabs">
                <button
                  className={`trade-tab ${activeTab === "login" ? "active buy" : ""}`}
                  onClick={() => setActiveTab("login")}
                  style={{ padding: "8px" }}
                >
                  เข้าสู่ระบบ
                </button>
                <button
                  className={`trade-tab ${activeTab === "register" ? "active sell" : ""}`}
                  onClick={() => setActiveTab("register")}
                  style={{ padding: "8px" }}
                >
                  สมัครสมาชิก
                </button>
              </div>

              <form id="auth-form" onSubmit={handleSubmit} className="trade-form">
                <div className="trade-input-group">
                  <span className="trade-input-label">ชื่อผู้ใช้งาน (Username)</span>
                  <div className="trade-input-wrapper">
                    <input
                      type="text"
                      value={username}
                      onChange={(e) => setUsername(e.target.value)}
                      placeholder="ระบุชื่อผู้ใช้งาน..."
                      required
                    />
                  </div>
                </div>

                <div className="trade-input-group" style={{ marginTop: "12px" }}>
                  <span className="trade-input-label">รหัสผ่าน (Password)</span>
                  <div className="trade-input-wrapper">
                    <input
                      type="password"
                      value={password}
                      onChange={(e) => setPassword(e.target.value)}
                      placeholder="ระบุรหัสผ่าน..."
                      required
                    />
                  </div>
                </div>

                {/* Shown only on register tab */}
                {activeTab === "register" && (
                  <div className="trade-input-group" id="register-cash-group" style={{ marginTop: "12px" }}>
                    <span className="trade-input-label">วงเงินลงทุนเริ่มต้นจำลอง</span>
                    <div className="trade-input-wrapper">
                      <select
                        value={startCash}
                        onChange={(e) => setStartCash(e.target.value)}
                        style={{
                          width: "100%",
                          background: "rgba(0, 0, 0, 0.2)",
                          border: "1px solid var(--border-color)",
                          borderRadius: "12px",
                          padding: "12px 18px",
                          color: "#fff",
                          fontFamily: "var(--font-title)",
                          fontWeight: 700,
                          outline: "none",
                        }}
                      >
                        <option value="500000" style={{ background: "#090e18" }}>฿500,000</option>
                        <option value="1000000" style={{ background: "#090e18" }}>฿1,000,000</option>
                        <option value="5000000" style={{ background: "#090e18" }}>฿5,000,000</option>
                        <option value="10000000" style={{ background: "#090e18" }}>฿10,000,000</option>
                      </select>
                    </div>
                  </div>
                )}

                <button
                  type="submit"
                  className={`btn ${activeTab === "login" ? "btn-primary" : "btn-success"}`}
                  id="auth-submit-btn"
                  style={{ width: "100%", marginTop: "20px" }}
                >
                  {activeTab === "login" ? "เข้าสู่ระบบ (Log In)" : "สมัครสมาชิก (Register)"}
                </button>
              </form>
            </div>
          ) : (
            /* User Profile Details (Logged in) */
            <div id="auth-user-section" style={{ display: "flex", flexDirection: "column", gap: "20px" }}>
              <div style={{ display: "flex", alignParagraph: "center", alignItems: "center", gap: "16px", background: "rgba(255,255,255,0.015)", border: "1px solid var(--border-color)", padding: "16px", borderRadius: "16px" }}>
                <div className="avatar" id="profile-avatar" style={{ width: "50px", height: "50px", fontSize: "18px" }}>
                  {(currentUser[0] || "U").toUpperCase()}
                </div>
                <div style={{ display: "flex", flexDirection: "column", gap: "4px" }}>
                  <h4 id="profile-username" style={{ fontFamily: "var(--font-title)", fontSize: "18px", fontWeight: 700, color: "#fff" }}>
                    {currentUser}
                  </h4>
                  <span className="stock-badge badge-growth" id="profile-role" style={{ fontSize: "10px" }}>
                    Standard Account
                  </span>
                </div>
              </div>

              <div className="trade-info-summary" style={{ background: "rgba(0, 0, 0, 0.2)", padding: "16px", borderRadius: "12px", fontSize: "13px", border: "1px solid var(--border-color)" }}>
                <div className="trade-summary-row" style={{ display: "flex", justifyContent: "space-between", color: "var(--text-secondary)", marginBottom: "8px" }}>
                  <span>เงินสดจำลองในบัญชี:</span>
                  <strong id="profile-cash" style={{ color: "var(--color-success)", fontFamily: "var(--font-title)", fontSize: "14px" }}>
                    ฿{cash.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                  </strong>
                </div>
                <div className="trade-summary-row" style={{ display: "flex", justifyContent: "space-between", color: "var(--text-secondary)" }}>
                  <span>ระดับความเสี่ยง:</span>
                  <strong id="profile-risk" style={{ color: "var(--color-brand)", fontSize: "13px" }}>
                    {riskProfile ? riskMapping[riskProfile] : "ยังไม่ประเมิน"}
                  </strong>
                </div>
              </div>

              <button
                className="btn btn-secondary"
                id="auth-logout-btn"
                onClick={onLogout}
                style={{ width: "100%", color: "var(--color-danger)", borderColor: "rgba(239, 68, 68, 0.2)", background: "rgba(239, 68, 68, 0.05)", justifyContent: "center" }}
              >
                ออกจากระบบ (Log Out)
              </button>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
