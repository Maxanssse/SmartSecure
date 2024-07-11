const express = require('express');
const bodyParser = require('body-parser');
const nodemailer = require('nodemailer');
const cors = require('cors');
require('dotenv').config(); // Pour charger les variables d'environnement depuis un fichier .env

const app = express();
app.use(bodyParser.json());
app.use(cors()); // Utiliser le middleware CORS

const transporter = nodemailer.createTransport({
    service: 'gmail', // Utilisez le service que vous préférez
    auth: {
        user: process.env.EMAIL_USER,
        pass: process.env.EMAIL_PASS // Utilisez des variables d'environnement pour plus de sécurité
    }
});

app.post('/send-email', (req, res) => {
    const { name, email, message } = req.body;

    const mailOptions = {
        from: email,
        to: 'maxvabre7@gmail.com',
        subject: `Nouveau message de ${name}`,
        text: message
    };

    transporter.sendMail(mailOptions, (error, info) => {
        if (error) {
            console.error('Error sending email:', error); 
            return res.status(500).send(error.toString());
        }
        res.send('Email envoyé : ' + info.response);
    });
});
// Démarrer le serveur
const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
    console.log(`Server is running on port ${PORT}`);
});