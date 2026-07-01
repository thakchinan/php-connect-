"use client";

import React from "react";

export default function DashboardView({ stocks, indices, news, onOpenStockModal }) {
  // Sort stocks by performance change descending for top gainers
  const topGainers = [...stocks]
    .sort((a, b) => b.change - a.change)
    .slice(0, 4);

  return (
    <section className="page-view active" id="dashboard-view">
      <div className="page-title-section">
        <h2 className="page-title">แดชบอร์ดภาพรวมการลงทุน</h2>
        <p className="page-subtitle">ติดตามราคาหุ้นแบบเรียลไทม์ ดัชนีสำคัญ และสรุปความเคลื่อนไหวตลาด</p>
      </div>

      {/* Ticker Horizontal Prices */}
      <div className="ticker-container" id="ticker-container">
        {stocks.map((stock) => {
          const isUp = stock.change >= 0;
          const changePrefix = isUp ? "+" : "";
          return (
            <div
              key={stock.ticker}
              className="ticker-item"
              onClick={() => onOpenStockModal(stock.ticker)}
              style={{ cursor: "pointer" }}
            >
              <span className="ticker-name">{stock.ticker}</span>
              <span className="ticker-val">฿{stock.price.toFixed(2)}</span>
              <span className={`ticker-change ${isUp ? "up" : "down"}`}>
                {changePrefix}{stock.change.toFixed(2)}%
              </span>
            </div>
          );
        })}
      </div>

      <div className="dashboard-grid">
        {/* Left Column */}
        <div className="dashboard-left">
          <div className="glass-card" style={{ paddingBottom: "12px" }}>
            <h3 className="section-title">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
              </svg>
              หุ้นแนะนำที่มีผลตอบแทนยอดเยี่ยมช่วงนี้
            </h3>
            <div className="stock-list" id="top-gainers-list">
              {topGainers.map((stock) => {
                const isUp = stock.change >= 0;
                const changePrefix = isUp ? "+" : "";
                const categoryLabel =
                  stock.category === "dividend"
                    ? "ปันผลสูง"
                    : stock.category === "growth"
                    ? "เติบโตสูง"
                    : "เสี่ยงสูง/เก็งกำไร";

                return (
                  <div
                    key={stock.ticker}
                    className="stock-row"
                    onClick={() => onOpenStockModal(stock.ticker)}
                    style={{ cursor: "pointer" }}
                  >
                    <div className="stock-info">
                      <div className="stock-icon">{stock.ticker}</div>
                      <div className="stock-meta">
                        <span className="stock-symbol">{stock.ticker}</span>
                        <span className="stock-fullname">{stock.name}</span>
                      </div>
                      <span className={`stock-badge badge-${stock.category}`}>
                        {categoryLabel}
                      </span>
                    </div>
                    <div className="stock-values">
                      <span className="stock-price-val">฿{stock.price.toFixed(2)}</span>
                      <span className={`stock-pct-val ${isUp ? "up" : "down"}`}>
                        {changePrefix}{stock.change.toFixed(2)}%
                      </span>
                    </div>
                  </div>
                );
              })}
            </div>
          </div>
        </div>

        {/* Right Column */}
        <div className="dashboard-right">
          {/* Market Indices */}
          <div className="glass-card" style={{ display: "flex", flexDirection: "column", gap: "16px" }}>
            <h3 className="section-title" style={{ marginBottom: 0 }}>
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                <line x1="18" y1="20" x2="18" y2="10"></line>
                <line x1="12" y1="20" x2="12" y2="4"></line>
                <line x1="6" y1="20" x2="6" y2="14"></line>
              </svg>
              ดัชนีสำคัญทั่วโลก
            </h3>
            <div style={{ display: "flex", flexDirection: "column", gap: "12px" }} id="indices-container">
              {indices.map((idx) => {
                const isUp = idx.isPositive;
                return (
                  <div key={idx.name} className="mini-card">
                    <span className="mini-card-title">{idx.name}</span>
                    <span className="mini-card-val">{idx.value}</span>
                    <span className={`stock-pct-val ${isUp ? "up" : "down"}`} style={{ fontSize: "12px", fontWeight: 600 }}>
                      {idx.change} ({idx.changeValue >= 0 ? "+" : ""}{idx.changeValue})
                    </span>
                  </div>
                );
              })}
            </div>
          </div>

          {/* Financial News */}
          <div className="glass-card">
            <h3 className="section-title">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                <polyline points="22 6 12 13 2 6"></polyline>
              </svg>
              ข่าวสารเศรษฐกิจและการเงินล่าสุด
            </h3>
            <div className="news-list" id="news-container">
              {news.map((item, idx) => (
                <div key={idx} className="news-card">
                  <div className="news-cat">{item.category}</div>
                  <div className="news-title">{item.title}</div>
                  <div className="news-meta">
                    <span>{item.source}</span>
                    <span>{item.time}</span>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}
