<div class="events__filter__option">
    <label class="select__label" for="<?php echo $field_name ?>">
        <?php echo $field_label ?>
    </label>
    <select class="select" name="<?php echo $field_name ?>" id="<?php echo $field_name ?>" data-filter="<?php echo $field_name?>">
        <option value="all"><?php print __('All', 'community-portal') ?></option>
        <?php foreach($options as $key  =>  $option): ?>
            <?php if($field_name === 'Initiative'): ?>
				<option value="<?php print $key; ?>" <?php if(isset($_GET['initiative']) && strlen($_GET['initiative']) > 0 && intval($_GET['initiative']) == $key): ?> selected<?php endif; ?>><?php print $option; ?></option>
			<?php elseif ($field_name === 'Language'): ?>
				<option value="<?php print $key; ?>" <?php if(isset($_GET['language']) && strlen($_GET['language']) > 0 && strtolower($_GET['language']) === strtolower($key)): ?> selected<?php endif; ?>><?php print $option; ?></option>

            <?php else: ?>
            <?php if ($option === $country || $option === $tag): ?>
                <option value="<?php echo $option ?>" selected><?php echo $option ?></option>
            <?php else: ?>
                <option value="<?php echo $option ?>"><?php echo $option ?></option>
            <?php endif; ?>
            <?php endif; ?>
        <?php endforeach; ?>
    </select>
</div>