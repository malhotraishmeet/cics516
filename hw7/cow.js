
"use strict";


document.observe("dom:loaded", function() {
    $("logInButton").observe("click", logInSubmit);
    
});


function getSessionStatus() {
    var file = "cow.php";
   
   new Ajax.Request(file,
            {
                parameters: {
                },
                onSuccess: function(ajax) {
                    var response = JSON.parse(ajax.responseText);
                    if (response.userName === null)
                    {
                        
                    }
                    else
                    {
                       
                        createListView();
                        buildToDoListView(response.userName.toString());
                    }
                },
                onFailure: ajaxFailure,
    
				onException: ajaxFailure
            }
    );
}

function buildToDoListView(userName) {
    $("main").hide();
    
	$("TDlist").appear();
    
	$("h2list").innerHTML = userName + "'s To-Do List";
    
	createEditLineInToDoList();
    
    getJSONFile();
}


function createEditLineInToDoList() {
    var inputLine = document.createElement("div");
    inputLine.id = "inputLine";
    
    $("h2list").parentNode.insertBefore(inputLine, $("logOutList"));

   
    var inputNew = document.createElement("input");
    inputNew.id = "inputNew";
    inputNew.type = "text";
    inputNew.size = "30";
    inputNew.maxlength = "30";
    inputLine.appendChild(inputNew);

    
    var addToListButton = document.createElement("input");
    addToListButton.id = "addToListButton";
    addToListButton.type = "submit";
    addToListButton.value = "Add to Bottom";
    inputLine.appendChild(addToListButton);
    
    $("addToListButton").observe("click", addToBottom);

  
    var deleteTopItemButton = document.createElement("input");
    deleteTopItemButton.id = "deleteTopItemButton";
    deleteTopItemButton.type = "submit";
    deleteTopItemButton.value = "Delete Top Item";
    inputLine.appendChild(deleteTopItemButton);
    
    $("deleteTopItemButton").observe("click", deleteTop);
}

function addToBottom() {
    
    var currentList = getCurrentList();
    
    if ($F("inputNew").trim() !== "") {
        
        currentList.items.push($F("inputNew").escapeHTML());
        
        $("inputNew").clear();
        
        printToDoList(currentList);
        writeListToFile(JSON.stringify(currentList));

      
        var item = currentList.items.length - 1;
        
		var itemId = "toDoList_" + item;
        
		new Effect.BlindDown($(itemId), {duration: 0.8});
		
		new Effect.Highlight($(itemId), {startcolor: '#9fc2e6', endcolor: '#ffffff'});
    }
}

function deleteTop() {
    var currentList = getCurrentList();
    
    
    new Effect.BlindUp($("toDoList_0"), {duration: 0.8,

		afterFinish: function() {
            
            currentList.items.shift();
            
            printToDoList(currentList);

            writeListToFile(JSON.stringify(currentList));
        }
    }
    );
	new Effect.Highlight($("toDoList_0"), {startcolor: '#9fc2e6', endcolor: '#ffffff'});
}

function writeListToFile(jsonStr) {

    var file = "cowUpdate.php";

    new Ajax.Request(file,
            {
                parameters: {
                    jsonString: jsonStr
                },
                onSuccess: function(ajax) {
                    if (ajax.responseText === "ERROR") {
                        alert("ERROR when writing to file");
                    }
                },
                onFailure: ajaxFailure,

                onException: ajaxFailure
            }
    );
}

function getCurrentList() {
    var curJson = {};

    curJson["items"] = [];

    if ($("toDoList").hasChildNodes()) {
        var nodeArr = $("toDoList").childNodes;

        for (var i = 0; i < nodeArr.length; i++)
        {
            curJson["items"][i] = nodeArr[i].innerHTML;
        }
    }
    return curJson;
}


