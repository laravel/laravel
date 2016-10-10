/*
 JavaScript for Sweety to wrap the standard template around the API.
 */


/**
 * The UI Manager object for setting up the interface.
 * @author Chris Corbyn
 * @constructor
 */
function SweetyUIManager() {
  
  var _this = this;
  
  /** Packages toggled on or off */
  var _pkgs = { };
  
  /** Test cases within packages */
  var _pkgTests = { };
  
  /** An element cache */
  var _cached = { };
  
  /**
   * Initialize the user interface.
   */
  this.initialize = function initialize() {
    _this.showFilterBox();
    _this.loadTestList();
    _this.resetMessageDiv();
    _this.resetTotals();
  }
  
  /**
   * Show or hide an entire package.
   * @param {String} pkg
   * @param {Boolean} onoff
   */
  this.togglePackage = function togglePackage(pkg, onOff) {
    if (typeof _pkgs[pkg] == "undefined") {
      _pkgs[pkg] = true;
    }
    
    //Toggle if not overridden
    _pkgs[pkg] = (typeof onOff == "undefined") ? !_pkgs[pkg] : onOff;
    
    var pkgState = _pkgs[pkg] ? "1" : "0";
    
    var date = new Date();
    date.setTime(date.getTime() + (30 * 24 * 60 * 60 * 1000));
    		
    document.cookie = escape("sweetyPkg" + pkg) + "=" + pkgState +
      "; expires=" + date.toGMTString() +
      "; path=/";
    
    var pkgRegex = new RegExp("^" + pkg + "_[^_]+$");
    for (var testCase in sweetyTestCases) {
      if (testCase.match(pkgRegex)) {
        var testDiv = _getElementById(testCase);
        if (_pkgs[pkg]) {
          if (sweetyTestCases[testCase]) {
            testDiv.style.display = "block";
          }
        } else {
          testDiv.style.display = "none";
        }
      }
    }
    var headerImg;
    if (headerImg = _getElementById("sweety-pkg-img-" + pkg))
    {
      if (_pkgs[pkg]) {
        headerImg.src = "templates/sweety/images/darr.gif";
      } else {
        headerImg.src = "templates/sweety/images/rarr.gif";
      }
    }
  }
  
  /**
   * Enable or disable user input.
   * @param {Boolean} on
   */
  this.allowInteractivity = function allowInteractivity(on) {
    if (!on) {
      _paintFilterDisabled(true);
      _getRunButton().value = "Stop Tests";
      _getRunButton().onclick = function() {
        try {
          SweetyTestRunner.getCurrentInstance().cancelTesting(true);
          _this.allowInteractivity(true);
        } catch (e) { }
      };
      for (var testCase in sweetyTestCases) {
        _getElementById(testCase).onclick = function() {
          return false;
        };
      }
    } else {
      _paintFilterDisabled(false);
      _getRunButton().value = "Run Tests";
      _getRunButton().onclick = function() {
        _this.initialize();
        sweetyRunner.runAll();
      };
      for (var testCase in sweetyTestCases) {
        _getElementById(testCase).onclick = function() {
          _this.initialize();
          sweetyRunner.runTestCase(this.id);
        };
      }
    }
  }
  
  /**
   * Display the filter box.
   */
  this.showFilterBox = function showFilterBox() {
    _getFilter().style.visibility = 'visible';
  }
  
  /**
   * Restore the UI on a new page load or reload.
   */
  this.restore = function restore() {
    for (var testName in sweetyTestCases) {
      var pkgName = _pkgFor(testName);
      if (typeof _pkgTests[pkgName] == "undefined") {
        _pkgTests[pkgName] = { };
      }
      _pkgTests[pkgName][testName] = true;
    }
    this.hideCheckboxes();
    _loadPkgsFromCookie();
    for (var pkg in _pkgs) {
      this.togglePackage(pkg, _pkgs[pkg]);
    }
  }
  
  /**
   * Hide all the checkboxes which are only applicable to the non-JS version.
   */
  this.hideCheckboxes = function hideCheckboxes() {
    var inputs = document.getElementsByTagName("input");
    for (var i = 0, len = inputs.length; i < len; i++) {
      if (inputs.item(i).className == "sweety-check") {
        inputs.item(i).style.display = "none";
      }
    }
    delete inputs;
  }
  
  /**
   * Load the available test case list in the UI.
   */
  this.loadTestList = function loadTestList() {
    var caseBox = _getListContainer();

    //Show or hide any tests
    for (var testCase in sweetyTestCases) {
      var pkgName = _pkgFor(testCase);
      _pkgTests[pkgName][testCase] = sweetyTestCases[testCase];
      
      this.paintTestCaseIdle(testCase);
      
      var pkg = _pkgFor(testCase);
      this.paintPkgIdle(pkg);
      
      var testDiv = _getElementById(testCase);
      
      //Make it look idle
      testDiv.className = "sweety-test sweety-idle";
      
      if (sweetyTestCases[testCase]) {
        if (typeof _pkgs[pkg] == "undefined") {
          testDiv.style.display = "block";
        } else if (_pkgs[pkg]) {
          testDiv.style.display = "block";
        }
      } else {
        testDiv.style.display = "none";
      }
    }
    
    //Show or hide any packages
    for (var pkgName in _pkgTests) {
      var display = false;
      var len = 0;
      for (var testCase in _pkgTests[pkgName]) {
        if (_pkgTests[pkgName][testCase]) {
          display = true;
          //break;
          len++;
        }
      }
      this.showPkgCount(pkgName, len);
      this.showHidePkg(pkgName, display);
    }
  }
  
  /**
   * Shows or hides the headers for the given package.
   * @param {String} pkg
   * @param {Boolean} show
   */
  this.showHidePkg = function showHidePkg(pkg, show) {
    var pkgDiv = _getElementById("sweety-package-" + pkg);
    if (show) {
      pkgDiv.style.display = "block";
    } else {
      pkgDiv.style.display = "none";
    }
  }
  
  this.showPkgCount = function showPkgCount(pkg, n) {
    var countBox = _getElementById("sweety-pkg-count-" + pkg);
    _setContent(countBox, "(" + n + ")");
  }
  
  /**
   * Reset all the aggregate results in the UI.
   */
  this.resetTotals = function resetTotals() {
    _this.paintNumCases(0);
    _this.paintNumRun(0);
    _this.paintNumPasses(0);
    _this.paintNumFails(0);
    _this.paintNumExceptions(0);
    
    _this.paintAllIdle();
  }
  
  /**
   * Paint or unpaint the networking icon to indicate communication with the server.
   * @param {Boolean} on
   */
  this.paintNetworking = function paintNetworking(on) {
    if (on) {
      _getCommIcon().style.display = "block";
    } else {
      _getCommIcon().style.display = "none";
    }
  }
  
  /**
   * Flush the contents of the assertion message area.
   */
  this.resetMessageDiv = function resetMessageDiv() {
    _getMessages().innerHTML = "";
    _getElementById("sweety-smoke-images").innerHTML = "";
  }
  
  /**
   * Marks a given package as running (yellow) in the UI.
   * @param {String} pkg
   */
  this.paintPkgRunning = function paintPkgRunning(pkg) {
    _getElementById("sweety-package-" + pkg).className = "sweety-package-header sweety-running";
  }
  
  /**
   * Marks a given package as idle (grey) in the UI.
   * @param {String} pkg
   */
  this.paintPkgIdle = function paintPkgIdle(pkg) {
    _getElementById("sweety-package-" + pkg).className = "sweety-package-header sweety-pkg-idle";
  }
  
  /**
   * Marks a given package as passed (green) in the UI.
   * @param {String} pkg
   */
  this.paintPkgPassed = function paintPkgPassed(pkg) {
    _getElementById("sweety-package-" + pkg).className = "sweety-package-header sweety-pass";
  }
  
  /**
   * Marks a given package as failed (red) in the UI.
   * @param {String} pkg
   */
  this.paintPkgFailed = function paintPkgFailed(pkg) {
    _getElementById("sweety-package-" + pkg).className = "sweety-package-header sweety-fail";
  }
  
  /**
   * Marks a given test case as running (yellow) in the UI.
   * @param {String} testCase
   */
  this.paintTestCaseRunning = function paintTestCaseRunning(testCase) {
    _getElementById(testCase).className = "sweety-test sweety-running";
  }
  
  /**
   * Marks a given test case as idle (grey) in the UI.
   * @param {String} testCase
   */
  this.paintTestCaseIdle = function paintTestCaseIdle(testCase) {
    _getElementById(testCase).className = "sweety-test sweety-idle";
  }
  
  /**
   * Marks a given test case as failed (red) in the UI.
   * @param {String} testCase
   */
  this.paintTestCaseFailed = function paintTestCaseFailed(testCase) {
    _getElementById(testCase).className = "sweety-test sweety-fail";
  }
  
  /**
   * Marks a given test case as passed (green) in the UI.
   * @param {String} testCase
   */
  this.paintTestCasePassed = function paintTestCasePassed(testCase) {
    _getElementById(testCase).className = "sweety-test sweety-pass";
  }
  
  /**
   * Paints a skipped testcase message to the message area.
   * @param {String} message
   * @param {String} path
   */
  this.paintSkip = function paintSkip(message, path) {
    var skipDiv = document.createElement("div");
    skipDiv.className = "sweety-message";
    
    var skipLabel = _createSkipLabel("Skip");
    skipDiv.appendChild(skipLabel);
    
    var messageSpan = document.createElement("strong");
    _setContent(messageSpan, ": " + message);
    skipDiv.appendChild(messageSpan);
    
    var pathDiv = _createPathDiv(path);
    skipDiv.appendChild(pathDiv);
    
    _getMessages().appendChild(skipDiv);
  }
  
  /**
   * Paints an unexpected exception notice to the message area.
   * @param {String} message
   * @param {String} path
   */
  this.paintException = function paintException(message, path) {
    var exceptionDiv = document.createElement("div");
    exceptionDiv.className = "sweety-message";
    
    var exceptionLabel = _createFailLabel("Exception");
    exceptionDiv.appendChild(exceptionLabel);
    
    var messageSpan = document.createElement("strong");
    _setContent(messageSpan, ": " + message);
    exceptionDiv.appendChild(messageSpan);
    
    var pathDiv = _createPathDiv(path);
    exceptionDiv.appendChild(pathDiv);
    
    _getMessages().appendChild(exceptionDiv);
  }
  
  /**
   * Paints a failed assertion message to the message area.
   * @param {String} message
   * @param {String} path
   */
  this.paintFail = function paintFail(message, path) {
    var failDiv = document.createElement("div");
    failDiv.className = "sweety-message";
    
    var failLabel = _createFailLabel("Fail");
    failDiv.appendChild(failLabel);
    
    var messageSpan = document.createElement("span");
    _setContent(messageSpan, ": " + message);
    failDiv.appendChild(messageSpan);
    
    var pathDiv = _createPathDiv(path);
    failDiv.appendChild(pathDiv);
    
    _getMessages().appendChild(failDiv);
  }
  
  /**
   * Paints dump() output to the message area.
   * @param {String} output
   * @param {String} path
   */
  this.paintOutput = function paintOutput(output, path) {
    var refs;
    if (refs = /^\{image @ (.*?)\}$/.exec(output)) {
      this.paintSmokeImage(refs[1]);
    } else {
      var outputPane = document.createElement("pre");
      outputPane.className = "sweety-raw-output";
      _setContent(outputPane, output);
    
      _getMessages().appendChild(outputPane);
    }
  }
  
  this.paintSmokeImage = function paintSmokeImage(imageSrc) {
    var imagePane = _getElementById("sweety-smoke-images");
    var smokeImg = document.createElement("img");
    smokeImg.title = 'Smoke test image';
    smokeImg.src = imageSrc;
    smokeImg.style.cursor = 'pointer';
    smokeImg.onclick = function() { window.open(imageSrc); };
    imagePane.appendChild(smokeImg);
  }
  
  /**
   * Paints an internal message to the message area.
   * @param {String} message
   * @param {String} path
   */
  this.paintMessage = function paintMessage(message) {
    var messageDiv = document.createElement("div");
    messageDiv.className = "sweety-message sweety-running";
    _setContent(messageDiv, message);
    
    _getMessages().appendChild(messageDiv);
  }
  
  /**
   * Paints the current number of test cases to the summary bar.
   * @param {Number} num
   */
  this.paintNumCases = function paintNumCases(num) {
    _setContent(_getCases(), num);
  }
  
  /**
   * Paints the current number of finished test cases to the summary bar.
   * @param {Number} num
   */
  this.paintNumRun = function paintNumRun(num) {
    _setContent(_getComplete(), num);
  }
  
  /**
   * Paints the current number of passing assertions to the summary bar.
   * @param {Number} num
   */
  this.paintNumPasses = function paintNumPasses(num) {
    _setContent(_getPasses(), num);
  }
  
  /**
   * Paints the current number of failing assertions to the summary bar.
   * @param {Number} num
   */
  this.paintNumFails = function paintNumFails(num) {
    _setContent(_getFails(), num);
  }
  
  /**
   * Paints the current number of exceptions to the summary bar.
   * @param {Number} num
   */
  this.paintNumExceptions = function paintNumExceptions(num) {
    _setContent(_getExceptions(), num);
  }
  
  /**
   * Paints the summary bar (green) as passed.
   */
  this.paintConclusionPassed = function paintConclusionPassed() {
    _getResultsBar().className = "sweety-pass";
  }
  
  /**
   * Paints the summary bar (red) as failed.
   */
  this.paintConclusionFailed = function paintConclusionFailed() {
    _getResultsBar().className = "sweety-fail";
  }
  
  /**
   * Paints the summary bar (yellow) as running.
   */
  this.paintAllRunning = function paintAllRunning() {
    _getResultsBar().className = "sweety-running";
  }
  
  /**
   * Paints the summary bar (grey) as idle.
   */
  this.paintAllIdle = function paintAllIdle() {
    _getResultsBar().className = "sweety-idle";
  }
  
  /**
   * Puts the filter box in searching L&F.
   */
  this.paintSearching = function paintSearching() {
    _getFilter().className = "sweety-text sweety-waiting";
  }
  
  /**
   * Returns the filter box the idle L&F.
   */
  this.paintSearchComplete = function paintSearchComplete() {
    _getFilter().className = "sweety-text";
  }
  
  /**
   * Apply data to a page element.
   * @param {Element} el
   * @param {String} content
   */
  var _setContent = function _setContent(el, content) {
    if (typeof el.textContent != "undefined") {
      el.textContent = content;
    } else {
      el.innerHTML = content;
    }
  }
  
  /**
   * Create a label used at the start of a message to indicate a skipped test case.
   * @param {String} label
   * @returns HTMLSpanElement
   */
  var _createSkipLabel = function _createSkipLabel(label) {
    var skipLabel = document.createElement("span");
    skipLabel.className = "sweety-skip-text";
    _setContent(skipLabel, label);
    return skipLabel;
  }
  
  /**
   * Create a label used at the start of a message to indicate failure.
   * @param {String} label
   * @returns HTMLSpanElement
   */
  var _createFailLabel = function _createFailLabel(label) {
    var failLabel = document.createElement("span");
    failLabel.className = "sweety-fail-text";
    _setContent(failLabel, label);
    return failLabel;
  }
  
  /**
   * Creates the text which shows the complete pathway to a test method.
   * The path includes all groups, the test case and the test method.
   * @param {String} path
   * @returns HTMLDivElement
   */
  var _createPathDiv = function _createPathDiv(path) {
    var pathDiv = document.createElement("div");
    pathDiv.className = "sweety-test-path";
    _setContent(pathDiv, "in " + path);
    return pathDiv;
  }
  
  var _paintFilterDisabled = function _paintFilterDisabled(disabled) {
    if (disabled) {
      _getFilter().disabled = true;
      _getFilter().className = "sweety-text sweety-disabled";
    } else {
      _getFilter().disabled = false;
      _getFilter().className = "sweety-text";
    }
  }
  
  /**
   * A caching wrapper around document.getElementById().
   * @param {String} id
   * @returns Element
   */
  var _getElementById = function _getElementById(elId) {
    if (!_cached[elId]) {
      _cached[elId] = document.getElementById(elId);
    }
    return _cached[elId];
  }
  
  /**
   * Get the icon which shows network activity.
   * @returns Element
   */
  var _getCommIcon = function _getCommIcon() {
    return _getElementById("sweety-communication");
  }
  
  /**
   * Get the container which holds the list of test cases.
   * @returns Element
   */
  var _getListContainer = function _getListContainer() {
    return _getElementById("sweety-testlist-container");
  }
  
  /**
   * Get the container where all assertion messages go.
   * @returns Element
   */
  var _getMessages = function _getMessages() {
    return _getElementById("sweety-messages");
  }
  
  /**
   * Get the element for number of test cases.
   * @returns Element
   */
  var _getCases = function _getCases() {
    return _getElementById("sweety-num-cases");
  }
  
  /**
   * Get the container for number of test cases finished.
   * @returns Element
   */
  var _getComplete = function _getComplete() {
    return _getElementById("sweety-num-run");
  }
  
  /**
   * Get the container for number of exceptions.
   * @returns Element
   */
  var _getExceptions = function _getExceptions() {
    return _getElementById("sweety-num-exceptions");
  }
  
  /**
   * Get the container for number of fails.
   * @returns Element
   */
  var _getFails = function _getFails() {
    return _getElementById("sweety-num-fails");
  }
  
  /**
   * Get the container for number of passes.
   * @returns Element
   */
  var _getPasses = function _getPasses() {
    return _getElementById("sweety-num-passes");
  }
  
  
  /**
   * Get the bar showing aggregate results.
   * @returns Element
   */
  var _getResultsBar = function _getResutsBar() {
    return _getElementById("sweety-results");
  }
  
  /**
   * Get the filter input box.
   * @returns Element
   */
  var _getFilter = function _getFilter() {
    return _getElementById("sweety-filter");
  }
  
  /**
   * Get the button which operates the filter.
   * @returns Element
   */
  var _getRunButton = function _getRunButton() {
    return _getElementById("sweety-run-button");
  }
  
  var _loadPkgsFromCookie = function _loadPkgsFromCookie() {
    for (var testCase in sweetyTestCases) {
      var pkg = _pkgFor(testCase);
      _pkgs[pkg] = false;
    }
    var cookieBits = document.cookie.split(/\s*;\s*/g);
    for (var i in cookieBits) {
      if (cookieBits[i].substring(0, 9) != "sweetyPkg")
      {
        continue;
      }
      var nvp = cookieBits[i].substring(9).split('=');
      _pkgs[unescape(nvp[0])] = (nvp[1] == "0") ? false : true;
      //alert(unescape(nvp[0]) +  " => " + _pkgs[unescape(nvp[0])]);
    }
  }
  
  var _pkgFor = function _pkgFor(testName) {
    return testName.replace(/_?[^_]+$/, "");
  }
  
}

