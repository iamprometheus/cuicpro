<?php

class TournamentScheduler {
  private $scheduleHours;
  private $hours;
  private $brackets;
  private $officials;
  private $tournament_id;
  private $tournament_days;

  public function __construct($hours, $fields5v5, $fields7v7, $divisions, $officials, $tournament_id, $tournament_days) {
    $this->tournament_id = $tournament_id;
    $this->hours = $hours;
    $this->scheduleHours = $this->createScheduleHours($hours, $fields5v5, $fields7v7);
    $this->brackets = $this->initializeBrackets($divisions);
    $this->officials = $this->createOfficialSchedule($officials);
    $this->tournament_days = $tournament_days;
    //$this->scheduleMatches();
  }

  private function createScheduleHours($hours, $fields5v5, $fields7v7) {
    $scheduleHours = [];
    foreach ($hours as $day => $availableHours) { 
      foreach ($availableHours as $hour) {
        $scheduleHours[$day][$hour] = [
          '1' => $fields5v5,
          '2' => $fields7v7
        ];
      }
    }
    return $scheduleHours;
  }

  private function createOfficialSchedule($officials) {
    foreach ($officials as &$official) { 
      foreach ($official['days'] as $day) {
        $official['scheduleHours'][$day] = $official['hours'];
        $official['occupiedHours'][$day] = [];
      }
    }
    return $officials;
  }

  private function generate_bracket_rounds(int $total_teams) {
	  $total_teams_4 = [1,2];
	  $total_teams_8 = [1,2,4];
	  $total_teams_16 = [1,2,4,8];
	  $total_teams_32 = [1,2,4,8,16];
	  $total_teams_64 = [1,2,4,8,16,32];
	
	  if ($total_teams < 4) {
	    return false;
	  }
	  
	  if ($total_teams < 8) {
	    if ($total_teams== 4) {
	      return [
	        'brackets' => array_reverse($total_teams_4),
	        'bye_teams' => 0
	      ];
	    }
	    $first_round = $total_teams - 4;
	    $brackets = $total_teams_4;
	    $bye_teams = 8 - $total_teams ;
	
	    if ($first_round > 0) {
	      $brackets[] = $first_round;
	    }
	    $reversed_brackets = array_reverse($brackets);
	    $result = [
	      'brackets' => $reversed_brackets,
	      'bye_teams' => $bye_teams
	    ];
	    return $result;
	  }
	  if ($total_teams < 16) {
	    if ($total_teams== 8) {
	      return [
	        'brackets' => array_reverse($total_teams_8),
	        'bye_teams' => 0
	      ];
	    }
	    $first_round = $total_teams - 8;
	    $brackets = $total_teams_8;
	    $bye_teams = 16 - $total_teams;
	
	    if ($first_round > 0) {
	      $brackets[] = $first_round;
	    }
	    $reversed_brackets = array_reverse($brackets);
	    $result = [
	      'brackets' => $reversed_brackets,
	      'bye_teams' => $bye_teams
	    ];
	    return $result;
	  }
	  if ($total_teams < 32) {
	    if ($total_teams== 16) {
	      return [
	        'brackets' => array_reverse($total_teams_16),
	        'bye_teams' => 0
	      ];
	    }
	    $first_round = $total_teams - 16;
	    $brackets = $total_teams_16;
	    $bye_teams = 32 - $total_teams;
	
	    if ($first_round > 0) {
	      $brackets[] = $first_round;
	    }
	    $reversed_brackets = array_reverse($brackets);
	    $result = [
	      'brackets' => $reversed_brackets,
	      'bye_teams' => $bye_teams
	    ];
	    return $result;
	  }
	  if ($total_teams < 64) {
	    if ($total_teams == 32) {
	      return [
	        'brackets' => array_reverse($total_teams_32),
	        'bye_teams' => 0
	      ];
	    }
	    $first_round = $total_teams - 32;
	    $brackets = $total_teams_32;
	    $bye_teams = 64 - $total_teams;
	
	    if ($first_round > 0) {
	      $brackets[] = $first_round;
	    }
	    $reversed_brackets = array_reverse($brackets);
	    $result = [
	      'brackets' => $reversed_brackets,
	      'bye_teams' => $bye_teams
	    ];
	    return $result;
	  }
	  if ($total_teams== 64) {
	    return [
	      'brackets' => array_reverse($total_teams_64),
	      'bye_teams' => 0
	    ];
	  }
	  $first_round = $total_teams - 64;
	  $brackets = $total_teams_64;
	  $bye_teams = 64 - $total_teams;
	
	  if ($first_round > 0) {
	    $brackets[] = $first_round;
	  }
	  $reversed_brackets = array_reverse($brackets);
	  $result = [
	    'brackets' => $reversed_brackets,
	    'bye_teams' => $bye_teams
	  ];
	  return $result;
  }

