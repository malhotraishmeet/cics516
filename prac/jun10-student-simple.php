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
    
    // Form Processing
    
    if ($_SERVER ["REQUEST_METHOD"] == "POST") {
      
      // Validation Templates
      $validId = "/^\s*([0-9]*[1-9][0-9]*)\s*$/";  // positive integer w/ trailing or leading whitespace
      
      // Fetch Parameters
      $selected  = (isset ($_POST ["selected"]))? $_POST ["selected"] : array();
      $ids       = $_POST ["id"];
      $names     = $_POST ["name"];
      $iId       = $_POST ["iId"];
      $iName     = $_POST ["iName"];
      $isUpdate  = isset ($_POST ["update"]);
      $isDelete  = isset ($_POST ["delete"]);
      $isInsert  = isset ($_POST ["insert"]);
      
      // Validate Parameters
      if ($isUpdate || $isDelete) {
        foreach ($selected as $rowIndex) {
          if (! preg_match ($validId, $ids [$rowIndex])) {
            header ("HTTP/1.1 400 Bad Request");
            die    ("HTTP/1.1 400 Bad Request: Invalid id '$ids [$rowIndex]' on row $rowIndex");
          }
          $names [$rowIndex] = htmlspecialchars ($names [$rowIndex]);
        }
      }
      if ($isInsert) {
        if ($isInsert && ! preg_match ($validId, $iId)) {
          header ("HTTP/1.1 400 Bad Request");
          die    ("HTTP/1.1 400 Bad Request: Invalid insert id '$iId'");
        }
        $iName = htmlspecialchars ($iName);
      }
      
      // Update the Database
      if ($isUpdate) {
        try {
          $stmt = $db->prepare ("UPDATE student SET name = :name WHERE id = :id");
          $stmt->bindParam (":name", $name);
          $stmt->bindParam (":id",   $id);
          foreach ($selected as $rowIndex) {
            $name = $names [$rowIndex];
            $id   = $ids   [$rowIndex];
            $stmt->execute();
          }
        } catch (PDOException $e) {
          header ("HTTP/1.1 500 Server Error");
          die    ("HTTP/1.1 500 Server Error: Error updating: {$e->getMessage()}");
        }
      } elseif ($isDelete) {
        try {
          $stmt = $db->prepare ("DELETE FROM student WHERE id = :id");
          $stmt->bindParam (":id", $id);
          foreach ($selected as $rowIndex) {
            $id = $ids [$rowIndex];
            $stmt->execute();
          }
        } catch (PDOException $e) {
          header ("HTTP/1.1 500 Server Error");
          die    ("HTTP/1.1 500 Server Error: Error deleting: {$e->getMessage()}");
        }
      } elseif ($isInsert) {
        try {
          $stmt = $db->prepare ("INSERT INTO student (id, name) VALUES (:id, :name)");
          $stmt->bindValue (":id", $iId);
          $stmt->bindValue (":name", $iName);
          $stmt->execute();
        } catch (PDOException $e) {
          header ("HTTP/1.1 500 Server Error");
          die    ("HTTP/1.1 500 Server Error: Error inserting: {$e->getMessage()}");
        }
      }
    }
    
    // Read the table from Database
    try {
      $rows = $db->query ("SELECT id, name FROM student ORDER BY id");
    } catch (PDOException $e) {
      header ("HTTP/1.1 500 Server Error");
      die    ("HTTP/1.1 500 Server Error: Error reading from database: {$e->getMessage()}");
    }
  ?>

  <!-- CSS (Normally this would be in a separate file) -->
  <head>
    <style>
      table, th, td {
        border-collapse:  collapse;
        border:           1px solid #aaaaaa;
        background-color: #eeeeee;
      }
      input[type="text"] {
        background-color: #ffffee;
      }
      input.id {
        width: 6em;
      }
      input.name {
        width: 30em;
      }
      td.id {
        text-align:    right;
        padding-right: 4px;
      }
      input[type="text"].error {
        background-color: #ff0000;
      }
    </style>
  </head>

  <head>
    <script type="text/javascript">
      function $(id) {
        return document.getElementById (id);
      }
      function checkForError (textbox, checkbox) {
        if (! checkbox.checked && textbox.value != textbox.default)
          textbox.className = "error";
        else
          textbox.className = "";
      }
    </script>
  </head>

  <body>

    <!-- Display table content on form for editing -->
    <form method="post" action="">
      <table>
        <tr><th> </th><th>ID</th><th>Name</th></tr>
          <?php foreach ($rows as $rowIndex => $row): ?>
            <tr>
              <input type  = "hidden"
                     name  = "id[]"
                     value = "<?php print $row ['id'] ?>"
              />
              <td>
                <input type  = "checkbox"
                       id    = "c<?php print $rowIndex;?>"
                       name  = "selected[]"
                       onclick = "checkForError($('t<?php print $rowIndex;?>'),this);"
                       value = "<?php print $rowIndex ?>"/>
              </td>
              <td class="id">
                <?php print $row ["id"] ?>
              </td>
              <td>
                <input type  = "text"
                       id    = "t<?php print $rowIndex;?>"
                       name  = "name[]"
                       value = "<?php print $row ['name'] ?>"
                       class = "name"
                       onchange = "checkForError(this,$('c<?php print $rowIndex;?>'));"
                />
              </td>
            </tr>
          <?php endforeach ?>
        <tr>
          <td><input type="submit" name="insert" value="Insert"/></td>
          <td><input type="text"   name="iId"    class="id"/>    </td>
          <td><input type="text"   name="iName"  class="name"/>  </td>
        </tr>
      </table>
      <input type="submit" name="update" value="Update"/>
      <input type="submit" name="delete" value="Delete"/>
    </form>

  </body>
</html>