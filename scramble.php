<pre><?php

//config
$characters = $_GET['characters'];
$xoffset = 80;
$yoffset = 330;
$deltax = 115;
$deltay = 115;
$checkxy = array(440, 220);
$words = array();
$debug = false;

//get wordlist 
$wordlist = file_get_contents("wordlist.txt");
$wordlist = explode("\r\n", $wordlist);

//start logic

for($i=0; $i<=3; $i++){
	for($j=0; $j<=3; $j++){
		$grid[$i][$j] = substr($characters, $i*4+$j, 1);
	}
}

foreach ($wordlist as $word){
	//echo "\n\n\n===\n";
	$word = preg_replace("[^A-Za-z]", '', $word);
	$word = strtolower($word);
	//echo "\n";
	find_word($word);
}

//print_r($words);

        echo "#SingleInstance force\n";
        echo "^+s::\n";
	foreach($words as $key => $word){
            echo "; $key\n";
            $word_iteration = array_shift($word);
            if (is_array($word_iteration)){
                foreach ($word_iteration as $wordposition){
                        $x = $xoffset + $wordposition[0] * $deltax;
                        $y = $yoffset + $wordposition[1] * $deltay;
                        echo "MouseMove $x, $y,  2\n";
                        echo "Click\n";
                }
            }
            echo "MouseMove {$checkxy[0]}, {$checkxy[1]},  2 \n";
            echo "Click\n";
        }


function find_word($word){
	global $xoffset, $yoffset, $deltax, $deltay, $checkxy, $grid, $words, $debug;
	for($i=0; $i<strlen($word); $i++){
		$chars_positions[$i] = find_positions($word[$i]);
	}
	echo ($debug)?"\n\n\n\n\n\n\n\n".$word:"";
	$words[$word][0] = array();
	foreach($chars_positions as $key => $char_positions){
		if (!isset($char_positions)){
			return false;
		}

		if (count($char_positions)==1){
			echo ($debug)?"\n--single position... adding 1":"";
			foreach($words[$word] as $iteration => $posarray){
				array_push($words[$word][$iteration], $char_positions[0]);
			}
		} else {
		
			$char_count = count($char_positions);
			$count = count($words[$word]);
			echo ($debug)?"\n--multiple character positions... adding $char_count":"";

			for ($idan=1; $idan<$char_count; $idan++){
                            for($i=0; $i<$count; $i++){
                                array_push($words[$word], $words[$word][$i]);
                                //array_push($words[$word][$iteration], $char_positions[$idan]);
                            }
			}
                        
                            //print_r($words[$word]);
			
                            $c = 1;
                            $s = 0;
			foreach($words[$word] as $iteration => $posarray){
                            array_push($words[$word][$iteration], $char_positions[$s]);
                           
                            
                            if ($c == $count){
				//echo "increasing s";
                                $s++;
				$c=0;
                            }
			    $c++;
			}
		}
	}
	
        //print_r($words[$word]);
	
	foreach ($words[$word] as $key => $iteration){
		//echo "\n=testing iteration $key of {$words[$word]}";
		//loop through each iteration of the word and see if they are touching
		foreach ($iteration as $key_pos => $pos){
                        $nextpos = isset($iteration[$key_pos+1])?$iteration[$key_pos+1]:NULL;
                    
			echo ($debug)?"\n--testing position {$pos[0]}, {$pos[1]} and {$nextpos[0]}, {$nextpos[1]}":"";
			if (!is_touching($pos, $nextpos)){
				unset($words[$word][$key]);
				echo ($debug)?"\n----Iteration $key does not work\n":"";
				break;
			}
		}
	}
	//echo "\n\n\n".$words[$word]."\n";
	//print_r($words[$word][0]);
	//echo "\n";
	//echo "";

}

function find_positions($char){
	global $grid;
	$positions = array();
	foreach ($grid as $y => $gridy){
		foreach ($gridy as $x => $gridxy){
			if ($char == $gridxy){
				array_push($positions, array($x, $y));
			}
		}
	}
	return $positions;
}

function is_touching($pos1, $pos2){
	if (!isset($pos2)){
		return true;
	} else {
		$delx = abs($pos1[0] - $pos2[0]);
		$dely = abs($pos1[1] - $pos2[1]);
		if (($delx == 1 && $dely == 1) || ($delx == 0 && $dely == 1) || ($delx == 1 && $dely == 0)){
			return true;
		} else {
			return false;
		}
	}
}


?>
</pre>
