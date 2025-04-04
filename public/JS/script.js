let autresButton = document.querySelector('.autres');
let profil_dropdown = document.querySelector('.profil-dropdown');
let angleIcone = document.querySelector('.the-angle');
let profilAngleIcone = document.querySelector('.the-profil-angle');
let overlay = document.querySelector('#overlay');
let profilOverlay = document.querySelector('#profilOverlay');
let body = document.querySelector('body');
let connect = document.querySelector('#connect');
let register = document.querySelector('#register');
let bibli = document.querySelector('#bibli');
let home = document.querySelector('#home');
let search_bar = document.querySelector('#search-book');
let search_bar_first = document.querySelector('#search-book-first');
let search_bar_second = document.querySelector('#search-book-second');
let search_overlay = document.querySelector('#search-overlay');
let search_overlay_first = document.querySelector('#search-overlay-library-first');
let search_overlay_second = document.querySelector('#search-overlay-library-second');
let userStatus = document.querySelector('#status');
let firstname = document.querySelector('#firstname-status');
let route = document.querySelector('#route');

if (userStatus.textContent == 'logged') {
    firstnameContent = firstname.textContent;
}

autresButton.addEventListener('click', function () {

    if (autresButton.innerHTML == 'Autres <i class="the-angle fa-solid fa-angle-down"></i>') {

        if (userStatus.textContent == 'logged') {

            profilAngleIcone.classList.remove('fa-angle-up');

            profil_dropdown.innerHTML = firstnameContent + ' <i class="the-profil-angle fa-solid fa-angle-down"></i>';

            profil_dropdown.classList.add('bg-black');

            profilOverlay.classList.add('d-none');

        }

        angleIcone.classList.remove('fa-angle-down');

        autresButton.innerHTML = 'Fermer <i class="the-angle fa-solid fa-angle-up">';

        autresButton.classList.add('bg-black');

        overlay.classList.remove('d-none');

        body.classList.add('bg-black');

        connect.classList.add('overlay-opened');

        register.classList.add('overlay-opened');

        bibli.classList.add('overlay-opened');

        home.classList.add('overlay-opened');

    } else {

        angleIcone.classList.remove('fa-angle-up');

        autresButton.innerHTML = 'Autres <i class="the-angle fa-solid fa-angle-down"></i>';

        if (userStatus.textContent == 'logged') {

            profil_dropdown.classList.remove('bg-black');

        }

        overlay.classList.add('d-none');

        autresButton.classList.remove('bg-black');

        body.classList.remove('bg-black');

        connect.classList.remove('overlay-opened');

        register.classList.remove('overlay-opened');

        bibli.classList.remove('overlay-opened');

        home.classList.remove('overlay-opened');

    }

});

if (route.textContent == 'yes') {

    search_bar_first.addEventListener('focus', function () {

        search_overlay_first.classList.remove('d-none');

    });

    search_bar_first.addEventListener('focusout', function () {

        search_overlay_first.classList.add('d-none');

    });

    search_bar_second.addEventListener('focus', function () {

        search_overlay_second.classList.remove('d-none');

    });

    search_bar_second.addEventListener('focusout', function () {

        search_overlay_second.classList.add('d-none');

    });

}

search_bar.addEventListener('focus', function () {

    search_overlay.classList.remove('d-none');

});

search_bar.addEventListener('focusout', function () {

    search_overlay.classList.add('d-none');

});

profil_dropdown.addEventListener('click', function () {

    if (profil_dropdown.innerHTML == firstnameContent + ' <i class="the-profil-angle fa-solid fa-angle-down"></i>') {

        angleIcone.classList.remove('fa-angle-up');

        autresButton.innerHTML = 'Autres <i class="the-angle fa-solid fa-angle-down"></i>';

        overlay.classList.add('d-none');

        autresButton.classList.add('bg-black');

        bibli.classList.remove('overlay-opened');

        home.classList.remove('overlay-opened');

        profilAngleIcone.classList.remove('fa-angle-down');

        profil_dropdown.innerHTML = 'Fermer <i class="the-angle fa-solid fa-angle-up">';

        profil_dropdown.classList.add('bg-black');

        profilOverlay.classList.remove('d-none');

        body.classList.add('bg-black');

        bibli.classList.add('overlay-opened');

        home.classList.add('overlay-opened');

    } else {

        profilAngleIcone.classList.remove('fa-angle-up');

        profil_dropdown.innerHTML = firstnameContent + ' <i class="the-profil-angle fa-solid fa-angle-down"></i>';

        profil_dropdown.classList.remove('bg-black');

        profilOverlay.classList.add('d-none');

        autresButton.classList.remove('bg-black');

        body.classList.remove('bg-black');

        connect.classList.remove('overlay-opened');

        register.classList.remove('overlay-opened');

        bibli.classList.remove('overlay-opened');

        home.classList.remove('overlay-opened');

    }

});