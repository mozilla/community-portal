<div class="events__filter__option">
  <label class="select__label" for="<?php echo $field_name ?>">
  <?php echo $field_label ?>
      </label>
      <select class="select" name="<?php echo $field_name ?>" id="<?php echo $field_name ?>" data-filter="<?php echo $field_name?>">
        <option value="all">All</option>
        <?php
          foreach($options as $option) {
            if ($option === $country || $option === $tag) {
            ?>
              <option value="<?php echo $option ?>" selected><?php echo $option ?></option>
            <?php
            } else {
              ?>
              <option value="<?php echo $option ?>"><?php echo $option ?></option>
              <?php
            }
          }
        ?>
      </select>
      <!-- <svg class="select__icon events__filter__option__icon" width="14" height="12" viewBox="0 0 14 12" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M1.5 3.5L7 9L12.5 3.5" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg> -->
</div>