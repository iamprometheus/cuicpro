
<?php
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

// $brackets = [
//   [
//     "id" => "2",
//     "teams" => [
//       "293",
//       "294",
//       "295",
//       "296",
//       "297",
//       "298",
//       "299",
//       "300"
//     ],
//     "division_mode" => "1",
//     "bracket_id" => "564",
//     "matches_per_round" => [
//       4,
//       2,
//       1
//     ],
//     "matches_per_day" => [
//       0,
//       0
//     ],
//     "bye_teams" => 0,
//     "matches" => [],
//     "scheduled_matches" => [
//       [
//         [
//           "tournament_id" => 29,
//           "division_id" => "2",
//           "bracket_id" => "564",
//           "field" => 2,
//           "team_id_1" => "297",
//           "team_id_2" => "296",
//           "day" => 0,
//           "hour" => 19,
//           "bracket_match" => 1,
//           "official" => null,
//           "bracket_round" => 0
//         ],
//         [
//           "tournament_id" => 29,
//           "division_id" => "2",
//           "bracket_id" => "564",
//           "field" => 1,
//           "team_id_1" => "299",
//           "team_id_2" => "298",
//           "day" => 0,
//           "hour" => 19,
//           "bracket_match" => 2,
//           "official" => null,
//           "bracket_round" => 0
//         ],
//         [
//           "tournament_id" => 29,
//           "division_id" => "2",
//           "bracket_id" => "564",
//           "field" => 2,
//           "team_id_1" => "294",
//           "team_id_2" => "300",
//           "day" => 0,
//           "hour" => 20,
//           "bracket_match" => 3,
//           "official" => null,
//           "bracket_round" => 0
//         ],
//         [
//           "tournament_id" => 29,
//           "division_id" => "2",
//           "bracket_id" => "564",
//           "field" => 1,
//           "team_id_1" => "295",
//           "team_id_2" => "293",
//           "day" => 0,
//           "hour" => 20,
//           "bracket_match" => 4,
//           "official" => null,
//           "bracket_round" => 0
//         ]
//       ],
//       [
//         [
//           "tournament_id" => 29,
//           "division_id" => "2",
//           "bracket_id" => "564",
//           "field" => 2,
//           "team_id_1" => "TBD",
//           "team_id_2" => "TBD",
//           "day" => 1,
//           "hour" => 8,
//           "bracket_match" => 5,
//           "official" => null,
//           "bracket_round" => 1
//         ],
//         [
//           "tournament_id" => 29,
//           "division_id" => "2",
//           "bracket_id" => "564",
//           "field" => 1,
//           "team_id_1" => "TBD",
//           "team_id_2" => "TBD",
//           "day" => 1,
//           "hour" => 8,
//           "bracket_match" => 6,
//           "official" => null,
//           "bracket_round" => 1
//         ]
//       ],
//       [
//         [
//           "tournament_id" => 29,
//           "division_id" => "2",
//           "bracket_id" => "564",
//           "field" => 2,
//           "team_id_1" => "TBD",
//           "team_id_2" => "TBD",
//           "day" => 1,
//           "hour" => 11,
//           "bracket_match" => 7,
//           "official" => null,
//           "bracket_round" => 2
//         ]
//       ]
//     ]
//         ],
//     [
//     "id" => "3",
//     "teams" => [
//       "301",
//       "302",
//       "303",
//       "304",
//       "305",
//       "306"
//     ],
//     "division_mode" => "1",
//     "bracket_id" => "565",
//     "matches_per_round" => [
//       2,
//       2,
//       1
//     ],
//     "matches_per_day" => [
//       0,
//       0
//     ],
//     "bye_teams" => 2,
//     "matches" => [],
//     "scheduled_matches" => [
//       [],
//       [
//         [
//           "tournament_id" => 29,
//           "division_id" => "3",
//           "bracket_id" => "565",
//           "field" => 2,
//           "team_id_1" => "304",
//           "team_id_2" => "TBD",
//           "day" => 1,
//           "hour" => 9,
//           "bracket_match" => 1,
//           "official" => null,
//           "bracket_round" => 1
//         ],
//         [
//           "tournament_id" => 29,
//           "division_id" => "3",
//           "bracket_id" => "565",
//           "field" => 1,
//           "team_id_1" => "303",
//           "team_id_2" => "TBD",
//           "day" => 1,
//           "hour" => 9,
//           "bracket_match" => 2,
//           "official" => null,
//           "bracket_round" => 1
//         ]
//       ],
//       [
//         [
//           "tournament_id" => 29,
//           "division_id" => "3",
//           "bracket_id" => "565",
//           "field" => 2,
//           "team_id_1" => "TBD",
//           "team_id_2" => "TBD",
//           "day" => 1,
//           "hour" => 12,
//           "bracket_match" => 3,
//           "official" => null,
//           "bracket_round" => 2
//         ]
//       ]
//     ]
//   ]
// ]