  private function initializeBrackets($divisions) {
  	
    $total_hours = [];
    $days = $this->hours;
    foreach ($days as $hour) {
      $total_hours[] = count($hour);
    }
    rsort($total_hours);
    $priority_day = [];
    
    foreach ($total_hours as $index => $temp_hour) {
      foreach ($days as $key => $hours) {
        if ($temp_hour == count($hours)) {
          $priority_day[] = $key;
          unset($days[$key]);
          break;
        }
      }
    }
    
    foreach ($divisions as &$division) {
      $matches = [];
      $bracket_round_matches = $this->generate_bracket_rounds(count($division['teams']));

      if(!$bracket_round_matches) continue;
      
      $rounds = $bracket_round_matches['brackets'];
      $bye_teams =$bracket_round_matches['bye_teams'];

      // create matches per round and create an entry for each match
      foreach ($rounds as $matchCount) {
          $match = [];
          for ($i = 0; $i < $matchCount; $i++) {
              $match[] = ["TBD", "TBD"];
          }
          $division['matches_per_round'][] = $matchCount;
          $matches[] = $match;
      }

      // determine matches per day
      $tournament_days = $this->scheduleHours;
      $matches_per_day = [];
      $total_matches = count($rounds);

      for ($i = 0; $i < count($tournament_days); $i++) {
          $matches_per_day[$i] = 0;
      }
      
      $priority_day_index = 0;
      while($total_matches > 0) {
          for ($i = 0; $i < count($tournament_days) && $total_matches > 0; $i++) {
              $matches_per_day[$priority_day[$priority_day_index]] += 1;
              $total_matches--;
              $priority_day_index++;
          }
          $priority_day_index= 0;
      }
      // if last day has no matches, 
      if ($matches_per_day[count($tournament_days)-1] == 0) 
          $matches_per_day = array_reverse($matches_per_day);

      $division['matches_per_day'] = $matches_per_day;
  $division['bye_teams'] = $bye_teams;
      $division['matches'] = $matches;
  }
  return $divisions;
}
  
