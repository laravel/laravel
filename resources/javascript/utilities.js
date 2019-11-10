export function selectorElement (selector) {
  let element

  switch (true) {
    case(typeof selector === 'string'):
      element = document.querySelector(selector)
      break
    case(typeof selector === 'object' && selector.nodeType === Node.ELEMENT_NODE):
      element = selector
      break
    default:
      throw new Error('selectorElement: Invalid selector given')
  }

  return element
}

export function append (wrapper, html) {
  const parent = selectorElement(wrapper)

  parent.innerHTML = ''

  let template = document.createElement('template')

  template.innerHTML = html.trim()
  template = template.content

  parent.appendChild(template)
}

export function domEvent (selector, type, callback) {
  const element = selectorElement(selector)

  element.addEventListener(type, event => {
    callback(element, event)
  })
}
