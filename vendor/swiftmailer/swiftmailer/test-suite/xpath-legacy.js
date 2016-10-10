/*  JavaScript-XPath 0.1.5
 *  (c) 2007 Cybozu Labs, Inc.
 *
 *  JavaScript-XPath is freely distributable under the terms of an MIT-style license.
 *  For details, see the JavaScript-XPath web site: http://coderepos.org/share/wiki/JavaScript-XPath
 *
/*--------------------------------------------------------------------------*/

if (!document.implementation
                || !document.implementation.hasFeature
                || !document.implementation.hasFeature("XPath", null)) (function() {

var undefined = void(0);


var defaultConfig = {
    targetFrame: undefined
};

var config;

if (window.jsxpath) {
    config = window.jsxpath;
}
else {
    var scriptElms = document.getElementsByTagName('script');
    var scriptElm = scriptElms[scriptElms.length - 1];
    var scriptSrc = scriptElm.src;
    config = {};
    var scriptSrcMatchResult = scriptSrc.match(/\?(.*)$/);
    if (scriptSrcMatchResult) {
        var configStrings = scriptSrcMatchResult[1].split('&');
        for (var i = 0, l = configStrings.length; i < l; i ++) {
            var configString = configStrings[i];
            var configStringSplited = configString.split('=');
            config[configStringSplited[0]] = configStringSplited[1] || true;
        }
    }
}

for (var n in defaultConfig) {
    if (!(n in config)) config[n] = defaultConfig[n]
}


var BinaryExpr;
var FilterExpr;
var FunctionCall;
var Literal;
var NameTest;
var NodeSet;
var NodeType;
var NodeUtil;
var Number;
var PathExpr;
var Step;
var UnaryExpr;
var UnionExpr;
var VariableReference;

/*
 * object: user agent identifier
 */
var uai = new function() {

    var ua = navigator.userAgent;

    if (RegExp == undefined) {
        if (ua.indexOf("Opera") >= 0) { 
            this.opera = true; 
        } else if (ua.indexOf("Netscape") >= 0) { 
            this.netscape = true; 
        } else if (ua.indexOf("Mozilla/") == 0) { 
            this.mozilla = true; 
        } else {
            this.unknown = slide 
        }
        
        if (ua.indexOf("Gecko/") >= 0) { 
            this.gecko = true; 
        }
        
        if (ua.indexOf("Win") >= 0) { 
            this.windows = true; 
        } else if (ua.indexOf("Mac") >= 0) { 
            this.mac = true; 
        } else if (ua.indexOf("Linux") >= 0) { 
            this.linux = true; 
        } else if (ua.indexOf("BSD") >= 0) { 
            this.bsd = true; 
        } else if (ua.indexOf("SunOS") >= 0) { 
            this.sunos = true; 
        }
    }
    else {  
    
        /* for Trident/Tasman */
        /*@cc_on
        @if (@_jscript)
            function jscriptVersion() {
                switch (@_jscript_version) {
                    case 3.0:  return "4.0";
                    case 5.0:  return "5.0";
                    case 5.1:  return "5.01";
                    case 5.5:  return "5.5";
                    case 5.6:
                        if ("XMLHttpRequest" in window) return "7.0";
                        return "6.0";
                    case 5.7:
                        return "7.0";
                    default:   return true;
                }
            }
            if (@_win16 || @_win32 || @_win64) {
                this.windows = true;
                this.trident = jscriptVersion();
            } else if (@_mac || navigator.platform.indexOf("Mac") >= 0) {
                // '@_mac' may be 'NaN' even if the platform is Mac,
                // so we check 'navigator.platform', too.
                this.mac = true;
                this.tasman = jscriptVersion();
            }
            if (match = ua.match("MSIE ?(\\d+\\.\\d+)b?;")) {
                this.ie = match[1];
                this['ie' + match[1].charAt(0)] = true;
            }
        @else @*/

        /* for AppleWebKit */
        if (match = ua.match("AppleWebKit/(\\d+(\\.\\d+)*)")) {
            this.applewebkit = match[1];
            this['applewebkit' + match[1].charAt(0)] = true;
        }

        /* for Gecko */
        else if (typeof(Components) == "object") {
            if (match = ua.match("Gecko/(\\d{8})")) {
                this.gecko = match[1];
            } else if (navigator.product == "Gecko"
                    && (match = navigator.productSub.match("^(\\d{8})$"))) {
                this.gecko = match[1];
            }
        }

        /*@end @*/

        if (typeof(opera) == "object" && typeof(opera.version) == "function") {
            this.opera = opera.version(); 
            this['opera' + this.opera[0] + this.opera[2]] = true;
            
        } else if (typeof(opera) == "object"
                && (match = ua.match("Opera[/ ](\\d+\\.\\d+)"))) {
            this.opera = match[1];
        } else if (this.ie) {
        } else if (match = ua.match("Safari/(\\d+(\\.\\d+)*)")) {
            this.safari = match[1];
        } else if (match = ua.match("Konqueror/(\\d+(\\.\\d+)*)")) {
            this.konqueror = match[1];
        } else if (ua.indexOf("(compatible;") < 0
                && (match = ua.match("^Mozilla/(\\d+\\.\\d+)"))) {
            this.mozilla = match[1];
            if (match = ua.match("\\([^(]*rv:(\\d+(\\.\\d+)*).*?\\)"))
                this.mozillarv = match[1];
            if (match = ua.match("Firefox/(\\d+(\\.\\d+)*)")) {
                this.firefox = match[1];
            } else if (match = ua.match("Netscape\\d?/(\\d+(\\.\\d+)*)")) {
                this.netscape = match[1];
            }
        } else {
            this.unknown = true;
        }

        if (ua.indexOf("Win 9x 4.90") >= 0) {
            this.windows = "ME";
        } else if (match = ua.match("Win(dows)? ?(NT ?(\\d+\\.\\d+)?|\\d+|XP|ME|Vista)")) {
            this.windows = match[2];
            if (match[3]) {
                this.winnt = match[3];
            } else switch (match[2]) {
                case "2000":   this.winnt = "5.0";  break;
                case "XP":     this.winnt = "5.1";  break;
                case "Vista":  this.winnt = "6.0";  break;
            }
        } else if (ua.indexOf("Mac") >= 0) {
            this.mac = true;
        } else if (ua.indexOf("Linux") >= 0) {
            this.linux = true;
        } else if (match = ua.match("\\w*BSD")) {
            this.bsd = match[0];
        } else if (ua.indexOf("SunOS") >= 0) {
            this.sunos = true;
        }
    }
};


/**
 * pseudo class: Lexer
 */
var Lexer = function(source) {
    var proto = Lexer.prototype;
    var tokens = source.match(proto.regs.token);
    for (var i = 0, l = tokens.length; i < l; i ++) {
        if (proto.regs.strip.test(tokens[i])) {
            tokens.splice(i, 1);
        }
    }
    for (var n in proto) tokens[n] = proto[n];
    tokens.index = 0;
    return tokens;
};

Lexer.prototype.regs = {
    token: /\$?(?:(?![0-9-])[\w-]+:)?(?![0-9-])[\w-]+|\/\/|\.\.|::|\d+(?:\.\d*)?|\.\d+|"[^"]*"|'[^']*'|[!<>]=|(?![0-9-])[\w-]+:\*|\s+|./g,
    strip: /^\s/
};

Lexer.prototype.peek = function(i) {
    return this[this.index + (i||0)];
};
Lexer.prototype.next = function() {
    return this[this.index++];
};
Lexer.prototype.back = function() {
    this.index--;
};
Lexer.prototype.empty = function() {
    return this.length <= this.index;
};


/**
 * class: Ctx
 */
var Ctx = function(node, position, last) {
    this.node = node;
    this.position = position || 1;
    this.last = last || 1;
};


/**
 * abstract class: BaseExpr
 */
var BaseExpr = function() {};

BaseExpr.prototype.number = function(ctx) {
    var exrs = this.evaluate(ctx);
    if (exrs.isNodeSet) return exrs.number();
    return + exrs;
};

BaseExpr.prototype.string = function(ctx) {
    var exrs = this.evaluate(ctx);
    if (exrs.isNodeSet) return exrs.string();
    return '' + exrs;
};

BaseExpr.prototype.bool = function(ctx) {
    var exrs = this.evaluate(ctx);
    if (exrs.isNodeSet) return exrs.bool();
    return !! exrs;
};


/**
 * abstract class: BaseExprHasPredicates
 */
var BaseExprHasPredicates = function() {};

BaseExprHasPredicates.parsePredicates = function(lexer, expr) {
    while (lexer.peek() == '[') {
        lexer.next();
        if (lexer.empty()) {
            throw Error('missing predicate expr');
        }
        var predicate = BinaryExpr.parse(lexer);
        expr.predicate(predicate);
        if (lexer.empty()) {
            throw Error('unclosed predicate expr');
        }
        if (lexer.next() != ']') {
            lexer.back();
            throw Error('bad token: ' + lexer.next());
        }
    }
};

BaseExprHasPredicates.prototyps = new BaseExpr();

BaseExprHasPredicates.prototype.evaluatePredicates = function(nodeset, start) {
    var predicates, predicate, nodes, node, nodeset, position, reverse;

    reverse = this.reverse;
    predicates = this.predicates;

    nodeset.sort();

    for (var i = start || 0, l0 = predicates.length; i < l0; i ++) {
        predicate = predicates[i];

        var deleteIndexes = [];
        var nodes = nodeset.list();

        for (var j = 0, l1 = nodes.length; j < l1; j ++) {

            position = reverse ? (l1 - j) : (j + 1);
            exrs = predicate.evaluate(new Ctx(nodes[j], position, l1));

            switch (typeof exrs) {
                case 'number':
                    exrs = (position == exrs);
                    break;
                case 'string':
                    exrs = !!exrs;
                    break;
                case 'object':
                    exrs = exrs.bool();
                    break;
            }

            if (!exrs) {
                deleteIndexes.push(j);
            }
        }

        for (var j = deleteIndexes.length - 1, l1 = 0; j >= l1; j --) {
            nodeset.del(deleteIndexes[j]);
        }

    }

    return nodeset;
};


/**
 * class: BinaryExpr
 */
if (!window.BinaryExpr && window.defaultConfig)
    window.BinaryExpr = null;

BinaryExpr = function(op, left, right, datatype) {
    this.op = op;
    this.left = left;
    this.right = right;

    this.datatype = BinaryExpr.ops[op][2];

    this.needContextPosition = left.needContextPosition || right.needContextPosition;
    this.needContextNode = left.needContextNode || right.needContextNode;

    // Optimize [@id="foo"] and [@name="bar"]
    if (this.op == '=') {
        if (!right.needContextNode && !right.needContextPosition && 
            right.datatype != 'nodeset' && right.datatype != 'void' && left.quickAttr) {
            this.quickAttr = true;
            this.attrName = left.attrName;
            this.attrValueExpr = right;
        }
        else if (!left.needContextNode && !left.needContextPosition && 
            left.datatype != 'nodeset' && left.datatype != 'void' && right.quickAttr) {
            this.quickAttr = true;
            this.attrName = right.attrName;
            this.attrValueExpr = left;
        }
    }
};

BinaryExpr.compare = function(op, comp, left, right, ctx) {
    var type, lnodes, rnodes, nodes, nodeset, primitive;

    left = left.evaluate(ctx);
    right = right.evaluate(ctx);

    if (left.isNodeSet && right.isNodeSet) {
        lnodes = left.list();
        rnodes = right.list();
        for (var i = 0, l0 = lnodes.length; i < l0; i ++)
            for (var j = 0, l1 = rnodes.length; j < l1; j ++)
                if (comp(NodeUtil.to('string', lnodes[i]), NodeUtil.to('string', rnodes[j])))
                    return true;
        return false;
    }

    if (left.isNodeSet || right.isNodeSet) {
        if (left.isNodeSet)
            nodeset = left, primitive = right;
        else
            nodeset = right, primitive = left;

        nodes = nodeset.list();
        type = typeof primitive;
        for (var i = 0, l = nodes.length; i < l; i ++) {
            if (comp(NodeUtil.to(type, nodes[i]), primitive))
                return true;
        }
        return false;
    }

    if (op == '=' || op == '!=') {
        if (typeof left == 'boolean' || typeof right == 'boolean') {
            return comp(!!left, !!right);
        }
        if (typeof left == 'number' || typeof right == 'number') {
            return comp(+left, +right);
        }
        return comp(left, right);
    }

    return comp(+left, +right);
};


BinaryExpr.ops = {
    'div': [6, function(left, right, ctx) {
        return left.number(ctx) / right.number(ctx);
    }, 'number'],
    'mod': [6, function(left, right, ctx) {
        return left.number(ctx) % right.number(ctx);
    }, 'number'],
    '*': [6, function(left, right, ctx) {
        return left.number(ctx) * right.number(ctx);
    }, 'number'],
    '+': [5, function(left, right, ctx) {
        return left.number(ctx) + right.number(ctx);
    }, 'number'],
    '-': [5, function(left, right, ctx) {
        return left.number(ctx) - right.number(ctx);
    }, 'number'],
    '<': [4, function(left, right, ctx) {
        return BinaryExpr.compare('<',
                    function(a, b) { return a < b }, left, right, ctx);
    }, 'boolean'],
    '>': [4, function(left, right, ctx) {
        return BinaryExpr.compare('>',
                    function(a, b) { return a > b }, left, right, ctx);
    }, 'boolean'],
    '<=': [4, function(left, right, ctx) {
        return BinaryExpr.compare('<=',
                    function(a, b) { return a <= b }, left, right, ctx);
    }, 'boolean'],
    '>=': [4, function(left, right, ctx) {
        return BinaryExpr.compare('>=',
                    function(a, b) { return a >= b }, left, right, ctx);
    }, 'boolean'],
    '=': [3, function(left, right, ctx) {
        return BinaryExpr.compare('=',
                    function(a, b) { return a == b }, left, right, ctx);
    }, 'boolean'],
    '!=': [3, function(left, right, ctx) {
        return BinaryExpr.compare('!=',
                    function(a, b) { return a != b }, left, right, ctx);
    }, 'boolean'],
    'and': [2, function(left, right, ctx) {
        return left.bool(ctx) && right.bool(ctx);
    }, 'boolean'],
    'or': [1, function(left, right, ctx) {
        return left.bool(ctx) || right.bool(ctx);
    }, 'boolean']
};


BinaryExpr.parse = function(lexer) {
    var op, precedence, info, expr, stack = [], index = lexer.index;

    while (true) {

        if (lexer.empty()) {
            throw Error('missing right expression');
        }
        expr = UnaryExpr.parse(lexer);

        op = lexer.next();
        if (!op) {
            break;
        }

        info = this.ops[op];
        precedence = info && info[0];
        if (!precedence) {
            lexer.back();
            break;
        }

        while (stack.length && precedence <= this.ops[stack[stack.length-1]][0]) {
            expr = new BinaryExpr(stack.pop(), stack.pop(), expr);
        }

        stack.push(expr, op);
    }

    while (stack.length) {
        expr = new BinaryExpr(stack.pop(), stack.pop(), expr);
    }

    return expr;
};

BinaryExpr.prototype = new BaseExpr();

BinaryExpr.prototype.evaluate = function(ctx) {
    return BinaryExpr.ops[this.op][1](this.left, this.right, ctx);
};

BinaryExpr.prototype.show = function(indent) {
    indent = indent || '';
    var t = '';
    t += indent + 'binary: ' + this.op + '\n';
    indent += '    ';
    t += this.left.show(indent);
    t += this.right.show(indent);
    return t;
};


/**
 * class: UnaryExpr
 */
if (!window.UnaryExpr && window.defaultConfig)
    window.UnaryExpr = null;

UnaryExpr = function(op, expr) {
    this.op = op;
    this.expr = expr;

    this.needContextPosition = expr.needContextPosition;
    this.needContextNode = expr.needContextNode;
};

UnaryExpr.ops = { '-': 1 };

UnaryExpr.parse = function(lexer) {
    var token;
    if (this.ops[lexer.peek()])
        return new UnaryExpr(lexer.next(), UnaryExpr.parse(lexer));
    else
        return UnionExpr.parse(lexer);
};

UnaryExpr.prototype = new BaseExpr();

UnaryExpr.prototype.datatype = 'number';

UnaryExpr.prototype.evaluate = function(ctx) {
    return - this.expr.number(ctx);
};

UnaryExpr.prototype.show = function(indent) {
    indent = indent || '';
    var t = '';
    t += indent + 'unary: ' + this.op + '\n';
    indent += '    ';
    t += this.expr.show(indent);
    return t;
};


/**
 * class: UnionExpr
 */
if (!window.UnionExpr && window.defaultConfig)
    window.UnionExpr = null;

UnionExpr = function() {
    this.paths = [];
};

UnionExpr.ops = { '|': 1 };


UnionExpr.parse = function(lexer) {
    var union, expr;

    expr = PathExpr.parse(lexer);
    if (!this.ops[lexer.peek()])
        return expr;

    union = new UnionExpr();
    union.path(expr);

    while (true) {
        if (!this.ops[lexer.next()]) break;
        if (lexer.empty()) {
            throw Error('missing next union location path');
        }
        union.path(PathExpr.parse(lexer));
    }



    lexer.back();
    return union;
};

UnionExpr.prototype = new BaseExpr();

UnionExpr.prototype.datatype = 'nodeset';

UnionExpr.prototype.evaluate = function(ctx) {
    var paths = this.paths;
    var nodeset = new NodeSet();
    for (var i = 0, l = paths.length; i < l; i ++) {
        var exrs = paths[i].evaluate(ctx);
        if (!exrs.isNodeSet) throw Error('PathExpr must be nodeset');
        nodeset.merge(exrs);
    }
    return nodeset;
};

UnionExpr.prototype.path = function(path) {
    this.paths.push(path);

    if (path.needContextPosition) {
        this.needContextPosition = true;
    }
    if (path.needContextNode) {
        this.needContextNode = true;
    }
}
UnionExpr.prototype.show = function(indent) {
    indent = indent || '';
    var t = '';
    t += indent + 'union:' + '\n';
    indent += '    ';
    for (var i = 0; i < this.paths.length; i ++) {
        t += this.paths[i].show(indent);
    }
    return t;
};


/**
 * class: PathExpr
 */
if (!window.PathExpr && window.defaultConfig)
    window.PathExpr = null;

PathExpr = function(filter) {
    this.filter = filter;
    this.steps = [];

    this.datatype = filter.datatype;

    this.needContextPosition = filter.needContextPosition;
    this.needContextNode = filter.needContextNode;
};

PathExpr.ops = { '//': 1, '/': 1 };

PathExpr.parse = function(lexer) {
    var op, expr, path, token;

    if (this.ops[lexer.peek()]) {
        op = lexer.next();
        token = lexer.peek();

        if (op == '/' && (lexer.empty() || 
                (token != '.' && token != '..' && token != '@' && token != '*' && 
                !token.match(/(?![0-9])[\w]/)))) { 
            return FilterExpr.root();
        }

        path = new PathExpr(FilterExpr.root()); // RootExpr

        if (lexer.empty()) {
            throw Error('missing next location step');
        }
        expr = Step.parse(lexer);
        path.step(op, expr);
    }
    else {
        expr = FilterExpr.parse(lexer);
        if (!expr) {
            expr = Step.parse(lexer);
            path = new PathExpr(FilterExpr.context());
            path.step('/', expr);
        }
        else if (!this.ops[lexer.peek()])
            return expr;
        else
            path = new PathExpr(expr);
    }

    while (true) {
        if (!this.ops[lexer.peek()]) break;
        op = lexer.next();
        if (lexer.empty()) {
            throw Error('missing next location step');
        }
        path.step(op, Step.parse(lexer));
    }

    return path;
};

PathExpr.prototype = new BaseExpr();

PathExpr.prototype.evaluate = function(ctx) {
    var nodeset = this.filter.evaluate(ctx);
    if (!nodeset.isNodeSet) throw Exception('Filter nodeset must be nodeset type');

    var steps = this.steps;

    for (var i = 0, l0 = steps.length; i < l0 && nodeset.length; i ++) {
        var step = steps[i][1];
        var reverse = step.reverse;
        var iter = nodeset.iterator(reverse);
        var prevNodeset = nodeset;
        nodeset = null;
        var node, next;
        if (!step.needContextPosition && step.axis == 'following') {
            for (node = iter(); next = iter(); node = next) {

                // Safari 2 node.contains problem
                if (uai.applewebkit4) {
                    var contains = false;
                    var ancestor = next;
                    do {
                        if (ancestor == node) {
                            contains = true;
                            break;
                        }
                    } while (ancestor = ancestor.parentNode);
                    if (!contains) break;
                }
                else {
                    try { if (!node.contains(next)) break }
                    catch(e) { if (!(next.compareDocumentPosition(node) & 8)) break }
                }
            }
            nodeset = step.evaluate(new Ctx(node));
        }
        else if (!step.needContextPosition && step.axis == 'preceding') {
            node = iter();
            nodeset = step.evaluate(new Ctx(node));
        }
        else {
            node = iter();
            var j = 0;
            nodeset = step.evaluate(new Ctx(node), false, prevNodeset, j);
            while (node = iter()) {
                j ++;
                nodeset.merge(step.evaluate(new Ctx(node), false, prevNodeset, j));
            }
        }
    }

    return nodeset;
};

PathExpr.prototype.step = function(op, step) {
    step.op = op;
    this.steps.push([op, step]);

    this.quickAttr = false;

    if (this.steps.length == 1) {
        if (op == '/' && step.axis == 'attribute') {
            var test = step.test;
            if (!test.notOnlyElement && test.name != '*') {
                this.quickAttr = true;
                this.attrName = test.name;
            }
        }
    }
};

PathExpr.prototype.show = function(indent) {
    indent = indent || '';
    var t = '';
    t += indent + 'path:' + '\n';
    indent += '    ';
    t += indent + 'filter:' + '\n';
    t += this.filter.show(indent + '    ');
    if (this.steps.length) {
        t += indent + 'steps:' + '\n';
        indent += '    ';
        for (var i = 0; i < this.steps.length; i ++) {
            var step = this.steps[i];
            t += indent + 'operator: ' + step[0] + '\n';
            t += step[1].show(indent);
        }
    }
    return t;
};


/**
 * class: FilterExpr
 */
if (!window.FilterExpr && window.defaultConfig)
    window.FilterExpr = null;

FilterExpr = function(primary) {
    this.primary = primary;
    this.predicates = [];

    this.datatype = primary.datatype;

    this.needContextPosition = primary.needContextPosition;

    this.needContextNode = primary.needContextNode;
};

FilterExpr.parse = function(lexer) {
    var expr, filter, token, ch;

    token = lexer.peek();
    ch = token.charAt(0);

    switch (ch) {
        case '$':
            expr = VariableReference.parse(lexer);
            break;

        case '(':
            lexer.next();
            expr = BinaryExpr.parse(lexer);
            if (lexer.empty()) {
                throw Error('unclosed "("');
            }
            if (lexer.next() != ')') {
                lexer.back();
                throw Error('bad token: ' + lexer.next());
            }
            break;

        case '"':
        case "'":
            expr = Literal.parse(lexer);
            break;

        default:
            if (!isNaN(+token)) {
                expr = Number.parse(lexer);
            }

            else if (NodeType.types[token]) {
                return null;
            }

            else if (ch.match(/(?![0-9])[\w]/) && lexer.peek(1) == '(') {
                expr = FunctionCall.parse(lexer);
            }
            else {
                return null;
            }
            break;
    }

    if (lexer.peek() != '[') return expr;

    filter = new FilterExpr(expr);

    BaseExprHasPredicates.parsePredicates(lexer, filter);

    return filter;
};

FilterExpr.root = function() {
    return new FunctionCall('root-node');
};
FilterExpr.context = function() {
    return new FunctionCall('context-node');
};

FilterExpr.prototype = new BaseExprHasPredicates();

FilterExpr.prototype.evaluate = function(ctx) {
    var nodeset = this.primary.evaluate(ctx);
    if(!nodeset.isNodeSet) {
        if (this.predicates.length)
            throw Error(
                'Primary result must be nodeset type ' +
                'if filter have predicate expression');
        return nodeset;
    }

    return  this.evaluatePredicates(nodeset);
};

FilterExpr.prototype.predicate = function(predicate) {
    this.predicates.push(predicate);
};

FilterExpr.prototype.show = function(indent) {
    indent = indent || '';
    var t = '';
    t += indent + 'filter: ' + '\n';
    indent += '    ';
    t += this.primary.show(indent);
    if (this.predicates.length) {
        t += indent + 'predicates: ' + '\n';
        indent += '    ';
        for (var i = 0; i < this.predicates.length; i ++) {
            t += this.predicates[i].show(indent);
        }
    }
    return t;
};


if (!window.NodeUtil && window.defaultConfig)
    window.NodeUtil = null;

NodeUtil = {
    to: function(valueType, node) {
        var type = node.nodeType;
/*@cc_on
        if (type == 1 && node.nodeName.toLowerCase() == 'title') {
            t = node.text;
        }
        else
@*/
        if (type == 9 || type == 1) {
            if (type == 9) {
                node =  node.documentElement;
            }
            else {
                node = node.firstChild;
            }
            for (var t = '', stack = [], i = 0; node;) {
                do {
                    if (node.nodeType != 1) {
                        t += node.nodeValue;
                    }
/*@cc_on
                    else if (node.nodeName.toLowerCase() == 'title') {
                        t += node.text;
                    }
@*/
                    stack[i++] = node; // push
                } while (node = node.firstChild);
                while (i && !(node = stack[--i].nextSibling)) {}
            }
        }
        else {
            var t = node.nodeValue;
        }
        switch (valueType) {
            case 'number':
                return + t;
            case 'boolean':
                return !! t;
            default:
                return t;
        }
    },
    attrPropMap: {
        name: 'name',
        'class': 'className',
        dir: 'dir',
        id: 'id',
        name: 'name',
        title: 'title'
    },
    attrMatch: function(node, attrName, attrValue) {
/*@cc_on @if (@_jscript)
        var propName = NodeUtil.attrPropMap[attrName];
        if (!attrName ||
            attrValue == null && (
                propName && node[propName] ||
                !propName && node.getAttribute && node.getAttribute(attrName)
            ) ||
            attrValue != null && (
                propName && node[propName] == attrValue ||
                !propName && node.getAttribute && node.getAttribute(attrName) == attrValue
            )) {
@else @*/
        if (!attrName ||
            attrValue == null && node.hasAttribute && node.hasAttribute(attrName) ||
            attrValue != null && node.getAttribute && node.getAttribute(attrName) == attrValue) {
/*@end @*/
            return true;
        }
        else {
            return false;
        }
    },
    getDescendantNodes: function(test, node, nodeset, attrName, attrValue, prevNodeset, prevIndex) {
        if (prevNodeset) {
            prevNodeset.delDescendant(node, prevIndex);
        }
/*@cc_on
        if (!test.notOnlyElement || test.type == 8 || (attrName && test.type == 0)) {

            var all = node.all;
            if (!all) {
                return nodeset;
            }

            var name = test.name;
            if (test.type == 8) name = '!';
            else if (test.type == 0) name = '*';

            if (name != '*') {
                all = all.tags(name);
                if (!all) {
                    return nodeset;
                }
            }

            if (attrName) {
                var result = []
                var i = 0;
                if (attrValue != null && (attrName == 'id' || attrName == 'name')) {
                    all = all[attrValue];
                    if (!all) {
                        return nodeset;
                    }
                    if (!all.length) {
                        all = [all];
                    }
                }
    
                while (node = all[i++]) {
                    if (NodeUtil.attrMatch(node, attrName, attrValue)) result.push(node);
                }

                all = result;
            }

            var i = 0;
            while (node = all[i++]) {
                if (name != '*' || node.tagName != '!') {
                    nodeset.push(node);
                }
            }

            return nodeset;
        }

        (function (parent) {
            var g = arguments.callee;
            var node = parent.firstChild;
            if (node) {
                for (; node; node = node.nextSibling) {
                    if (NodeUtil.attrMatch(node, attrName, attrValue)) {
                        if (test.match(node)) nodeset.push(node);
                    }
                    g(node);
                }
            }
        })(node);

        return nodeset;
@*/
        if (attrValue && attrName == 'id' && node.getElementById) {
            node = node.getElementById(attrValue);
            if (node && test.match(node)) {
                nodeset.push(node);
            }
        }
        else if (attrValue && attrName == 'name' && node.getElementsByName) {
            var nodes = node.getElementsByName(attrValue);
            for (var i = 0, l = nodes.length; i < l; i ++) {
                node = nodes[i];
                if (uai.opera ? (node.name == attrValue && test.match(node)) : test.match(node)) {
                    nodeset.push(node);
                }
            }
        }
        else if (attrValue && attrName == 'class' && node.getElementsByClassName) {
            var nodes = node.getElementsByClassName(attrValue);
            for (var i = 0, l = nodes.length; i < l; i ++) {
                node = nodes[i];
                if (node.className == attrValue && test.match(node)) {
                    nodeset.push(node);
                }
            }
        }
        else if (test.notOnlyElement) {
            (function (parent) {
                var f = arguments.callee;
                for (var node = parent.firstChild; node; node = node.nextSibling) {
                    if (NodeUtil.attrMatch(node, attrName, attrValue)) {
                        if (test.match(node.nodeType)) nodeset.push(node);
                    }
                    f(node);
                }
            })(node);
        }
        else {
            var name = test.name;
            if (node.getElementsByTagName) {
                var nodes = node.getElementsByTagName(name);
                if (nodes) {
                    var i = 0;
                    while (node = nodes[i++]) {
                        if (NodeUtil.attrMatch(node, attrName, attrValue)) nodeset.push(node);
                    }
                }
            }
        }
        return nodeset;
    },

    getChildNodes: function(test, node, nodeset, attrName, attrValue) {

/*@cc_on
        var children;

        if ((!test.notOnlyElement || test.type == 8 || (attrName && test.type == 0)) && (children = node.children)) {
            var name, elm;

            name = test.name;
            if (test.type == 8) name = '!';
            else if (test.type == 0) name = '*';

            if (name != '*') {
                children = children.tags(name);
                if (!children) {
                    return nodeset;
                }
            }

            if (attrName) {
                var result = []
                var i = 0;
                if (attrName == 'id' || attrName == 'name') {
                    children = children[attrValue];
    
                    if (!children) {
                        return nodeset;
                    }
    
                    if (!children.length) {
                        children = [children];
                    }
                }
    
                while (node = children[i++]) {
                    if (NodeUtil.attrMatch(node, attrName, attrValue)) result.push(node);
                }
                children = result;
            }

            var i = 0;
            while (node = children[i++]) {
                if (name != '*' || node.tagName != '!') {
                    nodeset.push(node);
                }
            }

            return nodeset;
        }

        for (var i = 0, node = node.firstChild; node; i++, node = node.nextSibling) {
            if (NodeUtil.attrMatch(node, attrName, attrValue)) {
                if (test.match(node)) nodeset.push(node);
            }
        }

        return nodeset;
@*/
        for (var node = node.firstChild; node; node = node.nextSibling) {
            if (NodeUtil.attrMatch(node, attrName, attrValue)) {
                if (test.match(node)) nodeset.push(node);
            }
        }
        return nodeset;
    }
};

/*@cc_on
var AttributeWrapper = function(node, parent, sourceIndex) {
    this.node = node;
    this.nodeType = 2;
    this.nodeValue = node.nodeValue;
    this.nodeName = node.nodeName;
    this.parentNode = parent;
    this.ownerElement = parent;
    this.parentSourceIndex = sourceIndex;
};

@*/


/**
 * class: Step
 */
if (!window.Step && window.defaultConfig)
    window.Step = null;

Step = function(axis, test) {
    // TODO check arguments and throw axis error
    this.axis = axis;
    this.reverse = Step.axises[axis][0];
    this.func = Step.axises[axis][1];
    this.test = test;
    this.predicates = [];
    this._quickAttr = Step.axises[axis][2]
};

Step.axises = {

    ancestor: [true, function(test, node, nodeset, _, __, prevNodeset, prevIndex) {
        while (node = node.parentNode) {
            if (prevNodeset && node.nodeType == 1) {
                prevNodeset.reserveDelByNode(node, prevIndex, true);
            }
            if (test.match(node)) nodeset.unshift(node);
        }
        return nodeset;
    }],

    'ancestor-or-self': [true, function(test, node, nodeset, _, __, prevNodeset, prevIndex) {
        do {
            if (prevNodeset && node.nodeType == 1) {
                prevNodeset.reserveDelByNode(node, prevIndex, true);
            }
            if (test.match(node)) nodeset.unshift(node);
        } while (node = node.parentNode)
        return nodeset;
    }],

    attribute: [false, function(test, node, nodeset) {
        var attrs = node.attributes;
        if (attrs) {
/*@cc_on
            var sourceIndex = node.sourceIndex;
@*/
            if ((test.notOnlyElement && test.type == 0) || test.name == '*') {
                for (var i = 0, l = attrs.length; i < l; i ++) {
                    var attr = attrs[i];
/*@cc_on @if (@_jscript)
                    if (attr.nodeValue) {
                        nodeset.push(new AttributeWrapper(attr, node, sourceIndex));
                    }
@else @*/
                    nodeset.push(attr);
/*@end @*/
                }
            }
            else {
                var attr = attrs.getNamedItem(test.name)
                
/*@cc_on @if (@_jscript)
                if (attr && attr.nodeValue) {
                    attr = new AttributeWrapper(attr, node, sourceIndex);;
@else @*/
                if (attr) {
/*@end @*/
                    nodeset.push(attr);
                }
            }
        }
        return nodeset;
    }],

    child: [false, NodeUtil.getChildNodes, true],

    descendant: [false, NodeUtil.getDescendantNodes, true],

    'descendant-or-self': [false, function(test, node, nodeset, attrName, attrValue, prevNodeset, prevIndex) {
        if (NodeUtil.attrMatch(node, attrName, attrValue)) {
            if (test.match(node)) nodeset.push(node);
        }
        return NodeUtil.getDescendantNodes(test, node, nodeset, attrName, attrValue, prevNodeset, prevIndex);
    }, true],

    following: [false, function(test, node, nodeset, attrName, attrValue) {
        do {
            var child = node;
            while (child = child.nextSibling) {
                if (NodeUtil.attrMatch(child, attrName, attrValue)) {
                    if (test.match(child)) nodeset.push(child);
                }
                nodeset = NodeUtil.getDescendantNodes(test, child, nodeset, attrName, attrValue);
            }
        } while (node = node.parentNode);
        return nodeset;
    }, true],

    'following-sibling': [false, function(test, node, nodeset, _, __, prevNodeset, prevIndex) {
        while (node = node.nextSibling) {

            if (prevNodeset && node.nodeType == 1) {
                prevNodeset.reserveDelByNode(node, prevIndex);
            }

            if (test.match(node)) {
                nodeset.push(node);
            }
        }
        return nodeset;
    }],

    namespace: [false, function(test, node, nodeset) {
        // not implemented
        return nodeset;
    }],

    parent: [false, function(test, node, nodeset) {
        if (node.nodeType == 9) {
            return nodeset;
        }
        if (node.nodeType == 2) {
            nodeset.push(node.ownerElement);
            return nodeset;
        }
        var node = node.parentNode;
        if (test.match(node)) nodeset.push(node);
        return nodeset;
    }],

    preceding: [true, function(test, node, nodeset, attrName, attrValue) {
        var parents = [];
        do {
            parents.unshift(node);
        } while (node = node.parentNode);

        for (var i = 1, l0 = parents.length; i < l0; i ++) {
            var siblings = [];
            node = parents[i];
            while (node = node.previousSibling) {
                siblings.unshift(node);
            }

            for (var j = 0, l1 = siblings.length; j < l1; j ++) {
                node = siblings[j];
                if (NodeUtil.attrMatch(node, attrName, attrValue)) {
                    if (test.match(node)) nodeset.push(node);
                }
                nodeset = NodeUtil.getDescendantNodes(test, node, nodeset, attrName, attrValue);
            }
        }
        return nodeset;
    }, true],

    'preceding-sibling': [true, function(test, node, nodeset, _, __, prevNodeset, prevIndex) {
        while (node = node.previousSibling) {

            if (prevNodeset && node.nodeType == 1) {
                prevNodeset.reserveDelByNode(node, prevIndex, true);
            }

            if (test.match(node)) {
                nodeset.unshift(node)
            }
        }
        return nodeset;
    }],

    self: [false, function(test, node, nodeset) {
        if (test.match(node)) nodeset.push(node);
        return nodeset;
    }]
};

Step.parse = function(lexer) {
    var axis, test, step, token;

    if (lexer.peek() == '.') {
        step = this.self();
        lexer.next();
    }
    else if (lexer.peek() == '..') {
        step = this.parent();
        lexer.next();
    }
    else {
        if (lexer.peek() == '@') {
            axis = 'attribute';
            lexer.next();
            if (lexer.empty()) {
                throw Error('missing attribute name');
            }
        }
        else {
            if (lexer.peek(1) == '::') {
                
                if (!lexer.peek().charAt(0).match(/(?![0-9])[\w]/)) {
                    throw Error('bad token: ' + lexer.next());
                }
        
                axis = lexer.next();
                lexer.next();

                if (!this.axises[axis]) {
                    throw Error('invalid axis: ' + axis);
                }
                if (lexer.empty()) {
                    throw Error('missing node name');
                }
            }
            else {
                axis = 'child';
            }
        }
    
        token = lexer.peek();
        if (!token.charAt(0).match(/(?![0-9])[\w]/)) {
            if (token == '*') {
                test = NameTest.parse(lexer)
            }
            else {
                throw Error('bad token: ' + lexer.next());
            }
        }
        else {
            if (lexer.peek(1) == '(') {
                if (!NodeType.types[token]) {
                    throw Error('invalid node type: ' + token);
                }
                test = NodeType.parse(lexer)
            }
            else {
                test = NameTest.parse(lexer);
            }
        }
        step = new Step(axis, test);
    }

    BaseExprHasPredicates.parsePredicates(lexer, step);

    return step;
};

Step.self = function() {
    return new Step('self', new NodeType('node'));
};

Step.parent = function() {
    return new Step('parent', new NodeType('node'));
};

Step.prototype = new BaseExprHasPredicates();

Step.prototype.evaluate = function(ctx, special, prevNodeset, prevIndex) {
    var node = ctx.node;
    var reverse = false;

    if (!special && this.op == '//') {

        if (!this.needContextPosition && this.axis == 'child') {
            if (this.quickAttr) {
                var attrValue = this.attrValueExpr ? this.attrValueExpr.string(ctx) : null;
                var nodeset = NodeUtil.getDescendantNodes(this.test, node, new NodeSet(), this.attrName, attrValue, prevNodeset, prevIndex);
                nodeset = this.evaluatePredicates(nodeset, 1);
            }
            else {
                var nodeset = NodeUtil.getDescendantNodes(this.test, node, new NodeSet(), null, null, prevNodeset, prevIndex);
                nodeset = this.evaluatePredicates(nodeset);
            }
        }
        else {
            var step = new Step('descendant-or-self', new NodeType('node'));
            var nodes = step.evaluate(ctx, false, prevNodeset, prevIndex).list();
            var nodeset = null;
            step.op = '/';
            for (var i = 0, l = nodes.length; i < l; i ++) {
                if (!nodeset) {
                    nodeset = this.evaluate(new Ctx(nodes[i]), true);
                }
                else {
                    nodeset.merge(this.evaluate(new Ctx(nodes[i]), true));
                }
            }
            nodeset = nodeset || new NodeSet();
        }
    }
    else {

        if (this.needContextPosition) {
            prevNodeset = null;
            prevIndex = null;
        }

        if (this.quickAttr) {
            var attrValue = this.attrValueExpr ? this.attrValueExpr.string(ctx) : null;
            var nodeset = this.func(this.test, node, new NodeSet(), this.attrName, attrValue, prevNodeset, prevIndex);
            nodeset = this.evaluatePredicates(nodeset, 1);
        }
        else {
            var nodeset = this.func(this.test, node, new NodeSet(), null, null, prevNodeset, prevIndex);
            nodeset = this.evaluatePredicates(nodeset);
        }
        if (prevNodeset) {
            prevNodeset.doDel();
        }
    }
    return nodeset;
};

Step.prototype.predicate = function(predicate) {
    this.predicates.push(predicate);

    if (predicate.needContextPosition ||
        predicate.datatype == 'number'||
        predicate.datatype == 'void') {
        this.needContextPosition = true;
    }

    if (this._quickAttr && this.predicates.length == 1 && predicate.quickAttr) {
        var attrName = predicate.attrName;
/*@cc_on @if (@_jscript)
        this.attrName = attrName.toLowerCase();
@else @*/
        this.attrName = attrName;
/*@end @*/
        this.attrValueExpr = predicate.attrValueExpr;
        this.quickAttr = true;
    }
};

Step.prototype.show = function(indent) {
    indent = indent || '';
    var t = '';
    t += indent + 'step: ' + '\n';
    indent += '    ';
    if (this.axis) t += indent + 'axis: ' + this.axis + '\n';
    t += this.test.show(indent);
    if (this.predicates.length) {
        t += indent + 'predicates: ' + '\n';
        indent += '    ';
        for (var i = 0; i < this.predicates.length; i ++) {
            t += this.predicates[i].show(indent);
        }
    }
    return t;
};



/**
 * NodeType
 */
if (!window.NodeType && window.defaultConfig)
    window.NodeType = null;
    
NodeType = function(name, literal) {
    this.name = name;
    this.literal = literal;

    switch (name) {
        case 'comment':
            this.type = 8;
            break;
        case 'text':
            this.type = 3;
            break;
        case 'processing-instruction':
            this.type = 7;
            break;
        case 'node':
            this.type = 0;
            break;
    }
};

NodeType.types = {
    'comment':1, 'text':1, 'processing-instruction':1, 'node':1
};

NodeType.parse = function(lexer) {
    var type, literal, ch;
    type = lexer.next();
    lexer.next();
    if (lexer.empty()) {
        throw Error('bad nodetype');
    }
    ch = lexer.peek().charAt(0);
    if (ch == '"' || ch == "'") {
        literal = Literal.parse(lexer);
    }
    if (lexer.empty()) {
        throw Error('bad nodetype');
    }
    if (lexer.next() != ')') {
        lexer.back();
        throw Error('bad token ' + lexer.next());
    }
    return new NodeType(type, literal);
};

NodeType.prototype = new BaseExpr();

NodeType.prototype.notOnlyElement = true;

NodeType.prototype.match = function(node) {
    return !this.type || this.type == node.nodeType;
};

NodeType.prototype.show = function(indent) {
    indent = indent || '';
    var t = '';
    t += indent + 'nodetype: ' + this.type + '\n';
    if (this.literal) {
        indent += '    ';
        t += this.literal.show(indent);
    }
    return t;
};


/**
 * NodeType
 */
if (!window.NameTest && window.defaultConfig)
    window.NameTest = null;

NameTest = function(name) {
    this.name = name.toLowerCase();
};

NameTest.parse = function(lexer) {
    if (lexer.peek() != '*' &&  lexer.peek(1) == ':' && lexer.peek(2) == '*') {
        return new NameTest(lexer.next() + lexer.next() + lexer.next());
    }
    return new NameTest(lexer.next());
};

NameTest.prototype = new BaseExpr();

NameTest.prototype.match = function(node) {
    var type = node.nodeType;

    if (type == 1 || type == 2) {
        if (this.name == '*' || this.name == node.nodeName.toLowerCase()) {
            return true;
        }
    }
    return false;
};

NameTest.prototype.show = function(indent) {
    indent = indent || '';
    var t = '';
    t += indent + 'nametest: ' + this.name + '\n';
    return t;
};


/**
 * class: VariableRefernce
 */
if (!window.VariableReference && window.defaultConfig)
    window.VariableReference = null;
    
VariableReference = function(name) {
    this.name = name.substring(1);
};


VariableReference.parse = function(lexer) {
    var token = lexer.next();
    if (token.length < 2) {
        throw Error('unnamed variable reference');
    }
    return new VariableReference(token)
};

VariableReference.prototype = new BaseExpr();

VariableReference.prototype.datatype = 'void';

VariableReference.prototype.show = function(indent) {
    indent = indent || '';
    var t = '';
    t += indent + 'variable: ' + this.name + '\n';
    return t;
};


/**
 * class: Literal
 */
if (!window.Literal && window.defaultConfig)
    window.Literal = null;

Literal = function(text) {
    this.text = text.substring(1, text.length - 1);
};

Literal.parse = function(lexer) {
    var token = lexer.next();
    if (token.length < 2) {
        throw Error('unclosed literal string');
    }
    return new Literal(token)
};

Literal.prototype = new BaseExpr();

Literal.prototype.datatype = 'string';

Literal.prototype.evaluate = function(ctx) {
    return this.text;
};

Literal.prototype.show = function(indent) {
    indent = indent || '';
    var t = '';
    t += indent + 'literal: ' + this.text + '\n';
    return t;
};


/**
 * class: Number
 */
if (!window.Number && window.defaultConfig)
    window.Number = null;

Number = function(digit) {
    this.digit = +digit;
};


Number.parse = function(lexer) {
    return new Number(lexer.next());
};

Number.prototype = new BaseExpr();

Number.prototype.datatype = 'number';

Number.prototype.evaluate = function(ctx) {
    return this.digit;
};

Number.prototype.show = function(indent) {
    indent = indent || '';
    var t = '';
    t += indent + 'number: ' + this.digit + '\n';
    return t;
};


/**
 * class: FunctionCall
 */
if (!window.FunctionCall && window.defaultConfig)
    window.FunctionCall = null;

FunctionCall = function(name) {
    var info = FunctionCall.funcs[name];
    this.name = name;
    this.func = info[0];
    this.args = [];

    this.datatype = info[1];

    if (info[2]) {
        this.needContextPosition = true;
    }

    this.needContextNodeInfo = info[3];
    this.needContextNode = this.needContextNodeInfo[0]
};

FunctionCall.funcs = {

    // Original Function
    'context-node': [function() {
        if (arguments.length != 0) {
            throw Error('Function context-node expects ()');
        }
        var ns;
        ns = new NodeSet();
        ns.push(this.node);
        return ns;
    }, 'nodeset', false, [true]],

    // Original Function
    'root-node': [function() {
        if (arguments.length != 0) {
            throw Error('Function root-node expects ()');
        }
        var ns, ctxn;
        ns = new NodeSet();
        ctxn = this.node;
        if (ctxn.nodeType == 9)
            ns.push(ctxn);
        else
            ns.push(ctxn.ownerDocument);
        return ns;
    }, 'nodeset', false, []],

    last: [function() {
        if (arguments.length != 0) {
            throw Error('Function last expects ()');
        }
        return this.last;
    }, 'number', true, []],

    position: [function() {
        if (arguments.length != 0) {
            throw Error('Function position expects ()');
        }
        return this.position;
    }, 'number', true, []],

    count: [function(ns) {
        if (arguments.length != 1 || !(ns = ns.evaluate(this)).isNodeSet) {
            throw Error('Function count expects (nodeset)');
        }
        return ns.length;
    }, 'number', false, []],

    id: [function(s) {
        var ids, ns, i, id, elm, ctxn, doc;
        if (arguments.length != 1) {
            throw Error('Function id expects (object)');
        }
        ctxn = this.node;
        if (ctxn.nodeType == 9)
            doc = ctxn;
        else
            doc = ctxn.ownerDocument;
/*@cc_on
        all = doc.all;
@*/
        s = s.string(this);
        ids = s.split(/\s+/);
        ns = new NodeSet();
        for (i = 0, l = ids.length; i < l; i ++) {
            id = ids[i];

/*@cc_on @if (@_jscript)
            elm = all[id];
            if (elm) {
                if (elm.length) {
                    var elms = elm;
                    for (var j = 0, l0 = elms.length; j < l0; j ++) {
                        var elem = elms[j];
                        if (id == elem.id) {
                            ns.push(elem);
                            break;
                        }
                    }
                }
                else if (id == elm.id) {
                    ns.push(elm)
                }
            }
@else @*/
            elm = doc.getElementById(id);
            if (uai.opera && elm.id != id) {
                var elms = doc.getElementsByName(id);
                for (var j = 0, l0 = elms.length; j < l0; j ++) {
                    elm = elms[j];
                    if (elm.id == id) {
                        ns.push(elm);
                    }
                }
            }
            else {
                if (elm) ns.push(elm)
            }
/*@end @*/

        }
        ns.isSorted = false;
        return ns;
    }, 'nodeset', false, []],

    'local-name': [function(ns) {
        var nd;
        switch (arguments.length) {
            case 0:
                nd = this.node;
                break;
            case 1:
                if ((ns = ns.evaluate(this)).isNodeSet) {
                    nd = ns.first();
                    break;
                }
            default:
                throw Error('Function local-name expects (nodeset?)');
                break;
        }
        return '' + nd.nodeName.toLowerCase();
    }, 'string', false, [true, false]],

    name: [function(ns) {
        // not implemented
        return FunctionCall.funcs['local-name'][0].apply(this, arguments);
    }, 'string', false, [true, false]],

    'namespace-uri': [function(ns) {
        // not implemented
        return '';
    }, 'string', false, [true, false]],

    string: [function(s) {
        switch (arguments.length) {
            case 0:
                s = NodeUtil.to('string', this.node);
                break;
            case 1:
                s = s.string(this);
                break;
            default:
                throw Error('Function string expects (object?)');
                break;
        }
        return s;
    }, 'string', false, [true, false]],

    concat: [function(s1, s2) {
        if (arguments.length < 2) {
            throw Error('Function concat expects (string, string[, ...])');
        }
        for (var t = '', i = 0, l = arguments.length; i < l; i ++) {
            t += arguments[i].string(this);
        }
        return t;
    }, 'string', false, []],

    'starts-with': [function(s1, s2) {
        if (arguments.length != 2) {
            throw Error('Function starts-with expects (string, string)');
        }
        s1 = s1.string(this);
        s2 = s2.string(this);
        return s1.indexOf(s2) == 0;
    }, 'boolean', false, []],

    contains: [function(s1, s2) {
        if (arguments.length != 2) {
            throw Error('Function contains expects (string, string)');
        }
        s1 = s1.string(this);
        s2 = s2.string(this);
        return s1.indexOf(s2) != -1;
    }, 'boolean', false, []],

    substring: [function(s, n1, n2) {
        var a1, a2;
        s = s.string(this);
        n1 = n1.number(this);
        switch (arguments.length) {
            case 2:
                n2 = s.length - n1 + 1;
                break;
            case 3:
                n2 = n2.number(this);
                break;
            default:
                throw Error('Function substring expects (string, string)');
                break;
        }
        n1 = Math.round(n1);
        n2 = Math.round(n2);
        a1 = n1 - 1;
        a2 = n1 + n2 - 1;
        if (a2 == Infinity) {
            return s.substring(a1 < 0 ? 0 : a1);
        }
        else {
            return s.substring(a1 < 0 ? 0 : a1, a2)
        }
    }, 'string', false, []],

    'substring-before': [function(s1, s2) {
        var n;
        if (arguments.length != 2) {
            throw Error('Function substring-before expects (string, string)');
        }
        s1 = s1.string(this);
        s2 = s2.string(this);
        n = s1.indexOf(s2);
        if (n == -1) return '';
        return s1.substring(0, n);
    }, 'string', false, []],

    'substring-after': [function(s1, s2) {
        if (arguments.length != 2) {
            throw Error('Function substring-after expects (string, string)');
        }
        s1 = s1.string(this);
        s2 = s2.string(this);
        var n = s1.indexOf(s2);
        if (n == -1) return '';
        return s1.substring(n + s2.length);
    }, 'string', false, []],

    'string-length': [function(s) {
        switch (arguments.length) {
            case 0:
                s = NodeUtil.to('string', this.node);
                break;
            case 1:
                s = s.string(this);
                break;
            default:
                throw Error('Function string-length expects (string?)');
                break;
        }
        return s.length;
    }, 'number', false, [true, false]],

    'normalize-space': [function(s) {
        switch (arguments.length) {
            case 0:
                s = NodeUtil.to('string', this.node);
                break;
            case 1:
                s = s.string(this);
                break;
            default:
                throw Error('Function normalize-space expects (string?)');
                break;
        }
        return s.replace(/\s+/g, ' ').replace(/^ /, '').replace(/ $/, '');
    }, 'string', false, [true, false]],

    translate: [function(s1, s2, s3) {
        if (arguments.length != 3) {
            throw Error('Function translate expects (string, string, string)');
        }
        s1 = s1.string(this);
        s2 = s2.string(this);
        s3 = s3.string(this);

        var map = [];
        for (var i = 0, l = s2.length; i < l; i ++) {
            var ch = s2.charAt(i);
            if (!map[ch]) map[ch] = s3.charAt(i) || '';
        }
        for (var t = '', i = 0, l = s1.length; i < l; i ++) {
            var ch = s1.charAt(i);
            var replace = map[ch]
            t += (replace != undefined) ? replace : ch;
        }
        return t;
    }, 'string', false, []],

    'boolean': [function(b) {
        if (arguments.length != 1) {
            throw Error('Function boolean expects (object)');
        }
        return b.bool(this)
    }, 'boolean', false, []],

    not: [function(b) {
        if (arguments.length != 1) {
            throw Error('Function not expects (object)');
        }
        return !b.bool(this)
    }, 'boolean', false, []],

    'true': [function() {
        if (arguments.length != 0) {
            throw Error('Function true expects ()');
        }
        return true;
    }, 'boolean', false, []],

    'false': [function() {
        if (arguments.length != 0) {
            throw Error('Function false expects ()');
        }
        return false;
    }, 'boolean', false, []],

    lang: [function(s) {
        // not implemented
        return false;
    }, 'boolean', false, []],

    number: [function(n) {
        switch (arguments.length) {
            case 0:
                n = NodeUtil.to('number', this.node);
                break;
            case 1:
                n = n.number(this);
                break;
            default:
                throw Error('Function number expects (object?)');
                break;
        }
        return n;
    }, 'number', false, [true, false]],

    sum: [function(ns) {
        var nodes, n, i, l;
        if (arguments.length != 1 || !(ns = ns.evaluate(this)).isNodeSet) {
            throw Error('Function sum expects (nodeset)');
        }
        nodes = ns.list();
        n = 0;
        for (i = 0, l = nodes.length; i < l; i ++) {
            n += NodeUtil.to('number', nodes[i]);
        }
        return n;
    }, 'number', false, []],

    floor: [function(n) {
        if (arguments.length != 1) {
            throw Error('Function floor expects (number)');
        }
        n = n.number(this);
        return Math.floor(n);
    }, 'number', false, []],

    ceiling: [function(n) {
        if (arguments.length != 1) {
            throw Error('Function ceiling expects (number)');
        }
        n = n.number(this);
        return Math.ceil(n);
    }, 'number', false, []],

    round: [function(n) {
        if (arguments.length != 1) {
            throw Error('Function round expects (number)');
        }
        n = n.number(this);
        return Math.round(n);
    }, 'number', false, []]
};

FunctionCall.parse = function(lexer) {
    var expr, func = new FunctionCall(lexer.next());
    lexer.next();
    while (lexer.peek() != ')') {
        if (lexer.empty()) {
            throw Error('missing function argument list');
        }
        expr = BinaryExpr.parse(lexer);
        func.arg(expr);
        if (lexer.peek() != ',') break;
        lexer.next();
    }
    if (lexer.empty()) {
        throw Error('unclosed function argument list');
    }
    if (lexer.next() != ')') {
        lexer.back();
        throw Error('bad token: ' + lexer.next());
    }
    return func
};

FunctionCall.prototype = new BaseExpr();

FunctionCall.prototype.evaluate = function (ctx) {
    return this.func.apply(ctx, this.args);
};

FunctionCall.prototype.arg = function(arg) {
    this.args.push(arg);

    if (arg.needContextPosition) {
        this.needContextPosition = true;
    }

    var args = this.args;
    if (arg.needContextNode) {
        args.needContexNode = true;
    }
    this.needContextNode = args.needContextNode ||
                            this.needContextNodeInfo[args.length];
};

FunctionCall.prototype.show = function(indent) {
    indent = indent || '';
    var t = '';
    t += indent + 'function: ' + this.name + '\n';
    indent += '    ';

    if (this.args.length) {
        t += indent + 'arguments: ' + '\n';
        indent += '    ';
        for (var i = 0; i < this.args.length; i ++) {
            t += this.args[i].show(indent);
        }
    }

    return t;
};


/*@cc_on @if (@_jscript)
var NodeWrapper = function(node, sourceIndex, subIndex, attributeName) {
    this.node = node;
    this.nodeType = node.nodeType;
    this.sourceIndex = sourceIndex;
    this.subIndex = subIndex;
    this.attributeName = attributeName || '';
    this.order = String.fromCharCode(sourceIndex) + String.fromCharCode(subIndex) + attributeName;
};

NodeWrapper.prototype.toString = function() {
    return this.order;
};
@else @*/
var NodeID = {
    uuid: 1,
    get: function(node) {
        return node.__jsxpath_id__ || (node.__jsxpath_id__ = this.uuid++);
    }
};
/*@end @*/

if (!window.NodeSet && window.defaultConfig)
    window.NodeSet = null;
    
NodeSet = function() {
    this.length = 0;
    this.nodes = [];
    this.seen = {};
    this.idIndexMap = null;
    this.reserveDels = [];
};

NodeSet.prototype.isNodeSet = true;
NodeSet.prototype.isSorted = true;

/*@_cc_on
NodeSet.prototype.shortcut = true;
@*/

NodeSet.prototype.merge = function(nodeset) {
    this.isSorted = false;
    if (nodeset.only) {
        return this.push(nodeset.only);
    }

    if (this.only){
        var only = this.only;
        delete this.only;
        this.push(only);
        this.length --;
    }

    var nodes = nodeset.nodes;
    for (var i = 0, l = nodes.length; i < l; i ++) {
        this._add(nodes[i]);
    }
};

NodeSet.prototype.sort = function() {
    if (this.only) return;
    if (this.sortOff) return;

    if (!this.isSorted) {
        this.isSorted = true;
        this.idIndexMap = null;

/*@cc_on
        if (this.shortcut) {
            this.nodes.sort();
        }
        else {
            this.nodes.sort(function(a, b) {
                var result;
                result = a.sourceIndex - b.sourceIndex;
                if (result == 0)
                    return a.subIndex - a.subIndex;
                else
                    return result;
            });
        }
        return;
@*/
        var nodes = this.nodes;
        nodes.sort(function(a, b) {
            if (a == b) return 0;

            if (a.compareDocumentPosition) {
                var result = a.compareDocumentPosition(b);
                if (result & 2) return 1;
                if (result & 4) return -1;
                return 0;
            }
            else {
                var node1 = a, node2 = b, ancestor1 = a, ancestor2 = b, deep1 = 0, deep2 = 0;

                while(ancestor1 = ancestor1.parentNode) deep1 ++;
                while(ancestor2 = ancestor2.parentNode) deep2 ++;

                // same deep
                if (deep1 > deep2) {
                    while (deep1-- != deep2) node1 = node1.parentNode;
                    if (node1 == node2) return 1;
                }
                else if (deep2 > deep1) {
                    while (deep2-- != deep1) node2 = node2.parentNode;
                    if (node1 == node2) return -1;
                }

                while ((ancestor1 = node1.parentNode) != (ancestor2 = node2.parentNode)) {
                    node1 = ancestor1;
                    node2 = ancestor2;
                }

                // node1 is node2's sibling
                while (node1 = node1.nextSibling) if (node1 == node2) return -1;

                return 1;
            }
        });
    }
};


/*@cc_on @if (@_jscript)
NodeSet.prototype.sourceOffset = 1;
NodeSet.prototype.subOffset = 2;
NodeSet.prototype.createWrapper = function(node) {
    var parent, child, attributes, attributesLength, sourceIndex, subIndex, attributeName;

    sourceIndex = node.sourceIndex;

    if (typeof sourceIndex != 'number') {
        type = node.nodeType;
        switch (type) {
            case 2:
                parent = node.parentNode;
                sourceIndex = node.parentSourceIndex;
                subIndex = -1;
                attributeName = node.nodeName;
                break;
            case 9:
                subIndex = -2;
                sourceIndex = -1;
                break;
            default:
                child = node;
                subIndex = 0;
                do {
                    subIndex ++;
                    sourceIndex = child.sourceIndex;
                    if (sourceIndex) {
                        parent = child;
                        child = child.lastChild;
                        if (!child) {
                            child = parent;
                            break;
                        }
                        subIndex ++;
                    }
                } while (child = child.previousSibling);
                if (!sourceIndex) {
                    sourceIndex = node.parentNode.sourceIndex;
                }
                break;
        }
    }
    else {
        subIndex = -2;
    }

    sourceIndex += this.sourceOffset;
    subIndex += this.subOffset;

    return new NodeWrapper(node, sourceIndex, subIndex, attributeName);
};

NodeSet.prototype.reserveDelBySourceIndexAndSubIndex = function(sourceIndex, subIndex, offset, reverse) {
    var map = this.createIdIndexMap();
    var index;
    if ((map = map[sourceIndex]) && (index = map[subIndex])) {
        if (reverse && (this.length - offset - 1) > index || !reverse && offset < index) {
            var obj = {
                value: index,
                order: String.fromCharCode(index),
                toString: function() { return this.order },
                valueOf: function() { return this.value }
            };
            this.reserveDels.push(obj);
        }
    }
};
@else @*/
NodeSet.prototype.reserveDelByNodeID = function(id, offset, reverse) {
    var map = this.createIdIndexMap();
    var index;
    if (index = map[id]) {
        if (reverse && (this.length - offset - 1) > index || !reverse && offset < index) {
            var obj = {
                value: index,
                order: String.fromCharCode(index),
                toString: function() { return this.order },
                valueOf: function() { return this.value }
            };
            this.reserveDels.push(obj);
        }
    }
};
/*@end @*/

NodeSet.prototype.reserveDelByNode = function(node, offset, reverse) {
/*@cc_on @if (@_jscript)
    node = this.createWrapper(node);
    this.reserveDelBySourceIndexAndSubIndex(node.sourceIndex, node.subIndex, offset, reverse);
@else @*/
    this.reserveDelByNodeID(NodeID.get(node), offset, reverse);
/*@end @*/
};

NodeSet.prototype.doDel = function() {
    if (!this.reserveDels.length) return;

    if (this.length < 0x10000) {
        var dels = this.reserveDels.sort(function(a, b) { return b - a });
    }
    else {
        var dels = this.reserveDels.sort(function(a, b) { return b - a });
    }
    for (var i = 0, l = dels.length; i < l; i ++) {
        this.del(dels[i]);
    }
    this.reserveDels = [];
    this.idIndexMap = null;
};

NodeSet.prototype.createIdIndexMap = function() {
    if (this.idIndexMap) {
        return this.idIndexMap;
    }
    else {
        var map = this.idIndexMap = {};
        var nodes = this.nodes;
        for (var i = 0, l = nodes.length; i < l; i ++) {
            var node = nodes[i];
/*@cc_on @if (@_jscript)
            var sourceIndex = node.sourceIndex;
            var subIndex = node.subIndex;
            if (!map[sourceIndex]) map[sourceIndex] = {};
            map[sourceIndex][subIndex] = i;
@else @*/
            var id = NodeID.get(node);
            map[id] = i;
/*@end @*/
        }
        return map;
    }
};

NodeSet.prototype.del = function(index) {
    this.length --;
    if (this.only) {
        delete this.only;
    }
    else {  
        var node = this.nodes.splice(index, 1)[0];

        if (this._first == node) {
            delete this._first;
            delete this._firstSourceIndex;
            delete this._firstSubIndex;
        }

/*@cc_on @if (@_jscript)
        delete this.seen[node.sourceIndex][node.subIndex];
@else @*/
        delete this.seen[NodeID.get(node)];
/*@end @*/
    }
};


NodeSet.prototype.delDescendant = function(elm, offset) {
    if (this.only) return;
    var nodeType = elm.nodeType;
    if (nodeType != 1 && nodeType != 9) return;
    if (uai.applewebkit4) return;

    // element || document
    if (!elm.contains) {
        if (nodeType == 1) {
            var _elm = elm;
            elm = {
                contains: function(node) {
                    return node.compareDocumentPosition(_elm) & 8;
                }
            };
        }
        else {
            // document
            elm = {
                contains: function() {
                    return true;
                }
            };
        }
    }

    var nodes = this.nodes;
    for (var i = offset + 1; i < nodes.length; i ++) {

/*@cc_on @if (@_jscript)
        if (nodes[i].node.nodeType == 1 && elm.contains(nodes[i].node)) {
@else @*/
        if (elm.contains(nodes[i])) {
/*@end @*/
            this.del(i);
            i --;
        }
    }
};

NodeSet.prototype._add = function(node, reverse) {

/*@cc_on @if (@_jscript)

    var first, firstSourceIndex, firstSubIndex, sourceIndex, subIndex, attributeName;

    sourceIndex = node.sourceIndex;
    subIndex = node.subIndex;
    attributeName = node.attributeName;
    seen = this.seen;

    seen = seen[sourceIndex] || (seen[sourceIndex] = {});

    if (node.nodeType == 2) {
        seen = seen[subIndex] || (seen[subIndex] = {});
        if (seen[attributeName]) {
            return true;
        }
        seen[attributeName] = true;
    }
    else {
        if (seen[subIndex]) {
            return true;
        }
        seen[subIndex] = true;
    }

    if (sourceIndex >= 0x10000 || subIndex >= 0x10000) {
        this.shortcut = false;
    }

    // if this._first is undefined and this.nodes is not empty
    // then first node shortcut is disabled.
    if (this._first || this.nodes.length == 0) {
        first = this._first;
        firstSourceIndex = this._firstSourceIndex;
        firstSubIndex = this._firstSubIndex;
        if (!first || firstSourceIndex > sourceIndex || (firstSourceIndex == sourceIndex && firstSubIndex > subIndex)) {
            this._first = node;
            this._firstSourceIndex = sourceIndex;
            this._firstSubIndex = subIndex
        }
    }

@else @*/

    var seen = this.seen;
    var id = NodeID.get(node);
    if (seen[id]) return true;
    seen[id] = true;

/*@end @*/

    this.length++;
    if (reverse) 
        this.nodes.unshift(node);
    else
        this.nodes.push(node);
};


NodeSet.prototype.unshift = function(node) {
    if (!this.length) {
        this.length ++;
        this.only = node;
        return
    }
    if (this.only){
        var only = this.only;
        delete this.only;
        this.unshift(only);
        this.length --;
    }
/*@cc_on
    node = this.createWrapper(node);
@*/
    return this._add(node, true);
};


NodeSet.prototype.push = function(node) {
    if (!this.length) {
        this.length ++;
        this.only = node;
        return;
    }
    if (this.only) {
        var only = this.only;
        delete this.only;
        this.push(only);
        this.length --;
    }
/*@cc_on
    node = this.createWrapper(node);
@*/
    return this._add(node);
};

NodeSet.prototype.first = function() {
    if (this.only) return this.only;
/*@cc_on
    if (this._first) return this._first.node;
    if (this.nodes.length > 1) this.sort();
    var node = this.nodes[0];
    return node ? node.node : undefined;
@*/
    if (this.nodes.length > 1) this.sort();
    return this.nodes[0];
};

NodeSet.prototype.list = function() {
    if (this.only) return [this.only];
    this.sort();
/*@cc_on
    var i, l, nodes, results;
    nodes = this.nodes;
    results = [];
    for (i = 0, l = nodes.length; i < l; i ++) {
        results.push(nodes[i].node);
    }
    return results;
@*/
    return this.nodes;
};

NodeSet.prototype.string = function() {
    var node = this.only || this.first();
    return node ? NodeUtil.to('string', node) : '';
};

NodeSet.prototype.bool = function() {
    return !! (this.length || this.only);
};

NodeSet.prototype.number = function() {
    return + this.string();
};

NodeSet.prototype.iterator = function(reverse) {
    this.sort();
    var nodeset = this;

    if (!reverse) {
        var count = 0;
        return function() {
            if (nodeset.only && count++ == 0) return nodeset.only;
/*@cc_on @if(@_jscript)
            var wrapper = nodeset.nodes[count++];
            if (wrapper) return wrapper.node;
            return undefined;
@else @*/
            return nodeset.nodes[count++];
/*@end @*/
        };
    }
    else {
        var count = 0;
        return function() {
            var index = nodeset.length - (count++) - 1;
            if (nodeset.only && index == 0) return nodeset.only;
/*@cc_on @if(@_jscript)
            var wrapper = nodeset.nodes[index];
            if (wrapper) return wrapper.node;
            return undefined;
@else @*/
            return nodeset.nodes[index];
/*@end @*/
        };
    }
};


var install = function(win) {

    win = win || this;

    win.XPathExpression = function(expr) {
        if (!expr.length) {
            throw Error('no expression');
        }
        var lexer = this.lexer = Lexer(expr);
        if (lexer.empty()) {
            throw Error('no expression');
        }
        this.expr = BinaryExpr.parse(lexer);
        if (!lexer.empty()) {
            throw Error('bad token: ' + lexer.next());
        }
    };
    
    win.XPathExpression.prototype.evaluate = function(node, type) {
        return new XPathResult(this.expr.evaluate(new Ctx(node)), type);
    };
    
    win.XPathResult = function (value, type) {
        if (type == 0) {
            switch (typeof value) {
                case 'object':  type ++; // 4
                case 'boolean': type ++; // 3
                case 'string':  type ++; // 2
                case 'number':  type ++; // 1
            }
        }
    
        this.resultType = type;
    
        switch (type) {
            case 1:
                this.numberValue = value.isNodeSet ? value.number() : +value;
                return;
            case 2:
                this.stringValue = value.isNodeSet ? value.string() : '' + value;
                return;
            case 3:
                this.booleanValue = value.isNodeSet ? value.bool() : !! value;
                return;
            case 4: case 5: case 6: case 7:
                this.nodes = value.list();
                this.snapshotLength = value.length;
                this.index = 0;
                this.invalidIteratorState = false;
                break;
            case 8: case 9:
                this.singleNodeValue = value.first();
                return;
        }
    };
    
    win.XPathResult.prototype.iterateNext = function() { return this.nodes[this.index++] };
    win.XPathResult.prototype.snapshotItem = function(i) { return this.nodes[i] };
    
    win.XPathResult.ANY_TYPE = 0;
    win.XPathResult.NUMBER_TYPE = 1;
    win.XPathResult.STRING_TYPE = 2;
    win.XPathResult.BOOLEAN_TYPE = 3;
    win.XPathResult.UNORDERED_NODE_ITERATOR_TYPE = 4;
    win.XPathResult.ORDERED_NODE_ITERATOR_TYPE = 5;
    win.XPathResult.UNORDERED_NODE_SNAPSHOT_TYPE = 6;
    win.XPathResult.ORDERED_NODE_SNAPSHOT_TYPE = 7;
    win.XPathResult.ANY_UNORDERED_NODE_TYPE = 8;
    win.XPathResult.FIRST_ORDERED_NODE_TYPE = 9;
    
    
    win.document.createExpression = function(expr) {
        return new XPathExpression(expr, null);
    };
    
    win.document.evaluate = function(expr, context, _, type) {
        return document.createExpression(expr, null).evaluate(context, type);
    };
};

var win;

if (config.targetFrame) {
    var frame = document.getElementById(config.targetFrame);
    if (frame) win = frame.contentWindow;
}

install(win || window);

})();

// Thanks for reading this source code. We love JavaScript.