    public function scheduleMatches() {
    // First, assign randomized teams to round 1, handle BYEs
    foreach ($this->brackets as &$division) {
        $teams = $division['teams'];
        shuffle($teams); // randomize team order
		
        // assign teams to first round matches
        if(!isset($division['matches'])) continue;
        foreach ($division['matches'][0] as &$match) {
            $match = [array_pop($teams), array_pop($teams)];
        }

        // Add BYE teams
        $bye_teams = $division['bye_teams'];
        $team_position = 0;
        $matches_per_round = $division['matches_per_round'][1];
        $jump = $matches_per_round / 2;
        while($bye_teams > 0) {
            for ($i = 0; $i < $jump && $bye_teams > 0; $i++) {
                for ($j = $i; $j < $matches_per_round && $bye_teams > 0; $j+= $jump) {
                    $bye_team = array_pop($teams);
                    $division['matches'][1][$j][$team_position] = $bye_team;
                    $bye_teams--;
                }
            }
            
            $team_position = 1;
        }
    }

    $divisions = &$this->brackets;
    shuffle($divisions);

    // cycle through each day and assign matches to hours
    $bracket_match = [];
    $result = [];
    foreach ($this->scheduleHours as $day => $hours) {
      // cycle through each division
        for ($i = 0; $i < count($divisions); $i++) {
            $division = &$divisions[$i];
            if (!isset($division['matches_per_day'])) continue;
            if ( $division['matches_per_day'][$day] == 0) continue;
            $matches_this_day = &$division['matches_per_day'][$day];
            $mode = $division['division_mode'];

            // cycle through each round
            $need_rest = false;
            if (!isset($bracket_match[$division['id']])) {
              $bracket_match[$division['id']] = 1;
            }
            foreach($division['matches'] as $round => &$matches) {
							if ($matches_this_day == 0) break;
							$scheduleMatches = [];
              
							// cycle through each match
							foreach ($matches as &$match) {
                // get next available hour
								$hour = $this->getNextHour($need_rest, $day,$mode);
								if (!$hour) continue;
                $official = $this->assignOfficial($day, $hour[0], $mode);
								$scheduleMatches[] = [
                  "tournament_id" => $this->tournament_id,
									"division_id" => $division['id'],
									"bracket_id" => $division['bracket_id'],
									"field" => $hour[1],
									"team_id_1" => $match[0],
									"team_id_2" => $match[1],
									"day" => $day,
									"hour" => $hour[0],
									"bracket_match" => $bracket_match[$division['id']],
									"official" => $official,
									"bracket_round" => $round
								];

                $team_1 = $match[0] == "TBD" ? null : intval($match[0]);
                $team_2 = $match[1] == "TBD" ? null : intval($match[1]);

                PendingMatchesDatabase::insert_match(
                  $this->tournament_id, 
                  intval($division['id']), 
                  intval($division['bracket_id']), 
                  $hour[1], 
                  $this->tournament_days[$day], 
                  $hour[0], 
                  $bracket_match[$division['id']], 
                  intval($official),
                  $team_1, 
                  $team_2,
                  intval($round)
                );

								$bracket_match[$division['id']]++;
							}
							$result[$division['id']]['scheduled_matches'][$round]  = $scheduleMatches;
							$division['scheduled_matches'][$round] = $scheduleMatches;
							unset($division['matches'][$round]);
							$matches_this_day--;
              if ($matches_this_day == 1) $need_rest = true;
						}
        }
    }
  }

  private function getNextHour($need_rest, $day, $fieldType) {
    $rest_hours = 2;

    foreach ($this->scheduleHours[$day] as $hour => &$fields) {
      if (!$fields[$fieldType]) continue;
      if ($need_rest) {
        $rest_hours--;
        if ($rest_hours >= 0) continue;
        $need_rest = false;
      }
      
      $field = array_pop($fields[$fieldType]);
      return [$hour, $field];
    }
    return null;
  }

  private function assignOfficial($day, $hour, $mode) {
      foreach ($this->officials as &$official) {
        if (array_key_exists($day, $official['scheduleHours']) 
            && !array_key_exists($hour, $official['occupiedHours'][$day]) 
            && intval($official['scheduleHours'][$day]) > 0 
            && ($official['mode'] === '3' || $official['mode'] === $mode)) {
          $official['scheduleHours'][$day] = intval($official['scheduleHours'][$day]) - 1;
          $official['occupiedHours'][$day][$hour] = true;
          return $official['id'];
        }
      }
      return null;
  }
  
  public function getBrackets() {
      return $this->brackets;
  }
}


// Example usage:
// $hours = [ "0" => ["14:00", "15:00", "16:00", "17:00", "18:00", "19:00", "20:00"], 
//             "1" => [ "08:00", "09:00", "10:00", "11:00", "12:00", "13:00", "14:00", "15:00", "16:00", "17:00", "18:00", "19:00", "20:00", "21:00", "22:00"], 
//             "2" => [ "08:00", "09:00", "10:00", "11:00", "12:00", "13:00", "14:00", "15:00", "16:00", "17:00", "18:00", "19:00", "20:00", "21:00", "22:00"]];
// $fields5v5 = ["Field 1", "Field 2", "Field 3", "Field 4", "Field 5", "Field 6", "Field 7", "Field 8"];
// $fields7v7 = ["Field 9", "Field 10", "Field 11", "Field 12"];

// $hours = [
//     "0"=> [8,9,10,11,12,13,14,15,16,17,18,19,20],
//     "1" => [8,9,10,11,12,13,14,15,16,17,18,19,20],
//     "2" => [8,9,10,11,12,13,14,15,16,17,18,19,20],
// ];

