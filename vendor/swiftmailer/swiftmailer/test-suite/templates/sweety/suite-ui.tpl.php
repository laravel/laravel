<?php if (isset($_GET['noajax'])) { include(dirname(__FILE__) . '/suite-ui-noajax.tpl.php'); exit(0); } ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title><?php echo $suiteName; ?></title>
    <link rel="stylesheet" type="text/css" href="templates/sweety/css/main.css" />
    <script type="text/javascript">
    var sweetyTestCases = {};
    <?php foreach ($testCases as $name): ?>
    sweetyTestCases.<?php echo $name; ?> = true;
    <?php endforeach; ?>
    </script>
    <!-- XPath legacy is completely inert in DOM 3 XPath enabled browsers -->
    <script type="text/javascript" src="xpath-legacy.js"></script>
    <script type="text/javascript" src="sweety.js"></script>
    <script type="text/javascript" src="templates/sweety/js/sweety-template.js"></script>
  </head>
  <body>
    <div id="sweety-page">
      
      <div id="sweety-testlist">
        
        <div class="sweety-pad">
          
          <form action="?noajax=1" method="post" onsubmit="return false;">
          
          <div>
            <input type="text" class="sweety-text" id="sweety-filter"
              onkeyup="sweetyFilter.search();" />
            <input type="submit" id="sweety-run-button" value="Run Tests"
              onclick="sweetyUI.initialize(); sweetyRunner.runAll();" />
          </div>
          
          <!-- Dynamically generated list of tests goes here -->
          <div id="sweety-testlist-container">
            
            <?php $currentPackage = null; foreach ($testCases as $testCase): ?>
            
            <?php if ($currentPackage != $package = preg_replace('/_?[^_]+$/', '', $testCase)): ?>
              <?php $currentPackage = $package; ?>
              <div id="sweety-package-<?php echo $package; ?>"
                onmouseover="this.style.cursor='pointer';"
                onclick="sweetyUI.initialize(); sweetyRunner.runAll('<?php echo $package; ?>');"
                class="sweety-package-header sweety-pkg-idle">
                <span class="sweety-pkg-count" id="sweety-pkg-count-<?php echo $package; ?>"></span>
                <img id="sweety-pkg-img-<?php echo $package; ?>" src="templates/sweety/images/darr.gif"
                  alt="Toggle Display" title="Toggle Display"
                  onmouseover="this.style.cursor='default';"
                  onclick="sweetyUI.togglePackage('<?php echo $package; ?>'); event.cancelBubble=true;" />
                <?php echo preg_replace('/^.*_/', '', $package); ?> Tests
                <span class="sweety-test-package">
                  <?php echo preg_replace('/_?[^_]+$/', '', $package); ?>
                </span>
              </div>
            <?php endif; ?>
            
            <div id="<?php echo $testCase; ?>" class="sweety-test sweety-idle"
              onmouseover="this.style.cursor='pointer';"
              onclick="sweetyUI.initialize(); sweetyRunner.runTestCase(this.id);">
              
              <div class="sweety-testcase">
                
                <a href="?test=<?php echo $testCase; ?>&amp;format=xml" onclick="event.cancelBubble=true;"><img
                  src="templates/sweety/images/xmlicon.gif" alt="As XML" title="As XML" /></a>
                <a href="?test=<?php echo $testCase; ?>&amp;format=html" onclick="event.cancelBubble=true;"><img
                  src="templates/sweety/images/htmlicon.gif" alt="As HTML" title="As HTML" /></a>
                <a href="?runtests=<?php echo $testCase; ?>&amp;noajax=1" onclick="return false;"><img
                  src="templates/sweety/images/runicon.gif" alt="Run" title="Run this test" /></a>
                
                <input id="sweety-field-<?php echo $testCase; ?>" class="sweety-check"
                  type="checkbox" name="runtests[]" value="<?php echo $testCase; ?>"
                  <?php if (array_key_exists($testCase, $runTests)): ?>
                  checked="checked"
                  <?php endif; ?> />
                
                <label for="sweety-field-<?php echo $testCase; ?>"
                  onmouseover="this.style.cursor='pointer';" onclick="return false;">
                  <?php echo preg_replace('/^.*_/', '', $testCase); ?>
                </label>
                
                <span class="sweety-test-package">
                  <?php echo $package; ?>
                </span>
              
              </div>
              
            </div>
            
            <?php endforeach; ?>
            
          </div>
          
          </form>
          
        </div>
        
      </div>
      
      <div id="sweety-output">
        
        <div class="sweety-pad">
          
          <div id="sweety-communication">
            <img src="templates/sweety/images/network.gif" alt="Communicating" />
          </div>
          
          <h1><?php echo $suiteName; ?></h1>
          
          <div id="sweety-results" class="sweety-idle">
            <span id="sweety-num-run">0</span>/<span id="sweety-num-cases">0</span>
            test cases complete:
            <strong id="sweety-num-passes">0</strong> passes,
            <strong id="sweety-num-fails">0</strong> fails and
            <strong id="sweety-num-exceptions">0</strong> exceptions.
          </div>
          
          <div id="sweety-smoke-images">
          </div>
          
          <div id="sweety-messages">
          </div>
          
        </div>
        
      </div>
      
      <div class="sweety-clear"></div>
      
    </div>
  </body>
</html>
