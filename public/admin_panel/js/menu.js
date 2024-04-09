const TRANSITION_EVENTS = ['transitionend', 'webkitTransitionEnd', 'oTransitionEnd']
// const TRANSITION_PROPERTIES = ['transition', 'MozTransition', 'webkitTransition', 'WebkitTransition', 'OTransition']
const DELTA = 5

class Menu {
  constructor(el, config = {}, _PS = null) {
    this._el = el
    this._horizontal = config.orientation === 'horizontal'
    this._animate = config.animate !== false
    this._accordion = config.accordion !== false
    this._showDropdownOnHover = Boolean(config.showDropdownOnHover)
    this._closeChildren = Boolean(config.closeChildren)
    this._rtl = document.documentElement.getAttribute('dir') === 'rtl' || document.body.getAttribute('dir') === 'rtl'

    this._onOpen = config.onOpen || (() => {})
    this._onOpened = config.onOpened || (() => {})
    this._onClose = config.onClose || (() => {})
    this._onClosed = config.onClosed || (() => {})

    this._psScroll = null
    this._topParent = null
    this._menuBgClass = null

    el.classList.add('menu')
    el.classList[this._animate ? 'remove' : 'add']('menu-no-animation')

    if (!this._horizontal) {
      el.classList.add('menu-vertical')
      el.classList.remove('menu-horizontal')

      const PerfectScrollbarLib = _PS || window.PerfectScrollbar

      if (PerfectScrollbarLib) {
        this._scrollbar = new PerfectScrollbarLib(el.querySelector('.menu-inner'), {
          suppressScrollX: true,
          wheelPropagation: !Menu._hasClass('layout-menu-fixed layout-menu-fixed-offcanvas')
        })

        window.Helpers.menuPsScroll = this._scrollbar
      } else {
        el.querySelector('.menu-inner').classList.add('overflow-auto')
      }
    } else {
      el.classList.add('menu-horizontal')
      el.classList.remove('menu-vertical')

      this._inner = el.querySelector('.menu-inner')
      const container = this._inner.parentNode

      this._prevBtn = el.querySelector('.menu-horizontal-prev')
      if (!this._prevBtn) {
        this._prevBtn = document.createElement('a')
        this._prevBtn.href = '#'
        this._prevBtn.className = 'menu-horizontal-prev'
        container.appendChild(this._prevBtn)
      }

      this._wrapper = el.querySelector('.menu-horizontal-wrapper')
      if (!this._wrapper) {
        this._wrapper = document.createElement('div')
        this._wrapper.className = 'menu-horizontal-wrapper'
        this._wrapper.appendChild(this._inner)
        container.appendChild(this._wrapper)
      }

      this._nextBtn = el.querySelector('.menu-horizontal-next')
      if (!this._nextBtn) {
        this._nextBtn = document.createElement('a')
        this._nextBtn.href = '#'
        this._nextBtn.className = 'menu-horizontal-next'
        container.appendChild(this._nextBtn)
      }

      this._innerPosition = 0
      this.update()
    }

    // Add data attribute for bg color class of menu
    const menuClassList = el.classList

    for (let i = 0; i < menuClassList.length; i++) {
      if (menuClassList[i].startsWith('bg-')) {
        this._menuBgClass = menuClassList[i]
      }
    }
    el.setAttribute('data-bg-class', this._menuBgClass)

    // Switch to vertical menu on small screen for horizontal menu layout on page load
    if (this._horizontal && window.innerWidth < window.Helpers.LAYOUT_BREAKPOINT) this.switchMenu('vertical')

    this._bindEvents()

    // Link menu instance to element
    el.menuInstance = this
  }

