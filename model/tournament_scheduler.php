<?php

class TournamentScheduler {
  private $scheduleHours;
  private $hours;
  private $brackets;
  private $tournament_id;
  private $tournament_days;
  private $first_part_brackets;

  public function __construct($hours, $fields5v5, $fields7v7, $tournament_id, $tournament_days) {
    $this->tournament_id = $tournament_id;
    $this->hours = $hours;
    $this->scheduleHours = $this->createScheduleHours($hours, $fields5v5, $fields7v7);
    $this->tournament_days = $tournament_days;
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
      $bye_teams = $bracket_round_matches['bye_teams'];

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
  
  public function createMatchesForBrackets($divisions) {
    $this->brackets = $this->initializeBrackets($divisions);

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
    for ($day = 0 ;$day < count($this->scheduleHours); $day++) {
      // $hours = $this->scheduleHours[$day];
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
               if (!$hour) $hour = $this->getNextHour($need_rest, $day + 1,$mode);;
                // $official = $this->assignOfficial($day, $hour[0], $mode);
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
									"official" => null,
									"bracket_round" => $round
								];

                $team_1 = $match[0] == "TBD" ? null : intval($match[0]);
                $team_2 = $match[1] == "TBD" ? null : intval($match[1]);

                PendingMatchesDatabase::insert_match(
                  $this->tournament_id, 
                  intval($division['id']), 
                  intval($division['bracket_id']), 
                  $hour[1], 
                  $mode,
                  $this->tournament_days[$day], 
                  $hour[0], 
                  $bracket_match[$division['id']], 
                  null,
                  $team_1, 
                  $team_2,
                  2,
                  1,
                  intval($round)
                );

								$bracket_match[$division['id']]++;
							}
							$division['scheduled_matches'][$round] = $scheduleMatches;
							unset($division['matches'][$round]);
							$matches_this_day--;
              if ($matches_this_day == 1) $need_rest = true;
						}
        }
    }
  }

  private function initializeRoundRobin(&$divisions) {
    $matches = [];
    foreach ($divisions as &$division) {
      $teams = $division['teams'];
      if (count($teams) % 2 == 1)
          $teams[] = "BYE";
  
      $n = count($teams);
      $rounds = $n - 1;
      $matches = [];
  
      for ($round = 0; $round < $rounds; $round++) {
          $round_matches = [];
          for ($i = 0; $i < $n / 2; $i++) {
              $team_1 = $teams[$i];
              $team_2 = $teams[$n - 1 - $i];
              if ($team_1 != "BYE" && $team_2 != "BYE") 
                  $round_matches[] = [$team_1, $team_2];
          }
          $matches[] = $round_matches;
          $teams1 = [$teams[0] , $teams[$n-1]];
          $teams2 =  array_slice($teams, 1, count($teams)-2);
          $teams = array_merge($teams1, $teams2);
      }
      $division['matches'] = $matches;

      $tournament_days = $this->scheduleHours;
      $total_matches = $rounds;

      $matches_per_day = [];
      for ($i = 0; $i < count($tournament_days); $i++) {
        $matches_per_day[$i] = 0;
      }

      while($total_matches > 0) {
        for ($i = 0; $i < count($tournament_days) && $total_matches > 0; $i++) {
            $matches_per_day[$i] += 1;
            $total_matches--;
        }
      }

      rsort($matches_per_day);

      $division['matches_per_day'] = $matches_per_day;
    }

    return $divisions;
  }

  public function createMatchesForRoundRobin($divisions) {
    $this->brackets = $this->initializeRoundRobin($divisions);
    
    $bracket_match = [];
    for ($day = 0 ;$day < count($this->scheduleHours); $day++) {
        // cycle through each division
       for ($i = 0; $i < count($this->brackets); $i++) {
           $division = &$this->brackets[$i];
           if (!isset($division['matches_per_day'])) continue;
           if ( $division['matches_per_day'][$day] == 0) continue;
           $matches_this_day = &$division['matches_per_day'][$day];
           $mode = $division['division_mode'];

           if (!isset($bracket_match[$division['id']])) {
             $bracket_match[$division['id']] = 1;
           }
           $need_rest = false;
           // cycle through each round
           foreach($division['matches'] as $round => &$matches) {
              if ($matches_this_day == 0) break;
              $scheduleMatches = [];
             
             // cycle through each match
              foreach ($matches as &$match) {
               // get next available hour
               $hour = $this->getNextHour($need_rest, $day,$mode);
               if (!$hour) $hour = $this->getNextHour($need_rest, $day + 1,$mode);;
                // $official = $this->assignOfficial($day, $hour[0], $mode);
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
									"official" => null,
									"bracket_round" => $round
								];

                PendingMatchesDatabase::insert_match(
                  $this->tournament_id, 
                  intval($division['id']), 
                  intval($division['bracket_id']), 
                  $hour[1], 
                  $mode,
                  $this->tournament_days[$day], 
                  $hour[0], 
                  $bracket_match[$division['id']], 
                  null,
                  $match[0], 
                  $match[1],
                  1,
                  null,
                  intval($round)
                );

							}
              $bracket_match[$division['id']]++;
							$division['scheduled_matches'][$round] = $scheduleMatches;
							unset($division['matches'][$round]);
							$matches_this_day--;
              if ($matches_this_day == 1) $need_rest = true;
						}
        }
    }
    
    return $this->brackets;
  }

  private function selectBracketTeams(&$number_of_teams, $is_base_case) {
    if ($is_base_case) {
      $teams = $number_of_teams;
      $number_of_teams -= $number_of_teams;
      return $teams;
    }
    
    $number_of_teams -= 8;
    return 8;
  }

  private function initializePartialRoundRobin(&$divisions) {
    $matches = [];
    shuffle($divisions);
    foreach ($divisions as $index => &$division) {
      $teams = $division['teams'];
      shuffle($teams);
      
      // decide total rounds based on # of teams
      $teams_count = count($teams);
      $rounds = $teams_count % 2 === 0 ? 2  : 3;
      $matches = [];

      // create matches for round robin
      $total_matches = [];
      for ($i = 0; $i < $teams_count; $i++) {
        if ($i == $teams_count - 1) {
          $team_1 = $teams[$i];
          $team_2 = $teams[0];
          $total_matches[] = [$team_1, $team_2];
        } else {
          $team_1 = $teams[$i];
          $team_2 = $teams[$i + 1];
          $total_matches[] = [$team_1, $team_2];
        }
      }

      for ($round = 0; $round < $rounds; $round++) {
          $round_matches = [];

          if ($round === 2 && $rounds === 3) {
            $round_matches[] = $total_matches[count($total_matches) - 1];
            $matches[] = $round_matches;
            continue;
          }

          for ($i = $round; $i < count($total_matches); $i+= 2) {
            if ($rounds == 3 && $i == count($total_matches) - 1) {
              continue;
            }
            $round_matches[] = $total_matches[$i];
          }
          $matches[] = $round_matches;
      }

      // create matches for single elimination brackets
      $matches_single_elimination = [];
      $number_of_teams = $teams_count;
      $preferred_days_single_elimination = array_slice($division['preferred_days'], 1);
      while ($number_of_teams > 0) {
        $bracket_data = [];
        $bracket_data['rounds_per_day'] = [];
        $teams_this_bracket = 0;
        match (true) {
          $number_of_teams <= 8 => $teams_this_bracket = $this->selectBracketTeams($number_of_teams, true),
          $number_of_teams > 8 && $number_of_teams < 16 => $teams_this_bracket = $this->selectBracketTeams($number_of_teams, true),
          $number_of_teams >= 16 => $teams_this_bracket = $this->selectBracketTeams($number_of_teams, false),
          default => $teams_this_bracket = $number_of_teams,
        };

        $bracket_round_matches = $this->generate_bracket_rounds($teams_this_bracket);

        if(!$bracket_round_matches) continue;
        
        $rounds_single_elimination = $bracket_round_matches['brackets'];
        $bye_teams = $bracket_round_matches['bye_teams'];

        // create matches per round and create an entry for each match
        foreach ($rounds_single_elimination as $matchCount) {
            $match = [];
            for ($i = 0; $i < $matchCount; $i++) {
                $match[] = ["TBD", "TBD"];
            }
            $bracket_data['matches_per_round'][] = $matchCount;
            $bracket_data['matches_playoffs'][] = $match;
        }
        
        $rounds_per_day = [];
        for ($i = 0; $i < count($this->tournament_days); $i++) {
          $rounds_per_day[$i] = isset($bracket_data['rounds_per_day'][$i]) ? $bracket_data['rounds_per_day'][$i] : 0;
        }

        $total_rounds = count($rounds_single_elimination);
        while($total_rounds > 0) {
          for ($i = 0; $i < count($this->tournament_days) && $total_rounds > 0; $i++) {
            if (in_array($this->tournament_days[$i], $preferred_days_single_elimination)) {
              $rounds_per_day[$i] += 1;
              $total_rounds--;
            }
          }
        }

        $bracket_data['matches_playoffs_copy'] = $bracket_data['matches_playoffs'];
        $bracket_data['teams'] = $teams_this_bracket;
        $bracket_data['rounds_per_day'] = $rounds_per_day;
        $bracket_data['bye_teams'] = $bye_teams;
        $matches_single_elimination[] = $bracket_data;
      }

      $division['matches'] = $matches;
      $division['matches_single_elimination'] = $matches_single_elimination;
      $total_matches = $rounds;
      
      $matches_per_day = [];
      for ($i = 0; $i < count($this->tournament_days); $i++) {
        $matches_per_day[$i] = 0;
      }

      while($total_matches > 0) {
        for ($i = 0; $i < count($this->tournament_days) && $total_matches > 0; $i++) {
          if ($this->tournament_days[$i] == $division['preferred_days'][0]) {
            $matches_per_day[$i] += 1;
            $total_matches--;
          }
        }
      }

      $division['matches_per_day'] = $matches_per_day;
    }

    return $divisions;
  } 

  private function initializeSingleEliminationBrackets(&$divisions) {
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
  
  public function createMatchesForGeneralTournament($divisions) {
    $this->first_part_brackets = $this->initializePartialRoundRobin($divisions);
    
    $bracket_match = [];
    for ($day = 0 ;$day < count($this->scheduleHours); $day++) {
      // cycle through each division
      for ($i = 0; $i < count($this->first_part_brackets); $i++) {
        $division = &$this->first_part_brackets[$i];
        if (!isset($division['matches_per_day'])) continue;
        if ( $division['matches_per_day'][$day] == 0) continue;

        $matches_this_day = &$division['matches_per_day'][$day];
        $mode = $division['division_mode'];

        if (!isset($bracket_match[$division['bracket_id']])) {
          $bracket_match[$division['bracket_id']] = 1;
        }

        $multiple_matches = false;
        $hour_shift = 0;
        $last_assigned_hour = null;
        $current_day = $day;
        // cycle through each round
        foreach($division['matches'] as $round => &$matches) {
          if (!$multiple_matches && count($matches) > 1) $multiple_matches = true;
          if ($matches_this_day == 0) break;
          $scheduleMatches = [];
          
          // cycle through each match of the round
          foreach ($matches as &$match) {
            // get next available hour
            $hour = $this->getNextHourAndField($current_day,$mode,$hour_shift, $last_assigned_hour, $multiple_matches);
            if (!$hour) {
              $current_day++;
              $hour = $this->getNextHourAndField($current_day,$mode,$hour_shift, $last_assigned_hour, $multiple_matches);
            }
            if (!$hour) break;
            $scheduleMatches[] = [
              "tournament_id" => $this->tournament_id,
              "division_id" => $division['id'],
              "bracket_id" => $division['bracket_id'],
              "field" => $hour[1],
              "field_type" => $mode,
              "team_id_1" => $match[0],
              "team_id_2" => $match[1],
              "day" => $day,
              "hour" => $hour[0],
              "match_type" => 1,
              "bracket_match" => $bracket_match[$division['bracket_id']],
              "official" => null,
              "playoff_id" => null,
              "bracket_round" => $round
            ];

            PendingMatchesDatabase::insert_match(
              $this->tournament_id, 
              intval($division['id']), 
              intval($division['bracket_id']), 
              $hour[1], 
              $mode,
              $this->tournament_days[$day], 
              $hour[0], 
              $bracket_match[$division['bracket_id']], 
              null,
              $match[0], 
              $match[1],
              1,
              null,
              intval($round)
            );

            $bracket_match[$division['bracket_id']]++;
          }
          $hour_shift = 2;
          $division['scheduled_matches'][$round] = $scheduleMatches;
          unset($division['matches'][$round]);
            $matches_this_day--;
        }

        $division['bracket_match'] = $bracket_match[$division['bracket_id']];
      }
    }
      
    // First, assign randomized teams to round 1, handle BYEs
    foreach ($this->first_part_brackets as &$division) {
      $teams = &$division['teams'];

      foreach ($division['matches_single_elimination'] as &$bracket) {
        $teams_this_bracket = array_splice($teams, 0, $bracket['teams']);
        $bracket['teams_array'] = $teams_this_bracket;
        
        // assign teams to first round matches
        if(!isset($bracket['matches_playoffs'])) continue;
        foreach ($bracket['matches_playoffs'][0] as &$match) {
          $match = [array_pop($teams_this_bracket), array_pop($teams_this_bracket)];
        }
      
        // Add BYE teams
        $bye_teams = $bracket['bye_teams'];
        $team_position = 0;
        $matches_per_round = $bracket['matches_per_round'][1];
        $jump = $matches_per_round / 2;
        while($bye_teams > 0) {
          for ($i = 0; $i < $jump && $bye_teams > 0; $i++) {
            for ($j = $i; $j < $matches_per_round && $bye_teams > 0; $j+= $jump) {
              $bye_team = array_pop($teams_this_bracket);
              $bracket['matches_playoffs'][1][$j][$team_position] = $bye_team;
              $bye_teams--;
            }
          }
          $team_position = 1;
        }
        $bracket['matches_playoffs_copy'] = $bracket['matches_playoffs'];
      }
    }

    return $this->first_part_brackets;
  }

  public function createMatchesForPlayoffs($brackets) {

    // cycle through each day and assign matches to hours
    for ($day = 0; $day < count($this->scheduleHours); $day++) {
      // cycle through each division
      for ($i = 0; $i < count($brackets); $i++) {
        $division = &$brackets[$i];
        
        $bracket_match = $division['bracket_match'];
        $playoff_id = 1;
        for($j = 0; $j < count($division['matches_single_elimination']); $j++) {
          $bracket = &$division['matches_single_elimination'][$j];
          if (!isset($bracket['rounds_per_day'])) continue;
          if ( $bracket['rounds_per_day'][$day] == 0) continue;
          $matches_this_day = &$bracket['rounds_per_day'][$day];
          $mode = $division['division_mode'];


          $multiple_matches = false;
          $hour_shift = 0;
          $last_assigned_hour = null;
          $current_day = $day;

          foreach($bracket['matches_playoffs'] as $round => &$matches) {
            if (!$multiple_matches && count($matches) > 1) $multiple_matches = true;
            if ($matches_this_day == 0) break;
            $scheduleMatches = [];
            
            // cycle through each match
            foreach ($matches as $match) {
              // get next available hour  
              $hour = $this->getNextHourAndField($current_day,$mode,$hour_shift, $last_assigned_hour, $multiple_matches);
              if (!$hour) {
                $current_day++;
                $hour = $this->getNextHourAndField($current_day,$mode,$hour_shift, $last_assigned_hour, $multiple_matches);
              }

              if (!$hour) break;
              $scheduleMatches[] = [
                "tournament_id" => $this->tournament_id,
                "division_id" => $division['id'],
                "bracket_id" => $division['bracket_id'],
                "field" => $hour[1],
                "field_type" => $mode,
                "team_id_1" => $match[0],
                "team_id_2" => $match[1],
                "match_type" => 1,
                "day" => $day,
                "hour" => $hour[0],
                "bracket_match" => $bracket_match,
                "official" => null,
                "playoff_id" => $playoff_id,
                "bracket_round" => $round
              ];

              $team_1 = $match[0] == "TBD" ? null : intval($match[0]);
              $team_2 = $match[1] == "TBD" ? null : intval($match[1]);

              PendingMatchesDatabase::insert_match(
                $this->tournament_id, 
                intval($division['id']), 
                intval($division['bracket_id']), 
                $hour[1], 
                $mode,
                $this->tournament_days[$day], 
                $hour[0], 
                $bracket_match,
                null,
                $team_1, 
                $team_2,
                2,
                $playoff_id,
                intval($round)
              );

              $bracket_match++;
            }

            $hour_shift = 2;
            $bracket['scheduled_matches'][$round] = $scheduleMatches;
            unset($bracket['matches_playoffs'][$round]);
            $matches_this_day--;
          }
          $playoff_id++;
        }
        $division['bracket_match'] = $bracket_match;
      }
    }
    return $brackets;
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

  private function getNextHourAndField($day, $fieldType, &$hour_shift, &$last_assigned_hour, $multiple_matches) {
    foreach ($this->scheduleHours[$day] as $hour => &$fields) {
      if (!$fields[$fieldType]) continue;
      if ($multiple_matches && $last_assigned_hour && $hour < $last_assigned_hour) continue;
      if ($multiple_matches && $last_assigned_hour && $hour_shift > 0 && $hour - $last_assigned_hour != $hour_shift) continue;
      
      $hour_shift = 0;
      $last_assigned_hour = $hour;
      $field = array_shift($fields[$fieldType]);
      return [$hour, $field];
    }
    return null;
  }
  
  public function getBrackets() {
      return $this->brackets;
  }
}

?>