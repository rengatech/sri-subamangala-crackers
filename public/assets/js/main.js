(function () {
    "use strict";

    /**
     * Easy selector helper function
     */
    const select = (el, all = false) => {
        el = el.trim();
        if (all) {
            return [...document.querySelectorAll(el)];
        } else {
            return document.querySelector(el);
        }
    };

    /**
     * Easy event listener function
     */
    const on = (type, el, listener, all = false) => {
        let selectEl = select(el, all);
        if (selectEl) {
            if (all) {
                selectEl.forEach((e) => e.addEventListener(type, listener));
            } else {
                selectEl.addEventListener(type, listener);
            }
        }
    };

    /**
     * Easy on scroll event listener
     */
    const onscroll = (el, listener) => {
        el.addEventListener("scroll", listener);
    };

    /**
     * Navbar links active state on scroll
     */
    
    // let navbarlinks = select("#navbar .scrollto", true);
    // const navbarlinksActive = () => {
    //     let position = window.scrollY + 200;
    //     navbarlinks.forEach((navbarlink) => {
    //         if (!navbarlink.hash) return;
    //         let section = select(navbarlink.hash);
    //         if (!section) return;
    //         if (
    //             position >= section.offsetTop &&
    //             position <= section.offsetTop + section.offsetHeight
    //         ) {
    //             navbarlink.classList.add("active");
    //         } else {
    //             navbarlink.classList.remove("active");
    //         }
    //     });
    // };
    // window.addEventListener("load", navbarlinksActive);
    // onscroll(document, navbarlinksActive);

    /**
     * Scrolls to an element with header offset
     */
    const scrollto = (el) => {
        let header = select("#header");
        let offset = header.offsetHeight;

        if (!header.classList.contains("header-scrolled")) {
            offset -= 16;
        }

        let elementPos = select(el).offsetTop;
        window.scrollTo({
            top: elementPos - offset,
            behavior: "smooth",
        });
    };

    /**
     * Toggle .header-scrolled class to #header when page is scrolled
     */
    let selectHeader = select("#header");
    if (selectHeader) {
        const headerScrolled = () => {
            if (window.scrollY > 100) {
                selectHeader.classList.add("header-scrolled");
            } else {
                selectHeader.classList.remove("header-scrolled");
            }
        };
        window.addEventListener("load", headerScrolled);
        onscroll(document, headerScrolled);
    }

    /**
     * Back to top button
     */
    let backtotop = select(".back-to-top");
    if (backtotop) {
        const toggleBacktotop = () => {
            if (window.scrollY > 100) {
                backtotop.classList.add("active");
            } else {
                backtotop.classList.remove("active");
            }
        };
        window.addEventListener("load", toggleBacktotop);
        onscroll(document, toggleBacktotop);
    }

    /**
     * Mobile nav toggle
     */
    on("click", ".mobile-nav-toggle", function (e) {
        select("#navbar").classList.toggle("navbar-mobile");
        this.classList.toggle("bi-list");
        this.classList.toggle("bi-x");
    });

    /**
     * Mobile nav dropdowns activate
     */
    on(
        "click",
        ".navbar .dropdown > a",
        function (e) {
            if (select("#navbar").classList.contains("navbar-mobile")) {
                e.preventDefault();
                this.nextElementSibling.classList.toggle("dropdown-active");
            }
        },
        true
    );

    /**
     * Scrool with ofset on links with a class name .scrollto
     */
    // on(
    //     "click",
    //     ".scrollto",
    //     function (e) {
    //         if (select(this.hash)) {
    //             e.preventDefault();

    //             let navbar = select("#navbar");
    //             if (navbar.classList.contains("navbar-mobile")) {
    //                 navbar.classList.remove("navbar-mobile");
    //                 let navbarToggle = select(".mobile-nav-toggle");
    //                 navbarToggle.classList.toggle("bi-list");
    //                 navbarToggle.classList.toggle("bi-x");
    //             }
    //             scrollto(this.hash);
    //         }
    //     },
    //     true
    // );

    /**
     * Scroll with ofset on page load with hash links in the url
     */
    window.addEventListener("load", () => {
        if (window.location.hash) {
            if (select(window.location.hash)) {
                scrollto(window.location.hash);
            }
        }
    });
    /**
     * Clients Slider
     */
    new Swiper(".clients-slider", {
        speed: 400,
        loop: true,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        slidesPerView: "auto",
        pagination: {
            el: ".swiper-pagination",
            type: "bullets",
            clickable: true,
        },
        breakpoints: {
            320: {
                slidesPerView: 2,
                spaceBetween: 40,
            },
            480: {
                slidesPerView: 3,
                spaceBetween: 60,
            },
            640: {
                slidesPerView: 4,
                spaceBetween: 80,
            },
            992: {
                slidesPerView: 6,
                spaceBetween: 120,
            },
        },
    });

    /**
     * Porfolio isotope and filter
     */
    window.addEventListener("load", () => {
        let portfolioContainer = select(".portfolio-container");
        if (portfolioContainer) {
            let portfolioIsotope = new Isotope(portfolioContainer, {
                itemSelector: ".portfolio-item",
                layoutMode: "fitRows",
            });

            let portfolioFilters = select("#portfolio-flters li", true);

            on(
                "click",
                "#portfolio-flters li",
                function (e) {
                    e.preventDefault();
                    portfolioFilters.forEach(function (el) {
                        el.classList.remove("filter-active");
                    });
                    this.classList.add("filter-active");

                    portfolioIsotope.arrange({
                        filter: this.getAttribute("data-filter"),
                    });
                    portfolioIsotope.on("arrangeComplete", function () {
                        AOS.refresh();
                    });
                },
                true
            );
        }
    });

    /**
     * Initiate portfolio lightbox
     */
    // const portfolioLightbox = GLightbox({
    //     selector: ".portfolio-lightbox",
    // });

    /**
     * Portfolio details slider
     */
    new Swiper(".portfolio-details-slider", {
        speed: 400,
        loop: true,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        pagination: {
            el: ".swiper-pagination",
            type: "bullets",
            clickable: true,
        },
    });

    /**
     * Testimonials slider
     */
    new Swiper(".testimonials-slider", {
        speed: 600,
        loop: true,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        slidesPerView: "auto",
        pagination: {
            el: ".swiper-pagination",
            type: "bullets",
            clickable: true,
        },
        breakpoints: {
            320: {
                slidesPerView: 1,
                spaceBetween: 20,
            },

            1200: {
                slidesPerView: 3,
                spaceBetween: 20,
            },
        },
    });

    /**
     * Animation on scroll
     */
    window.addEventListener("load", () => {
        AOS.init({
            duration: 1000,
            easing: "ease-in-out",
            once: true,
            mirror: false,
        });
    });

    /**
     * Initiate Pure Counter
     */
    new PureCounter();
})();

