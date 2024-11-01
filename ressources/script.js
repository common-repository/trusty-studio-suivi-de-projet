const tabs = document.querySelectorAll('.nav-tab');
const tabContents = document.querySelectorAll('.tab-content');

tabs.forEach(tab => {
    tab.addEventListener('click', (e) => {
        e.preventDefault();

        tabs.forEach(t => t.classList.remove('nav-tab-active'));
        tab.classList.add('nav-tab-active');

        tabContents.forEach(content => content.style.display = 'none');
        document.querySelector(tab.getAttribute('href')).style.display = 'block';
    });
});

document.querySelector('.nav-tab-active').click();
