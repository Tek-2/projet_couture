import './stimulus_bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');


function initHowSection() {
    const howSection = document.getElementById('how');
    if (!howSection) return; // sécurité si la section n'existe pas

    const items = howSection.querySelectorAll('.col-md-3');

    const animateItems = () => {
        items.forEach((item, index) => {
            setTimeout(() => {
                item.classList.add('animate');
            }, index * 300); // 300ms entre chaque icône
        });
    };

    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if(entry.isIntersecting) {
                animateItems();
                observer.unobserve(howSection); // une seule fois
            }
        });
    }, { threshold: 0.3 });

    observer.observe(howSection);

    // Si on arrive directement via un hash (#how)
    if (window.location.hash === '#how') {
        setTimeout(() => {
            animateItems();
        }, 200); // petit délai pour laisser le scroll se terminer
    }
}

// Déclenche au load complet de la page
window.addEventListener('load', initHowSection);

// Déclenche si l'utilisateur clique sur un lien anchor vers #how
window.addEventListener('hashchange', () => {
    if (window.location.hash === '#how') {
        initHowSection();
    }
});