//Create an instance of the UI Manager for usage
var sweetyUI = new SweetyUIManager();


/**
 * A filter to hide/show test cases in the list.
 * @author Chris Corbyn
 * @consructor
 */
function SweetyFilter() {
  
  var _this = this;
  
  /** Asynchronous page timer (so nothing happens whilst typing) */
  var _timer;
  
  /** The sweety-filter element, lazy loaded */
  var _filter = null;
  
  /**
   * Update the display once the search is complete.
   */
  this.repaintUI = function repaintUI() {
    sweetyUI.initialize();
    sweetyUI.paintSearchComplete();
  }
  
  /**
   * Search for matching test cases.
   */
  this.search = function search() {
    sweetyUI.paintSearching();
    
    var query = _getFilterInput().value.toLowerCase();
    var queryBits = query.split(/[^\!a-zA-Z0-9_]+/g);
    
    //Cancel searching if still typing
    try {
      window.clearTimeout(_timer);
    } catch (e) { }
    
    for (var testCase in sweetyTestCases) {
      for (var i in queryBits) {
        var testFor = queryBits[i];
        var isNegated = ("!" == testFor.charAt(0));
        if (isNegated) {
          testFor = testFor.substring(1);
        }
        
        if (!isNegated && 0 > testCase.toLowerCase().indexOf(testFor)) {
          sweetyTestCases[testCase] = false;
          break;
        } else if (isNegated && 0 < testCase.toLowerCase().indexOf(testFor)) {
          sweetyTestCases[testCase] = false;
          break;
        } else {
          sweetyTestCases[testCase] = true;
        }
      }
    }
    
    //Only apply the search in 500ms, since user may be typing
    _timer = window.setTimeout(_this.repaintUI, 500);
  }
  
  /**
   * Get a lazy loaded reference to the input element.
   * @return HTMLInputElement
   */
  var _getFilterInput = function _getFilterInput() {
    if (!_filter) {
      _filter = document.getElementById("sweety-filter");
    }
    return _filter;
  }
  
}

