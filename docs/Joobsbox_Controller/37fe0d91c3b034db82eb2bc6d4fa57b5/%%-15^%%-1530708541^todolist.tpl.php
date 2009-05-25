<?php /* Smarty version 2.6.0, created on 2009-05-25 14:09:36
         compiled from todolist.tpl */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "header.tpl", 'smarty_include_vars' => array('title' => 'Todo List')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div align="center"><h1>Todo List</h1></div>
<?php if (count($_from = (array)$this->_tpl_vars['todos'])):
    foreach ($_from as $this->_tpl_vars['todopackage'] => $this->_tpl_vars['todo']):
?>
<h2><?php echo $this->_tpl_vars['todopackage']; ?>
</h2>
<?php if (isset($this->_sections['todo'])) unset($this->_sections['todo']);
$this->_sections['todo']['name'] = 'todo';
$this->_sections['todo']['loop'] = is_array($_loop=$this->_tpl_vars['todo']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['todo']['show'] = true;
$this->_sections['todo']['max'] = $this->_sections['todo']['loop'];
$this->_sections['todo']['step'] = 1;
$this->_sections['todo']['start'] = $this->_sections['todo']['step'] > 0 ? 0 : $this->_sections['todo']['loop']-1;
if ($this->_sections['todo']['show']) {
    $this->_sections['todo']['total'] = $this->_sections['todo']['loop'];
    if ($this->_sections['todo']['total'] == 0)
        $this->_sections['todo']['show'] = false;
} else
    $this->_sections['todo']['total'] = 0;
if ($this->_sections['todo']['show']):

            for ($this->_sections['todo']['index'] = $this->_sections['todo']['start'], $this->_sections['todo']['iteration'] = 1;
                 $this->_sections['todo']['iteration'] <= $this->_sections['todo']['total'];
                 $this->_sections['todo']['index'] += $this->_sections['todo']['step'], $this->_sections['todo']['iteration']++):
$this->_sections['todo']['rownum'] = $this->_sections['todo']['iteration'];
$this->_sections['todo']['index_prev'] = $this->_sections['todo']['index'] - $this->_sections['todo']['step'];
$this->_sections['todo']['index_next'] = $this->_sections['todo']['index'] + $this->_sections['todo']['step'];
$this->_sections['todo']['first']      = ($this->_sections['todo']['iteration'] == 1);
$this->_sections['todo']['last']       = ($this->_sections['todo']['iteration'] == $this->_sections['todo']['total']);
?>
<h3><?php echo $this->_tpl_vars['todo'][$this->_sections['todo']['index']]['link']; ?>
</h3>
<ul>
<?php if (isset($this->_sections['t'])) unset($this->_sections['t']);
$this->_sections['t']['name'] = 't';
$this->_sections['t']['loop'] = is_array($_loop=$this->_tpl_vars['todo'][$this->_sections['todo']['index']]['todos']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['t']['show'] = true;
$this->_sections['t']['max'] = $this->_sections['t']['loop'];
$this->_sections['t']['step'] = 1;
$this->_sections['t']['start'] = $this->_sections['t']['step'] > 0 ? 0 : $this->_sections['t']['loop']-1;
if ($this->_sections['t']['show']) {
    $this->_sections['t']['total'] = $this->_sections['t']['loop'];
    if ($this->_sections['t']['total'] == 0)
        $this->_sections['t']['show'] = false;
} else
    $this->_sections['t']['total'] = 0;
if ($this->_sections['t']['show']):

            for ($this->_sections['t']['index'] = $this->_sections['t']['start'], $this->_sections['t']['iteration'] = 1;
                 $this->_sections['t']['iteration'] <= $this->_sections['t']['total'];
                 $this->_sections['t']['index'] += $this->_sections['t']['step'], $this->_sections['t']['iteration']++):
$this->_sections['t']['rownum'] = $this->_sections['t']['iteration'];
$this->_sections['t']['index_prev'] = $this->_sections['t']['index'] - $this->_sections['t']['step'];
$this->_sections['t']['index_next'] = $this->_sections['t']['index'] + $this->_sections['t']['step'];
$this->_sections['t']['first']      = ($this->_sections['t']['iteration'] == 1);
$this->_sections['t']['last']       = ($this->_sections['t']['iteration'] == $this->_sections['t']['total']);
?>
    <li><?php echo $this->_tpl_vars['todo'][$this->_sections['todo']['index']]['todos'][$this->_sections['t']['index']]; ?>
</li>
<?php endfor; endif; ?>
</ul>
<?php endfor; endif;  endforeach; unset($_from); endif;  $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>