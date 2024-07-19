var isSigningUp = false

function switchMode(){
    console.log(isSigningUp)
    if(isSigningUp){
        document.getElementById("login").style.backgroundColor="rgba(73, 145, 252 , 1)";
        document.getElementById("SignUp").style.backgroundColor="rgba(73, 145, 252 , 0.5)";
    }else{
        document.getElementById("login").style.backgroundColor="rgba(73, 145, 252 , 0.5)";
        document.getElementById("SignUp").style.backgroundColor="rgba(73, 145, 252 , 1)";
    }
    isSigningUp=! isSigningUp
    loadCorrespondingHtml()
}

function loadCorrespondingHtml(){
    fetch('/login/switchmode', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ mode: isSigningUp}),
      })
      .then(response => response.text())
      .then(data => {
        document.getElementById("content").innerHTML=data

        })
      .catch((error) => console.error('Error:', error));

}

function loginOrSignUp(){
    if(isSigningUp){
        signUp()
    }
    login()
}

function login(){
    // TODO
    window.location.href = "/Home";
}

window.onload = loadCorrespondingHtml()

