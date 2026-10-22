const express = require('express');
const axios = require('axios');
const mysql = require('mysql2/promise');
require('dotenv').config();

const app = express();
app.use(express.json());

const PORT = process.env.PORT || 5000;
const FONNTE_TOKEN = process.env.FONNTE_TOKEN || '';

// Konfigurasi Koneksi ke Database WA Gateway (db_wa_gateway)
const dbConfig = {
    host: process.env.DB_WA_HOST || 'host.docker.internal',
    port: process.env.DB_WA_PORT || 3306,
    user: process.env.DB_WA_USERNAME || 'root',
    password: process.env.DB_WA_PASSWORD || '',
    database: process.env.DB_WA_DATABASE || 'db_wa_gateway'
};

// Fungsi Helper untuk Mencatat Log Pesan ke db_wa_gateway
async function logMessageToDb(phone, recipientName, message, type, status, gatewayResponse) {
    try {
        const connection = await mysql.createConnection(dbConfig);
        const query = `
            INSERT INTO wa_message_logs 
            (recipient_phone, recipient_name, message, type, status, response_gateway, sent_at) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        `;
        await connection.execute(query, [
            phone,
            recipientName || 'Siswa/Wali',
            message,
            type,
            status,
            JSON.stringify(gatewayResponse)
        ]);
        await connection.end();
    } catch (err) {
        console.error('Gagal mencatat log ke DB WA:', err.message);
    }
}

// Endpoint 1: Kirim Notifikasi Presensi Instan (Check-in / Check-out)
app.post('/api/send-presence-notification', async (req, res) => {
    const { phone, name, message } = req.body;

    try {
        const response = await axios.post('https://api.fonnte.com/send', {
            target: phone,
            message: message,
            countryCode: '62',
        }, {
            headers: { 'Authorization': FONNTE_TOKEN }
        });

        // Catat Log Berhasil ke Database db_wa_gateway
        await logMessageToDb(phone, name, message, 'presensi_masuk', 'sent', response.data);

        return res.json({ success: true, data: response.data });
    } catch (error) {
        // Catat Log Gagal ke Database db_wa_gateway
        await logMessageToDb(phone, name, message, 'presensi_masuk', 'failed', { error: error.message });

        return res.status(500).json({ success: false, error: error.message });
    }
});

// Endpoint 2: Kirim Notifikasi Recap Absensi & Peringatan Deteksi Dini
app.post('/api/send-recap-notification', async (req, res) => {
    const { phone, name, recapData, period, isWarning } = req.body;
    
    const prefix = isWarning ? '[PERINGATAN DETEKSI DINI]' : `[REKAP PRESENSI - ${period}]`;
    const message = `${prefix}\n\nHalo, berikut informasi kehadiran:\n\n${recapData}`;
    const msgType = isWarning ? 'peringatan_deteksi_dini' : 'info_umum';

    try {
        const response = await axios.post('https://api.fonnte.com/send', {
            target: phone,
            message: message,
            countryCode: '62',
        }, {
            headers: { 'Authorization': FONNTE_TOKEN }
        });

        // Catat Log Berhasil ke Database db_wa_gateway
        await logMessageToDb(phone, name, message, msgType, 'sent', response.data);

        return res.json({ success: true, message: 'Notifikasi berhasil dikirim via WA', data: response.data });
    } catch (error) {
        // Catat Log Gagal ke Database db_wa_gateway
        await logMessageToDb(phone, name, message, msgType, 'failed', { error: error.message });

        return res.status(500).json({ success: false, error: error.message });
    }
});

app.listen(PORT, () => {
    console.log(`Notification Service running on port ${PORT} & Connected to db_wa_gateway`);
});