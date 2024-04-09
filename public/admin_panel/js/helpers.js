// Constants
const TRANS_EVENTS = ['transitionend', 'webkitTransitionEnd', 'oTransitionEnd']
const TRANS_PROPERTIES = ['transition', 'MozTransition', 'webkitTransition', 'WebkitTransition', 'OTransition']
const INLINE_STYLES = `
.layout-menu-fixed .layout-navbar-full .layout-menu,
.layout-menu-fixed-offcanvas .layout-navbar-full .layout-menu {
  top: {navbarHeight}px !important;
}
.layout-page {
  padding-top: {navbarHeight}px !important;
}
.content-wrapper {
  padding-bottom: {footerHeight}px !important;
}`

// Guard
function requiredParam(name) {
  throw new Error(`Parameter required${name ? `: \`${name}\`` : ''}`)
}

const Helpers = {
  // Root Element
  ROOT_EL: typeof window !== 'undefined' ? document.documentElement : null,

  // Large screens breakpoint
  LAYOUT_BREAKPOINT: 1200,

  // Resize delay in milliseconds
  RESIZE_DELAY: 200,

  menuPsScroll: null,

  mainMenu: null,

  // Internal variables
  _curStyle: null,
  _styleEl: null,
  _resizeTimeout: null,
  _resizeCallback: null,
  _transitionCallback: null,
  _transitionCallbackTimeout: null,
  _listeners: [],
  _initialized: false,
  _autoUpdate: false,
  _lastWindowHeight: 0,

  // *******************************************************************************
  // * Utilities

  // ---
  // Scroll To Active Menu Item
  _scrollToActive(animate = false, duration = 500) {
    const layoutMenu = this.getLayoutMenu()

    if (!layoutMenu) return

    let activeEl = layoutMenu.querySelector('li.menu-item.active:not(.open)')

    if (activeEl) {
      // t = current time
      // b = start value
      // c = change in value
      // d = duration
      const easeInOutQuad = (t, b, c, d) => {
        t /= d / 2
        if (t < 1) return (c / 2) * t * t + b
        t -= 1
        return (-c / 2) * (t * (t - 2) - 1) + b
      }

      const element = this.getLayoutMenu().querySelector('.menu-inner')

      if (typeof activeEl === 'string') {
        activeEl = document.querySelector(activeEl)
      }
      if (typeof activeEl !== 'number') {
        activeEl = activeEl.getBoundingClientRect().top + element.scrollTop
      }

      // If active element's top position is less than 2/3 (66%) of menu height than do not scroll
      if (activeEl < parseInt((element.clientHeight * 2) / 3, 10)) return

      const start = element.scrollTop
      const change = activeEl - start - parseInt(element.clientHeight / 2, 10)
      const startDate = +new Date()

      if (animate === true) {
        const animateScroll = () => {
          const currentDate = +new Date()
          const currentTime = currentDate - startDate
          const val = easeInOutQuad(currentTime, start, change, duration)
          element.scrollTop = val
          if (currentTime < duration) {
            requestAnimationFrame(animateScroll)
          } else {
            element.scrollTop = change
          }
        }
        animateScroll()
      } else {
        element.scrollTop = change
      }
    }
  },

  // ---
  // Swipe In Gesture
  _swipeIn(targetEl, callback) {
    const { Hammer } = window
    if (typeof Hammer !== 'undefined' && typeof targetEl === 'string') {
      // Swipe menu gesture
      const swipeInElement = document.querySelector(targetEl)

      if (swipeInElement) {
        const hammerInstance = new Hammer(swipeInElement)

        hammerInstance.on('panright', callback)
      }
    }
  },

  // ---
  // Swipe Out Gesture
  _swipeOut(targetEl, callback) {
    const { Hammer } = window
    if (typeof Hammer !== 'undefined' && typeof targetEl === 'string') {
      setTimeout(() => {
        // Swipe menu gesture
        const swipeOutElement = document.querySelector(targetEl)

        if (swipeOutElement) {
          const hammerInstance = new Hammer(swipeOutElement)

          hammerInstance.get('pan').set({ direction: Hammer.DIRECTION_ALL, threshold: 250 })
          hammerInstance.on('panleft', callback)
        }
      }, 500)
    }
  },

  // ---
  // Swipe Out On Overlay Tap
  _overlayTap(targetEl, callback) {
    const { Hammer } = window

    if (typeof Hammer !== 'undefined' && typeof targetEl === 'string') {
      // Swipe out overlay element
      const swipeOutOverlayElement = document.querySelector(targetEl)

      if (swipeOutOverlayElement) {
        const hammerInstance = new Hammer(swipeOutOverlayElement)

        hammerInstance.on('tap', callback)
      }
    }
  },

  // ---
  // Add classes
  _addClass(cls, el = this.ROOT_EL) {
    if (el.length !== undefined) {
      // Add classes to multiple elements
      el.forEach(e => {
        cls.split(' ').forEach(c => e.classList.add(c))
      })
    } else {
      // Add classes to single element
      cls.split(' ').forEach(c => el.classList.add(c))
    }
  },

  // ---
  // Remove classes
  _removeClass(cls, el = this.ROOT_EL) {
    if (el.length !== undefined) {
      // Remove classes to multiple elements
      el.forEach(e => {
        cls.split(' ').forEach(c => e.classList.remove(c))
      })
    } else {
      // Remove classes to single element
      cls.split(' ').forEach(c => el.classList.remove(c))
    }
  },

  // Toggle classes
  _toggleClass(el = this.ROOT_EL, cls1, cls2) {
    if (el.classList.contains(cls1)) {
      el.classList.replace(cls1, cls2)
    } else {
      el.classList.replace(cls2, cls1)
    }
  },

  // ---
  // Has class
  _hasClass(cls, el = this.ROOT_EL) {
    let result = false

    cls.split(' ').forEach(c => {
      if (el.classList.contains(c)) result = true
    })

    return result
  },

  _findParent(el, cls) {
    if ((el && el.tagName.toUpperCase() === 'BODY') || el.tagName.toUpperCase() === 'HTML') return null
    el = el.parentNode
    while (el && el.tagName.toUpperCase() !== 'BODY' && !el.classList.contains(cls)) {
      el = el.parentNode
    }
    el = el && el.tagName.toUpperCase() !== 'BODY' ? el : null
    return el
  },

  // ---
  // Trigger window event
  _triggerWindowEvent(name) {
    if (typeof window === 'undefined') return

    if (document.createEvent) {
      let event

      if (typeof Event === 'function') {
        event = new Event(name)
      } else {
        event = document.createEvent('Event')
        event.initEvent(name, false, true)
      }

      window.dispatchEvent(event)
    } else {
      window.fireEvent(`on${name}`, document.createEventObject())
    }
  },

  // ---
  // Trigger event
  _triggerEvent(name) {
    this._triggerWindowEvent(`layout${name}`)

    this._listeners.filter(listener => listener.event === name).forEach(listener => listener.callback.call(null))
  },

  // ---
  // Update style
  _updateInlineStyle(navbarHeight = 0, footerHeight = 0) {
    if (!this._styleEl) {
      this._styleEl = document.createElement('style')
      this._styleEl.type = 'text/css'
      document.head.appendChild(this._styleEl)
    }

    const newStyle = INLINE_STYLES.replace(/\{navbarHeight\}/gi, navbarHeight).replace(
      /\{footerHeight\}/gi,
      footerHeight
    )

    if (this._curStyle !== newStyle) {
      this._curStyle = newStyle
      this._styleEl.textContent = newStyle
    }
  },

  // ---
  // Remove style
  _removeInlineStyle() {
    if (this._styleEl) document.head.removeChild(this._styleEl)
    this._styleEl = null
    this._curStyle = null
  },

  // ---
  // Redraw layout menu (Safari bugfix)
  _redrawLayoutMenu() {
    const layoutMenu = this.getLayoutMenu()

    if (layoutMenu && layoutMenu.querySelector('.menu')) {
      const inner = layoutMenu.querySelector('.menu-inner')
      const { scrollTop } = inner
      const pageScrollTop = document.documentElement.scrollTop

      layoutMenu.style.display = 'none'
      // layoutMenu.offsetHeight
      layoutMenu.style.display = ''
      inner.scrollTop = scrollTop
      document.documentElement.scrollTop = pageScrollTop

      return true
    }

    return false
  },

  // ---
  // Check for transition support
  _supportsTransitionEnd() {
    if (window.QUnit) return false

    const el = document.body || document.documentElement

    if (!el) return false

    let result = false
    TRANS_PROPERTIES.forEach(evnt => {
      if (typeof el.style[evnt] !== 'undefined') result = true
    })

    return result
  },

  // ---
  // Calculate current navbar height
  _getNavbarHeight() {
    const layoutNavbar = this.getLayoutNavbar()

    if (!layoutNavbar) return 0
    if (!this.isSmallScreen()) return layoutNavbar.getBoundingClientRect().height

    // Needs some logic to get navbar height on small screens

    const clonedEl = layoutNavbar.cloneNode(true)
    clonedEl.id = null
    clonedEl.style.visibility = 'hidden'
    clonedEl.style.position = 'absolute'

    Array.prototype.slice.call(clonedEl.querySelectorAll('.collapse.show')).forEach(el => this._removeClass('show', el))

    layoutNavbar.parentNode.insertBefore(clonedEl, layoutNavbar)

    const navbarHeight = clonedEl.getBoundingClientRect().height

    clonedEl.parentNode.removeChild(clonedEl)

    return navbarHeight
  },

  // ---
  // Get current footer height
  _getFooterHeight() {
    const layoutFooter = this.getLayoutFooter()

    if (!layoutFooter) return 0

    return layoutFooter.getBoundingClientRect().height
  },

  // ---
  // Get animation duration of element
  _getAnimationDuration(el) {
    const duration = window.getComputedStyle(el).transitionDuration

    return parseFloat(duration) * (duration.indexOf('ms') !== -1 ? 1 : 1000)
  },

  // ---
  // Set menu hover state
  _setMenuHoverState(hovered) {
    this[hovered ? '_addClass' : '_removeClass']('layout-menu-hover')
  },

  // ---
  // Toggle collapsed
  _setCollapsed(collapsed) {
    if (this.isSmallScreen()) {
      if (collapsed) {
        this._removeClass('layout-menu-expanded')
      } else {
        setTimeout(
          () => {
            this._addClass('layout-menu-expanded')
          },
          this._redrawLayoutMenu() ? 5 : 0
        )
      }
    } else {
      this[collapsed ? '_addClass' : '_removeClass']('layout-menu-collapsed')
    }
  },

  // ---
  // Add layout sivenav toggle animationEnd event
  _bindLayoutAnimationEndEvent(modifier, cb) {
    const menu = this.getMenu()
    const duration = menu ? this._getAnimationDuration(menu) + 50 : 0

    if (!duration) {
      modifier.call(this)
      cb.call(this)
      return
    }

    this._transitionCallback = e => {
      if (e.target !== menu) return
      this._unbindLayoutAnimationEndEvent()
      cb.call(this)
    }

    TRANS_EVENTS.forEach(e => {
      menu.addEventListener(e, this._transitionCallback, false)
    })

    modifier.call(this)

    this._transitionCallbackTimeout = setTimeout(() => {
      this._transitionCallback.call(this, { target: menu })
    }, duration)
  },

  // ---
  // Remove layout sivenav toggle animationEnd event
  _unbindLayoutAnimationEndEvent() {
    const menu = this.getMenu()

    if (this._transitionCallbackTimeout) {
      clearTimeout(this._transitionCallbackTimeout)
      this._transitionCallbackTimeout = null
    }

    if (menu && this._transitionCallback) {
      TRANS_EVENTS.forEach(e => {
        menu.removeEventListener(e, this._transitionCallback, false)
      })
    }

    if (this._transitionCallback) {
      this._transitionCallback = null
    }
  },

  // ---
  // Bind delayed window resize event
  _bindWindowResizeEvent() {
    this._unbindWindowResizeEvent()

    const cb = () => {
      if (this._resizeTimeout) {
        clearTimeout(this._resizeTimeout)
        this._resizeTimeout = null
      }
      this._triggerEvent('resize')
    }

    this._resizeCallback = () => {
      if (this._resizeTimeout) clearTimeout(this._resizeTimeout)
      this._resizeTimeout = setTimeout(cb, this.RESIZE_DELAY)
    }

    window.addEventListener('resize', this._resizeCallback, false)
  },

  // ---
  // Unbind delayed window resize event
  _unbindWindowResizeEvent() {
    if (this._resizeTimeout) {
      clearTimeout(this._resizeTimeout)
      this._resizeTimeout = null
    }

    if (this._resizeCallback) {
      window.removeEventListener('resize', this._resizeCallback, false)
      this._resizeCallback = null
    }
  },

  _bindMenuMouseEvents() {
    if (this._menuMouseEnter && this._menuMouseLeave && this._windowTouchStart) return

    const layoutMenu = this.getLayoutMenu()
    if (!layoutMenu) return this._unbindMenuMouseEvents()

    if (!this._menuMouseEnter) {
      this._menuMouseEnter = () => {
        if (
          this.isSmallScreen() ||
          !this._hasClass('layout-menu-collapsed') ||
          this.isOffcanvas() ||
          this._hasClass('layout-transitioning')
        ) {
          return this._setMenuHoverState(false)
        }

        return this._setMenuHoverState(true)
      }
      layoutMenu.addEventListener('mouseenter', this._menuMouseEnter, false)
      layoutMenu.addEventListener('touchstart', this._menuMouseEnter, false)
    }

    if (!this._menuMouseLeave) {
      this._menuMouseLeave = () => {
        this._setMenuHoverState(false)
      }
      layoutMenu.addEventListener('mouseleave', this._menuMouseLeave, false)
    }

    if (!this._windowTouchStart) {
      this._windowTouchStart = e => {
        if (!e || !e.target || !this._findParent(e.target, '.layout-menu')) {
          this._setMenuHoverState(false)
        }
      }
      window.addEventListener('touchstart', this._windowTouchStart, true)
    }
  },

  _unbindMenuMouseEvents() {
    if (!this._menuMouseEnter && !this._menuMouseLeave && !this._windowTouchStart) return

    const layoutMenu = this.getLayoutMenu()

    if (this._menuMouseEnter) {
      if (layoutMenu) {
        layoutMenu.removeEventListener('mouseenter', this._menuMouseEnter, false)
        layoutMenu.removeEventListener('touchstart', this._menuMouseEnter, false)
      }
      this._menuMouseEnter = null
    }

    if (this._menuMouseLeave) {
      if (layoutMenu) {
        layoutMenu.removeEventListener('mouseleave', this._menuMouseLeave, false)
      }
      this._menuMouseLeave = null
    }

    if (this._windowTouchStart) {
      if (layoutMenu) {
        window.addEventListener('touchstart', this._windowTouchStart, true)
      }
      this._windowTouchStart = null
    }

    this._setMenuHoverState(false)
  },

  // *******************************************************************************
  // * Methods

  scrollToActive(animate = false) {
    this._scrollToActive(animate)
  },

  swipeIn(el, callback) {
    this._swipeIn(el, callback)
  },

  swipeOut(el, callback) {
    this._swipeOut(el, callback)
  },

  overlayTap(el, callback) {
    this._overlayTap(el, callback)
  },

  scrollPageTo(to, duration = 500) {
    // t = current time
    // b = start value
    // c = change in value
    // d = duration
    const easeInOutQuad = (t, b, c, d) => {
      t /= d / 2
      if (t < 1) return (c / 2) * t * t + b
      t -= 1
      return (-c / 2) * (t * (t - 2) - 1) + b
    }

    const element = document.scrollingElement

    if (typeof to === 'string') {
      to = document.querySelector(to)
    }
    if (typeof to !== 'number') {
      to = to.getBoundingClientRect().top + element.scrollTop
    }

    const start = element.scrollTop
    const change = to - start
    const startDate = +new Date()
    // const increment = 20

    const animateScroll = () => {
      const currentDate = +new Date()
      const currentTime = currentDate - startDate
      const val = easeInOutQuad(currentTime, start, change, duration)
      element.scrollTop = val
      if (currentTime < duration) {
        requestAnimationFrame(animateScroll)
      } else {
        element.scrollTop = to
      }
    }
    animateScroll()
  },

  // ---
  // Collapse / expand layout
  setCollapsed(collapsed = requiredParam('collapsed'), animate = true) {
    const layoutMenu = this.getLayoutMenu()

    if (!layoutMenu) return

    this._unbindLayoutAnimationEndEvent()

    if (animate && this._supportsTransitionEnd()) {
      this._addClass('layout-transitioning')
      if (collapsed) this._setMenuHoverState(false)

      this._bindLayoutAnimationEndEvent(
        () => {
          // Collapse / Expand
          this._setCollapsed(collapsed)
        },
        () => {
          this._removeClass('layout-transitioning')
          this._triggerWindowEvent('resize')
          this._triggerEvent('toggle')
          this._setMenuHoverState(false)
        }
      )
    } else {
      this._addClass('layout-no-transition')
      if (collapsed) this._setMenuHoverState(false)

      // Collapse / Expand
      this._setCollapsed(collapsed)

      setTimeout(() => {
        this._removeClass('layout-no-transition')
        this._triggerWindowEvent('resize')
        this._triggerEvent('toggle')
        this._setMenuHoverState(false)
      }, 1)
    }
  },

  // ---
  // Toggle layout
  toggleCollapsed(animate = true) {
    this.setCollapsed(!this.isCollapsed(), animate)
  },

  // ---
  // Set layout positioning
  setPosition(fixed = requiredParam('fixed'), offcanvas = requiredParam('offcanvas')) {
    this._removeClass('layout-menu-offcanvas layout-menu-fixed layout-menu-fixed-offcanvas')

    if (!fixed && offcanvas) {
      this._addClass('layout-menu-offcanvas')
    } else if (fixed && !offcanvas) {
      this._addClass('layout-menu-fixed')
      this._redrawLayoutMenu()
    } else if (fixed && offcanvas) {
      this._addClass('layout-menu-fixed-offcanvas')
      this._redrawLayoutMenu()
    }

    this.update()
  },

  // *******************************************************************************
  // * Getters

  getLayoutMenu() {
    return document.querySelector('.layout-menu')
  },

  getMenu() {
    const layoutMenu = this.getLayoutMenu()

    if (!layoutMenu) return null

    return !this._hasClass('menu', layoutMenu) ? layoutMenu.querySelector('.menu') : layoutMenu
  },

  getLayoutNavbar() {
    return document.querySelector('.layout-navbar')
  },

  getLayoutFooter() {
    return document.querySelector('.content-footer')
  },

  getLayoutContainer() {
    return document.querySelector('.layout-page')
  },

  // *******************************************************************************
  // * Setters

  setNavbarFixed(fixed = requiredParam('fixed')) {
    this[fixed ? '_addClass' : '_removeClass']('layout-navbar-fixed')
    this.update()
  },

  setFooterFixed(fixed = requiredParam('fixed')) {
    this[fixed ? '_addClass' : '_removeClass']('layout-footer-fixed')
    this.update()
  },

  setFlipped(reversed = requiredParam('reversed')) {
    this[reversed ? '_addClass' : '_removeClass']('layout-menu-flipped')
  },

  // *******************************************************************************
  // * Update

  update() {
    if (
      (this.getLayoutNavbar() &&
        ((!this.isSmallScreen() && this.isLayoutNavbarFull() && this.isFixed()) || this.isNavbarFixed())) ||
      (this.getLayoutFooter() && this.isFooterFixed())
    ) {
      this._updateInlineStyle(this._getNavbarHeight(), this._getFooterHeight())
    }

    this._bindMenuMouseEvents()
  },

  setAutoUpdate(enable = requiredParam('enable')) {
    if (enable && !this._autoUpdate) {
      this.on('resize.Helpers:autoUpdate', () => this.update())
      this._autoUpdate = true
    } else if (!enable && this._autoUpdate) {
      this.off('resize.Helpers:autoUpdate')
      this._autoUpdate = false
    }
  },

  // Update custom option based on element
  updateCustomOptionCheck(el) {
    if (el.checked) {
      // If custom option element is radio, remove checked from the siblings (closest `.row`)
      if (el.type === 'radio') {
        const customRadioOptionList = [].slice.call(el.closest('.row').querySelectorAll('.custom-option'))
        customRadioOptionList.map(function (customRadioOptionEL) {
          customRadioOptionEL.closest('.custom-option').classList.remove('checked')
        })
      }
      el.closest('.custom-option').classList.add('checked')
    } else {
      el.closest('.custom-option').classList.remove('checked')
    }
  },

  // *******************************************************************************
  // * Tests

  isRtl() {
    return (
      document.querySelector('body').getAttribute('dir') === 'rtl' ||
      document.querySelector('html').getAttribute('dir') === 'rtl'
    )
  },

  isMobileDevice() {
    return typeof window.orientation !== 'undefined' || navigator.userAgent.indexOf('IEMobile') !== -1
  },

  isSmallScreen() {
    return (
      (window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth) < this.LAYOUT_BREAKPOINT
    )
  },

  isLayoutNavbarFull() {
    return !!document.querySelector('.layout-wrapper.layout-navbar-full')
  },

  isCollapsed() {
    if (this.isSmallScreen()) {
      return !this._hasClass('layout-menu-expanded')
    }
    return this._hasClass('layout-menu-collapsed')
  },

  isFixed() {
    return this._hasClass('layout-menu-fixed layout-menu-fixed-offcanvas')
  },

  isOffcanvas() {
    return this._hasClass('layout-menu-offcanvas layout-menu-fixed-offcanvas')
  },

  isNavbarFixed() {
    return (
      this._hasClass('layout-navbar-fixed') || (!this.isSmallScreen() && this.isFixed() && this.isLayoutNavbarFull())
    )
  },

  isFooterFixed() {
    return this._hasClass('layout-footer-fixed')
  },

  isFlipped() {
    return this._hasClass('layout-menu-flipped')
  },

  isLightStyle() {
    return document.documentElement.classList.contains('light-style')
  },

  isDarkStyle() {
    return document.documentElement.classList.contains('dark-style')
  },

  // *******************************************************************************
  // * Events

  on(event = requiredParam('event'), callback = requiredParam('callback')) {
    const [_event] = event.split('.')
    let [, ...namespace] = event.split('.')
    // let [_event, ...namespace] = event.split('.')
    namespace = namespace.join('.') || null

    this._listeners.push({ event: _event, namespace, callback })
  },

  off(event = requiredParam('event')) {
    const [_event] = event.split('.')
    let [, ...namespace] = event.split('.')
    namespace = namespace.join('.') || null

    this._listeners
      .filter(listener => listener.event === _event && listener.namespace === namespace)
      .forEach(listener => this._listeners.splice(this._listeners.indexOf(listener), 1))
  },

  // *******************************************************************************
  // * Life cycle

  init() {
    if (this._initialized) return
    this._initialized = true

    // Initialize `style` element
    this._updateInlineStyle(0)

    // Bind window resize event
    this._bindWindowResizeEvent()

    // Bind init event
    this.off('init._Helpers')
    this.on('init._Helpers', () => {
      this.off('resize._Helpers:redrawMenu')
      this.on('resize._Helpers:redrawMenu', () => {
        // eslint-disable-next-line no-unused-expressions
        this.isSmallScreen() && !this.isCollapsed() && this._redrawLayoutMenu()
      })

      // Force repaint in IE 10
      if (typeof document.documentMode === 'number' && document.documentMode < 11) {
        this.off('resize._Helpers:ie10RepaintBody')
        this.on('resize._Helpers:ie10RepaintBody', () => {
          if (this.isFixed()) return
          const { scrollTop } = document.documentElement
          document.body.style.display = 'none'
          // document.body.offsetHeight
          document.body.style.display = 'block'
          document.documentElement.scrollTop = scrollTop
        })
      }
    })

    this._triggerEvent('init')
  },

  destroy() {
    if (!this._initialized) return
    this._initialized = false

    this._removeClass('layout-transitioning')
    this._removeInlineStyle()
    this._unbindLayoutAnimationEndEvent()
    this._unbindWindowResizeEvent()
    this._unbindMenuMouseEvents()
    this.setAutoUpdate(false)

    this.off('init._Helpers')

    // Remove all listeners except `init`
    this._listeners
      .filter(listener => listener.event !== 'init')
      .forEach(listener => this._listeners.splice(this._listeners.indexOf(listener), 1))
  },

  // ---
  // Init Password Toggle
  initPasswordToggle() {
    const toggler = document.querySelectorAll('.form-password-toggle i')
    if (typeof toggler !== 'undefined' && toggler !== null) {
      toggler.forEach(el => {
        el.addEventListener('click', e => {
          e.preventDefault()
          const formPasswordToggle = el.closest('.form-password-toggle')
          const formPasswordToggleIcon = formPasswordToggle.querySelector('i')
          const formPasswordToggleInput = formPasswordToggle.querySelector('input')

          if (formPasswordToggleInput.getAttribute('type') === 'text') {
            formPasswordToggleInput.setAttribute('type', 'password')
            formPasswordToggleIcon.classList.replace('bx-show', 'bx-hide')
          } else if (formPasswordToggleInput.getAttribute('type') === 'password') {
            formPasswordToggleInput.setAttribute('type', 'text')
            formPasswordToggleIcon.classList.replace('bx-hide', 'bx-show')
          }
        })
      })
    }
  },

  //--
  // Init custom option check
  initCustomOptionCheck() {
    const _this = this

    const custopOptionList = [].slice.call(document.querySelectorAll('.custom-option .form-check-input'))
    custopOptionList.map(function (customOptionEL) {
      // Update custom options check on page load
      _this.updateCustomOptionCheck(customOptionEL)

      // Update custom options check on click
      customOptionEL.addEventListener('click', e => {
        _this.updateCustomOptionCheck(customOptionEL)
      })
    })
  },

  // ---
  // Init Speech To Text
  initSpeechToText() {
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition
    const speechToText = document.querySelectorAll('.speech-to-text')
    if (SpeechRecognition !== undefined && SpeechRecognition !== null) {
      if (typeof speechToText !== 'undefined' && speechToText !== null) {
        const recognition = new SpeechRecognition()
        const toggler = document.querySelectorAll('.speech-to-text i')
        toggler.forEach(el => {
          let listening = false
          el.addEventListener('click', () => {
            el.closest('.input-group').querySelector('.form-control').focus()
            recognition.onspeechstart = () => {
              listening = true
            }
            if (listening === false) {
              recognition.start()
            }
            recognition.onerror = () => {
              listening = false
            }
            recognition.onresult = event => {
              el.closest('.input-group').querySelector('.form-control').value = event.results[0][0].transcript
            }
            recognition.onspeechend = () => {
              listening = false
              recognition.stop()
            }
          })
        })
      }
    }
  },

  // ---
  // Init Navbar Dropdown (i.e notification) PerfectScrollbar
  initNavbarDropdownScrollbar() {
    const scrollbarContainer = document.querySelectorAll('.navbar-dropdown .scrollable-container')
    const { PerfectScrollbar } = window

    if (PerfectScrollbar !== undefined) {
      if (typeof scrollbarContainer !== 'undefined' && scrollbarContainer !== null) {
        scrollbarContainer.forEach(el => {
          // eslint-disable-next-line no-new
          new PerfectScrollbar(el, {
            wheelPropagation: false,
            suppressScrollX: true
          })
        })
      }
    }
  },

  // Ajax Call Promise
  ajaxCall(url) {
    return new Promise((resolve, reject) => {
      const req = new XMLHttpRequest()
      req.open('GET', url)
      req.onload = () => (req.status === 200 ? resolve(req.response) : reject(Error(req.statusText)))
      req.onerror = e => reject(Error(`Network Error: ${e}`))
      req.send()
    })
  },

  // ---
  // SidebarToggle (Used in Apps)
  initSidebarToggle() {
    const sidebarToggler = document.querySelectorAll('[data-bs-toggle="sidebar"]')

    sidebarToggler.forEach(el => {
      el.addEventListener('click', () => {
        const target = el.getAttribute('data-target')
        const overlay = el.getAttribute('data-overlay')
        const appOverlay = document.querySelectorAll('.app-overlay')
        const targetEl = document.querySelectorAll(target)

        targetEl.forEach(tel => {
          tel.classList.toggle('show')
          if (
            typeof overlay !== 'undefined' &&
            overlay !== null &&
            overlay !== false &&
            typeof appOverlay !== 'undefined'
          ) {
            if (tel.classList.contains('show')) {
              appOverlay[0].classList.add('show')
            } else {
              appOverlay[0].classList.remove('show')
            }
            appOverlay[0].addEventListener('click', e => {
              e.currentTarget.classList.remove('show')
              tel.classList.remove('show')
            })
          }
        })
      })
    })
  }
}

// *******************************************************************************
// * Initialization

if (typeof window !== 'undefined') {
  Helpers.init()

  if (Helpers.isMobileDevice() && window.chrome) {
    document.documentElement.classList.add('layout-menu-100vh')
  }

  // Update layout after page load
  if (document.readyState === 'complete') Helpers.update()
  else
    document.addEventListener('DOMContentLoaded', function onContentLoaded() {
      Helpers.update()
      document.removeEventListener('DOMContentLoaded', onContentLoaded)
    })
}

// ---
export { Helpers }
