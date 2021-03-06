<?php
    //make db conn
    require_once 'DB/pdo.php';   

    //fetch the user profile details
    $ustmt = $pdo->prepare("SELECT * FROM user where id = :idvar");
    $ustmt->execute(array(":idvar"=> $_SESSION['userid']));
    $urow = $ustmt->fetch(PDO::FETCH_ASSOC);
?>  

   <div class="container custom2 fadeIn a1">
        <div class="row row-content">
            <div class="col-10 offset-1">
                
                <div class="row">
                    
                    <div class="col-12 col-md-4 text-lg-right">   
                        <img src="<?= $urow['img'] ?>" width="250" height="250" class="img-fluid img-responsive" alt="userprofile">    
                    </div>
                    <div class="col-12 col-md-8">
                        <strong class="cursive-font"><?= $urow['usern'] ?></strong><br>
                        <p>
                            <?= $urow['about'] ?>
                        </p><br>
                        <div class="text-hover">
                            <a href="http://www.linkedin.com/in/awanikendurkar" target="_blank"><i class="fa fa-lg fa-linkedin fa1" aria-hidden="true"></i></a>
                            <a href="http://www.instagram.com/idiosyncrasies.exe" target="_blank"><i class="fa fa-lg fa-instagram fa1"></i></a>
                            <a href="https://anecdotesbyawani.wordpress.com/" target="_blank"><i class="fa fa-lg fa-wordpress fa1"></i></a>
                            <a href="https://github.com/awanikendurkar" target="_blank"><i class="fa fa-lg fa-github fa1"></i></a>
                        </div>
                    </div>  
                </div>
            </div>
        </div>
    </div>

   <?php
    require_once 'DB/pdo.php';


    if(!$_REQUEST || isset($_REQUEST['page'])){

        $res_per_page = 9;
        $page = 1;
        if (isset($_GET['page'])) {
            $page = htmlspecialchars($_GET['page']);
         } 
        $start = ($page-1) * $res_per_page;
        $query = 'SELECT * FROM `posts` WHERE `post_status` = 1 AND post_author_id = '. $_SESSION['userid'] .'  ORDER BY `post_id` DESC LIMIT ' . $res_per_page . ' OFFSET '. $start ; 
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        // $res = $stmt->fetch(PDO::FETCH_ASSOC);
        $row_arr = array();
        $count = 0;
        echo "<div class='row fadeIn a1 mt-5'>" . "<div class='col-10 offset-1 col-md-8 offset-md-2'>". "<div class='card-columns'>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // $count += 1;
            // array_push($row_arr, $row);
            echo "<div class='card'>
                        <a href='blogpagev2.php?postid=",$row['post_id'],"' class='nodecorat'>
                            <img class='card-img-top' src='",$row['post_img'],"' alt=''>
                            <div class='card-body'>
                                <h5 class='card-title'>",ucwords($row['post_title']),"</h5>
                                <p class='card-text'>",html_entity_decode(trim($row['post_incerpt'])),"</p>
                        </a>
                            <footer class='blockquote-footer text-right mb-1 mr-1'>
                                On ",date("d F y", strtotime($row['post_date']))," by <a href='blogpagev2.php?prof_id=",$row['post_author_id'],"' class='proflinks'><cite title='Source Title'>",$row['post_author'],"</cite></a>
                            </footer>
                        </div>
                    </div>";
            // print_r($row);
        }
        echo "</div></div></div>";


        $stmt = $pdo->query("SELECT COUNT(*) FROM posts WHERE post_status = 1 AND post_author_id =" .$_SESSION['userid'] );
        // $stmt->execute();
        // $rowvar = $stmt->fetch(PDO::FETCH_ASSOC);
        $rowcount = $stmt->fetchColumn();
        // print_r(array_keys($row_arr[0]));


        $numpages = ceil($rowcount / 9);
        $current = 1;
        if (isset($_GET['page'])) {
            $current = htmlspecialchars(htmlentities($_GET['page']));
        }
        $minpage = $current - 2;
        $maxpage = $current + 2;

        if ($minpage < 1) {
            $maxpage += abs($minpage);
            $minpage = 1;

        }
        if ($maxpage > $numpages) {
            $maxpage = $numpages;
        }

    
        for ($num= $minpage; $num <= $maxpage; $num++) { 
            $printvar = $num;
            
            if ($num == $minpage && $minpage!=1) {
                $prev = $minpage-1;
                echo "
                <li class='page-item'>
                    <a class='page-link' href='blogpagev2.php?page={$prev}' aria-label='Previous'>
                        <span aria-hidden='true'>&laquo;</span>
                        <span class='sr-only'>Previous</span>
                    </a>
                </li>
                ";
                // continue;
            }

            $active = ($current == $num) ? "active": "";
            echo "
                <li class='page-item'><a class='page-link {$active}' href='blogpagev2.php?page={$num}'>{$num}</a></li>
            ";

            if ($num == $maxpage && $maxpage != $numpages) {
                $nex = $maxpage + 1;
                echo "
                <li class='page-item'>
                    <a class='page-link' href='blogpagev2.php?page={$nex}' aria-label='Next'>
                        <span aria-hidden='true'>&raquo;</span>
                        <span class='sr-only'>Next</span>
                    </a>
                </li>
                ";
                // continue;
            }

        } //end of for loop
        echo "</ul></nav> "; //end of pagination nav tag
                
    } //if at line 5 ends. to show blogs according to page num

    //to show blog
    if (isset($_GET['postid'])) {
        // echo '<h1>' . $_GET['postid'] . '</h1>' ;
        $stmt = $pdo->prepare('SELECT * FROM posts WHERE post_id = :postidvar');
        $stmt->execute(array(":postidvar"=>$_GET['postid']));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        // echo "<style>
        //      img{
     //             max-width: 100%;
     //             height: auto;
     //             }
  //        </style>    
        // ";
        echo "<div class = 'container'><h1 class='col-12 text-center mt-4' id='blogtitle'>" . ucwords($row['post_title']). "</h1>" . "<div class='col-12 mr-2 proflinks'>- " . date("d F y", strtotime($row['post_date']))." by <a href='blogpagev2.php?prof_id=" . $row["post_author_id"] ."'>" . $row['post_author'].
            "</a></div><br><br>" .

            htmlspecialchars_decode($row['post_content']) . 
            "</div>"; 
    }
    
?>
<!-- jQuery first, then Popper.js, then Bootstrap JS. -->
<script src='https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.js'></script>
<script src='node_modules/jquery/dist/jquery.slim.min.js'></script>
<script src='node_modules/popper.js/dist/umd/popper.min.js'></script>
<script src='node_modules/bootstrap/dist/js/bootstrap.min.js'></script>


