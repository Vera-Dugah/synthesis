<?php

function shelf_db() {
  $db = @new mysqli("127.0.0.1", "root", "mysql", "saturday_shelf");
  if ($db->connect_errno) {
    return null;
  }
  $db->set_charset("utf8mb4");
  return $db;
}

function shelf_books() {
  return [
    "The Palm-Wine Drinkard",
    "Changes",
    "Homegoing",
    "English for Form Two",
    "Everyday Mathematics",
    "Mend a Torn Page",
    "Bicycle Repair"
  ];
}

function shelf_days_late($iso) {
  $due = DateTimeImmutable::createFromFormat("!Y-m-d", substr($iso, 0, 10));
  $today = new DateTimeImmutable("today");
  if ($today <= $due) {
    return 0;
  }
  return (int) $due->diff($today)->days;
}

function shelf_due_label($iso) {
  $date = DateTimeImmutable::createFromFormat("!Y-m-d", substr($iso, 0, 10));
  $days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
  $months = [
    1 => "January",
    "February",
    "March",
    "April",
    "May",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December"
  ];
  return $days[(int) $date->format("w")] . " " . $date->format("j") . " " . $months[(int) $date->format("n")];
}
