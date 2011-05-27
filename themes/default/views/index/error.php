Error number: <?php echo reset($this->router->args); ?><br>
<?php if (reset($this->router->args) == 404): ?>
Could not find that page. Did you know that error pages can't have controllers?
<?php endif; ?>
