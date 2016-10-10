<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title><?php echo $suiteName; ?> - No AJAX</title>
    <link rel="stylesheet" type="text/css" href="templates/sweety/css/main.css" />
  </head>
  <body>
    <div id="sweety-page">
      
      <div id="sweety-testlist">
        
        <div class="sweety-pad">
          
          <form action="?noajax=1" method="post">
          
          <div>
            <input type="text" class="sweety-text" id="sweety-filter" />
            <input type="submit" id="sweety-run-button" value="Run Tests" />
          </div>
          
          <!-- Dynamically generated list of tests goes here -->
          <div id="sweety-testlist-container">
            
            <?php $currentPackage = null; foreach ($testCases as $testCase): ?>
            
            <?php if ($currentPackage != $package = preg_replace('/_?[^_]+$/', '', $testCase)): ?>
              <?php $currentPackage = $package; ?>
              <div id="sweety-package-<?php echo $package; ?>"
                class="sweety-package-header sweety-pkg-idle">
                <img id="sweety-pkg-img-<?php echo $package; ?>" src="templates/sweety/images/darr.gif"
                  alt="Not available" title="Not available" />
                <?php echo preg_replace('/^.*_/', '', $package); ?> Tests
                <span class="sweety-test-package">
                  <?php echo preg_replace('/_?[^_]+$/', '', $package); ?>
                </span>
              </div>
            <?php endif; ?>
            
            <div id="<?php echo $testCase; ?>" class="sweety-test sweety-<?php
            
            if (array_key_exists($testCase, $runTests)) echo $runTests[$testCase]; else echo 'idle';
            
            ?>">
              
              <div class="sweety-testcase">
                
                <a href="?test=<?php echo $testCase; ?>&amp;format=xml"><img
                  src="templates/sweety/images/xmlicon.gif" alt="As XML" title="As XML" /></a>
                <a href="?test=<?php echo $testCase; ?>&amp;format=html"><img
                  src="templates/sweety/images/htmlicon.gif" alt="As HTML" title="As HTML" /></a>
                <a href="?runtests=<?php echo $testCase; ?>&amp;noajax=1"><img
                  src="templates/sweety/images/runicon.gif" alt="Run" title="Run this test" /></a>
                
                <input id="sweety-field-<?php echo $testCase; ?>" class="sweety-check"
                  type="checkbox" name="runtests[]" value="<?php echo $testCase; ?>"
                  <?php if (array_key_exists($testCase, $runTests)): ?>
                  checked="checked"
                  <?php endif; ?> />
                
                <label for="sweety-field-<?php echo $testCase; ?>">
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
          
          <h1><?php echo $suiteName; ?> - No AJAX</h1>
          
          <div id="sweety-results" class="sweety-<?php echo $result; ?>">
            <span id="sweety-num-run"><?php echo $runCount; ?></span>/<span id="sweety-num-cases"><?php echo $caseCount; ?></span>
            test cases complete:
            <strong id="sweety-num-passes"><?php echo $passCount; ?></strong> passes,
            <strong id="sweety-num-fails"><?php echo $failCount; ?></strong> fails and
            <strong id="sweety-num-exceptions"><?php echo $exceptionCount; ?></strong> exceptions.
          </div>
          
          <div id="sweety-messages">
            <?php foreach ($messages as $message)
            {
              switch ($message['type'])
              {
                case 'pass':
                  break;
                case 'skip': ?>
                <div class="sweety-message">
                  <span class="sweety-skip-text">Skip</span>:
                  <strong><?php echo $message['text']; ?></strong>
                  <div class="sweety-test-path">
                    in <?php echo $message['path']; ?>
                  </div>
                </div>
                <?php
                  break;
                case 'fail': ?>
                <div class="sweety-message">
                  <span class="sweety-fail-text">Fail</span>: <?php echo $message['text']; ?>
                  <div class="sweety-test-path">
                    in <?php echo $message['path']; ?>
                  </div>
                </div>
                <?php
                  break;
                case 'exception': ?>
                <div class="sweety-message">
                  <span class="sweety-fail-text">Exception</span>:
                  <strong><?php echo $message['text']; ?></strong>
                  <div class="sweety-test-path">
                    in <?php echo $message['path']; ?>
                  </div>
                </div>
                <?php
                  break;
                case 'output': ?>
                <pre class="sweety-raw-output"><?php echo $message['text']; ?></pre>
                <?php
                  break;
                case 'internal': ?>
                <div class="sweety-internal-message sweety-running">
                  <?php echo $message['text']; ?>
                </div>
                <?php
                  break;
              }
            } ?>
          </div>
          
        </div>
        
      </div>
      
      <div class="sweety-clear"></div>
      
    </div>
  </body>
</html>