//Create a new instance of the filter
var sweetyFilter = new SweetyFilter();

/**
 * The reporter which gathers aggregate results and displays a summary.
 * @author Chris Corbyn
 * @constructor
 * @param {Boolean} reportPkgs if package status should be reported
 */
function SweetyTemplateAggregateReporter(testCaseList, reportPkgs) {
  
  var _this = this;
  
  /** True if this reporter instance is running now */
  var _started = false;
  
  /** Aggregate totals */
  var _aggregates = { cases : 0, run: 0, passes : 0, fails : 0, exceptions : 0 };
  
  /** Aggregates per-package */
  var _pkgs = { };
  
  /** Currently running package */
  var _currentPkg;
  
  /**
   * Creates a reporter for the given testCase.
   * @param {String} testCase
   * @returns SweetyReporter
   */
  this.getReporterFor = function getReporterFor(testCase) {
    _aggregates.cases++;
    
    if (reportPkgs) {
      var pkg = _getPkgName(testCase);
      sweetyUI.paintPkgRunning(pkg);
      
      _pkgs[pkg].cases++;
      
      if (_currentPkg && _currentPkg != pkg) {
        _updatePkgStatus(_currentPkg);
      }
      
      _currentPkg = pkg;
    }
    
    sweetyUI.paintNumCases(_aggregates.cases);
    
    var reporter = new SweetyTemplateCaseReporter(testCase, _this);
    return reporter;
  }
  
  /**
   * Updates the UI with the new aggregate totals.
   */
  this.notifyEnded = function notifyEnded(testCase) {
    _aggregates.run++;
    
    if (reportPkgs) {
      var pkg = _getPkgName(testCase);
      _pkgs[pkg].run++;
    }
    
    //Update the UI with new totals
    sweetyUI.paintNumRun(_aggregates.run);
    sweetyUI.paintNumPasses(_aggregates.passes);
    sweetyUI.paintNumFails(_aggregates.fails);
    sweetyUI.paintNumExceptions(_aggregates.exceptions);
  }
  
  /**
   * Returns true if this reporter instance is running.
   * @returns Boolean
   */
  this.isStarted = function isStarted() {
    return _started;
  }
  
  /**
   * Start reporting.
   */
  this.start = function start() {
    _started = true;
    
    if (reportPkgs)
    {
      for (var i = 0, len = testCaseList.length; i < len; i++) {
        var testCase = testCaseList[i];
        var pkg = _getPkgName(testCase);
        if (typeof _pkgs[pkg] == "undefined") {
          _pkgs[pkg] = { cases : 0, run : 0, passes : 0, fails : 0, exceptions : 0 };
        }
      }
    }
    
    sweetyUI.allowInteractivity(false);
    sweetyUI.paintNetworking(true);
    sweetyUI.paintAllRunning();
  }
  
  /**
   * Report a skipped test case.
   * @param {String} message
   * @param {String} path
   */
  this.reportSkip = function reportSkip(message, path) {
    sweetyUI.paintSkip(message, path);
  }
  
  /**
   * Report a passing assertion.
   * @param {String} message
   * @param {String} path
   */
  this.reportPass = function reportPass(message, path) {
    _aggregates.passes++;
    
    if (reportPkgs) {
      _pkgs[_currentPkg].passes++;
    }
  }
  
  /**
   * Report a failing assertion.
   * @param {String} message
   * @param {String} path
   */
  this.reportFail = function reportFail(message, path) {
    _aggregates.fails++;
    
    if (reportPkgs) {
      _pkgs[_currentPkg].fails++;
    }
    
    sweetyUI.paintFail(message, path);
  }
  
  /**
   * Report an unexpected exception.
   * @param {String} message
   * @param {String} path
   */
  this.reportException = function reportException(message, path) {
    _aggregates.exceptions++;
    
    if (reportPkgs) {
      _pkgs[_currentPkg].exceptions++;
    }
    
    sweetyUI.paintException(message, path);
  }
  
  /**
   * Handle test case output from something like a dump().
   * @param {String} output
   * @param {String} path
   */
  this.reportOutput = function reportOutput(output, path) {
    sweetyUI.paintOutput(output, path);
  }
  
  /**
   * End reporting.
   * This method is used to come to a conclusion about the test results in the UI.
   */
  this.finish = function finish() {
    _started = false;
    
    if (reportPkgs) {
      _updatePkgStatus(_currentPkg);
    }
    
    sweetyUI.allowInteractivity(true);
    
    sweetyUI.paintNetworking(false);
    
    if ((!_aggregates.fails && !_aggregates.exceptions)
        && (_aggregates.cases == _aggregates.run)) {
      sweetyUI.paintConclusionPassed();
    } else {
      sweetyUI.paintConclusionFailed();
    }
    
    var incompleteCount = _aggregates.cases - _aggregates.run;
    
    //Check if all tests actually got fully parsed (i.e. finished)
    if (0 < incompleteCount) {
      sweetyUI.paintMessage(
        incompleteCount + " test case(s) did not complete." +
        " This may be because invalid XML was output during the test run" +
        " and/or because an error occured." +
        " Incomplete test cases are shown in yellow.  Click the HTML link " +
        "next to the test for more detail.");
    }
  }
  
  var _getPkgName = function _getPkgName(testCase) {
    return testCase.replace(/_?[^_]+$/, "");
  }
  
  var _updatePkgStatus = function _updatePkgStatus(pkg) {
    if ((!_pkgs[pkg].fails && !_pkgs[pkg].exceptions)
        && (_pkgs[pkg].cases == _pkgs[pkg].run)) {
      sweetyUI.paintPkgPassed(pkg);
    } else if (_pkgs[pkg].cases == _pkgs[pkg].run) {
      sweetyUI.paintPkgFailed(pkg);
    }
  }
  
}
SweetyTemplateAggregateReporter.prototype = new SweetyReporter();

