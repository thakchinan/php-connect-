// charts.js - Charts manager wrapping ApexCharts for Stock Recommendation Web App
(function() {
  // Store chart instances globally to clean them up properly before re-rendering
  const activeCharts = {};

  function destroyChart(id) {
    if (activeCharts[id]) {
      activeCharts[id].destroy();
      delete activeCharts[id];
    }
  }

  // 1. Historical Stock Price Chart (Area, Line or Candlestick)
  function renderHistoricalChart(elementId, stock, type = 'area', duration = '3M') {
    destroyChart(elementId);

    const container = document.getElementById(elementId);
    if (!container) return;
    container.innerHTML = '';

    // Filter stock history based on duration
    let historyPoints = [...stock.history];
    const pointsCount = historyPoints.length;
    
    if (duration === '1W') {
      historyPoints = historyPoints.slice(pointsCount - 7);
    } else if (duration === '1M') {
      historyPoints = historyPoints.slice(pointsCount - 30);
    } else if (duration === '3M') {
      // Keep all 90 days
    }

    const prices = historyPoints.map(p => p.y);
    const minVal = Math.min(...prices) * 0.98;
    const maxVal = Math.max(...prices) * 1.02;

    let series, chartType, plotOptions = {}, stroke = {}, colors = [];

    // Color based on performance (compare first and last visible points)
    const isUp = historyPoints[historyPoints.length - 1].y >= historyPoints[0].y;
    const lineAccent = isUp ? '#10b981' : '#ef4444';
    const gradientAccent = isUp ? 'rgba(16, 185, 129, 0.2)' : 'rgba(239, 68, 68, 0.2)';

    if (type === 'candle') {
      chartType = 'candlestick';
      // Map candles data (using slice for matching duration)
      let candleData = [...stock.candles];
      if (duration === '1W') {
        candleData = candleData.slice(candleData.length - 7);
      } else if (duration === '1M') {
        candleData = candleData.slice(candleData.length - 30);
      }
      series = [{
        name: stock.ticker,
        data: candleData
      }];
      colors = ['#10b981', '#ef4444'];
    } else {
      chartType = 'area';
      series = [{
        name: 'Price (THB)',
        data: historyPoints
      }];
      stroke = {
        curve: 'smooth',
        width: 2.5
      };
      colors = [lineAccent];
    }

    const options = {
      series: series,
      chart: {
        type: chartType,
        height: '100%',
        toolbar: { show: false },
        animations: {
          enabled: true,
          easing: 'easeinout',
          speed: 800
        },
        background: 'transparent',
        foreColor: '#9ca3af'
      },
      colors: colors,
      grid: {
        borderColor: 'rgba(255, 255, 255, 0.05)',
        strokeDashArray: 4,
        xaxis: { lines: { show: false } },
        yaxis: { lines: { show: true } }
      },
      stroke: stroke,
      plotOptions: plotOptions,
      fill: {
        type: type === 'candle' ? 'solid' : 'gradient',
        gradient: {
          shadeIntensity: 1,
          opacityFrom: 0.45,
          opacityTo: 0.05,
          stops: [0, 95, 100]
        }
      },
      xaxis: {
        type: 'datetime',
        labels: {
          style: {
            colors: '#6b7280',
            fontSize: '10px'
          }
        },
        axisBorder: { show: false },
        axisTicks: { show: false }
      },
      yaxis: {
        min: minVal,
        max: maxVal,
        labels: {
          formatter: (v) => v.toFixed(2) + ' ฿',
          style: {
            colors: '#6b7280',
            fontSize: '10px'
          }
        }
      },
      tooltip: {
        theme: 'dark',
        x: { format: 'dd MMM yyyy' },
        y: {
          formatter: (v) => v.toFixed(2) + ' THB'
        }
      }
    };

    const chart = new ApexCharts(container, options);
    chart.render();
    activeCharts[elementId] = chart;
  }

  // 2. Risk Profiler Asset Allocation (Donut Chart)
  function renderAllocationChart(elementId, allocationList) {
    destroyChart(elementId);

    const container = document.getElementById(elementId);
    if (!container) return;
    container.innerHTML = '';

    const series = allocationList.map(a => a.percentage);
    const labels = allocationList.map(a => a.asset);

    const options = {
      series: series,
      labels: labels,
      chart: {
        type: 'donut',
        height: 280,
        background: 'transparent',
        foreColor: '#f3f4f6'
      },
      colors: ['#6366f1', '#10b981', '#f59e0b', '#3b82f6', '#ec4899'],
      stroke: {
        show: true,
        colors: ['#0f1622'],
        width: 3
      },
      plotOptions: {
        pie: {
          donut: {
            size: '72%',
            labels: {
              show: true,
              name: {
                show: true,
                fontSize: '14px',
                fontFamily: 'Plus Jakarta Sans',
                color: '#9ca3af'
              },
              value: {
                show: true,
                fontSize: '22px',
                fontFamily: 'Outfit',
                fontWeight: 700,
                color: '#fff',
                formatter: (val) => val + '%'
              },
              total: {
                show: true,
                label: 'สัดส่วน',
                color: '#9ca3af',
                formatter: () => '100%'
              }
            }
          }
        }
      },
      dataLabels: { enabled: false },
      legend: {
        show: true,
        position: 'bottom',
        fontSize: '11px',
        labels: { colors: '#9ca3af' },
        markers: { radius: 12 }
      },
      tooltip: {
        theme: 'dark',
        y: {
          formatter: (v) => v + '%'
        }
      }
    };

    const chart = new ApexCharts(container, options);
    chart.render();
    activeCharts[elementId] = chart;
  }

  // 3. Stock Detail Financials (Revenue vs Net Profit Double Bar Chart)
  function renderFinancialsChart(elementId, stock) {
    destroyChart(elementId);

    const container = document.getElementById(elementId);
    if (!container) return;
    container.innerHTML = '';

    const years = stock.financials.years;
    const revenue = stock.financials.revenue;
    const netProfit = stock.financials.netProfit;

    // Detect scale unit (Trillion/Billion vs Billion/Million depending on stock size)
    const revUnit = stock.ticker === 'PTT' || stock.ticker === 'DELTA' ? 'ล้านล้านบาท' : 'พันล้านบาท';
    const profitUnit = 'พันล้านบาท';

    const options = {
      series: [
        {
          name: `รายได้รวม (${revUnit})`,
          data: revenue
        },
        {
          name: `กำไรสุทธิ (${profitUnit})`,
          data: netProfit.map(v => stock.ticker === 'PTT' || stock.ticker === 'DELTA' ? v / 1000 : v) // Align units roughly
        }
      ],
      chart: {
        type: 'bar',
        height: 240,
        toolbar: { show: false },
        background: 'transparent',
        foreColor: '#9ca3af'
      },
      plotOptions: {
        bar: {
          horizontal: false,
          columnWidth: '55%',
          endingShape: 'rounded',
          borderRadius: 4
        }
      },
      colors: ['#6366f1', '#10b981'],
      dataLabels: { enabled: false },
      stroke: {
        show: true,
        width: 2,
        colors: ['transparent']
      },
      xaxis: {
        categories: years,
        labels: {
          style: { colors: '#6b7280', fontSize: '11px' }
        }
      },
      yaxis: {
        labels: {
          style: { colors: '#6b7280', fontSize: '11px' }
        }
      },
      fill: { opacity: 0.85 },
      grid: {
        borderColor: 'rgba(255, 255, 255, 0.05)',
        strokeDashArray: 4
      },
      legend: {
        show: true,
        position: 'top',
        fontSize: '11px',
        labels: { colors: '#9ca3af' }
      },
      tooltip: {
        theme: 'dark',
        y: {
          formatter: function(val, { seriesIndex }) {
            return val.toFixed(2) + (seriesIndex === 0 ? ` ${revUnit}` : ` ${profitUnit}`);
          }
        }
      }
    };

    const chart = new ApexCharts(container, options);
    chart.render();
    activeCharts[elementId] = chart;
  }

  // 4. Portfolio Asset Distribution (Donut Chart)
  function renderPortfolioDonut(elementId, holdings) {
    destroyChart(elementId);

    const container = document.getElementById(elementId);
    if (!container || !holdings || holdings.length === 0) {
      if (container) {
        container.innerHTML = `
          <div class="empty-state">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pie-chart"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path><path d="M22 12A10 10 0 0 0 12 2v10z"></path></svg>
            <div class="empty-state-title">ไม่มีข้อมูลสัดส่วนหุ้น</div>
            <div class="empty-state-desc">สัดส่วนพอร์ตจะปรากฏขึ้นเมื่อคุณซื้อหุ้น</div>
          </div>
        `;
      }
      return;
    }

    const series = holdings.map(h => h.shares * h.avgPrice);
    const labels = holdings.map(h => h.ticker);
    const totalValue = series.reduce((a, b) => a + b, 0);

    const options = {
      series: series,
      labels: labels,
      chart: {
        type: 'donut',
        height: 250,
        background: 'transparent',
        foreColor: '#9ca3af'
      },
      colors: ['#6366f1', '#10b981', '#f59e0b', '#3b82f6', '#ec4899', '#8b5cf6', '#06b6d4'],
      stroke: {
        show: true,
        colors: ['#0f1622'],
        width: 3
      },
      plotOptions: {
        pie: {
          donut: {
            size: '70%',
            labels: {
              show: true,
              name: {
                show: true,
                fontSize: '12px',
                fontFamily: 'Plus Jakarta Sans',
                color: '#9ca3af'
              },
              value: {
                show: true,
                fontSize: '18px',
                fontFamily: 'Outfit',
                fontWeight: 700,
                color: '#fff',
                formatter: (val) => {
                  const pct = ((val / totalValue) * 100).toFixed(1);
                  return pct + '%';
                }
              },
              total: {
                show: true,
                label: 'มูลค่าพอร์ต',
                color: '#9ca3af',
                formatter: () => '฿' + totalValue.toLocaleString(undefined, { maximumFractionDigits: 0 })
              }
            }
          }
        }
      },
      dataLabels: { enabled: false },
      legend: {
        show: true,
        position: 'bottom',
        fontSize: '11px',
        labels: { colors: '#9ca3af' }
      },
      tooltip: {
        theme: 'dark',
        y: {
          formatter: (v) => '฿' + v.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })
        }
      }
    };

    const chart = new ApexCharts(container, options);
    chart.render();
    activeCharts[elementId] = chart;
  }

  // Expose to window
  window.StockCharts = {
    renderHistoricalChart,
    renderAllocationChart,
    renderFinancialsChart,
    renderPortfolioDonut,
    destroyChart
  };
})();
