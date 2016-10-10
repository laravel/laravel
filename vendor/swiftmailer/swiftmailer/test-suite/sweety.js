/*
 JavaScript wrapper around REST API in Sweety.
 */

/**
 * A convenience class for using XPath.
 * @author Chris Corbyn
 * @constructor
 */
function SweetyXpath() {
  
  /**
   * Get the first node matching the given expression.
   * @param {String} expr
   * @param {Element} node
   * @returns Element
   */
  this.getFirstNode = function getFirstNode(expr, node) {
    var firstNode = _getRootNode(node).evaluate(
      expr, node, _getNsResolver(node), XPathResult.FIRST_ORDERED_NODE_TYPE, null);
    return firstNode.singleNodeValue;
  },
  
  /**
   * Get all nodes matching the given expression.
   * The returned result is a Node Snapshot.
   * @param {String} expr
   * @param {Element} node
   * @returns Element[]
   */
  this.getNodes = function getNodes(expr, node) {
    var nodes = _getRootNode(node).evaluate(
      expr, node, _getNsResolver(node), XPathResult.ORDERED_NODE_SNAPSHOT_TYPE, null);
    
    var nodeSet = new Array();
    for (var i = 0, len = nodes.snapshotLength; i < len; i++) {
      nodeSet.push(nodes.snapshotItem(i));
    }
    return nodeSet;
  },
  
  /**
   * Get the string value of the node matching the given expression.
   * @param {String} expr
   * @param {Element} node
   * @returns String
   */
  this.getValue = function getValue(expr, node) {
    return _getRootNode(node).evaluate(
      expr, node, _getNsResolver(node), XPathResult.STRING_TYPE, null).stringValue;
  }
  
  /**
   * Get the root node from which run evaluate.
   * @param {Element} node
   * @returns Element
   */
  var _getRootNode = function _getRootNode(node) {
    if (node.ownerDocument && node.ownerDocument.evaluate) {
      return node.ownerDocument;
    } else {
      if (node.evaluate) {
        return node;
      } else {
        return document;
      }
    }
  }
  
  /**
   * Get the NS Resolver used when searching.
   * @param {Element} node
   * @returns Element
   */
  var _getNsResolver = function _getNsResolver(node) {
    if (!document.createNSResolver) {
      return null;
    }
    
    if (node.ownerDocument) {
      return document.createNSResolver(node.ownerDocument.documentElement);
    } else {
      return document.createNSResolver(node.documentElement);
    }
  }
  
}

/**
 * The reporter interface so Sweety can tell the UI what's happening.
 * @author Chris Corbyn
 * @constructor
 */
function SweetyReporter() { //Interface/Base Class
  
  var _this = this;
  
  /**
   * Create a sub-reporter for an individual test case.
   * @param {String} testCaseName
   * @returns SweetyReporter
   */
  this.getReporterFor = function getReporterFor(testCaseName) {
    return _this;
  }
  
  /**
   * Start reporting.
   */
  this.start = function start() {
  }
  
  /**
   * Handle a skipped test case.
   * @param {String} message
   * @param {String} path
   */
  this.reportSkip = function reportSkip(message, path) {
  }
  
  /**
   * Handle a passing assertion.
   * @param {String} message
   * @param {String} path
   */
  this.reportPass = function reportPass(message, path) {
  }
  
  /**
   * Handle a failing assertion.
   * @param {String} message
   * @param {String} path
   */
  this.reportFail = function reportFail(message, path) {
  }
  
  /**
   * Handle an unexpected exception.
   * @param {String} message
   * @param {String} path
   */
  this.reportException = function reportException(message, path) {
  }
  
  /**
   * Handle miscellaneous test output.
   * @param {String} output
   * @param {String} path
   */
  this.reportOutput = function reportOutput(output, path) {
  }
  
  /**
   * Finish reporting.
   */
  this.finish = function finish() {
  }
  
}


/**
 * Represents a single test case being run.
 * @author Chris Corbyn
 * @constructor
 */