  _bindEvents() {
    // Click Event
    this._evntElClick = e => {
      // Find top parent element
      if (e.target.closest('ul') && e.target.closest('ul').classList.contains('menu-inner')) {
        const menuItem = Menu._findParent(e.target, 'menu-item', false)

        // eslint-disable-next-line prefer-destructuring
        if (menuItem) this._topParent = menuItem.childNodes[0]
      }

      const toggleLink = e.target.classList.contains('menu-toggle')
        ? e.target
        : Menu._findParent(e.target, 'menu-toggle', false)

      if (toggleLink) {
        e.preventDefault()

        if (toggleLink.getAttribute('data-hover') !== 'true') {
          this.toggle(toggleLink)
        }
      }
    }
    if ((!this._showDropdownOnHover && this._horizontal) || !this._horizontal || window.Helpers.isMobileDevice)
      this._el.addEventListener('click', this._evntElClick)

    this._evntWindowResize = () => {
      this.update()
      if (this._lastWidth !== window.innerWidth) {
        this._lastWidth = window.innerWidth
        this.update()
      }

      const horizontalMenuTemplate = document.querySelector("[data-template^='horizontal-menu']")
      if (!this._horizontal && !horizontalMenuTemplate) this.manageScroll()
    }
    window.addEventListener('resize', this._evntWindowResize)

    if (this._horizontal) {
      this._evntPrevBtnClick = e => {
        e.preventDefault()
        if (this._prevBtn.classList.contains('disabled')) return
        this._slide('prev')
      }
      this._prevBtn.addEventListener('click', this._evntPrevBtnClick)

      this._evntNextBtnClick = e => {
        e.preventDefault()
        if (this._nextBtn.classList.contains('disabled')) return
        this._slide('next')
      }
      this._nextBtn.addEventListener('click', this._evntNextBtnClick)

      this._evntBodyClick = e => {
        if (!this._inner.contains(e.target) && this._el.querySelectorAll('.menu-inner > .menu-item.open').length)
          this.closeAll()
      }
      document.body.addEventListener('click', this._evntBodyClick)

      if (this._showDropdownOnHover) {
        /** ***********************************************
         * Horizontal Menu Mouse Over Event
         * ? e.target !== e.currentTarget condition to disable mouseover event on whole menu navbar
         * ? !e.target.parentNode.classList.contains('open') to disable mouseover events on icon, text and dropdown arrow
         */
        this._evntElMouseOver = e => {
          if (e.target !== e.currentTarget && !e.target.parentNode.classList.contains('open')) {
            const toggleLink = e.target.classList.contains('menu-toggle') ? e.target : null

            if (toggleLink) {
              e.preventDefault()

              if (toggleLink.getAttribute('data-hover') !== 'true') {
                this.toggle(toggleLink)
              }
            }
          }
          e.stopPropagation()
        }
        if (this._horizontal && window.screen.width > window.Helpers.LAYOUT_BREAKPOINT) {
          this._el.addEventListener('mouseover', this._evntElMouseOver)
        }

        /** ***********************************************
         * Horizontal Menu Mouse Out Event
         * ? e.target !== e.currentTarget condition to disable mouseout event on whole menu navbar
         * ? mouseOutEl.parentNode.classList.contains('open') to check if the mouseout element has open class or not
         * ? !mouseOutEl.classList.contains('menu-toggle') to check if mouseout was from single menu item and not from the one which has submenu
         * ? !mouseOverEl.parentNode.classList.contains('menu-link') to disable mouseout event for icon, text and dropdown arrow
         */
        this._evntElMouseOut = e => {
          const mainEl = e.currentTarget
          const mouseOutEl = e.target
          const mouseOverEl = e.toElement || e.relatedTarget

          // Find absolute parent of any menu item from which mouseout event triggered
          if (mouseOutEl.closest('ul') && mouseOutEl.closest('ul').classList.contains('menu-inner')) {
            this._topParent = mouseOutEl
          }

          if (
            mouseOutEl !== mainEl &&
            (mouseOutEl.parentNode.classList.contains('open') || !mouseOutEl.classList.contains('menu-toggle')) &&
            mouseOverEl &&
            mouseOverEl.parentNode &&
            !mouseOverEl.parentNode.classList.contains('menu-link')
          ) {
            // When mouse goes totally out of menu items, check mouse over element to confirm it's not the child of menu, once confirmed close the menu
            if (this._topParent && !Menu.childOf(mouseOverEl, this._topParent.parentNode)) {
              const toggleLink = this._topParent.classList.contains('menu-toggle') ? this._topParent : null

              if (toggleLink) {
                e.preventDefault()

                if (toggleLink.getAttribute('data-hover') !== 'true') {
                  this.toggle(toggleLink)
                  this._topParent = null
                }
              }
            }

            // When mouse enter the sub menu, check if it's child of the initially mouse overed menu item(Actual Parent),
            // if it's the parent do not close the sub menu else close the sub menu
            if (Menu.childOf(mouseOverEl, mouseOutEl.parentNode)) {
              return
            }
            const toggleLink = mouseOutEl.classList.contains('menu-toggle') ? mouseOutEl : null

            if (toggleLink) {
              e.preventDefault()

              if (toggleLink.getAttribute('data-hover') !== 'true') {
                this.toggle(toggleLink)
              }
            }
          }
          e.stopPropagation()
        }
        if (this._horizontal && window.screen.width > window.Helpers.LAYOUT_BREAKPOINT) {
          this._el.addEventListener('mouseout', this._evntElMouseOut)
        }
      }
    }
  }

