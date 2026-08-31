'use strict'

let Container = require('./container')
let { my } = require('./symbols')

class Warning {
  constructor(text, opts = {}) {
    this.type = 'warning'
    this.text = text

    if (opts.node && opts.node.source) {
      if (!opts.node[my]) {
        // The node comes from another PostCSS copy in node_modules, so it does
        // not have this copy’s methods. Container#normalize() rebuilds such
        // nodes on insert, but a node passed straight to Result#warn() never
        // goes through it.
        Container.rebuild(opts.node)
      }
      let range = opts.node.rangeBy(opts)
      this.line = range.start.line
      this.column = range.start.column
      this.endLine = range.end.line
      this.endColumn = range.end.column
    }

    for (let opt in opts) this[opt] = opts[opt]
  }

  toString() {
    if (this.node) {
      return this.node.error(this.text, {
        index: this.index,
        plugin: this.plugin,
        word: this.word
      }).message
    }

    if (this.plugin) {
      return this.plugin + ': ' + this.text
    }

    return this.text
  }
}

module.exports = Warning
Warning.default = Warning
