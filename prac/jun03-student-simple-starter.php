<!DOCTYPE html>
<html>

  <?php
    
    // PHP Initialization
    ini_set         ('display_errors', 1);
    error_reporting (E_ALL | E_STRICT);
    
    // Open the DB
    $dbunix_socket = '/ubc/icics/mss/cics516/db/cur/mysql/mysql.sock';
    $dbuser        = 'cics516';
    $dbpass        = 'cics516password';
    $dbname        = 'cics516';
    try {
      $db = new PDO ("mysql:unix_socket=$dbunix_socket;dbname=$dbname", $dbuser, $dbpass);
      $db->setAttribute (PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
      header ("HTTP/1.1 500 Server Error");
      die    ("HTTP/1.1 500 Server Error: Database Unavailable ({$e->getMessage()})");
    }
  ?>

  <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
      if (isset ($_POST['Insert'])) {
        $id   = $_POST['id'];
        $name = $_POST['name'];
        if (! preg_match ('/^\d*[1-9]+\d*$/', $id)) {
          die ("<p>Bad id</>");
        }
        $name = htmlspecialchars ($name);
        $s    = $db->prepare ('insert into student (id,name) values (:id,:name);');
        $s->bindParam (':id',   $id);
        $s->bindParam (':name', $name);
        try {
          $s->execute();
        } catch (PDOException $e) {
          print ("<p>Error inserting</p>");
        }
      } else if (isset ($_POST['Delete'])) {
        $s = $db->prepare ('delete from student where id = :id;');
        $s->bindParam (':id', $did);
        foreach ($_POST['deleteid'] as $did) {
          try {
            $s->execute();
          } catch (PDOException $e) {
            print ("<p>Error deleting</p>");
          }
        }
      } else if (isset ($_POST['Update'])) {
        $ids   = $_POST['ids'];
        $names = $_POST['names'];
        foreach ($_POST['checked'] as $cid) {
          print "<p>id = $ids[$cid] name = $names[$cid]</p>";
        }
      }
    }
    
    $s = $db->prepare ("select * from student");
    $s->execute();
    $rows = $s->fetchAll();
  ?>

  <body>
    <form action="" method="post">
      <table>
        <?php foreach ($rows as $rownum => $row): ?>
          <tr>
            <td><input type="checkbox" name="checked[]" value="<?php print $rownum;?>"></td>
            <td><?php print $row['id']; ?></td>
            <input type="hidden" name="ids[]" value="<?php print $row['id'];?>"</td>
            <td><input type="text" name="names[]" value="<?php print $row['name']; ?>"</td>
          </tr>
        <?php endforeach ?>
      </table>
      <input type="submit" name="Delete" value="Delete">
      <input type="submit" name="Update" value="Update">
    </form>

    <form action="" method="post">
      <label>ID <input type="text" name="id"></label>
      <label>Name <input type="text" name="name"></label>
      <input type="submit" name="Insert" value="Insert">
    </form>

  </body>
</html>