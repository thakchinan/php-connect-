"use client";

import React, { useState, useEffect } from "react";
import dynamic from "next/dynamic";

const Chart = dynamic(() => import("react-apexcharts"), { ssr: false });

export default function StockModal({
  isOpen,
  onClose,
  stock,
  cash,
  portfolio,
  watchlist,
  onTrade,
  onWatchlistToggle,
}) {
  const [tradeType, setTradeType] = useState("buy"); // "buy" | "sell"
  const [quantity, setQuantity] = useState(100);
  const [chartType, setChartType] = useState("area"); // "area" | "candle"
  const [duration, setDuration] = useState("3M"); // "1W" | "1M" | "3M"

  // Reset states when stock changes
  useEffect(() => {
    setTradeType("buy");
    setQuantity(100);
    setChartType("area");
    setDuration("3M");
  }, [stock]);

  if (!isOpen || !stock) return null;

  // Watchlist status
  const isBookmarked = watchlist.has(stock.ticker);

  // Shares owned
  const holding = portfolio.find((h) => h.ticker === stock.ticker);
  const sharesOwned = holding ? holding.shares : 0;
  const avgPrice = holding ? holding.avgPrice : 0;

  // Estimated values
  const totalCost = quantity * stock.price;
  const isAffordable = cash >= totalCost;
  const hasEnoughShares = sharesOwned >= quantity;

  // Filter historical points
  let historyPoints = [...(stock.history || [])];
  let candleData = [...(stock.candles || [])];

  if (duration === "1W") {
    historyPoints = historyPoints.slice(-7);
    candleData = candleData.slice(-7);
  } else if (duration === "1M") {
    historyPoints = historyPoints.slice(-30);
    candleData = candleData.slice(-30);
  }

  // Calculate chart boundaries
  const prices = historyPoints.map((p) => p.y);
  const minVal = prices.length > 0 ? Math.min(...prices) * 0.98 : 0;
  const maxVal = prices.length > 0 ? Math.max(...prices) * 1.02 : 100;

  const isUp =
    historyPoints.length > 1
      ? historyPoints[historyPoints.length - 1].y >= historyPoints[0].y
      : true;
  const lineAccent = isUp ? "#10b981" : "#ef4444";

  // Chart configuration: Historical
  const historySeries =
    chartType === "candle"
      ? [
          {
            name: stock.ticker,
            data: candleData,
          },
        ]
      : [
          {
            name: "Price (THB)",
            data: historyPoints,
          },
        ];

  const historyOptions = {
    chart: {
      type: chartType === "candle" ? "candlestick" : "area",
      toolbar: { show: false },
      background: "transparent",
      foreColor: "#9ca3af",
    },
    colors: chartType === "candle" ? ["#10b981", "#ef4444"] : [lineAccent],
    grid: {
      borderColor: "rgba(255, 255, 255, 0.05)",
      strokeDashArray: 4,
      xaxis: { lines: { show: false } },
      yaxis: { lines: { show: true } },
    },
    stroke:
      chartType === "candle"
        ? {}
        : {
            curve: "smooth",
            width: 2.5,
          },
    fill: {
      type: chartType === "candle" ? "solid" : "gradient",
      gradient: {
        shadeIntensity: 1,
        opacityFrom: 0.45,
        opacityTo: 0.05,
        stops: [0, 95, 100],
      },
    },
    xaxis: {
      type: "datetime",
      labels: {
        style: {
          colors: "#6b7280",
          fontSize: "10px",
        },
      },
      axisBorder: { show: false },
      axisTicks: { show: false },
    },
    yaxis: {
      min: minVal,
      max: maxVal,
      labels: {
        formatter: (v) => v.toFixed(2) + " ฿",
        style: {
          colors: "#6b7280",
          fontSize: "10px",
        },
      },
    },
    tooltip: {
      theme: "dark",
      x: { format: "dd MMM yyyy" },
      y: {
        formatter: (v) => v.toFixed(2) + " THB",
      },
    },
  };

  // Chart configuration: Financials (Revenue vs Profit Bar Chart)
  const years = stock.financials?.years || [];
  const revenue = stock.financials?.revenue || [];
  const netProfit = stock.financials?.netProfit || [];

  const revUnit = stock.ticker === "PTT" || stock.ticker === "DELTA" ? "ล้านล้านบาท" : "พันล้านบาท";
  const profitUnit = "พันล้านบาท";

  const financialSeries = [
    {
      name: `รายได้รวม (${revUnit})`,
      data: revenue,
    },
    {
      name: `กำไรสุทธิ (${profitUnit})`,
      data: netProfit.map((v) =>
        stock.ticker === "PTT" || stock.ticker === "DELTA" ? v / 1000 : v
      ),
    },
  ];

  const financialOptions = {
    chart: {
      type: "bar",
      toolbar: { show: false },
      background: "transparent",
      foreColor: "#9ca3af",
    },
    plotOptions: {
      bar: {
        horizontal: false,
        columnWidth: "55%",
        borderRadius: 4,
      },
    },
    colors: ["#6366f1", "#10b981"],
    dataLabels: { enabled: false },
    stroke: {
      show: true,
      width: 2,
      colors: ["transparent"],
    },
    xaxis: {
      categories: years,
      labels: {
        style: { colors: "#6b7280", fontSize: "11px" },
      },
    },
    yaxis: {
      labels: {
        style: { colors: "#6b7280", fontSize: "11px" },
      },
    },
    fill: { opacity: 0.85 },
    grid: {
      borderColor: "rgba(255, 255, 255, 0.05)",
      strokeDashArray: 4,
    },
    legend: {
      show: true,
      position: "top",
      fontSize: "11px",
      labels: { colors: "#9ca3af" },
    },
    tooltip: {
      theme: "dark",
      y: {
        formatter: (val, { seriesIndex }) => {
          return val.toFixed(2) + (seriesIndex === 0 ? ` ${revUnit}` : ` ${profitUnit}`);
        },
      },
    },
  };

  const handleTradeAction = () => {
    if (quantity <= 0) return;
    onTrade(stock.ticker, tradeType, quantity);
  };

  const changePrefix = stock.change >= 0 ? "+" : "";
  const catLabel =
    stock.category === "dividend"
      ? "ปันผลดี"
      : stock.category === "growth"
      ? "เติบโตสูง"
      : "ความเสี่ยงสูง";

  return (
    <div className="modal-overlay active" id="stock-modal">
      <div className="modal-content">
        {/* Modal Header */}
        <div className="modal-header">
          <div className="modal-header-left">
            <div className="stock-icon" style={{ width: "50px", height: "50px", fontSize: "16px" }}>
              {stock.ticker}
            </div>
            <div className="modal-title-desc">
              <div style={{ display: "flex", alignItems: "center", gap: "12px" }}>
                <span className="modal-stock-ticker">{stock.ticker}</span>
                <span className={`stock-badge badge-${stock.category}`}>{catLabel}</span>
              </div>
              <span className="modal-stock-name">{stock.name}</span>
            </div>
          </div>

          <div style={{ display: "flex", alignItems: "center", gap: "12px" }}>
            <button
              className={`watchlist-btn ${isBookmarked ? "active" : ""}`}
              onClick={() => onWatchlistToggle(stock.ticker)}
              title={isBookmarked ? "ลบออกจากรายการเฝ้าดู" : "เพิ่มเข้าเฝ้าดู"}
              style={{ color: isBookmarked ? "var(--color-danger)" : "" }}
            >
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill={isBookmarked ? "currentColor" : "none"} stroke="currentColor" strokeWidth="2">
                <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"></path>
              </svg>
            </button>
            <button className="modal-close-btn" onClick={onClose}>
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
              </svg>
            </button>
          </div>
        </div>

        {/* Modal Body */}
        <div className="modal-body">
          <div className="modal-grid">
            {/* Left Column (Chart and description) */}
            <div className="modal-chart-section">
              <div style={{ display: "flex", justifyContent: "space-between", alignItems: "center" }}>
                <div style={{ display: "flex", alignItems: "baseline", gap: "8px" }}>
                  <span style={{ fontFamily: "var(--font-title)", fontSize: "26px", fontWeight: 700 }}>
                    ฿{stock.price.toFixed(2)}
                  </span>
                  <span className={stock.change >= 0 ? "up" : "down"} style={{ fontSize: "14px", fontWeight: 600 }}>
                    {changePrefix}{stock.change.toFixed(2)}%
                  </span>
                </div>
                <div style={{ display: "flex", gap: "12px" }}>
                  {/* Chart type toggle */}
                  <div className="chart-period-selectors" style={{ background: "rgba(0,0,0,0.15)", padding: "2px", borderRadius: "6px", border: "1px solid var(--border-color)" }}>
                    <button
                      className={`chart-period-btn ${chartType === "area" ? "active" : ""}`}
                      onClick={() => setChartType("area")}
                      style={{ padding: "2px 8px" }}
                    >
                      Area
                    </button>
                    <button
                      className={`chart-period-btn ${chartType === "candle" ? "active" : ""}`}
                      onClick={() => setChartType("candle")}
                      style={{ padding: "2px 8px" }}
                    >
                      Candles
                    </button>
                  </div>
                  {/* Chart period toggle */}
                  <div className="chart-period-selectors">
                    {["1W", "1M", "3M"].map((p) => (
                      <button
                        key={p}
                        className={`chart-period-btn ${duration === p ? "active" : ""}`}
                        onClick={() => setDuration(p)}
                      >
                        {p}
                      </button>
                    ))}
                  </div>
                </div>
              </div>

              {/* Price Chart wrapper */}
              <div style={{ height: "250px", background: "rgba(0,0,0,0.1)", borderRadius: "12px", border: "1px solid var(--border-color)", overflow: "hidden" }}>
                <Chart
                  options={historyOptions}
                  series={historySeries}
                  type={chartType === "candle" ? "candlestick" : "area"}
                  height="100%"
                  width="100%"
                />
              </div>

              {/* Stats Grid */}
              <div className="stats-grid">
                <div className="stat-item">
                  <span className="stat-label">P/E Ratio</span>
                  <span className="stat-val">{stock.pe}x</span>
                </div>
                <div className="stat-item">
                  <span className="stat-label">Div. Yield</span>
                  <span className="stat-val">{stock.dividendYield}%</span>
                </div>
                <div className="stat-item">
                  <span className="stat-label">Market Cap</span>
                  <span className="stat-val">{stock.marketCap}</span>
                </div>
                <div className="stat-item">
                  <span className="stat-label">Beta Index</span>
                  <span className="stat-val">{stock.beta}</span>
                </div>
                <div className="stat-item" style={{ gridColumn: "span 2" }}>
                  <span className="stat-label">Analyst Consensus</span>
                  <span className="stat-val" style={{ color: "var(--color-brand)", fontWeight: 700 }}>
                    {stock.consensus}
                  </span>
                </div>
              </div>

              {/* Description */}
              <div className="glass-card" style={{ padding: "16px" }}>
                <h4 style={{ fontSize: "12px", fontWeight: 700, textTransform: "uppercase", color: "var(--text-muted)", marginBottom: "6px" }}>
                  เกี่ยวกับบริษัท
                </h4>
                <p style={{ fontSize: "12px", color: "var(--text-secondary)", lineHeight: 1.6 }}>
                  {stock.description}
                </p>
              </div>
            </div>

            {/* Right Column (Financials chart and trade execute) */}
            <div style={{ display: "flex", flexDirection: "column", gap: "20px" }}>
              {/* Financial Bar Chart */}
              <div className="glass-card" style={{ padding: "16px" }}>
                <h4 style={{ fontSize: "12px", fontWeight: 700, color: "var(--text-muted)", marginBottom: "12px", textTransform: "uppercase" }}>
                  ผลการดำเนินงานทางการเงิน
                </h4>
                <Chart options={financialOptions} series={financialSeries} type="bar" height={240} width="100%" />
              </div>

              {/* Trade execute block */}
              <div className="glass-card trade-card">
                <div className="trade-tabs">
                  <button
                    className={`trade-tab ${tradeType === "buy" ? "active buy" : ""}`}
                    onClick={() => setTradeType("buy")}
                  >
                    ซื้อหุ้น (Buy)
                  </button>
                  <button
                    className={`trade-tab ${tradeType === "sell" ? "active sell" : ""}`}
                    onClick={() => setTradeType("sell")}
                  >
                    ขายหุ้น (Sell)
                  </button>
                </div>

                <div className="trade-form">
                  <div className="trade-input-group">
                    <span className="trade-input-label">จำนวนหุ้นที่ต้องการ</span>
                    <div className="trade-input-wrapper">
                      <input
                        type="number"
                        value={quantity}
                        onChange={(e) => setQuantity(Math.max(1, parseInt(e.target.value) || 0))}
                        min="1"
                        step="100"
                      />
                      <span className="trade-input-suffix">หุ้น</span>
                    </div>
                  </div>

                  <div className="trade-info-summary">
                    {tradeType === "buy" ? (
                      <>
                        <div className="trade-summary-row">
                          <span>เงินสดที่มีอยู่:</span>
                          <strong style={{ color: isAffordable ? "var(--color-success)" : "var(--color-danger)" }}>
                            ฿{cash.toLocaleString(undefined, { maximumFractionDigits: 2 })}
                          </strong>
                        </div>
                        <div className="trade-summary-row">
                          <span>จำนวนที่ครอบครอง:</span>
                          <strong>{sharesOwned.toLocaleString()} หุ้น</strong>
                        </div>
                      </>
                    ) : (
                      <>
                        <div className="trade-summary-row">
                          <span>จำนวนที่ครอบครอง:</span>
                          <strong style={{ color: hasEnoughShares ? "var(--color-success)" : "var(--color-danger)" }}>
                            {sharesOwned.toLocaleString()} หุ้น
                          </strong>
                        </div>
                        <div className="trade-summary-row">
                          <span>เงินสดที่จะได้รับ:</span>
                          <strong style={{ color: "var(--color-success)" }}>
                            ฿{totalCost.toLocaleString(undefined, { maximumFractionDigits: 2 })}
                          </strong>
                        </div>
                      </>
                    )}
                    <div className="trade-summary-row total">
                      <span>มูลค่ารวมประมาณ:</span>
                      <strong>
                        ฿{totalCost.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                      </strong>
                    </div>
                  </div>

                  <button
                    className={`btn ${tradeType === "buy" ? "btn-success" : "btn-danger"}`}
                    onClick={handleTradeAction}
                    style={{ width: "100%" }}
                    disabled={tradeType === "buy" ? !isAffordable : !hasEnoughShares}
                  >
                    {tradeType === "buy" ? "ส่งคำสั่งซื้อหุ้น (Buy)" : "ส่งคำสั่งขายหุ้น (Sell)"}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
