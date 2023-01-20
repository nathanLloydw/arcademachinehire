<?php
    $displays = reddq_rental_get_settings( get_the_ID(), 'display' );
    $displays = $displays['display'];
?>
<?php if(isset($displays['quantity']) && $displays['quantity']!=='closed'): ?>
<div class="redq-quantity rnb-select-wrapper rnb-component-wrapper">
    <?php
        $labels = reddq_rental_get_settings( get_the_ID(), 'labels', array('quantity') );
        $labels = $labels['labels'];
    ?>
    <h5><?php echo esc_attr($labels['quantity']); ?></h5>
    <input type="number" name="inventory_quantity" class="inventory-qty" min="" max="" value="1">
</div>
<?php endif; ?>