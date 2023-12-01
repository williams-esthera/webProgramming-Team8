<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Game</title>
		<link rel="stylesheet" href="style.css">
	</head>
<?php

session_start();
$currentUsername = isset($_SESSION['username']) ? $_SESSION['username'] : '';
include 'questions.php';

//check if the selected question variable is in the session, and initialize it to 1
//Variable is used to determine whether all questions have been answered
//this will be accessed in displayQuestion
 if (!isset($_SESSION['selectedQuestions'])){
	$_SESSION['selectedQuestions'] = 1;
 
 }

// Initialize 'clicked' session variable if not set
if (!isset($_SESSION['clicked'])) {
    $_SESSION['clicked'] = array();

    // Points for each player
    for ($i = 0; $i < count($_POST['username']); $i++) {
        $username = $_POST['username'][$i];
        $_SESSION['players'][$username] = 0;
        $players[$username] = 0;
    }

    $_SESSION['turn'] = $_POST['username'][0];
    $_SESSION['first-turn'] = $_SESSION['turn'];
}

function updateScoresInFile($username, $points) {
    $profileFile = 'profiles.txt';
    $profiles = file($profileFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    // Search for the current user in the profiles array
    foreach ($profiles as $index => $profile) {
        $userInfo = explode('|', $profile);
        if ($userInfo[1] === $username) {
            $profiles[$index] = $userInfo[0] . '|' . $userInfo[1] . '|' . $points;
            break;
        }
    }

    file_put_contents($profileFile, implode("\n", $profiles));
}

// Update scores based on the points earned during the game for all players
$players = $_SESSION['players'];
foreach ($players as $player => $points) {
    updateScoresInFile($player, $points);
}

// Display scores for all players
echo '<div class="total-points">';
foreach ($players as $player => $points) {
    echo "<h2>$player: $points</h2>";
}

echo '<h2>Current turn: ' . $_SESSION['turn'] . '</h2>';
echo '</div>';

// Check if a question is selected
if (isset($_POST['q'])) {
    $q = $_POST['q'];
    $category = floor($q / 10);
    $value = ($q % 10) - 1;

    // Check if the question has already been selected
    if (!isset($_SESSION['clicked'][$q])) {
        $question = $questions[$category]["questions"][$value];
        $answers = $questions[$category]["answers"][$value];
        $correctIndex = $questions[$category]["correct"][$value];

        $initial_time = strtotime('+15 seconds');

        $currTurn = $_SESSION['turn'];

        foreach ($answers as $index => $answer) {
            if ($index == 0){
                $firstAns = $answer;
            }
            elseif ($index == 1){
                $secAns = $answer;
            }
            elseif ($index == 2){
                $thirdAns = $answer;
            }
            elseif ($index == 3){
                $fourthAns = $answer;
            }
            elseif ($index == 4){
                $fifthAns = $answer;
            }
        }

        header("Location: displayQuestion.php?question=$question&currPoints=$currPoints&currPlayer=$currPlayer&currTurn=$currTurn&initial_time=$initial_time&answers=$answers&firstAns=$firstAns&secAns=$secAns&thirdAns=$thirdAns&fourthAns=$fourthAns&fifthAns=$fifthAns&categories=$categories&category=$category&correctIndex=$correctIndex&q=$q&cat=$categories[$category]");

        // Mark the question as clicked in session
        $_SESSION['clicked'][$q] = true;

		//checkSelectedQuestions2();

    } else {
        echo '<p>This question has already been selected.</p>';
        echo '<a href="index.php">Back to Board</a>';
    }
} else {
    echo '<form method="post" action="index.php">';
    echo '<div class="container">';
    echo '<table>';
    echo '<tr>';
    echo '<th></th>';
    foreach ($categories as $category) {
        echo '<th>' . $category . '</th>';
    }
    echo '</tr>';

    for ($i = 0; $i < count($questions[0]["questions"]); $i++) {
        echo '<tr>';
        echo '<td><strong>' . ($i + 1) . '</strong></td>';

        for ($j = 0; $j < count($categories); $j++) {
            $questionNumber = ($j * 10) + ($i + 1);

            // Check if the question has already been selected
            if (isset($_SESSION['clicked'][$questionNumber])) {
                // Question already selected, display the question text on the button
                echo '<td><button disabled class="selected-button">' . $questions[$j]["questions"][$i] . '</button></td>';
            } else {
                // Question available to select
                echo '<td><button type="submit" name="q" value="' . $questionNumber . '" class="question-button">' . '$' . $questions[$j]["points"][$i] . '</button></td>';
            }
        }

        echo '</tr>';
    }
    echo '</table>';
    echo '</div>';
    echo '</form>';
}
?>
</html>