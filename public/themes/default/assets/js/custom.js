/**
* global search mobile view
*/
let searchBtn       = document.querySelector('.search-btn span'),
    exitBtn         = document.querySelector('.exit-search'),
    searchContainer = document.querySelector('.global-search')

searchBtn.addEventListener('pointerdown', () => {
    searchContainer.classList.add('open')
    document.querySelector('.wrapper').classList.add('searching')
})

exitBtn.addEventListener('pointerdown', () => {
    searchContainer.classList.remove('open')
    document.querySelector('.wrapper').classList.remove('searching')
})

document.addEventListener('pointerdown', e => {
    if (searchContainer.classList.contains('open') && !e.target.closest('.global-search') && e.target != searchBtn) {
        searchContainer.classList.remove('open')
        document.querySelector('.wrapper').classList.remove('searching')
    }
})

/**
* dynamic width for mega-menu
*/
function setTargetWidth() {
    var widthOrigin = document.getElementById('primary-nav-container');
    var megaMenu = document.querySelectorAll('.mega-dropdown');
    megaMenu.forEach(e => {
        e.style.width = (widthOrigin.offsetWidth - 270) + 'px';
    });
    if (document.querySelector('.ei-slider')) {
        let fullHeight = document.querySelector('.ei-slider').scrollHeight   
        megaMenu.forEach(e => {
            e.style.height = (fullHeight - 19) + 'px';
        });
    }
}
setTargetWidth();
window.addEventListener('resize', setTargetWidth);

/**
 * dynamic height for all-categories dropdown
 */
let cateScroll = document.querySelector('.header .menu-list-wrapper')
if (document.querySelector('.ei-slider')) {
    let fullHeight = document.querySelector('.ei-slider').scrollHeight - 36
    cateScroll.style.height = fullHeight + 'px'
}

/**
 * appropriate .col- classes for the .banner-block\s
 */
let bannerCol = document.querySelectorAll('.banner-block [class^="col-lg-"]');

bannerCol.forEach(e => {
    if (!e.classList.contains('col-lg-12') && !e.classList.contains('col-lg-8')) {
        e.className += " col-sm-6 mb-4 mb-md-0";
    }
});

/**
 * NiceSelect activation for all-category filter in the global search-bar
 */
NiceSelect.bind(document.getElementById("niceSelect"), {searchable: true});

/**
 * making space at the bottom of the search bar if the trending-keywords exist or not
 */
if ($('.header-search').find('.trending-words').length === 0) {
    $('.header-search').css('padding-bottom', '15px')
}