<div class="events__filter__option">
    <label class="select__label" for="<?php echo $field_name ?>">
        <?php echo $field_label ?>
    </label>
    <select class="select" name="<?php echo $field_name ?>" id="<?php echo $field_name ?>" data-filter="<?php echo $field_name?>">
        <option value="all">All</option>
        <?php foreach($options as $option): ?>
            <?php if ($option === $country || $option === $tag): ?>
                <option value="<?php echo $option ?>" selected><?php echo $option ?></option>
            <?php else: ?>
                <option value="<?php echo $option ?>"><?php echo $option ?></option>
            <?php endif; ?>
        <?php endforeach; ?>
    </select>
</div>