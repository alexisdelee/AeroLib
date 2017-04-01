<?php
  for($day = 0; $day < 36; $day++) {
    echo "<label class=\"" . ($day <= 4 ? "day invalid" : "day") . "\" data-day=\"" . $day . "\">";

    echo "<input class=\"appointment\" data-day=\"" . ($day - 4) . "\" placeholder=\"What would you like to do?\" required=\"true\" type=\"text\">";
    echo "<span>" . ($day - 4) . "</span>";

    echo "<em></em>";
    echo "</label>";
  }

  echo "<div class=\"clearfix\"></div>";
?>