// $fields5v5 = [1,2,3,4,5,6,7,8];
// $fields7v7 = [9,10,11,12];

// $officials = [
  //   [
  //     "id" => 2,
  //     "days" => ["0", "1", "2"],
  //     "hours" => 12,
  //     "mode" => 1,
  //     "tournament_id" => 2
  //   ],
  //   [
  //     "id" => 6,
  //     "days" => ["0", "1", "2"],
  //     "hours" => 12,
  //     "mode" => 3,
  //     "tournament_id" => 2
  //   ],
  //   [
  //     "id" => 7,
  //     "days" => ["0", "1", "2"],
  //     "hours" => 12,
  //     "mode" => 1,
  //     "tournament_id" => 2
  //   ],
  //   [
  //     "id" => 8,
  //     "days" => ["0", "1", "2"],
  //     "hours" => 12,
  //     "mode" => 3,
  //     "tournament_id" => 2
  //   ],
  //   [
  //     "id" => 7,
  //     "days" => ["0", "1", "2"],
  //     "hours" => 12,
  //     "mode" => 1,
  //     "tournament_id" => 2
  //   ],

  //   [
  //     "id" => 8,
  //     "days" => ["0", "1", "2"],
  //     "hours" => 12,
  //     "mode" => 3,
  //     "tournament_id" => 2
  //   ],

  //   [
  //     "id" => 7,
  //     "days" => ["0", "1", "2"],
  //     "hours" => 12,
  //     "mode" => 1,
  //     "tournament_id" => 2
  //   ],

  //   [
  //     "id" => 8,
  //     "days" => ["0", "1"],
  //     "hours" => 12,
  //     "mode" => 3,
  //     "tournament_id" => 2
  //   ],

  //   [
  //     "id" => 7,
  //     "days" => ["0", "1", "2"],
  //     "hours" => 12,
  //     "mode" => 1,
  //     "tournament_id" => 2
  //   ],

  //   [
  //     "id" => 8,
  //     "days" => ["0", "1", "2"],
  //     "hours" => 12,
  //     "mode" => 3,
  //     "tournament_id" => 2
  //   ],

  //   [
  //     "id" => 9,
  //     "days" => ["0", "1", "2"],
  //     "hours" => 6,
  //     "mode" => 3,
  //     "tournament_id" => 2
  //   ],

  //   [
  //     "id" => 10,
  //     "days" => ["0", "1", "2"],
  //     "hours" => 12,
  //     "mode" => 3,
  //     "tournament_id" => 2
  //   ],

  //   [
  //     "id" => 11,
  //     "days" => ["0", "1", "2"],
  //     "hours" => 8,
  //     "mode" => 3,
  //     "tournament_id" => 2
  //   ],

  //   [
  //     "id" => 12,
  //     "days" => ["0", "1", "2"],
  //     "hours" => 12,
  //     "mode" => 3,
  //     "tournament_id" => 2
  //   ],

  //   [
  //     "id" => 13,
  //     "days" => ["0", "1"],
  //     "hours" => 12,
  //     "mode" => 3,
  //     "tournament_id" => 2
  //   ],

  //   [
  //     "id" => 13,
  //     "days" => ["0", "1"],
  //     "hours" => 12,
  //     "mode" => 3,
  //     "tournament_id" => 2
  //   ],

  //   [
  //     "id" => 14,
  //     "days" => ["0", "1"],
  //     "hours" => 4,
  //     "mode" => 1,
  //     "tournament_id" => 2
  //   ],

  //   [
  //     "id" => 15,
  //     "days" => ["0", "1", "2"],
  //     "hours" => 12,
  //     "mode" => 3,
  //     "tournament_id" => 2
  //   ],

  //   [
  //     "id" => 16,
  //     "days" => ["0", "1", "2"],
  //     "hours" => 12,
  //     "mode" => 3,
  //     "tournament_id" => 2
  //   ],

  //   [
  //     "id" => 17,
  //     "days" => ["0", "1", "2"],
  //     "hours" => 12,
  //     "mode" => 3,
  //     "tournament_id" => 2
  //   ],

  //   [
  //     "id" => 18,
  //     "days" => ["0", "1", "2"],
  //     "hours" => 4,
  //     "mode" => 1,
  //     "tournament_id" => 2
  //   ],

  //   [
  //     "id" => 19,
  //     "days" => ["0", "1", "2"],
  //     "hours" => 4,
  //     "mode" => 1,
  //     "tournament_id" => 2
  //   ],

  //   [
  //     "id" => 20,
  //     "days" => ["2"],
  //     "hours" => 5,
  //     "mode" => 2,
  //     "tournament_id" => 2
  //   ],

  //   [
  //     "id" => 21,
  //     "days" => ["0", "1", "2"],
  //     "hours" => 12,
  //     "mode" => 1,
  //     "tournament_id" => 2
  //   ],

  //   [
  //     "id" => 22,
  //     "days" => ["0", "1", "2"],
  //     "hours" => 6,
  //     "mode" => 3,
  //     "tournament_id" => 2
  //   ],

  //   [
  //     "id" => 23,
  //     "days" => ["0", "1", "2"],
  //     "hours" => 12,
  //     "mode" => 1,
  //     "tournament_id" => 2
  //   ],

  //   [
  //     "id" => 24,
  //     "days" => ["0", "1", "2"],
  //     "hours" => 12,
  //     "mode" => 3,
  //     "tournament_id" => 2
  //   ],

  //   [
  //     "id" => 25,
  //     "days" => ["0", "1", "2"],
  //     "hours" => 12,
  //     "mode" => 1,
  //     "tournament_id" => 2
  //   ],

  //   [
  //     "id" => 26,
  //     "days" => ["0", "1", "2"],
  //     "hours" => 8,
  //     "mode" => 1,
  //     "tournament_id" => 2
  //   ],

  //   [
  //     "id" => 27,
  //     "days" => ["0", "1", "2"],
  //     "hours" => 6,
  //     "mode" => 1,
  //     "tournament_id" => 2
  //   ],

  //   [
  //     "id" => 28,
  //     "days" => ["0", "1"],
  //     "hours" => 12,
  //     "mode" => 2,
  //     "tournament_id" => 2
  //   ],

  //   [
  //     "id" => 29,
  //     "days" => ["0", "1"],
  //     "hours" => 12,
  //     "mode" => 2,
  //     "tournament_id" => 2
  //   ],

  //   [
  //     "id" => 30,
  //     "days" => ["0", "1","2"],
  //     "hours" => 12,
  //     "mode" => 1,
  //     "tournament_id" => 2
  //   ],
  // ];

