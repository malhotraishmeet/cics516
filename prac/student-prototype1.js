/** CONSTANTS */
var INVALID_CLASS_NAME  = "invalid";
var CHANGED_CLASS_NAME  = "changed";
var ERROR_CONSOLE_ID    = "errorConsole";
var CHECKBOXY_SELECTOR  = ".checkboxy";
var CHECKBOXY_SEPARATOR = "_";
var CHECKBOX_ID_PREFIX  = "checkbox_";
var FIELD_TYPES         = 
    [new FieldType (".id.insert.field",    checkId,   ".id.container",   "#insert", false, "ID must be a positive integer"),
     new FieldType (".name.insert.field",  checkName, ".name.container", "#insert", false, "Name must be at least 10 characters"),
     new FieldType (".name.update.field",  checkName, ".name.container", "#update", false, "Name must be at least 10 characters"),
     new FieldType (".pc.update.field",    checkPC,   ".pc.container",   "#update", false, "Must be a valid postal code of the form A0A 0A0")
    ];

/**
 * DOM Initialization.
 * 
 * Runs after document creation is complete.
 */
window.onload = function () {
	connectCheckboxesAndCheckboxies();
	connectRowSubmitsToRowCheckboxes();
	// Setup field validation
	var fc = new FieldController (INVALID_CLASS_NAME, CHANGED_CLASS_NAME, ERROR_CONSOLE_ID);
	FIELD_TYPES.each (function (f) {
		fc.addFieldType (f);
	});
	fc.setDocumentFieldTypes();
};

/**
 * Connect input checkboxes to name textboxes.
 * 
 * Changing a checkboxy sets the checkbox and unchecking the checkbox resets the checkboxy.
 */
function connectCheckboxesAndCheckboxies () {
  $$(CHECKBOXY_SELECTOR).each (function (checkboxy) {
    var checkbox = $(CHECKBOX_ID_PREFIX + checkboxy.id.split (CHECKBOXY_SEPARATOR) [1]);
    checkboxy.observe ("keyup", function () {
    
      checkbox.checked = checkboxy .up ("tr") .select (".checkboxy") .any (function (c) {
         return c.value != c.defaultValue;
      });
                       
      //checkbox.checked = checkboxy.value != checkboxy.defaultValue;
      setSubmitsFromCheckboxes();
    });
    checkbox.observe ("click", function () {
      if (! checkbox.checked) {
        checkboxy.value = checkboxy.defaultValue;
        checkboxy.fieldType.validate (checkboxy);
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
 * Determine validity of Name
 * 
 * @param   {HtmlInputElement} input - html input that contains name
 * @returns {boolean}                  true iff input contains a valid name
 */
function checkName (input) {
	return input.value.length >= 10 || input.value == input.defaultValue;
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
	upd.disabled = (! isAnyRowChecked) || upd.fieldValidationSaysDoNotEnable;
	del.disabled = (! isAnyRowChecked) || del.fieldValidationSaysDoNotEnable;
	upd.mainSaysDoNotEnable = ! isAnyRowChecked;
	del.mainSaysDoNotEnable = ! isAnyRowChecked;		
}

/**
 * Add event handler to connect enableness of submits to check boxes
 */
function connectRowSubmitsToRowCheckboxes() {
	$("studentForm").on ("change", "input[type=checkbox]", setSubmitsFromCheckboxes);		
	setSubmitsFromCheckboxes();
}
