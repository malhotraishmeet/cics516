/** CONSTANTS */
var INVALID_CLASS_NAME  = "invalid";
var CHANGED_CLASS_NAME  = "changed";
var ERROR_CONSOLE_ID    = "errorConsole";
var CHECKBOX_SELECTOR   = ".checkbox";
var CHECKBOXY_SELECTOR  = ".checkboxy";
var FIELD_TYPES         =
  [new FieldType (".id.insert.field",    checkId,         ".id.container",   "#insert", false, "ID must be a positive integer"),
   new FieldType (".name.insert.field",  checkNameInsert, ".name.container", "#insert", false, "Name must be at least 10 characters"),
   new FieldType (".name.update.field",  checkNameUpdate, ".name.container", "#update", false, "Name must be at least 10 characters"),
   new FieldType (".pc.update.field",    checkPC,         ".pc.container",   "#update", false, "Must be a valid postal code of the form A0A 0A0")
  ];

/**
 * DOM Initialization.
 * 
 * Runs after document creation is complete.
 */
window.onload = function () {
  // Setup field validation
  var fc = new FieldController ($("studentForm"), INVALID_CLASS_NAME, CHANGED_CLASS_NAME, ERROR_CONSOLE_ID);
  FIELD_TYPES.each (function (f) {
    fc.addFieldType (f);
  });
  // Do other initialization
  connectCheckboxesAndCheckboxies();
  connectRowSubmitsToRowCheckboxes();
};

/**
 * Connect input checkboxes to name textboxes.
 * 
 * Changing a checkboxy sets the checkbox and unchecking the checkbox resets the checkboxy.
 */
function connectCheckboxesAndCheckboxies () {
  $$(CHECKBOX_SELECTOR) .each (function (checkbox) {
    var row         = checkbox .up ("tr");
    var checkboxies = row .select (CHECKBOXY_SELECTOR);
    row .on ("keyup", CHECKBOXY_SELECTOR, function () {
      checkbox.checked = checkboxies .any (function (checkboxy) {
        return checkboxy.value != checkboxy.defaultValue;
      });
      setSubmitsFromCheckboxes();
    });
    checkbox .observe ("click", function () {
      if (! checkbox.checked) {
        checkboxies .each (function (checkboxy) {
          checkboxy.value = checkboxy.defaultValue;
          checkboxy.fieldType .validateField (checkboxy);
        });
      }
    });
  });
}

/**
 * Deterine validity of Student ID
 * 
 * @param   {HTMLInputElement} input - html input that contains id
 * @returns {boolean}                  true iff input contains a valid id
 */
function checkId (input) {
  return ! isNaN (input.value) && input.value > 0;
}

/**
 * Determine validity of Name Insert
 *
 * @param   {HtmlInputElement} input - html input that contains name
 * @returns {boolean}                  true iff input contains a valid name
 */
function checkNameInsert (input) {
  return input.value.length >= 10;
}

/**
 * Determine validity of Name Update
 *
 * @param   {HtmlInputElement} input - html input that contains name
 * @returns {boolean}                  true iff input contains a valid name
 */
function checkNameUpdate (input) {
  return checkNameInsert (input) || input.value == input.defaultValue;
}

/**
 */
function checkPC (input) {
  return input.value.match (/^[A-Z][0-9][A-Z] [0-9][A-Z][0-9]$/) || input.value == input.defaultValue;
}

/**
 * Update and Delete submits an only be enabled if at least one checkbox is checked.
 */
function setSubmitsFromCheckboxes () {
  var upd = $("update");
  var del = $("delete");
  var isAnyRowChecked = $$("input[type='checkbox']"). any (function (c) {
    return c.checked;
  });
  upd .setDisabled (! isAnyRowChecked);
  del .setDisabled (! isAnyRowChecked);
}

/**
 * Add event handler to connect enableness of submits to check boxes
 */
function connectRowSubmitsToRowCheckboxes() {
  $("studentForm").on ("change", "input[type=checkbox]", setSubmitsFromCheckboxes);		
  setSubmitsFromCheckboxes();
}