// $divisions = [
//   ["id" => 2,
//    "teams" => [293,294,295,296,297,298,299,300],
//    "division_mode" => 1,
//    "bracket_id" => 49
//   ],
//   ["id" => 3,
//    "teams" => [301,302,303,304,305,306],
//    "division_mode" => 1,
//    "bracket_id" => 50
//   ],
//   ["id" => 4,
//    "teams" => [307,308,309,310,311,312,313,314,315,316,317,318,319],
//    "division_mode" => 1,
//    "bracket_id" => 51
//   ],
//   ["id" => 5,
//    "teams" => [320,321,322,323,324,325,326,327],
//    "division_mode" => 1,
//    "bracket_id" => 52
//   ],
//   ["id" => 6,
//    "teams" => [328,329,330,331,332,333,334,335,336],
//    "division_mode" => 1,
//    "bracket_id" => 53
//   ],
//   ["id" => 8,
//    "teams" => [335,337,338,339,340,341,342,343,344,345,346,347,348,349,350,351,352,353,354,355,356,357,358,359,360,361,362,363,364,365,366,367,369],
//    "division_mode" => 1,
//    "bracket_id" => 54
//   ],
//   ["id" => 9,
//    "teams" => [368,370,371,372,373,374,375,376,377],
//    "division_mode" => 1,
//    "bracket_id" => 55
//   ],
//   ["id" => 10,
//    "teams" => [378,379,380,381],
//    "division_mode" => 1,
//    "bracket_id" => 56
//   ],
//   ["id" => 11,
//    "teams" => [382,383,384,385,386,387,388,389,390,391],
//    "division_mode" => 2,
//    "bracket_id" => 57
//   ],
//   ["id" => 12,
//    "teams" => [392,393,394,395,396,397,399],
//    "division_mode" => 2,
//    "bracket_id" => 58
//   ],

