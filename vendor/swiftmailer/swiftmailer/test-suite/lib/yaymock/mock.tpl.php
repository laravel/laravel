<?php

/*
 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.
 
 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>.
 
 */
 
?><?php ob_start(); ?>

class <?php echo $className; ?>
 <?php if ($extends) echo 'extends ', $extends; ?>
 implements
 <?php foreach ($interfaces as $interface) echo $interface, ', '; ?>
 Yay_MockObject
{
  
 private $_yayInvocationHandler;
 
 public function __construct(Yay_InvocationHandler $invocationHandler)
 {
  $this->_yayInvocationHandler = $invocationHandler;
 }
  
 <?php foreach ($methods as $method): ?>
 <?php echo $method['access']; ?> <?php echo $method['modifiers']; ?> function <?php
 if ($method['returnReference']) echo '&'; ?><?php
 echo $method['name']; ?>(<?php
  foreach ($method['parameters'] as $i => $param): ?>
  <?php if ($i > 0) echo ','; ?>
  <?php if ($param['hint']) echo $param['hint']; ?>
  <?php if ($param['byReference']) echo '&'; ?>$arg_<?php echo $i; ?>
  <?php if ($param['optional']) echo '= null'; ?>
 <?php endforeach; ?>)
 {
  $value = null;
  if (isset($this->_yayInvocationHandler))
  {
   $args = array();
   for ($i = 0; $i < func_num_args(); ++$i)
   {
     $argName = 'arg_' . $i;
     $args[] =& ${$argName};
   }
   $invocation = new Yay_SimpleInvocation($this, __FUNCTION__, $args);
   $value =& $this->_yayInvocationHandler->handleInvocation($invocation);
  }
  return $value;
 }
 <?php endforeach; ?>
 
 public function __clone()
 {
  $this->_yayInvocationHandler = null;
 }

}

<?php return ob_get_clean(); ?>