This is the default view.

<h1>Lib1</h1>
<?php g1(); ?>
<h1>Lib2</h1>
<?php g2(); ?>

<?php echo $b; ?>

<h1><?php $this->outputFragment('testfragment', array('a' => 'Fragment variable')); ?></h1>