/**
 * The reporter class per-test case.
 * @author Chris Corbyn
 * @consructor
 */
function SweetyTemplateCaseReporter(testCase, reporter) {
  
  var _this = this;
  
  /** Aggregate totals */
  var _aggregates = { passes : 0, fails : 0, exceptions : 0 };
  
  /** The DIV element showing this test case */
  var _testCaseDiv = document.getElementById(testCase);
  
  /** True only if this reporter is running */
  var _started = false;
  
  /**
   * Stubbed only to return itself.
   * @returns SweetyReporter
   */
  this.getReporterFor = function getReporterFor(testCase) {
    return _this;
  }
  
  /**
   * Returns true when the reporter is started.
   * @returns Boolean
   */
  this.isStarted = function isStarted() {
    return _started;
  }
  
  /**
   * Start reporting.
   */
  this.start = function start() {
    _started = true;
    sweetyUI.paintTestCaseRunning(testCase);
  }
  
  /**
   * Report a skipped test case.
   * @param {String} message
   * @param {String} path
   */
  this.reportSkip = function reportSkip(message, path) {
    reporter.reportSkip(message, path);
  }
  
  /**
   * Report a passing assertion.
   * @param {String} message
   * @param {String} path
   */
  this.reportPass = function reportPass(message, path) {
    _aggregates.passes++;
    reporter.reportPass(message, path);
  }
  
  /**
   * Report a failing assertion.
   * @param {String} message
   * @param {String} path
   */
  this.reportFail = function reportFail(message, path) {
    _aggregates.fails++;
    reporter.reportFail(message, path);
  }
  
  /**
   * Report an unexpected exception.
   * @param {String} message
   * @param {String} path
   */
  this.reportException = function reportException(message, path) {
    _aggregates.exceptions++;
    reporter.reportException(message, path);
  }
  
  /**
   * Handle output from a test case in the form of something like a dump().
   * @param {String} output
   * @param {string} path
   */
  this.reportOutput = function reportOutput(output, path) {
    reporter.reportOutput(output, path);
  }
  
  /**
   * End reporting.
   */
  this.finish = function finish() {
    _started = false;
    
    if (!_aggregates.fails && !_aggregates.exceptions) {
      sweetyUI.paintTestCasePassed(testCase);
    } else {
      sweetyUI.paintTestCaseFailed(testCase);
    }
    
    reporter.notifyEnded(testCase);
  }
  
}
SweetyTemplateCaseReporter.prototype = new SweetyReporter();

