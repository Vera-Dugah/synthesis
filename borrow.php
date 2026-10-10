<?php
session_start();
require __DIR__ . "/db.php";

$books = shelf_books();
$name = "";
$email = "";
$book = "";
$warning = "";
$received = $_SESSION["received"] ?? "";
unset($_SESSION["received"]);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $name = trim($_POST["name"] ?? "");
  $email = trim($_POST["email"] ?? "");
  $book = trim($_POST["book"] ?? "");

  if ($name === "") {
    $warning = "Enter your name. A name is required.";
  } elseif ($email === "") {
    $warning = "Enter your email. An email is required.";
  } elseif (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
    $warning = "That email is not valid. Include an @.";
  } elseif (!in_array($book, $books, true)) {
    $warning = "Choose a book. The blank first choice does not count.";
  } else {
    $db = shelf_db();
    if ($db === null) {
      $warning = "The loan list is not available right now. Try again in a moment.";
    } else {
      $due = (new DateTimeImmutable("today"))->modify("next saturday")->format("Y-m-d");
      $stmt = $db->prepare("INSERT INTO loans (borrower, email, title, due_date) VALUES (?, ?, ?, ?)");
      $stmt->bind_param("ssss", $name, $email, $book, $due);
      $saved = $stmt->execute();
      $stmt->close();
      $db->close();
      if ($saved) {
        $_SESSION["received"] = $name . ", your request for " . $book . " was received. A note can be sent to " . $email . ".";
        header("Location: borrow.php");
        exit;
      }
      $warning = "The loan list is not available right now. Try again in a moment.";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Borrow — The Saturday Shelf</title>
  <meta name="description" content="How to borrow one book from The Saturday Shelf on Saturday.">
  <link rel="stylesheet" href="css/shelf.css">
  <script src="js/borrow.js" defer></script>
</head>
<body>
  <header>
    <p><a href="index.html">The Saturday Shelf</a></p>
    <nav aria-label="Pages">
      <ul>
        <li><a href="index.html">The room</a></li>
        <li><a href="shelves.html">The shelves</a></li>
        <li><a href="board.php">Loan board</a></li>
        <li><a href="borrow.php" aria-current="page">Borrow</a></li>
        <li><a href="slip.html">Late slip</a></li>
      </ul>
    </nav>
  </header>

  <main>
    <h1>Borrow</h1>
    <p>You can take one book home if you come while the room is open.</p>
    <ol>
      <li>Come on Saturday between 9:00 and 13:00.</li>
      <li>Check the <a href="board.php">loan board</a> so you do not choose a book that is already out.</li>
      <li>Take one book from the <a href="shelves.html">shelves</a>.</li>
      <li>Write your name beside the title on the loan board.</li>
      <li>Bring the book back on the next Saturday.</li>
    </ol>

    <form id="borrow-form" action="borrow.php" method="post">
      <fieldset>
        <legend>Ask for a book</legend>
        <p id="warning" role="alert"<?= $warning === "" ? " hidden" : "" ?>><?= htmlspecialchars($warning) ?></p>
        <p>
          <label for="borrower">Your name <span class="req">Required</span></label>
          <input id="borrower" name="name" type="text" required maxlength="80" autocomplete="name" value="<?= htmlspecialchars($name) ?>">
        </p>
        <p>
          <label for="email">Email <span class="req">Required</span></label>
          <input id="email" name="email" type="email" required maxlength="120" autocomplete="email" value="<?= htmlspecialchars($email) ?>">
        </p>
        <p>
          <label for="book">Book <span class="req">Required</span></label>
          <select id="book" name="book" required>
            <option value="">Choose one book</option>
            <?php foreach ($books as $title) { ?>
            <option<?= $title === $book ? " selected" : "" ?>><?= htmlspecialchars($title) ?></option>
            <?php } ?>
          </select>
        </p>
        <button type="submit">Ask for this book</button>
      </fieldset>
    </form>
  </main>

  <dialog id="received">
    <form method="dialog">
      <h2>Request received</h2>
      <p id="received-text"><?= htmlspecialchars($received) ?></p>
      <button type="submit">Close</button>
    </form>
  </dialog>

  <footer>
    <p>Open Saturday, 9:00 to 13:00. One book for one week.</p>
  </footer>
</body>
</html>
