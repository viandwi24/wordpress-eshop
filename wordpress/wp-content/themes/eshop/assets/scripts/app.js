// navbar
const navbarInit = () => {
    const navbar = document.querySelector('#navbar');
    if (!navbar) return false;
    const navbarTop = document.querySelector('#navbar-top');
    const navbarBottom = document.querySelector('#navbar-bottom');
    const navbarBaseHeight = navbarTop.offsetHeight;
    const navbarTopBaseHeight = navbarTop.clientHeight;
    const offset = 20;
    const onWindowScroll = function() {
        const requiredClass = ['fixed', 'w-full', 'z-20', 'top-0', 'left-0']
        if (window.scrollY > navbarBottom.offsetHeight + offset) {
            const result = (navbarTopBaseHeight + navbarBottom.clientHeight);
            if (navbarTop.clientHeight !== result) {
                navbarTop.style.height = `${result}px`;
            }
            if (!navbarBottom.classList.contains(requiredClass[0])) {
                navbarBottom.classList.add(...requiredClass);
                navbarBottom.animate([
                    { transform: 'translateY(-10vh)' },
                    { transform: 'translateY(0)' },
                ], {
                    duration: 300,
                    easing: 'ease-out',
                });
            }
        } else if (window.scrollY < (navbarBottom.offsetHeight * 2) + offset) {
            navbarTop.style.height = `${navbarTopBaseHeight}px`;
            navbarBottom.classList.remove(...requiredClass);
        }
    };
    window.addEventListener('scroll', onWindowScroll);
    window.addEventListener('DOMContentLoaded', onWindowScroll);
}

// 
navbarInit();