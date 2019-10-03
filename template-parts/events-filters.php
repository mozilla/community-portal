<?php
  global $wpdb;
  $countries = em_get_countries();
  $em_countries = $wpdb->get_results("SELECT DISTINCT location_country FROM ".EM_LOCATIONS_TABLE." WHERE location_country IS NOT NULL AND location_country != '' AND location_status=1 ORDER BY location_country ASC", ARRAY_N);
  $ddm_countries = array();
  foreach($em_countries as $em_country) {
    $ddm_countries[$em_country[0]] = $countries[$em_country[0]];
  }
  asort($ddm_countries);
  foreach($ddm_countries as $country_code => $country_name);
  $categories = EM_Categories::get();
  foreach($categories as $category) {
    $categories[$category->id] = $category->name;
  }

?>
<div class="col-md-12 events__filter">
  <p class="events__filter__title">Filter By:</p>
  <form action="" class="events__filter__form">
    <?php
      $field_name = "Country";
      $field_label = "Location";
      $options = $ddm_countries;
      include(locate_template('template-parts/options.php', false, false));    

      $field_name =  "Tag";
      $field_label = "Tag";
      $options = $categories;
      include(locate_template('template-parts/options.php', false, false));    

    ?>
  </form>
</div>