  static childOf(/* child node */ c, /* parent node */ p) {
    // returns boolean
    if (c.parentNode) {
      while ((c = c.parentNode) && c !== p);
      return !!c
    }
    return false
  }

  _unbindEvents() {
    if (this._evntElClick) {
      this._el.removeEventListener('click', this._evntElClick)
      this._evntElClick = null
    }

    if (this._evntElMouseOver) {
      this._el.removeEventListener('mouseover', this._evntElMouseOver)
      this._evntElMouseOver = null
    }

    if (this._evntElMouseOut) {
      this._el.removeEventListener('mouseout', this._evntElMouseOut)
      this._evntElMouseOut = null
    }

    if (this._evntWindowResize) {
      window.removeEventListener('resize', this._evntWindowResize)
      this._evntWindowResize = null
    }

    if (this._evntBodyClick) {
      document.body.removeEventListener('click', this._evntBodyClick)
      this._evntBodyClick = null
    }

    if (this._evntInnerMousemove) {
      this._inner.removeEventListener('mousemove', this._evntInnerMousemove)
      this._evntInnerMousemove = null
    }

    if (this._evntInnerMouseleave) {
      this._inner.removeEventListener('mouseleave', this._evntInnerMouseleave)
      this._evntInnerMouseleave = null
    }
  }

  static _isRoot(item) {
    return !Menu._findParent(item, 'menu-item', false)
  }

  static _findParent(el, cls, throwError = true) {
    if (el.tagName.toUpperCase() === 'BODY') return null
    el = el.parentNode
    while (el.tagName.toUpperCase() !== 'BODY' && !el.classList.contains(cls)) {
      el = el.parentNode
    }

    el = el.tagName.toUpperCase() !== 'BODY' ? el : null

    if (!el && throwError) throw new Error(`Cannot find \`.${cls}\` parent element`)

    return el
  }

  static _findChild(el, cls) {
    const items = el.childNodes
    const found = []

    for (let i = 0, l = items.length; i < l; i++) {
      if (items[i].classList) {
        let passed = 0

        for (let j = 0; j < cls.length; j++) {
          if (items[i].classList.contains(cls[j])) passed += 1
        }

        if (cls.length === passed) found.push(items[i])
      }
    }

    return found
  }

  static _findMenu(item) {
    let curEl = item.childNodes[0]
    let menu = null

    while (curEl && !menu) {
      if (curEl.classList && curEl.classList.contains('menu-sub')) menu = curEl
      curEl = curEl.nextSibling
    }

    if (!menu) throw new Error('Cannot find `.menu-sub` element for the current `.menu-toggle`')

    return menu
  }

  // Has class
  static _hasClass(cls, el = window.Helpers.ROOT_EL) {
    let result = false

    cls.split(' ').forEach(c => {
      if (el.classList.contains(c)) result = true
    })

    return result
  }

