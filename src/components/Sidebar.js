"use client";

import React from "react";

export default function Sidebar({ activeView, setActiveView, currentUser, onOpenAuth }) {
  const isGuest = currentUser === "guest";
  const userDisplayName = isGuest ? "นักลงทุนจำลอง" : currentUser;
  const userDisplayRole = isGuest ? "Demo Account" : "สมาชิก (Member)";
  const avatarChar = (isGuest ? "U" : currentUser[0] || "U").toUpperCase();

  const navItems = [
    {
      id: "dashboard",
      label: "แดชบอร์ดภาพรวม",
      icon: (
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
          <rect x="3" y="3" width="7" height="7"></rect>
          <rect x="14" y="3" width="7" height="7"></rect>
          <rect x="14" y="14" width="7" height="7"></rect>
          <rect x="3" y="14" width="7" height="7"></rect>
        </svg>
      ),
    },
    {
      id: "recommendations",
      label: "แนะนำหุ้นน่าลงทุน",
      icon: (
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
          <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
        </svg>
      ),
    },
    {
      id: "profiler",
      label: "ประเมินระดับความเสี่ยง",
      icon: (
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
          <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
        </svg>
      ),
    },
    {
      id: "portfolio",
      label: "พอร์ตจำลอง (Portfolio)",
      icon: (
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
          <path d="M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16"></path>
          <path d="M2 21h20M10 8h4M10 12h4M10 16h4"></path>
        </svg>
      ),
    },
    {
      id: "watchlist",
      label: "หุ้นที่กำลังเฝ้าดู",
      icon: (
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
          <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"></path>
        </svg>
      ),
    },
  ];

  return (
    <aside className="sidebar">
      <div className="logo-container">
        <div className="logo-icon">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round">
            <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline>
            <polyline points="16 7 22 7 22 13"></polyline>
          </svg>
        </div>
        <span className="logo-text">SmartInvest</span>
      </div>

      <nav style={{ flexGrow: 1 }}>
        <ul className="nav-links">
          {navItems.map((item) => (
            <li key={item.id}>
              <a
                className={`nav-item ${activeView === item.id ? "active" : ""}`}
                onClick={() => setActiveView(item.id)}
                style={{ cursor: "pointer" }}
              >
                {item.icon}
                {item.label}
              </a>
            </li>
          ))}
        </ul>
      </nav>

      <div className="sidebar-footer">
        <div
          className="user-profile"
          onClick={onOpenAuth}
          style={{ cursor: "pointer", transition: "background 0.2s", padding: "8px", borderRadius: "14px" }}
        >
          <div className="avatar">{avatarChar}</div>
          <div className="user-info">
            <span className="username">{userDisplayName}</span>
            <span className="user-role">{userDisplayRole}</span>
          </div>
        </div>
      </div>
    </aside>
  );
}
