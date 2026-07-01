<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    // Helper: get current username from session
    private function getActiveUsername()
    {
        return session('username', 'guest');
    }

    // Helper: get user data from users table
    private function getUserData($username)
    {
        $result = DB::select("SELECT username, cash, risk_profile FROM users WHERE username = ?", [$username]);
        return count($result) > 0 ? (array)$result[0] : null;
    }

    // Helper: set user cash
    private function setUserCash($username, $cash)
    {
        DB::update("UPDATE users SET cash = ? WHERE username = ?", [$cash, $username]);
    }

    // Helper: set user risk profile
    private function setUserRiskProfile($username, $profile)
    {
        DB::update("UPDATE users SET risk_profile = ? WHERE username = ?", [$profile, $username]);
    }

    // GET /api/get_market_data
    public function getMarketData()
    {
        try {
            // 1. Fetch all stocks
            $stocks = DB::select("SELECT * FROM stocks");
            $stocksArray = [];

            foreach ($stocks as $stock) {
                $stock = (array)$stock;
                $ticker = $stock['ticker'];

                // Fetch price history
                $historyRows = DB::select("SELECT timestamp, price, open, high, low, close FROM price_history WHERE ticker = ? ORDER BY timestamp ASC", [$ticker]);
                $history = [];
                $candles = [];

                foreach ($historyRows as $row) {
                    $row = (array)$row;
                    $history[] = [
                        'x' => (int)$row['timestamp'],
                        'y' => (float)$row['price']
                    ];
                    $candles[] = [
                        'x' => (int)$row['timestamp'],
                        'y' => [
                            (float)$row['open'],
                            (float)$row['high'],
                            (float)$row['low'],
                            (float)$row['close']
                        ]
                    ];
                }

                // Fetch financials
                $finRows = DB::select("SELECT year, revenue, net_profit FROM financials WHERE ticker = ? ORDER BY year ASC", [$ticker]);
                $years = [];
                $revenue = [];
                $netProfit = [];

                foreach ($finRows as $frow) {
                    $frow = (array)$frow;
                    $years[] = $frow['year'];
                    $revenue[] = (float)$frow['revenue'];
                    $netProfit[] = (float)$frow['net_profit'];
                }

                $stock['history'] = $history;
                $stock['candles'] = $candles;
                $stock['financials'] = [
                    'years' => $years,
                    'revenue' => $revenue,
                    'netProfit' => $netProfit
                ];

                // Map database columns to match JSON camelCase outputs expected by React
                $stock['price'] = (float)$stock['price'];
                $stock['prevPrice'] = (float)$stock['prev_price'];
                $stock['change'] = (float)$stock['change_pct'];
                $stock['pe'] = (float)$stock['pe'];
                $stock['dividendYield'] = (float)$stock['dividend_yield'];
                $stock['eps'] = (float)$stock['eps'];
                $stock['pbv'] = (float)$stock['pbv'];
                $stock['beta'] = (float)$stock['beta'];

                $stocksArray[] = $stock;
            }

            // 2. Fetch market indices
            $indices = DB::select("SELECT * FROM indices");
            $indicesArray = [];
            foreach ($indices as $idx) {
                $idx = (array)$idx;
                $idx['changeValue'] = (float)$idx['change_value'];
                $idx['isPositive'] = (bool)$idx['is_positive'];
                $indicesArray[] = $idx;
            }

            // 3. Fetch latest financial news
            $news = DB::select("SELECT * FROM news ORDER BY id DESC");
            $newsArray = [];
            foreach ($news as $item) {
                $newsArray[] = (array)$item;
            }

            return response()->json([
                'success' => true,
                'stocks' => $stocksArray,
                'indices' => $indicesArray,
                'news' => $newsArray
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // GET /api/get_user_state
    public function getUserState()
    {
        try {
            $username = $this->getActiveUsername();
            $user = $this->getUserData($username);

            if (!$user) {
                if ($username === 'guest') {
                    DB::insert("INSERT OR IGNORE INTO users (username, password, cash, risk_profile) VALUES ('guest', 'guest', 1000000.0, NULL)");
                    $user = $this->getUserData('guest');
                } else {
                    session(['username' => 'guest']);
                    $username = 'guest';
                    $user = $this->getUserData('guest');
                }
            }

            $cash = (float)$user['cash'];
            $risk_profile = $user['risk_profile'];

            // Fetch watchlist
            $wlRows = DB::select("SELECT ticker FROM watchlist WHERE username = ?", [$username]);
            $watchlist = [];
            foreach ($wlRows as $wl) {
                $watchlist[] = $wl->ticker;
            }

            // Fetch portfolio
            $portRows = DB::select("SELECT ticker, shares, avg_price AS avgPrice FROM portfolio WHERE username = ?", [$username]);
            $portfolio = [];
            foreach ($portRows as $hold) {
                $hold = (array)$hold;
                $portfolio[] = [
                    'ticker' => $hold['ticker'],
                    'shares' => (int)$hold['shares'],
                    'avgPrice' => (float)$hold['avgPrice']
                ];
            }

            return response()->json([
                'success' => true,
                'username' => $username,
                'cash' => $cash,
                'riskProfile' => $risk_profile,
                'watchlist' => $watchlist,
                'portfolio' => $portfolio
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // POST /api/login
    public function login(Request $request)
    {
        $username = trim($request->input('username', ''));
        $password = trim($request->input('password', ''));

        if (empty($username) || empty($password)) {
            return response()->json(['success' => false, 'error' => 'กรุณากรอกข้อมูลให้ครบถ้วน']);
        }

        try {
            $result = DB::select("SELECT * FROM users WHERE username = ?", [$username]);
            if (count($result) === 0) {
                return response()->json(['success' => false, 'error' => 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง']);
            }

            $user = (array)$result[0];
            if (!password_verify($password, $user['password'])) {
                return response()->json(['success' => false, 'error' => 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง']);
            }

            session(['username' => $username]);
            return response()->json([
                'success' => true,
                'username' => $username,
                'message' => 'เข้าสู่ระบบสำเร็จ ยินดีต้อนรับกลับ!'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // POST /api/register
    public function register(Request $request)
    {
        $username = trim($request->input('username', ''));
        $password = trim($request->input('password', ''));
        $starting_cash = (float)$request->input('cash', 1000000.00);

        if (empty($username) || empty($password)) {
            return response()->json(['success' => false, 'error' => 'กรุณากรอกข้อมูลให้ครบถ้วน']);
        }

        if (strlen($username) < 3 || strlen($password) < 4) {
            return response()->json(['success' => false, 'error' => 'ชื่อผู้ใช้ต้องมีอย่างน้อย 3 ตัวอักษร และรหัสผ่าน 4 ตัวอักษร']);
        }

        try {
            $exists = DB::select("SELECT COUNT(*) as count FROM users WHERE username = ?", [$username]);
            if ((int)$exists[0]->count > 0) {
                return response()->json(['success' => false, 'error' => 'ชื่อผู้ใช้นี้มีคนใช้แล้ว']);
            }

            $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
            DB::insert("INSERT INTO users (username, password, cash, risk_profile) VALUES (?, ?, ?, NULL)", [
                $username, $hashed_pass, $starting_cash
            ]);

            session(['username' => $username]);
            return response()->json([
                'success' => true,
                'username' => $username,
                'message' => 'สมัครสมาชิกและเข้าสู่ระบบสำเร็จ!'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'Registration failed: ' . $e->getMessage()]);
        }
    }

    // GET /api/logout
    public function logout()
    {
        session()->forget('username');
        return response()->json([
            'success' => true,
            'message' => 'ออกจากระบบเรียบร้อยแล้ว พบกันใหม่โอกาสหน้า!'
        ]);
    }

    // POST /api/save_profile
    public function saveProfile(Request $request)
    {
        $username = $this->getActiveUsername();
        $profile = trim($request->input('risk_profile', ''));

        if (!in_array($profile, ['conservative', 'moderate', 'aggressive', 'null'])) {
            return response()->json(['success' => false, 'error' => 'Invalid profile type']);
        }

        try {
            $db_profile = ($profile === 'null') ? null : $profile;
            $this->setUserRiskProfile($username, $db_profile);
            return response()->json(['success' => true, 'message' => 'บันทึกระดับความเสี่ยงสำเร็จ']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // POST /api/watchlist_toggle
    public function watchlistToggle(Request $request)
    {
        $username = $this->getActiveUsername();
        $ticker = trim($request->input('ticker', ''));

        if (empty($ticker)) {
            return response()->json(['success' => false, 'error' => 'Invalid parameters']);
        }

        try {
            $exists = DB::select("SELECT COUNT(*) as count FROM watchlist WHERE username = ? AND ticker = ?", [$username, $ticker]);
            $hasItem = (int)$exists[0]->count > 0;

            if ($hasItem) {
                DB::delete("DELETE FROM watchlist WHERE username = ? AND ticker = ?", [$username, $ticker]);
                return response()->json(['success' => true, 'is_watchlist' => false, 'message' => "นำ $ticker ออกจากรายการเฝ้าดูแล้ว"]);
            } else {
                DB::insert("INSERT INTO watchlist (username, ticker) VALUES (?, ?)", [$username, $ticker]);
                return response()->json(['success' => true, 'is_watchlist' => true, 'message' => "เพิ่ม $ticker เข้าในรายการเฝ้าดูเรียบร้อย"]);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // POST /api/trade
    public function trade(Request $request)
    {
        $username = $this->getActiveUsername();
        $ticker = trim($request->input('ticker', ''));
        $tradeType = trim($request->input('type', ''));
        $qty = (int)$request->input('quantity', 0);

        if (empty($ticker) || !in_array($tradeType, ['buy', 'sell']) || $qty <= 0) {
            return response()->json(['success' => false, 'error' => 'Invalid parameters']);
        }

        try {
            return DB::transaction(function () use ($username, $ticker, $tradeType, $qty) {
                // Fetch stock price
                $stockRows = DB::select("SELECT price FROM stocks WHERE ticker = ?", [$ticker]);
                if (count($stockRows) === 0) {
                    return response()->json(['success' => false, 'error' => 'Stock not found']);
                }
                $price = (float)$stockRows[0]->price;
                $total_val = $qty * $price;

                // Fetch user data
                $user = $this->getUserData($username);
                $cash = (float)$user['cash'];

                // Fetch holding
                $holdRows = DB::select("SELECT shares, avg_price FROM portfolio WHERE username = ? AND ticker = ?", [$username, $ticker]);
                $holding = count($holdRows) > 0 ? (array)$holdRows[0] : null;
                $owned_shares = $holding ? (int)$holding['shares'] : 0;
                $avg_price = $holding ? (float)$holding['avg_price'] : 0.0;

                if ($tradeType === 'buy') {
                    if ($cash < $total_val) {
                        return response()->json(['success' => false, 'error' => 'เงินจำลองคงเหลือไม่เพียงพอสำหรับการสั่งซื้อ!']);
                    }

                    $new_cash = $cash - $total_val;
                    $this->setUserCash($username, $new_cash);

                    if ($holding) {
                        $new_shares = $owned_shares + $qty;
                        $new_avg = (($owned_shares * $avg_price) + $total_val) / $new_shares;
                        DB::update("UPDATE portfolio SET shares = ?, avg_price = ? WHERE username = ? AND ticker = ?", [
                            $new_shares, $new_avg, $username, $ticker
                        ]);
                    } else {
                        DB::insert("INSERT INTO portfolio (username, ticker, shares, avg_price) VALUES (?, ?, ?, ?)", [
                            $username, $ticker, $qty, $price
                        ]);
                    }

                    return response()->json([
                        'success' => true,
                        'message' => "ซื้อหุ้น $ticker สำเร็จ จำนวน " . number_format($qty) . " หุ้น ในราคา ฿" . number_format($price, 2)
                    ]);
                } else {
                    if ($owned_shares < $qty) {
                        return response()->json(['success' => false, 'error' => 'คุณมีจำนวนหุ้นในครอบครองไม่เพียงพอสำหรับการขาย!']);
                    }

                    $new_cash = $cash + $total_val;
                    $this->setUserCash($username, $new_cash);

                    $new_shares = $owned_shares - $qty;
                    if ($new_shares === 0) {
                        DB::delete("DELETE FROM portfolio WHERE username = ? AND ticker = ?", [$username, $ticker]);
                    } else {
                        DB::update("UPDATE portfolio SET shares = ? WHERE username = ? AND ticker = ?", [
                            $new_shares, $username, $ticker
                        ]);
                    }

                    return response()->json([
                        'success' => true,
                        'message' => "ขายหุ้น $ticker สำเร็จ จำนวน " . number_format($qty) . " หุ้น ในราคา ฿" . number_format($price, 2)
                    ]);
                }
            });
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'Transaction failed: ' . $e->getMessage()]);
        }
    }
}