  open(el, closeChildren = this._closeChildren) {
    const item = this._findUnopenedParent(Menu._getItem(el, true), closeChildren)

    if (!item) return

    const toggleLink = Menu._getLink(item, true)

    Menu._promisify(this._onOpen, this, item, toggleLink, Menu._findMenu(item))
      .then(() => {
        if (!this._horizontal || !Menu._isRoot(item)) {
          if (this._animate && !this._horizontal) {
            window.requestAnimationFrame(() => this._toggleAnimation(true, item, false))
            if (this._accordion) this._closeOther(item, closeChildren)
          } else if (this._animate) {
            this._toggleDropdown(true, item, closeChildren)
            // eslint-disable-next-line no-unused-expressions
            this._onOpened && this._onOpened(this, item, toggleLink, Menu._findMenu(item))
          } else {
            item.classList.add('open')
            // eslint-disable-next-line no-unused-expressions
            this._onOpened && this._onOpened(this, item, toggleLink, Menu._findMenu(item))
            if (this._accordion) this._closeOther(item, closeChildren)
          }
        } else {
          this._toggleDropdown(true, item, closeChildren)
          // eslint-disable-next-line no-unused-expressions
          this._onOpened && this._onOpened(this, item, toggleLink, Menu._findMenu(item))
        }
      })
      .catch(() => {})
  }

  close(el, closeChildren = this._closeChildren, _autoClose = false) {
    const item = Menu._getItem(el, true)
    const toggleLink = Menu._getLink(el, true)

    if (!item.classList.contains('open') || item.classList.contains('disabled')) return

    Menu._promisify(this._onClose, this, item, toggleLink, Menu._findMenu(item), _autoClose)
      .then(() => {
        if (!this._horizontal || !Menu._isRoot(item)) {
          if (this._animate && !this._horizontal) {
            window.requestAnimationFrame(() => this._toggleAnimation(false, item, closeChildren))
          } else {
            item.classList.remove('open')

            if (closeChildren) {
              const opened = item.querySelectorAll('.menu-item.open')
              for (let i = 0, l = opened.length; i < l; i++) opened[i].classList.remove('open')
            }

            // eslint-disable-next-line no-unused-expressions
            this._onClosed && this._onClosed(this, item, toggleLink, Menu._findMenu(item))
          }
        } else {
          this._toggleDropdown(false, item, closeChildren)
          // eslint-disable-next-line no-unused-expressions
          this._onClosed && this._onClosed(this, item, toggleLink, Menu._findMenu(item))
        }
      })
      .catch(() => {})
  }

  _closeOther(item, closeChildren) {
    const opened = Menu._findChild(item.parentNode, ['menu-item', 'open'])

    for (let i = 0, l = opened.length; i < l; i++) {
      if (opened[i] !== item) this.close(opened[i], closeChildren)
    }
  }

  toggle(el, closeChildren = this._closeChildren) {
    const item = Menu._getItem(el, true)
    // const toggleLink = Menu._getLink(el, true)

    if (item.classList.contains('open')) this.close(item, closeChildren)
    else this.open(item, closeChildren)
  }

  _toggleDropdown(show, item, closeChildren) {
    const menu = Menu._findMenu(item)
    const actualItem = item
    let subMenuItem = false

    if (show) {
      if (Menu._findParent(item, 'menu-sub', false)) {
        subMenuItem = true
        item = this._topParent ? this._topParent.parentNode : item
      }

      const wrapperWidth = Math.round(this._wrapper.getBoundingClientRect().width)
      const position = this._innerPosition
      const itemOffset = this._getItemOffset(item)
      const itemWidth = Math.round(item.getBoundingClientRect().width)

      if (itemOffset - DELTA <= -1 * position) {
        this._innerPosition = -1 * itemOffset
      } else if (itemOffset + position + itemWidth + DELTA >= wrapperWidth) {
        if (itemWidth > wrapperWidth) {
          this._innerPosition = -1 * itemOffset
        } else {
          this._innerPosition = -1 * (itemOffset + itemWidth - wrapperWidth)
        }
      }

      actualItem.classList.add('open')

      const menuWidth = Math.round(menu.getBoundingClientRect().width)

      if (subMenuItem) {
        if (
          itemOffset + this._innerPosition + menuWidth * 2 > wrapperWidth &&
          menuWidth < wrapperWidth &&
          menuWidth >= itemWidth
        ) {
          menu.style.left = [this._rtl ? '100%' : '-100%']
        }
      } else if (
        itemOffset + this._innerPosition + menuWidth > wrapperWidth &&
        menuWidth < wrapperWidth &&
        menuWidth > itemWidth
      ) {
        menu.style[this._rtl ? 'marginRight' : 'marginLeft'] = `-${menuWidth - itemWidth}px`
      }

      this._closeOther(actualItem, closeChildren)
      this._updateSlider()
    } else {
      const toggle = Menu._findChild(item, ['menu-toggle'])

      // eslint-disable-next-line no-unused-expressions
      toggle.length && toggle[0].removeAttribute('data-hover', 'true')
      item.classList.remove('open')
      menu.style[this._rtl ? 'marginRight' : 'marginLeft'] = null

      if (closeChildren) {
        const opened = menu.querySelectorAll('.menu-item.open')

        for (let i = 0, l = opened.length; i < l; i++) opened[i].classList.remove('open')
      }
    }
  }

