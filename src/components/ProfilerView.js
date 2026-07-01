"use client";

import React, { useState } from "react";
import dynamic from "next/dynamic";

const Chart = dynamic(() => import("react-apexcharts"), { ssr: false });

const QUIZ_QUESTIONS = [
  {
    id: 1,
    question: "ช่วงอายุของคุณคือช่วงใด?",
    options: [
      { text: "มากกว่า 55 ปีขึ้นไป (เน้นรักษาเงินต้น)", score: 1 },
      { text: "35 - 55 ปี (เน้นการเติบโตแบบสมดุล)", score: 2 },
      { text: "น้อยกว่า 35 ปี (เน้นเติบโตระยะยาว รับความผันผวนสูงได้)", score: 3 }
    ]
  },
  {
    id: 2,
    question: "เป้าหมายหลักในการลงทุนของคุณคืออะไร?",
    options: [
      { text: "เน้นกระแสเงินสดปันผลที่สม่ำเสมอและความปลอดภัยของเงินต้น", score: 1 },
      { text: "สร้างการเติบโตปานกลางควบคู่กับความมั่นคงของเงินทุน", score: 2 },
      { text: "สร้างผลตอบแทนสูงสุดในระยะยาว ยอมรับการขาดทุนชั่วคราวได้สูง", score: 3 }
    ]
  },
  {
    id: 3,
    question: "คุณมีประสบการณ์ลงทุนในหุ้น กองทุนรวม หรือสินทรัพย์ดิจิทัลมากน้อยเพียงใด?",
    options: [
      { text: "ไม่มีประสบการณ์เลย หรือน้อยมาก (เน้นเงินฝากหรือพันธบัตร)", score: 1 },
      { text: "พอมีความเข้าใจ (เข้าใจงบการเงินเบื้องต้น เคยซื้อกองทุน/หุ้นบ้าง)", score: 2 },
      { text: "มีความเชี่ยวชาญสูง (ซื้อขายบ่อย เข้าใจการวิเคราะห์กราฟเทคนิค หรือตราสารอนุพันธ์)", score: 3 }
    ]
  },
  {
    id: 4,
    question: "หากเงินลงทุนของคุณลดลง 20% ภายใน 1 เดือนเนื่องจากตลาดปรับฐาน คุณจะจัดการอย่างไร?",
    options: [
      { text: "ขายล้างพอร์ตทันทีเพื่อป้องกันไม่ให้ขาดทุนมากไปกว่านี้", score: 1 },
      { text: "ไม่ทำอะไร รอตลาดฟื้นตัว หรือปรึกษาผู้แนะนำการลงทุน", score: 2 },
      { text: "ทยอยซื้อหุ้นเพิ่มเพื่อเฉลี่ยต้นทุนในราคาที่ถูกลง (DCA / Buy the dip)", score: 3 }
    ]
  },
  {
    id: 5,
    question: "คุณวางแผนจะแบ่งเงินออมส่วนตัวมาลงทุนในสินทรัพย์เสี่ยงสูง (เช่น หุ้น) ประมาณเท่าใด?",
    options: [
      { text: "น้อยกว่า 10% (ที่เหลือฝากธนาคาร ซื้อทองคำ หรือพันธบัตรรัฐบาล)", score: 1 },
      { text: "10% - 50% (กระจายในหลายสินทรัพย์ความเสี่ยงปานกลาง)", score: 2 },
      { text: "มากกว่า 50% (ต้องการทุ่มเทเงินลงทุนเพื่อให้ได้ผลตอบแทนเติบโตสูงสุด)", score: 3 }
    ]
  }
];

