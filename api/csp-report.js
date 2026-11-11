/**
 * Content-Security-Policy の違反レポートを受け取り、Vercel のログ（Functions > Logs）に残す。
 * Report-Only の間に、本番で止めてはいけない読み込みがないかを確かめるために使う。
 */
module.exports = async (req, res) => {
  if (req.method !== 'POST') return res.status(405).end();
  try {
    const body = typeof req.body === 'string' ? req.body : JSON.stringify(req.body || {});
    console.warn('CSP report:', body.slice(0, 2000));
  } catch (e) {
    // 受け取れなくても何もしない
  }
  return res.status(204).end();
};
