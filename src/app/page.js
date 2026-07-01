"use client";

import React, { useState, useEffect, useCallback } from "react";
import Sidebar from "../components/Sidebar";
import Header from "../components/Header";
import DashboardView from "../components/DashboardView";
import RecommendationsView from "../components/RecommendationsView";
import ProfilerView from "../components/ProfilerView";
import PortfolioView from "../components/PortfolioView";
import WatchlistView from "../components/WatchlistView";
import StockModal from "../components/StockModal";
import AuthModal from "../components/AuthModal";

export default function Home() {
  // Navigation & View States
  const [activeView, setActiveView] = useState("dashboard");
  const [searchQuery, setSearchQuery] = useState("");

  // Market & Data States
  const [stocks, setStocks] = useState([]);
  const [indices, setIndices] = useState([]);
  const [news, setNews] = useState([]);

  // User States
  const [currentUser, setCurrentUser] = useState("guest");
  const [cash, setCash] = useState(1000000.0);
  const [riskProfile, setRiskProfile] = useState(null);
  const [watchlist, setWatchlist] = useState(new Set());
  const [portfolio, setPortfolio] = useState([]);

  // Modal States
  const [selectedStockTicker, setSelectedStockTicker] = useState(null);
  const [isAuthModalOpen, setIsAuthModalOpen] = useState(false);

  // Toast State
  const [toasts, setToasts] = useState([]);

  // Toast Engine
  const showToast = useCallback((message, type = "success") => {
    const id = Date.now();
    setToasts((prev) => [...prev, { id, message, type }]);
    setTimeout(() => {
      setToasts((prev) => prev.filter((t) => t.id !== id));
    }, 4000);
  }, []);

  // Fetch Market Data
  const fetchMarketData = useCallback(async () => {
    try {
      const response = await fetch("/api/get_market_data");
      const data = await response.json();
      if (data.success) {
        setStocks(data.stocks || []);
        setIndices(data.indices || []);
        setNews(data.news || []);
      }
    } catch (e) {
      console.error("Failed to fetch market data", e);
    }
  }, []);

  // Fetch User State
  const fetchUserState = useCallback(async () => {
    try {
      const response = await fetch("/api/get_user_state");
      const data = await response.json();
      if (data.success) {
        setCurrentUser(data.username || "guest");
        setCash(data.cash);
        setRiskProfile(data.riskProfile || null);
        setWatchlist(new Set(data.watchlist || []));
        setPortfolio(data.portfolio || []);
      }
    } catch (e) {
      console.error("Failed to fetch user state", e);
    }
  }, []);

  // Initial load and live syncing
  useEffect(() => {
    fetchMarketData();
    fetchUserState();

    const interval = setInterval(() => {
      fetchMarketData();
      fetchUserState();
    }, 4000);

    return () => clearInterval(interval);
  }, [fetchMarketData, fetchUserState]);

  // Auth: Login
  const handleLogin = async (username, password) => {
    try {
      const response = await fetch("/api/login", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ username, password }),
      });
      const data = await response.json();
      if (data.success) {
        showToast(data.message, "success");
        setIsAuthModalOpen(false);
        await fetchUserState();
      } else {
        showToast(data.error || "เกิดข้อผิดพลาดในการเข้าสู่ระบบ", "error");
      }
    } catch (e) {
      console.error(e);
      showToast("ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์เพื่อเข้าสู่ระบบได้", "error");
    }
  };

  // Auth: Register
  const handleRegister = async (username, password, startingCash) => {
    try {
      const response = await fetch("/api/register", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ username, password, cash: startingCash }),
      });
      const data = await response.json();
      if (data.success) {
        showToast(data.message, "success");
        setIsAuthModalOpen(false);
        await fetchUserState();
      } else {
        showToast(data.error || "เกิดข้อผิดพลาดในการสมัครสมาชิก", "error");
      }
    } catch (e) {
      console.error(e);
      showToast("ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์เพื่อสมัครสมาชิกได้", "error");
    }
  };

  // Auth: Logout
  const handleLogout = async () => {
    try {
      const response = await fetch("/api/logout");
      const data = await response.json();
      if (data.success) {
        showToast(data.message, "success");
        setIsAuthModalOpen(false);
        setCurrentUser("guest");
        setRiskProfile(null);
        setPortfolio([]);
        setWatchlist(new Set());
        await fetchUserState();
        setActiveView("dashboard");
      }
    } catch (e) {
      console.error(e);
      showToast("เกิดข้อผิดพลาดในการออกจากระบบ", "error");
    }
  };

  // Profile: Save Risk Profile
  const handleSaveRiskProfile = async (profile) => {
    try {
      const response = await fetch("/api/save_profile", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ risk_profile: profile }),
      });
      const data = await response.json();
      if (data.success) {
        setRiskProfile(profile);
        showToast("ประเมินความเสี่ยงและบันทึกข้อมูลเรียบร้อย!", "success");
      }
    } catch (e) {
      console.error(e);
      showToast("เกิดข้อผิดพลาดในการบันทึกผลลัพธ์", "error");
    }
  };

  // Profile: Clear Risk Profile (Retest)
  const handleClearRiskProfile = async () => {
    try {
      const response = await fetch("/api/save_profile", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ risk_profile: "null" }),
      });
      const data = await response.json();
      if (data.success) {
        setRiskProfile(null);
      }
    } catch (e) {
      console.error(e);
      showToast("เกิดข้อผิดพลาดในการล้างข้อมูลประเมินความเสี่ยง", "error");
    }
  };

  // Action: Watchlist Toggle
  const handleWatchlistToggle = async (ticker) => {
    try {
      const response = await fetch("/api/watchlist_toggle", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ ticker }),
      });
      const data = await response.json();
      if (data.success) {
        showToast(data.message, "success");
        await fetchUserState();
      }
    } catch (e) {
      console.error(e);
      showToast("เกิดข้อผิดพลาดในการสลับรายการเฝ้าดู", "error");
    }
  };

  // Action: Trade execution (Buy/Sell)
  const handleTrade = async (ticker, type, qty) => {
    try {
      const response = await fetch("/api/trade", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ ticker, type, quantity: qty }),
      });
      const data = await response.json();
      if (data.success) {
        showToast(data.message, "success");
        await fetchUserState();
      } else {
        showToast(data.error || "การส่งคำสั่งซื้อขายล้มเหลว", "error");
      }
    } catch (e) {
      console.error(e);
      showToast("เกิดข้อผิดพลาดในการซื้อขาย", "error");
    }
  };

  const selectedStock = stocks.find((s) => s.ticker === selectedStockTicker);

  return (
    <div className="app-container">
      {/* Sidebar Navigation */}
      <Sidebar
        activeView={activeView}
        setActiveView={setActiveView}
        currentUser={currentUser}
        onOpenAuth={() => setIsAuthModalOpen(true)}
      />

      {/* Main Content Area */}
      <main className="main-content">
        {/* Top Header */}
        <Header cash={cash} searchQuery={searchQuery} setSearchQuery={setSearchQuery} />

        {/* View Switcher Panels */}
        {activeView === "dashboard" && (
          <DashboardView
            stocks={stocks}
            indices={indices}
            news={news}
            onOpenStockModal={setSelectedStockTicker}
          />
        )}

        {activeView === "recommendations" && (
          <RecommendationsView
            stocks={stocks}
            searchQuery={searchQuery}
            onOpenStockModal={setSelectedStockTicker}
          />
        )}

        {activeView === "profiler" && (
          <ProfilerView
            riskProfile={riskProfile}
            stocks={stocks}
            onOpenStockModal={setSelectedStockTicker}
            onSaveRiskProfile={handleSaveRiskProfile}
            onClearRiskProfile={handleClearRiskProfile}
          />
        )}

        {activeView === "portfolio" && (
          <PortfolioView
            portfolio={portfolio}
            stocks={stocks}
            cash={cash}
            onOpenStockModal={setSelectedStockTicker}
            onNavigate={setActiveView}
          />
        )}

        {activeView === "watchlist" && (
          <WatchlistView
            watchlist={watchlist}
            stocks={stocks}
            onOpenStockModal={setSelectedStockTicker}
            onNavigate={setActiveView}
          />
        )}
      </main>

      {/* Stock Details & Trade Modal */}
      <StockModal
        isOpen={!!selectedStockTicker}
        onClose={() => setSelectedStockTicker(null)}
        stock={selectedStock}
        cash={cash}
        portfolio={portfolio}
        watchlist={watchlist}
        onTrade={handleTrade}
        onWatchlistToggle={handleWatchlistToggle}
      />

      {/* Auth & Profile Modal */}
      <AuthModal
        isOpen={isAuthModalOpen}
        onClose={() => setIsAuthModalOpen(false)}
        currentUser={currentUser}
        cash={cash}
        riskProfile={riskProfile}
        onLogin={handleLogin}
        onRegister={handleRegister}
        onLogout={handleLogout}
      />

      {/* Notification Toast Area */}
      <div className="notifications-container" id="toast-container">
        {toasts.map((toast) => (
          <div key={toast.id} className={`toast ${toast.type}`}>
            {toast.type === "success" ? (
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
              </svg>
            ) : (
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
              </svg>
            )}
            <div className="toast-message">{toast.message}</div>
          </div>
        ))}
      </div>
    </div>
  );
}
