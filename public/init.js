
const button =  document.querySelector("#send-message");

const messageBox = document.querySelector("#message-box");
const textfield = document.querySelector("#message");

const clickMenu = document.querySelector(".right-click-menu");

let messageId;

//WebSocket////////////////////////////////////////////////////////

if ("WebSocket" in window) {
  // Create a WebSocket object
  var websocket = new WebSocket("ws://localhost:8000");
  
  // WebSocket code goes here
} else {
  // WebSocket is not supported by the browser
  alert("WebSocket is not supported by your browser.");
}

websocket.onopen = function() {

  //AJAX-----------------------------------------
  const request = new XMLHttpRequest();

  request.onreadystatechange = function(){

    if (this.readyState === 4 && this.status === 200) {
        const test = JSON.parse(this.responseText);

        
        for(let element of test){
          const forSafe2 =String(decodeURI(element.valueText));
          messageBox.insertAdjacentHTML('beforeend', `<div class="messageSpaceMane" id=id`+element.id+`>`+element.userNick+': '+forSafe2+`</div>`);
          
        }
      }
  }

  request.open('GET', 'route.php?need=oldMessage');
  request.send();
  
//AJAXend-----------------------------------------
  //messageBox.insertAdjacentHTML('beforeend', `<div class="messageSpaceMane">`+"Привет!"+`</div>`);
}

websocket.onmessage = function(ev) {
  const response = JSON.parse(ev.data);
 console.log(response);
  const forSafe =String(decodeURI(response.message));
  messageBox.insertAdjacentHTML('beforeend', `<div class="messageSpaceMane" id=id`+response.messageId+`>`+response.userNick+': '+forSafe+`</div>`);
  play();
}

websocket.onerror = function(ev) {
  messageBox.insertAdjacentHTML('beforeend', `<div class="message-space">`+"Произошла ошибка - "+ev.data+`</div>`);
}

websocket.onclose = function() {
  messageBox.insertAdjacentHTML('beforeend', `<div class="message-space">`+"Соединение закрыто"+`</div>`);
}

function send_message(){

  if(textfield.value == ''){
    alert('Необхордимо ввести сообщение!');
    return;
  }
const safeField = encodeURI(textfield.value);
  const resultMessage = {

    message: safeField,
    userNick: userNickName,
    userField: userId,
    chatName: 'test'
    
  }

  websocket.send(JSON.stringify(resultMessage));
  textfield.value = '';

}

button.addEventListener('click', event => {
   
  send_message();
  
})

//////////////////////////////////////////////////////////

function delete_message(){
  const deleteMessage = {
    type: 'deleteMess',
    messID: messageId,
    chatName: 'test'
    
  }
  console.log(JSON.stringify(deleteMessage));
}

function forward_message(){
  const forwardMessage = {
    type: 'forwardMess',
    messID: messageId,
    chatName: 'test',
    to: 'test'
  }
  console.log(JSON.stringify(forwardMessage));
}

function edit_message(){
  const editMessage = {
    type: 'editMess',
    messID: messageId,
    chatName: 'test',
    newMess: 'testtest'
  }
  console.log(JSON.stringify(editMessage));
}

messageBox.addEventListener("contextmenu", event=>{
  if(event.target.className=="messageSpaceMane"){
    event.preventDefault();
    clickMenu.style.left = event.clientX +5+ 'px';
    clickMenu.style.top = event.clientY +5+ 'px';

    clickMenu.classList.add("active");

    messageId = event.target.id.substr(2);
  }
})

document.addEventListener("click", event => {
  if (event.button !== 2) {
    clickMenu.classList.remove("active");
  }
})

clickMenu.addEventListener("click", event => {
  event.stopPropagation();
})

document.querySelector("#l1").addEventListener("click", () => {
  edit_message();
}, false);
document.querySelector("#l2").addEventListener("click", () => {
  
  delete_message();
  
}, false);
document.querySelector("#l3").addEventListener("click", () => {
  forward_message();
}, false);





// sounds--------------------------------------------------

function play() {
  let audio = new Audio(
'https://media.geeksforgeeks.org/wp-content/uploads/20190531135120/beep.mp3');
audio.volume = loudness;
  audio.play();
 }

const soundButton = document.querySelector(".soundButton");
let loudness = 0;

soundButton.addEventListener("click", event => {
  if(loudness==0){
  soundButton.classList.add("red");
  loudness = 1;
  } else {
    soundButton.classList.remove("red");
    loudness = 0;
  }
})