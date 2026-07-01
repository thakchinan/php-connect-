"use client";

import React, { useState } from "react";

export default function RecommendationsView({ stocks, searchQuery, onOpenStockModal }) {
  const [activeCategory, setActiveCategory] = useState("all");

  const categories = [
    { id: "all", label: "ทั้งหมด (All)" },
    { id: "dividend", label: "หุ้นปันผลสูง (High Dividend)" },
    { id: "growth", label: "หุ้นเติบโตโดดเด่น (Growth Stock)" },
    { id: "highrisk", label: "อิเล็กทรอนิกส์/ความเสี่ยงสูง (Tech & Volatile)" },
  ];

  const filterText = searchQuery.toLowerCase().trim();

  const filteredStocks = stocks.filter((stock) => {
    const matchesCategory = activeCategory === "all" || stock.category === activeCategory;
    const matchesSearch =
      stock.ticker.toLowerCase().includes(filterText) ||
      stock.name.toLowerCase().includes(filterText);
    return matchesCategory && matchesSearch;
  });

  return (
    <section className="page-view active" id="recommendations-view">
      <div className="page-title-section">
        <h2 className="page-title">วิเคราะห์หุ้นแนะนำเพื่อการลงทุน</h2>
        <p className="page-subtitle">กรองและค้นหาหุ้นแบ่งตามประเภทกลยุทธ์การลงทุน ค้นหาหุ้นที่ใช่สำหรับคุณ</p>
      </div>

      <div className="filter-tabs" id="recom-filters">
        {categories.map((cat) => (
          <button
            key={cat.id}
            className={`filter-tab ${activeCategory === cat.id ? "active" : ""}`}
            onClick={() => setActiveCategory(cat.id)}
          >
            {cat.label}
          </button>
        ))}
      </div>

      <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: "20px" }} id="recommendations-list">
        {filteredStocks.length === 0 ? (
          <div className="empty-state" style={{ gridColumn: "span 2" }}>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className="lucide lucide-search">
              <circle cx="11" cy="11" r="8"></circle>
              <path d="m21 21-4.3-4.3"></path>
            </svg>
            <div className="empty-state-title">ไม่พบรายชื่อหุ้น</div>
            <div className="empty-state-desc">ลองค้นหาด้วยคำอื่น หรือเลือกหมวดหมู่อื่น</div>
          </div>
        ) : (
          filteredStocks.map((stock) => {
            const changePrefix = stock.change >= 0 ? "+" : "";
            const categoryLabel =
              stock.category === "dividend"
                ? "ปันผลสูง (Dividend)"
                : stock.category === "growth"
                ? "เติบโตดี (Growth)"
                : "เทค/ความเสี่ยงสูง (Tech/Risk)";

            return (
              <div
                key={stock.ticker}
                className="glass-card"
                onClick={() => onOpenStockModal(stock.ticker)}
                style={{ cursor: "pointer" }}
              >
                <div style={{ display: "flex", justifyContent: "space-between", alignItems: "flex-start", marginBottom: "16px" }}>
                  <div style={{ display: "flex", gap: "12px", alignItems: "center" }}>
                    <div className="stock-icon">{stock.ticker}</div>
                    <div style={{ display: "flex", flexDirection: "column", gap: "4px" }}>
                      <span className="stock-symbol" style={{ fontSize: "16px" }}>{stock.ticker}</span>
                      <span className={`stock-badge badge-${stock.category}`}>{categoryLabel}</span>
                    </div>
                  </div>
                  <div style={{ textAlign: "right" }}>
                    <div className="stock-price-val" style={{ fontSize: "18px" }}>฿{stock.price.toFixed(2)}</div>
                    <div className={`stock-pct-val ${stock.change >= 0 ? "up" : "down"}`} style={{ fontSize: "13px", fontWeight: 600 }}>
                      {changePrefix}{stock.change.toFixed(2)}%
                    </div>
                  </div>
                </div>
                <p style={{
                  fontSize: "12px",
                  color: "var(--text-secondary)",
                  lineHeight: 1.5,
                  height: "48px",
                  overflow: "hidden",
                  textOverflow: "ellipsis",
                  display: "-webkit-box",
                  WebkitLineClamp: 3,
                  WebkitBoxOrient: "vertical",
                  marginBottom: "16px"
                }}>
                  {stock.description}
                </p>
                <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: "8px", borderTop: "1px solid var(--border-color)", paddingTop: "12px", fontSize: "11px", color: "var(--text-muted)" }}>
                  <div>P/E: <strong style={{ color: "var(--text-primary)" }}>{stock.pe}x</strong></div>
                  <div>Div Yield: <strong style={{ color: "var(--text-primary)" }}>{stock.dividendYield}%</strong></div>
                  <div>Consensus: <strong style={{ color: "var(--color-brand)" }}>{stock.consensus}</strong></div>
                  <div>Risk: <strong style={{ color: stock.risk === "Low" ? "var(--color-success)" : stock.risk === "Medium" ? "var(--color-warning)" : "var(--color-danger)" }}>{stock.risk}</strong></div>
                </div>
              </div>
            );
          })
        )}
      </div>
    </section>
  );
}
