# simulator.py - SQLite Initializer and Real-time Stock Price Simulator in Python
import sqlite3
import time
import random
import os
from datetime import datetime, timedelta

DB_FILE = 'database.db'

# Define default stock listings
STOCKS_DATA = [
    {
        'ticker': 'PTT',
        'name': 'PTT Public Company Limited',
        'category': 'dividend',
        'risk': 'Low',
        'price': 33.75,
        'market_cap': '964.0B',
        'pe': 11.2,
        'dividend_yield': 5.92,
        'eps': 3.01,
        'pbv': 0.88,
        'beta': 0.82,
        'consensus': 'Buy',
        'description': 'The largest oil and gas retail company in Thailand, operating under state ownership, offering stable dividends and a low-risk profile.'
    },
    {
        'ticker': 'ADVANC',
        'name': 'Advanced Info Service PCL',
        'category': 'dividend',
        'risk': 'Low',
        'price': 210.00,
        'market_cap': '624.5B',
        'pe': 21.5,
        'dividend_yield': 4.25,
        'eps': 9.77,
        'pbv': 7.2,
        'beta': 0.65,
        'consensus': 'Buy',
        'description': "Thailand's leading telecommunication provider (AIS), serving millions of subscribers with robust cashflow and high infrastructure barriers to entry."
    },
    {
        'ticker': 'SCC',
        'name': 'The Siam Cement PCL',
        'category': 'dividend',
        'risk': 'Low',
        'price': 232.00,
        'market_cap': '278.4B',
        'pe': 14.8,
        'dividend_yield': 3.88,
        'eps': 15.68,
        'pbv': 0.72,
        'beta': 0.95,
        'consensus': 'Hold',
        'description': 'One of the oldest industrial conglomerates in Thailand, specialized in construction materials, chemicals, and packaging.'
    },
    {
        'ticker': 'CPALL',
        'name': 'CP ALL Public Company Limited',
        'category': 'growth',
        'risk': 'Medium',
        'price': 57.25,
        'market_cap': '514.3B',
        'pe': 26.8,
        'dividend_yield': 2.27,
        'eps': 2.14,
        'pbv': 3.4,
        'beta': 0.92,
        'consensus': 'Strong Buy',
        'description': 'Operates "7-Eleven" convenience stores across Thailand, benefit from consumer spending growth and retail distribution supremacy.'
    },
    {
        'ticker': 'DELTA',
        'name': 'Delta Electronics (Thailand) PCL',
        'category': 'growth',
        'risk': 'High',
        'price': 88.50,
        'market_cap': '1.1T',
        'pe': 58.2,
        'dividend_yield': 0.73,
        'eps': 1.52,
        'pbv': 12.4,
        'beta': 1.78,
        'consensus': 'Hold',
        'description': 'Manufacturer of power supplies and electronic components. Driven by massive global demand in AI data centers, EV components, and electronics.'
    },
    {
        'ticker': 'AOT',
        'name': 'Airports of Thailand PCL',
        'category': 'growth',
        'risk': 'Medium',
        'price': 61.75,
        'market_cap': '882.1B',
        'pe': 43.1,
        'dividend_yield': 1.13,
        'eps': 1.43,
        'pbv': 7.9,
        'beta': 1.05,
        'consensus': 'Buy',
        'description': "Monopolizes Thailand's major international airports. Benefits highly from tourism growth and border openings."
    },
    {
        'ticker': 'BDMS',
        'name': 'Bangkok Dusit Medical Services PCL',
        'category': 'dividend',
        'risk': 'Low',
        'price': 27.50,
        'market_cap': '437.0B',
        'pe': 30.2,
        'dividend_yield': 2.91,
        'eps': 0.91,
        'pbv': 4.1,
        'beta': 0.58,
        'consensus': 'Strong Buy',
        'description': "Thailand's largest private hospital network group. Offers stable defensiveness during inflation and economic turbulence."
    },
    {
        'ticker': 'KBANK',
        'name': 'Kasikornbank Public Company Limited',
        'category': 'growth',
        'risk': 'Medium',
        'price': 134.50,
        'market_cap': '319.3B',
        'pe': 8.5,
        'dividend_yield': 4.83,
        'eps': 15.82,
        'pbv': 0.58,
        'beta': 1.15,
        'consensus': 'Buy',
        'description': "One of Thailand's leading commercial banks, spearheading digital banking through its mobile app \"K PLUS\" and SME portfolios."
    },
    {
        'ticker': 'GULF',
        'name': 'Gulf Energy Development PCL',
        'category': 'growth',
        'risk': 'Medium',
        'price': 46.50,
        'market_cap': '545.6B',
        'pe': 34.6,
        'dividend_yield': 1.94,
        'eps': 1.34,
        'pbv': 4.8,
        'beta': 1.12,
        'consensus': 'Buy',
        'description': 'Leading private power producer in Thailand, expanding actively into digital infrastructure, data centers, and telecom investments.'
    },
    {
        'ticker': 'COM7',
        'name': 'Com7 Public Company Limited',
        'category': 'highrisk',
        'risk': 'High',
        'price': 24.20,
        'market_cap': '58.1B',
        'pe': 18.5,
        'dividend_yield': 3.10,
        'eps': 1.31,
        'pbv': 7.2,
        'beta': 1.45,
        'consensus': 'Hold',
        'description': 'Major retailer of IT products and electronic devices (Banana IT, Studio7). High growth exposure to tech gadget consumer upgrades.'
    },
    {
        'ticker': 'HANA',
        'name': 'Hana Microelectronics PCL',
        'category': 'highrisk',
        'risk': 'High',
        'price': 38.50,
        'market_cap': '31.0B',
        'pe': 19.8,
        'dividend_yield': 2.60,
        'eps': 1.94,
        'pbv': 1.25,
        'beta': 1.62,
        'consensus': 'Hold',
        'description': 'Electronics Manufacturing Services (EMS) provider. High volatility depending on global semiconductor cycle and export exchange rates.'
    }
]