const PORTFOLIO_ALLOCATIONS = {
  conservative: {
    title: "Conservative Portfolio (พอร์ตความเสี่ยงต่ำ - เน้นความปลอดภัย)",
    description: "เหมาะสำหรับนักลงทุนที่ต้องการปกป้องเงินต้น หลีกเลี่ยงความผันผวนของตลาด และรับผลตอบแทนสม่ำเสมอในรูปแบบของดอกเบี้ยและปันผล",
    allocation: [
      { asset: "เงินฝากดอกเบี้ยสูง / ตราสารหนี้ระยะสั้น", percentage: 30 },
      { asset: "พันธบัตรรัฐบาล / หุ้นกู้คุณภาพสูง", percentage: 45 },
      { asset: "หุ้นพื้นฐานดีปันผลสูง (Blue-chip & Dividend)", percentage: 20 },
      { asset: "ทองคำ / สินทรัพย์ป้องกันความเสี่ยง", percentage: 5 }
    ],
    recommendedStocks: ["PTT", "ADVANC", "BDMS", "SCC"]
  },
  moderate: {
    title: "Moderate Portfolio (พอร์ตความเสี่ยงปานกลาง - เติบโตสมดุล)",
    description: "ผสมผสานระหว่างการเติบโตของเงินทุนในระยะยาวและการกระจายความเสี่ยงอย่างเหมาะสม ยอมรับความผันผวนได้ปานกลางเพื่อสร้างโอกาสรับผลตอบแทนที่สูงขึ้น",
    allocation: [
      { asset: "เงินสด / ตราสารหนี้ระยะสั้น", percentage: 15 },
      { asset: "กองทุนรวมตราสารหนี้", percentage: 25 },
      { asset: "หุ้นเติบโตปานกลาง (Growth Stocks)", percentage: 35 },
      { asset: "หุ้นปันผลสูง (Dividend Stocks)", percentage: 20 },
      { asset: "ทองคำ / สินทรัพย์ทางเลือกอื่น", percentage: 5 }
    ],
    recommendedStocks: ["CPALL", "KBANK", "AOT", "GULF", "PTT", "ADVANC"]
  },
  aggressive: {
    title: "Aggressive Portfolio (พอร์ตความเสี่ยงสูง - เน้นการเติบโตเชิงรุก)",
    description: "มุ่งเน้นการเติบโตของเงินทุนสูงสุด ยอมรับความเสี่ยงการสูญเสียเงินต้นและความผันผวนสูงได้ เพื่อผลตอบแทนแบบทวีคูณในระยะยาว",
    allocation: [
      { asset: "เงินสดสำรองสภาพคล่อง", percentage: 5 },
      { asset: "หุ้นกู้คุณภาพสูง", percentage: 10 },
      { asset: "หุ้นเติบโตและเทคโนโลยี (Growth & Tech)", percentage: 55 },
      { asset: "หุ้นเสี่ยงสูง / อุปกรณ์อิเล็กทรอนิกส์ส่งออก", percentage: 20 },
      { asset: "สินทรัพย์ดิจิทัล / สินทรัพย์ทางเลือก", percentage: 10 }
    ],
    recommendedStocks: ["DELTA", "HANA", "COM7", "GULF", "CPALL", "KBANK"]
  }
};

