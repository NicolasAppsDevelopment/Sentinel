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

document.addEventListener('DOMContentLoaded', function () {
    const mobileMenuButton = document.querySelector('.mobile-menu-button');
    const mobileMenu = document.querySelector('.mobile-menu');

    // add event listener to the button
    mobileMenuButton.addEventListener('click', function () {
        if (mobileMenuOpened) {
            mobileMenu.classList.remove('opened');
        } else {
            mobileMenu.classList.add('opened');
        }
        mobileMenuOpened = !mobileMenuOpened;
    });
});