FINANCIALS_DATA = {
    'PTT': {'years': ['2023', '2024', '2025', '2026 (Est)'], 'revenue': [3.1, 3.3, 3.2, 3.4], 'net_profit': [112.0, 108.0, 115.0, 120.0]},
    'ADVANC': {'years': ['2023', '2024', '2025', '2026 (Est)'], 'revenue': [188.0, 192.0, 198.0, 205.0], 'net_profit': [29.0, 31.2, 32.5, 34.0]},
    'SCC': {'years': ['2023', '2024', '2025', '2026 (Est)'], 'revenue': [499.0, 521.0, 510.0, 530.0], 'net_profit': [25.9, 18.2, 22.0, 24.5]},
    'CPALL': {'years': ['2023', '2024', '2025', '2026 (Est)'], 'revenue': [820.0, 890.0, 950.0, 1020.0], 'net_profit': [18.5, 21.0, 23.4, 26.1]},
    'DELTA': {'years': ['2023', '2024', '2025', '2026 (Est)'], 'revenue': [118.0, 146.0, 178.0, 210.0], 'net_profit': [18.4, 21.2, 25.8, 30.5]},
    'AOT': {'years': ['2023', '2024', '2025', '2026 (Est)'], 'revenue': [48.1, 56.4, 65.0, 72.0], 'net_profit': [9.3, 16.5, 20.8, 23.5]},
    'BDMS': {'years': ['2023', '2024', '2025', '2026 (Est)'], 'revenue': [92.0, 98.5, 104.2, 110.0], 'net_profit': [12.6, 14.1, 15.3, 16.8]},
    'KBANK': {'years': ['2023', '2024', '2025', '2026 (Est)'], 'revenue': [185.0, 192.0, 199.0, 206.0], 'net_profit': [42.4, 44.8, 46.5, 48.9]},
    'GULF': {'years': ['2023', '2024', '2025', '2026 (Est)'], 'revenue': [102.0, 116.0, 125.0, 138.0], 'net_profit': [14.8, 16.2, 18.5, 20.8]},
    'COM7': {'years': ['2023', '2024', '2025', '2026 (Est)'], 'revenue': [62.8, 71.0, 78.5, 85.0], 'net_profit': [3.0, 3.3, 3.6, 4.0]},
    'HANA': {'years': ['2023', '2024', '2025', '2026 (Est)'], 'revenue': [25.5, 27.2, 29.0, 31.5], 'net_profit': [1.8, 2.1, 2.3, 2.6]}
}

