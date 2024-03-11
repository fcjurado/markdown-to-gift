<?php

$lines = file('questions.md', FILE_SKIP_EMPTY_LINES|FILE_IGNORE_NEW_LINES);
$count = 0;
$question = '';
$file = fopen("questions-gift.txt","w+");
$responses = [];
$numOk = 0;

foreach ($lines as $line) {
    $count += 1;
    if (strpos($line, '###') !== FALSE) {
        // Question start. Write the the previous
        if ($question != '') {
            writeQuestion($file, $question, $responses, $numOk);
        }
        $question = $line . '{';
        $responses = [];
        $numOk = 0;
    } elseif (strpos($line, '- [') !== FALSE) {
        // Add response
        $responses[] = $line;
        if (strpos($line, '- [x] ') !== FALSE) {
            $numOk++;
        }
    }
}

// The last question
writeQuestion($file, $question, $responses, $numOk);
fclose($file);

function writeQuestion($file, $question, $responses, $numOk) {
    if (count($responses) == 0) {
        return;
    }
    $question = str_replace('###', '::', $question);
    fwrite($file, $question . PHP_EOL);
    $percent = percent($numOk);

    foreach ($responses as $response) {
        if ($numOk > 1) {
            $line = str_replace('- [ ] ', '~%-100%', $response);
            //$line = str_replace('~%', '~%-', $line);
            //if (strpos($line, '- [x] ') !== FALSE) {
            $line = str_replace('- [x] ', $percent, $line);
            //}
        } else {
            $line = str_replace('- [ ] ', '~', $response);
            if (strpos($line, '- [x] ') !== FALSE) {
                $line = str_replace('- [x] ', '=', $response);
            }
        }
        fwrite($file, "\t" . $line . PHP_EOL);
    }

    fwrite($file, '}' . PHP_EOL . PHP_EOL);
}

function percent($numOk) {
    $percent = 0;
    switch ($numOk) {
        case 1: $percent = '~%100%'; break;
        case 2: $percent = '~%50%'; break;
        case 3: $percent = '~%33.33333%'; break;
        case 4: $percent = '~%25%'; break;
        case 5: $percent = '~%20%'; break;
        case 6: $percent = '~%16.66666%'; break;
    }
    return $percent;
}
