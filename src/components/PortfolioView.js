"use client";

import React from "react";
import dynamic from "next/dynamic";

const Chart = dynamic(() => import("react-apexcharts"), { ssr: false });

export default function PortfolioView({ portfolio, stocks, cash, onOpenStockModal, onNavigate }) {
  // Calculate summary metrics
  let totalInvested = 0;
  let currentEquity = 0;

  portfolio.forEach((hold) => {
    const liveStock = stocks.find((s) => s.ticker === hold.ticker);
    totalInvested += hold.shares * hold.avgPrice;
    currentEquity += hold.shares * (liveStock ? liveStock.price : hold.avgPrice);
  });

  const totalPortfolioVal = currentEquity + cash;
  const profitLoss = currentEquity - totalInvested;
  const profitLossPct = totalInvested > 0 ? (profitLoss / totalInvested) * 100 : 0;
  const plPrefix = profitLoss >= 0 ? "+" : "";

  // Prepare Donut Chart data
  const chartSeries = portfolio.map((h) => {
    const liveStock = stocks.find((s) => s.ticker === h.ticker);
    const price = liveStock ? liveStock.price : h.avgPrice;
    return h.shares * price;
  });

  const chartLabels = portfolio.map((h) => h.ticker);
  const totalValue = chartSeries.reduce((a, b) => a + b, 0);

  const chartOptions = {
    labels: chartLabels,
    chart: {
      type: "donut",
      background: "transparent",
      foreColor: "#9ca3af"
    },
    colors: ["#6366f1", "#10b981", "#f59e0b", "#3b82f6", "#ec4899", "#8b5cf6", "#06b6d4"],
    stroke: {
      show: true,
      colors: ["#0f1622"],
      width: 3
    },
    plotOptions: {
      pie: {
        donut: {
          size: "70%",
          labels: {
            show: true,
            name: {
              show: true,
              fontSize: "12px",
              fontFamily: "Plus Jakarta Sans",
              color: "#9ca3af"
            },
            value: {
              show: true,
              fontSize: "18px",
              fontFamily: "Outfit",
              fontWeight: 700,
              color: "#fff",
              formatter: (val) => {
                const pct = totalValue > 0 ? ((val / totalValue) * 100).toFixed(1) : 0;
                return pct + "%";
              }
            },
            total: {
              show: true,
              label: "มูลค่าพอร์ต",
              color: "#9ca3af",
              formatter: () => "฿" + totalValue.toLocaleString(undefined, { maximumFractionDigits: 0 })
            }
          }
        }
      }
    },
    dataLabels: { enabled: false },
    legend: {
      show: true,
      position: "bottom",
      fontSize: "11px",
      labels: { colors: "#9ca3af" }
    },
    tooltip: {
      theme: "dark",
      y: {
        formatter: (v) => "฿" + v.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })
      }
    }
  };

  return (
    <section className="page-view active" id="portfolio-view">
      <div className="page-title-section">
        <h2 className="page-title">พอร์ตการลงทุนจำลองของคุณ</h2>
        <p className="page-subtitle">ติดตามประสิทธิภาพการลงทุน สรุปกำไร/ขาดทุน และสัดส่วนหุ้นที่ซื้อขาย</p>
      </div>

      {/* Metric Mini Cards */}
      <div className="portfolio-header-grid">
        <div className="mini-card">
          <span className="mini-card-title">มูลค่ารวมทั้งพอร์ต (Total Asset)</span>
          <span className="mini-card-val" id="portfolio-total-equity">
            ฿{totalPortfolioVal.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
          </span>
        </div>
        <div className="mini-card">
          <span className="mini-card-title">เงินสดที่คงเหลือ (Cash Balance)</span>
          <span className="mini-card-val" id="portfolio-cash" style={{ color: "var(--text-primary)" }}>
            ฿{cash.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
          </span>
        </div>
        <div className="mini-card">
          <span className="mini-card-title">มูลค่ารวมของหุ้น (Stock Value)</span>
          <span className="mini-card-val" id="total-portfolio-value" style={{ color: "var(--color-brand)" }}>
            ฿{currentEquity.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
          </span>
        </div>
        <div className={`mini-card-val ${profitLoss >= 0 ? "up" : "down"}`} style={{ display: "flex", flexDirection: "column", padding: "16px", background: "var(--bg-card)", border: "1px solid var(--border-color)", borderRadius: "16px", justifyContent: "center" }}>
          <span className="mini-card-title" style={{ fontSize: "11px", color: "var(--text-secondary)", textTransform: "uppercase", fontWeight: 700, marginBottom: "8px" }}>
            กำไร/ขาดทุนสะสม (P&L)
          </span>
          <span style={{ fontSize: "20px", fontFamily: "var(--font-title)", fontWeight: 700 }}>
            {plPrefix}฿{profitLoss.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })} ({plPrefix}{profitLossPct.toFixed(2)}%)
          </span>
        </div>
      </div>

      <div className="portfolio-layout">
        {/* Table of holdings */}
        <div className="glass-card portfolio-list-card">
          <h3 className="section-title" style={{ marginBottom: 0 }}>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
              <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"></path>
              <path d="M12 6v6l4 2"></path>
            </svg>
            รายการหุ้นในครอบครอง
          </h3>

          <div style={{ overflowX: "auto" }}>
            <table className="portfolio-table">
              <thead>
                <tr>
                  <th>หุ้น</th>
                  <th>จำนวนหุ้น</th>
                  <th>ราคาเฉลี่ย</th>
                  <th>ราคาปัจจุบัน</th>
                  <th>มูลค่าล่าสุด</th>
                  <th>กำไร/ขาดทุน</th>
                </tr>
              </thead>
              <tbody id="portfolio-table-body">
                {portfolio.length === 0 ? (
                  <tr>
                    <td colSpan="6" style={{ textAlign: "center", color: "var(--text-muted)", padding: "40px 0" }}>
                      <div className="empty-state">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" strokeLinecap="round" strokeLinejoin="round" className="lucide lucide-wallet">
                          <path d="M21 12V7H5a2 2 0 0 1 0-4h14v4"></path>
                          <path d="M3 5v14a2 2 0 0 0 2 2h16v-5"></path>
                          <path d="M18 12a2 2 0 0 0 0 4h4v-4Z"></path>
                        </svg>
                        <div className="empty-state-title">ไม่มีหุ้นในพอร์ตโฟลิโอของคุณ</div>
                        <div className="empty-state-desc">คุณสามารถเริ่มต้นด้วยการจำลองซื้อหุ้นจากการแนะนำ</div>
                        <button className="btn btn-primary" onClick={() => onNavigate("recommendations")} style={{ marginTop: "8px" }}>
                          ดูคำแนะนำหุ้น
                        </button>
                      </div>
                    </td>
                  </tr>
                ) : (
                  portfolio.map((hold) => {
                    const stock = stocks.find((s) => s.ticker === hold.ticker);
                    if (!stock) return null;

                    const currentValue = hold.shares * stock.price;
                    const holdingProfitLoss = currentValue - hold.shares * hold.avgPrice;
                    const holdingProfitLossPct = ((stock.price - hold.avgPrice) / hold.avgPrice) * 100;
                    const itemPlPrefix = holdingProfitLoss >= 0 ? "+" : "";

                    return (
                      <tr
                        key={hold.ticker}
                        style={{ cursor: "pointer" }}
                        onClick={() => onOpenStockModal(hold.ticker)}
                      >
                        <td>
                          <div className="portfolio-asset-info">
                            <div className="stock-icon" style={{ width: "34px", height: "34px", fontSize: "11px" }}>
                              {stock.ticker}
                            </div>
                            <div style={{ display: "flex", flexDirection: "column" }}>
                              <span style={{ fontWeight: 700 }}>{stock.ticker}</span>
                              <span style={{ fontSize: "10px", color: "var(--text-muted)" }}>{stock.name}</span>
                            </div>
                          </div>
                        </td>
                        <td style={{ fontFamily: "var(--font-title)", fontWeight: 600 }}>{hold.shares.toLocaleString()}</td>
                        <td style={{ fontFamily: "var(--font-title)", fontWeight: 600 }}>฿{hold.avgPrice.toFixed(2)}</td>
                        <td style={{ fontFamily: "var(--font-title)", fontWeight: 600 }} className="stock-price-val">
                          ฿{stock.price.toFixed(2)}
                        </td>
                        <td style={{ fontFamily: "var(--font-title)", fontWeight: 600 }}>
                          ฿{currentValue.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                        </td>
                        <td style={{ fontFamily: "var(--font-title)", fontWeight: 700 }} className={`stock-pct-val ${holdingProfitLoss >= 0 ? "up" : "down"}`}>
                          {itemPlPrefix}฿{holdingProfitLoss.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })} ({itemPlPrefix}{holdingProfitLossPct.toFixed(1)}%)
                        </td>
                      </tr>
                    );
                  })
                )}
              </tbody>
            </table>
          </div>
        </div>

        {/* Allocation donut */}
        <div className="glass-card" style={{ display: "flex", flexDirection: "column", justifyContent: "center", alignItems: "center" }}>
          <h3 className="section-title" style={{ width: "100%", textAlign: "left", marginBottom: "12px" }}>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" strokeLinecap="round" strokeLinejoin="round">
              <circle cx="12" cy="12" r="10"></circle>
              <path d="m18 8-6 6-4-2"></path>
            </svg>
            สัดส่วนหุ้นที่ลงทุน
          </h3>
          <div id="portfolio-donut-container" style={{ width: "100%", minHeight: "250px", display: "flex", justifyContent: "center", alignItems: "center" }}>
            {portfolio.length === 0 ? (
              <div className="empty-state">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" className="lucide lucide-pie-chart">
                  <path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path>
                  <path d="M22 12A10 10 0 0 0 12 2v10z"></path>
                </svg>
                <div className="empty-state-title">ไม่มีข้อมูลสัดส่วนหุ้น</div>
                <div className="empty-state-desc">สัดส่วนพอร์ตจะปรากฏขึ้นเมื่อคุณซื้อหุ้น</div>
              </div>
            ) : (
              <Chart options={chartOptions} series={chartSeries} type="donut" width="100%" height={250} />
            )}
          </div>
        </div>
      </div>
    </section>
  );
}
