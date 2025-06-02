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

?>