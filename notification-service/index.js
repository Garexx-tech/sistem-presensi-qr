const express = require('express');
const axios = require('axios');
const mysql = require('mysql2/promise');
require('dotenv').config();

const app = express();
app.use(express.json());

const PORT = process.env.PORT || 5000;
const FONNTE_TOKEN = process.env.FONNTE_TOKEN || '';

// --- Database WA Gateway (dipakai jika env DB_WA_HOST tersedia) ---
let waPool = null;
if (process.env.DB_WA_HOST) {
    waPool = mysql.createPool({
        host: process.env.DB_WA_HOST,
        port: Number(process.env.DB_WA_PORT || 3306),
        user: process.env.DB_WA_USERNAME || 'root',
        password: process.env.DB_WA_PASSWORD || '',
        database: process.env.DB_WA_DATABASE || 'db_wa_gateway',
        waitForConnections: true,
        connectionLimit: 5,
    });
    console.log(`[db_wa] pool dibuat untuk ${process.env.DB_WA_HOST}/${process.env.DB_WA_DATABASE || 'db_wa_gateway'}`);
}

async function logWa({ phone, message, status, response, refType, refId }) {
    if (!waPool) return;
    try {
        await waPool.query(
            `INSERT INTO wa_messages (phone, message, status, fonnte_response, ref_type, ref_id)
             VALUES (?, ?, ?, ?, ?, ?)`,
            [
                phone || null,
                message || null,
                status || 'queued',
                response ? JSON.stringify(response) : null,
                refType || null,
                refId || null,
            ]
        );
    } catch (e) {
        console.error('[db_wa] gagal simpan log:', e.message);
    }
}

// Endpoint 1: Kirim Notifikasi Presensi Instan (Check-in / Check-out)
app.post('/api/send-presence-notification', async (req, res) => {
    const { phone, message, ref_type, ref_id } = req.body;
    const token = req.body.api_key || FONNTE_TOKEN;

    try {
        const response = await axios.post('https://api.fonnte.com/send', {
            target: phone,
            message: message,
            countryCode: '62',
        }, {
            headers: { 'Authorization': token }
        });

        const ok = response.data && response.data.status === true;
        await logWa({
            phone,
            message,
            status: ok ? 'sent' : 'failed',
            response: response.data,
            refType: ref_type,
            refId: ref_id,
        });

        return res.json({ success: true, data: response.data });
    } catch (error) {
        await logWa({
            phone,
            message,
            status: 'failed',
            response: { error: error.message },
            refType: ref_type,
            refId: ref_id,
        });
        return res.status(500).json({ success: false, error: error.message });
    }
});

// Endpoint 2: Kirim Notifikasi Recap Absensi
app.post('/api/send-recap-notification', async (req, res) => {
    const { phone, recapData, period } = req.body;
    const message = `[REKAP PRESENSI - ${period}]\n\nHalo, berikut rekapitulasi kehadiran:\n\n${recapData}`;

    try {
        const response = await axios.post('https://api.fonnte.com/send', {
            target: phone,
            message: message,
            countryCode: '62',
        }, {
            headers: { 'Authorization': FONNTE_TOKEN }
        });

        return res.json({ success: true, message: 'Rekap berhasil dikirim via WA', data: response.data });
    } catch (error) {
        return res.status(500).json({ success: false, error: error.message });
    }
});

app.get('/health', (req, res) => res.json({ status: 'ok', db_wa: !!waPool }));

app.listen(PORT, () => {
    console.log(`Notification Service running on port ${PORT}`);
});