function getJSONFile() {
    var file = "cowGet.php";

    new Ajax.Request(file,
            {
                parameters: {
                },
                onSuccess: function(ajax) {
                  
                    if (ajax.responseText.indexOf("File created.") > -1)
                    {
                    }
                    else {
                        
                        var response = JSON.parse(ajax.responseText);
                        printToDoList(response);
                    }
                },
                onFailure: ajaxFailure,

                onException: ajaxFailure
            }
    );
}

 
function printToDoList(response) {
   
    if ($("TDlist").contains($("toDoList"))) {
    
    $("TDlist").removeChild($("toDoList"));
    }
    
    var ul = document.createElement("ul");
    
	ul.id = "toDoList";
    
    $("inputLine").parentNode.insertBefore(ul, $("inputLine"));
   
    for (var i = 0; i < response.items.length; i++) {
       
        var li = document.createElement("li");
        
		li.id = "toDoList_" + i;
        
		li.innerHTML = response.items[i];
        
		ul.appendChild(li);
    }

   
    Sortable.create("toDoList", {
        onUpdate: function listUpdate() {
            
			new Effect.Shake($("toDoList"));
        
		
            var currentList = getCurrentList();
            
			writeListToFile(JSON.stringify(currentList));
        }
    });
}

function logInSubmit() {
    
    if (document.body.contains($("logInError"))) {
        document.body.removeChild($("logInError"));
    }

    if (document.body.contains($("TDlist"))) {
        document.body.removeChild($("TDlist"));
    }

    createListView();

    var file = "cowLogin.php";
    new Ajax.Request(file,
            {
                method: "post", 
                parameters: {
                    
                    user: $F("userName"),
                
					password: $F("psw")
                },
                onSuccess: function ajaxSuccess(ajax) {
                    var response = JSON.parse(ajax.responseText);
                    var re = response.resp;
                    if (re == "OK")
                    {
                        
                        if ($("main").previousSibling.id === "logInError") {
                            
                            $("logInError").parentNode.removeChild($("logInError"));
                        }
                        buildToDoListView($F("userName"));
                    }
                    else
                    {
                        if (document.body.contains($("TDlist"))) {
                            document.body.removeChild($("TDlist"));
                        }
                        
                        logInErrorMsg();
                    }
                },
    
				onFailure: ajaxFailure,
                
				onException: ajaxFailure
            }
    );
}

function ajaxFailure(ajax, exception) {
    
	alert("Error making Ajax request:" +
            "\n\nServer status:\n" + ajax.status + " " + ajax.statusText +
            "\n\nServer response text:\n" + ajax.responseText);
    
	if (exception) {
		throw exception;
    }
}


function logInErrorMsg() {
  
    if ($("main").previousSibling.id === "logInError") {
       
        $("logInError").parentNode.removeChild($("logInError"));
        
        createErrorMsg();
    }
    else {
        createErrorMsg();
    }
}


function createErrorMsg() {
    var p = document.createElement("p");
    
	p.innerHTML = "The Username or Password or Both are incorrect. Try Again!"
    
	p.id = "logInError";
  
    document.body.insertBefore(p, $("main"));
    
	p.addClassName("errorMsg");
    
	p.pulsate({
        duration: 200.0,
        pulses: 200
    });
}

 
function createListView() {
    
    var TDlist = document.createElement("div");
    TDlist.id = "TDlist";
    
    TDlist.addClassName("todoList");

    document.body.insertBefore(TDlist, $("main"));
    
    var h2list = document.createElement("h2");
    
	h2list.id = "h2list";
    
    TDlist.appendChild(h2list);


    var logOutList = document.createElement("ul");
    
	logOutList.id = "logOutList";
    
	var logOutItem = document.createElement("li");
    
	logOutList.appendChild(logOutItem);

    var logOutButton = document.createElement("a");
    
	var linkText = document.createTextNode("Log Out");
    
	logOutButton.appendChild(linkText);
    
	logOutButton.id = "logOutButton";
    
	logOutButton.href = "cow.html";
    
	$("TDlist").appendChild(logOutList);
    
	logOutItem.appendChild(logOutButton);
    
    $("logOutButton").observe("click", logOutSubmit);
}


function logOutSubmit() {
    
    $("inputNew").disabled = true;
    
	$("addToListButton").disabled = true;
    
	$("deleteTopItemButton").disabled = true;

    var file = "cowLogout.php";
    
	new Ajax.Request(file,
            {
                method: "post", 
                parameters: {
                },
                onSuccess: function ajaxSuccess(ajax) {
                    $("TDlist").hide();
					
					$("userName").clear();
                    
					$("psw").clear();
					
                    $("main").style.display = '';

                    $("inputNew").disabled = false;
					
                    $("addToListButton").disabled = false;
                    
					$("deleteTopItemButton").disabled = false;
                },
                onFailure: ajaxFailure,
                onException: ajaxFailure
            }
    );
}