INDICES_DATA = [
    {'name': 'SET Index', 'value': '1,382.46', 'change': '+0.45%', 'change_value': 6.22, 'is_positive': 1},
    {'name': 'S&P 500', 'value': '5,137.08', 'change': '+0.80%', 'change_value': 40.85, 'is_positive': 1},
    {'name': 'NASDAQ', 'value': '16,274.94', 'change': '+1.14%', 'change_value': 183.02, 'is_positive': 1},
    {'name': 'Gold Spot', 'value': '$2,328.60', 'change': '-0.32%', 'change_value': -7.48, 'is_positive': 0},
    {'name': 'Bitcoin (BTC)', 'value': '$68,450.00', 'change': '+2.41%', 'change_value': 1612.3, 'is_positive': 1}
]

NEWS_DATA = [
    {
        'title': 'FED hints at interest rate cuts later this year as inflation cools down.',
        'source': 'Global Finance',
        'time': '30 mins ago',
        'category': 'Macro Economy'
    },
    {
        'title': 'EV registrations in Thailand spike 140% YoY; DELTA & GULF set to benefit.',
        'source': 'Tech & Trade',
        'time': '2 hours ago',
        'category': 'Industry Growth'
    },
    {
        'title': 'SET index eyes 1,400 mark as foreign net buying resumes in banking sector.',
        'source': 'Thai Market Report',
        'time': '4 hours ago',
        'category': 'Market Trends'
    },
    {
        'title': 'PTT reports solid retail fuel margins despite production shifts in OPEC.',
        'source': 'Energy Inside',
        'time': '6 hours ago',
        'category': 'Energy'
    }
]

def init_db(conn):
    cursor = conn.cursor()
    
    # 1. Stocks table
    cursor.execute('''
    CREATE TABLE IF NOT EXISTS stocks (
        ticker TEXT PRIMARY KEY,
        name TEXT,
        category TEXT,
        risk TEXT,
        price REAL,
        prev_price REAL,
        change_pct REAL,
        market_cap TEXT,
        pe REAL,
        dividend_yield REAL,
        eps REAL,
        pbv REAL,
        beta REAL,
        consensus TEXT,
        description TEXT
    )
    ''')

    # 2. Price History table
    cursor.execute('''
    CREATE TABLE IF NOT EXISTS price_history (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        ticker TEXT,
        timestamp INTEGER,
        price REAL,
        open REAL,
        high REAL,
        low REAL,
        close REAL
    )
    ''')

    # 3. Financials table
    cursor.execute('''
    CREATE TABLE IF NOT EXISTS financials (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        ticker TEXT,
        year TEXT,
        revenue REAL,
        net_profit REAL
    )
    ''')

    # 4. Indices table
    cursor.execute('''
    CREATE TABLE IF NOT EXISTS indices (
        name TEXT PRIMARY KEY,
        value TEXT,
        change TEXT,
        change_value REAL,
        is_positive INTEGER
    )
    ''')

    # 5. News table
    cursor.execute('''
    CREATE TABLE IF NOT EXISTS news (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT,
        source TEXT,
        time TEXT,
        category TEXT
    )
    ''')

    # 6. User State table
    cursor.execute('''
    CREATE TABLE IF NOT EXISTS user_state (
        key TEXT PRIMARY KEY,
        value TEXT
    )
    ''')

    # 7. Users table
    cursor.execute('''
    CREATE TABLE IF NOT EXISTS users (
        username TEXT PRIMARY KEY,
        password TEXT,
        cash REAL DEFAULT 1000000.0,
        risk_profile TEXT DEFAULT NULL
    )
    ''')

    # 8. Portfolio holdings table
    cursor.execute('''
    CREATE TABLE IF NOT EXISTS portfolio (
        username TEXT,
        ticker TEXT,
        shares INTEGER,
        avg_price REAL,
        PRIMARY KEY (username, ticker)
    )
    ''')

    # 9. Watchlist table
    cursor.execute('''
    CREATE TABLE IF NOT EXISTS watchlist (
        username TEXT,
        ticker TEXT,
        PRIMARY KEY (username, ticker)
    )
    ''')
    
    conn.commit()

