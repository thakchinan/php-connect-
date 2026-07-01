import "./globals.css";

export const metadata = {
  title: "SmartInvest - ระบบแนะนำการลงทุนและจำลองพอร์ตหุ้น",
  description: "ระบบแนะนำการลงทุนและจัดพอร์ตจำลองสำหรับนักลงทุนมือใหม่",
};

export default function RootLayout({ children }) {
  return (
    <html lang="th" className="h-full antialiased">
      <body className="min-h-full flex flex-col">
        {children}
      </body>
    </html>
  );
}
