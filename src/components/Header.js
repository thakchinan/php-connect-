"use client";

import React from "react";

export default function Header({ cash, searchQuery, setSearchQuery }) {
  return (
    <header className="header">
      <div className="search-box">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
          <circle cx="11" cy="11" r="8"></circle>
          <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
        <input
          type="text"
          value={searchQuery}
          onChange={(e) => setSearchQuery(e.target.value)}
          placeholder="ค้นหาหุ้นด้วยชื่อย่อ หรือชื่อบริษัท..."
        />
      </div>

      <div className="header-actions">
        <div className="market-status">
          <div className="status-dot"></div>
          <span>จำลองตลาดเปิดทำการ</span>
        </div>
        <div className="balance-card">
          <span>วงเงินสดเหลือ:</span>
          <strong>฿{cash.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</strong>
        </div>
      </div>
    </header>
  );
}