def populate_mock_data(conn):
    cursor = conn.cursor()
    
    # Check if data already exists
    cursor.execute("SELECT COUNT(*) FROM stocks")
    if cursor.fetchone()[0] > 0:
        print("Database already populated.")
        return

    print("Populating default stock listings...")
    # Populate stocks
    for s in STOCKS_DATA:
        cursor.execute('''
        INSERT INTO stocks (ticker, name, category, risk, price, prev_price, change_pct, market_cap, pe, dividend_yield, eps, pbv, beta, consensus, description)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ''', (
            s['ticker'], s['name'], s['category'], s['risk'], s['price'], s['price'], 0.0,
            s['market_cap'], s['pe'], s['dividend_yield'], s['eps'], s['pbv'], s['beta'], s['consensus'], s['description']
        ))

        # Generate 90 days of historical price ticks backwards
        now = datetime.now()
        base_price = s['price']
        volatility = 0.01 if s['risk'] == 'Low' else 0.015 if s['risk'] == 'Medium' else 0.025
        trend = 0.0005
        
        history_rows = []
        for i in range(90, 0, -1):
            date_point = now - timedelta(days=i)
            timestamp = int(date_point.timestamp() * 1000)
            
            # Simple random walk
            change = base_price * (trend + (random.random() - 0.48) * volatility)
            base_price = max(0.1, base_price + change)
            
            # Generate random OHLC for candles
            open_price = base_price
            close_price = max(0.1, open_price * (1 + (random.random() - 0.5) * volatility * 0.5))
            high_price = max(open_price, close_price) * (1 + random.random() * volatility * 0.25)
            low_price = min(open_price, close_price) * (1 - random.random() * volatility * 0.25)
            
            history_rows.append((
                s['ticker'], timestamp, round(base_price, 2), 
                round(open_price, 2), round(high_price, 2), round(low_price, 2), round(close_price, 2)
            ))
            
        cursor.executemany('''
        INSERT INTO price_history (ticker, timestamp, price, open, high, low, close)
        VALUES (?, ?, ?, ?, ?, ?, ?)
        ''', history_rows)

        # Financials
        fin = FINANCIALS_DATA[s['ticker']]
        for year_idx, year in enumerate(fin['years']):
            cursor.execute('''
            INSERT INTO financials (ticker, year, revenue, net_profit)
            VALUES (?, ?, ?, ?)
            ''', (s['ticker'], year, fin['revenue'][year_idx], fin['net_profit'][year_idx]))

    # Populate Indices
    print("Populating global indices...")
    for idx in INDICES_DATA:
        cursor.execute('''
        INSERT INTO indices (name, value, change, change_value, is_positive)
        VALUES (?, ?, ?, ?, ?)
        ''', (idx['name'], idx['value'], idx['change'], idx['change_value'], idx['is_positive']))

    # Populate News
    print("Populating financial news...")
    for news in NEWS_DATA:
        cursor.execute('''
        INSERT INTO news (title, source, time, category)
        VALUES (?, ?, ?, ?)
        ''', (news['title'], news['source'], news['time'], news['category']))

    # Populate User Defaults (Guest account)
    cursor.execute("INSERT OR IGNORE INTO users (username, password, cash, risk_profile) VALUES ('guest', 'guest', 1000000.0, NULL)")
    
    # Prepopulate watchlist linked to guest
    cursor.execute("INSERT OR IGNORE INTO watchlist (username, ticker) VALUES ('guest', 'PTT')")
    cursor.execute("INSERT OR IGNORE INTO watchlist (username, ticker) VALUES ('guest', 'DELTA')")
    cursor.execute("INSERT OR IGNORE INTO watchlist (username, ticker) VALUES ('guest', 'CPALL')")

    conn.commit()
    print("Database initialization and seed complete!")

