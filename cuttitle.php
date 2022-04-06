<?php
// tách chuối thành cụm từ
function split_words_each_phrases($string){
    $origin = $string;
    $string = preg_replace('/\s+/', ' ', trim($string));
    $words = explode(" ", $string);
     ///////////////////////////////
    // Tách chuỗi ra bốn từ mỗi cụm
    ////////////////////////////////
    $four1 = array();
	$four2 = array();
	$four3 = array();
	$four4 = array();
    $j = 0;
    for($i = 0; $i<count($words); $i+=4){
        $four1[$j] = $words[$i]." ". $words[$i+1]." ".$words[$i+2]." ".$words[$i+3];
        $j++;
      
	   }
	echo "four1:";
	var_dump($four1);
	echo "<br>";
    $j = 0;
	for($i = 1; $i<count($words); $i+=4){
        $four2[$j] = $words[$i]." ". $words[$i+1]." ".$words[$i+2]." ".$words[$i+3];
        $j++;
      
   		}
			echo "four2:";
				var_dump($four2);
	echo "<br>";
    $j = 0;
	for($i = 2; $i<count($words); $i+=4){
        $four3[$j] = $words[$i]." ". $words[$i+1]." ".$words[$i+2]." ".$words[$i+3];
        $j++;
      
   		}
			echo "four3:";
				var_dump($four3);
	echo "<br>";
    $j = 0;
	for($i = 3; $i<count($words); $i+=4){
        $four4[$j] = $words[$i]." ". $words[$i+1]." ".$words[$i+2]." ".$words[$i+3];
        $j++;
      
   		}
			echo "four4:";
				var_dump($four4);
	echo "<br>";
    ///////////////////////////////
    // Tách chuỗi ra ba từ mỗi cụm
    //////////////////////////////
    $three1 = array();
	$three2 = array();
	$three3 = array();
    $j = 0;
    for($i = 0; $i<count($words); $i+=3){
        $three1[$j] = $words[$i]." ". $words[$i+1]." ".$words[$i+2];
        $j++;
      
	   }
	echo "three1:";
	var_dump($three1);
	echo "<br>";
    $j = 0;
	for($i = 1; $i<count($words); $i+=3){
        $three2[$j] = $words[$i]." ". $words[$i+1]." ".$words[$i+2];
        $j++;
      
   		}
			echo "three2:";
				var_dump($three2);
	echo "<br>";
    $j = 0;
	for($i = 2; $i<count($words); $i+=3){
        $three3[$j] = $words[$i]." ". $words[$i+1]." ".$words[$i+2];
        $j++;
      
   		}
			echo "three3:";
				var_dump($three3);
	echo "<br>";
    ///////////////////////////////
    // Tách chuỗi ra hai từ mỗi cụm
    ///////////////////////////////
    $two1 = array();
	$two2 = array();
    $j = 0;
    for($i = 0; $i<count($words); $i+=2){
        $two1[$j] = $words[$i]." ". $words[$i+1];
        $j++;
      
	   }
	echo "two1:";
	var_dump($two1);
	echo "<br>";
    $j = 0;
	for($i = 1; $i<count($words); $i+=2){
        $two2[$j] = $words[$i]." ". $words[$i+1];
        $j++;
      
   		}
			echo "two2:";
				var_dump($two2);
	echo "<br>";

}




$test ="iGa Club – Giới thiệu cổng game đổi thưởng chơi cực vui thắng lớn cực hot năm 2022";
split_words_each_phrases($test);

echo "///////////////////////////////////"."<br>";