  _slide(direction) {
    const wrapperWidth = Math.round(this._wrapper.getBoundingClientRect().width)
    const innerWidth = this._innerWidth
    let newPosition

    if (direction === 'next') {
      newPosition = this._getSlideNextPos()

      if (innerWidth + newPosition < wrapperWidth) {
        newPosition = wrapperWidth - innerWidth
      }
    } else {
      newPosition = this._getSlidePrevPos()

      if (newPosition > 0) newPosition = 0
    }

    this._innerPosition = newPosition
    this.update()
  }

  _getSlideNextPos() {
    const wrapperWidth = Math.round(this._wrapper.getBoundingClientRect().width)
    const position = this._innerPosition
    let curItem = this._inner.childNodes[0]
    let left = 0

    while (curItem) {
      if (curItem.tagName) {
        const curItemWidth = Math.round(curItem.getBoundingClientRect().width)

        if (left + position - DELTA <= wrapperWidth && left + position + curItemWidth + DELTA >= wrapperWidth) {
          if (curItemWidth > wrapperWidth && left === -1 * position) left += curItemWidth
          break
        }

        left += curItemWidth
      }

      curItem = curItem.nextSibling
    }

    return -1 * left
  }

  _getSlidePrevPos() {
    const wrapperWidth = Math.round(this._wrapper.getBoundingClientRect().width)
    const position = this._innerPosition
    let curItem = this._inner.childNodes[0]
    let left = 0

    while (curItem) {
      if (curItem.tagName) {
        const curItemWidth = Math.round(curItem.getBoundingClientRect().width)

        if (left - DELTA <= -1 * position && left + curItemWidth + DELTA >= -1 * position) {
          if (curItemWidth <= wrapperWidth) left = left + curItemWidth - wrapperWidth
          break
        }

        left += curItemWidth
      }

      curItem = curItem.nextSibling
    }

    return -1 * left
  }

  static _getItem(el, toggle) {
    let item = null
    const selector = toggle ? 'menu-toggle' : 'menu-link'

    if (el.classList.contains('menu-item')) {
      if (Menu._findChild(el, [selector]).length) item = el
    } else if (el.classList.contains(selector)) {
      item = el.parentNode.classList.contains('menu-item') ? el.parentNode : null
    }

    if (!item) {
      throw new Error(`${toggle ? 'Toggable ' : ''}\`.menu-item\` element not found.`)
    }

    return item
  }

  static _getLink(el, toggle) {
    let found = []
    const selector = toggle ? 'menu-toggle' : 'menu-link'

    if (el.classList.contains(selector)) found = [el]
    else if (el.classList.contains('menu-item')) found = Menu._findChild(el, [selector])

    if (!found.length) throw new Error(`\`${selector}\` element not found.`)

    return found[0]
  }

  _findUnopenedParent(item, closeChildren) {
    let tree = []
    let parentItem = null

    while (item) {
      if (item.classList.contains('disabled')) {
        parentItem = null
        tree = []
      } else {
        if (!item.classList.contains('open')) parentItem = item
        tree.push(item)
      }

      item = Menu._findParent(item, 'menu-item', false)
    }

    if (!parentItem) return null
    if (tree.length === 1) return parentItem

    tree = tree.slice(0, tree.indexOf(parentItem))

    for (let i = 0, l = tree.length; i < l; i++) {
      tree[i].classList.add('open')

      if (this._accordion) {
        const openedItems = Menu._findChild(tree[i].parentNode, ['menu-item', 'open'])

        for (let j = 0, k = openedItems.length; j < k; j++) {
          if (openedItems[j] !== tree[i]) {
            openedItems[j].classList.remove('open')

            if (closeChildren) {
              const openedChildren = openedItems[j].querySelectorAll('.menu-item.open')
              for (let x = 0, z = openedChildren.length; x < z; x++) {
                openedChildren[x].classList.remove('open')
              }
            }
          }
        }
      }
    }

    return parentItem
  }

