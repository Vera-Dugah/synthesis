<?php
session_start();
require __DIR__ . "/db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $id = (int) ($_POST["id"] ?? 0);
  $action = $_POST["action"] ?? "";
  $db = shelf_db();

  if ($db !== null && $id > 0 && ($action === "return" || $action === "paid")) {
    $stmt = $db->prepare("SELECT borrower, title, due_date FROM loans WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($borrower, $title, $due);
    $found = $stmt->fetch();
    $stmt->close();

    if ($found) {
      $days = shelf_days_late($due);
      if ($action === "return" && $days === 0) {
        $delete = $db->prepare("DELETE FROM loans WHERE id = ?");
        $delete->bind_param("i", $id);
        $delete->execute();
        $delete->close();
        $_SESSION["board_notice"] = ["kind" => "back", "title" => $title];
      } elseif ($action === "return" && $days > 0) {
        $_SESSION["board_notice"] = [
          "kind" => "late",
          "id" => $id,
          "title" => $title,
          "borrower" => $borrower,
          "days" => $days,
          "fee" => number_format($days * 2, 2, ".", "")
        ];
      } elseif ($action === "paid" && $days > 0) {
        $delete = $db->prepare("DELETE FROM loans WHERE id = ?");
        $delete->bind_param("i", $id);
        $delete->execute();
        $delete->close();
        $_SESSION["board_notice"] = ["kind" => "back", "title" => $title];
      }
    }

    $db->close();
  }

  header("Location: board.php");
  exit;
}

$notice = $_SESSION["board_notice"] ?? null;
unset($_SESSION["board_notice"]);

$db = shelf_db();
$rows = [];
$problem = $db === null;

if (!$problem) {
  $result = $db->query("SELECT id, borrower, title, due_date FROM loans ORDER BY id");
  if ($result) {
    while ($row = $result->fetch_assoc()) {
      $rows[] = $row;
    }
    $result->free();
  }
  $db->close();
}

$lateDays = (int) ($notice["days"] ?? 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Loan board — The Saturday Shelf</title>
  <meta name="description" content="Books already borrowed from The Saturday Shelf, with the borrower and the Saturday each one is due.">
  <link rel="stylesheet" href="css/shelf.css">
  <script src="js/board.js" defer></script>
</head>
<body>
  <header>
    <p><a href="index.html">The Saturday Shelf</a></p>
    <nav aria-label="Pages">
      <ul>
        <li><a href="index.html">The room</a></li>
        <li><a href="shelves.html">The shelves</a></li>
        <li><a href="board.php" aria-current="page">Loan board</a></li>
        <li><a href="borrow.php">Borrow</a></li>
        <li><a href="slip.html">Late slip</a></li>
      </ul>
    </nav>
  </header>

  <main>
    <h1>Loan board</h1>
    <p>These books are already out. A title on this board is not on the shelf today. Press Returned when that book is back.</p>

    <table>
      <caption>Books that are out</caption>
      <thead>
        <tr>
          <th scope="col">Title</th>
          <th scope="col">Who has it</th>
          <th scope="col">Due back</th>
          <th scope="col">Returned</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($problem) { ?>
        <tr>
          <td colspan="4">The loan list is not available right now.</td>
        </tr>
        <?php } elseif (count($rows) === 0) { ?>
        <tr>
          <td colspan="4">No books are out.</td>
        </tr>
        <?php } else { ?>
        <?php foreach ($rows as $row) { ?>
        <tr>
          <th scope="row"><?= htmlspecialchars($row["title"]) ?></th>
          <td><?= htmlspecialchars($row["borrower"]) ?></td>
          <td><?= htmlspecialchars(shelf_due_label($row["due_date"])) ?></td>
          <td>
            <form method="post" action="board.php">
              <input type="hidden" name="id" value="<?= (int) $row["id"] ?>">
              <input type="hidden" name="action" value="return">
              <button type="submit">Returned</button>
            </form>
          </td>
        </tr>
        <?php } ?>
        <?php } ?>
      </tbody>
    </table>
  </main>

  <?php if (is_array($notice) && ($notice["kind"] ?? "") === "back") { ?>
  <dialog id="returned">
    <form method="dialog">
      <h2>Back on the shelf</h2>
      <p id="returned-text"><?= htmlspecialchars($notice["title"]) ?> is back on the shelf.</p>
      <button type="submit">Close</button>
    </form>
  </dialog>
  <?php } elseif (is_array($notice) && ($notice["kind"] ?? "") === "late") { ?>
  <dialog id="returned">
    <h2>Late return</h2>
    <p id="returned-text"><?= htmlspecialchars($notice["title"]) ?>, with <?= htmlspecialchars($notice["borrower"]) ?>, is <?= $lateDays ?> <?= $lateDays === 1 ? "day" : "days" ?> late. The fee is <?= htmlspecialchars($notice["fee"]) ?> cedis.</p>
    <div class="dialog-actions">
      <form method="post" action="board.php">
        <input type="hidden" name="id" value="<?= (int) $notice["id"] ?>">
        <input type="hidden" name="action" value="paid">
        <button type="submit">Fee paid</button>
      </form>
      <form method="dialog">
        <button type="submit">Close</button>
      </form>
    </div>
  </dialog>
  <?php } ?>

  <footer>
    <p>Open Saturday, 9:00 to 13:00. One book for one week.</p>
  </footer>
</body>
</html>