function SweetyTestCaseRun(testClass, reporter) {
  
  var _this = this;
  
  /** The XMLHttpRequest used in testing */
  var _req;
  
  /** XPath handler */
  var _xpath = new SweetyXpath();
  
  /** Callback function for completion event */
  this.oncompletion = function oncompletion() {
  }
  
  /**
   * Run this test.
   */
  this.run = function run() {
    if (!reporter.isStarted()) {
      reporter.start();
    }
    _req = _createHttpRequest();
    
    if (!_req) {
      return;
    }
    
    _req.open("GET", "?test=" + testClass + "&format=xml", true);
    _req.onreadystatechange = _handleXml;
    _req.send(null);
  }
  
  /**
   * Get an XmlHttpRequest instance, cross browser compatible.
   * @return Object
   */
  var _createHttpRequest = function _createHttpRequest() {
    var req = false;
    
    if (window.XMLHttpRequest && !(window.ActiveXObject)) {
      try {
        req = new XMLHttpRequest();
      } catch(e) {
        req = false;
      }
    } else if (window.ActiveXObject) {
      try {
        req = new ActiveXObject("Msxml2.XMLHTTP");
      } catch(e) {
        try {
          req = new ActiveXObject("Microsoft.XMLHTTP");
        } catch(e) {
          req = false;
        }
      }
    }
    
    return req;
  }
  
  /**
   * Handle the XML response from the test.
   */
  var _handleXml = function _handleXml() {
    if (_req.readyState == 4) {
      try {
        
        var xml = _req.responseXML;
        var txt = _req.responseText.replace(/[\r\n]+/g, "").
          replace(/^(.+)<\?xml.*$/, "$1");
        
        //Test case was skipped
        var skipElements = xml.getElementsByTagName('skip');
        if (!skipElements || 1 != skipElements.length)
        {
          var runElements = xml.getElementsByTagName('run');
          //Invalid document, an error probably occured
          if (!runElements || 1 != runElements.length) {
            reporter.reportException(
              "Invalid XML response: " +
              _stripTags(txt.replace(/^\s*<\?xml.+<\/(?:name|pass|fail|exception)>/g, "")), testClass);
          } else {
            var everything = runElements.item(0);
            _parseResults(everything, testClass);
            reporter.finish();
          }
        }
        else
        {
          reporter.reportSkip(_textValueOf(skipElements.item(0)), testClass);
          reporter.finish();
        }
      } catch (ex) {
        //Invalid document or an error occurred.
        reporter.reportException(
          "Invalid XML response: " +
          _stripTags(txt.replace(/^\s*<\?xml.+<\/(?:name|pass|fail|exception)>/g, "")), testClass);
      }
      
      //Invoke the callback
      _this.oncompletion();
    }
  }
  
  /**
   * Cross browser method for reading the value of a node in XML.
   * @param {Element} node
   * @returns String
   */
  var _textValueOf = function _textValueOf(node) {
    if (!node.textContent && node.text) {
      return node.text;
    } else {
      return node.textContent;
    }
  }
  
  var _stripTags = function _stripTags(txt) {
    txt = txt.replace(/[\r\n]+/g, "");
    return txt.replace(
      /<\/?(?:a|b|br|p|strong|u|i|em|span|div|ul|ol|li|table|thead|tbody|th|td|tr)\b.*?\/?>/g,
      "");
  }
  
  /**
   * Parse an arbitrary message output.
   * @param {Element} node
   * @param {String} path
   */
  var _parseMessage = function _parseMessage(node, path) {
    reporter.reportOutput(_textValueOf(node), path);
  }
  
  /**
   * Parse formatted text output (such as a dump()).
   * @param {Element} node
   * @param {String} path
   */
  var _parseFormatted = function _parseFormatted(node, path) {
    reporter.reportOutput(_textValueOf(node), path);
  }
  
  /**
   * Parse failing test assertion.
   * @param {Element} node
   * @param {String} path
   */
  var _parseFail = function _parseFail(node, path) {
    reporter.reportFail(_textValueOf(node), path);
  }
  
  /**
   * Parse an Exception.
   * @param {Element} node
   * @param {String} path
   */
  var _parseException = function _parseException(node, path) {
    reporter.reportException(_textValueOf(node), path);
  }
  
  /**
   * Parse passing test assertion.
   * @param {Element} node
   * @param {String} path
   */
  var _parsePass = function _parsePass(node, path) {
    reporter.reportPass(_textValueOf(node), path);
  }
  
  /**
   * Parse an entire test case
   * @param {Element} node
   * @param {String} path
   */
  var _parseTestCase = function _parseTestCase(node, path) {
    var testMethodNodes = _xpath.getNodes("./test", node);
    
    for (var x in testMethodNodes) {
      var testMethodNode = testMethodNodes[x];
      var testMethodName = _xpath.getValue("./name", testMethodNode);
      
      var formattedNodes = _xpath.getNodes("./formatted", testMethodNode);
      for (var i in formattedNodes) {
        var formattedNode = formattedNodes[i];
        _parseFormatted(formattedNode, path + " -> " + testMethodName);
      }
      
      var messageNodes = _xpath.getNodes("./message", testMethodNode);
      for (var i in messageNodes) {
        var messageNode = messageNodes[i];
        _parseMessage(messageNode, path + " -> " + testMethodName);
      }
      
      var failNodes = _xpath.getNodes("./fail", testMethodNode);
      for (var i in failNodes) {
        var failNode = failNodes[i];
        _parseFail(failNode, path + " -> " + testMethodName);
      }
      
      var exceptionNodes = _xpath.getNodes("./exception", testMethodNode);
      for (var i in exceptionNodes) {
        var exceptionNode = exceptionNodes[i];
        _parseException(exceptionNode, path + " -> " + testMethodName);
      }
      
      var passNodes = _xpath.getNodes("./pass", testMethodNode);
      for (var i in passNodes) {
        var passNode = passNodes[i];
        _parsePass(passNode, path + " -> " + testMethodName);
      }
    }
  }
  
  /**
   * Parse an entire grouped or single test case.
   * @param {Element} node
   * @param {String} path
   */
  var _parseResults = function _parseResults(node, path) {
    var groupNodes = _xpath.getNodes("./group", node);
    
    if (0 != groupNodes.length) {
      for (var i in groupNodes) {
        var groupNode = groupNodes[i];
        var groupName = _xpath.getValue("./name", groupNode);
        _parseResults(groupNode, path + " -> " + groupName);
      }
    } else {
      var caseNodes = _xpath.getNodes("./case", node);
      for (var i in caseNodes) {
        var caseNode = caseNodes[i];
        _parseTestCase(caseNode, path);
      }
    }
  }
  
}

