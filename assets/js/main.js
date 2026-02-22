const bodyHidden = () => {
    document.querySelector('body').style.overflow = 'hidden';
}

const bodyVisible = () => {
    document.querySelector('body').style.overflow = 'visible';
}

const phoneInp = document.querySelectorAll('input[type="tel"]');

if (phoneInp.length) {
    phoneInp.forEach(el => {
        IMask(el, {
            mask: '+{7}(000) 000-00-00',
        })
    });
}

const productCatalogSwp = document.querySelectorAll('.product-catalog .tab-body__item');
if (productCatalogSwp.length) {
    productCatalogSwp.forEach(el => {
        const swp = new Swiper(el.querySelector('.swiper'), {
            slidesPerView: "auto",
            spaceBetween: 10,
            loop: true,
            breakpoints: {
                992: {
                    slidesPerView: 3,
                    spaceBetween: 20,
                }
            },
            navigation: {
                nextEl: el.querySelector('.swiper .swp-next'),
                prevEl: el.querySelector('.swiper .swp-prev'),
            }
        })
    })
}

const productTabBtn = document.querySelectorAll('.product-catalog__content .tab-head a');
const productTabBody = document.querySelectorAll('.product-catalog__content .tab-body__item');

if (productTabBtn.length) {
    productTabBtn.forEach((btn, btnID) => {
        btn.onclick = e => {
            e.preventDefault();
            productTabBtn.forEach((el, elID) => {
                if (elID == btnID) {
                    el.classList.add('active');
                } else {
                    el.classList.remove('active');
                }
            })
            productTabBody.forEach((el, elID) => {
                if (elID == btnID) {
                    el.classList.add('active');
                } else {
                    el.classList.remove('active');
                }
            })
        }
    })
}

if (document.querySelector('.review-swp')) {
    var reviewSwpInit = false;
    var reviewSwp;
    function reviewSwpFunction() {
        if (window.innerWidth <= 992) {
            if (!reviewSwpInit) {
                reviewSwpInit = true;
                reviewSwp = new Swiper(".review-swp .swiper", {
                    slidesPerView: "auto",
                    spaceBetween: 10,
                });
            }
        } else if (reviewSwpInit) {
            reviewSwp.destroy();
            reviewSwpInit = false;
        }
    }
    reviewSwpFunction();
    window.addEventListener("resize", reviewSwpFunction);
}

const accordions = document.querySelectorAll('.accordions');

if (accordions.length) {
    accordions.forEach((item) => {
        const acc = item.querySelectorAll('.accordion');
        if (acc.length) {
            acc.forEach(data => {
                const btn = data.querySelector('.accordion-btn');
                const bodyWrap = data.querySelector('.accordion-body__wrap');

                btn.addEventListener('click', () => {
                    bodyWrap.style.maxHeight = bodyWrap.style.maxHeight ? null : bodyWrap.scrollHeight + 'px';
                    data.classList.toggle('active');
                    acc.forEach(el => {
                        if (data != el) {
                            el.querySelector('.accordion-body__wrap').style.maxHeight = null;
                            el.classList.remove('active');
                        }
                    })
                });
            })
        }
    });
}

const headerCatalogWrap = document.querySelector('.header-catalog__wrap');
const headerCatalogOpenBtn = document.querySelector('.header-ctalog__open');
const headerCatalog = document.querySelector('.header-catalog');
const headerCatalogBtns = document.querySelectorAll('.header-catalog__link');
const headerCatalogContents = document.querySelectorAll('.header-catalog__content-item');

if (headerCatalog) {
    headerCatalogOpenBtn.onclick = e => {
        e.preventDefault();
        headerCatalog.classList.toggle('active');
    }

    headerCatalogBtns.forEach((btn, btnID) => {
        btn.onclick = e => {
            e.preventDefault();
            headerCatalogContents.forEach((el, elID) => {
                if (btnID == elID) {
                    el.classList.add('active');
                } else {
                    el.classList.remove('active');
                }
            })
            headerCatalogBtns.forEach((el, elID) => {
                if (btnID == elID) {
                    el.classList.add('active');
                } else {
                    el.classList.remove('active');
                }
            })
        }
    })
}

const searchBtn = document.querySelector('.header .search-btn');
const search = document.querySelector('.header .search');
const searchInp = document.querySelector('.header .search-head input');
const searchInpClr = document.querySelector('.header .search-head .clear');
const searchResult = document.querySelector('.header .search-result__wrap');

if (search) {
    searchBtn.onclick = () => {
        search.classList.toggle('active');
    }

    searchInp.oninput = () => {
        if (searchInp.value.length > 0) {
            searchResult.classList.add('active');
        } else {
            searchResult.classList.remove('active');
        }
    }

    searchInpClr.onclick = () => {
        searchInp.value = '';
        searchResult.classList.remove('active');
    }
}

const menuCatalog = document.querySelector('.menu-catalog');
const menuCatalogBtn = document.querySelector('.menu-catalog__btn');
const menuNavs = document.querySelector('.menu-navs');
const menuCatalogList = document.querySelectorAll('.menu-catalog li');
const menuCatalogContent = document.querySelector('.menu-catalog__content');
const menuCatalogContentItems = document.querySelectorAll('.menu-catalog__content-item');

if (menuCatalog) {
    menuCatalogBtn.onclick = () => {
        menuCatalog.classList.toggle('active');
        menuCatalogBtn.classList.toggle('active');
        menuNavs.classList.toggle('hidden')
    }

    menuCatalogList.forEach((list, listID) => {
        list.onclick = () => {
            list.classList.toggle('active')
            menuCatalog.classList.toggle('hidden')
            menuCatalogBtn.classList.toggle('hidden')
            menuCatalogContent.classList.toggle('active');
            menuCatalogContentItems.forEach((el, elID) => {
                if (listID == elID) {
                    el.classList.add('active');
                } else {
                    el.classList.remove('active');
                }
            })
        }
    })
}