  _toggleAnimation(open, item, closeChildren) {
    const toggleLink = Menu._getLink(item, true)
    const menu = Menu._findMenu(item)

    Menu._unbindAnimationEndEvent(item)

    const linkHeight = Math.round(toggleLink.getBoundingClientRect().height)

    item.style.overflow = 'hidden'

    const clearItemStyle = () => {
      item.classList.remove('menu-item-animating')
      item.classList.remove('menu-item-closing')
      item.style.overflow = null
      item.style.height = null

      if (!this._horizontal) this.update()
    }

    if (open) {
      item.style.height = `${linkHeight}px`
      item.classList.add('menu-item-animating')
      item.classList.add('open')

      Menu._bindAnimationEndEvent(item, () => {
        clearItemStyle()
        this._onOpened(this, item, toggleLink, menu)
      })

      setTimeout(() => {
        item.style.height = `${linkHeight + Math.round(menu.getBoundingClientRect().height)}px`
      }, 50)
    } else {
      item.style.height = `${linkHeight + Math.round(menu.getBoundingClientRect().height)}px`
      item.classList.add('menu-item-animating')
      item.classList.add('menu-item-closing')

      Menu._bindAnimationEndEvent(item, () => {
        item.classList.remove('open')
        clearItemStyle()

        if (closeChildren) {
          const opened = item.querySelectorAll('.menu-item.open')
          for (let i = 0, l = opened.length; i < l; i++) opened[i].classList.remove('open')
        }

        this._onClosed(this, item, toggleLink, menu)
      })

      setTimeout(() => {
        item.style.height = `${linkHeight}px`
      }, 50)
    }
  }

  static _bindAnimationEndEvent(el, handler) {
    const cb = e => {
      if (e.target !== el) return
      Menu._unbindAnimationEndEvent(el)
      handler(e)
    }

    let duration = window.getComputedStyle(el).transitionDuration
    duration = parseFloat(duration) * (duration.indexOf('ms') !== -1 ? 1 : 1000)

    el._menuAnimationEndEventCb = cb
    TRANSITION_EVENTS.forEach(ev => el.addEventListener(ev, el._menuAnimationEndEventCb, false))

    el._menuAnimationEndEventTimeout = setTimeout(() => {
      cb({ target: el })
    }, duration + 50)
  }

  _getItemOffset(item) {
    let curItem = this._inner.childNodes[0]
    let left = 0

    while (curItem !== item) {
      if (curItem.tagName) {
        left += Math.round(curItem.getBoundingClientRect().width)
      }

      curItem = curItem.nextSibling
    }

    return left
  }

  _updateSlider(wrapperWidth = null, innerWidth = null, position = null) {
    const _wrapperWidth = wrapperWidth !== null ? wrapperWidth : Math.round(this._wrapper.getBoundingClientRect().width)
    const _innerWidth = innerWidth !== null ? innerWidth : this._innerWidth
    const _position = position !== null ? position : this._innerPosition

    if (_innerWidth < _wrapperWidth || window.innerWidth < window.Helpers.LAYOUT_BREAKPOINT) {
      this._prevBtn.classList.add('d-none')
      this._nextBtn.classList.add('d-none')
    } else {
      this._prevBtn.classList.remove('d-none')
      this._nextBtn.classList.remove('d-none')
    }
    if (_innerWidth > _wrapperWidth && window.innerWidth > window.Helpers.LAYOUT_BREAKPOINT) {
      if (_position === 0) this._prevBtn.classList.add('disabled')
      else this._prevBtn.classList.remove('disabled')

      if (_innerWidth + _position <= _wrapperWidth) this._nextBtn.classList.add('disabled')
      else this._nextBtn.classList.remove('disabled')
    }
  }

