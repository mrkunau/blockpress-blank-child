window.addEventListener("resize", navigation_resize );

function navigation_resize() {
    if( window.innerWidth >= 1200 ){
        if( document.querySelector(".site-header-2 .site-navigation .wp-block-navigation__responsive-container") !== null ){
            document.querySelector(".site-header-2 .site-navigation .wp-block-navigation__responsive-container").classList.remove('is-menu-open');
        }
    }
}

window.addEventListener('load', function() {
    if (document.querySelectorAll('.site-navigation button.wp-block-navigation__responsive-container-open').length > 0) {
        var customMenuIcon = document.createElement('div');
        customMenuIcon.className = 'custom-menu-icon';
        for (var i = 0; i < 3; i++) {
            var customMenuIconLine = document.createElement('div');
            customMenuIconLine.className = 'custom-menu-icon-line';
            customMenuIcon.appendChild(customMenuIconLine);
        }
        document.querySelector('.site-navigation button.wp-block-navigation__responsive-container-open').appendChild(customMenuIcon);
    }
});

window.addEventListener('click', function(e) {
    if (e.target.matches('.wp-block-navigation-submenu__toggle')) {
        var parentItem = e.target.parentElement;
        var submenuContainer = e.target.nextElementSibling;
        submenuContainer.style.display = submenuContainer.style.display === 'none' ? 'block' : 'none';
        if (e.target.classList.contains('open-sub')) {
            e.target.classList.remove('open-sub');
            parentItem.classList.remove('current-open');
        } else {
            e.target.classList.add('open-sub');
            parentItem.classList.add('current-open');
        }
    }
    if (e.target.matches('.wp-block-navigation-submenu__toggle svg')) {
        var toggle = e.target.parentElement;
        var parentItem = toggle.parentElement;
        var submenu = toggle.nextElementSibling;
        submenu.style.display = submenu.style.display === 'block' ? 'none' : 'block';
        if (toggle.classList.contains('open-sub')) {
          toggle.classList.remove('open-sub');
          parentItem.classList.remove('current-open');
        } else {
          toggle.classList.add('open-sub');
          parentItem.classList.add('current-open');
        }
    }
});

document.addEventListener('click', function(event) {
    if (event.target.matches('.plus, .minus')) {

        // Get values
        var quantityEl = event.target.closest('.quantity');
        var qtyEl = quantityEl.querySelector('.qty');
        var currentVal = parseFloat(qtyEl.value);
        var max = parseFloat(qtyEl.getAttribute('max'));
        var min = parseFloat(qtyEl.getAttribute('min'));
        var step = qtyEl.getAttribute('step');

        // Format values
        if (!currentVal || currentVal === '' || isNaN(currentVal)) {
          currentVal = 0;
        }
        if (max === '' || isNaN(max)) {
          max = '';
        }
        if (min === '' || isNaN(min)) {
          min = 0;
        }
        if (step === 'any' || step === '' || step === undefined || isNaN(parseFloat(step))) {
          step = 1;
        }

        // Change the value
        if (event.target.classList.contains('plus')) {

          if (max && (max == currentVal || currentVal > max)) {
            qtyEl.value = max;
          } else {
            qtyEl.value = currentVal + parseFloat(step);
          }

        } else {

          if (min && (min == currentVal || currentVal < min)) {
            qtyEl.value = min;
          } else if (currentVal > 0) {
            qtyEl.value = currentVal - parseFloat(step);
          }

        }

        // Trigger change event
        var changeEvent = new Event('change', { bubbles: true });
        qtyEl.dispatchEvent(changeEvent);
    }
});


