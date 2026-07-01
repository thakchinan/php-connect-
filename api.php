<?php
// api.php - PHP SQLite API Endpoint สำหรับระบบจำลองการลงทุน SmartInvest
// เริ่มต้นใช้งาน Session เพื่อใช้เก็บข้อมูลการล็อกอินของผู้ใช้
session_start();

// กำหนด Header ให้ส่งข้อมูลกลับเป็น JSON และอนุญาตการเข้าถึงจากโดเมนอื่น (CORS)
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // อนุญาตให้ทุก Domain ดึงข้อมูลได้
header('Access-Control-Allow-Headers: Content-Type'); // อนุญาต Header ประเภท Content-Type
header('Access-Control-Allow-Methods: GET, POST, OPTIONS'); // อนุญาต Method GET, POST และ OPTIONS

// หากเป็น OPTIONS Request (การตรวจสอบสิทธิ์ก่อนส่งข้อมูลจริงของ Browser) ให้จบการทำงานทันที
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// ชื่อไฟล์ฐานข้อมูล SQLite
$db_file = 'database.db';

// เชื่อมต่อฐานข้อมูล SQLite ด้วย PDO
try {
    $pdo = new PDO("sqlite:$db_file");
    // ตั้งค่าให้แจ้งเตือนข้อผิดพลาดในรูปแบบ Exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // ตั้งค่าให้ดึงข้อมูลออกมาเป็น Array แบบเอาชื่อคอลัมน์เป็น Key
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // หากเชื่อมต่อไม่สำเร็จ ส่งข้อความแจ้งเตือนความผิดพลาดกลับไป
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// ฟังก์ชันช่วยตรวจสอบผู้ใช้ปัจจุบันจาก Session (หากไม่มี ให้ใช้เป็น 'guest')
function getActiveUsername() {
    return isset($_SESSION['username']) ? $_SESSION['username'] : 'guest';
}

// ฟังก์ชันดึงข้อมูลผู้ใช้ (ชื่อ, เงินสด, ระดับความเสี่ยง) จากตาราง users
function getUserData($pdo, $username) {
    $stmt = $pdo->prepare("SELECT username, cash, risk_profile FROM users WHERE username = ?");
    $stmt->execute([$username]);
    return $stmt->fetch();
}

// ฟังก์ชันอัปเดตยอดเงินคงเหลือของผู้ใช้
function setUserCash($pdo, $username, $cash) {
    $stmt = $pdo->prepare("UPDATE users SET cash = ? WHERE username = ?");
    $stmt->execute([$cash, $username]);
}

// ฟังก์ชันอัปเดตระดับความเสี่ยงของผู้ใช้
function setUserRiskProfile($pdo, $username, $profile) {
    $stmt = $pdo->prepare("UPDATE users SET risk_profile = ? WHERE username = ?");
    $stmt->execute([$profile, $username]);
}

// รับค่า action จาก parameter ใน URL (เช่น api.php?action=login)
$action = isset($_GET['action']) ? $_GET['action'] : '';

// เลือกรันโค้ดตาม action ที่ส่งมา
switch ($action) {
    // ====================================================
    // สมัครสมาชิกใหม่
    // ====================================================
    case 'register':
        // อนุญาตเฉพาะ Request แบบ POST เท่านั้น
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            exit;
        }

        // ดึงข้อมูลดิบจาก JSON body ที่ส่งมาจาก Frontend
        $input = json_decode(file_get_contents('php://input'), true);
        $username = isset($input['username']) ? trim($input['username']) : '';
        $password = isset($input['password']) ? trim($input['password']) : '';
        $starting_cash = isset($input['cash']) ? (float)$input['cash'] : 1000000.00; // เงินทุนเริ่มต้น (ค่าเริ่มต้น 1,000,000)

        // ตรวจสอบว่ากรอกข้อมูลครบถ้วนหรือไม่
        if (empty($username) || empty($password)) {
            echo json_encode(['success' => false, 'error' => 'กรุณากรอกข้อมูลให้ครบถ้วน']);
            exit;
        }

        // ตรวจสอบความยาวของชื่อผู้ใช้และรหัสผ่าน
        if (strlen($username) < 3 || strlen($password) < 4) {
            echo json_encode(['success' => false, 'error' => 'ชื่อผู้ใช้ต้องมีอย่างน้อย 3 ตัวอักษร และรหัสผ่าน 4 ตัวอักษร']);
            exit;
        }

        try {
            // ตรวจสอบว่ามีชื่อผู้ใช้นี้อยู่ในระบบแล้วหรือยัง
            $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
            $check_stmt->execute([$username]);
            if ($check_stmt->fetchColumn() > 0) {
                echo json_encode(['success' => false, 'error' => 'ชื่อผู้ใช้นี้มีคนใช้แล้ว']);
                exit;
            }

            // เข้ารหัสผ่านให้ปลอดภัยด้วยฟังก์ชัน Hash
            $hashed_pass = password_hash($password, PASSWORD_DEFAULT);

            // บันทึกผู้ใช้ใหม่ลงในฐานข้อมูล
            $ins_stmt = $pdo->prepare("INSERT INTO users (username, password, cash, risk_profile) VALUES (?, ?, ?, NULL)");
            $ins_stmt->execute([$username, $hashed_pass, $starting_cash]);

            // บันทึกชื่อผู้ใช้เข้า Session เพื่อให้มีสถานะเป็นล็อกอินอยู่ทันที
            $_SESSION['username'] = $username;

            echo json_encode([
                'success' => true,
                'username' => $username,
                'message' => 'สมัครสมาชิกและเข้าสู่ระบบสำเร็จ!'
            ]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => 'Registration failed: ' . $e->getMessage()]);
        }
        break;

    // ====================================================
    // เข้าสู่ระบบ
    // ====================================================
    case 'login':
        // อนุญาตเฉพาะ Request แบบ POST เท่านั้น
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            exit;
        }

        // ดึงข้อมูลจาก JSON body ที่ส่งมาจาก Frontend
        $input = json_decode(file_get_contents('php://input'), true);
        $username = isset($input['username']) ? trim($input['username']) : '';
        $password = isset($input['password']) ? trim($input['password']) : '';

        // ตรวจสอบความถูกต้องของการกรอกข้อมูล
        if (empty($username) || empty($password)) {
            echo json_encode(['success' => false, 'error' => 'กรุณากรอกข้อมูลให้ครบถ้วน']);
            exit;
        }

        try {
            // ดึงข้อมูลผู้ใช้จากฐานข้อมูล
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            // ตรวจสอบว่าพบผู้ใช้ และรหัสผ่านถูกต้องตรงกันหรือไม่
            if (!$user || !password_verify($password, $user['password'])) {
                echo json_encode(['success' => false, 'error' => 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง']);
                exit;
            }

            // บันทึกผู้ใช้ลงใน Session ของเซิร์ฟเวอร์
            $_SESSION['username'] = $username;

            echo json_encode([
                'success' => true,
                'username' => $username,
                'message' => 'เข้าสู่ระบบสำเร็จ!'
            ]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => 'Login failed: ' . $e->getMessage()]);
        }
        break;

    // ====================================================
    // ออกจากระบบ
    // ====================================================
    case 'logout':
        // กำหนดสถานะผู้ใช้ใน Session ให้เป็น 'guest'
        $_SESSION['username'] = 'guest';
        echo json_encode([
            'success' => true,
            'message' => 'ออกจากระบบเรียบร้อย'
        ]);
        break;

    // ====================================================
    // ดึงข้อมูลตลาดหุ้นทั้งหมด (หุ้น, ดัชนี, ข่าว)
    // ====================================================
    case 'get_market_data':
        try {
            // 1. ดึงข้อมูลหุ้นทั้งหมดที่มีอยู่ในตาราง stocks
            $stocks_stmt = $pdo->query("SELECT * FROM stocks");
            $stocks = $stocks_stmt->fetchAll();
            
            // วนลูปเพื่อดึงข้อมูลประวัติราคาและผลประกอบการมาเพิ่มให้แต่ละหุ้น
            foreach ($stocks as &$stock) {
                // ดึงประวัติราคาทั้งหมดของหุ้นนั้นเรียงตามเวลา
                $hist_stmt = $pdo->prepare("SELECT timestamp, price, open, high, low, close FROM price_history WHERE ticker = ? ORDER BY timestamp ASC");
                $hist_stmt->execute([$stock['ticker']]);
                $history_rows = $hist_stmt->fetchAll();
                
                $history = [];
                $candles = [];
                foreach ($history_rows as $row) {
                    // ข้อมูลสำหรับกราฟเส้น (Line Chart)
                    $history[] = [
                        'x' => (int)$row['timestamp'],
                        'y' => (float)$row['price']
                    ];
                    // ข้อมูลสำหรับกราฟแท่งเทียน (Candlestick Chart)
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
                $stock['history'] = $history;
                $stock['candles'] = $candles;
                
                // ดึงข้อมูลทางการเงินย้อนหลัง (ปี, รายได้, กำไรสุทธิ) เพื่อใช้ทำกราฟการเงิน
                $fin_stmt = $pdo->prepare("SELECT year, revenue, net_profit FROM financials WHERE ticker = ? ORDER BY year ASC");
                $fin_stmt->execute([$stock['ticker']]);
                $fin_rows = $fin_stmt->fetchAll();
                
                $years = [];
                $revenue = [];
                $netProfit = [];
                foreach ($fin_rows as $frow) {
                    $years[] = $frow['year'];
                    $revenue[] = (float)$frow['revenue'];
                    $netProfit[] = (float)$frow['net_profit'];
                }
                
                $stock['financials'] = [
                    'years' => $years,
                    'revenue' => $revenue,
                    'netProfit' => $netProfit
                ];

                // แปลงค่าข้อมูลสำคัญของหุ้นให้เป็นประเภทตัวเลข (float)
                $stock['price'] = (float)$stock['price'];
                $stock['prevPrice'] = (float)$stock['prev_price'];
                $stock['change'] = (float)$stock['change_pct'];
                $stock['pe'] = (float)$stock['pe'];
                $stock['dividendYield'] = (float)$stock['dividend_yield'];
                $stock['eps'] = (float)$stock['eps'];
                $stock['pbv'] = (float)$stock['pbv'];
                $stock['beta'] = (float)$stock['beta'];
            }

            // 2. ดึงข้อมูลดัชนีตลาดหุ้น (ตาราง indices)
            $indices_stmt = $pdo->query("SELECT * FROM indices");
            $indices = $indices_stmt->fetchAll();
            foreach ($indices as &$idx) {
                $idx['changeValue'] = (float)$idx['change_value'];
                $idx['isPositive'] = (bool)$idx['is_positive'];
            }

            // 3. ดึงข่าวสารการลงทุนล่าสุด (ตาราง news) เรียงลำดับจากล่าสุด
            $news_stmt = $pdo->query("SELECT * FROM news ORDER BY id DESC");
            $news = $news_stmt->fetchAll();

            // ส่งข้อมูลทั้งหมดกลับไปที่ Frontend
            echo json_encode([
                'success' => true,
                'stocks' => $stocks,
                'indices' => $indices,
                'news' => $news
            ]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        break;

    // ====================================================
    // ดึงสถานะปัจจุบันของผู้ใช้ (เงินสด, ระดับความเสี่ยง, Watchlist, Portfolio)
    // ====================================================
    case 'get_user_state':
        try {
            $username = getActiveUsername();
            
            // ดึงข้อมูลผู้ใช้จากตาราง users
            $user = getUserData($pdo, $username);
            if (!$user) {
                // หากไม่เจอข้อมูลผู้ใช้ (เช่น ในกรณีของ guest ที่ยังไม่มีในตาราง) ให้สร้างขึ้นมาใหม่
                if ($username === 'guest') {
                    $pdo->query("INSERT OR IGNORE INTO users (username, password, cash, risk_profile) VALUES ('guest', 'guest', 1000000.0, NULL)");
                    $user = getUserData($pdo, 'guest');
                } else {
                    // หาก Session ผิดพลาด ให้รีเซ็ตผู้ใช้กลับไปเป็น guest
                    $_SESSION['username'] = 'guest';
                    $username = 'guest';
                    $user = getUserData($pdo, 'guest');
                }
            }

            $cash = (float)$user['cash'];
            $risk_profile = $user['risk_profile'];

            // ดึงรายการหุ้นที่ผู้ใช้คนนี้กดติดตามไว้ (Watchlist)
            $wl_stmt = $pdo->prepare("SELECT ticker FROM watchlist WHERE username = ?");
            $wl_stmt->execute([$username]);
            $watchlist_rows = $wl_stmt->fetchAll(PDO::FETCH_COLUMN);

            // ดึงข้อมูลหุ้นที่ผู้ใช้คนนี้ถือครองอยู่ (Portfolio)
            $port_stmt = $pdo->prepare("SELECT ticker, shares, avg_price AS avgPrice FROM portfolio WHERE username = ?");
            $port_stmt->execute([$username]);
            $portfolio = $port_stmt->fetchAll();
            foreach ($portfolio as &$hold) {
                $hold['shares'] = (int)$hold['shares'];
                $hold['avgPrice'] = (float)$hold['avgPrice'];
            }

            // ส่งข้อมูลสถานะปัจจุบันทั้งหมดกลับไป
            echo json_encode([
                'success' => true,
                'username' => $username,
                'cash' => $cash,
                'riskProfile' => $risk_profile,
                'watchlist' => $watchlist_rows,
                'portfolio' => $portfolio
            ]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        break;

    // ====================================================
    // การซื้อ/ขายหุ้น
    // ====================================================
    case 'trade':
        // อนุญาตเฉพาะ Request แบบ POST เท่านั้น
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            exit;
        }

        $username = getActiveUsername();
        $input = json_decode(file_get_contents('php://input'), true);
        $ticker = isset($input['ticker']) ? trim($input['ticker']) : '';
        $tradeType = isset($input['type']) ? trim($input['type']) : ''; // 'buy' หรือ 'sell'
        $qty = isset($input['quantity']) ? (int)$input['quantity'] : 0; // จำนวนหุ้น

        // ตรวจสอบความถูกต้องของข้อมูลนำเข้า
        if (empty($ticker) || !in_array($tradeType, ['buy', 'sell']) || $qty <= 0) {
            echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
            exit;
        }

        try {
            // เริ่มต้น Transaction ฐานข้อมูลเพื่อความปลอดภัย (ถ้าพังบรรทัดใดจะยกเลิกการทำงานทั้งหมด)
            $pdo->beginTransaction();

            // ดึงราคาหุ้นปัจจุบัน
            $stock_stmt = $pdo->prepare("SELECT price FROM stocks WHERE ticker = ?");
            $stock_stmt->execute([$ticker]);
            $stock = $stock_stmt->fetch();
            if (!$stock) {
                $pdo->rollBack(); // ยกเลิกรายการหากไม่พบหุ้น
                echo json_encode(['success' => false, 'error' => 'Stock not found']);
                exit;
            }
            $price = (float)$stock['price'];
            $total_val = $qty * $price; // ยอดเงินรวมที่ต้องจ่ายหรือได้รับ

            // ดึงข้อมูลยอดเงินสดปัจจุบันของผู้ใช้
            $user = getUserData($pdo, $username);
            $cash = (float)$user['cash'];

            // ตรวจสอบว่าผู้ใช้มีหุ้นนี้อยู่ในพอร์ตแล้วหรือยัง
            $hold_stmt = $pdo->prepare("SELECT shares, avg_price FROM portfolio WHERE username = ? AND ticker = ?");
            $hold_stmt->execute([$username, $ticker]);
            $holding = $hold_stmt->fetch();
            $owned_shares = $holding ? (int)$holding['shares'] : 0;
            $avg_price = $holding ? (float)$holding['avg_price'] : 0.0;

            // --- ส่วนสำหรับ สั่งซื้อหุ้น ---
            if ($tradeType === 'buy') {
                // ตรวจสอบว่าเงินพอซื้อหรือไม่
                if ($cash < $total_val) {
                    $pdo->rollBack();
                    echo json_encode(['success' => false, 'error' => 'เงินจำลองคงเหลือไม่เพียงพอสำหรับการสั่งซื้อ!']);
                    exit;
                }

                // หักเงินสดของผู้ใช้
                $new_cash = $cash - $total_val;
                setUserCash($pdo, $username, $new_cash);

                // หากมีหุ้นเดิมอยู่แล้ว ให้คำนวณราคาเฉลี่ยใหม่ (Average Cost) และอัปเดตตาราง portfolio
                if ($holding) {
                    $new_shares = $owned_shares + $qty;
                    $new_avg = (($owned_shares * $avg_price) + $total_val) / $new_shares;
                    $up_stmt = $pdo->prepare("UPDATE portfolio SET shares = ?, avg_price = ? WHERE username = ? AND ticker = ?");
                    $up_stmt->execute([$new_shares, $new_avg, $username, $ticker]);
                } else {
                    // หากไม่มีหุ้นเดิมในพอร์ต ให้เพิ่มแถวใหม่เข้าไปเลย
                    $in_stmt = $pdo->prepare("INSERT INTO portfolio (username, ticker, shares, avg_price) VALUES (?, ?, ?, ?)");
                    $in_stmt->execute([$username, $ticker, $qty, $price]);
                }

                // ทำการบันทึกข้อมูลทั้งหมดลงฐานข้อมูลอย่างสมบูรณ์
                $pdo->commit();
                echo json_encode([
                    'success' => true,
                    'message' => "ซื้อหุ้น $ticker สำเร็จ จำนวน " . number_format($qty) . " หุ้น ในราคา ฿" . number_format($price, 2)
                ]);

            // --- ส่วนสำหรับ สั่งขายหุ้น ---
            } else { 
                // ตรวจสอบว่าผู้ใช้มีจำนวนหุ้นที่จะขายเพียงพอหรือไม่
                if ($owned_shares < $qty) {
                    $pdo->rollBack();
                    echo json_encode(['success' => false, 'error' => 'คุณมีจำนวนหุ้นในครอบครองไม่เพียงพอสำหรับการขาย!']);
                    exit;
                }

                // เพิ่มยอดเงินสดให้ผู้ใช้
                $new_cash = $cash + $total_val;
                setUserCash($pdo, $username, $new_cash);

                $new_shares = $owned_shares - $qty;
                if ($new_shares === 0) {
                    // หากขายหุ้นหมดพอดี ให้ลบรายการหุ้นนั้นออกจาก portfolio
                    $del_stmt = $pdo->prepare("DELETE FROM portfolio WHERE username = ? AND ticker = ?");
                    $del_stmt->execute([$username, $ticker]);
                } else {
                    // หากขายบางส่วน ให้อัปเดตลดจำนวนหุ้นลง
                    $up_stmt = $pdo->prepare("UPDATE portfolio SET shares = ? WHERE username = ? AND ticker = ?");
                    $up_stmt->execute([$new_shares, $username, $ticker]);
                }

                // ทำการบันทึกข้อมูลทั้งหมดลงฐานข้อมูลอย่างสมบูรณ์
                $pdo->commit();
                echo json_encode([
                    'success' => true,
                    'message' => "ขายหุ้น $ticker สำเร็จ จำนวน " . number_format($qty) . " หุ้น ในราคา ฿" . number_format($price, 2)
                ]);
            }

        } catch (PDOException $e) {
            // หากเกิด Error ขึ้นให้ดึงข้อมูลกลับ (Rollback) เสมือนไม่เคยทำรายการใดๆ เลย เพื่อความปลอดภัยของข้อมูล
            $pdo->rollBack();
            echo json_encode(['success' => false, 'error' => 'Transaction failed: ' . $e->getMessage()]);
        }
        break;

    // ====================================================
    // เพิ่มหรือลดหุ้นในรายการเฝ้าดู (Watchlist)
    // ====================================================
    case 'watchlist_toggle':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            exit;
        }

        $username = getActiveUsername();
        $input = json_decode(file_get_contents('php://input'), true);
        $ticker = isset($input['ticker']) ? trim($input['ticker']) : '';

        if (empty($ticker)) {
            echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
            exit;
        }

        try {
            // ตรวจสอบว่าหุ้นนี้อยู่ใน Watchlist แล้วหรือไม่
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM watchlist WHERE username = ? AND ticker = ?");
            $stmt->execute([$username, $ticker]);
            $exists = $stmt->fetchColumn() > 0;

            if ($exists) {
                // หากเคยมีอยู่แล้ว ให้ลบออก
                $del = $pdo->prepare("DELETE FROM watchlist WHERE username = ? AND ticker = ?");
                $del->execute([$username, $ticker]);
                echo json_encode(['success' => true, 'is_watchlist' => false, 'message' => "นำ $ticker ออกจากรายการเฝ้าดูแล้ว"]);
            } else {
                // หากยังไม่มี ให้เพิ่มเข้าไปใหม่
                $ins = $pdo->prepare("INSERT INTO watchlist (username, ticker) VALUES (?, ?)");
                $ins->execute([$username, $ticker]);
                echo json_encode(['success' => true, 'is_watchlist' => true, 'message' => "เพิ่ม $ticker เข้าในรายการเฝ้าดูเรียบร้อย"]);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        break;

    // ====================================================
    // บันทึกผลทดสอบระดับความเสี่ยงที่รับได้ (Risk Profile)
    // ====================================================
    case 'save_profile':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            exit;
        }

        $username = getActiveUsername();
        $input = json_decode(file_get_contents('php://input'), true);
        $profile = isset($input['risk_profile']) ? trim($input['risk_profile']) : '';

        // ป้องกันค่าที่ส่งเข้ามาต้องตรงตามที่กำหนดเท่านั้น
        if (!in_array($profile, ['conservative', 'moderate', 'aggressive', 'null'])) {
            echo json_encode(['success' => false, 'error' => 'Invalid profile type']);
            exit;
        }

        try {
            $db_profile = ($profile === 'null') ? null : $profile;
            setUserRiskProfile($pdo, $username, $db_profile);
            echo json_encode(['success' => true, 'message' => 'บันทึกระดับความเสี่ยงสำเร็จ']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        break;

    // ====================================================
    // Action อื่นๆ ที่ไม่ถูกต้อง
    // ====================================================
    default:
        echo json_encode(['error' => 'Invalid API Action']);
        break;
}