def run_price_simulator(conn):
    print("Starting stock price live simulator. Press Ctrl+C to stop.")
    cursor = conn.cursor()
    
    while True:
        try:
            # 1. Fetch current stocks
            cursor.execute("SELECT ticker, price, risk FROM stocks")
            stocks = cursor.fetchall()
            
            now_ts = int(time.time() * 1000)
            
            for ticker, current_price, risk in stocks:
                # Volatility based on risk rating
                volatility = 0.005 if risk == 'Low' else 0.012 if risk == 'Medium' else 0.025
                change_percent = (random.random() - 0.49) * volatility
                
                prev_price = current_price
                new_price = round(current_price * (1 + change_percent), 2)
                change_pct = round(((new_price - prev_price) / prev_price) * 100, 2)
                
                # Update stocks price
                cursor.execute('''
                UPDATE stocks
                SET price = ?, prev_price = ?, change_pct = ?
                WHERE ticker = ?
                ''', (new_price, prev_price, change_pct, ticker))
                
                # Add price point to history (new minute/hour update)
                open_p = prev_price
                close_p = new_price
                high_p = round(max(open_p, close_p) * (1 + random.random() * volatility * 0.25), 2)
                low_p = round(min(open_p, close_p) * (1 - random.random() * volatility * 0.25), 2)

                cursor.execute('''
                INSERT INTO price_history (ticker, timestamp, price, open, high, low, close)
                VALUES (?, ?, ?, ?, ?, ?, ?)
                ''', (ticker, now_ts, new_price, open_p, high_p, low_p, close_p))
                
                # Prune history to keep only the last 150 records per stock
                cursor.execute('''
                DELETE FROM price_history 
                WHERE ticker = ? AND id NOT IN (
                    SELECT id FROM price_history WHERE ticker = ? ORDER BY timestamp DESC LIMIT 150
                )
                ''', (ticker, ticker))
            
            # 2. Simulate small index changes
            cursor.execute("SELECT name, value FROM indices")
            indices = cursor.fetchall()
            for name, val in indices:
                # Remove currency formatting
                raw_val = float(val.replace(',', '').replace('$', ''))
                change_pct = (random.random() - 0.48) * 0.002
                new_val = raw_val * (1 + change_pct)
                is_pos = 1 if new_val >= raw_val else 0
                change_val = round(new_val - raw_val, 2)
                change_str = f"{'+' if change_val >= 0 else ''}{round((change_val / raw_val) * 100, 2)}%"
                
                if 'Gold' in name or 'Bitcoin' in name:
                    formatted_val = f"${new_val:,.2f}"
                else:
                    formatted_val = f"{new_val:,.2f}"
                    
                cursor.execute('''
                UPDATE indices
                SET value = ?, change = ?, change_value = ?, is_positive = ?
                WHERE name = ?
                ''', (formatted_val, change_str, change_val, is_pos, name))

            conn.commit()
            print(f"[{datetime.now().strftime('%H:%M:%S')}] Simulated price updates committed.")
            
            time.sleep(4) # Run simulation step every 4 seconds
            
        except sqlite3.Error as e:
            print(f"Database error during simulation: {e}")
            conn.rollback()
            time.sleep(4)
        except KeyboardInterrupt:
            print("\nSimulation stopped.")
            break

def main():
    # Make sure we run in the correct workspace directory
    conn = sqlite3.connect(DB_FILE)
    try:
        init_db(conn)
        populate_mock_data(conn)
        run_price_simulator(conn)
    finally:
        conn.close()

if __name__ == '__main__':
    main()