////add button

$(".mainBtn").click(function () {

    $(this).siblings(".minusBtn ,.plusBtn").css("display", "inline-block");
});

var target = $("section2");
target.after('<div class="affix" id="affix"></div>');

var affix = $(".affix");
affix.append(target.clone(true));

// Show affix on scroll.
var element = document.getElementById("affix");
if (element !== null) {
    var position = target.position();
    window.addEventListener("scroll", function () {
        var height = $(window).scrollTop();
        if (height > position.top) {
            target.css("visibility", "hidden");
            affix.css("display", "block");
        } else {
            affix.css("display", "none");
            target.css("visibility", "visible");
        }
    });
}

// const accordion = document.getElementsByClassName('contentBx');
// for (i =0; i <accordion.length;i++){
//     accordion[i].addEventListener('click',function(){
//         this.classList.toggle('active')
//     })
// }

// https://john-dugan.com/javascript-debounce/
// https://codepen.io/johndugan/pen/BNwBWL?editors=001
var debounce = function (func, wait, immediate) {
    "use strict";

    var timeout;
    return function () {
        var context = this;
        var args = arguments;
        var later = function () {
            timeout = null;
            if (!immediate) {
                func.apply(context, args);
            }
        };
        var callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait || 200);
        if (callNow) {
            func.apply(context, args);
        }
    };
};

// peek-a-boo.7.3.js - Mike Foskett - https://websemantics.uk/articles/peek-a-boo-v7/

// Show - hide a block - adapted for FAQ
// Requires:
//    setAttribute / getAttribute (IE9+)
//    classList (IE10+)  - disabled
//    addEventListener (IE9+)
//    requestAnimationFrame (IE10+) - replace with requestAF() for IE9
//    querySelectorAll
//    preventDefault
//    debounce()

// FAQ version:
// v7.4 Added: open an question from an internal anchor
// v7.3 Expanded when URI fragment matches the target ID
// v7.2 HTML button reinstated, js adjusted.
//			Initial open/close state reworked

