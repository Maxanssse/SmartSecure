console.log('Script loaded');

// Animations GSAP
gsap.from(".hero h1", { duration: 1, opacity: 0, y: -50 });
gsap.from(".hero p", { duration: 1, opacity: 0, y: 50, delay: 0.5 });
gsap.from(".hero a", { duration: 1, opacity: 0, scale: 0.5, delay: 1 });

document.addEventListener('DOMContentLoaded', (event) => {
    console.log('DOM fully loaded and parsed');

    try {
        const form = document.getElementById('contactForm');
        const errorElement = document.getElementById('error');

        if (form && errorElement) {
            // Pré-remplir le formulaire avec les données enregistrées et les récupérer dans le localStorage
            const savedFormData = localStorage.getItem('formData');
            if (savedFormData) {
                const formData = JSON.parse(savedFormData);
                document.getElementById('name').value = formData.name;
                document.getElementById('email').value = formData.email;
                document.getElementById('message').value = formData.message;
            }

            form.addEventListener('submit', function(e) {
                e.preventDefault(); // Empêche l'envoi du formulaire

                // Récupère les valeurs des champs du formulaire
                const name = document.getElementById('name').value;
                const email = document.getElementById('email').value;
                const message = document.getElementById('message').value;

                // Valide le format de l'email
                if (!validateEmail(email)) {
                    errorElement.textContent = 'Veuillez entrer une adresse email valide.';
                    return;
                }

                // Crée un objet pour stocker les données
                const formData = {
                    name: name,
                    email: email,
                    message: message
                };

                // Convertit l'objet en chaîne JSON et l'enregistre dans localStorage
                localStorage.setItem('formData', JSON.stringify(formData));

                // Envoyer les données du formulaire au serveur
                fetch('http://localhost:3000/send-email', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => response.text())
                .then(data => {
                    alert('Email envoyé avec succès');
                    form.reset(); // Réinitialise le formulaire
                    localStorage.removeItem('formData'); // Supprime les données enregistrées
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    errorElement.textContent = 'Une erreur s\'est produite. Veuillez réessayer plus tard.';
                });

                // Créer et télécharger le fichier .txt avec les données du formulaire
                downloadFormDataAsTxt(formData);

                // Affiche un message de confirmation
                alert('Les données du formulaire ont été enregistrées localement.');
                errorElement.textContent = ''; // Réinitialise le message d'erreur
            });

            // Valide l'email
            function validateEmail(email) {
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return re.test(String(email).toLowerCase());
            }

            // Fonction pour télécharger les données du formulaire en fichier .txt
            function downloadFormDataAsTxt(formData) {
                const dataStr = `Nom: ${formData.name}\nEmail: ${formData.email}\nMessage: ${formData.message}`;
                const blob = new Blob([dataStr], { type: 'text/plain' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'form_data.txt';
                a.click();
                URL.revokeObjectURL(url);
            }
        } else {
            throw new Error('Form or error element not found.');
        }

        // Botpress Chatbot initialization
        window.botpressWebChat.init({
            hostUrl: "https://mediafiles.botpress.cloud/a55411e4-1b2e-433a-ad96-98f2b878356f/webchat/bot.html",
            botId: "a55411e4-1b2e-433a-ad96-98f2b878356f",
        });

        // Gérer la fermeture et la réduction du chatbot
        const closeBtn = document.getElementById('close-btn');
        const minimizeBtn = document.getElementById('minimize-btn');
        if (closeBtn && minimizeBtn) {
            closeBtn.addEventListener('click', function() {
                document.getElementById('botpress-webchat').style.display = 'none';
            });

            minimizeBtn.addEventListener('click', function() {
                const chatContainer = document.getElementById('botpress-webchat');
                if (chatContainer) {
                    if (chatContainer.style.display === 'none') {
                        chatContainer.style.display = 'block';
                    } else {
                        chatContainer.style.display = 'none';
                    }
                } else {
                    throw new Error('Chatbot container not found.');
                }
            });
        } else {
            throw new Error('Close or minimize button not found.');
        }
    } catch (error) {
        console.error(error.message);
    }
});
