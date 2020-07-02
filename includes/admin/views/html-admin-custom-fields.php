<?php
if (! defined('ABSPATH')) {
    exit;
}
?>


<div class="woorule-custom-fields">
    <h2>Custom Fields</h2>

    <div class="empty">
        <li class="field-row">
            Meta Attribute: <input class="text attribute" type="text" name="attribute" placeholder="Attribute">
            <div class="source">
                <label for="source">Source:</label>
                <select name="source">
                <option value="order">Order</option>
                <option value="user">User</option>
                </select>
            </div>
            <a href="#" class="remove">Remove</a>
        </li>
    </div>

    <div class="fields"></div>

    <a class="btn button show-all-metas" href="#">Show all availiable meta fields</a>


    <div class="metas">
        <div class="order">
            <h3>Order meta fields</h3>
            <ul>
            <?php foreach(WC_Admin_Settings_Rulemailer::render_order_metas() as $meta => $value): ?>
                <li type="order"><?php echo $meta;?><span>Add</span></li>
            <?php endforeach; ?>
            </ul>
        </div>

        <div class="user">
            <h3>User meta fields</h3>
            <ul>
            <?php foreach(WC_Admin_Settings_Rulemailer::render_user_metas() as $meta => $value): ?>
                <li type="user"><?php echo $meta;?><span>Add</span></li>
            <?php endforeach; ?>
            </ul>
        </div>
    </div>

</div>