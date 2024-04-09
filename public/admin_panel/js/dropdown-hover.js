// Add onHover event for dropdowns

;(function ($) {
  if (!$ || !$.fn) return

  const SELECTOR = '[data-bs-toggle=dropdown][data-trigger=hover]'
  const TIMEOUT = 150

  function openDropdown($i) {
    let t = $i.data('dd-timeout')

    if (t) {
      clearTimeout(t)
      t = null
      $i.data('dd-timeout', t)
    }

    if ($i.attr('aria-expanded') !== 'true') $i.dropdown('toggle')
  }

  function closeDropdown($i) {
    let t = $i.data('dd-timeout')

    if (t) clearTimeout(t)

    t = setTimeout(() => {
      let t2 = $i.data('dd-timeout')

      if (t2) {
        clearTimeout(t2)
        t2 = null
        $i.data('dd-timeout', t2)
      }

      if ($i.attr('aria-expanded') === 'true') $i.dropdown('toggle')
    }, TIMEOUT)

    $i.data('dd-timeout', t)
  }

  $(function () {
    $('body')
      .on('mouseenter', `${SELECTOR}, ${SELECTOR} ~ .dropdown-menu`, function () {
        const $toggle = $(this).hasClass('dropdown-toggle') ? $(this) : $(this).prev('.dropdown-toggle')
        const $dropdown = $(this).hasClass('dropdown-menu') ? $(this) : $(this).next('.dropdown-menu')

        if (window.getComputedStyle($dropdown[0], null).getPropertyValue('position') === 'static') return

        // Set hovered flag
        if ($(this).is(SELECTOR)) {
          $(this).data('hovered', true)
        }

        openDropdown($(this).hasClass('dropdown-toggle') ? $(this) : $(this).prev('.dropdown-toggle'))
      })
      .on('mouseleave', `${SELECTOR}, ${SELECTOR} ~ .dropdown-menu`, function () {
        const $toggle = $(this).hasClass('dropdown-toggle') ? $(this) : $(this).prev('.dropdown-toggle')
        const $dropdown = $(this).hasClass('dropdown-menu') ? $(this) : $(this).next('.dropdown-menu')

        if (window.getComputedStyle($dropdown[0], null).getPropertyValue('position') === 'static') return

        // Remove hovered flag
        if ($(this).is(SELECTOR)) {
          $(this).data('hovered', false)
        }

        closeDropdown($(this).hasClass('dropdown-toggle') ? $(this) : $(this).prev('.dropdown-toggle'))
      })
      .on('hide.bs.dropdown', function (e) {
        if ($(this).find(SELECTOR).data('hovered')) e.preventDefault()
      })
  })
})(window.jQuery)