const bars = document.querySelector('.header .bars');
const menu = document.querySelector('.menu')

if (menu) {
    bars.onclick = () => {
        bars.classList.toggle('active');
        menu.classList.toggle('active');
    }
}

const modal = document.querySelector('.modal');
const modalOpenBtn = document.querySelectorAll('.modal-open');
const modalBg = document.querySelector('.modal-bg');
const modalCloseBtn = document.querySelector('.modal-close');

if (modal) {
    modalOpenBtn.forEach(el => {
        el.onclick = e => {
            e.preventDefault();
            modal.classList.add('active');
            bodyHidden();
        }
    })

    modalBg.onclick = () => {
        modal.classList.remove('active');
        bodyVisible();
    }

    modalCloseBtn.onclick = () => {
        modal.classList.remove('active');
        bodyVisible();
    }
}

// tabs
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll("[data-tabs]").forEach((tabs) => {
        const buttons = tabs.querySelectorAll(".tabs__btn");
        const panels = tabs.querySelectorAll(".tabs__panel");

        if (!buttons.length || !panels.length) return;

        buttons.forEach((btn) => {
        btn.addEventListener("click", () => {
            const targetId = btn.dataset.tab;
            if (!targetId) return;

            buttons.forEach((b) => b.classList.remove("is-active"));
            panels.forEach((p) => p.classList.remove("is-active"));

            btn.classList.add("is-active");
            tabs.querySelector(`#${targetId}`)?.classList.add("is-active");
        });
        });
    });
});
// tabs

const productCards = document.querySelectorAll('.product-card');

if (productCards.length) {
    productCards.forEach(el => {
        const swp = new Swiper(el.querySelector('.product-card__swp'), {
            slidesPerView: 1,
            loop: true,
            pagination: {
                el: el.querySelector('.swp-pagination'),
                clickable: true,
            }
        })
    })
}

const catalogTabHead = document.querySelector('.catalog-tab__head');
const catalogTabHeadBtn = document.querySelector('.catalog-tab__head-btn');
const catalogtabHeadList = document.querySelector('.catalog-tab__head-list');
const catalogTabHeadListBtn = document.querySelectorAll('.catalog-tab__head-list button');
const catalogTabBody = document.querySelectorAll('.catalog-tab__body');

if (catalogTabHeadBtn) {
    catalogTabHeadBtn.onclick = () => {
        catalogtabHeadList.classList.toggle('active');
    }

    catalogTabHeadListBtn.forEach((btn, btnID) => {
        btn.onclick = () => {
            catalogtabHeadList.classList.remove('active');
            catalogTabBody.forEach((el, elID) => {
                if (elID == btnID) {
                    el.classList.add('active');
                } else {
                    el.classList.remove('active');
                }
            })
            catalogTabHeadListBtn.forEach((el, elID) => {
                if (elID == btnID) {
                    el.classList.add('active');
                } else {
                    el.classList.remove('active');
                }
            })
        }
    })
}

const catalogSort = document.querySelector('.catalog .sort');
const catalogSortBtn = document.querySelector('.catalog .sort-btn');
const catalogSortList = document.querySelector('.catalog .sort-list');

if (catalogSort) {
    catalogSortBtn.onclick = () => {
        catalogSortList.classList.toggle('active');
    }
}

const selects = document.querySelectorAll('.main-select');

if (selects.length) {
    selects.forEach(el => {
        const btn = el.querySelector('.main-select__btn');
        const list = el.querySelectorAll('.main-select__list button');

        btn.onclick = () => {
            el.classList.toggle('active');
        }

        list.forEach(item => {
            item.onclick = () => {
                btn.querySelector('span').textContent = item.textContent;
                btn.querySelector('input').value = item.textContent;
                list.forEach(e => {
                    if (e == item) {
                        e.classList.add('selected');
                    } else {
                        e.classList.remove('selected');
                    }
                })
                el.classList.remove('active');
            }
        })
    })
}

window.addEventListener('click', function (event) {
    if (search && !search.contains(event.target) && !searchBtn.contains(event.target)) {
        search.classList.remove('active');
    }

    if (headerCatalogWrap && !headerCatalogWrap.contains(event.target)) {
        headerCatalog.classList.remove('active');
    }

    if (catalogTabHead && !catalogTabHead.contains(event.target)) {
        catalogtabHeadList.classList.remove('active')
    }

    if (catalogSort && !catalogSort.contains(event.target)) {
        catalogSortList.classList.remove('active');
    }

    if (selects.length) {
        selects.forEach(el => {
            if (!el.contains(event.target)) {
                el.classList.remove('active')
            }
        })
    }
})

const swpChild = new Swiper('.product .swp-child', {
    slidesPerView: 'auto',
    spaceBetween: 10,
    breakpoints: {
        992: {
            slidesPerView: 5,
            spaceBetween: 20,
        }
    },
})

const swpParent = new Swiper('.product .swp-parent', {
    slidesPerView: 1,
    thumbs: {
        swiper: swpChild,
    },
});

const otherProductSwp = new Swiper('.other-product__swp', {
    slidesPerView: 'auto',
    spaceBetween: 10,
    loop: true,
    breakpoints: {
        1400: {
            slidesPerView: 6,
            spaceBetween: 20,
        }
    },
    navigation: {
        nextEl: '.other-product__head .btn-next',
        prevEl: '.other-product__head .btn-prev',
    }
})