var Pab = (function (window, document, debounce) {
    // Terminology used:
    // toggle - The dynamically added button used to toggle the hidden content
    // target - The object which contains the hidden content
    // toggleParent - The object which will, or does, contain the toggle button

    "use strict";

    var dataAttr = "data-pab";
    var attrName = dataAttr.replace("data-", "") + "_";
    var btnClass = dataAttr.replace("data-", "") + "-btn";
    var dataExpandAttr = dataAttr + "-expand";
    var internalId = 1;

    function $(selector) {
        return Array.prototype.slice.call(document.querySelectorAll(selector));
    }

    function _isExpanded(obj) {
        // or not aria-hidden
        return (
            obj &&
            (obj.getAttribute("aria-expanded") === "true" ||
                obj.getAttribute("aria-hidden") === "false")
        );
    }

    // This function is globally reusable. Perhaps externalise for reuse?
    // Get height of an element object
    // Assumes it is hidden by max-height: 0 in the CSS
    var _getHiddenObjectHeight = function (obj) {
        obj.setAttribute("style", "max-height: none");
        var height = obj.scrollHeight;
        obj.removeAttribute("style");
        return height;
    };

    /* Not enough support to be truly useful.
   Under most circumstance aria-expanded is sufficient.
  var _setToggleSvgTitle = function(toggle) {
    var title = toggle.getElementsByTagName('title');
    if (title && title[0]) {
      title[0].innerHTML = _isExpanded(toggle) ? 'Hide' : 'Show';
    }
  };
*/

    var _openCloseToggleTarget = function (toggle, target, isExpanded) {
        toggle.setAttribute("aria-expanded", !isExpanded);
        _setToggleMaxHeight(target);
        window.requestAnimationFrame(function () {
            target.setAttribute("aria-hidden", isExpanded);
        });
        // _setToggleSvgTitle(toggle); - not enough support to be useful
    };

    /* // deprecated to support IE 9 FAQ
  var _toggleParentClass = function (event) {
    var cls = attrName + 'btn_';
    switch (event.type) {
      case 'focus' :
        //event.target.parentNode.classList.add(cls + 'focused');
        event.target.parentNode.className += ' ' + cls + 'focused';
        break;
      case 'blur' :
        event.target.parentNode.classList.remove(cls + 'focused');
        break;
      case 'mouseover' :
        event.target.parentNode.classList.add(cls + 'hovered');
        break;
      case 'mouseout' :
        event.target.parentNode.classList.remove(cls + 'hovered');
        break;
    }
  };
*/

    var _setToggleMaxHeight = function (target) {
        if (_isExpanded(target)) {
            // max-height overidden by CSS !important
            // target.style.maxHeight = 0;
        } else {
            target.style.maxHeight = _getHiddenObjectHeight(target) + "px";
        }
    };

    var _toggleClicked = function (event) {
        var toggle = event.target;
        var target;
        var isExpanded;

        if (toggle) {
            // To prevent children bubbling up to parent causing more than one click event
            event.stopPropagation();

            target = document.getElementById(
                toggle.getAttribute("aria-controls")
            );
            if (target) {
                isExpanded = _isExpanded(toggle);
                _openCloseToggleTarget(toggle, target, isExpanded);
            }
        }
    };

    var _addToggleListeners = function (toggle) {
        // Simpler to mangage here rather than in a global handler (consider hover and blur)

        // Parent of toggle and target - Deprecated to support IE 9
        //toggle.addEventListener('focus', _toggleParentClass, false);
        //toggle.addEventListener('blur', _toggleParentClass, false);
        //toggle.addEventListener('mouseout', _toggleParentClass, false);
        //toggle.addEventListener('mouseover', _toggleParentClass, false);

        toggle.addEventListener("click", _toggleClicked, false);
    };

    var _setUpToggle = function (toggle) {
        // Create a html button, add content from parent, replace original parent content.
        var btn = document.createElement("button");

        btn.className = btnClass;
        btn.innerHTML = toggle.innerHTML;
        btn.setAttribute("aria-expanded", false);
        btn.setAttribute("id", attrName + internalId++);
        btn.setAttribute("aria-controls", toggle.getAttribute(dataAttr));

        toggle.innerHTML = "";
        toggle.appendChild(btn);

        return btn;
    };

    // Prestating the container class in the HTML allows the CSS to render before JS kicks in.
    // Add container class to parent if not prestated
    var _setUpToggleParent = function (toggle) {
        var parent = toggle.parentElement;
        if (parent && !parent.className.match(attrName + "container")) {
            //parent.classList.add(attrName + 'container');
            parent.className += " " + attrName + "container";
        }
    };

    var _addToggleSVG = function (toggle) {
        var clone = toggle.cloneNode(true);
        if (!clone.innerHTML.match("svg")) {
            // HTML SVG definition allows more control
            clone.innerHTML +=
                "<svg role=presentational focusable=false class=" +
                dataAttr.replace("data-", "") +
                '-svg-plus><use class="use-plus" xlink:href="#icon-vert" /><use xlink:href="#icon-hori"/></svg>';
            //requestAnimationFrame(function () {
            toggle.parentElement.replaceChild(clone, toggle);
            //});
        }
        return clone;
    };

    var _setUpTargetAria = function (toggle, target) {
        target.setAttribute("aria-hidden", !_isExpanded(toggle));
        target.setAttribute("aria-labelledby", toggle.id);
    };

    var _resetAllTargetsMaxHeight = function () {
        var toggles = document.querySelectorAll("[" + dataAttr + "]");
        var i = toggles.length;
        var target;
        while (i--) {
            target = document.getElementById(toggles[i].getAttribute(dataAttr));
            if (target) {
                target.style.maxHeight = _getHiddenObjectHeight(target) + "px";
            }
        }
    };

    var isMustardCut = function () {
        return document.querySelectorAll && document.addEventListener;
    };

    var _openIfRequired = function (toggle, target) {
        var fragmentId = window.location.hash.replace("#", "");

        // Expand by default 'data-pab-expand' small delay applied
        if (toggle.parentElement.hasAttribute(dataExpandAttr)) {
            setTimeout(function () {
                _openCloseToggleTarget(toggle, target, _isExpanded(toggle));
            }, 500);
        }

        // Check url fragment and if target ID matches, open it
        if (target.id === fragmentId) {
            setTimeout(function () {
                _openCloseToggleTarget(toggle, target, false);
                toggle.focus();
            }, 500);
        }
    };

    var addSingleToggleTarget = function (toggleParent) {
        var targetId = toggleParent.getAttribute(dataAttr);
        var target = document.getElementById(targetId);
        var toggle;

        if (target && isMustardCut) {
            toggle = _setUpToggle(toggleParent);
            _setUpToggleParent(toggleParent);
            toggle = _addToggleSVG(toggle);
            _setUpTargetAria(toggle, target);
            _addToggleListeners(toggle);
            _openIfRequired(toggle, target);
        }
    };

    var hashChanged = function (e) {
        var fragmentId = window.location.hash.replace("#", "");
        var toggle = document.querySelector(
            "#" + fragmentId + " > ." + btnClass
        );
        var target = document.getElementById(
            toggle.getAttribute("aria-controls")
        );
        if (!toggle || !target) {
            return false;
        }

        toggle.focus();
        toggle.scrollIntoView({
            behavior: "smooth",
            block: "start",
            inline: "nearest",
        });

        _openCloseToggleTarget(toggle, target, false);
    };

    var addToggles = function () {
        // Iterate over all toggles (elements with the 'data-pab' attribute)
        var togglesMap = $("[" + dataAttr + "]").reduce(function (
            temp,
            toggleParent
        ) {
            addSingleToggleTarget(toggleParent);
            return true;
        },
        {});

        return true;
    };

    if (isMustardCut) {
        window.addEventListener("load", addToggles, false);

        // Recalculate all target max-heights after (debounced) window is resized.
        window.addEventListener(
            "resize",
            debounce(_resetAllTargetsMaxHeight, 500),
            false
        );

        // On fragment change
        window.addEventListener("hashchange", hashChanged, false);
    }

    return {
        // Exposes an addition function to the global scope allowing toggle & target to be added dynamically.
        add: addSingleToggleTarget,
    };
})(window, document, debounce);

