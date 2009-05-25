<?php /* Smarty version 2.6.0, created on 2009-05-25 14:09:33
         compiled from const.tpl */ ?>
<?php require_once(SMARTY_DIR . 'core' . DIRECTORY_SEPARATOR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'cycle', 'const.tpl', 3, false),array('modifier', 'replace', 'const.tpl', 9, false),)), $this); ?>
<?php if (isset($this->_sections['consts'])) unset($this->_sections['consts']);
$this->_sections['consts']['name'] = 'consts';
$this->_sections['consts']['loop'] = is_array($_loop=$this->_tpl_vars['consts']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['consts']['show'] = true;
$this->_sections['consts']['max'] = $this->_sections['consts']['loop'];
$this->_sections['consts']['step'] = 1;
$this->_sections['consts']['start'] = $this->_sections['consts']['step'] > 0 ? 0 : $this->_sections['consts']['loop']-1;
if ($this->_sections['consts']['show']) {
    $this->_sections['consts']['total'] = $this->_sections['consts']['loop'];
    if ($this->_sections['consts']['total'] == 0)
        $this->_sections['consts']['show'] = false;
} else
    $this->_sections['consts']['total'] = 0;
if ($this->_sections['consts']['show']):

            for ($this->_sections['consts']['index'] = $this->_sections['consts']['start'], $this->_sections['consts']['iteration'] = 1;
                 $this->_sections['consts']['iteration'] <= $this->_sections['consts']['total'];
                 $this->_sections['consts']['index'] += $this->_sections['consts']['step'], $this->_sections['consts']['iteration']++):
$this->_sections['consts']['rownum'] = $this->_sections['consts']['iteration'];
$this->_sections['consts']['index_prev'] = $this->_sections['consts']['index'] - $this->_sections['consts']['step'];
$this->_sections['consts']['index_next'] = $this->_sections['consts']['index'] + $this->_sections['consts']['step'];
$this->_sections['consts']['first']      = ($this->_sections['consts']['iteration'] == 1);
$this->_sections['consts']['last']       = ($this->_sections['consts']['iteration'] == $this->_sections['consts']['total']);
?>
<a name="const<?php echo $this->_tpl_vars['consts'][$this->_sections['consts']['index']]['const_name']; ?>
" id="<?php echo $this->_tpl_vars['consts'][$this->_sections['consts']['index']]['const_name']; ?>
"><!-- --></A>
<div class="<?php echo smarty_function_cycle(array('values' => "evenrow,oddrow"), $this);?>
">

	<div class="const-header">
		<img src="<?php echo $this->_tpl_vars['subdir']; ?>
media/images/<?php if ($this->_tpl_vars['consts'][$this->_sections['consts']['index']]['access'] == 'private'): ?>PrivateVariable<?php else: ?>Variable<?php endif; ?>.png" />
		<span class="const-title">
			<span class="const-name"><?php echo $this->_tpl_vars['consts'][$this->_sections['consts']['index']]['const_name']; ?>
</span>
			 = <span class="const-default"><?php echo ((is_array($_tmp=$this->_tpl_vars['consts'][$this->_sections['consts']['index']]['const_value'])) ? $this->_run_mod_handler('replace', true, $_tmp, "\n", "<br />") : smarty_modifier_replace($_tmp, "\n", "<br />")); ?>
</span>
			(line <span class="line-number"><?php if ($this->_tpl_vars['consts'][$this->_sections['consts']['index']]['slink']):  echo $this->_tpl_vars['consts'][$this->_sections['consts']['index']]['slink'];  else:  echo $this->_tpl_vars['consts'][$this->_sections['consts']['index']]['line_number'];  endif; ?></span>)
		</span>
	</div>

	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "docblock.tpl", 'smarty_include_vars' => array('sdesc' => $this->_tpl_vars['consts'][$this->_sections['consts']['index']]['sdesc'],'desc' => $this->_tpl_vars['consts'][$this->_sections['consts']['index']]['desc'],'tags' => $this->_tpl_vars['consts'][$this->_sections['consts']['index']]['tags'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>	
	
</div>
<?php endfor; endif; ?>
