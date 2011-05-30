This is the default view.

<h1>Lib1</h1>
<?php g1(); ?>
<h1>Lib2</h1>
<?php g2(); ?>

<?php echo $b; ?>

<h1><?php $this->outputFragment('testfragment', array('a' => 'Fragment variable')); ?></h1>

<a href="<?php echo $this->url('edit/user', array(1,'Jêrome')); ?>">dummy link</a>

<br>

<pre><code>
$_SERVER:
<?php echo $c; ?>
</code></pre>

<pre><code>
$OFF:
<?php var_export($this); ?>
</code></pre>

