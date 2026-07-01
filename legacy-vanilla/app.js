// app.js - Main Application Controller for Full-Stack SmartInvest Stock App
(function() {
  // Global Application State (loaded from PHP SQLite API)
  const state = {
    currentUser: 'guest',
    authTab: 'login', // 'login' or 'register'
    cash: 1000000.00,
    portfolio: [], // { ticker, shares, avgPrice }
    watchlist: new Set(), // tickers
    riskProfile: null, // 'conservative', 'moderate', 'aggressive'
    activeView: 'dashboard',
    
    // Loaded market data
    stocks: [],
    indices: [],
    news: [],
    
    // Modal State
    modalStock: null,
    modalChartType: 'area',
    modalChartDuration: '3M',
    tradeType: 'buy', // 'buy' or 'sell'
    tradeQuantity: 100,

    // Quiz State
    currentQuestionIndex: 0,
    quizAnswers: []
  };

  // Helper Elements Cache
  let elements = {};

  function initElements() {
    elements = {
      sidebarLinks: document.querySelectorAll('.nav-item'),
      views: document.querySelectorAll('.page-view'),
      searchBar: document.getElementById('search-input'),
      tickerContainer: document.getElementById('ticker-container'),
      cashValue: document.getElementById('header-cash'),
      
      // Dashboard Views
      topGainersList: document.getElementById('top-gainers-list'),
      indicesContainer: document.getElementById('indices-container'),
      newsContainer: document.getElementById('news-container'),

      // Recommendations View
      recomFilters: document.querySelectorAll('#recom-filters .filter-tab'),
      recomList: document.getElementById('recommendations-list'),

      // Risk Profiler View
      quizContainer: document.getElementById('quiz-view-container'),

      // Portfolio View
      portfolioGrid: document.getElementById('portfolio-grid'),
      portfolioTableBody: document.getElementById('portfolio-table-body'),
      totalPortfolioValue: document.getElementById('total-portfolio-value'),
      portfolioProfitLoss: document.getElementById('portfolio-profit-loss'),
      portfolioCash: document.getElementById('portfolio-cash'),
      portfolioTotalEquity: document.getElementById('portfolio-total-equity'),

      // Watchlist View
      watchlistList: document.getElementById('watchlist-list'),

      // Modal
      modal: document.getElementById('stock-modal'),
      modalCloseBtn: document.getElementById('modal-close'),
      modalTicker: document.getElementById('modal-stock-ticker'),
      modalName: document.getElementById('modal-stock-name'),
      modalBadge: document.getElementById('modal-stock-badge'),
      modalPrice: document.getElementById('modal-stock-price'),
      modalChange: document.getElementById('modal-stock-change'),
      modalPe: document.getElementById('modal-pe'),
      modalDivYield: document.getElementById('modal-dividend-yield'),
      modalMarketCap: document.getElementById('modal-market-cap'),
      modalBeta: document.getElementById('modal-beta'),
      modalConsensus: document.getElementById('modal-consensus'),
      modalDescription: document.getElementById('modal-description'),
      
      // Modal Trade
      tradeTabBuy: document.getElementById('trade-tab-buy'),
      tradeTabSell: document.getElementById('trade-tab-sell'),
      tradeInputQty: document.getElementById('trade-qty-input'),
      tradeEstimatedTotal: document.getElementById('trade-est-total'),
      tradeCashLabel: document.getElementById('trade-cash-label'),
      tradeCashVal: document.getElementById('trade-cash-val'),
      tradeSharesOwnedLabel: document.getElementById('trade-shares-owned-label'),
      tradeSharesOwnedVal: document.getElementById('trade-shares-owned-val'),
      tradeActionBtn: document.getElementById('trade-action-btn'),
      watchlistToggleBtn: document.getElementById('modal-watchlist-toggle'),

      // Auth & Profile Modal elements
      authModal: document.getElementById('auth-modal'),
      authModalClose: document.getElementById('auth-modal-close'),
      userProfileBtn: document.getElementById('user-profile-btn'),
      userAvatarChar: document.getElementById('user-avatar-char'),
      userDisplayName: document.getElementById('user-display-name'),
      userDisplayRole: document.getElementById('user-display-role'),
      authGuestSection: document.getElementById('auth-guest-section'),
      authUserSection: document.getElementById('auth-user-section'),
      authTabLogin: document.getElementById('auth-tab-login'),
      authTabRegister: document.getElementById('auth-tab-register'),
      registerCashGroup: document.getElementById('register-cash-group'),
      authForm: document.getElementById('auth-form'),
      authUsernameInput: document.getElementById('auth-username'),
      authPasswordInput: document.getElementById('auth-password'),
      authStartCashSelect: document.getElementById('auth-start-cash'),
      authSubmitBtn: document.getElementById('auth-submit-btn'),
      profileAvatar: document.getElementById('profile-avatar'),
      profileUsername: document.getElementById('profile-username'),
      profileRole: document.getElementById('profile-role'),
      profileCash: document.getElementById('profile-cash'),
      profileRisk: document.getElementById('profile-risk'),
      authLogoutBtn: document.getElementById('auth-logout-btn'),

      // Toast container
      toastContainer: document.getElementById('toast-container')
    };
  }

  // Fetch Market Data from PHP Backend
  async function fetchMarketData() {
    try {
      const response = await fetch('api.php?action=get_market_data');
      const data = await response.json();
      if (data.success) {
        state.stocks = data.stocks;
        state.indices = data.indices;
        state.news = data.news;
      }
    } catch (e) {
      console.error('Failed to fetch market data', e);
      showToast('ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์เพื่อดึงข้อมูลตลาดได้', 'error');
    }
  }

  // Fetch User State from PHP Backend
  async function fetchUserState() {
    try {
      const response = await fetch('api.php?action=get_user_state');
      const data = await response.json();
      if (data.success) {
        state.currentUser = data.username;
        state.cash = data.cash;
        state.riskProfile = data.riskProfile;
        state.watchlist = new Set(data.watchlist);
        state.portfolio = data.portfolio;
        updateSidebarProfile();
      }
    } catch (e) {
      console.error('Failed to fetch user state', e);
    }
  }

  function updateSidebarProfile() {
    if (!elements.userDisplayName) return;
    const isGuest = state.currentUser === 'guest';
    
    elements.userDisplayName.textContent = isGuest ? 'นักลงทุนจำลอง' : state.currentUser;
    elements.userDisplayRole.textContent = isGuest ? 'Demo Account' : 'สมาชิก (Member)';
    elements.userAvatarChar.textContent = (isGuest ? 'U' : state.currentUser[0]).toUpperCase();
    
    // Update profile section in modal if active
    if (elements.authModal.classList.contains('active')) {
      renderAuthModalContent();
    }
  }

  function renderAuthModalContent() {
    const isGuest = state.currentUser === 'guest';
    
    if (isGuest) {
      elements.authGuestSection.style.display = 'block';
      elements.authUserSection.style.display = 'none';
      
      // Update form tab presentation
      if (state.authTab === 'login') {
        elements.authTabLogin.classList.add('active', 'buy');
        elements.authTabRegister.classList.remove('active', 'sell');
        elements.registerCashGroup.style.display = 'none';
        elements.authSubmitBtn.textContent = 'เข้าสู่ระบบ (Log In)';
        elements.authSubmitBtn.className = 'btn btn-primary';
      } else {
        elements.authTabLogin.classList.remove('active', 'buy');
        elements.authTabRegister.classList.add('active', 'sell');
        elements.registerCashGroup.style.display = 'block';
        elements.authSubmitBtn.textContent = 'สมัครสมาชิก (Register)';
        elements.authSubmitBtn.className = 'btn btn-success';
      }
      elements.authSubmitBtn.style.width = '100%';
    } else {
      elements.authGuestSection.style.display = 'none';
      elements.authUserSection.style.display = 'flex';
      
      elements.profileUsername.textContent = state.currentUser;
      elements.profileAvatar.textContent = state.currentUser[0].toUpperCase();
      elements.profileCash.textContent = `฿${state.cash.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
      
      const riskMapping = {
        'conservative': 'ความเสี่ยงต่ำ (Conservative)',
        'moderate': 'ความเสี่ยงปานกลาง (Moderate)',
        'aggressive': 'ความเสี่ยงสูง (Aggressive)'
      };
      elements.profileRisk.textContent = state.riskProfile ? riskMapping[state.riskProfile] : 'ยังไม่ประเมิน';
      elements.profileRisk.style.color = state.riskProfile ? 'var(--color-brand)' : 'var(--text-muted)';
    }
  }

  function setAuthTab(tab) {
    state.authTab = tab;
    renderAuthModalContent();
  }

  async function handleAuthSubmit() {
    const username = elements.authUsernameInput.value.trim();
    const password = elements.authPasswordInput.value.trim();
    
    if (!username || !password) {
      showToast('กรุณากรอกชื่อผู้ใช้และรหัสผ่าน', 'error');
      return;
    }

    const actionUrl = state.authTab === 'login' ? 'api.php?action=login' : 'api.php?action=register';
    const payload = { username, password };
    
    if (state.authTab === 'register') {
      payload.cash = parseFloat(elements.authStartCashSelect.value);
    }

    try {
      const response = await fetch(actionUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      const data = await response.json();
      
      if (data.success) {
        showToast(data.message, 'success');
        elements.authModal.classList.remove('active');
        
        // Reset form inputs
        elements.authUsernameInput.value = '';
        elements.authPasswordInput.value = '';
        
        // Load state and refresh views
        await fetchUserState();
        updateHeader();
        navigateTo(state.activeView);
      } else {
        showToast(data.error || 'เกิดข้อผิดพลาดในการตรวจสอบสิทธิ์', 'error');
      }
    } catch (e) {
      console.error(e);
      showToast('ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์เพื่อลงชื่อเข้าใช้งานได้', 'error');
    }
  }

  async function handleLogout() {
    try {
      const response = await fetch('api.php?action=logout');
      const data = await response.json();
      if (data.success) {
        showToast(data.message, 'success');
        elements.authModal.classList.remove('active');
        
        // Reset state & load guest defaults
        state.currentUser = 'guest';
        state.riskProfile = null;
        state.quizAnswers = [];
        state.currentQuestionIndex = 0;
        
        await fetchUserState();
        updateHeader();
        navigateTo('dashboard');
      }
    } catch (e) {
      console.error(e);
    }
  }

  // Toast Notification Engine
  function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    
    const icon = type === 'success' 
      ? `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>`
      : `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>`;

    toast.innerHTML = `
      ${icon}
      <div class="toast-message">${message}</div>
    `;

    elements.toastContainer.appendChild(toast);

    setTimeout(() => {
      toast.style.animation = 'slide-in-toast 0.3s ease reverse forwards';
      setTimeout(() => {
        toast.remove();
      }, 300);
    }, 3000);
  }

  // Navigation SPA Router
  function navigateTo(viewId) {
    state.activeView = viewId;
    
    // Update sidebar UI
    elements.sidebarLinks.forEach(link => {
      if (link.getAttribute('data-view') === viewId) {
        link.classList.add('active');
      } else {
        link.classList.remove('active');
      }
    });

    // Update Viewports
    elements.views.forEach(view => {
      if (view.id === `${viewId}-view`) {
        view.classList.add('active');
      } else {
        view.classList.remove('active');
      }
    });

    // Perform view-specific initializations
    if (viewId === 'dashboard') {
      renderDashboard();
    } else if (viewId === 'recommendations') {
      renderRecommendations();
    } else if (viewId === 'profiler') {
      renderProfiler();
    } else if (viewId === 'portfolio') {
      renderPortfolio();
    } else if (viewId === 'watchlist') {
      renderWatchlist();
    }
  }

  // Live Updating Loop (polling backend DB changes made by Python simulator)
  function startSyncLoop() {
    setInterval(async () => {
      await fetchMarketData();
      await fetchUserState();
      
      // Update UI components in real-time
      updateRealtimeUI();
    }, 4000);
  }

  function updateRealtimeUI() {
    updateHeader();
    
    if (state.activeView === 'dashboard') {
      renderTickerBar();
      renderTopGainers();
      renderIndices();
    } else if (state.activeView === 'recommendations') {
      updateStockListPrices('.stock-list');
    } else if (state.activeView === 'portfolio') {
      updatePortfolioSummary();
      updateStockListPrices('#portfolio-table-body');
    } else if (state.activeView === 'watchlist') {
      updateStockListPrices('#watchlist-list');
    }

    // Update modal if active
    if (state.modalStock && elements.modal.classList.contains('active')) {
      const liveStock = state.stocks.find(s => s.ticker === state.modalStock.ticker);
      if (liveStock) {
        elements.modalPrice.textContent = `฿${liveStock.price.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        
        const changePrefix = liveStock.change >= 0 ? '+' : '';
        elements.modalChange.textContent = `${changePrefix}${liveStock.change.toFixed(2)}%`;
        elements.modalChange.className = liveStock.change >= 0 ? 'up' : 'down';
        
        updateTradeDetails();
      }
    }
  }

  function updateStockListPrices(parentSelector) {
    const list = document.querySelector(parentSelector);
    if (!list) return;

    state.stocks.forEach(stock => {
      const row = list.querySelector(`[data-ticker="${stock.ticker}"]`);
      if (row) {
        const priceEl = row.querySelector('.stock-price-val');
        const changeEl = row.querySelector('.stock-pct-val');
        
        if (priceEl) {
          const oldPriceText = priceEl.textContent;
          const newPriceText = `฿${stock.price.toFixed(2)}`;
          
          if (oldPriceText && oldPriceText !== newPriceText) {
            const flashClass = stock.change >= 0 ? 'flash-up' : 'flash-down';
            row.classList.add(flashClass);
            setTimeout(() => {
              row.classList.remove(flashClass);
            }, 1200);
          }
          priceEl.textContent = newPriceText;
        }
        if (changeEl) {
          const prefix = stock.change >= 0 ? '+' : '';
          changeEl.textContent = `${prefix}${stock.change.toFixed(2)}%`;
          changeEl.className = `stock-pct-val ${stock.change >= 0 ? 'up' : 'down'}`;
        }
      }
    });
  }

  function updateHeader() {
    elements.cashValue.textContent = `฿${state.cash.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
  }

  // Dashboard Renderer
  function renderDashboard() {
    renderTickerBar();
    renderTopGainers();
    renderIndices();
    renderNews();
  }

  function renderTickerBar() {
    elements.tickerContainer.innerHTML = '';
    
    state.stocks.forEach(stock => {
      const tickerItem = document.createElement('div');
      tickerItem.className = 'ticker-item';
      tickerItem.setAttribute('data-ticker', stock.ticker);
      tickerItem.onclick = () => openStockModal(stock.ticker);
      
      const isUp = stock.change >= 0;
      const changePrefix = isUp ? '+' : '';
      
      tickerItem.innerHTML = `
        <span class="ticker-name">${stock.ticker}</span>
        <span class="ticker-val">฿${stock.price.toFixed(2)}</span>
        <span class="ticker-change ${isUp ? 'up' : 'down'}">${changePrefix}${stock.change.toFixed(2)}%</span>
      `;
      elements.tickerContainer.appendChild(tickerItem);
    });
  }

  function renderTopGainers() {
    elements.topGainersList.innerHTML = '';
    
    // Sort stocks by performance change descending
    const sorted = [...state.stocks].sort((a, b) => b.change - a.change).slice(0, 4);
    
    sorted.forEach(stock => {
      const row = document.createElement('div');
      row.className = 'stock-row';
      row.setAttribute('data-ticker', stock.ticker);
      row.onclick = () => openStockModal(stock.ticker);
      
      const prefix = stock.change >= 0 ? '+' : '';
      const categoryLabel = stock.category === 'dividend' ? 'ปันผลสูง' : stock.category === 'growth' ? 'เติบโตสูง' : 'เสี่ยงสูง/เก็งกำไร';
      
      row.innerHTML = `
        <div class="stock-info">
          <div class="stock-icon">${stock.ticker}</div>
          <div class="stock-meta">
            <span class="stock-symbol">${stock.ticker}</span>
            <span class="stock-fullname">${stock.name}</span>
          </div>
          <span class="stock-badge badge-${stock.category}">${categoryLabel}</span>
        </div>
        <div class="stock-values">
          <span class="stock-price-val">฿${stock.price.toFixed(2)}</span>
          <span class="stock-pct-val ${stock.change >= 0 ? 'up' : 'down'}">${prefix}${stock.change.toFixed(2)}%</span>
        </div>
      `;
      elements.topGainersList.appendChild(row);
    });
  }

  function renderIndices() {
    elements.indicesContainer.innerHTML = '';
    
    state.indices.forEach(idx => {
      const card = document.createElement('div');
      card.className = 'mini-card';
      
      const isUp = idx.isPositive;
      
      card.innerHTML = `
        <span class="mini-card-title">${idx.name}</span>
        <span class="mini-card-val">${idx.value}</span>
        <span class="stock-pct-val ${isUp ? 'up' : 'down'}" style="font-size: 12px; font-weight:600;">
          ${idx.change} (${idx.changeValue >= 0 ? '+' : ''}${idx.changeValue})
        </span>
      `;
      elements.indicesContainer.appendChild(card);
    });
  }

  function renderNews() {
    elements.newsContainer.innerHTML = '';
    
    state.news.forEach(item => {
      const card = document.createElement('div');
      card.className = 'news-card';
      
      card.innerHTML = `
        <div class="news-cat">${item.category}</div>
        <div class="news-title">${item.title}</div>
        <div class="news-meta">
          <span>${item.source}</span>
          <span>${item.time}</span>
        </div>
      `;
      elements.newsContainer.appendChild(card);
    });
  }

  // Stock Recommendations Renderer
  let activeRecomCategory = 'all';

  function renderRecommendations() {
    elements.recomList.innerHTML = '';
    
    const filterText = elements.searchBar.value.toLowerCase().trim();
    
    const filtered = state.stocks.filter(stock => {
      const matchesCategory = activeRecomCategory === 'all' || stock.category === activeRecomCategory;
      const matchesSearch = stock.ticker.toLowerCase().includes(filterText) || stock.name.toLowerCase().includes(filterText);
      return matchesCategory && matchesSearch;
    });

    if (filtered.length === 0) {
      elements.recomList.innerHTML = `
        <div class="empty-state" style="grid-column: span 2;">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.3-4.3"></path></svg>
          <div class="empty-state-title">ไม่พบรายชื่อหุ้น</div>
          <div class="empty-state-desc">ลองค้นหาด้วยคำอื่น หรือเลือกหมวดหมู่อื่น</div>
        </div>
      `;
      return;
    }

    filtered.forEach(stock => {
      const card = document.createElement('div');
      card.className = 'glass-card';
      card.onclick = () => openStockModal(stock.ticker);
      
      const changePrefix = stock.change >= 0 ? '+' : '';
      const categoryLabel = stock.category === 'dividend' ? 'ปันผลสูง (Dividend)' : stock.category === 'growth' ? 'เติบโตดี (Growth)' : 'เทค/ความเสี่ยงสูง (Tech/Risk)';

      card.innerHTML = `
        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:16px;">
          <div style="display:flex; gap:12px; align-items:center;">
            <div class="stock-icon">${stock.ticker}</div>
            <div style="display:flex; flex-direction:column; gap:4px;">
              <span class="stock-symbol" style="font-size:16px;">${stock.ticker}</span>
              <span class="stock-badge badge-${stock.category}">${categoryLabel}</span>
            </div>
          </div>
          <div style="text-align:right;">
            <div class="stock-price-val" style="font-size:18px;">฿${stock.price.toFixed(2)}</div>
            <div class="stock-pct-val ${stock.change >= 0 ? 'up' : 'down'}" style="font-size:13px; font-weight:600;">
              ${changePrefix}${stock.change.toFixed(2)}%
            </div>
          </div>
        </div>
        <p style="font-size:12px; color:var(--text-secondary); line-height:1.5; height: 48px; overflow:hidden; text-overflow:ellipsis; display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; margin-bottom:16px;">
          ${stock.description}
        </p>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; border-top:1px solid var(--border-color); padding-top:12px; font-size:11px; color:var(--text-muted);">
          <div>P/E: <strong style="color:var(--text-primary);">${stock.pe}x</strong></div>
          <div>Div Yield: <strong style="color:var(--text-primary);">${stock.dividendYield}%</strong></div>
          <div>Consensus: <strong style="color:var(--color-brand);">${stock.consensus}</strong></div>
          <div>Risk: <strong style="color:${stock.risk === 'Low' ? 'var(--color-success)' : stock.risk === 'Medium' ? 'var(--color-warning)' : 'var(--color-danger)'}">${stock.risk}</strong></div>
        </div>
      `;
      elements.recomList.appendChild(card);
    });
  }

  // Quiz questions array (read-only client-side quiz flow template)
  const QUIZ_QUESTIONS = [
    {
      id: 1,
      question: 'ช่วงอายุของคุณคือช่วงใด?',
      options: [
        { text: 'มากกว่า 55 ปีขึ้นไป (เน้นรักษาเงินต้น)', score: 1 },
        { text: '35 - 55 ปี (เน้นการเติบโตแบบสมดุล)', score: 2 },
        { text: 'น้อยกว่า 35 ปี (เน้นเติบโตระยะยาว รับความผันผวนสูงได้)', score: 3 }
      ]
    },
    {
      id: 2,
      question: 'เป้าหมายหลักในการลงทุนของคุณคืออะไร?',
      options: [
        { text: 'เน้นกระแสเงินสดปันผลที่สม่ำเสมอและความปลอดภัยของเงินต้น', score: 1 },
        { text: 'สร้างการเติบโตปานกลางควบคู่กับความมั่นคงของเงินทุน', score: 2 },
        { text: 'สร้างผลตอบแทนสูงสุดในระยะยาว ยอมรับการขาดทุนชั่วคราวได้สูง', score: 3 }
      ]
    },
    {
      id: 3,
      question: 'คุณมีประสบการณ์ลงทุนในหุ้น กองทุนรวม หรือสินทรัพย์ดิจิทัลมากน้อยเพียงใด?',
      options: [
        { text: 'ไม่มีประสบการณ์เลย หรือน้อยมาก (เน้นเงินฝากหรือพันธบัตร)', score: 1 },
        { text: 'พอมีความเข้าใจ (เข้าใจงบการเงินเบื้องต้น เคยซื้อกองทุน/หุ้นบ้าง)', score: 2 },
        { text: 'มีความเชี่ยวชาญสูง (ซื้อขายบ่อย เข้าใจการวิเคราะห์กราฟเทคนิค หรือตราสารอนุพันธ์)', score: 3 }
      ]
    },
    {
      id: 4,
      question: 'หากเงินลงทุนของคุณลดลง 20% ภายใน 1 เดือนเนื่องจากตลาดปรับฐาน คุณจะจัดการอย่างไร?',
      options: [
        { text: 'ขายล้างพอร์ตทันทีเพื่อป้องกันไม่ให้ขาดทุนมากไปกว่านี้', score: 1 },
        { text: 'ไม่ทำอะไร รอตลาดฟื้นตัว หรือปรึกษาผู้แนะนำการลงทุน', score: 2 },
        { text: 'ทยอยซื้อหุ้นเพิ่มเพื่อเฉลี่ยต้นทุนในราคาที่ถูกลง (DCA / Buy the dip)', score: 3 }
      ]
    },
    {
      id: 5,
      question: 'คุณวางแผนจะแบ่งเงินออมส่วนตัวมาลงทุนในสินทรัพย์เสี่ยงสูง (เช่น หุ้น) ประมาณเท่าใด?',
      options: [
        { text: 'น้อยกว่า 10% (ที่เหลือฝากธนาคาร ซื้อทองคำ หรือพันธบัตรรัฐบาล)', score: 1 },
        { text: '10% - 50% (กระจายในหลายสินทรัพย์ความเสี่ยงปานกลาง)', score: 2 },
        { text: 'มากกว่า 50% (ต้องการทุ่มเทเงินลงทุนเพื่อให้ได้ผลตอบแทนเติบโตสูงสุด)', score: 3 }
      ]
    }
  ];

  const PORTFOLIO_ALLOCATIONS = {
    conservative: {
      title: 'Conservative Portfolio (พอร์ตความเสี่ยงต่ำ - เน้นความปลอดภัย)',
      description: 'เหมาะสำหรับนักลงทุนที่ต้องการปกป้องเงินต้น หลีกเลี่ยงความผันผวนของตลาด และรับผลตอบแทนสม่ำเสมอในรูปแบบของดอกเบี้ยและปันผล',
      allocation: [
        { asset: 'เงินฝากดอกเบี้ยสูง / ตราสารหนี้ระยะสั้น', percentage: 30 },
        { asset: 'พันธบัตรรัฐบาล / หุ้นกู้คุณภาพสูง', percentage: 45 },
        { asset: 'หุ้นพื้นฐานดีปันผลสูง (Blue-chip & Dividend)', percentage: 20 },
        { asset: 'ทองคำ / สินทรัพย์ป้องกันความเสี่ยง', percentage: 5 }
      ],
      recommendedStocks: ['PTT', 'ADVANC', 'BDMS', 'SCC']
    },
    moderate: {
      title: 'Moderate Portfolio (พอร์ตความเสี่ยงปานกลาง - เติบโตสมดุล)',
      description: 'ผสมผสานระหว่างการเติบโตของเงินทุนในระยะยาวและการกระจายความเสี่ยงอย่างเหมาะสม ยอมรับความผันผวนได้ปานกลางเพื่อสร้างโอกาสรับผลตอบแทนที่สูงขึ้น',
      allocation: [
        { asset: 'เงินสด / ตราสารหนี้ระยะสั้น', percentage: 15 },
        { asset: 'กองทุนรวมตราสารหนี้', percentage: 25 },
        { asset: 'หุ้นเติบโตปานกลาง (Growth Stocks)', percentage: 35 },
        { asset: 'หุ้นปันผลสูง (Dividend Stocks)', percentage: 20 },
        { asset: 'ทองคำ / สินทรัพย์ทางเลือกอื่น', percentage: 5 }
      ],
      recommendedStocks: ['CPALL', 'KBANK', 'AOT', 'GULF', 'PTT', 'ADVANC']
    },
    aggressive: {
      title: 'Aggressive Portfolio (พอร์ตความเสี่ยงสูง - เน้นการเติบโตเชิงรุก)',
      description: 'มุ่งเน้นการเติบโตของเงินทุนสูงสุด ยอมรับความเสี่ยงการสูญเสียเงินต้นและความผันผวนสูงได้ เพื่อผลตอบแทนแบบทวีคูณในระยะยาว',
      allocation: [
        { asset: 'เงินสดสำรองสภาพคล่อง', percentage: 5 },
        { asset: 'หุ้นกู้คุณภาพสูง', percentage: 10 },
        { asset: 'หุ้นเติบโตและเทคโนโลยี (Growth & Tech)', percentage: 55 },
        { asset: 'หุ้นเสี่ยงสูง / อุปกรณ์อิเล็กทรอนิกส์ส่งออก', percentage: 20 },
        { asset: 'สินทรัพย์ดิจิทัล / สินทรัพย์ทางเลือก', percentage: 10 }
      ],
      recommendedStocks: ['DELTA', 'HANA', 'COM7', 'GULF', 'CPALL', 'KBANK']
    }
  };

  // Risk Profiler UI Renderer
  function renderProfiler() {
    elements.quizContainer.innerHTML = '';
    
    if (state.riskProfile) {
      renderProfilerResult();
      return;
    }

    const question = QUIZ_QUESTIONS[state.currentQuestionIndex];
    const totalQuestions = QUIZ_QUESTIONS.length;
    const progressPercent = ((state.currentQuestionIndex) / totalQuestions) * 100;

    const quizCard = document.createElement('div');
    quizCard.className = 'glass-card quiz-card';

    // Render Options
    let optionsHtml = '';
    question.options.forEach((opt, idx) => {
      const isSelected = state.quizAnswers[state.currentQuestionIndex] === idx;
      optionsHtml += `
        <button class="quiz-option ${isSelected ? 'selected' : ''}" data-index="${idx}">
          ${opt.text}
        </button>
      `;
    });

    quizCard.innerHTML = `
      <div class="quiz-progress-bar">
        <div class="quiz-progress-fill" style="width: ${progressPercent}%"></div>
      </div>
      <div style="display:flex; justify-content:space-between; font-size:12px; color:var(--text-muted);">
        <span>คำถามข้อที่ ${state.currentQuestionIndex + 1} จากทั้งหมด ${totalQuestions} ข้อ</span>
        <span>ความคืบหน้า ${Math.round(progressPercent)}%</span>
      </div>
      <h3 class="quiz-question">${question.question}</h3>
      <div class="quiz-options">
        ${optionsHtml}
      </div>
      <div class="quiz-nav-buttons">
        <button class="btn btn-secondary" id="quiz-back-btn" ${state.currentQuestionIndex === 0 ? 'disabled' : ''}>
          ย้อนกลับ
        </button>
        <button class="btn btn-primary" id="quiz-next-btn" disabled>
          ${state.currentQuestionIndex === totalQuestions - 1 ? 'ดูผลการวิเคราะห์' : 'ข้อถัดไป'}
        </button>
      </div>
    `;

    elements.quizContainer.appendChild(quizCard);

    // Event Listeners for Quiz Card
    const optionBtns = quizCard.querySelectorAll('.quiz-option');
    const nextBtn = document.getElementById('quiz-next-btn');
    const backBtn = document.getElementById('quiz-back-btn');

    if (state.quizAnswers[state.currentQuestionIndex] !== undefined) {
      nextBtn.removeAttribute('disabled');
    }

    optionBtns.forEach(btn => {
      btn.onclick = () => {
        optionBtns.forEach(b => b.classList.remove('selected'));
        btn.classList.add('selected');
        
        const optionIdx = parseInt(btn.getAttribute('data-index'));
        state.quizAnswers[state.currentQuestionIndex] = optionIdx;
        
        nextBtn.removeAttribute('disabled');
      };
    });

    backBtn.onclick = () => {
      if (state.currentQuestionIndex > 0) {
        state.currentQuestionIndex--;
        renderProfiler();
      }
    };

    nextBtn.onclick = () => {
      if (state.currentQuestionIndex < totalQuestions - 1) {
        state.currentQuestionIndex++;
        renderProfiler();
      } else {
        calculateRiskProfile();
      }
    };
  }

  async function calculateRiskProfile() {
    let totalScore = 0;
    state.quizAnswers.forEach((optIdx, qIdx) => {
      totalScore += QUIZ_QUESTIONS[qIdx].options[optIdx].score;
    });

    let profileResult = 'moderate';
    if (totalScore <= 8) {
      profileResult = 'conservative';
    } else if (totalScore <= 12) {
      profileResult = 'moderate';
    } else {
      profileResult = 'aggressive';
    }

    try {
      const response = await fetch('api.php?action=save_profile', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ risk_profile: profileResult })
      });
      const data = await response.json();
      if (data.success) {
        state.riskProfile = profileResult;
        showToast('ประเมินความเสี่ยงและบันทึกข้อมูลเรียบร้อย!', 'success');
        renderProfiler();
      }
    } catch (e) {
      console.error(e);
      showToast('เกิดข้อผิดพลาดในการบันทึกผลลัพธ์', 'error');
    }
  }

  function renderProfilerResult() {
    const data = PORTFOLIO_ALLOCATIONS[state.riskProfile];
    const riskBadgeClass = state.riskProfile === 'conservative' ? 'badge-dividend' : state.riskProfile === 'moderate' ? 'badge-growth' : 'badge-highrisk';
    const riskLabel = state.riskProfile === 'conservative' ? 'ความเสี่ยงต่ำ (Conservative)' : state.riskProfile === 'moderate' ? 'ความเสี่ยงปานกลาง (Moderate)' : 'ความเสี่ยงสูง (Aggressive)';
    
    const resultCard = document.createElement('div');
    resultCard.className = 'glass-card';
    
    resultCard.innerHTML = `
      <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--border-color); padding-bottom:16px; margin-bottom:20px;">
        <h3 class="section-title" style="margin-bottom:0;">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield"><path d="M20 13c0 5-3.5 7.5-7.66 9.7a1 1 0 0 1-.68 0C7.5 20.5 4 18 4 13V6a1 1 0 0 1 .76-.97l8-2a1 1 0 0 1 .48 0l8 2A1 1 0 0 1 20 6z"></path></svg>
          ผลลัพธ์การประเมินระดับความเสี่ยง
        </h3>
        <button class="btn btn-secondary" style="padding:6px 12px; font-size:11px;" id="re-test-btn">ทำแบบประเมินใหม่</button>
      </div>

      <div class="result-container">
        <div class="result-pie-card">
          <div id="allocation-donut-chart" style="width: 100%; min-height: 280px;"></div>
        </div>
        <div class="result-details">
          <span class="result-badge ${riskBadgeClass}">${riskLabel}</span>
          <h4 class="result-title">${data.title}</h4>
          <p class="result-desc">${data.description}</p>
          
          <div class="recom-assets-list">
            <span style="font-size:11px; color:var(--text-muted); font-weight:700; text-transform:uppercase;">สัดส่วนที่แนะนำ:</span>
            ${data.allocation.map(a => `
              <div class="asset-bullet">
                <span>${a.asset}</span>
                <strong style="color:var(--text-primary);">${a.percentage}%</strong>
              </div>
            `).join('')}
          </div>
        </div>
      </div>

      <div style="margin-top:24px; border-top:1px solid var(--border-color); padding-top:20px;">
        <h4 style="font-size:14px; font-weight:600; margin-bottom:12px; color:var(--text-primary);">หุ้นที่แนะนำสำหรับคุณ (สอดคล้องกับพอร์ตโฟลิโอ)</h4>
        <div class="stock-list" style="display:grid; grid-template-columns:1fr 1fr; gap:12px;" id="recom-profile-stocks">
          <!-- Populated in JS below -->
        </div>
      </div>
    `;

    elements.quizContainer.appendChild(resultCard);

    // Render recommended stocks list inside the results view
    const recomStocksContainer = document.getElementById('recom-profile-stocks');
    data.recommendedStocks.forEach(ticker => {
      const stock = state.stocks.find(s => s.ticker === ticker);
      if (stock) {
        const row = document.createElement('div');
        row.className = 'stock-row';
        row.style.margin = '0';
        row.onclick = () => openStockModal(stock.ticker);
        
        const prefix = stock.change >= 0 ? '+' : '';
        const categoryLabel = stock.category === 'dividend' ? 'ปันผลดี' : stock.category === 'growth' ? 'เติบโตสูง' : 'เสี่ยงสูง';
        
        row.innerHTML = `
          <div class="stock-info">
            <div class="stock-icon">${stock.ticker}</div>
            <div class="stock-meta">
              <span class="stock-symbol">${stock.ticker}</span>
              <span class="stock-badge badge-${stock.category}" style="font-size:9px; padding:1px 6px;">${categoryLabel}</span>
            </div>
          </div>
          <div class="stock-values">
            <span class="stock-price-val" style="font-size:13px;">฿${stock.price.toFixed(2)}</span>
            <span class="stock-pct-val ${stock.change >= 0 ? 'up' : 'down'}" style="font-size:11px;">${prefix}${stock.change.toFixed(2)}%</span>
          </div>
        `;
        recomStocksContainer.appendChild(row);
      }
    });

    // Render allocation chart
    window.StockCharts.renderAllocationChart('allocation-donut-chart', data.allocation);

    // Retest Button listener
    document.getElementById('re-test-btn').onclick = async () => {
      try {
        const response = await fetch('api.php?action=save_profile', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ risk_profile: 'null' })
        });
        const rdata = await response.json();
        if (rdata.success) {
          state.riskProfile = null;
          state.currentQuestionIndex = 0;
          state.quizAnswers = [];
          renderProfiler();
        }
      } catch (e) {
        console.error(e);
      }
    };
  }

  // Portfolio Dashboard View Renderer
  function renderPortfolio() {
    updatePortfolioSummary();
    renderPortfolioTable();
  }

  function updatePortfolioSummary() {
    if (!elements.portfolioGrid) return;
    
    // Calculate values
    let totalInvested = 0;
    let currentEquity = 0;
    
    state.portfolio.forEach(hold => {
      const liveStock = state.stocks.find(s => s.ticker === hold.ticker);
      totalInvested += hold.shares * hold.avgPrice;
      currentEquity += hold.shares * (liveStock ? liveStock.price : hold.avgPrice);
    });

    const totalPortfolioVal = currentEquity + state.cash;
    const profitLoss = currentEquity - totalInvested;
    const profitLossPct = totalInvested > 0 ? (profitLoss / totalInvested) * 100 : 0;
    
    elements.portfolioCash.textContent = `฿${state.cash.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    elements.portfolioTotalEquity.textContent = `฿${currentEquity.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    elements.totalPortfolioValue.textContent = `฿${totalPortfolioVal.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    
    const plPrefix = profitLoss >= 0 ? '+' : '';
    elements.portfolioProfitLoss.textContent = `${plPrefix}฿${profitLoss.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })} (${plPrefix}${profitLossPct.toFixed(2)}%)`;
    elements.portfolioProfitLoss.className = `mini-card-val ${profitLoss >= 0 ? 'up' : 'down'}`;

    // Render portfolio asset distribution donut chart
    window.StockCharts.renderPortfolioDonut('portfolio-donut-container', state.portfolio);
  }

  function renderPortfolioTable() {
    elements.portfolioTableBody.innerHTML = '';
    
    if (state.portfolio.length === 0) {
      elements.portfolioTableBody.innerHTML = `
        <tr>
          <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 40px 0;">
            <div class="empty-state">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-wallet"><path d="M21 12V7H5a2 2 0 0 1 0-4h14v4"></path><path d="M3 5v14a2 2 0 0 0 2 2h16v-5"></path><path d="M18 12a2 2 0 0 0 0 4h4v-4Z"></path></svg>
              <div class="empty-state-title">ไม่มีหุ้นในพอร์ตโฟลิโอของคุณ</div>
              <div class="empty-state-desc">คุณสามารถเริ่มต้นด้วยการจำลองซื้อหุ้นจากการแนะนำ</div>
              <button class="btn btn-primary" onclick="window.StockApp.navigateTo('recommendations')" style="margin-top:8px;">ดูคำแนะนำหุ้น</button>
            </div>
          </td>
        </tr>
      `;
      return;
    }

    state.portfolio.forEach(hold => {
      const stock = state.stocks.find(s => s.ticker === hold.ticker);
      if (!stock) return;

      const currentValue = hold.shares * stock.price;
      const profitLoss = currentValue - (hold.shares * hold.avgPrice);
      const profitLossPct = ((stock.price - hold.avgPrice) / hold.avgPrice) * 100;
      const plPrefix = profitLoss >= 0 ? '+' : '';

      const tr = document.createElement('tr');
      tr.style.cursor = 'pointer';
      tr.setAttribute('data-ticker', stock.ticker);
      tr.onclick = () => openStockModal(stock.ticker);

      tr.innerHTML = `
        <td>
          <div class="portfolio-asset-info">
            <div class="stock-icon" style="width:34px; height:34px; font-size:11px;">${stock.ticker}</div>
            <div style="display:flex; flex-direction:column;">
              <span style="font-weight:700;">${stock.ticker}</span>
              <span style="font-size:10px; color:var(--text-muted);">${stock.name}</span>
            </div>
          </div>
        </td>
        <td style="font-family:var(--font-title); font-weight:600;">${hold.shares.toLocaleString()}</td>
        <td style="font-family:var(--font-title); font-weight:600;">฿${hold.avgPrice.toFixed(2)}</td>
        <td style="font-family:var(--font-title); font-weight:600;" class="stock-price-val">฿${stock.price.toFixed(2)}</td>
        <td style="font-family:var(--font-title); font-weight:600;">฿${currentValue.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
        <td style="font-family:var(--font-title); font-weight:700;" class="stock-pct-val ${profitLoss >= 0 ? 'up' : 'down'}">
          ${plPrefix}฿${profitLoss.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })} (${plPrefix}${profitLossPct.toFixed(1)}%)
        </td>
      `;
      elements.portfolioTableBody.appendChild(tr);
    });
  }

  // Watchlist View Renderer
  function renderWatchlist() {
    elements.watchlistList.innerHTML = '';
    
    if (state.watchlist.size === 0) {
      elements.watchlistList.innerHTML = `
        <div class="empty-state" style="grid-column: span 3; padding: 60px 0;">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-heart"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"></path></svg>
          <div class="empty-state-title">รายการเฝ้าดูว่างเปล่า</div>
          <div class="empty-state-desc">กดรูปหัวใจในหน้ารายละเอียดหุ้นเพื่อบันทึกหุ้นมาไว้ที่นี่</div>
          <button class="btn btn-primary" onclick="window.StockApp.navigateTo('recommendations')" style="margin-top:8px;">สำรวจหุ้นน่าสนใจ</button>
        </div>
      `;
      return;
    }

    state.watchlist.forEach(ticker => {
      const stock = state.stocks.find(s => s.ticker === ticker);
      if (!stock) return;

      const card = document.createElement('div');
      card.className = 'glass-card';
      card.setAttribute('data-ticker', stock.ticker);
      card.onclick = () => openStockModal(stock.ticker);
      
      const changePrefix = stock.change >= 0 ? '+' : '';
      const categoryLabel = stock.category === 'dividend' ? 'ปันผลสูง' : stock.category === 'growth' ? 'เติบโตดี' : 'เทค/ความเสี่ยงสูง';

      card.innerHTML = `
        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
          <div style="display:flex; gap:12px; align-items:center;">
            <div class="stock-icon">${stock.ticker}</div>
            <div style="display:flex; flex-direction:column; gap:4px;">
              <span class="stock-symbol" style="font-size:16px;">${stock.ticker}</span>
              <span class="stock-badge badge-${stock.category}">${categoryLabel}</span>
            </div>
          </div>
          <div style="text-align:right;">
            <div class="stock-price-val" style="font-size:16px; font-weight:700;">฿${stock.price.toFixed(2)}</div>
            <div class="stock-pct-val ${stock.change >= 0 ? 'up' : 'down'}" style="font-size:12px; font-weight:600;">
              ${changePrefix}${stock.change.toFixed(2)}%
            </div>
          </div>
        </div>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; border-top:1px solid var(--border-color); margin-top:16px; padding-top:12px; font-size:11px; color:var(--text-muted);">
          <div>P/E: <strong style="color:var(--text-primary);">${stock.pe}x</strong></div>
          <div>Consensus: <strong style="color:var(--color-brand);">${stock.consensus}</strong></div>
        </div>
      `;
      elements.watchlistList.appendChild(card);
    });
  }

  // Stock details Modal Manager & Buying/Selling
  function openStockModal(ticker) {
    const stock = state.stocks.find(s => s.ticker === ticker);
    if (!stock) return;

    state.modalStock = stock;
    state.tradeType = 'buy';
    state.tradeQuantity = 100;
    state.modalChartType = 'area';
    state.modalChartDuration = '3M';

    // Populate metadata
    elements.modalTicker.textContent = stock.ticker;
    elements.modalName.textContent = stock.name;
    
    const catLabel = stock.category === 'dividend' ? 'ปันผลดี (Dividend)' : stock.category === 'growth' ? 'เติบโตสูง (Growth)' : 'ความเสี่ยงสูง (High Risk)';
    elements.modalBadge.className = `stock-badge badge-${stock.category}`;
    elements.modalBadge.textContent = catLabel;

    elements.modalPrice.textContent = `฿${stock.price.toFixed(2)}`;
    
    const changePrefix = stock.change >= 0 ? '+' : '';
    elements.modalChange.textContent = `${changePrefix}${stock.change.toFixed(2)}%`;
    elements.modalChange.className = stock.change >= 0 ? 'up' : 'down';

    elements.modalPe.textContent = `${stock.pe}x`;
    elements.modalDivYield.textContent = `${stock.dividendYield}%`;
    elements.modalMarketCap.textContent = stock.marketCap;
    elements.modalBeta.textContent = stock.beta;
    elements.modalConsensus.textContent = stock.consensus;
    elements.modalDescription.textContent = stock.description;

    // Reset input fields
    elements.tradeInputQty.value = state.tradeQuantity;

    // Watchlist state indicator
    updateWatchlistButtonState();

    // Set active trade tab buttons
    setTradeTab(state.tradeType);

    // Show modal
    elements.modal.classList.add('active');

    // Render Charts
    renderModalPriceChart();
    window.StockCharts.renderFinancialsChart('financial-bar-chart', stock);
  }

  function updateWatchlistButtonState() {
    if (state.watchlist.has(state.modalStock.ticker)) {
      elements.watchlistToggleBtn.classList.add('active');
      elements.watchlistToggleBtn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"></path></svg>`;
    } else {
      elements.watchlistToggleBtn.classList.remove('active');
      elements.watchlistToggleBtn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"></path></svg>`;
    }
  }

  function setTradeTab(type) {
    state.tradeType = type;
    if (type === 'buy') {
      elements.tradeTabBuy.classList.add('active', 'buy');
      elements.tradeTabSell.classList.remove('active', 'sell');
      elements.tradeActionBtn.className = 'btn btn-success';
      elements.tradeActionBtn.style.width = '100%';
      elements.tradeActionBtn.textContent = 'ยืนยันสั่งซื้อหุ้น (Buy)';
    } else {
      elements.tradeTabBuy.classList.remove('active', 'buy');
      elements.tradeTabSell.classList.add('active', 'sell');
      elements.tradeActionBtn.className = 'btn btn-danger';
      elements.tradeActionBtn.style.width = '100%';
      elements.tradeActionBtn.textContent = 'ยืนยันขายหุ้น (Sell)';
    }
    updateTradeDetails();
  }

  function updateTradeDetails() {
    if (!state.modalStock) return;
    
    const qty = parseInt(elements.tradeInputQty.value) || 0;
    state.tradeQuantity = qty;

    const price = state.modalStock.price;
    const total = qty * price;
    elements.tradeEstimatedTotal.textContent = `฿${total.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

    if (state.tradeType === 'buy') {
      elements.tradeCashLabel.textContent = 'เงินสดที่มีอยู่:';
      elements.tradeCashVal.textContent = `฿${state.cash.toLocaleString(undefined, { maximumFractionDigits: 2 })}`;
      elements.tradeCashVal.style.color = state.cash >= total ? 'var(--color-success)' : 'var(--color-danger)';
      
      const holding = state.portfolio.find(h => h.ticker === state.modalStock.ticker);
      elements.tradeSharesOwnedLabel.textContent = 'จำนวนที่ครอบครอง:';
      elements.tradeSharesOwnedVal.textContent = holding ? `${holding.shares.toLocaleString()} หุ้น` : '0 หุ้น';
      elements.tradeSharesOwnedVal.style.color = '';
    } else {
      const holding = state.portfolio.find(h => h.ticker === state.modalStock.ticker);
      const owned = holding ? holding.shares : 0;
      
      elements.tradeCashLabel.textContent = 'จำนวนที่ครอบครอง:';
      elements.tradeCashVal.textContent = `${owned.toLocaleString()} หุ้น`;
      elements.tradeCashVal.style.color = owned >= qty ? 'var(--color-success)' : 'var(--color-danger)';
      
      elements.tradeSharesOwnedLabel.textContent = 'เงินสดที่จะได้รับ:';
      elements.tradeSharesOwnedVal.textContent = `฿${total.toLocaleString(undefined, { maximumFractionDigits: 2 })}`;
      elements.tradeSharesOwnedVal.style.color = 'var(--color-success)';
    }
  }

  function renderModalPriceChart() {
    window.StockCharts.renderHistoricalChart('stock-detail-chart', state.modalStock, state.modalChartType, state.modalChartDuration);
  }

  async function executeTrade() {
    const qty = state.tradeQuantity;
    if (qty <= 0) {
      showToast('กรุณาระบุจำนวนหุ้นที่ถูกต้อง', 'error');
      return;
    }

    try {
      const response = await fetch('api.php?action=trade', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          ticker: state.modalStock.ticker,
          type: state.tradeType,
          quantity: qty
        })
      });
      const data = await response.json();
      
      if (data.success) {
        showToast(data.message, 'success');
        
        // Refresh User state
        await fetchUserState();
        updateHeader();
        updateTradeDetails();
        
        if (state.activeView === 'portfolio') {
          renderPortfolio();
        }
      } else {
        showToast(data.error || 'การส่งคำสั่งล้มเหลว', 'error');
      }
    } catch (e) {
      console.error(e);
      showToast('เกิดข้อผิดพลาดในการเชื่อมต่อเซิร์ฟเวอร์เพื่อซื้อขาย', 'error');
    }
  }

  async function toggleWatchlist() {
    const ticker = state.modalStock.ticker;
    try {
      const response = await fetch('api.php?action=watchlist_toggle', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ ticker: ticker })
      });
      const data = await response.json();
      
      if (data.success) {
        showToast(data.message, 'success');
        await fetchUserState();
        updateWatchlistButtonState();
        
        if (state.activeView === 'watchlist') {
          renderWatchlist();
        }
      }
    } catch (e) {
      console.error(e);
    }
  }

  function setupEventListeners() {
    initElements();

    // 1. Sidebar Links Navigation
    elements.sidebarLinks.forEach(link => {
      link.addEventListener('click', (e) => {
        e.preventDefault();
        const view = link.getAttribute('data-view');
        navigateTo(view);
      });
    });

    // 2. Search Box recommendations filter
    elements.searchBar.addEventListener('input', () => {
      if (state.activeView !== 'recommendations') {
        navigateTo('recommendations');
      } else {
        renderRecommendations();
      }
    });

    // 3. Category Buttons Filter Recommendations
    elements.recomFilters.forEach(tab => {
      tab.onclick = () => {
        elements.recomFilters.forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        activeRecomCategory = tab.getAttribute('data-category');
        renderRecommendations();
      };
    });

    // 4. Modal Close events
    elements.modalCloseBtn.onclick = () => {
      elements.modal.classList.remove('active');
      window.StockCharts.destroyChart('stock-detail-chart');
      window.StockCharts.destroyChart('financial-bar-chart');
      state.modalStock = null;
    };

    elements.modal.onclick = (e) => {
      if (e.target === elements.modal) {
        elements.modalCloseBtn.onclick();
      }
    };

    // 5. Watchlist Button Toggle
    elements.watchlistToggleBtn.onclick = () => {
      toggleWatchlist();
    };

    // 6. Buy / Sell Form toggle tabs
    elements.tradeTabBuy.onclick = () => setTradeTab('buy');
    elements.tradeTabSell.onclick = () => setTradeTab('sell');

    elements.tradeInputQty.addEventListener('input', () => {
      updateTradeDetails();
    });

    elements.tradeActionBtn.onclick = () => {
      executeTrade();
    };

    // 7. Modal price chart period buttons
    const periodBtns = document.querySelectorAll('.chart-period-btn[data-duration]');
    periodBtns.forEach(btn => {
      btn.onclick = () => {
        periodBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        state.modalChartDuration = btn.getAttribute('data-duration');
        renderModalPriceChart();
      };
    });

    // Chart style type buttons
    const chartTypeBtns = document.querySelectorAll('[data-chart-type]');
    chartTypeBtns.forEach(btn => {
      btn.onclick = () => {
        chartTypeBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        state.modalChartType = btn.getAttribute('data-chart-type');
        renderModalPriceChart();
      };
    });

    // 8. Auth / Profile Modal Listeners
    elements.userProfileBtn.onclick = () => {
      elements.authModal.classList.add('active');
      renderAuthModalContent();
    };

    elements.authModalClose.onclick = () => {
      elements.authModal.classList.remove('active');
    };

    elements.authModal.onclick = (e) => {
      if (e.target === elements.authModal) {
        elements.authModalClose.onclick();
      }
    };

    elements.authTabLogin.onclick = () => setAuthTab('login');
    elements.authTabRegister.onclick = () => setAuthTab('register');

    elements.authForm.onsubmit = (e) => {
      e.preventDefault();
      handleAuthSubmit();
    };

    elements.authLogoutBtn.onclick = () => {
      handleLogout();
    };
  }

  // Initialize App on DOM Load
  document.addEventListener('DOMContentLoaded', async () => {
    setupEventListeners();
    
    // Initial fetch of data from backend API
    await fetchMarketData();
    await fetchUserState();
    
    updateHeader();
    navigateTo('dashboard');
    
    // Launch background pricing sync loop with backend SQLite changes
    startSyncLoop();
  });

  // Expose public navigateTo to let HTML button work
  window.StockApp = {
    navigateTo
  };
})();
