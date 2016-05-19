// hw6 submitted by Ishmeet Singh Malhotra
// Student # : 87553146


"use strict"


//size of row and column
var size =4;

//tile size
var tile = 100;

var emptyRow = 3;
var emptyColumn = 3;

window.onload = startingWindow;



function startingWindow()
{
	var tilePieces = document.querySelectorAll("#puzzlearea div");
	
	
	
	for(var i=0;i<tilePieces.length ; i++)
	{
		var row = Math.floor(i/size);
		
		var col = i%size;
		
		// applying css to each tile
		tilePieces[i].className = "puzzlepiece";
		
		tilePieces[i].id = row +"_"+col;
		
		//setting position
		
		tilePieces[i].style.top = row * tile +"px";
		
		tilePieces[i].style.left = col * tile + "px";
		
		tilePieces[i].style.backgroundPosition = -col * tile + "px " + -row * tile + "px";
		
		tilePieces[i].onclick = moveTile;
		tilePieces[i].onmouseover = hover;
		tilePieces[i].onmouseout = undoHover; 
		
	}
	
	$("shufflebutton").onclick = shuffle;
	
}
	
	
	
	//give row number from Object
	
	function getRowNum(obj){
		var rowNum = parseInt(obj.style.top)/tile;
		return rowNum;
	}
	
	
	//give col number from Object
	
	function getColNum(obj){
		var colNum = parseInt(obj.style.left)/tile;
		return colNum;	
	}
	
	
	
	//chek if tile is movable
	
	function checkMovable(obj){
		
		var rowNum = getRowNum(obj);
		var colNum = getColNum(obj);
		
		//if tile is adjescent to empty tile then return true
		var sameRowBool = Math.abs(rowNum - emptyRow) === 1 && colNum === emptyColumn;
        var sameColBool = Math.abs(colNum - emptyColumn) === 1 && rowNum === emptyRow;
        
        
        if(sameRowBool || sameColBool){
			return true;
		}        
		
		else{
			return false;
		}
	
	}

  // move tile if movable
  function moveTile(){
  	if (checkMovable(this)){
		move(this);
		//if(checkWon()){
		//	alert("You have won! Finally :P"+emptyColumn+emptyRow)
		//}
	}
  }
  
	function move(obj){
		
		var rowNum = getRowNum(obj);
		var colNum = getColNum(obj);
		
		obj.style.top = emptyRow*tile+"px";
		obj.style.left = emptyColumn*tile+"px"; 
		
		emptyRow = rowNum;
		emptyColumn = colNum;
		
	}
	
	
	function hover(){
		if(checkMovable(this)){
			this.className = "puzzlepiece movablepiece";
		}
	}
	
	function undoHover() {
    this.className = "puzzlepiece";
	}

	
	
	
	
	
	
	function shuffle(){
		for(var j=0;j<400;j++){
				
		var movable=[];
		
		var puzzlePieces = document.querySelectorAll("#puzzlearea div");
		
		for(var i = 0; i < puzzlePieces.length; i++){
			if(checkMovable(puzzlePieces[i])){
			movable.push(puzzlePieces[i]);
			}
		}
		
		// choose one and move it randomly
		
		var choice = parseInt(Math.random()*movable.length);
		
		move(movable[choice]);
		
		}
		
		
	}
	
	
	function checkWon(){
		var puzzlePieces = document.querySelectorAll("#puzzlearea div");
		
		for( var i=0; i<puzzlePieces.length ;i++){
			
			var row = Math.floor(i/size) !== getRowNum(puzzlePieces[i]);
			var col = i%size !== getColNum(puzzlePieces[i]);
			
			if(row || col){
				
			return false;
			}
			else{
			return true;
			}			
		}
		
	}
  

	


