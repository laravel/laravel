const TIMEOUT = 150

class MegaDropdown {
  constructor(element, options = {}) {
    this._onHover = options.trigger === 'hover' || element.getAttribute('data-trigger') === 'hover'

    this._container = MegaDropdown._findParent(element, 'mega-dropdown')
    if (!this._container) return

    this._menu = this._container.querySelector('.dropdown-toggle ~ .dropdown-menu')
    if (!this._menu) return

    element.setAttribute('aria-expanded', 'false')

    this._el = element
    this._bindEvents()
  }

  open() {
    if (this._timeout) {
      clearTimeout(this._timeout)
      this._timeout = null
    }
    if (this._focusTimeout) {
      clearTimeout(this._focusTimeout)
      this._focusTimeout = null
    }

    if (this._el.getAttribute('aria-expanded') !== 'true') {
      this._triggerEvent('show')
      this._container.classList.add('show')
      this._menu.classList.add('show')
      this._el.setAttribute('aria-expanded', 'true')
      this._el.focus()
      this._triggerEvent('shown')
    }
  }

  close(force) {
    if (this._timeout) {
      clearTimeout(this._timeout)
      this._timeout = null
    }
    if (this._focusTimeout) {
      clearTimeout(this._focusTimeout)
      this._focusTimeout = null
    }

    if (this._onHover && !force) {
      this._timeout = setTimeout(() => {
        if (this._timeout) {
          clearTimeout(this._timeout)
          this._timeout = null
        }
        this._close()
      }, TIMEOUT)
    } else {
      this._close()
    }
  }

  toggle() {
    // eslint-disable-next-line no-unused-expressions
    this._el.getAttribute('aria-expanded') === 'true' ? this.close(true) : this.open()
  }

  destroy() {
    this._unbindEvents()
    this._el = null

    if (this._timeout) {
      clearTimeout(this._timeout)
      this._timeout = null
    }

    if (this._focusTimeout) {
      clearTimeout(this._focusTimeout)
      this._focusTimeout = null
    }
  }

  _close() {
    if (this._el.getAttribute('aria-expanded') === 'true') {
      this._triggerEvent('hide')
      this._container.classList.remove('show')
      this._menu.classList.remove('show')
      this._el.setAttribute('aria-expanded', 'false')
      this._triggerEvent('hidden')
    }
  }

  _bindEvents() {
    this._elClickEvnt = e => {
      e.preventDefault()
      this.toggle()
    }
    this._el.addEventListener('click', this._elClickEvnt)

    this._bodyClickEvnt = e => {
      if (!this._container.contains(e.target) && this._container.classList.contains('show')) {
        this.close(true)
      }
    }
    document.body.addEventListener('click', this._bodyClickEvnt, true)

    this._menuClickEvnt = e => {
      if (e.target.classList.contains('mega-dropdown-link')) {
        this.close(true)
      }
    }
    this._menu.addEventListener('click', this._menuClickEvnt, true)

    this._focusoutEvnt = () => {
      if (this._focusTimeout) {
        clearTimeout(this._focusTimeout)
        this._focusTimeout = null
      }

      if (this._el.getAttribute('aria-expanded') !== 'true') return

      this._focusTimeout = setTimeout(() => {
        if (
          document.activeElement.tagName.toUpperCase() !== 'BODY' &&
          MegaDropdown._findParent(document.activeElement, 'mega-dropdown') !== this._container
        ) {
          this.close(true)
        }
      }, 100)
    }
    this._container.addEventListener('focusout', this._focusoutEvnt, true)

    if (this._onHover) {
      this._enterEvnt = () => {
        if (window.getComputedStyle(this._menu, null).getPropertyValue('position') === 'static') return
        this.open()
      }
      this._leaveEvnt = () => {
        if (window.getComputedStyle(this._menu, null).getPropertyValue('position') === 'static') return
        this.close()
      }

      this._el.addEventListener('mouseenter', this._enterEvnt)
      this._menu.addEventListener('mouseenter', this._enterEvnt)
      this._el.addEventListener('mouseleave', this._leaveEvnt)
      this._menu.addEventListener('mouseleave', this._leaveEvnt)
    }
  }

  _unbindEvents() {
    if (this._elClickEvnt) {
      this._el.removeEventListener('click', this._elClickEvnt)
      this._elClickEvnt = null
    }
    if (this._bodyClickEvnt) {
      document.body.removeEventListener('click', this._bodyClickEvnt, true)
      this._bodyClickEvnt = null
    }
    if (this._menuClickEvnt) {
      this._menu.removeEventListener('click', this._menuClickEvnt, true)
      this._menuClickEvnt = null
    }
    if (this._focusoutEvnt) {
      this._container.removeEventListener('focusout', this._focusoutEvnt, true)
      this._focusoutEvnt = null
    }
    if (this._enterEvnt) {
      this._el.removeEventListener('mouseenter', this._enterEvnt)
      this._menu.removeEventListener('mouseenter', this._enterEvnt)
      this._enterEvnt = null
    }
    if (this._leaveEvnt) {
      this._el.removeEventListener('mouseleave', this._leaveEvnt)
      this._menu.removeEventListener('mouseleave', this._leaveEvnt)
      this._leaveEvnt = null
    }
  }

  static _findParent(el, cls) {
    if (el.tagName.toUpperCase() === 'BODY') return null
    el = el.parentNode
    while (el.tagName.toUpperCase() !== 'BODY' && !el.classList.contains(cls)) {
      el = el.parentNode
    }
    return el.tagName.toUpperCase() !== 'BODY' ? el : null
  }

  _triggerEvent(event) {
    if (document.createEvent) {
      let customEvent

      if (typeof Event === 'function') {
        customEvent = new Event(event)
      } else {
        customEvent = document.createEvent('Event')
        customEvent.initEvent(event, false, true)
      }

      this._container.dispatchEvent(customEvent)
    } else {
      this._container.fireEvent(`on${event}`, document.createEventObject())
    }
  }
}

export { MegaDropdown }