/**
 * Wraps the invokation of SweetyTestRunner.
 * @author Chris Corbyn
 * @constructor
 */
function SweetyTestWrapper() {
  
  var _this = this;
  
  /**
   * Run a single test case.
   * @param {String} testClass
   */
  this.runTestCase = function runTestCase(testClass) {
    var testCaseList = new Array();
    testCaseList.push(testClass);
    
    var reporter = new SweetyTemplateAggregateReporter(testCaseList);
    
    var runner = new SweetyTestRunner();
    runner.runTests(testCaseList, reporter);
  }
  
  /**
   * Run all selected test cases.
   */
  this.runAll = function runAll(pkg) {
    var pkgRegex;
    if (pkg) {
      pkgRegex = new RegExp("^" + pkg + "_[^_]+$");
    }
    
    var testCaseList = new Array();
    
    for (var testCase in sweetyTestCases) {
      if (!sweetyTestCases[testCase] || (pkg && !testCase.match(pkgRegex))) {
        continue;
      }
      testCaseList.push(testCase);
    }
    
    var reporter = new SweetyTemplateAggregateReporter(testCaseList, true);
    
    var runner = new SweetyTestRunner();
    runner.runTests(testCaseList, reporter);
  }
  
}

//Create an instance of the test runner for usage
var sweetyRunner = new SweetyTestWrapper();

if (typeof document.onreadystatechange != "undefined") { //IE 6/7
  document.onreadystatechange = function() {
    if (document.readyState == "complete") {
      sweetyUI.restore(); sweetyUI.initialize();
    }
  };
} else { //Fallback
  window.onload = function() {
    sweetyUI.restore(); sweetyUI.initialize();
  };
  
  try { //FF
    document.addEventListener("DOMContentLoaded", window.onload, false);
  } catch (e) {
  }
}
