"use client";

import React from "react";

export default function WatchlistView({ watchlist, stocks, onOpenStockModal, onNavigate }) {
  // watchlist can be a Set or an Array
  const watchlistArray = Array.from(watchlist);

  return (
    <section className="page-view active" id="watchlist-view">
      <div className="page-title-section">
        <h2 className="page-title">หุ้นที่คุณเฝ้าติดตาม</h2>
        <p className="page-subtitle">รายการหุ้นที่คุณบันทึกไว้ในความสนใจเพื่อการติดตามราคาอย่างใกล้ชิด</p>
      </div>

      <div style={{ display: "grid", gridTemplateColumns: "repeat(3, 1fr)", gap: "20px" }} id="watchlist-list">
        {watchlistArray.length === 0 ? (
          <div className="empty-state" style={{ gridColumn: "span 3", padding: "60px 0" }}>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className="lucide lucide-heart">
              <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"></path>
            </svg>
            <div className="empty-state-title">รายการเฝ้าดูว่างเปล่า</div>
            <div className="empty-state-desc">กดรูปหัวใจในหน้ารายละเอียดหุ้นเพื่อบันทึกหุ้นมาไว้ที่นี่</div>
            <button className="btn btn-primary" onClick={() => onNavigate("recommendations")} style={{ marginTop: "8px" }}>
              สำรวจหุ้นน่าสนใจ
            </button>
          </div>
        ) : (
          watchlistArray.map((ticker) => {
            const stock = stocks.find((s) => s.ticker === ticker);
            if (!stock) return null;

            const isUp = stock.change >= 0;
            const changePrefix = isUp ? "+" : "";
            const categoryLabel =
              stock.category === "dividend"
                ? "ปันผลสูง"
                : stock.category === "growth"
                ? "เติบโตดี"
                : "เทค/ความเสี่ยงสูง";

            return (
              <div
                key={stock.ticker}
                className="glass-card"
                onClick={() => onOpenStockModal(stock.ticker)}
                style={{ cursor: "pointer" }}
              >
                <div style={{ display: "flex", justifyContent: "space-between", alignItems: "flex-start" }}>
                  <div style={{ display: "flex", gap: "12px", alignItems: "center" }}>
                    <div className="stock-icon">{stock.ticker}</div>
                    <div style={{ display: "flex", flexDirection: "column", gap: "4px" }}>
                      <span className="stock-symbol" style={{ fontSize: "16px" }}>{stock.ticker}</span>
                      <span className={`stock-badge badge-${stock.category}`}>{categoryLabel}</span>
                    </div>
                  </div>
                  <div style={{ textAlign: "right" }}>
                    <div className="stock-price-val" style={{ fontSize: "16px", fontWeight: 700 }}>
                      ฿{stock.price.toFixed(2)}
                    </div>
                    <div className={`stock-pct-val ${isUp ? "up" : "down"}`} style={{ fontSize: "12px", fontWeight: 600 }}>
                      {changePrefix}{stock.change.toFixed(2)}%
                    </div>
                  </div>
                </div>
                <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: "8px", borderTop: "1px solid var(--border-color)", marginTop: "16px", paddingTop: "12px", fontSize: "11px", color: "var(--text-muted)" }}>
                  <div>P/E: <strong style={{ color: "var(--text-primary)" }}>{stock.pe}x</strong></div>
                  <div>Consensus: <strong style={{ color: "var(--color-brand)" }}>{stock.consensus}</strong></div>
                </div>
              </div>
            );
          })
        )}
      </div>
    </section>
  );
}
