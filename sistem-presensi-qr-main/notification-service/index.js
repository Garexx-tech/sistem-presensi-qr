const express = require('express');
const axios = require('axios');
require('dotenv').config();

const app = express();
app.use(express.json());

const PORT = process.env.PORT || 5000;
const FONNTE_TOKEN = process.env.FONNTE_TOKEN || '';

// Endpoint 1: Kirim Notifikasi Presensi Instan (Check-in / Check-out)
app.post('/api/send-presence-notification', async (req, res) => {
    const { phone, message } = req.body;

    try {
        const response = await axios.post('https://api.fonnte.com/send', {
            target: phone,
            message: message,
            countryCode: '62',
        }, {
            headers: { 'Authorization': FONNTE_TOKEN }
        });

        return res.json({ success: true, data: response.data });
    } catch (error) {
        return res.status(500).json({ success: false, error: error.message });
    }
});

// Endpoint 2: Kirim Notifikasi Recap Absensi (Sesuai Tugas Dosen)
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

app.listen(PORT, () => {
    console.log(`Notification Service running on port ${PORT}`);
});