  static _promisify(fn, ...args) {
    const result = fn(...args)
    if (result instanceof Promise) {
      return result
    }
    if (result === false) {
      return Promise.reject()
    }
    return Promise.resolve()
  }

  get _innerWidth() {
    const items = this._inner.childNodes
    let width = 0

    for (let i = 0, l = items.length; i < l; i++) {
      if (items[i].tagName) {
        width += Math.round(items[i].getBoundingClientRect().width)
      }
    }

    return width
  }

  get _innerPosition() {
    return parseInt(this._inner.style[this._rtl ? 'marginRight' : 'marginLeft'] || '0px', 10)
  }

  set _innerPosition(value) {
    this._inner.style[this._rtl ? 'marginRight' : 'marginLeft'] = `${value}px`
    return value
  }

  static _unbindAnimationEndEvent(el) {
    const cb = el._menuAnimationEndEventCb

    if (el._menuAnimationEndEventTimeout) {
      clearTimeout(el._menuAnimationEndEventTimeout)
      el._menuAnimationEndEventTimeout = null
    }

    if (!cb) return

    TRANSITION_EVENTS.forEach(ev => el.removeEventListener(ev, cb, false))
    el._menuAnimationEndEventCb = null
  }

  closeAll(closeChildren = this._closeChildren) {
    const opened = this._el.querySelectorAll('.menu-inner > .menu-item.open')

    for (let i = 0, l = opened.length; i < l; i++) this.close(opened[i], closeChildren)
  }

  static setDisabled(el, disabled) {
    Menu._getItem(el, false).classList[disabled ? 'add' : 'remove']('disabled')
  }

  static isActive(el) {
    return Menu._getItem(el, false).classList.contains('active')
  }

  static isOpened(el) {
    return Menu._getItem(el, false).classList.contains('open')
  }

  static isDisabled(el) {
    return Menu._getItem(el, false).classList.contains('disabled')
  }

  update() {
    if (!this._horizontal) {
      if (this._scrollbar) {
        this._scrollbar.update()
      }
    } else {
      this.closeAll()

      const wrapperWidth = Math.round(this._wrapper.getBoundingClientRect().width)
      const innerWidth = this._innerWidth
      let position = this._innerPosition

      if (wrapperWidth - position > innerWidth) {
        position = wrapperWidth - innerWidth
        if (position > 0) position = 0
        this._innerPosition = position
      }

      this._updateSlider(wrapperWidth, innerWidth, position)
    }
  }

  manageScroll() {
    const { PerfectScrollbar } = window
    const menuInner = document.querySelector('.menu-inner')

    if (window.innerWidth < window.Helpers.LAYOUT_BREAKPOINT) {
      if (this._scrollbar !== null) {
        // window.Helpers.menuPsScroll.destroy()
        this._scrollbar.destroy()
        this._scrollbar = null
      }
      menuInner.classList.add('overflow-auto')
    } else {
      if (this._scrollbar === null) {
        const menuScroll = new PerfectScrollbar(document.querySelector('.menu-inner'), {
          suppressScrollX: true,
          wheelPropagation: false
        })
        // window.Helpers.menuPsScroll = menuScroll
        this._scrollbar = menuScroll
      }
      menuInner.classList.remove('overflow-auto')
    }
  }

