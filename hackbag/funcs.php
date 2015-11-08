<?php

include '../parse.php';
use Parse\ParseUser;
use Parse\ParseQuery;

//gets array of users with available bags
function getLenders(){
  $query = new ParseQuery("_User");
  $query->equalTo("ownsBag", false);
  $results = $query->find();

  return $results;
}

//gets number of current sleepers
function getNumOfSleepers(){
  $query = new ParseQuery("BagTransaction");
  $query->startsWith("status", "active");
  $results = $query->find();
  $count = count($results);

  return $count;
}

//get array of users seeking bags
function getSeekers(){
  $query = new ParseQuery("BagTransaction");
  $query->doesNotExist("status");
  $results = $query->find();
  $users = array();

  //convert BagTransactions to Users
  foreach ($results as $result){
    $user = $result->get("borrower");
    $user->fetch();
    $users[] = $user;
  }

  return $users;
}

//get type of user
function getUserType(&$user){
  if ($user->get("ownsBag")){
    if ($user->get("currentTransaction") == NULL){
      return "availableLender";
    }

    $currentTransaction = $user->get("currentTransaction");
    $currentTransaction->fetch();
    if ($currentTransaction->get("status") == "active"){
      return "currentLender";
    }
    else if ($currentTransaction->get("status") == "scheduled"){
      return "scheduledLender";
    }
  }
  else {
    if ($user->get("currentTransaction") == NULL){
      return "availableBorrower";
    }

    $currentTransaction = $user->get("currentTransaction");
    $currentTransaction->fetch();
    if ($currentTransaction->get("status") == "active"){
      return "currentBorrower";
    }
    else if ($currentTransaction->get("status") == "scheduled"){
      return "scheduledBorrower";
    }
    else if ($currentTransaction->get("lender") == NULL){
      return "seekingBorrower";
    }
  }
}

// function createTransaction($startTime, $endTime){
//   $user = ParseUser::getCurrentUser();
//
// }
//
// function getRegistrationTime(){
//   $user = ParseUser::getCurrentUser();
//   $currentTransaction = $user->get("currentTransaction");
//   $currentTransaction-> fetch();
//   echo $currentTransaction->get("startingTime");
// }


getRegistrationTime();

?>
