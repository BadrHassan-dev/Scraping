<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    <input type="text" name="search" id="search">
    <input type="submit" name="submit" id="submit">
</form>

<?php
include('simple_html_dom.php');


$ch= curl_init();

if( isset($_POST['search']) ) {

    $search_string = $_POST['search'];

    $url= "https://cimanow.cc/?s=$search_string";
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);

    // Get The Titles of Search Results
    preg_match_all('!<li aria-label="title">[^\"\>]+!', $result, $matches);

    if( empty($matches[0]) ){
        echo 'Sorry There Are no Results';
    } else {

        $unique_titles = array_unique($matches[0]);
        $titles = array_values($unique_titles);

        if( count($titles) > 1 ) {

            foreach ($titles as $title) {

                $search_string = strip_tags($title);

                // Get The Titles of Search Results
                $search_string = str_replace(array("\n", "\r"), '', $search_string);

                $ch= curl_init();

                $url= "https://cimanow.cc/?s=$search_string";

                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $result = curl_exec($ch);

                // Get Results Single Pages URL's
                preg_match_all('!<article aria-label="post">\n<a href=(.*?)>!', $result, $matches, PREG_SET_ORDER,0);

                $url = $matches[0][1];

                $search_string3 = str_replace(array("\n", "\r", '"'), '', $url);
                $html = file_get_html($search_string3);

                // Get Data : Number of Episodes - Excerpt - Featured images - Titles
                $episodes= '';
                if( $html->find('li[class="active"]', 0) ) {
                    $episodes =  $html->find('li[class="active"]', 0)->plaintext;
                }
                $excerpt = $html->find('ul[id="details"] li p', 0)->text();
                $imgUrl =  $html->find('main figure img', 0)->src;
                $items_titles = $html->find('main figure img', 0)->getAttribute('alt');

                ?>
                <div class="blocks" style="width: 25%; float: right; margin: 10px; text-align: right;">
                    <img src="<?php echo $imgUrl; ?>" style="max-width: 100%; display: block;">
                    <div class="content">
                        <h1 class="title"><?php echo $items_titles; ?></h1>
                        <p class="episodes"><?php  echo $episodes;  ?></p>
                        <p class="excerpt"><?php echo $excerpt; ?></p>
                    </div>
                </div>
                <?php
            }

            /*
            for( $i=0; $i <= count($titles); $i++ ){

                $ch= curl_init();

                $search_string = strip_tags($titles[$i]);

                $search_string = str_replace(array("\n", "\r"), '', $search_string);

                $url= "https://cimanow.cc/?s=$search_string";

                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $result = curl_exec($ch);
                curl_close($ch);

                preg_match_all('!<article aria-label="post">\n<a href=(.*?)>!', $result, $matches, PREG_SET_ORDER,0);


                if( $matches[0][1] ) {
                    $url = $matches[0][1];
                } else {
                    $url = $matches[$i][1];
                }

                $search_string3 = str_replace(array("\n", "\r", '"'), '', $url);
                $html = file_get_html($search_string3);

                $episodes= '';
                if( $html->find('li[class="active"]', 0) ) {
                    $episodes =  $html->find('li[class="active"]', 0)->plaintext;
                }
                $excerpt = $html->find('ul[id="details"] li p', 0)->text();
                $imgUrl =  $html->find('main figure img', 0)->src;
                $items_titles = $html->find('main figure img', 0)->getAttribute('alt');

                ?>
                <div class="blocks" style="width: 25%; float: right; margin: 10px; text-align: right;">
                    <img src="<?php echo $imgUrl; ?>" style="max-width: 100%; display: block;">
                    <div class="content">
                        <h1 class="title"><?php echo $items_titles; ?></h1>
                        <p class="episodes"><?php  echo $episodes;  ?></p>
                        <p class="excerpt"><?php echo $excerpt; ?></p>
                    </div>
                </div>
                <?php

            }*/

        } else {

            // Get Results Single Pages URL's
            preg_match_all('!<article aria-label="post">\n<a href=(.*?)>!', $result, $matches, PREG_SET_ORDER,0);
            $url = $matches[0][1];
            $search_string3 = str_replace(array("\n", "\r", '"'), '', $url);

            $html = file_get_html($search_string3);

            // Get Data : Number of Episodes - Excerpt - Featured images - Titles
            $episodes =  $html->find('li[class="active"]', 0)->plaintext;
            $excerpt = $html->find('ul[id="details"] li p', 0)->text();
            $imgUrl =  $html->find('main figure img', 0)->src;
            $items_titles = $html->find('main figure img', 0)->getAttribute('alt');

            echo '<div class="blocks" style="width: 25%; float: right; margin: 10px; text-align: right;">
                    <img src="'.$imgUrl.'" style="max-width: 100%; display: block;">
                    <div class="content">
                        <h1 class="title">'.$items_titles.'</h1>
                        <p class="episodes">'.$episodes.'</p>
                        <p class="excerpt">'.$excerpt.'</p>
                    </div>
                    
                </div>';

        }

    }



}