/**
 * Runs a list of test cases.
 * @author Chris Corbyn
 * @constructor
 */
function SweetyTestRunner() {
  
  var _this = this;
  
  SweetyTestRunner._currentInstance = _this;
  
  /** True if the test runner has been stopped */
  var _cancelled = false;
  
  /**
   * Invoked to cause the test runner to stop execution at the next available
   * opportunity.  If XML is being parsed in another thread, or an AJAX request
   * is in progress the test runner will wait until the next test.
   * @param {Boolean} cancel
   */
  this.cancelTesting = function cancelTesting(cancel) {
    _cancelled = cancel;
  }
  
  /**
   * Run the given list of test cases.
   * @param {String[]} tests
   * @param {SweetyReporter} reporter
   */
  this.runTests = function runTests(tests, reporter) {
    if (!reporter.isStarted()) {
      reporter.start();
    }
    
    if (_cancelled || !tests || !tests.length) {
      _cancelled = false;
      reporter.finish();
      return;
    }
    
    var testCase = tests.shift();
    
    var caseReporter = reporter.getReporterFor(testCase);
    
    var testRun = new SweetyTestCaseRun(testCase, caseReporter);
    
    //Repeat until no tests remaining in list
    // Ok, I know, I know I'll try to eradicate this lazy use of recursion
    testRun.oncompletion = function() {
      _this.runTests(tests, reporter);
    };
    
    testRun.run();
  }
  
}

/** Active instance */
SweetyTestRunner._currentInstance = null;

/**
 * Fetches the currently running instance of the TestRunner.
 * @returns SweetyTestRunner
 */
SweetyTestRunner.getCurrentInstance = function getCurrentInstance() {
  return this._currentInstance;
}
