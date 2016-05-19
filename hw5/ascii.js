//Submitted by : ISHMEET SINGH MALHOTRA, Student#; 87553146

"use strict";
var content;
var frames;
var frameIndex;
var timer;



//on clicking start button

function start(){
	
	content = document.getElementById("playarea").value;
	
	frames = content.split("=====\n");
	
	if(frames.length <= 1){
		alert("Enter something to Play");
		return;
	}
	
	setControls(true);
	
	frameIndex = 0;
	
	getFrame();
	
	timer = window.setInterval(getFrame, 250);
	
}

//getting next frame
function getFrame(){
	document.getElementById("playarea").value = frames[frameIndex];
	frameIndex = (frameIndex + 1) % frames.length;
}


function stop(){
	window.clearInterval(timer);
	timer= null;
	document.getElementById("playarea").value = content;
	
	setControls(false);
	
}

//selecting anumation 
function animation(){
	var choice = document.getElementById("animation").value;
	document.getElementById("playarea").value = ANIMATIONS[choice];
	
}

//changing size
function changeSize(){
	var option = document.getElementById("size").value;
	document.getElementById("playarea").className = option;
}
//turbo toggle
function turbo(){
	var speed;
	if(!document.getElementById("turbo").checked){
		speed = 250;
	}
	else{
		speed = document.getElementById("turbo").value;
	}
	
	window.clearInterval(timer);
	timer = window.setInterval(getFrame, speed);

	
	
}
//function for setting controls 
function setControls(isDisable){
	document.getElementById("animation").disabled = isDisable;
	document.getElementById("stop").disabled =! isDisable;
	document.getElementById("start").disabled = isDisable;
	document.getElementById("turbo").disabled =! isDisable;
	document.getElementById("turbo").checked =! isDisable;
	document.getElementById("playarea").readOnly = isDisable;
}