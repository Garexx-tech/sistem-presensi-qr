const express = require('express');
const axios = require('axios');
const mysql = require('mysql2/promise');
const cron = require('node-cron');
require('dotenv').config();

const app = express();
app.use(express.json());

const PORT = process.env.PORT || 5002;
const NOTIF_URL = process.env.NOTIFICATION_SERVICE_URL || 'http://notification_service:5000';
const ALPHA_LIMIT = Number(process.env.ALPHA_LIMIT || 1);
const IZIN_LIMIT = Number(process.env.IZIN_LIMIT || 3);

// Database utama (db_sipena) — read-only untuk hitung alpha/izin
const mainPool = mysql.createPool({
    host: process.env.DB_HOST || 'host.docker.internal',
    port: Number(process.env.DB_PORT || 3306),
    user: process.env.DB_USERNAME || 'root',
    password: process.env.DB_PASSWORD || '',
    database: process.env.DB_DATABASE || 'db_sipena',
    waitForConnections: true,
    connectionLimit: 5,
});

// Database WA gateway (db_wa) — untuk idempotensi & log
const waPool = mysql.createPool({
    host: process.env.DB_WA_HOST || 'db_wa',
    port: Number(process.env.DB_WA_PORT || 3306),
    user: process.env.DB_WA_USERNAME || 'root',
    password: process.env.DB_WA_PASSWORD || '',
    database: process.env.DB_WA_DATABASE || 'db_wa_gateway',
    waitForConnections: true,
    connectionLimit: 5,
});

async function getApiKey() {
    try {
        const [rows] = await mainPool.query(
            `SELECT api_key FROM fonnte_devices
             WHERE is_active = 1 AND status != 'error'
             ORDER BY priority ASC, sent_count ASC LIMIT 1`
        );
        return rows.length ? rows[0].api_key : null;
    } catch (e) {
        console.error('[main] getApiKey error:', e.message);
        return null;
    }
}

async function alreadySentThisMonth(siswaId) {
    try {
        const [rows] = await waPool.query(
            `SELECT COUNT(*) AS c FROM wa_messages
             WHERE ref_type = 'deteksi_dini' AND ref_id = ?
               AND created_at >= DATE_FORMAT(NOW(), '%Y-%m-01')`,
            [siswaId]
        );
        return rows[0].c > 0;
    } catch (e) {
        console.error('[wa] alreadySent error:', e.message);
        return false;
    }
}

function buildMessage(s) {
    return `Assalamualaikum Bapak/Ibu\n\n`
        + `Peringatan Deteksi Dini Presensi:\n`
        + `*Nama:* ${s.name}\n`
        + `*NIS:* ${s.nis || '-'}\n`
        + `*Kelas:* ${s.nama_kelas || '-'}\n`
        + `*Total Alpha:* ${s.alpha} hari\n`
        + `*Total Izin:* ${s.izin} hari\n\n`
        + `Mohon perhatiannya agar kehadiran ananda lebih baik.\n\n`
        + `_Sistem Presensi Sekolah_`;
}

async function runJob(dryRun = false) {
    const summary = {
        total_siswa: 0,
        berisiko: 0,
        terkirim: 0,
        dilewati: 0,
        gagal: 0,
        dryRun,
    };

    const apiKey = await getApiKey();

    const [siswa] = await mainPool.query(
        `SELECT u.id, u.name, u.nis, u.parent_phone, k.nama_kelas
         FROM users u
         LEFT JOIN kelas k ON k.id = u.kelas_id
         WHERE u.role = 2`
    );

    for (const s of siswa) {
        summary.total_siswa++;

        const [[a]] = await mainPool.query(
            `SELECT COUNT(*) AS c FROM presensis WHERE siswa_id = ? AND status = 'alpha'`,
            [s.id]
        );
        const [[i]] = await mainPool.query(
            `SELECT COUNT(*) AS c FROM presensis WHERE siswa_id = ? AND status = 'izin'`,
            [s.id]
        );

        s.alpha = a.c;
        s.izin = i.c;

        const berisiko = (s.alpha >= ALPHA_LIMIT) || (s.izin >= IZIN_LIMIT);
        if (!berisiko) continue;

        summary.berisiko++;

        if (!s.parent_phone) {
            summary.dilewati++;
            continue;
        }

        if (await alreadySentThisMonth(s.id)) {
            summary.dilewati++;
            continue;
        }

        const message = buildMessage(s);

        if (dryRun) {
            summary.terkirim++;
            continue;
        }

        try {
            await axios.post(`${NOTIF_URL}/api/send-presence-notification`, {
                phone: s.parent_phone,
                message,
                api_key: apiKey,
                ref_type: 'deteksi_dini',
                ref_id: s.id,
            }, { timeout: 20000 });
            summary.terkirim++;
        } catch (e) {
            summary.gagal++;
            console.error(`[send] gagal siswa ${s.id}:`, e.message);
        }
    }

    return summary;
}

app.get('/health', (req, res) => {
    res.json({ status: 'ok', service: 'deteksi-dini-service', schedule: '0 8 1 * *' });
});

app.post('/run', async (req, res) => {
    try {
        const dry = req.query.dry === '1' || (req.body && req.body.dry === true);
        const summary = await runJob(dry);
        res.json({ success: true, summary });
    } catch (e) {
        res.status(500).json({ success: false, error: e.message });
    }
});

// Scheduler: tanggal 1 setiap bulan pukul 08:00
cron.schedule('0 8 1 * *', () => {
    console.log('[cron] mulai deteksi dini bulanan...');
    runJob(false)
        .then((s) => console.log('[cron] selesai:', JSON.stringify(s)))
        .catch((e) => console.error('[cron] error:', e.message));
});

app.listen(PORT, () => {
    console.log(`Deteksi Dini Service running on port ${PORT} (cron: 0 8 1 * *)`);
});