// To add dynamically created toggles:
// Pab.add(toggle-object); // Add individual toggle & target

// setTimeout(function(){
//   document.querySelector('.pab_container').innerHTML += `
//   <dt data-pab=faq_6><span>Test dynamic insertion</span></dt>
//   <dd id=faq_6>
//     <div>
//       <p>Dynamically added to <code>dl</code>.</p>
//     </div>
//   </dd>`;
//   Pab.add(document.querySelector('[data-pab=faq_6]'));
// }, 2000);

// setTimeout(function(){
//   document.getElementById('injection_point').innerHTML += `
//   <div data-pab=faq_7><span>Test dynamic insertion</span></div>
//   <div id=faq_7>
//     <div>
//       <p>Dynamically added externally to the <code>dl</code>.</p>
//     </div>
//   </div>`;
//   Pab.add(document.querySelector('[data-pab=faq_7]'));
// }, 2000);

(function ($) {
    $("body").addClass("js");
    $.fn.accordion1 = function () {
        $(this).each(function () {
            var a = $(this);
            a.find("dt")
                .click(function (e) {
                    if ($(this).hasClass("open")) {
                        $(this).removeClass("open");
                    } else {
                        $(this).addClass("open");
                    }
                    e.preventDefault();
                    return false;
                })
                .first()
                .addClass("open");
        });
        return $(this);
    };
    $(".accordion1").accordion1();
})(jQuery);




  