'use strict'

let AtRule = require('./at-rule')
let Comment = require('./comment')
let Declaration = require('./declaration')
let Input = require('./input')
let PreviousMap = require('./previous-map')
let Root = require('./root')
let Rule = require('./rule')

function hydrateInputs(json, inputs) {
  if (!json.inputs) return inputs
  return json.inputs.map(input => {
    let inputHydrated = { ...input, __proto__: Input.prototype }
    if (inputHydrated.map) {
      inputHydrated.map = {
        ...inputHydrated.map,
        __proto__: PreviousMap.prototype
      }
    }
    return inputHydrated
  })
}

function constructNode(json, inputs, children) {
  let defaults = { ...json }
  delete defaults.inputs
  delete defaults.nodes
  if (defaults.source) {
    let { inputId, ...source } = defaults.source
    defaults.source = source
    if (inputId != null) {
      defaults.source.input = inputs[inputId]
    }
  }

  let node
  if (defaults.type === 'root') {
    node = new Root(defaults)
  } else if (defaults.type === 'decl') {
    node = new Declaration(defaults)
  } else if (defaults.type === 'rule') {
    node = new Rule(defaults)
  } else if (defaults.type === 'comment') {
    node = new Comment(defaults)
  } else if (defaults.type === 'atrule') {
    node = new AtRule(defaults)
  } else {
    throw new Error('Unknown node type: ' + json.type)
  }

  // Rehydrated children are attached after construction. Passing them
  // through the container constructor would re-run insertion spacing
  // normalization and overwrite each child's own `raws.before`.
  if (children) {
    node.nodes = children
    for (let child of children) child.parent = node
  }

  return node
}

function fromJSON(json, inputs) {
  if (Array.isArray(json)) return json.map(n => fromJSON(n))

  // An explicit stack instead of recursive calls to survive deeply
  // nested trees. Children are rehydrated before their parent node
  // is constructed.
  let result
  let stack = [
    { childIndex: 0, children: [], inputs: hydrateInputs(json, inputs), json }
  ]

  while (stack.length > 0) {
    let frame = stack[stack.length - 1]
    let jsonNodes = frame.json.nodes

    if (jsonNodes && frame.childIndex < jsonNodes.length) {
      let childJson = jsonNodes[frame.childIndex]
      frame.childIndex += 1
      stack.push({
        childIndex: 0,
        children: [],
        inputs: hydrateInputs(childJson, frame.inputs),
        json: childJson
      })
      continue
    }

    stack.pop()
    let node = constructNode(
      frame.json,
      frame.inputs,
      jsonNodes ? frame.children : undefined
    )
    if (stack.length > 0) {
      stack[stack.length - 1].children.push(node)
    } else {
      result = node
    }
  }

  return result
}

module.exports = fromJSON
fromJSON.default = fromJSON