export default function ProfilerView({ riskProfile, stocks, onOpenStockModal, onSaveRiskProfile, onClearRiskProfile }) {
  const [currentIdx, setCurrentIdx] = useState(0);
  const [answers, setAnswers] = useState([]);

  const handleOptionSelect = (optionIdx) => {
    const newAnswers = [...answers];
    newAnswers[currentIdx] = optionIdx;
    setAnswers(newAnswers);
  };

  const handleNext = () => {
    if (currentIdx < QUIZ_QUESTIONS.length - 1) {
      setCurrentIdx(currentIdx + 1);
    } else {
      // Calculate score and save profile
      let totalScore = 0;
      answers.forEach((optIdx, qIdx) => {
        totalScore += QUIZ_QUESTIONS[qIdx].options[optIdx].score;
      });

      let profileResult = "moderate";
      if (totalScore <= 8) {
        profileResult = "conservative";
      } else if (totalScore <= 12) {
        profileResult = "moderate";
      } else {
        profileResult = "aggressive";
      }

      onSaveRiskProfile(profileResult);
    }
  };

  const handleBack = () => {
    if (currentIdx > 0) {
      setCurrentIdx(currentIdx - 1);
    }
  };

  const handleReset = () => {
    setAnswers([]);
    setCurrentIdx(0);
    onClearRiskProfile();
  };

  // Render Result view if user already has a risk profile
  if (riskProfile) {
    const data = PORTFOLIO_ALLOCATIONS[riskProfile] || PORTFOLIO_ALLOCATIONS.moderate;
    const riskBadgeClass =
      riskProfile === "conservative"
        ? "badge-dividend"
        : riskProfile === "moderate"
        ? "badge-growth"
        : "badge-highrisk";
    const riskLabel =
      riskProfile === "conservative"
        ? "ความเสี่ยงต่ำ (Conservative)"
        : riskProfile === "moderate"
        ? "ความเสี่ยงปานกลาง (Moderate)"
        : "ความเสี่ยงสูง (Aggressive)";

    const chartOptions = {
      labels: data.allocation.map((a) => a.asset),
      chart: {
        type: "donut",
        background: "transparent",
        foreColor: "#f3f4f6"
      },
      colors: ["#6366f1", "#10b981", "#f59e0b", "#3b82f6", "#ec4899"],
      stroke: {
        show: true,
        colors: ["#0f1622"],
        width: 3
      },
      plotOptions: {
        pie: {
          donut: {
            size: "72%",
            labels: {
              show: true,
              name: {
                show: true,
                fontSize: "14px",
                fontFamily: "Plus Jakarta Sans",
                color: "#9ca3af"
              },
              value: {
                show: true,
                fontSize: "22px",
                fontFamily: "Outfit",
                fontWeight: 700,
                color: "#fff",
                formatter: (val) => val + "%"
              },
              total: {
                show: true,
                label: "สัดส่วน",
                color: "#9ca3af",
                formatter: () => "100%"
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
        labels: { colors: "#9ca3af" },
        markers: { radius: 12 }
      },
      tooltip: {
        theme: "dark",
        y: {
          formatter: (v) => v + "%"
        }
      }
    };

    const chartSeries = data.allocation.map((a) => a.percentage);

    return (
      <section className="page-view active" id="profiler-view">
        <div className="page-title-section">
          <h2 className="page-title">ประเมินความเสี่ยงเพื่อจัดพอร์ตที่เหมาะสม</h2>
          <p className="page-subtitle">ทำแบบทดสอบเพื่อค้นหาความต้องการการลงทุน พร้อมคำนวณสัดส่วนสินทรัพย์ที่แนะนำ (Asset Allocation)</p>
        </div>

        <div className="glass-card">
          <div style={{ display: "flex", justifyContent: "space-between", alignItems: "center", borderBottom: "1px solid var(--border-color)", paddingBottom: "16px", marginBottom: "20px" }}>
            <h3 className="section-title" style={{ marginBottom: 0 }}>
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className="lucide lucide-shield">
                <path d="M20 13c0 5-3.5 7.5-7.66 9.7a1 1 0 0 1-.68 0C7.5 20.5 4 18 4 13V6a1 1 0 0 1 .76-.97l8-2a1 1 0 0 1 .48 0l8 2A1 1 0 0 1 20 6z"></path>
              </svg>
              ผลลัพธ์การประเมินระดับความเสี่ยง
            </h3>
            <button className="btn btn-secondary" style={{ padding: "6px 12px", fontSize: "11px" }} onClick={handleReset}>
              ทำแบบประเมินใหม่
            </button>
          </div>

          <div className="result-container">
            <div className="result-pie-card">
              <Chart options={chartOptions} series={chartSeries} type="donut" height={280} />
            </div>
            <div className="result-details">
              <span className={`result-badge ${riskBadgeClass}`}>{riskLabel}</span>
              <h4 className="result-title">{data.title}</h4>
              <p className="result-desc">{data.description}</p>

              <div className="recom-assets-list">
                <span style={{ fontSize: "11px", color: "var(--text-muted)", fontWeight: 700, textTransform: "uppercase" }}>สัดส่วนที่แนะนำ:</span>
                {data.allocation.map((a, index) => (
                  <div key={index} className="asset-bullet">
                    <span>{a.asset}</span>
                    <strong style={{ color: "var(--text-primary)" }}>{a.percentage}%</strong>
                  </div>
                ))}
              </div>
            </div>
          </div>

          <div style={{ marginTop: "24px", borderTop: "1px solid var(--border-color)", paddingTop: "20px" }}>
            <h4 style={{ fontSize: "14px", fontWeight: 600, marginBottom: "12px", color: "var(--text-primary)" }}>หุ้นที่แนะนำสำหรับคุณ (สอดคล้องกับพอร์ตโฟลิโอ)</h4>
            <div className="stock-list" style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: "12px" }}>
              {data.recommendedStocks.map((ticker) => {
                const stock = stocks.find((s) => s.ticker === ticker);
                if (!stock) return null;
                const isUp = stock.change >= 0;
                const prefix = isUp ? "+" : "";
                const categoryLabel =
                  stock.category === "dividend"
                    ? "ปันผลดี"
                    : stock.category === "growth"
                    ? "เติบโตสูง"
                    : "เสี่ยงสูง";

                return (
                  <div
                    key={stock.ticker}
                    className="stock-row"
                    style={{ margin: 0, cursor: "pointer" }}
                    onClick={() => onOpenStockModal(stock.ticker)}
                  >
                    <div className="stock-info">
                      <div className="stock-icon">{stock.ticker}</div>
                      <div className="stock-meta">
                        <span className="stock-symbol">{stock.ticker}</span>
                        <span className={`stock-badge badge-${stock.category}`} style={{ fontSize: "9px", padding: "1px 6px" }}>
                          {categoryLabel}
                        </span>
                      </div>
                    </div>
                    <div className="stock-values">
                      <span className="stock-price-val" style={{ fontSize: "13px" }}>฿{stock.price.toFixed(2)}</span>
                      <span className={`stock-pct-val ${isUp ? "up" : "down"}`} style={{ fontSize: "11px" }}>
                        {prefix}{stock.change.toFixed(2)}%
                      </span>
                    </div>
                  </div>
                );
              })}
            </div>
          </div>
        </div>
      </section>
    );
  }

  // Questionnaire view
  const question = QUIZ_QUESTIONS[currentIdx];
  const totalQuestions = QUIZ_QUESTIONS.length;
  const progressPercent = (currentIdx / totalQuestions) * 100;
  const isOptionSelected = answers[currentIdx] !== undefined;

  return (
    <section className="page-view active" id="profiler-view">
      <div className="page-title-section">
        <h2 className="page-title">ประเมินความเสี่ยงเพื่อจัดพอร์ตที่เหมาะสม</h2>
        <p className="page-subtitle">ทำแบบทดสอบเพื่อค้นหาความต้องการการลงทุน พร้อมคำนวณสัดส่วนสินทรัพย์ที่แนะนำ (Asset Allocation)</p>
      </div>

      <div id="quiz-view-container">
        <div className="glass-card quiz-card">
          <div className="quiz-progress-bar">
            <div className="quiz-progress-fill" style={{ width: `${progressPercent}%` }}></div>
          </div>
          <div style={{ display: "flex", justifyContent: "space-between", fontSize: "12px", color: "var(--text-muted)" }}>
            <span>คำถามข้อที่ {currentIdx + 1} จากทั้งหมด {totalQuestions} ข้อ</span>
            <span>ความคืบหน้า {Math.round(progressPercent)}%</span>
          </div>
          <h3 className="quiz-question">{question.question}</h3>
          <div className="quiz-options">
            {question.options.map((opt, optIdx) => {
              const isSelected = answers[currentIdx] === optIdx;
              return (
                <button
                  key={optIdx}
                  className={`quiz-option ${isSelected ? "selected" : ""}`}
                  onClick={() => handleOptionSelect(optIdx)}
                >
                  {opt.text}
                </button>
              );
            })}
          </div>
          <div className="quiz-nav-buttons">
            <button
              className="btn btn-secondary"
              id="quiz-back-btn"
              disabled={currentIdx === 0}
              onClick={handleBack}
            >
              ย้อนกลับ
            </button>
            <button
              className="btn btn-primary"
              id="quiz-next-btn"
              disabled={!isOptionSelected}
              onClick={handleNext}
            >
              {currentIdx === totalQuestions - 1 ? "ดูผลการวิเคราะห์" : "ข้อถัดไป"}
            </button>
          </div>
        </div>
      </div>
    </section>
  );
}
