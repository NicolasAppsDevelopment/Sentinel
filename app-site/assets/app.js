/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';
import './fontawesome/css/fontawesome.css';
import './fontawesome/css/solid.css';
import './fontawesome/css/regular.css';

let mobileMenuOpened = false;
let lastPlayerUITimeout = null;

document.addEventListener('DOMContentLoaded', function () {
    const mobileMenuButton = document.querySelector('.mobile-menu-button');
    const mobileMenu = document.querySelector('.mobile-menu');

    // add event listener to the menu button
    mobileMenuButton.addEventListener('click', function () {
        if (mobileMenuOpened) {
            mobileMenu.classList.remove('opened');
        } else {
            mobileMenu.classList.add('opened');
        }
        mobileMenuOpened = !mobileMenuOpened;
    });


    // add event listener to display dropup content on button touched for mobile
    const dropupDiv = document.querySelector('.dropup');
    if (dropupDiv) {
        dropupDiv.addEventListener('touchstart', function () {
            if (dropupDiv.classList.contains('opened')) {
                dropupDiv.classList.remove('opened');
            } else {
                dropupDiv.classList.add('opened');
            }
        });
    }


    // add event listener to display player controls on player touched for mobile
    const player = document.querySelector('.stream-player-container');
    if (player) {
        // display UI at the beginning
        player.classList.add('display-ui');
        lastPlayerUITimeout = setTimeout(function () {
            player.classList.remove('display-ui');
            lastPlayerUITimeout = null;
        }, 5000);


        player.addEventListener('touchstart', function (e) {
            if (lastPlayerUITimeout) {
                clearTimeout(lastPlayerUITimeout);
            } else {
                e.preventDefault();
            }
            player.classList.add('display-ui');
            lastPlayerUITimeout = setTimeout(function () {
                player.classList.remove('display-ui');
                lastPlayerUITimeout = null;
            }, 3000);
        });
    }
});
