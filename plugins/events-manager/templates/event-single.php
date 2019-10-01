<?php get_header(); ?>
    <div class="content">
  
    <?php global $EM_Event;
    /* @var $EM_Event EM_Event */
    $months = array(
      '01' => 'January',
      '02' => 'February',
      '03' => 'March',
      '04' => 'April',
      '05' => 'May',
      '06' => 'June',
      '07' => 'July',
      '08' => 'August',
      '09' => 'September',
      '10' => 'October',
      '11' => 'November',
      '12' => 'December',
    );
    $month = substr($EM_Event->start_date, '5', '2');
    $date = substr($EM_Event->start_date, '8', '2');
    $year = substr($EM_Event->start_date, '0', '4');
    ?>
    <div>
      <h1><?php echo $EM_Event->post_title?></h1>
      <p><?php echo $months[$month].' '.$date.', '.$year; ?><span><?php echo substr($EM_Event->start_time, '0', '5').' to '.substr($EM_Event->end_time, '0', '5').' '.$EM_Event->event_timezone ?></span></p>
      <?php 
              $imgUrl = wp_get_attachment_url( get_post_thumbnail_id($events[$i]->post_id));
              if ($imgUrl) {
                ?>
                <img src="<?php echo $imgUrl ?>" alt="">
                <?php
              }
            ?>
            
      <?php  
        $EM_Bookings_Table = new EM_Bookings_Table();
        $bookings = $EM_Bookings_Table->cols_view->bookings->bookings;
        ?>
        <ul>
        <?php 
        if ($bookings) {
          for ($i = 0; $i < count($bookings); $i = $i + 1) {
            ?>
              <li>@<?php echo $bookings[$i]->person->data->user_nicename ?>
              <p><?php echo $bookings[$i]->person->data->display_name ?></p></li>
            <?php
          }
        }
      ?>
      </ul>
    </div>
    </div>
<?php get_footer(); ?>