//   ["id" => 13,
//    "teams" => [398,400,401,402,403],
//    "division_mode" => 2,
//    "bracket_id" => 59
//   ],

//   ["id" => 14,
//    "teams" => [404,405,406],
//    "division_mode" => 1,
//    "bracket_id" => 60
//   ],

//   ["id" => 15,
//    "teams" => [407,408,409,410,411,412,413,414,415,416,417,418],
//    "division_mode" => 1,
//    "bracket_id" => 60
//   ],

//   ["id" => 15,
//    "teams" => [407,408,409,410,411,412,413,414,415,416,417,418],
//    "division_mode" => 1,
//    "bracket_id" => 61
//   ],

//     ["id" => 16,
//    "teams" => [419,420,421,422,423,424],
//    "division_mode" => 1,
//    "bracket_id" => 62
//   ],

//   ["id" => 17,
//    "teams" => [425,426,427,428],
//    "division_mode" => 1,
//    "bracket_id" => 63
//   ],

//   ["id" => 18,
//    "teams" => [429,430,431,432,433],
//    "division_mode" => 1,
//    "bracket_id" => 64
//   ]
// ];
// $officials = [
//     ["id" => "1", "days" => ["0", "1", "2"], "hours" => "12", "mode" => "1"],
//     ["id" => "2", "days" => [ "1", "2"], "hours" => "12", "mode" => "3"],
//     ["id" => "3", "days" => ["0", "1", "2"], "hours" => "12", "mode" => "1"],
//     ["id" => "5", "days" => ["0", "1", "2"], "hours" => "12", "mode" => "3"],
//     ["id" => "6", "days" => ["0", "1", "2"], "hours" => "6", "mode" => "3"],
//     ["id" => "7", "days" => ["0", "1", "2"], "hours" => "12", "mode" => "3"],
//     ["id" => "8", "days" => ["0", "1", "2"], "hours" => "8", "mode" => "3"],
//     ["id" => "9", "days" => ["0", "1", "2"], "hours" => "12", "mode" => "3"],
//     ["id" => "10", "days" => [ "1", "2"], "hours" => "12", "mode" => "3"],
//     ["id" => "11", "days" => [ "1", "2"], "hours" => "4", "mode" => "1"],
//     ["id" => "12", "days" => ["0", "1", "2"], "hours" => "12", "mode" => "3"],
//     ["id" => "13", "days" => ["0", "1", "2"], "hours" => "12", "mode" => "3"],
//     ["id" => "14", "days" => ["0", "1", "2"], "hours" => "12", "mode" => "3"],
//     ["id" => "15", "days" => ["0", "1", "2"], "hours" => "4", "mode" => "1"],
//     ["id" => "16", "days" => ["1", "2"], "hours" => "12", "mode" => "3"],
//     ["id" => "17", "days" => ["2"], "hours" => "5", "mode" => "2"],
//     ["id" => "18", "days" => ["0", "1", "2"], "hours" => "12", "mode" => "1"],
//     ["id" => "19", "days" => ["0", "1", "2"], "hours" => "6", "mode" => "3"],
//     ["id" => "20", "days" => ["0", "1", "2"], "hours" => "12", "mode" => "1"],
//     ["id" => "21", "days" => ["0", "1", "2"], "hours" => "12", "mode" => "3"],
//     ["id" => "22", "days" => ["0", "1", "2"], "hours" => "12", "mode" => "1"],
//     ["id" => "23", "days" => ["0", "1", "2"], "hours" => "8", "mode" => "1"],
//     ["id" => "24", "days" => ["0", "1", "2"], "hours" => "6", "mode" => "1"],
//     ["id" => "25", "days" => ["2"], "hours" => "12", "mode" => "2"],
//     ["id" => "26", "days" => ["1", "2"], "hours" => "12", "mode" => "2"],
//     ["id" => "27", "days" => ["0", "1", "2"], "hours" => "12", "mode" => "1"]
// ];

// $scheduler = new TournamentScheduler($hours, $fields5v5, $fields7v7, $divisions, $officials, 2);
// $result = $scheduler->scheduleMatches();
// print_r($result);



?>



