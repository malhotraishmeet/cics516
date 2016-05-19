
<!DOCTYPE html>
<html>

  <head>

    <script src="//ajax.googleapis.com/ajax/libs/prototype/1.7.0.0/prototype.js" type="text/javascript"></script>

    <style> /* This should really be in a separate file */
      table, th, td {
        border-collapse:  collapse;
        border:           1px solid #aaaaaa;
        background-color: #eeeeee;
      }
      table .updDelBox {
        width:      1em;
        text-align: center;
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
      input.invalid {
        background-color: #ffdddd;
      }
      td.invalid {
        outline: 2px solid red;
      }
      .messageField {
        font-size: small;
        color: red;
      }
      .hidden {
        display: none;
      }
    </style>

    <script type="text/javascript"> // This should really be in a separate file
    
      HTMLElement.prototype.addClassName = function (className) {
        var cna = this.className.split (" ");
        if (cna.indexOf (className) == -1) {
          cna.push (className);
          this.className = cna.join (" ");
        }
      };
    
      HTMLElement.prototype.removeClassName = function (className) {
        var cna = this.className.split (" ");
        var ind = cna.indexOf (className);
        if (ind != -1)
          cna.splice (ind,1);
        this.className = cna.join (" ");
      }

      function $(id) {
        return document.getElementById (id);
      }

      function getRowNumber (input) {
        return input.id.substr (1);
      }

      function conditionallyEnableButton (checkboxName, buttonName) {
        var disable    = true;
        var checkboxes = document.getElementsByName (checkboxName);
        for (var i=0; i<checkboxes.length; i++) {
          if (checkboxes[i].checked) {
            disable = false;
            break;
          }
        }
        $(buttonName) .disabled = disable;
      }

      function deleteBoxChanged (deleteBox) {
        conditionallyEnableButton ("delRowNum[]", "deleteButton");
      }

      function updateBoxChanged (checkbox) {
        var name   = $("n" + getRowNumber (checkbox));
        name.value = name.defaultValue;
        makeValid (name);
        checkbox.checked = false;
        conditionallyEnableButton ("updRowNum[]", "updateButton");
      }

      function setMessage (messageCell, message) {
        $(messageCell) .innerText = message;
        var row        = $(messageCell) .parentNode;
        var cells      = row.getElementsByClassName ("messageField");
        var hasMessage = false;
        for (var i=0; i<cells.length; i++)
          if (cells[i].innerText != "") {
            hasMessage = true;
            break;
          }
        if (hasMessage)
          row.removeClassName ("hidden");
        else
          row.addClassName ("hidden");
      }

      function isIdValid (id) {
        return id.match ("^\\s*[0-9]*[1-9]+[0-9]*$");
      }

      function isNameValid (name) {
        return name.match ("^([A-Z][A-Za-z.]*)(\\s[A-Z][A-Za-z.]*)+\\s*$");
      }

      var nameInvalidMessage = "At least two capitalized names required";
      var idInvalidMessage   = "Number";

      function checkValid (field) {
        if (field.isValid (field.value)) {
          field.removeClassName ("invalid");
          setMessage (field.errField, "");
        } else {
          field.addClassName ("invalid");
          setMessage (field.errField, field.errMessage);
        }
      }

      function makeValid (field) {
        field.removeClassName ("invalid");
        setMessage (field.errField, "");
      }

      function nameChanged (name) {
        checkValid (name);
        $("u" + getRowNumber (name)) .checked = name.defaultValue != name.value && name.isValid (name.value);
        conditionallyEnableButton ("updRowNum[]", "updateButton");
      }
    
      function conditionallyEnableInsertButton() {
        $("insertButton") .disabled = !$("ii").isValid ($("ii").value) || !$("in").isValid ($("in").value);
       }

      function insertIdChanged (id) {
        checkValid (id);
        conditionallyEnableInsertButton();
      }

      function insertNameChanged (name) {
        checkValid (name);
        conditionallyEnableInsertButton();
      }

      window.onload = function() {
      	
      	$('main').on('change','.delBox', deleteBoxChanged());
      	
      	
        var names = document.getElementsByName ("name[]");
        for (var i = 0; i < names.length; i++) {
          names[i].isValid    = isNameValid;
          names[i].errField   = "nameMessage" + getRowNumber (names[i]);
          names[i].errMessage = nameInvalidMessage;
        }
        $("ii").isValid    = isIdValid;
        $("ii").errField   = "idMessageInsert";
        $("ii").errMessage = idInvalidMessage;
        $("in").isValid    = isNameValid;
        $("in").errField   = "nameMessageInsert";
        $("in").errMessage = nameInvalidMessage;
      }
      
      
      
      
      
    </script>

  </head>

  <body>
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
        $validId   = "/^\\s*([0-9]*[1-9][0-9]*)\s*$/";  // positive integer w/ trailing or leading whitespace
        $validName = "/^([A-Z][A-Za-z.]*)(\\s[A-Z][A-Za-z.]*)+\\s*$/";
        
        // Fetch Parameters
        $delRowNum = (isset ($_POST ["delRowNum"]))? $_POST ["delRowNum"] : array();
        $updRowNum = (isset ($_POST ["updRowNum"]))? $_POST ["updRowNum"] : array();
        $ids       = $_POST ["id"];
        $names     = $_POST ["name"];
        $iId       = $_POST ["iId"];
        $iName     = $_POST ["iName"];
        $isUpdate  = isset ($_POST ["update"]);
        $isDelete  = isset ($_POST ["delete"]);
        $isInsert  = isset ($_POST ["insert"]);
        if ($isDelete)
          $selectedRowNum = (isset ($_POST ["delRowNum"]))? $_POST ["delRowNum"] : array();
        else if ($isUpdate)
          $selectedRowNum = (isset ($_POST ["updRowNum"]))? $_POST ["updRowNum"] : array();
        else
          $selectedRowNum = array();
        
        // Validate Parameters
        foreach ($selectedRowNum as $rowIndex) {
          if (! preg_match ($validId, $ids [$rowIndex]))
            die ("Invalid id '$ids [$rowIndex]' on row $rowIndex");
          $names [$rowIndex] = htmlspecialchars ($names [$rowIndex]);
        }
        if ($isInsert) {
          if (! preg_match ($validId, $iId))
            die ("Invalid insert id '$iId'");
          if (! preg_match ($validName, $iName))
            die ("Invalid insert name '$iName'");
          $iName = htmlspecialchars ($iName);
        } else if ($isUpdate) {
          foreach ($selectedRowNum as $rowIndex)
          if (! preg_match ($validName, $names [$rowIndex]))
            die ("Invalid name '$names[$rowIndex]' on row $rowIndex");
        }
        
        // Perform DB Operation
        if ($isUpdate) {
          try {
            $stmt = $db->prepare ("UPDATE student SET name = :name WHERE id = :id");
            $stmt->bindParam (":name", $name);
            $stmt->bindParam (":id",   $id);
            foreach ($selectedRowNum as $rowIndex) {
              $name = $names [$rowIndex];
              $id   = $ids   [$rowIndex];
              $stmt->execute();
            }
          } catch (PDOException $e) {
            die ("Error updating: {$e->getMessage()}");
          }
        } elseif ($isDelete) {
          try {
            $stmt = $db->prepare ("DELETE FROM student WHERE id = :id");
            $stmt->bindParam (":id", $id);
            foreach ($selectedRowNum as $rowIndex) {
              $id = $ids [$rowIndex];
              $stmt->execute();
            }
          } catch (PDOException $e) {
            die ("Error deleting: {$e->getMessage()}");
          }
        } elseif ($isInsert) {
          try {
            $stmt = $db->prepare ("INSERT INTO student (id, name) VALUES (:id, :name)");
            $stmt->bindValue (":id", $iId);
            $stmt->bindValue (":name", $iName);
            $stmt->execute();
          } catch (PDOException $e) {
            die ("Error inserting: {$e->getMessage()}");
          }
        }
      }
      
      // Read the table from DB
      try {
        $rows = $db->query ("SELECT id, name FROM student ORDER BY id");
      } catch (PDOException $e) {
        die ("Error reading from database: {$e->getMessage()}");
      }
    ?>

    <!-- Display table content on form for editing -->
    <form method="post" action="">
      <table id="main">
        <thead>
          <tr><th class="updDelBox">Dl</th><th class="updDelBox">Up</td><th>ID</th><th>Name</th></tr>
        <thead>

        <tbody>
          <!-- Show database context for update and delete -->
          <?php foreach ($rows as $rowIndex => $row): ?>
            <tr>
              <td class="updDelBox">
                <!-- Delete checkbox -->
                <input class    = "delBox"
                	   type     = "checkbox"
                       name     = "delRowNum[]"
                       value    = "<?php print $rowIndex; ?>"
                       id       = '<?php print "d$rowIndex"; ?>'
                      
                />
              </td>
              <td class="updDelBox">
                <!-- Update checkbox -->
                <input type     = "checkbox"
                       name     = "updRowNum[]"
                       value    = "<?php print $rowIndex; ?>"
                       id       = '<?php print "u$rowIndex"; ?>'
                       onchange = "updateBoxChanged (this);"
                />
              </td>
              <td class="id">
                <!-- Displayed id -->
                <?php print $row ["id"]; ?>
              </td>
              <td>
                <!-- Name textbox -->
                <input type     = "text"
                       class    = "name"
                       name     = "name[]"
                       value    = '<?php print $row ["name"]; ?>'
                       id       = '<?php print "n$rowIndex"; ?>'
                       onkeyup  = "nameChanged (this);"
                />
              </td>
              <!-- Hidden id for submitted query string -->
              <input type  = "hidden"
                     name  = "id[]"
                     value = '<?php print $row ["id"]; ?>'
              />
            </tr>
            <tr class = "hidden messageRow">
              <td colspan="3"></td>
              <td class = "messageField" id = '<?php print "nameMessage$rowIndex"; ?>'></td>
              </td>
            </tr>
          <?php endforeach ?>
        </tbody>

        <!-- Inputs for database insert -->
        <tfoot>
          <tr>
            <td colspan="2">
              <input type  = "submit"
                     name  = "insert"
                     value = "Insert"
                     id    = "insertButton"
                     disabled
              />
            </td>
            <td>
              <input type     = "text"
                     name     = "iId"
                     class    = "id"
                     id       = "ii"
                     onkeyup  = "insertIdChanged (this);"
              />
            </td>
            <td>
              <input type     = "text"
                     name     = "iName"
                     class    = "name"
                     id       = "in"
                     onkeyup  = "insertNameChanged (this);"
              />
            </td>
          </tr>
          <tr class = "hidden messageRow">
            <td colspan="2"></td>
            <td class = "messageField" id = "idMessageInsert"></td>
            <td class = "messageField" id = "nameMessageInsert"></td>
          </tr>
        </tfoot>
      </table>

      <!-- Update and Delete buttons -->
      <input type  = "submit"
             name  = "delete"
             value = "Delete"
             id    = "deleteButton"
             disabled
      />
      <input type  = "submit"
             name  = "update"
             value = "Update"
             id    = "updateButton"
             disabled
      />
    </form>

  </body>
</html>