  switchMenu(menu) {
    // Unbind Events
    this._unbindEvents()

    // const html = document.documentElement
    const navbar = document.querySelector('nav.layout-navbar')
    const navbarCollapse = document.querySelector('#navbar-collapse')
    /* const fullNavbar = document.querySelector('.layout-navbar-full')
    const contentNavbar = document.querySelector('.layout-content-navbar')
    const contentWrapper = document.querySelector('.content-wrapper') */
    const asideMenuWrapper = document.querySelector('#layout-menu div')
    const asideMenu = document.querySelector('#layout-menu')
    const horzMenuClasses = ['layout-menu-horizontal', 'menu', 'menu-horizontal', 'container-fluid', 'flex-grow-0']
    const vertMenuClasses = ['layout-menu', 'menu', 'menu-vertical']
    const horzMenuWrapper = document.querySelector('.menu-horizontal-wrapper')
    const menuInner = document.querySelector('.menu-inner')
    const brand = document.querySelector('.app-brand')
    const menuToggler = document.querySelector('.layout-menu-toggle')
    const activeMenuItems = document.querySelectorAll('.menu-inner .active')
    /* const layoutPage = document.querySelector('.layout-page')
    const layoutContainer = document.querySelector('.layout-container')
    const content = document.querySelector('.container-fluid') */

    // const { PerfectScrollbar } = window

    if (menu === 'vertical') {
      this._horizontal = false
      asideMenuWrapper.insertBefore(brand, horzMenuWrapper)
      asideMenuWrapper.insertBefore(menuInner, horzMenuWrapper)
      asideMenuWrapper.classList.add('flex-column', 'p-0')
      asideMenu.classList.remove(...asideMenu.classList)
      asideMenu.classList.add(...vertMenuClasses, this._menuBgClass)
      brand.classList.remove('d-none', 'd-lg-flex')
      menuToggler.classList.remove('d-none')
      // if (PerfectScrollbar !== undefined) {
      //   this._psScroll = new PerfectScrollbar(document.querySelector('.menu-inner'), {
      //     suppressScrollX: true,
      //     wheelPropagation: !Menu._hasClass('layout-menu-fixed layout-menu-fixed-offcanvas')
      //   })
      // }

      menuInner.classList.add('overflow-auto')

      // Add open class to active items
      for (let i = 0; i < activeMenuItems.length - 1; ++i) {
        activeMenuItems[i].classList.add('open')
      }
    } else {
      this._horizontal = true
      navbar.children[0].insertBefore(brand, navbarCollapse)
      brand.classList.add('d-none', 'd-lg-flex')
      horzMenuWrapper.appendChild(menuInner)
      asideMenuWrapper.classList.remove('flex-column', 'p-0')
      asideMenu.classList.remove(...asideMenu.classList)
      asideMenu.classList.add(...horzMenuClasses, this._menuBgClass)
      menuToggler.classList.add('d-none')
      menuInner.classList.remove('overflow-auto')

      // if (PerfectScrollbar !== undefined && this._psScroll !== null) {
      //   this._psScroll.destroy()
      //   this._psScroll = null
      // }

      // Remove open class from active items
      for (let i = 0; i < activeMenuItems.length; ++i) {
        activeMenuItems[i].classList.remove('open')
      }
    }

    this._bindEvents()
  }

  destroy() {
    if (!this._el) return

    this._unbindEvents()

    const items = this._el.querySelectorAll('.menu-item')
    for (let i = 0, l = items.length; i < l; i++) {
      Menu._unbindAnimationEndEvent(items[i])
      items[i].classList.remove('menu-item-animating')
      items[i].classList.remove('open')
      items[i].style.overflow = null
      items[i].style.height = null
    }

    const menus = this._el.querySelectorAll('.menu-menu')
    for (let i2 = 0, l2 = menus.length; i2 < l2; i2++) {
      menus[i2].style.marginRight = null
      menus[i2].style.marginLeft = null
    }

    this._el.classList.remove('menu-no-animation')

    if (this._wrapper) {
      this._prevBtn.parentNode.removeChild(this._prevBtn)
      this._nextBtn.parentNode.removeChild(this._nextBtn)
      this._wrapper.parentNode.insertBefore(this._inner, this._wrapper)
      this._wrapper.parentNode.removeChild(this._wrapper)
      this._inner.style.marginLeft = null
      this._inner.style.marginRight = null
    }

    this._el.menuInstance = null
    delete this._el.menuInstance

    this._el = null
    this._horizontal = null
    this._animate = null
    this._accordion = null
    this._showDropdownOnHover = null
    this._closeChildren = null
    this._rtl = null
    this._onOpen = null
    this._onOpened = null
    this._onClose = null
    this._onClosed = null
    if (this._scrollbar) {
      this._scrollbar.destroy()
      this._scrollbar = null
    }
    this._inner = null
    this._prevBtn = null
    this._wrapper = null
    this._nextBtn = null
  }
}

export { Menu }
