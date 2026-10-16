/**
 * PERFORMANCE ESTOICA - CRON WORKER 24/7 (RAILWAY)
 * 
 * Executa continuamente a cada 60 segundos, contornando a proteção
 * anti-bot (AES JavaScript Challenge) da InfinityFree de forma nativa e ultra-leve.
 * 
 * Requisitos: Node.js 18+ (zero dependências externas).
 */

const https = require('https');
const crypto = require('crypto');

const TARGET_URL = process.env.TARGET_URL || 'https://performanceestoica.freedev.app/cron/notify_whatsapp.php';
const INTERVAL_SECONDS = parseInt(process.env.INTERVAL_SECONDS || '60', 10);
const USER_AGENT = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36';

let cachedCookie = null;
let cookieExpiresAt = 0;

function httpRequest(url, options = {}) {
    return new Promise((resolve, reject) => {
        const req = https.get(url, options, (res) => {
            let data = '';
            res.on('data', chunk => data += chunk);
            res.on('end', () => resolve({
                statusCode: res.statusCode,
                headers: res.headers,
                body: data
            }));
        });
        req.on('error', reject);
        req.setTimeout(15000, () => {
            req.destroy(new Error('Timeout de requisição (15s)'));
        });
    });
}

/**
 * Resolve o desafio AES da InfinityFree usando decifração AES-128-CBC nativa
 */
async function solveInfinityFreeChallenge(originUrl) {
    const res = await httpRequest(originUrl, {
        headers: { 'User-Agent': USER_AGENT }
    });

    const matchA = res.body.match(/a=toNumbers\(["']([a-f0-9]+)["']\)/);
    const matchB = res.body.match(/b=toNumbers\(["']([a-f0-9]+)["']\)/);
    const matchC = res.body.match(/c=toNumbers\(["']([a-f0-9]+)["']\)/);

    if (!matchA || !matchB || !matchC) {
        // Se já não houver o desafio, talvez a resposta já seja direta
        return null;
    }

    const a = Buffer.from(matchA[1], 'hex');
    const b = Buffer.from(matchB[1], 'hex');
    const c = Buffer.from(matchC[1], 'hex');

    const decipher = crypto.createDecipheriv('aes-128-cbc', a, b);
    decipher.setAutoPadding(false);
    const testCookie = Buffer.concat([decipher.update(c), decipher.final()]).toString('hex');

    return testCookie;
}

async function triggerCron() {
    const timestamp = new Date().toLocaleTimeString('pt-BR', { timeZone: 'America/Sao_Paulo' });
    const urlObj = new URL(TARGET_URL);
    const origin = urlObj.origin + '/';

    try {
        const now = Date.now();
        // Se o cookie não existir ou tiver mais de 4 horas, renova
        if (!cachedCookie || now > cookieExpiresAt) {
            console.log(`[${timestamp}] 🔄 Renovando cookie anti-bot da InfinityFree...`);
            cachedCookie = await solveInfinityFreeChallenge(origin);
            cookieExpiresAt = now + (4 * 60 * 60 * 1000); // 4 horas
            console.log(`[${timestamp}] 🔑 Cookie obtido com sucesso: ${cachedCookie ? cachedCookie.substring(0, 10) + '...' : 'nenhum (acesso direto)'}`);
        }

        const headers = { 'User-Agent': USER_AGENT };
        if (cachedCookie) {
            headers['Cookie'] = '__test=' + cachedCookie;
        }

        const res = await httpRequest(TARGET_URL, { headers });

        // Se o InfinityFree recusou o cookie (re-desafio), limpa o cache e tenta mais uma vez
        if (res.body.includes('toNumbers') && res.body.includes('slowAES')) {
            console.log(`[${timestamp}] ⚠️ Cookie expirado ou recusado. Resolvendo novo desafio...`);
            cachedCookie = await solveInfinityFreeChallenge(origin);
            headers['Cookie'] = '__test=' + cachedCookie;
            const retryRes = await httpRequest(TARGET_URL, { headers });
            const output = retryRes.body.trim().replace(/\n/g, ' ');
            console.log(`[${timestamp}] ⚡ [Status ${retryRes.statusCode}] ${output}`);
            return;
        }

        const output = res.body.trim().replace(/\n/g, ' ');
        console.log(`[${timestamp}] ⚡ [Status ${res.statusCode}] ${output}`);

    } catch (err) {
        console.error(`[${timestamp}] ❌ Erro ao disparar cron:`, err.message);
    }
}

console.log('====================================================');
console.log('🚀 PERFORMANCE ESTOICA - CRON WORKER 24/7 INICIADO');
console.log(`🎯 Alvo: ${TARGET_URL}`);
console.log(`⏱️ Intervalo: a cada ${INTERVAL_SECONDS} segundos`);
console.log('====================================================');

// Dispara imediatamente na inicialização
triggerCron();

// Repete no intervalo programado
setInterval(triggerCron, INTERVAL_SECONDS * 1000);
