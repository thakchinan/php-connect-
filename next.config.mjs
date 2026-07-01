/** @type {import('next').NextConfig} */
const nextConfig = {
  async rewrites() {
    return [
      {
        source: '/api/:action',
        destination: 'http://127.0.0.1:8000/api/:action',
      },
    ];
  },
};

export default nextConfig;
